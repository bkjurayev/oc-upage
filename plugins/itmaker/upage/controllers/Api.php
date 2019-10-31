<?php namespace Itmaker\Upage\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Input;
use JWTAuth;
use RainLab\User\Models\User;
use Itmaker\Upage\Models\PhoneVerification;

// models
use Itmaker\Upage\Models\Poster;
use Itmaker\Upage\Models\Category;
use Itmaker\Upage\Models\Company;
use Itmaker\Upage\Models\Interesting;
use Itmaker\Upage\Models\Location;
use Itmaker\Upage\Models\CompanyReview;
use Itmaker\Upage\Models\Favorite;

class Api extends Controller
{

    public function index() {
        echo 'I am API';
    }


    public function getPosts() {
        return Poster::with('posterSchedules')->where('is_active', true)->remember(10)->get();
    }
    
    public function getInterestings()
    {
        return Interesting::with('image')->where('is_active', true)->remember(10)->get();
    }

    // categories
    public function getCategories() {
        return Category::with('icon')->getAllRoot();
    }

    public function getCategory($id) {
        return Category::with(['icon', 'children'])->where('id', $id)->first();
    }

    public function getCompaniesByCategory($id) {
        return Company::companyList(['category' => $id]);
    }

    public function getDistricts()
    {
        return Location::with('parent')->where('nest_depth', 1)->remember(10)->get();
    }
    
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities()
    {
        return Location::with('children')->where('nest_depth', 0)->get();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'review' => 'required',
            'user' => 'integer|required',
            'companyId' => 'integer|required',
            'rating' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $review = new CompanyReview;
        $review->review = $request->input('review');
        $review->user_id = $request->input('user');
        $review->company_id = $request->input('companyId');
        $review->rating = $request->input('rating');
        $review->save();

        return Company::find($request->input('company_id'));
    }

    public function getReviewsByCompany($id)
    {
        return CompanyReview::where('company_id', $id)->remember(10)->get();
    }
    
    /**
     * @param Company $company
     * @return \Illuminate\Http\JsonResponse
     */
    
    
    public function getCompanies(Request $request)
    {
        $page = $request['page'];
        return Company::remember(10)->CompanyList([
            'search'   => $request['search'],
            'page'     => $request['page'],
            'category' => $request['category'],
            'district' => $request['district']
        ]);
    }
    
    public function show($id)
    {
        return Company::find($id);
    }

    public function associate(Request $request){
        $validator = \Validator::make($request->all(), [
            'post_id' => 'integer|required',
            'user_id' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        
        if(isset($request['user_id'])){
            $fav = Favorite::where('company_id', $request['post_id'])->where('user_id', $request['user_id'])->first();
            if( $fav ) {
                Favorite::where('company_id', $request['post_id'])->delete();
                return response()->json('Deleted ok!');
            }else{
                $favorite = new Favorite();
                $favorite->company_id = $request['post_id'];
                $favorite->user_id = $request['user_id'];
                $favorite->save();
                return response()->json('Added ok!');
            }
        }else{
            return;
        }    
    }
    
    public function favorites() {
        $user = $this->auth();

        if ($user) {
            $favorites = Favorite::where('user_id', $user->id)->get();
            return $favorites;
        }

        /*$favorites = Favorite::where('user_id', $user->id)->get();
        if(!$favorites) return;
        
        $fav = [];
        foreach( $favorites as $favorite ) {
            $fav[] = $favorite->company_id;
        }
        
        $fav = array_unique($fav);
        return Company::whereIn('id', $fav)->get();*/
    }
    
    // private methods
    
    private function auth() {
		return JWTAuth::parseToken()->authenticate();
	}
    public function phoneVerification (Request $request) {
        $user = $this->auth();
        $validator = \Validator::make($request->all(), [
            'code'   => 'integer|required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        
        $phoneVerification = PhoneVerification::where([
            ['user_id', $user->id],
            ['code', $request->code],
        ]);
        $phoneVerification->delete();

        $user->is_activated = true;
        $user->activated_at = time();
        $user->update();

        return response()->json([
            'status' => 'ok',
            'user'   => $user
        ]);
    }
    public function emailVerification (Request $request) {
        $user = $this->auth();
        $validator = \Validator::make($request->all(), [
            'code'   => 'required'
        ]);
        $data = explode("-", $request->code);

         if ($user->id == $data[0] && $user->activation_code == $data[0].'-'.$data[1]) {
            $user->activation_code = null;
            $user->is_activated = true;
            $user->activated_at = $user->freshTimestamp();
            $user->forceSave();
            return true;
        }
        return $user;
    }
}
