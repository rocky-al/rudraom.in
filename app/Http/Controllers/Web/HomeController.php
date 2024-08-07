<?php

namespace App\Http\Controllers\Web;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Offer;
use App\Models\Content;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public function index()
    {

        $result = ['title' => 'Home'];
        return view('web.index', $result);
    }


    public function faq()
    {
        $result = ['title' => "FAQ's"];
        return view('web.pages.faq', $result);
    }



    public function check_user(Request $request)
    {
        $data = User::where('referral_id', $request->referral_id)->first();

        if(isset($data)){
            $status = "Registered";
            $message = "This user is registered user";
        }else{
            $status = "Not Register";
            $message = "Not Register this user";
        }
        $result = ['title' => "Check User", 'status'=> $status, 'message'=> $message];
        return view('web.pages.check_user', $result);
    }

    



    public function aboutUs() 
    {
        $page = Content::select('title','description')->where('id','1')->first();
       
        $result = ['title' => "About us",'data'=>$page];
        return view('web.aboutus', $result);
    } 

    public function privacyPolicy()
    {
        $page = Content::select('title','description')->where('id','3')->first();
        $result = ['title' => "FAQ's",'data'=>$page];
        
        return view('web.privacy_policy', $result);
    }


    public function termsConditions()
    {
        $page = Content::select('title','description')->where('id','2')->first();
        $result = ['title' => "Terms and condition",'data'=>$page];
        return view('web.terms_conditions', $result);
    }

    public function category_detail($id)
    {

        $data = User::where('id', base64_decode($id))->first();

        if(isset($data)){

            $releted_item = User::where('business_type', $data->business_type)->get();
            $result = ['title' => "Category Detail", 'data' => $data, 'releted_item'=>$releted_item];
            return view('web.pages.category_detail', $result);
        }else{
            return Redirect::back()->with('error', 'Something went wrong');
        }
      
    }



    



    
    public function contactUs()
    {
        $result = ['title' => "Contact Us"];
        return view('web.pages.contact_us', $result);
    }

    public function howItWork()
    {
        $result = ['title' => "How It Work"];
        return view('web.pages.how_it_work', $result);
    }


    public function subscription()
    {
        $result = ['title' => "Subscription Fee"];
        return view('web.pages.subscription', $result);
    }





    public function disclaimer()
    {
        $result = ['title' => "Disclaimer"];
        return view('web.pages.disclaimer', $result);
    }


    public function refundPolicy()
    {
        $result = ['title' => "Refund policy"];
        return view('web.pages.refund', $result);
    }


    public function feedback()
    {
        $result = ['title' => "Feedback and complaint"];
        return view('web.pages.feedback', $result);
    }



    public function feedbackSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|max:30',
            'email' => 'required|max:30',
            'phone' => 'required',
            'subject' => 'required',
            'description' => 'required'
        ]);
        $data = new Feedback();
        $data->name = $request->get('name');
        $data->phone = $request->get('phone');
        $data->email = $request->get('email');
        $data->subject = $request->get('subject');
        $data->description = $request->get('description');
        if ($data->save()) {
            return response()->json(['status' => true, 'redirect_url'=>route('web.index'), 'message' => 'Feddback has been submitted']);
        } else {
            return response()->json(['status' => false,  'message' => 'Something went wrong']);
        }
    }


    public function model()
    {
        return view('other.model');
    }

    public function offers()
    {
        $result = ['title' => "Offers"];
        return view('web.pages.offers', $result);
    }


    public function get_offers(Request $request)
    {

        $q = Offer::where('status', Constant::ACTIVE);
        
        if(isset($request->city_id)) {
            $q->where('city_id', $request->city_id);
        }


        if (isset($request->main_locality_id)) {
            $q->where('main_locality_id', $request->main_locality_id);
        }

        if (isset($request->zip_code)) {
            $q->where('zip_code', $request->zip_code);
        }

        
        $response = $q->orderBy('created_at', 'Desc');
        $response = $q->paginate(6);
        $result = ['title' => "Offers", 'data'=> $response];

        return view('web.pages.offer_ajax', $result);



    }



    public function category()
    {
        $offer = Offer::where(['status' => Constant::ACTIVE])->limit(3);
        $result = ['title' => "Offers",'offer' => $offer ];
        return view('web.pages.category', $result);
    }


    public function searching(Request $request)
    {
       
        $query = User::where('status', Constant::ACTIVE);

        if(isset($request->business_type)) {
            $query =   $query->where('users.business_type', $request->business_type);
        }

        $response = $query->orderBy('created_at', 'Desc');

        // if(isset($request->discount_percentag)) {
        //     $query->where('discount',  '=<', $request->discount_percentag);
        // }

        if(isset($request->city_id)) {
            $query->where('city_id', $request->city_id);
        }


        if (isset($request->main_locality_id)) {
            $query->where('main_locality_id', $request->main_locality_id);
        }

        if (isset($request->zip_code)) {
            $query->where('zip_code', $request->zip_code);
        }

        $response = $query->orderBy('created_at', 'Desc');
        $response = $query->paginate(6);

        $result = ['title' => "Offers",'data' => $response ];
        return view('web.pages.category_ajax', $result);
    }



    



    public function get_category($id)
    {
      
        $data = User::where(['status' => Constant::ACTIVE, 'business_type' => base64_decode($id) ])->paginate(6);
        $result = ['title' => "Offers", 'data'=> $data];
        return view('web.pages.category_ajax', $result);
    }




    public function view_offer($id)
    {
        $data = Offer::where(['id' => $id,'status' => Constant::ACTIVE])->first();
        if(isset($data)){
            $result = ['title' => "Offers", 'data'=> $data];
            return view('web.pages.offer_view', $result);
        }else{
            return 'Data not found';
        }
    }



    public function pay_now($id)
    {
        $data = Offer::where(['id' => $id,'status' => Constant::ACTIVE])->first();
        if(isset($data)){
            $result = ['title' => "Offers", 'data'=> $data];
            return view('web.pages.offer_view', $result);
        }else{
            return 'Data not found';
        }
    }



}
