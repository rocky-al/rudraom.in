<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
//use Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{



    public function login(Request $request)
    {
        $val=Auth::check();
        if($val==1)
          {
            return redirect()->route('admin.index');
           
          }
        $data = ['title' => 'Admin Login'];
        return view('admin.auth.login', $data);
    }

    public function do_login(AuthRequest $request)
    {

        try {
            if (Auth::attempt($request->only('email', 'password')) && roleName() =='admin') {
                 // /print_r(roleName()); die();
                if (Auth::user()->status == Constant::IN_ACTIVE) {
                    Auth::logout();
                    return response()->json(['status' => false,  'message' => 'Your account is temporarily blocked please contact administrator.']);
                }
                return response()->json(['status' => true,  'message' => 'Login successfully', 'redirect_url' => route('admin.index')]);
            } else {
                return response()->json(['status' => false,  'message' => 'These credentials do not match our records.']);
            }
        } catch (Exception $e) {
           
            dd($e);
            return response()->json(['status' => false,  'message' => $e->message]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['status' => true,  'message' => 'Admin logout successfully', 'redirect_url' => Route('admin.login')]);
    }

    public function forgetPassword()
    {
        $title = "Forget Password";
        return view('password.forget', compact('title'));
    }

    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:50',
        ]);

        $user = User::where('email', $request->email)->first();
        if (isset($user)) {
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => Str::random(40),
                'created_at' => Carbon::now(),
            ]);

            $tokenData = DB::table('password_resets')->where('email', $request->email)->first();
            $content = getEmailContentValue(1);
            $emailval = $content->description;
            $subject = $content->title;
            //echo getSettingValue('logo'); die();
            if(empty(getSettingValue('logo'))){
                $logo = url('images/logo.png');
            }else{
                $logo = url('uploads/logo').'/'.getSettingValue('logo');
            }
            $replace_data = [
                    '@link_value' => Constant::APP_URL.'reset-password/'.$tokenData->token,
                    '@logo' => $logo,
                ];

                foreach ($replace_data as $key => $value) {
                    $emailval = str_replace($key, $value, $emailval);
                }
            if (sendMail($request->email, $emailval, $subject)) {
                return response()->json(['status' => true, 'redirect_url' => Route('admin.login'), 'message' => 'A reset link has been sent to your email address.']);
            } else {
                return response()->json(['status' => false, 'redirect_url' => '',   'message' => 'Something went wrong. Please try again.']);
            }
        } else {
            return response()->json(['status' => false,  'message' => 'These email does not match our records.']);
        }
    }

    
    public function resetPassword($token)
    {
        $result = ['title' => 'Reset Password', 'token' => $token];
        return view('password.reset', $result);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|max:15',
            'confirm_password' => 'required|min:6|same:password',
        ]);
        $password = $request->password;
        $token = $request->token;
        $tokenData = DB::table('password_resets')->where('token', $token)->first();

        if (!isset($tokenData)) {
            return response()->json(['status' => false, 'redirect_url' => '',   'message' => 'Token Expire.']);
        }
        $user = User::where('email', $tokenData->email)->first();

        if (!isset($user)) {
            return response()->json(['status' => false, 'redirect_url' => '',   'message' => 'User not found.']);
        } else {
            $user->password = Hash::make($password);
            $user->save();
            DB::table('password_resets')->where('token', $tokenData->token)->delete();
            return response()->json(['status' => true, 'redirect_url' => Route('admin.login'),   'message' => 'Your password has been successfully reset please login.']);
        }
    }
}
