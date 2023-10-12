<?php

namespace Modules\Company\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Company\Models\Company;
use Modules\Company\Models\CompanyCategory as Category;
use Modules\Company\Models\CompanyTerm;
use Modules\Company\Models\CompanyTranslation;
use Modules\Core\Models\Attributes;
use Modules\FrontendController;
use Modules\Language\Models\Language;
use Modules\Location\Models\Location;

class ManageCompanyController extends FrontendController{

    protected $attributes;
    protected $location;
    protected $company;
    protected $company_translation;
    protected $category;
    protected $language;
    protected $company_term;

    public function __construct(){

        $this->attributes = Attributes::class;
        $this->company = Company::class;
        $this->location = Location::class;
        $this->category = Category::class;
        $this->company_translation = CompanyTranslation::class;
        $this->language = Language::class;
        $this->company_term = CompanyTerm::class;

    }

    public function companyProfile(Request $request){

        $this->checkPermission('employer_manage');

        $row = $this->company::where('owner_id', Auth::id())->first();

        if(empty($row)){
            $user = auth()->user();
            $row = new $this->company;
            $row->owner_id = auth()->id();
            $row->email = $user->email;
            $row->name = $user->display_name;
            $row->save();
        }

        $translation = $row->translateOrOrigin($request->query('lang'));

        $data = [
            'row'  => $row,
            'categories'        => $this->category::get()->toTree(),
            'attributes'     => $this->attributes::where('service', 'company')->get(),
            'company_location'     => $this->location::where('status', 'publish')->get()->toTree(),
            'translation'  => $translation,
            'enable_multi_lang'=>true,
            'page_title'=>__("Company Profile"),
            'menu_active' => 'company_profile',
            "selected_terms"    => $row->companyTerm ? $row->companyTerm->pluck('term_id') : [],
            'is_user_page' => true,
        ];
        return view('Company::frontend.layouts.manageCompany.detail', $data);
    }

    public function companyUpdate(Request $request){
        $this->checkPermission('employer_manage');
        $input = $request->input();

        $row = $this->company::where('owner_id', Auth::id())->first();

        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'phone'=>'required',
        ]);

        if(empty($row)){
            return redirect(route('user.company.profile'))->with('error', __("No company found"));
        }

        $attr = [
            'name',
            'email',
            'phone',
            'website',
            'location_id',
            'avatar_id',
            'founded_in',
            'category_id',
            'map_lat',
            'map_lng',
            'status',
            'about',
            'social_media',
            'city',
            'state',
            'country',
            'address',
            'team_size',
            'is_featured',
            'zip_code',
            'allow_search'
        ];
        $input['team_size'] = !empty($input['team_size']) ? $input['team_size'] : 0;

        $row->fillByAttr($attr, $input);
        if($request->input('slug')){
            $row->slug = $request->input('slug');
        }

        $res = $row->saveOriginOrTranslation($request->query('lang'),true);

        if ($res) {
            if (!$request->input('lang') or is_default_lang($request->input('lang'))) {
                $this->saveTerms($row, $request);
            }
            return back()->with('success',  __('Company updated') );
        }
    }

    public function saveTerms($row, $request)
    {
        if (empty($request->input('terms'))) {
            $this->company_term::where('company_id', $row->id)->delete();
        } else {
            $term_ids = $request->input('terms');
            foreach ($term_ids as $term_id) {
                $this->company_term::firstOrCreate([
                    'term_id' => $term_id,
                    'company_id' => $row->id
                ]);
            }
            $this->company_term::where('company_id', $row->id)->whereNotIn('term_id', $term_ids)->delete();
        }
    }
}
