<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
//use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Faqs;
use App\Models\FaqsCategory;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\ContactAgent;
use App\Models\BusinessList;
use App\Models\Category;
use App\Models\Reviews;
use App\Models\Wishlist;
use App\Models\Business_item;
use App\Models\Carts;
use App\Models\ShippingAddress;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Cards;
use App\Http\Controllers\PaymentController;





use Auth,
    Mail,
    DB,
    DateTime;

//use DateTime;

class ApiController extends Controller
{   
    // common function for send api responce
    function sendResponse($result, $message, $other=null){
            
            $response = [
                'status_code' => 200,
                'status' => true,
                'message' => $message,                 
                'data' => $result,
            ];
            
        return response()->json($response,200);
    }

    // common function for send api error
    function sendError($error, $code = 200, $errorMessages = []){
        $response = [
                'status_code' => 400,
                'status' => false,
                'message' => $error,
                'data' => [],
            ];
        return response()->json($response,$code);
    }

    // function for send otp via email
    function otp_email($email, $otp, $name)
    {
        $content = getEmailContentValue(5);
        if($content){ // get email template content
            $emailval = $content->description;
            $subject = $content->subject.' - '.$name;
            //echo getSettingValue('logo'); die();
            if(empty(getSettingValue('logo'))){
                $logo = url('images/logo.png');
            }else{
                $logo = url('uploads/logo').'/'.getSettingValue('logo');
            } 
            $replace_data = [
                '@user_name' => $name,
                'otp_0' => $otp,
                '@logo' => $logo,
            ];

                foreach ($replace_data as $key => $value) {
                    $emailval = str_replace($key, $value, $emailval);
                }

               // print_r($emailval);die;
            if (sendMail($email, $emailval, $subject)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // function for register new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',  // [0 => send otp on registration , 1 => verify OTP on registeration]           
            'email' => 'required|string|email|max:255',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'confirm_password' => 'required|min:8|max:16|same:password|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',           
            'device_id' => 'required',
            'device_type' => 'required',
        ]);
        

        if ($validator->fails()) {
            
            return response()->json(['status' => 'false', 'message' => $validator->errors()], 200);
        } else {
            // check email is already exist or not
            $admin = User::select('email')->where('id', '1')->whereNull('deleted_at')->first();
            $email_validate = User::where('email', request('email'))->where('id','!=', '1')->where('is_delete','!=', '1')->whereNull('deleted_at')->first();
             if (!empty($email_validate)) {
                return $this->sendError("Your email address already exists.");
            }else if ($request['password'] != $request['confirm_password']) {
                return $this->sendError("Password not matched with confirm password.");
            }else if($admin->email==request('email')){
                return $this->sendError("Cann't use this email.");
            }else {
                if (request('type') == '0') {
                    $otp = rand(100000, 999999);
                    $user = (object)[];                    
                    $user->email = request('email');                  
                    $user->status = '1';
                    $user->verification_status = '0';
                    $user->fcm_token = request('fcm_token');
                    $user->device_id = request('device_id');
                    $user->device_type = request('device_type');
                    $user->password = request('password');
                    $user->confirm_password = request('confirm_password');
                    $user->otp = $otp;

                    // Send OTP on the email
                    $this->otp_email($user->email, $otp, '');
                    $msg = "OTP sent successfully";
                } else {
                    $otp = "";
                    $request['password'] = Hash::make($request['password']);
                    $user = User::create($request->toArray());
                    // Generate Token
                    config(['auth.guards.api.provider' => 'api']);
                    $token = $user->createToken('MyApp', ['api'])->accessToken; 

                    // create customer strip Id
                  
                    $strip_customer_result = PaymentController::createCustomerOnStripe($request->email);
              

                  

                    $user->auth_token = $token;
                    $user->name = request('full_name');
                    $user->email = request('email');
                    $user->mobile = request('mobile');
                    $user->status = '1';
                    $user->verification_status = '1';
                    $user->fcm_token = request('fcm_token');
                    $user->device_id = request('device_id');
                    $user->device_type = request('device_type');   
                    $user->stripe_customer_id = (isset($strip_customer_result->id) ? $strip_customer_result->id : '');                 
                    $user->update();
                    $user = User::where('id', $user->id)->first();
                   

                    $msg = "Your account has been successfully created";
                }
                return response()->json(['status' => true,'status_code'=>200,'message'=> $msg, 'data' => $user, 'otp' => $otp], 200);
            }
        }
    }


    // function for user login
    public function login(Request $request) 
    {   
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:8',   
            'device_id' => 'required',
            'device_type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        } else {
            $admin = User::select('email')->where('id', '1')->whereNull('deleted_at')->first();
            $user = User::whereRaw('( email = "'.request('email').'")')->where('deleted_at', null)->where('is_delete', '0')->first();  
            if (empty($user)) { // if user not exist
                return $this->sendError('These credentials do not match our records.');
            } else if (!Hash::check(request('password'), $user->password)) { // match password
                return $this->sendError('These credentials do not match our records.');
            } else if ($user->status == '0') {  // if user is inactive
                return $this->sendError('Your account is temporary blocked.');
            } else if ($user->verification_status=='0') {   // if user account is not verified
                // return $this->sendError('Your account is not varified.');
                return $this->sendError('These credentials do not match our records.');
            } else if ($admin->email==request('email')) {    // if email is admin email
                return $this->sendError("Cann't use this email.");
            } else {
                if (Auth::guard('api')->setUser($user)) {   // set user in api guard
                        
                        config(['auth.guards.api.provider' => 'api']);
                        $user = User::find(auth()->guard('api')->user()->id);
                        DB::table('oauth_access_tokens')->where('user_id', $user->id)->delete();
                        $token = $user->createToken('MyApp', ['api'])->accessToken; // create and update token
                        $user->auth_token = $token;
                        $user->fcm_token = request('fcm_token');
                        $user->device_id = request('device_id');
                        $user->device_type = request('device_type'); 

                       
                            
                        if ($user->save()) {

                            if(isset($user->image) && !empty($user->image)){
                                $user->image = url('uploads/user_profile').'/'.$user->image;
                                }

                            return $this->sendResponse($user, 'User login successfully.');
                        }else{
                            return $this->sendError("Something went wrong.");
                        }
                }
            }
        }
    }

    // function for Resend OTP on resend otp button click
    public function resendOTP(Request $request)
    {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                //'type' => 'required',    // type is [0 => resend OTP on Register,1 => resend OTP on login if not verify,2 => Forgot Password]
            ]);
            $email = request('email');
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        } else {
            $otp = rand(100000, 999999);

            $otp_array= array('otp'=> $otp);

            if ($this->otp_email( $email, $otp, '')) {  // if otp sended to user email for account verification
                return $this->sendResponse($otp_array, 'OTP sent successfully');
            } else {
                return $this->sendError("Email not sended to user!!");
            }

            // $user = User::where('email', $email)->where('is_delete','!=', '1')->whereNull('deleted_at')->first();
            // // get user details
            // if (isset($user) && $user->status == '1') { // send otp if user is active
            //     $otp = rand(100000, 999999);
            //     $user->otp = $otp;
            //     $user->update();
            //     // send mail for OTP 
                
            //         if ($this->otp_email($user->email, $otp, $user->first_name)) {  // if otp sended to user email for account verification
            //             return $this->sendResponse($user, 'OTP send successfully.');
            //         } else {
            //             return $this->sendError("Email not sended to user!!");
            //         }
            // } else if (isset($user) && $user->status == '0') {  // if user is inactive
            //     return $this->sendError('Your account has been temporary blocked.');
            // } else {    // if user is not found
            //     return $this->sendError('User Not found.');
            // }
        }
    }

    // function for opt verification 

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        } else {
            $user = User::where('id', request('user_id'))->first();
            if ($user) {    // verify otp if user found
                if ($user->otp == $request->otp) {  // check otp is correct                    
                    $user->verification_status = '1';
                    $user->otp = NULL;
                    $user->update();
                    if(request('type')=='1'){   // type = 1 is to send "succesfull registration" email after verify otp, in case of registration otp is verify
                        if (Auth::guard('api')->setUser($user)) {   // set user to guard
                            $content = getEmailContentValue(4);
                            if($content){
                                $emailval = $content->description;
                                $subject = $content->subject.' - '.$user->first_name;
                                //echo getSettingValue('logo'); die();
                                if(empty(getSettingValue('logo'))){
                                    $logo = url('images/logo.png');
                                }else{
                                    $logo = url('uploads/logo').'/'.getSettingValue('logo');
                                }
                                $replace_data = [
                                        '@user_name' => $user->first_name,
                                        '@logo' => $logo,
                                    ];

                                    foreach ($replace_data as $key => $value) {
                                        $emailval = str_replace($key, $value, $emailval);
                                    }
                                if (sendMail($user->email, $emailval, $subject)) {  // send "succesfull registration" email to user
                                    return $this->sendResponse($user, 'User register successfully.');
                                } else {
                                    return $this->sendError("Email not sended to user!!");
                                }
                            }
                        }else{  // user not login case
                            return $this->sendError('Something went wrong.');
                        }
                    }else{  // type is not 1 or not set
                        return $this->sendResponse($user, 'OTP Verified successfully.');
                    }
                } else {    // password is not matched
                    return $this->sendError('OTP not match.');
                }
            } else {    // user is not found
                return $this->sendError('User Not found.');
            }
        }
    }

    // function for reset password when user forgot password

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',           
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        } else {
            $user = User::where('email', request('email'))->whereNull('deleted_at')->first();
            if ($user) {
                   $otp = rand(100000, 999999);
                    $this->otp_email($user->email, $otp, '');
                    $user->otp = $otp;
                    $user->update();
                    return $this->sendResponse($user, 'OTP sent successfully');
            } else {
                return $this->sendError('User Not found.');
            }
        }
    }

    // function for user logout and delete token
    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        $user->device_id = NULL;
        $user->auth_token = NULL;
        $user->save();
        $user->token()->revoke();
        return $this->sendResponse([], 'Logged out succesfully.');
    }

    // function for delete user and delete token
    public function deleteUser(Request $request)
    {
        $user = Auth::guard('api')->user();
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->device_id = NULL;
        $user->auth_token = NULL;
        $user->is_delete = 1;
        $user->save();
        $user->token()->revoke();
        return $this->sendResponse([], 'User deleted succesfully.');
    }

    public function changePassword(Request $request)
    {
        
        //$user = Auth::guard('api')->user();   
        $validator = Validator::make($request->all(), [          
            'password' => 'required|min:8|max:16|same:confirm_password|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'confirm_password' => 'required|min:8|max:16|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',            
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        //$user = User::select('id')->where('id', $request->user_id)->where('otp', $request->otp)->count();
        $user = User::select('id')->where('id', $request->user_id)->count();

        if($user > 0){

            User::where('id', $request->user_id)
            ->update([
           'password' => Hash::make($request->password)
           ]);
   
          return $this->sendResponse([], 'Password has been changed successfully.');
         }else{
                return $this->sendError("User does not exist.");
         }
         
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|min:8|max:16|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password' => 'required|min:8|max:16|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', 
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        // match old password
        if (Hash::check($request->old_password, Auth::guard('api')->user()->password)) {

            if (Hash::check($request->password, Auth::guard('api')->user()->password)) {  

            return $this->sendError('Old password & new password are same please choose another password.');
            
            }else{

                User::find(Auth::guard('api')->user()->id)
                ->update([
                    'password' => Hash::make($request->password)
                ]);
                return $this->sendResponse([], 'Password has been changed successfully.');
            }


        } else {
            return $this->sendError('Old password incorrect.');
        }
    }


    public function businessList(Request $request)
      {
        //   $validator = Validator::make($request->all(), [
        //       'latitude' => 'required',
        //       'longitude' => 'required',
        //   ]);
  
        //   if ($validator->fails()) {
        //       return $this->sendError($validator->errors());
        //   }
          // get blogs data by slug and category id
          
        $data = BusinessList::select('name','profile_img','address')->get();
        $setDistance = 6371; //if get distance result in km then set 6371 and if get distance result in mile then set 3959
        $filter_condition ='';
        if(isset($request->category_id) && !empty($request->category_id)){
            $filter_condition = " and category_id = '".$request->category_id."'";
        }
        if(isset($request->name) && !empty($request->name)){
            $filter_condition = " and businesses.name like '%".$request->name."%'";
        }
        if(isset($request->address) && !empty($request->address)){ 
            $filter_condition = " and address like '%".$request->address."%'";
        }

        
        $location_radius  = getSettingValue('radius_location');

        if (isset($request->pageno)) { 
            $pageno = $request->pageno;
        } else {
            $pageno = 1;
        }

         $no_of_records_per_page = 10;
         $offset = ($pageno-1) * $no_of_records_per_page; 

 
        //LIMIT $offset, $no_of_records_per_page
        //HAVING distance < $location_radius
        
        if(isset($request->latitude) && !empty($request->latitude) && isset($request->longitude) && !empty($request->longitude) ){
            $query = "SELECT * FROM 
            (SELECT categories.name as category_name,businesses.id,businesses.name,latitude,longitude,address,profile_img,ROUND(( $setDistance * acos( cos( radians ($request->latitude) ) * cos( radians(latitude) ) * cos( radians(longitude ) - radians($request->longitude ) ) + sin( radians($request->latitude) ) * sin( radians(latitude ) ) ) ),2) AS distance FROM businesses left join categories on businesses.category_id = categories.id 
             WHERE 
             EXISTS
            (
                SELECT items.business_id, COUNT(items.business_id) AS 'Number of Rows'
                FROM business_items as  items
                WHERE businesses.id = items.business_id
                GROUP BY items.business_id
                HAVING COUNT(items.business_id) >0
            )               
            AND  
              businesses.status='1' AND businesses.is_active='1' AND  businesses.is_delete='0' $filter_condition HAVING distance < $location_radius ORDER BY distance ASC LIMIT $offset, $no_of_records_per_page) AS t";

        }else{
            $query = "SELECT * FROM (SELECT categories.name as category_name, businesses.id,businesses.name,address,latitude,longitude,profile_img, 10 AS distance FROM businesses left join categories on businesses.category_id = categories.id  
             WHERE 
             EXISTS
            (
                SELECT items.business_id, COUNT(items.business_id) AS 'Number of Rows'
                FROM business_items as items
                WHERE businesses.id = items.business_id
                GROUP BY items.business_id
                HAVING COUNT(items.business_id) >0
            )               
            AND  
             businesses.status='1' AND businesses.is_active='1' AND  businesses.is_delete='0' $filter_condition HAVING distance < $location_radius   ORDER BY name ASC LIMIT $offset, $no_of_records_per_page) AS t";

        } 
        $data = DB::select(DB::raw( $query));


        $bussness_array =  array(); 
          if ($data) {
            foreach($data as $val){

                $reviews_avg = Reviews::where('business_id', $val->id)->where('is_approved', '1')->avg('rating');
                $check_wishlist = 0;
                if(isset($request->user_id) && !empty($request->user_id)){
                    $check_wishlist = Wishlist::select('id')->where('business_id', $val->id)->where('user_id', $request->user_id)->count();
 
                }

                $bussness_array[] = array(
                    'id' => $val->id,
                    'name' => $val->name,
                    'category_name' => $val->category_name,
                    'address' => $val->address,
                    'latitude' => $val->latitude,
                    'longitude' => $val->longitude,
                    'grade' => round($reviews_avg,1),
                    'is_wishlist' => $check_wishlist,
                    'profile_img' => url('uploads/business_profile_small/').'/'.$val->profile_img,
                );
            }
 
          $app_version =   getSettingValue('app_version');
          $update_app =   getSettingValue('update_on_app');
          $is_maintenance =   getSettingValue('is_maintenance');
         

              $response = [
                  'status' => true,
                  'app_version' => $app_version,
                  'update_app' => $update_app,
                  'is_maintenance' => $is_maintenance,
                  'message' => 'Data get successfully.',
               
                  'data' => $bussness_array                  
              ];
              return response()->json($response,200);
          }else{
              return $this->sendError("No data found.");
          }   
    }

    
    public function businessDetails(Request $request)
       {
           $validator = Validator::make($request->all(), [
               'business_id' => 'required',               
           ]);
   
           if ($validator->fails()) {
               return $this->sendError($validator->errors());
           }

         

           // get blogs data by slug and category id
           $BusinessDetails = BusinessList::select('id','name','category_id','profile_img','address','location','latitude','longitude','description')->where('id',$request->business_id)->first();

           $category = Category::select('name')->where('id',$BusinessDetails->category_id)->first();
           $BusinessDetails->category_name = (isset($category->name) ? $category->name : '');

           $reviews_avg = Reviews::where('business_id', $request->business_id)->where('is_approved', '1')->avg('rating');

           $check_wishlist = 0;
           if(isset($request->user_id) && !empty($request->user_id)){
               $check_wishlist = Wishlist::select('id')->where('business_id', $request->business_id)->where('user_id', $request->user_id)->count();

           }

           $BusinessDetails->grade = round($reviews_avg,1);
           $BusinessDetails->is_wishlist =$check_wishlist;
           $BusinessDetails->profile_img = url('uploads/business_profile/').'/'.$BusinessDetails->profile_img;
  
          $bussiness_photo_url = url('uploads/business_image_medium/'); 
          $bussiness_photo_url_popup = url('uploads/business_image/');
          $bussness_photos_qeury = "SELECT CONCAT('".$bussiness_photo_url."/',image) as bussiness_photo,CONCAT('".$bussiness_photo_url_popup."/',image) as bussiness_photo_popup FROM business_images where business_id ='".$request->business_id."'";
          $bussiness_photo_data = DB::select(DB::raw( $bussness_photos_qeury));
       
        
          $items_qeury = "SELECT id,item_name,item_price,item_description FROM business_items where status='1' and  is_delete='0' and business_id ='".$request->business_id."'";
          $items_data = DB::select(DB::raw( $items_qeury));
          $item_array = array();

         

          $item_image = url('uploads/item_image/');
          foreach( $items_data as $itemval){
            
            $items_qeury = "SELECT image FROM business_item_images where item_id ='".$itemval->id."'";
            $items_data = DB::select(DB::raw( $items_qeury));
            $items_data_img = '';
            if(isset($items_data[0]->image) && !empty($items_data[0]->image)){
                $items_data_img = $item_image.'/'.$items_data[0]->image;
            }

            $item_array[] = array(
                'id' => $itemval->id,
                'item_name' => $itemval->item_name,
                'item_price' => $itemval->item_price,
                'item_description' => $itemval->item_description,
                'item_img' => $items_data_img,
            );
          }

      if (isset($request->pageno)) { 
            $pageno = $request->pageno;
        } else {
            $pageno = 1;
        }

         $no_of_records_per_page = 10;
         $offset = ($pageno-1) * $no_of_records_per_page; 

         
        //   $review_data = Reviews::select('name','image','business_review.id','rating','review','business_review.created_at')
        //   ->leftJoin('users', function($join) {
        //         $join->on('users.id', '=', 'business_review.user_id');
        //       })->where('business_review.business_id',$request->business_id)->where('business_review.is_approved', '1')->orderBy('business_review.created_at', 'desc')->get();

        $query_review = "SELECT name,image,business_review.id,rating,review,business_review.created_at FROM business_review left join users on users.id =business_review.user_id   WHERE business_review.is_approved='1' AND  business_review.business_id='".$request->business_id."'  ORDER BY business_review.created_at desc LIMIT $offset, $no_of_records_per_page";
        $review_data = DB::select(DB::raw( $query_review));
          $review_array = array();
          $user_img = url('uploads/user_profile/');
          foreach( $review_data as $reviewVal){ 
            $review_array[] = array(
                'id' => $reviewVal->id,
                'user_name' => $reviewVal->name,
                'rating' => $reviewVal->rating,
                'review' => $reviewVal->review,
                'created_at' => date("Y-m-d H:i:s", strtotime($reviewVal->created_at)),
                'profile_img' =>$user_img.'/'.$reviewVal->image,
            );
          }


        $data_array = array('businessDetail' => $BusinessDetails,'business_photos' => $bussiness_photo_data, 'item_data'=>$item_array,'review_data'=>$review_array);

        
           if ($BusinessDetails) {
           
               $response = [
                   'status' => true,
                   'message' => 'Data get successfully.',
                   'data' => $data_array                  
               ];
               return response()->json($response,200);
           }else{
               return $this->sendError("No data found.");
           }   
    }


    public function get_faqs(Request $request)
    {
        $data = [];
        // get category list
        $category = FaqsCategory::select('id','name')->where('status','1')->whereNull('deleted_at')->get();
        foreach ($category as $cat) {
            // get faqs list by category id
            $list = Faqs::select('faq','description')->where('category_id',$cat->id)->where('status','1')->whereNull('deleted_at')->orderBy('id', 'desc')->get();
            $array = array('category_name' => $cat->name, 'list'=>$list);
            $data = Arr::prepend($data,$array);
        }
        if ($data) {
            return $this->sendResponse($data, 'Data get successfully.');
        }else{
            return $this->sendError("No data found.");
        }
    }


    public function get_blogs(Request $request)
    {   
        $data = [];
        // get category list
        $category = BlogCategory::select('id','name')->where('status','1')->whereNull('deleted_at')->get();
        foreach ($category as $cat) {
            // get blogs list by category id
            $list = Blog::select('title','description','slug','image')->where('category_id',$cat->id)->where('status','1')->whereNull('deleted_at')->orderBy('id', 'desc')->get();
            $array = array('category_name' => $cat->name, 'list'=>$list);
            $data = Arr::prepend($data,$array);
        }
        if ($data) {
            $response = [
                'status' => true,
                'message' => 'Data get successfully.',
                'data' => $data,
                'image_url' => url('uploads/blog_image'),
            ];
            return response()->json($response,200);
            //return $this->sendResponse($data, 'Data get successfully.', array('image_url' => $image_url ));
        }else{
            return $this->sendError("No data found.");
        }   
    }


    public function blog_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        // get blogs data by slug and category id
        $data = Blog::select('title','description','image')->where('category_id',$request->category_id)->where('slug',$request->slug)->where('status','1')->whereNull('deleted_at')->first();
        if ($data) {
            $response = [
                'status' => true,
                'message' => 'Data get successfully.',
                'data' => $data,
                'image_url' => url('uploads/blog_image'),
            ];
            return response()->json($response,200);
        }else{
            return $this->sendError("No data found.");
        }   
    }


    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'mobile' => 'required|numeric',
            'country_code' => 'required|string',
            'image' => 'mimes:jpeg,png,jpg',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $user = User::where('id',Auth::guard('api')->user()->id)->first();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->country_code = $request->country_code;

        if(User::where('mobile', '=', $request->get('mobile'))->where('id', '!=', $user->id)->count() > 0){
            return $this->sendError('Phone number already exists');
         }
      
        if ($request->hasFile('image')) {
            $path = 'uploads/user_profile/';
            if(!empty($user->image)){
                deleteImage($path . $user->image);
            }
            $image_path =  uploadImage($request->image, $path);
            $user->image = $image_path;
        }
        
        if($user->save()){
            $user = User::where('id',Auth::guard('api')->user()->id)->first();
            $user->image = url('uploads/user_profile').'/'.$user->image;
           

            return $this->sendResponse($user, 'Profile updated successfully');
        }else{
            return $this->sendError('Something went wrong.');
        }
    }


    public function updateImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|mimes:jpeg,png,jpg',
            //File::types(['jpeg', 'png','jpg'])
            // ->min(1024)
            // ->max(12 * 1024),
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $user = User::where('id',Auth::guard('api')->user()->id)->first();
        if ($request->hasFile('image')) {
            $path = 'uploads/user_profile/';
            if(!empty($data->image)){
                deleteImage($path . $data->image);
            }
            $image_path =  uploadImage($request->image, $path);
            $user->image = $image_path;
        }
        if($user->save()){
            return $this->sendResponse([], 'Profile image update successfully.');
        }else{
            return $this->sendError('Something went wrong.');
        }
    }

    public function categoryList(Request $request)
    {
      
        $query = Category::select('id', 'name')->where('status', '1')->where('is_delete', '0');

        if (isset($request->category_name) && $request->category_name != '') {

          $query->where('name', 'like', '%' . $request->category_name . '%');           

        } 
        $category = $query->get();


        if ($category && count($category) > 0) {        
            return $this->sendResponse($category, 'Data get successfully.');
        }else{
            return $this->sendError("No data found.");
        }
    }


    public function send_feedback(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'user_feedback' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $content = getEmailContentValue(5);
        if($content){ // get email template content
            $emailval = $content->description;
            $subject = $content->subject.' - '.Auth::guard('api')->user()->first_name;
            //echo getSettingValue('logo'); die();
            if(empty(getSettingValue('logo'))){
                $logo = url('images/logo.png');
            }else{
                $logo = url('uploads/logo').'/'.getSettingValue('logo');
            }
            $email_data = '';
            if(isset($request->user_email) && !empty($request->user_email)){
                $email_data = 'User Email ID : '.$request->user_email;
            }
            $replace_data = [
                    '@user_name' => Auth::guard('api')->user()->first_name,
                    '@user_feedback' => $request->user_feedback,
                    '@email_data' => $email_data,
                    '@logo' => $logo,
                ];

                foreach ($replace_data as $key => $value) {
                    $emailval = str_replace($key, $value, $emailval);
                }
            if (sendMail(getSettingValue('company_email'), $emailval, $subject)) {
                return $this->sendResponse([], 'Feedback send successfully.');
            } else {
                return $this->sendError('Something went wrong.');
            }
        } else {
            return $this->sendError('Something went wrong.');
        }
    }

 
    public function contact_agent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required',
            'property_data' => 'required|json',
            'user_name' => 'required|string',
            'email' => 'required|email',
            'phone_no' => 'required',
            'question' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $data = new ContactAgent();
        $data->user_id = Auth::guard('api')->user()->id;
        $data->property_id = $request->property_id;
        $data->property_data = $request->property_data;
        $data->user_name = $request->user_name;
        $data->email = $request->email;
        $data->phone_no = $request->phone_no;
        $data->question = $request->question;
        $data->status = 'pending';
        
        if($data->save()){
            return $this->sendResponse([], 'Inquiry send successfully.');
        }else{
            return $this->sendError('Something went wrong.');
        }
    }
 
    public function notification_setting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_type' => 'required',   // ['1'=>app, '2'=>email, '3'=>SMS]
            'status' => 'required',     // ['0'=>notifications not send, '1'=>notifications send to user]
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $user = User::where('id',Auth::guard('api')->user()->id)->first();
        if($request->notification_type=='1'){
            $user->notifications = $request->status;
        }elseif($request->notification_type=='2'){
            $user->email_notifications = $request->status;
        }elseif($request->notification_type=='3'){
            $user->sms_notifications = $request->status;
        }
        
        if($user->save()){
            return $this->sendResponse([], 'Notification settings updated successfully.');
        }else{
            return $this->sendError('Something went wrong.');
        }
    }

    public function addReview(Request $request)
    {
 
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',                   
            'rating' => 'required',
            'review' => 'required',           
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
 
        $data = new Reviews();
        $data->user_id = $user->id;
        $data->business_id = $request->business_id; 
        $data->rating = $request->rating;
        $data->review = $request->review; 
        $data->is_approved = 1;         
        $data->created_at = date("Y-m-d H:i:s"); 

        // $reviews_avg = Reviews::where('business_id', $request->business_id)->where('is_approved', '1')->avg('rating');
 

        // $grade = round($reviews_avg,1);

        if($data->save()){

            $reviews_avg = Reviews::where('business_id', $request->business_id)->where('is_approved', '1')->avg('rating');
  
            $grade = round($reviews_avg,1);

            $response = [
                'status_code' => 200,
                'status' => true,
                'message' => 'Business review has been submitted successfully',                 
                'grade' => $grade,
                'data' => array(),
            ];
            
        return response()->json($response,200);

          //  return $this->sendResponse([], 'Business review has been submitted successfully');
        }else{
            return $this->sendError('Something went wrong.');
        }
    }
 
     public function Addfavourite(Request $request)
     {
         $user = Auth::guard('api')->user();
         $validator = Validator::make($request->all(), [
             'business_id' => 'required',  
         ]); 
         if ($validator->fails()) {
             return $this->sendError($validator->errors());
         }         
         $check_wishlist_data = Wishlist::select('id')->where('business_id', $request->business_id)->where('user_id', $user->id)->count();
        
         if($check_wishlist_data > 0){ //Delete
            Wishlist::where('business_id', $request->business_id)->where('user_id', $user->id)->delete();
            return $this->sendResponse([], 'Business has been removed from favourite list');

         }else{ // Add
            $data = new Wishlist();
            $data->user_id = $user->id;
            $data->business_id = $request->business_id;                  
            $data->created_at = date("Y-m-d H:i:s");  
            if($data->save()){
                return $this->sendResponse([], 'Business has been added in favourite list successfully');
            }else{
                return $this->sendError('Something went wrong.');
            }
         }         
        
     }
  
     public function favouriteList(Request $request)
       {
           $user = Auth::guard('api')->user();
           
           $review_data = Wishlist::select('businesses.id','name','profile_img','address')
                         ->leftJoin('businesses', function($join) {
                        $join->on('businesses.id', '=', 'business_wishlist.business_id');
                    })
                    ->where('businesses.status','1')
                    ->where('businesses.is_active','1')
                    ->where('businesses.is_delete','0')
                    ->where('business_wishlist.user_id',$user->id)->orderBy('business_wishlist.created_at', 'desc')->get();
                    $bussiness_photo_url = url('uploads/business_profile_small/');
              $data_array =  array();
              if($review_data ){
                foreach($review_data as $val){
                     
                  $reviews_avg = Reviews::where('business_id', $val->id)->where('is_approved', '1')->avg('rating');
 

                    $data_array[] = array(
                         "id" => $val->id,
                         "name" => $val->name,
                         "bussiness_img" =>  $bussiness_photo_url.'/'.$val->profile_img,
                         "address" => $val->address,
                         "grade" => round($reviews_avg,1)   
                        );
                }
               return $this->sendResponse($data_array, 'Record list');
              }else{
                  return $this->sendError('Something went wrong.');
              }
                   
          
       }
     
     public function feedsList(Request $request)
     {
         $user = Auth::guard('api')->user();
      
         DB::enableQueryLog();
         
         $review_data = Wishlist::select('businesses.id','business_items.id as item_id','item_description','item_name','name','profile_img','address','business_items.created_at')
                       ->leftJoin('businesses', function($join) {
                      $join->on('businesses.id', '=', 'business_wishlist.business_id'); 
                  })
                  ->leftJoin('business_items', function($join) {
                      $join->on('businesses.id', '=', 'business_items.business_id'); 
                  })
                  ->where('business_items.status','1')
                  ->where('business_items.is_delete','0')
                  ->where('businesses.status','1')
                  ->where('businesses.is_active','1')
                  ->where('businesses.is_delete','0')
                  ->where('business_wishlist.user_id',$user->id)->orderBy('business_items.created_at', 'desc')->get();
 
            $bussiness_photo_url = url('uploads/business_profile_small/');
            $bussiness_item_url = url('uploads/item_image_medium/');
            $data_array =  array();
            if($review_data ){
              foreach($review_data as $val){
 


                $items_qeury_img = "SELECT image FROM business_item_images where item_id ='".$val->item_id."'";
                    $items_img = DB::select(DB::raw( $items_qeury_img));
                    $image ='';
                if(isset($items_img[0]->image) && !empty($items_img[0]->image)){
                   $image =  $bussiness_item_url.'/'.$items_img[0]->image;
                }

                  $data_array[] = array(
                       "id" => $val->id,
                       "name" => $val->name,
                       "item_id" => $val->item_id,
                       "item_name" => $val->item_name, 
                       "item_description" => $val->item_description, 
                       "created_at" => date("Y-m-d H:i:s", strtotime($val->created_at)),
                       "bussiness_img" =>  $bussiness_photo_url.'/'.$val->profile_img,
                       "item_img" =>   $image,  
                       
                       
                      );
              }
             return $this->sendResponse($data_array, 'Record list');
            }else{
                return $this->sendError('No Record Found');
            }
                 
        
     }

     public function itemDetail(Request $request)
     {         
            $validator = Validator::make($request->all(), [
                'item_id' => 'required'              
            ]);
            
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
          
            $bussiness_item_url = url('uploads/item_image_medium/');
            $data_array =  array(); 
            $check_cart =0;
            if(isset($request->user_id) && !empty($request->user_id)){ 
               // $check_cart = Carts::select('SUM(qty) as total_qty')->where('user_id', $request->user_id)->get();  
                $check_cart =  Carts::where('user_id', $request->user_id)->sum('qty');
            }
           
        
            $itemval = Business_item::select('id','business_id','item_name','item_price','item_description')->where('id', $request->item_id)->first();
            $data_item_array =  array();

            //foreach( $busness_items as $itemval){
                
                $items_qeury_img = "SELECT CONCAT('".$bussiness_item_url."/',image) as image  FROM business_item_images where item_id ='".$itemval->id."'";
                $items_img = DB::select(DB::raw( $items_qeury_img));

                $data_item_array = array(
                    "id" => $itemval->id,
                    "business_id" => $itemval->business_id,
                    "item_cart_count" => $check_cart,
                    "item_name" => $itemval->item_name, 
                    "item_price" => $itemval->item_price, 
                    "item_description" => $itemval->item_description,   
                    "item_img" =>$items_img,
                  
                  
                    );
            //}
            if($data_item_array ){              
             return $this->sendResponse($data_item_array, 'Record list');
            }else{
                return $this->sendError('No Record Found');
            }
                 
        
     }

     public function notificationSettingUpdate(Request $request)
     {
         $user = Auth::guard('api')->user();
         $validator = Validator::make($request->all(), [
             'is_notify' => 'required', 
            
         ]); 
         if ($validator->fails()) {
             return $this->sendError($validator->errors());
         }         
           
        User::where('id', $user->id)->update([
            'is_notification' => $request->is_notify
            ]);
        return $this->sendResponse([], 'Notification settings updated successfully');
 
     }
     public function notificationSetting(Request $request)
     {
         $user = Auth::guard('api')->user();               
        return $this->sendResponse($user, 'Record Data');
 
     } 
     public function addCart(Request $request)
     {
         $user = Auth::guard('api')->user();
         $validator = Validator::make($request->all(), [
             'business_id' => 'required',  
             'item_id' => 'required',  
             'qty' => 'required',  
         ]); 
         if ($validator->fails()) {
             return $this->sendError($validator->errors());
         }         
         $check_data = Carts::select('id')->where('business_id', $request->business_id)->where('item_id', $request->item_id)->where('user_id', $user->id)->count();
        
         if($request->qty > 0){
         if($check_data > 0){ //Update   

            $get_cart_item = Carts::select('qty')->where('business_id', $request->business_id)->where('item_id', $request->item_id)->where('user_id', $user->id)->first();
            $total_cart_item_qty =  $get_cart_item->qty + $request->qty;

            Carts::where('business_id', $request->business_id)->where('user_id', $user->id)->where('item_id', $request->item_id)->update([
                'qty' => $total_cart_item_qty
                ]);;
            return $this->sendResponse([], 'Item added to cart successfully');

         }else{ // Add
            $data = new Carts();
            $data->user_id = $user->id;
            $data->business_id = $request->business_id;         
            $data->item_id = $request->item_id;     
            $data->qty = $request->qty;         
            $data->created_at = date("Y-m-d h:i:s");  
            if($data->save()){
                return $this->sendResponse([], 'Item added to cart successfully');
            }else{
                return $this->sendError('Something went wrong.');
            }
         }  
        }else{
            return $this->sendError('Zero quantity not allowed to add to cart');
        }

        
     }

     public function updateCart(Request $request)
     {
         $user = Auth::guard('api')->user();
         $validator = Validator::make($request->all(), [            
             'item_id' => 'required',  
             'qty' => 'required',  
         ]); 
         if ($validator->fails()) {
             return $this->sendError($validator->errors());
         }   
         if($request->qty > 0){
                  
         $check_data = Carts::select('id')->where('item_id', $request->item_id)->where('user_id', $user->id)->count();
        

         if($check_data > 0){ //Update  

            Carts::where('item_id', $request->item_id)->update([
                'qty' => $request->qty
                ]);;
            return $this->sendResponse([], 'Item updated to cart successfully');

         }else{ // Add

            return $this->sendError('Something went wrong.');
         }  
         
        }else{
            return $this->sendError('Zero quantity not allowed to add to cart');
        }  
         
         
        
     }

     public function deleteCart(Request $request)
     {
         $user = Auth::guard('api')->user();
         $validator = Validator::make($request->all(), [ 
             'item_id' => 'required',  
              
         ]); 
         if ($validator->fails()) {
             return $this->sendError($validator->errors());
         }   

         $check_data = Carts::select('id')->where('id', $request->item_id)->count(); 

         if($check_data > 0){ //Deleted
            Carts::where('id', $request->item_id)->delete(); 
            return $this->sendResponse([], 'Item deleted successfully');
         }else{           
                return $this->sendError('Something went wrong.');
            
         }          
        
     }
     public function cartList(Request $request)
     {
         $user = Auth::guard('api')->user(); 
         $check_data = Carts::select('id')->where('user_id', $user->id)->count();  
         if($check_data > 0){ //Update  
            
            
            
            
            $cart_data = Carts::select('carts.id','carts.business_id','carts.item_id','name','item_name','item_price','qty')
                    ->leftJoin('businesses', function($join) {
                $join->on('businesses.id', '=', 'carts.business_id');
            })
            ->leftJoin('business_items', function($join) {
                $join->on('business_items.id', '=', 'carts.item_id');
            })
            ->where('carts.user_id',$user->id)->orderBy('carts.id', 'desc')->get();

             $bussiness_item_url = url('uploads/item_image_small/');
            $data_item_array =  array();
            $itemimg = '';

            $service_charges =   getSettingValue('service_charges');

            $total_amount = 0;
            foreach( $cart_data as $itemval){
               
               $items_qeury_img = "SELECT image FROM business_item_images where item_id ='".$itemval->item_id."'";
               $items_img = DB::select(DB::raw( $items_qeury_img));
               if(isset($items_img[0]->image) && !empty($items_img[0]->image)){
                $itemimg = $bussiness_item_url.'/'.$items_img[0]->image;
               }

               $data_item_array[] = array(
                   "id" => $itemval->id,
                   "business_id" => $itemval->business_id,
                   "item_id" => $itemval->item_id,
                   "business_name" => $itemval->name,
                   "item_name" => $itemval->item_name, 
                   "item_price" => $itemval->item_price, 
                   "qty" => $itemval->qty, 
                   "item_img" =>  $itemimg,  
                  );
                  $total_amount+= ($itemval->item_price*$itemval->qty);

            }

            $total_service_charges = ($total_amount*$service_charges)/100;


            $card_data = Cards::select('id','card_number','card_holder_name','expire_date','card_name','is_primary')
            ->orderBy('created_at', 'desc')->first();

            $response = [
                'status_code' => 200,
                'status' => true,
                'message' => 'Cart Data',
                'service_charges' => $total_service_charges,
                'card_data' => $card_data,
                'data' => $data_item_array,
            ];
            
           return response()->json($response,200);
            
 
           // return $this->sendResponse($data_item_array, 'Cart Data',$service_charges);

         }else{ // Add
            return $this->sendError('Your Cart is Empty');
         }         
        
     }

     public function addShippingAddress(Request $request)
     {
         $user = Auth::guard('api')->user();
         $validator = Validator::make($request->all(), [
             'address' => 'required',  
             'city' => 'required',  
             'zip_code' => 'required',  
             'country' => 'required', 
         ]); 
         if ($validator->fails()) {
             return $this->sendError($validator->errors());
         }         
       
            $data = new ShippingAddress();
            $data->user_id = $user->id;
            $data->address = $request->address;         
            $data->city = $request->city;     
            $data->zip_code = $request->zip_code;        
            $data->apt_no = $request->apt_no;    
            $data->country = $request->country;    
            $data->country = $request->country;  
            $data->status = 1;  
            $data->is_delete = 0;  
            $data->created_at = date("Y-m-d h:i:s");  
            if($data->save()){
                return $this->sendResponse([], 'Shipping address added successfully');
            }else{
                return $this->sendError('Something went wrong.');
            }
                 
        
     }
     public function updateShippingAddress(Request $request)
     {
         //$user = Auth::guard('api')->user();
         if(isset($request->type) && $request->type == 0){ //update
         $validator = Validator::make($request->all(), [
             'address' => 'required',  
             'city' => 'required',  
             'zip_code' => 'required',  
             'country' => 'required', 
             'shipping_id' => 'required',
             'type' => 'required',
         ]); 
        }else{
            $validator = Validator::make($request->all(), [ 
                'shipping_id' => 'required',
                'type' => 'required',
            ]); 

        }

         if ($validator->fails()) {
             return $this->sendError($validator->errors());
         }         
        
            if(isset($request->type) && $request->type == 0){ //update
                ShippingAddress::where('id', $request->shipping_id)
                ->update([
                    'address' => $request->address,
                    'city' => $request->city,
                    'zip_code' => $request->zip_code,
                    'apt_no' => $request->apt_no,
                    'country' => $request->country,
                ]);
                return $this->sendResponse([], 'Shipping address updated successfully');
            }else{ //Delete

                ShippingAddress::where('id', $request->shipping_id)->delete();
                return $this->sendResponse([], 'Shipping address deleted successfully');
 
            } 
                 
        
     }
     public function shippingAddressList(Request $request)
     {
         $user = Auth::guard('api')->user(); 
         $address_data = ShippingAddress::select('shipping_address.id','address','apt_no','city','zip_code','country','country.name as country_name','city.name as city_name')
         ->leftJoin('country', function($join) {
            $join->on('country.id', '=', 'shipping_address.country');
        })
        ->leftJoin('city', function($join) {
            $join->on('city.id', '=', 'shipping_address.city');
        })
        ->where('shipping_address.user_id',$user->id)->orderBy('shipping_address.id', 'desc')->get();
 
         if(count($address_data )> 0){ //Update   
            return $this->sendResponse( $address_data, 'Record Data');

        }else{ // Add
            return $this->sendError('No Address Added Yet.');
         }                   
        
     }

     public function countryList(Request $request)
     {
         $query = "SELECT id,name FROM country where status='1' and is_delete = '0' order by name ASC "; 
         $data = DB::select(DB::raw( $query));

         if(count($data )> 0){  
            return $this->sendResponse($data,'Record Data');

        }else{ 
            return $this->sendError('No Record');
         }                  
        
     }

     public function cityList(Request $request)
     {
        
        $validator = Validator::make($request->all(), [ 
            'country_id' => 'required'           
        ]);  

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }  
        

         $query = "SELECT id,name FROM city where country_id='".$request->country_id."' and  status='1' and is_delete = '0' order by name ASC "; 
         $data = DB::select(DB::raw( $query));

         if(count($data )> 0){  
            return $this->sendResponse($data,'Record Data');

        }else{ 
            return $this->sendError('No Record');
         }                  
        
     }
     public function notificationList(Request $request)
     {
         $user = Auth::guard('api')->user(); 
         $notification_data = Notification::select('id','title','message','notification_type','created_at','redirect_id','notification_status')
          ->orderBy('created_at', 'desc')->where('user_id',$user->id)->get();
          //->where('user_id',$user->id)
          $notification_array = array();
         if(count($notification_data )> 0){  

            foreach($notification_data  as $val){

                $notification_array[] = array(
                    "id" => $val->id,
                    "title"=> $val->title,
                    "redirect_id"=> $val->redirect_id,
                    "notification_status"=> $val->notification_status,
                    "message"=> $val->message,
                    "notification_type"=> $val->notification_type,
                    "created_at"=> date("Y-m-d H:i:s", strtotime($val->created_at)) 
                );
            }
            
            
            return $this->sendResponse( $notification_array, 'Record Data');

        }else{ // Add 
            return $this->sendError('Record not found.');
         }                   
        
     }

     public function cardList(Request $request)
     {
         $user = Auth::guard('api')->user(); 
         $card_data = Cards::select('id','card_number','card_holder_name','expire_date','card_name','is_primary')
          ->orderBy('created_at', 'desc')->get();
          //->where('user_id',$user->id)
          $card_array = array();
         if(count($card_data )> 0){   
            // foreach($card_data  as $val){

            //     $card_array[] = array(
            //         "id" => $val->id,
            //         "card_number"=> $val->card_number,
            //         "card_holder_name"=> $val->card_holder_name,
            //         "expire_date"=> $val->expire_date,
            //         "card_name"=> $val->card_name,
            //         "is_primary"=> $val->is_primary, 
            //     );
            // }  
         return $this->sendResponse( $card_data, 'Records Data');

        }else{  
            return $this->sendError('Record not found.');
         }                   
        
     }

 

     public function createOrder(Request $request)
     {
         $user = Auth::guard('api')->user();
         $validator = Validator::make($request->all(), [            
             'shipping_id' => 'required',   
             'service_charges' => 'required', 
             'payment_type' => 'required', 
             'payment_status' => 'required', 
             'transaction_id' => 'required', 
         ]); 
         if ($validator->fails()) {
             return $this->sendError($validator->errors());
         }         
 
         $get_carts = Carts::select('carts.id','business_items.business_id','carts.item_id','item_name','item_price','qty')       
        ->leftJoin('business_items', function($join) {
            $join->on('business_items.id', '=', 'carts.item_id');
        })
        ->where('carts.user_id',$user->id)->where('business_items.status','1')->where('business_items.is_delete','0')->orderBy('carts.id', 'desc')->get();
           

    //    $cart_total_query = "SELECT SUM(item_price*qty) as total_amount FROM carts left join business_items as items ON carts.item_id = items.id   where carts.user_id ='".$user->id."' AND items.status='1' AND items.is_delete='0'";
 
    //    $cart_total = DB::select(DB::raw( $cart_total_query));
    //    $service_charges=0;

    //    if(isset($cart_total[0]->total_amount) && !empty($cart_total[0]->total_amount)){

    //     $service_charges = ($cart_total[0]->total_amount*$request->service_charges)/100;
    //    }
    


        $address_data = ShippingAddress::select('shipping_address.id','address','apt_no', 'zip_code', 'country.name as country_name','city.name as city_name')
        ->leftJoin('country', function($join) {
           $join->on('country.id', '=', 'shipping_address.country');
       })
       ->leftJoin('city', function($join) {
           $join->on('city.id', '=', 'shipping_address.city');
       })
       ->where('shipping_address.id',$request->shipping_id)->first();

       $user_address = $address_data->address.' '.$address_data->zip_code;

         if(count($get_carts) > 0){      
            foreach( $get_carts as $val){ 

            $data = new Order();
            $data->user_id = $user->id;
            $data->business_id = $val->business_id;         
            $data->shipping_id = $request->shipping_id; 
            $data->item_id = $val->item_id; 
            $data->item_name = $val->item_name;  
            $data->item_price = $val->item_price;  
            $data->quantity = $val->qty;  
            $data->shipping_charges = $request->service_charges;    
            $data->payment_type = $request->payment_type;  
            $data->payment_status = $request->payment_status;    
            $data->transaction_id = $request->transaction_id; 
            $data->address = $address_data->address;     
            $data->apt_no = $address_data->apt_no;  
            $data->city = $address_data->city_name;  
            $data->zip_code = $address_data->zip_code;  
            $data->country = $address_data->country_name;  
            $data->is_delete = 0;   
            $data->order_status = 0;          
            $data->created_at = date("Y-m-d h:i:s");  
            $data->save();  


            }

         Carts::where('user_id', $user->id)->delete(); 
         return $this->sendResponse([], 'Your order has been placed successfully');
            
         }else{
           return $this->sendError('Cart is empty'); 
         }         
        
     } 

     public function orderList(Request $request)
     {
         $user = Auth::guard('api')->user(); 
         $check_data = Order::select('id')->where('user_id', $user->id)->count();  
         if($check_data > 0){  
            
            $order_data = Order::select('orders.id','orders.item_name','orders.item_id','order_status','reject_reason') 
            ->leftJoin('business_items', function($join) {
                $join->on('business_items.id', '=', 'orders.item_id');
            })
            ->where('orders.user_id',$user->id)->orderBy('orders.created_at', 'desc')->get();

             $bussiness_item_url = url('uploads/item_image_small/');
             $data_item_array =  array();
             $itemimg = ''; 
            foreach( $order_data as $itemval){
               
               $items_qeury_img = "SELECT image FROM business_item_images where item_id ='".$itemval->item_id."'";
               $items_img = DB::select(DB::raw( $items_qeury_img));
               if(isset($items_img[0]->image) && !empty($items_img[0]->image)){
                $itemimg = $bussiness_item_url.'/'.$items_img[0]->image;
               }

               $data_item_array[] = array(
                   "id" => $itemval->id,
                   "item_name" => $itemval->item_name,
                   "order_status" => $itemval->order_status,
                   "reject_reason" => $itemval->reject_reason, 
                   "item_img" =>  $itemimg,  
                  );

            }

            $response = [
                'status_code' => 200,
                'status' => true,
                'message' => 'Order Data', 
                'data' => $data_item_array,
            ];
            
           return response()->json($response,200);
             
 
         }else{ // Add

            return $this->sendError('No Order');
         }         
        
     }

     public function orderDetails(Request $request)
     {
         $user = Auth::guard('api')->user();  

         $validator = Validator::make($request->all(), [            
            'id' => 'required',   
        ]); 
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }  

         $check_data = Order::select('id')->where('id', $request->id)->count();  
         if($check_data > 0){  
            
            $itemval = Order::select('businesses.name','orders.id','orders.item_name','shipping_id','item_id','item_description','orders.item_price','orders.quantity','shipping_charges','order_status','orders.reject_reason','orders.created_at','order_shipped_date','order_delivered_date','order_cancel_date','order_confirmed_date','orders.address','apt_no','city','zip_code','country')     
            ->leftJoin('business_items', function($join) {
                $join->on('business_items.id', '=', 'orders.item_id');
            },
            )    
            ->leftJoin('businesses', function($join) {
                $join->on('businesses.id', '=', 'orders.business_id');
            },
            )      
            ->where('orders.id',$request->id)->first();

             $bussiness_item_url = url('uploads/item_image/');
            

             $address_data = ShippingAddress::select('shipping_address.id','address','apt_no', 'zip_code', 'country.name as country_name','city.name as city_name')
             ->leftJoin('country', function($join) {
                $join->on('country.id', '=', 'shipping_address.country');
            })
            ->leftJoin('city', function($join) {
                $join->on('city.id', '=', 'shipping_address.city');
            })
            ->where('shipping_address.id',$itemval->shipping_id)->first();

            $shppingAddress = array(
                "id" => '',
                "address" =>  $itemval->address,
                "apt_no" =>  (isset($itemval->apt_no) && !empty($itemval->apt_no) ? $itemval->apt_no : ''),
                "zip_code" => $itemval->zip_code,
                "country_name" => $itemval->country,
                "city_name" =>  $itemval->city
            );
     
                
            $items_qeury_img = "SELECT CONCAT('".$bussiness_item_url."/',image) as image  FROM business_item_images where item_id ='".$itemval->item_id."'";
            $items_img = DB::select(DB::raw( $items_qeury_img));

               $data_item_array = array(
                   "id" => $itemval->id,
                   "item_name" => $itemval->item_name,
                   "businesses_name" => $itemval->name,
                   "quantity" => $itemval->quantity,
                   "item_price" => ($itemval->item_price*$itemval->quantity),
                   "item_description" => $itemval->item_description,
                   "order_status" => $itemval->order_status,
                   "reject_reason" => $itemval->reject_reason, 
                   "created_at" => date("Y-m-d", strtotime($itemval->created_at)), 
                   "order_confirmed_date" => (isset($itemval->order_confirmed_date) && !empty($itemval->order_confirmed_date) ? date("Y-m-d", strtotime($itemval->order_confirmed_date))  : null), 
                   "order_shipped_date" =>(isset($itemval->order_shipped_date) && !empty($itemval->order_shipped_date) ? date("Y-m-d", strtotime($itemval->order_shipped_date))  : null),  
                   "order_delivered_date" => (isset($itemval->order_delivered_date) && !empty($itemval->order_delivered_date) ? date("Y-m-d", strtotime($itemval->order_delivered_date))  : null), 
                   "order_cancel_date" => (isset($itemval->order_cancel_date) && !empty($itemval->order_cancel_date) ? date("Y-m-d", strtotime($itemval->order_cancel_date))  : null), 
                   "address" => $itemval->address, 
                   "shipping_address" => $shppingAddress,                  
                   "item_img" =>  $items_img,  
                  );

                  

            $response = [
                'status_code' => 200,
                'status' => true,
                'message' => 'Order Item Data', 
                'data' => $data_item_array,
            ];
            
           return response()->json($response,200);
             
 
         }else{ // Add

            return $this->sendError('No Order');
         }         
        
     }

     public function cancelOrder(Request $request)
     {
         $user = Auth::guard('api')->user();  
         $validator = Validator::make($request->all(), [            
            'id' => 'required',   
        ]); 
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }  

         $check_data = Order::select('id')->where('id', $request->id)->count();  
         if($check_data > 0){  
            
            $order = Order::where('id', request('id'))->first(); 
            $order->order_status = '4';
            $order->order_cancel_date = date('Y-m-d');
            $order->update();

           $status ='Cancelled';
           $message='Cancelled: Oops!! '.$user->name.'! Your order has been cancelled. Shop again!!';
            
            send_notification_FCM($title='Order '.$status, $message, $token=$user->fcm_token, $redirection = $request->id);

            $notify_data = new Notification();
            $notify_data->user_id = $user->id;
            $notify_data->title = 'Order Cancelled';
            $notify_data->message = $message;
            $notify_data->notification_type = '0';
            $notify_data->notification_status = 'order';
            $notify_data->is_read = '0';
            $notify_data->redirect_id = $request->id;
            $notify_data->save();

            $response = [
                'status_code' => 200,
                'status' => true,
                'message' => 'Order has been cancelled successfully.',  
            ];
            
           return response()->json($response,200);
             
 
         }else{ // Add

            return $this->sendError('No Order');
         }         
        
     }


     
    public function generatePaymentToken(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'false', 'message' => $validator->errors()], 200);
        }else{
            $user = Auth::guard('api')->user();

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            //$stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));  // pass stripe secret key 

            // create customer 
            /* $customer = \Stripe\Customer::create([
                'description' => $user->first_name,
                'phone' => $user->phone,
                'email'=>$user->email
            ]); */

            $ephemeralKey = \Stripe\EphemeralKey::create(
                [
                'customer' => $user->stripe_customer_id,   // $customer->id
                ],
                [
                'stripe_version' => '2020-08-27',
                ]);

            $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $request->amount * 100,
            'currency' => 'USD',
            'customer' => $user->stripe_customer_id,
            'receipt_email'=>$user->email,
            'automatic_payment_methods' => [
                        'enabled' => 'true',
                ]
            ]);

            $senddata = array(
                'paymentIntent' => $paymentIntent->client_secret,
                'ephemeralKey' => $ephemeralKey->secret,
                'customer' => $user->stripe_customer_id,
                'publishableKey' => env('STRIPE_KEY'),
            );
            
            return response()->json([ 'status_code' => 200,'status' => true, 'message' => 'success.','data'=>$senddata], 200);
        }
       

    }


    public function createCardToken(Request $request){
        $user = Auth::guard('api')->user();
        
        $validator = Validator::make($request->all(), [
            'number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'cvc' => 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'false', 'message' => $validator->errors()], 200);
        }else{
            $token_result = PaymentController::createCardToken($request->number,$request->exp_month,$request->exp_year,$request->cvc,$request->name);
            
           if(isset($token_result->original['status_code']) && $token_result->original['status_code'] == 400){
            return response()->json([ 'status_code' => 400,'status' => false, 'message' => $token_result->original['data']], 200);
            exit();
           }

            if($token_result){ 

                if(isset($user->stripe_customer_id) && !empty($user->stripe_customer_id)){
                   
                    $stripe_customer_id = $user->stripe_customer_id;
                }else{
                    // create customer strip Id
                    $strip_customer_result = PaymentController::createCustomerOnStripe($user->email);
                

                    $stripe_customer_id = (isset($strip_customer_result->id) ? $strip_customer_result->id : '');
                    User::where('id', $user->id)->update(['stripe_customer_id'=>$stripe_customer_id]); 
                }
                

                

                $card_result = PaymentController::saveCard($stripe_customer_id,$token_result->id);

               
                
                return response()->json([ 'status_code' => 200,'status' => true, 'message' => 'Card saved successfully.','data'=>$card_result], 200);
            }else{
                return response()->json([ 'status_code' => 400,'status' => false, 'message' => 'Something went wrong.'], 200);
            }
        }
    }

    public function saveCard(Request $request){
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'card_token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([ 'status_code' => 400,'status' => false, 'message' => $validator->errors()], 200);
        }else{
            $card_result = PaymentController::saveCard($user->stripe_customer_id,$request->card_token);
            if($card_result){
                return response()->json([ 'status_code' => 200,'status' => true, 'message' => 'Card saved successfully.','data'=>$card_result], 200);
            }else{
                return response()->json([ 'status_code' => 400,'status' => false, 'message' => 'Something went wrong.'], 200);
            }
        }
    }

    public function getAllSavedCard(Request $request){
        $user = Auth::guard('api')->user();
        
        $all_card_result = PaymentController::getAllSavedCard($user->stripe_customer_id);
        if($all_card_result){
            return response()->json([ 'status_code' => 200,'status' => true, 'message' => 'Card listing.','data'=>$all_card_result], 200);
        }else{
            return response()->json([ 'status_code' => 400,'status' => false, 'message' => 'Card listing.','data'=>array()], 200);
        }
        
    }

    public function deleteCard(Request $request){
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'card_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'false', 'message' => $validator->errors()], 200);
        }else{
            $card_result = PaymentController::deleteCard($user->stripe_customer_id,$request->card_id);
            if($card_result){
                return response()->json([ 'status_code' => 200,'status' => true, 'message' => 'Card deleted successfully.','data'=>$card_result], 200);
            }else{
                return response()->json([ 'status_code' => 400,'status' => false, 'message' => 'Something went wrong.'], 200);
            }
        }
    }

    public function updateCard(Request $request){
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'card_id' => 'required',
            //'number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            //'cvc' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([ 'status_code' => 400,'status' => false, 'message' => $validator->errors()], 200);
        }else{
            $card_result = PaymentController::updateCard($user->stripe_customer_id,$request->card_id,$request->exp_month,$request->exp_year);
            if($card_result){
                return response()->json([ 'status_code' => 200,'status' => true, 'message' => 'Card updated successfully.','data'=>$card_result], 200);
            }else{
                return response()->json([ 'status_code' => 400,'status' => false, 'message' => 'Something went wrong.'], 200);
            }
        }
    }

     

}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
