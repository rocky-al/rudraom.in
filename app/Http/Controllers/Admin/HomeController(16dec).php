<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
//use App\Models\BankDetail;
//use App\Models\City;
use App\Models\Document;
use App\Models\Earning;
use App\Models\FamilyMember;
use App\Models\Locality;
//use App\Models\MainLocality;
use App\Models\Offer;
use App\Models\PaymentRequest;
use App\Models\Setting;
//use App\Models\State;
use App\Models\User;
use App\Models\Business;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Arr;

class HomeController extends Controller
{
    public function index()
    {


       
            $tm =  User::whereHas('roles', function ($tm) {
                $tm->where('name', 'team leader');
            });
   
        $tm_count = $tm->count();


      
            $employee = User::whereHas('roles', function ($employee) {
                $employee->where('name', 'employee');
            });
        
        $employee_count = $employee->count();


        $business = Business::where('id','>', '0')->where('is_delete', '0');
        $business_active = Business::where('status', '1')->where('is_delete', '0');
        $business_inactive = Business::where('status', '2')->where('is_delete', '0');
        $business_pending = Business::where('status', '0')->where('is_delete', '0');
        $business_count = $business->count();
        $business_count_active = $business_active->count();
        $business_count_inactive = $business_inactive->count();
        $business_count_pending = $business_pending->count();

        $category = Category::where('id','>', '0')->where('is_delete', '0');
        $category_count = $category->count();



        $user_data_main =  User::whereHas('roles', function ($query) {
                $query->where('name', 'user')->where('is_delete', '0');
            });
        $user_data_main_active =  User::whereHas('roles', function ($query) {
                $query->where('name', 'user')->where('status', '1')->where('is_delete', '0');
            });
        $user_data_main_inactive =  User::whereHas('roles', function ($query) {
                $query->where('name', 'user')->where('status', '0')->where('is_delete', '0');
            });
       
        $user_count = $user_data_main->count();
            $earning = 0;

        $user_count_active = $user_data_main_active->count();
        $user_count_inactive = $user_data_main_active->count();
        $user_data  = $user_data_main->select('id', 'created_at')->get();
        $user = $user_data->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });

        $usermcount = [];
        $userArr = [];
        foreach ($user as $key => $value) {
            $usermcount[(int)$key] = count($value);
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($usermcount[$i])) {
                $userArr[$i] = $usermcount[$i];
            } else {
                $userArr[$i] = 0;
            }
        }
        $month_array = json_encode(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
        $array_values = array_values($userArr);
        $monthly_users = json_encode($array_values);

       

        $subscription_data  = $business->select('id', 'created_at')->get();

        $subscription_count = $business->count();
        //echo $subscription_count; dd();
        $subscription = $subscription_data->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });



        $subscriptioncount = [];
        $subscriptionArr = [];

        foreach ($subscription as $key => $value) {
            $subscriptioncount[(int)$key] = count($value);
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($subscriptioncount[$i])) {
                $subscriptionArr[$i] = $subscriptioncount[$i];
            } else {
                $subscriptionArr[$i] = 0;
            }
        }

        $subscription_monthly = array_values($subscriptionArr);
        $subscription_monthly = json_encode($subscription_monthly);

        $result = ['total_category'=>$category_count,'business_pending' =>$business_count_pending,'business_rejected' =>$business_count_inactive,'business_approved' =>$business_count_active,'user_count_inactive' => $user_count_inactive,'user_count_active' => $user_count_active,'tm_count' => $tm_count, 'user_count' => $user_count,  'subscription_monthly' => $subscription_monthly, 'employee_count' => $employee_count,  'month_array' => $month_array, 'monthly_users' => $monthly_users, 'business_count' => $business_count,'earning' => $earning];
        return view('admin.index', $result);
    }



    public function theme_style(Request $request)
    {
        $data =    Session::put('theme_mode', $request->theme_mode);
        return response()->json(['status' => true, 'message' => 'Theme mode has been changed']);
    }


    public function changePassword()
    {
        $data = Auth::user();
        $result = ['title' => 'Change Password', 'data' => $data];
        return view('admin.setting.changePassword', $result);
    }




    public function setting()
    {
        $all_data = Setting::select("slug","value")->get();
        $array = array();
        foreach ($all_data as $value) {
            // code...
            //echo $value['value'];
            $array = Arr::add($array, $value['slug'], $value['value']);
        }
        
        $result = ['title' => 'Settings', 'array'=>$array];
        return view('admin.setting.setting', $result);
    }




    public function updateSetting(Request $request)
    {

        $request->validate([ 
            'logo' => 'mimes:jpeg,png,jpg',
           
        ]);

        //echo $request->logo; die();
        // $request->validate([
        //     'employee_commission' => 'required|integer',
        //     'tm_commission' => 'required|integer',
        //     'business_commission' => 'required|integer'
        // ]);

        // Setting::where('slug', 'employee_commission')->update(['value' => $request->employee_commission]);
        // Setting::where('slug', 'tm_commission')->update(['value' => $request->tm_commission]);
        // Setting::where('slug', 'business_commission')->update(['value' => $request->business_commission]);
        $input = $_POST;
        //print_r($input);
        foreach ($input as $key => $value) {
            // code...
            if($key!='_token' && $key!='logo'){
                Setting::where('slug', $key)->update(['value' => $value]);
            }
        }
        if (isset($request->logo)) {
            $path = 'uploads/logo/';
            if(!empty(getSettingValue('logo'))){
                deleteImage($path . getSettingValue('logo'));
            }
            $image_path =  uploadImage($request->logo, $path);
            //echo $image_path; die();
            Setting::where('slug', 'logo')->update(['value' => $image_path]);
            $json = ['status' => true, 'src' => url($path).'/'.$image_path, 'message' => 'Settings updated successfully'];
        }else{
            $json = ['status' => true, 'message' => 'Settings updated successfully'];
        }
        return response()->json($json);
    }




    public function updatePassword(Request $request)
    {
        $id = $request->get('id');
        $request->validate([
            'confirm_password' => 'required|min:6|max:15',
            'password' => 'required|min:6|max:15',
            'current_password' => 'required|min:6|max:15'
        ]);

        $data = User::where('id', $request->id)->first();
        if (isset($data)) {

            if (Hash::check($request->current_password, Auth::user()->password)) {

                $data->password = Hash::make($request->get('password'));
                if ($data->save()) {
                    return response()->json(['status' => true, 'message' => ' Password has been updated successfully']);
                } else {
                    return response()->json(['status' => false,  'message' => 'Something went wrong']);
                }
            } else {
                return response()->json(['status' => false,  'message' => 'Invalid old password.']);
            }
        } else {
            return response()->json(['status' => false,  'message' => 'User not found.']);
        }
    }






    public function profile()
    {
        $data = Auth::user();
        $result = ['title' => 'Update Profile Details', 'data' => $data];
        return view('admin.setting.profile', $result);
    }


    public function updateProfile(Request $request)
    {
        $id = $request->get('id');
        $request->validate([
            'first_name' => 'required|max:30',
            'image' => 'mimes:jpeg,png,jpg',
            'email' => 'required||unique:users,email,' . $id,
            'mobile' => 'required|unique:users,mobile,' . $id,
        ]);

        $data = User::where('id', $request->id)->first();
        if (isset($data)) {
            $data->name = $request->get('first_name');
            $data->email = $request->get('email');
            $data->mobile = $request->get('mobile');
            if (isset($request->image)) {
                $path = 'uploads/admin_profile/';
                if(!empty($data->image)){
                    deleteImage($path . $data->image);
                }
                $image_path =  uploadImage($request->image, $path);
                $data->image = $image_path;
                $json = ['status' => true, 'src' => url($path) .'/'. $data->image, 'message' => ' Profile has been updated successfully'];
            }else{
                $json = ['status' => true, 'message' => ' Profile has been updated successfully'];
            }

            if ($data->save()) {
                return response()->json($json);
            } else {
                return response()->json(['status' => false,  'message' => 'Something went wrong']);
            }
        } else {
            return response()->json(['status' => false,  'message' => 'User not found.']);
        }
    }
}
