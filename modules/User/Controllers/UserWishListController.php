<?php
namespace Modules\User\Controllers;

use Illuminate\Support\Facades\Auth;
use Modules\FrontendController;
use Modules\User\Models\UserWishList;
use Illuminate\Http\Request;

class UserWishListController extends FrontendController
{
    protected $userWishListClass;
    public function __construct()
    {
        parent::__construct();
        $this->userWishListClass = UserWishList::class;
    }

    public function index(Request $request){

        $wishlist = $this->userWishListClass::query();

        if(is_employer()){
            if($s = $request->get('s')) {
                $wishlist = $wishlist->whereHas('candidate', function ($q) use ($s) {
                    $q->where('title', 'like', '%' . $s . '%');
                });
            }
            $wishlist = $wishlist->where('object_model', 'candidate');
        }else {
            if($s = $request->get('s')) {
                $wishlist = $wishlist->whereHas('job', function ($q) use ($s) {
                    $q->where('title', 'like', '%' . $s . '%');
                });
            }
            $wishlist = $wishlist->where('object_model', 'job');
        }
        $wishlist = $wishlist->where("user_wishlist.user_id",Auth::id())
            ->orderBy('user_wishlist.id', 'desc');
        $data = [
            'rows' => $wishlist->paginate(10),
            'page_title'         => is_employer() ? __("Shortlisted Candidates") : __("Shortlisted Jobs"),
            'menu_active' => is_employer() ? 'user_bookmark_employer' : 'user_bookmark',
        ];
        return view('User::frontend.wishList.index', $data);
    }

    public function followingEmployers(Request $request){
        $wishlist = $this->userWishListClass::query();
        if($s = $request->get('s')){
            $wishlist = $wishlist->whereHas('company', function ($q) use ($s) {
                $q->where('name', 'like', '%'.$s.'%');
            });
        }
        $wishlist = $wishlist->where('object_model', 'company')
            ->where("user_wishlist.user_id",Auth::id())
            ->orderBy('user_wishlist.id', 'desc');
        $data = [
            'rows' => $wishlist->paginate(10),
            'page_title'         => __("Following Employers"),
            'menu_active' => 'following_employers',
        ];
        return view('User::frontend.wishList.following-employers', $data);

    }

    public function handleWishList(Request $request){
        $object_id = $request->input('object_id');
        $object_model = $request->input('object_model');
        if(empty($object_id))
        {
            return $this->sendError(__("Service ID is required"));
        }
        if(empty($object_model))
        {
            return $this->sendError(__("Service type is required"));
        }

        $meta = $this->userWishListClass::where("object_id",$object_id)
            ->where("object_model",$object_model)
            ->where("user_id",Auth::id())
            ->first();
        if(!empty($meta)){
            $meta->delete();
            return $this->sendSuccess(['class'=>"",'fragments'=>$this->fragments()]);
        }
        $meta = new $this->userWishListClass($request->input());
        $meta->user_id = Auth::id();
        $meta->save();
        return $this->sendSuccess(['class'=>"active", 'fragments'=>$this->fragments()]);
    }

    public function fragments(){
        return [
            '.wishlist_count'=>auth()->user()->wishlist_count ?? 0
        ];
    }

    public function remove(Request $request){
        $meta = $this->userWishListClass::where("id",$request->input('id'))
            ->where("user_id",Auth::id())
            ->first();
        if(!empty($meta)){
            $meta->delete();
            return $this->sendSuccess(['message' => __('Delete success!'), 'fragments'=>$this->fragments()]);
        }
        return $this->sendError(['message' => __('Delete fail!')]);
    }
}
