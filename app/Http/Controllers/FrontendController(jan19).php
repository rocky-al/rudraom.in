<?php

namespace App\Http\Controllers;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Jobs\OTP as JobsOTP;
use App\Models\Otp;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessImages;
use Illuminate\Http\Request;
use App\Http\Requests\UserAuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
//use Spatie\Permission\Models\Permission;

class FrontendController extends Controller
{
    
    protected $page = 'Business';

  
     public function index(Request $request)
    {
         if ($request->session()->has('user')) {
           return redirect()->route('frontend.dashboard');
           }

        $data = array();
        // get all country code
        $data['category'] = DB::select("SELECT name,id from categories where is_delete=0 and status=1 ORDER BY name ASC ");
       
        return view('frontend/index')->with($data);
    }

    public function dashboard(Request $request)
    {
        $value = $request->session()->get('user');

        if(!isset($value->id)){
            return redirect('');
        }
        $user = Business::where('id', $value->id)->first();
        if ($request->session()->missing('user') || $user->is_active=='0' || $user->is_delete=='1') {
           $request->session()->flush();
           return redirect('');
           }   
        $data['busns_detail']=Business::where('id', $value->id)->first();
        $data['businessImages'] = BusinessImages::where('business_id', $value->id)->get();
        $data['category'] = DB::select("SELECT categories.name,categories.id from categories join businesses on categories.id= businesses.category_id  where categories.is_delete=0 and businesses.id=$value->id");
        return view('frontend/dashboard')->with($data);
    }

     public function login(Request $request)
    {

       // print_r($request->all());dd();


       if (Auth::guard('user')->attempt(['email_address' => $request->get('email'),'password' => $request->password])) {
            $user = Business::where('email_address', $request->email)->first();
              if ($user->is_delete == '1') {
                Auth::guard('user')->logout();
                return response()->json(['status' => false, 'message' => 'These credentials do not match our records']);
            }

            if ($user->status == '0') {
                Auth::guard('user')->logout();
                return response()->json(['status' => false, 'message' => 'Your account verification is pending Please contact to admin']);
            }
            if ($user->status == '1' && $user->is_active == '1' ) {
               $request->session()->put('user', $user);
                return response()->json(['status' => true, 'message' => 'Login successfully', 'redirect_url' => route('frontend.dashboard')]);
            } 

             if ($user->status == '1' && $user->is_active == '0' ) {
               Auth::guard('user')->logout();
                return response()->json(['status' => false, 'message' => 'Your account  is inactive Please contact to admin']);
               
            } 
            if ($user->status == '2' ) {
                Auth::guard('user')->logout();
                return response()->json(['status' => false, 'message' => 'Your account has been rejected Please contact to admin']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'These credentials do not match our records']);
        }
    }

       public function change_pswd(Request $request,$id)
    {
        //echo $id;die;
         $user= Business::where('id', $id)->first();
          if (Hash::check($request->old_pass, $user->password)) {

             if (Hash::check($request->new_pass, $user->password)) {
                             return response()->json(['status' => false, 'message' => 'Old password & new password are same please choose another password']);
                    }
                    $user->password = Hash::make($request->new_pass);
                    $user->save();
                     return response()->json(['status' => true, 'message' => ' Password changed successfully']);
                    return json_encode($res);
                } else {
                   return response()->json(['status' => false, 'message' => 'Old password not matched']);
                }
     
    }

     public function logout(Request $request)
    {
       $request->session()->flush();
       
        return response()->json(['status' => true,  'message' => 'User logout successfully', 'redirect_url' => Route('frontend.index')]);
    }

     public function register(Request $request)
    {
       $validator = Validator::make($request->all(), [
                'photos.*' => 'max:1024',
                'profife_pic'=>'max:1024',
               ]);
        if ($validator->fails()) { 
                  return response()->json(['status' => false, 'message' =>'Maximum 1 MB size of  Photos are allowed'], 200);
              }

        $id = $request->get('id');

        if(isset($id) && !empty($id)){

             if(Business::where('email_address', '=', $request->get('email')) ->where('id', '!=', $request->id)->count() > 0){
                return response()->json(['status' => false,  'message' => 'User is already exist with given email id']);

            }

            $data = Business::where('id', $request->id)->first();
        }else{
            if(Business::where('email_address', '=', $request->get('email'))->where('is_delete',  0)->count() > 0){
                return response()->json(['status' => false,  'message' => 'User is already exist with given email id']);

            }
             if(Business::where('license_no', '=', $request->get('lic_no'))->count() > 0){
                return response()->json(['status' => false,  'message' => 'License no is already exist ']);

            }
            $data = new Business();
           }

           $data->category_id = $request->category;
           $data->name = $request->name_bsns;
           $data->email_address = $request->email;
           $data->phone_no = $request->phone_no;
           $data->address = $request->address;
           $data->location = $request->address2;
           $data->license_no = $request->lic_no;
           $data->registration_date = $request->regs_date;
           $data->latitude = $request->latitude;
           $data->longitude = $request->longitude;
           $data->description = $request->description;
           $data->created_at=date("Y-m-d H:i:s");

            if (isset($request->profife_pic)) {
             // $path = 'uploads/business_profile/';
              $width=400;   // large image width
              $height=400;  // large image height
              $width1=200;  // medium image width
              $height1=200; // medium image height
              $width2=120;   // small image width
              $height2=120;  // small image height
              $path="uploads/business_profile/";     // large image folder 
              $path1="uploads/business_profile_medium/"; // medium image folder 
              $path2="uploads/business_profile_small/";  // small image folder
            //imageResize($request->profife_pic, 'uploads/business_profile_small/',76,76);
            //imageResize($request->profife_pic, 'uploads/business_profile_medium/',375,207);
            //$image_path =  uploadImage($request->profife_pic, $path);
              $image_path =imageResize($request->profife_pic,$width,$height,$width1,$height1,$width2,$height2,$path,$path1,$path2);
            $data->profile_img = $image_path;
            $json = ['status' => true, 'src' => url($path).'/'.$image_path, 'message' => 'Settings updated successfully'];
        }
         $result=$data->save();

        $businessId = DB::getPdo()->lastInsertId();

        if($request->hasFile('photos'))
              {
                $files = $request->file('photos');
                //print_r($files);
                // $path1 = 'uploads/business_image/';

                  $width=400;   // large image width
                  $height=400;  // large image height
                  $width1=512;  // medium image width
                  $height1=512; // medium image height
                  $width2=120;   // small image width
                  $height2=120;  // small image height
                  $path="uploads/business_image/";     // large image folder 
                  $path1="uploads/business_image_medium/"; // medium image folder 
                  $path2="uploads/business_image_small/";  // small image folder
           
                foreach($files as $file){
                //imageResize($file, 'uploads/business_image_small/',76,76);
                //imageResize($file, 'uploads/business_image_medium/',375,207);
                //$image_path =  uploadImage($file, $path1);
                $image_path =imageResize($file,$width,$height,$width1,$height1,$width2,$height2,$path,$path1,$path2);
                DB::table('business_images')->insert(
                         array(
                                'business_id'=>$businessId, 
                                'image'   => $image_path
                         )
                    );
                }
            }

        

       if ($result) {
             $content = getEmailContentValue(6);
             $emailval = $content->description;
             $subject = $content->title;
             if(empty(getSettingValue('logo'))){
                $logo = url('images/logo.png');
            }
            else{
                $logo = url('uploads/logo').'/'.getSettingValue('logo');
            }
             $replace_data = [
                            '@logo' => $logo,
                            '@name'=>$data->name,
                            '@email'=>$data->email_address,
                        ];
             foreach ($replace_data as $key => $value) {
                            $emailval = str_replace($key, $value, $emailval);
                        }
            sendMail($data->email_address, $emailval, $subject);
            
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been added successfully','redirect_url' => route('frontend.index')]);
            
        } else {
            return response()->json(['status' => false,  'message' => 'Something went wrong']);
        }

      
    }

  

}
