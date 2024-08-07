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
            'password' => 'required|min:8|max:16|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'confirm_password' => 'required|min:8|max:16|same:password|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'fcm_token' => 'required',
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
                    $msg = "OTP send successfully";
                } else {
                    $otp = "";
                    $request['password'] = Hash::make($request['password']);
                    $user = User::create($request->toArray());
                    // Generate Token
                    config(['auth.guards.api.provider' => 'api']);
                    $token = $user->createToken('MyApp', ['api'])->accessToken;
              

                    $user->auth_token = $token;
                    $user->name = request('full_name');
                    $user->email = request('email');
                    $user->mobile = request('mobile');
                    $user->status = '1';
                    $user->verification_status = '1';
                    $user->fcm_token = request('fcm_token');
                    $user->device_id = request('device_id');
                    $user->device_type = request('device_type');                    
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
            'password' => 'required|min:8|max:16',           
            'fcm_token' => 'required',
            'device_id' => 'required',
            'device_type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        } else {
            $admin = User::select('email')->where('id', '1')->whereNull('deleted_at')->first();
            $user = User::whereRaw('( email = "'.request('email').'")')->where('deleted_at', null)->first();  
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
                        $token = $user->createToken('MyApp', ['api'])->accessToken; // create and update token
                        $user->auth_token = $token;
                        $user->fcm_token = request('fcm_token');
                        $user->device_id = request('device_id');
                        $user->device_type = request('device_type'); 

                        if(isset($user->image) && !empty($user->image)){
                            $user->image = url('uploads/user_profile').'/'.$user->image;
                            }
                            
                        if ($user->save()) {
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
            $user = User::where('email', $email)->where('is_delete','!=', '1')->whereNull('deleted_at')->first();
            // get user details
            if (isset($user) && $user->status == '1') { // send otp if user is active
                $otp = rand(100000, 999999);
                $user->otp = $otp;
                $user->update();
                // send mail for OTP 
                
                    if ($this->otp_email($user->email, $otp, $user->first_name)) {  // if otp sended to user email for account verification
                        return $this->sendResponse($user, 'OTP send successfully.');
                    } else {
                        return $this->sendError("Email not sended to user!!");
                    }
            } else if (isset($user) && $user->status == '0') {  // if user is inactive
                return $this->sendError('Your account has been temporary blocked.');
            } else {    // if user is not found
                return $this->sendError('User Not found.');
            }
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
                    return $this->sendResponse($user, 'OTP send successfully.');
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
        
        $user = Auth::guard('api')->user();   
        $validator = Validator::make($request->all(), [          
            'password' => 'required|min:8|max:16|same:confirm_password|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'confirm_password' => 'required|min:8|max:16|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        if(isset($user->otp) && $user->otp == $request->otp){
  
            User::find(Auth::guard('api')->user()->id)
                ->update([
                    'password' => Hash::make($request->password)
                ]);
                return $this->sendResponse([], 'Password has been changed successfully.');
            }else{
                return $this->sendError("Invalid OTP Code.");
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

            User::find(Auth::guard('api')->user()->id)
                ->update([
                    'password' => Hash::make($request->password)
                ]);
                return $this->sendResponse([], 'Password has been changed successfully.');
        } else {
            return $this->sendError('Old password incorrect.');
        }
    }

      // function for get blog data by slug
      public function businessList(Request $request)
      {
          $validator = Validator::make($request->all(), [
              'latitude' => 'required',
              'longitude' => 'required',
          ]);
  
          if ($validator->fails()) {
              return $this->sendError($validator->errors());
          }
          // get blogs data by slug and category id
        $data = BusinessList::select('name','profile_img','address')->get();
        $setDistance = 6371; //if get distance result in km then set 6371 and if get distance result in mile then set 3959
        $filter_condition ='';
        if(isset($request->category_id) && !empty($request->category_id)){
            $filter_condition = " and category_id = '".$request->category_id."'";
        }
        if(isset($request->name) && !empty($request->name)){
            $filter_condition = " and name = '".$request->name."'";
        }
        if(isset($request->license_no) && !empty($request->license_no)){
            $filter_condition = " and license_no = '".$request->license_no."'";
        }

        $query = "SELECT * FROM (SELECT id,name,address,profile_img,ROUND(( $setDistance * acos( cos( radians ($request->latitude) ) * cos( radians(latitude) ) * cos( radians(longitude ) - radians($request->longitude ) ) + sin( radians($request->latitude) ) * sin( radians(latitude ) ) ) ),2) AS distance FROM businesses   WHERE status='1' AND  is_delete='0' $filter_condition  ORDER BY distance ASC) AS t";
        $data = DB::select(DB::raw( $query));
        $bussness_array =  array(); 
          if ($data) {
            foreach($data as $val){

                $bussness_array[] = array(
                    'id' => $val->id,
                    'name' => $val->name,
                    'address' => $val->address,
                    'grade' => 0,
                    'is_wishlist' => 0,
                    'profile_img' => url('uploads/business_profile/').'/'.$val->profile_img,
                );
            }
              $response = [
                  'status' => true,
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

           $BusinessDetails->grade = 0;
           $BusinessDetails->is_wishlist =0;
           $BusinessDetails->profile_img = url('uploads/business_profile/').'/'.$BusinessDetails->profile_img;
  
          $bussiness_photo_url = url('uploads/business_image/');
          $bussness_photos_qeury = "SELECT CONCAT('".$bussiness_photo_url."/',image) as bussiness_photo FROM business_images where business_id ='".$request->business_id."'";
          $bussiness_photo_data = DB::select(DB::raw( $bussness_photos_qeury));
       
        
          $items_qeury = "SELECT id,item_name,item_price,item_description FROM business_items where business_id ='".$request->business_id."'";
          $items_data = DB::select(DB::raw( $items_qeury));
          $item_array = array();

          $business_item_img = url('uploads/business_item_img/');
          foreach( $items_data as $itemval){
            
            $items_qeury = "SELECT image FROM business_item_images where item_id ='".$itemval->id."'";
            $items_data = DB::select(DB::raw( $items_qeury));

            $item_array[] = array(
                'id' => $itemval->id,
                'item_name' => $itemval->item_name,
                'item_price' => $itemval->item_price,
                'item_description' => $itemval->item_description,
                'item_img' => $business_item_img.'/'.$items_data[0]->image,
            );
          }

          $reivew_qeury = "SELECT id,rating,review,created_at FROM business_review where business_id ='".$request->business_id."'";
          $review_data = DB::select(DB::raw( $reivew_qeury));

          $review_array = array();
          $business_item_img = url('uploads/business_item_img/');
          foreach( $review_data as $reviewVal){
            
         

            $review_array[] = array(
                'id' => $reviewVal->id,
                'user_name' => 'Tester',
                'rating' => $reviewVal->rating,
                'review' => $reviewVal->review,
                'created_at' => $reviewVal->created_at,
                'profile_img' =>'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460__340.png',
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

    // function for get FAQs data category wise
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

    // function for get blogs data category wise
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

    // function for get blog data by slug
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

    // function for update user profile
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'mobile' => 'required|numeric',
            'country_code' => 'required|string',
            'image' => 'required|mimes:jpeg,png,jpg',
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
           

            return $this->sendResponse($user, 'Profile update successfully.');
        }else{
            return $this->sendError('Something went wrong.');
        }
    }

    // function for update user profile image
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
        $data = [];
        // get category list
        $category = Category::select('id','name')->where('status','1')->where('is_delete','0')->get();
     
        if ($category) {
            return $this->sendResponse($category, 'Data get successfully.');
        }else{
            return $this->sendError("No data found.");
        }
    }

    // function for send user feedback to admin
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

    // function for user inquire for agent
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

    // function for update notification setting
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
            return $this->sendResponse([], 'Notification setting update successfully.');
        }else{
            return $this->sendError('Something went wrong.');
        }
    }

}
