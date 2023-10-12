<?php


namespace Modules\Job\Controllers;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Candidate\Models\CandidateCvs;
use Modules\FrontendController;
use Modules\Job\Events\EmployerChangeApplicantsStatus;
use Modules\Job\Exports\ApplicantsExport;
use Modules\Job\Models\Job;
use Modules\Job\Models\JobCandidate;
use Modules\Job\Models\JobCategory as Category;
use Modules\Job\Models\JobTranslation;
use Modules\Job\Models\JobType;
use Modules\Language\Models\Language;
use Modules\Location\Models\Location;
use Modules\Skill\Models\Skill;

class ManageJobController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function manageJobs(Request $request){
        $this->checkPermission('job_manage');

        $job_query = Job::query()->with(['location', 'category', 'company'])->orderBy('id', 'desc');
        $title = $request->query('s');

        if ($title) {
            $job_query->where('title', 'LIKE', '%' . $title . '%');
            $job_query->orderBy('title', 'asc');
        }

        $company_id = Auth::user()->company->id ?? '';
        $job_query->where('company_id', $company_id);


        $data = [
            'rows'        => $job_query->paginate(20),
            'menu_active' => 'manage_jobs',
            "languages"=>Language::getActive(false),
            "locale"=>\App::getLocale(),
            'page_title'=>__("Jobs Management")
        ];
        return view('Job::frontend.layouts.manage-job.manage-jobs', $data);
    }
    public function createJob(Request $request){
        $this->checkPermission('job_manage');
        if(!auth()->user()->checkCompanyInfo()) {
            return redirect(route('user.company.profile'))->with('error', __('Need to complete Company Profile before posting a job') );
        }

        $row = new Job();
        $row->fill([
            'status' => 'publish',
        ]);
        $data = [
            'categories'        => Category::get()->toTree(),
            'job_types' => JobType::query()->where('status', 'publish')->get(),
            'job_skills' => Skill::query()->where('status', 'publish')->get(),
            'job_location'     => Location::where('status', 'publish')->get()->toTree(),
            'row'         => $row,
            'menu_active' => 'new_job',
            'page_title' => __("Add Job"),
            'translation' => new JobTranslation(),
            'is_user_page' => true
        ];
        return view('Job::frontend.layouts.manage-job.edit-job', $data);
    }

    public function editJob(Request $request, $id){
        $this->checkPermission('job_manage');

        $row = Job::with('skills')->find($id);
        $company_id = Auth::user()->company->id ?? '';

        if (empty($row)) {
            return redirect(route('user.manage.jobs'));
        }elseif($company_id != $row->company_id){
            return redirect(route('user.manage.jobs'));
        }

        $translation = $row->translateOrOrigin($request->query('lang'));

        $data = [
            'row'  => $row,
            'translation'  => $translation,
            'categories' => Category::query()->where('status', 'publish')->get()->toTree(),
            'job_location' => Location::query()->where('status', 'publish')->get()->toTree(),
            'job_types' => JobType::query()->where('status', 'publish')->get(),
            'job_skills' => Skill::query()->where('status', 'publish')->get(),
            'enable_multi_lang'=>true,
            'page_title' => __("Edit Job: "). $translation->title,
            'menu_active' => 'manage_jobs',
            'is_user_page' => true
        ];
        return view('Job::frontend.layouts.manage-job.edit-job', $data);
    }
    public function storeJob(Request $request, $id){
        $this->checkPermission('job_manage');

        $check = Validator::make($request->input(), [
            'title' => 'required',
            'job_type_id' => 'required',
            'category_id' => 'required',
            'location_id' => 'required',
            'expiration_date' => 'required|date_format:Y/m/d'
        ]);
        if (!$check->validated()) {
            return back()->withInput($request->input());
        }
        if(!empty($request->input('salary_max')) && (int)$request->input('salary_max') > 0 && !empty($request->input('salary_min')) && (int)$request->input('salary_min') > 0) {
            $check = Validator::make($request->input(), [
                'salary_max' => 'required|gt:salary_min'
            ]);
            if (!$check->validated()) {
                return back()->withInput($request->input());
            }
        }

        if($id>0){
            $row = Job::find($id);
            if (empty($row)) {
                return redirect(route('user.manage.jobs'));
            }
        }else{

            $row = new Job();
            $row->status = "publish";
        }

        $input = $request->input();
        $attr = [
            'title',
            'content',
            'category_id',
            'thumbnail_id',
            'location_id',
            'company_id',
            'job_type_id',
            'expiration_date',
            'hours',
            'hours_type',
            'salary_min',
            'salary_max',
            'salary_type',
            'gender',
            'map_lat',
            'map_lng',
            'map_zoom',
            'experience',
            'is_featured',
            'is_urgent',
            'status',
            'create_user',
            'apply_type',
            'apply_link',
            'apply_email',
            'wage_agreement',
            'gallery',
            'video',
            'video_cover_id',
            'number_recruitments',
        ];
        if (!empty($input['wage_agreement'])){
            $input['salary_min'] = 0;
            $input['salary_max'] = 0;
        }
        if($row->is_approved != Job::APPROVED and setting_item('job_need_approve')){
            $attr[] = 'is_approved';
        }
        $row->fillByAttr($attr, $input);

        // Check Plan
        if($row->status == 'publish' and !auth()->user()->checkJobPlan()){
            return back()->with('error',__("Maximum published items reached. Please buy more plan"));
        }

        if($request->input('slug')){
            $row->slug = $request->input('slug');
        }
        if(empty($request->input('create_user'))){
            $row->create_user = Auth::id();
        }

        $user = User::with('company')->find(Auth::id());
        if(!empty($user->company)){
            $row->company_id = $user->company->id;
        }

        $res = $row->saveOriginOrTranslation($request->query('lang'),true);
        $row->skills()->sync($request->input('job_skills') ?? []);

        if ($res) {
            if($id > 0 ){
                return back()->with('success',  __('Job updated') );
            }else{
                return redirect(route('user.edit.job', ['id' => $row->id]))->with('success', __('Job created') );
            }
        }
    }

    public function deleteJob(Request $request, $id){
        $this->checkPermission('job_manage');

        $query = Job::where("id", $id);

        $company_id = Auth::user()->company->id ?? '';
        $query->where('company_id', $company_id);

        $query->first();
        if(!empty($query)){
            $query->delete();
        }

        return redirect()->back()->with('success', __('Deleted success!'));
    }

    public function applicants(Request $request){

        $this->hasPermission('job_manage');

        $rows = JobCandidate::with(['jobInfo', 'candidateInfo', 'cvInfo', 'company', 'company.getAuthor'])
            ->whereHas('jobInfo', function ($q) use($request){
                $job_id = $request->query('job_id');
                $company_id = Auth::user()->company->id ?? '';
                $q->where('company_id', $company_id);
                if($job_id){
                    $q->where("id", $job_id);
                }
            });

        $rows->where('status','!=','delete');
        $rows = $rows->orderBy('id', 'desc')
            ->paginate(20);
        $data = [
            'rows' => $rows,
            'menu_active' => 'all_applicants',
            'page_title' => __("All Applications")
        ];
        return view('Job::frontend.layouts.manage-job.applicants', $data);

    }

    public function applicantsChangeStatus($status, $id){
        $this->checkPermission('job_manage');

        $row = JobCandidate::with('jobInfo', 'jobInfo.user', 'candidateInfo', 'company', 'company.getAuthor')
            ->where('id', $id)
            ->whereHas('jobInfo', function ($q){
                $company_id = Auth::user()->company->id ?? '';
                $q->where('company_id', $company_id);
            });

        $row = $row->first();
        if (empty($row)){
            return redirect()->back()->with('error', __('Item not found!'));
        }
        $old_status = $row->status;
        if($status != 'approved' && $status != 'rejected' && $status != 'delete'){
            return redirect()->back()->with('error', __('Status unavailable'));
        }
        $row->status = $status;
        $row->save();
        //Send Notify and email
        if($old_status != $status) {
            event(new EmployerChangeApplicantsStatus($row));
        }
        if($status == 'delete'){
            return redirect()->back()->with('success', __('Delete success!'));
        }
        return redirect()->back()->with('success', __('Update success!'));
    }

    public function applicantsExport(){

        $this->checkPermission('job_manage');

        return (new ApplicantsExport())->download('applicants-' . date('M-d-Y') . '.xlsx');
    }

    public function applicantsCreate()
    {
        $this->checkPermission('job_manage');

        $row = new JobCandidate();
        $row->fill([
            'status' => 'pending',
        ]);
        $data = [
            'row'         => $row,
            'page_title' => __("Create new applicant"),
            'menu_active' => 'all_applicants',
            'translation' => new JobTranslation()
        ];
        return view('Job::frontend.layouts.manage-job.applicant-create', $data);
    }
    public function applicantsStore(Request $request)
    {
        $user = Auth::user();
        $candidate_id = $request->input('candidate_id');
        $status = $request->input('status');
        $apply_cv_id = $request->input('apply_cv_id');
        $message = $request->input('content');
        $job_id = $request->input('job_id');
        $company_id = ($user->company) ? $user->company->id : '';

        if(empty($candidate_id)){
            return redirect()->back()->with('error', __('Choose a candidate'));
        }
        if(empty($apply_cv_id)){
            return redirect()->back()->with('error', __('Choose a cv'));
        }
        if(empty($job_id)){
            return redirect()->back()->with('error', __('Choose a job'));
        }
        $row = JobCandidate::query()
            ->where('job_id', $job_id)
            ->where('candidate_id', $candidate_id)
            ->first();
        if ($row){
            return redirect()->back()->with('error', __('You have applied this job already'));
        }
        $row = new JobCandidate();
        $row->job_id = $job_id;
        $row->candidate_id = $candidate_id;
        $row->cv_id = $apply_cv_id;
        $row->message = !empty($message) ? $message : '';
        $row->status = $status;
        $row->company_id = $company_id;
        $row->save();
        $row->load('jobInfo', 'jobInfo.user', 'candidateInfo', 'company', 'company.getAuthor');

        return redirect(route('user.applicants'))->with('success', __('Added successfully!'));
    }
    public function applicantsGetCv(Request $request)
    {
        $id = $request->query('id');
        $cvs = CandidateCvs::query()->where('origin_id', $id)->with('media')->get();
        return $this->sendSuccess(['cv'=>$cvs],'success');
    }

}
