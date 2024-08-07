<?php

namespace App\Http\Controllers\Web;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Jobs\OTP as JobsOTP;
use App\Models\City;
use App\Models\Earning;
use App\Models\FamilyMember;
use App\Models\MainLocality;
use App\Models\Otp;
use App\Models\State;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|unique:users,email',
            'name' => "required"
        ]);

        Otp::where('email', $request->email)->update(['status' => 0]);

        $otp = new Otp;
        $otp->name = $request->name;
        $otp->email = $request->email;
        $otp->referral_id = $request->referral_id;
        $otp_generate = generateNumericOTP(4);
        $otp->otp = $otp_generate;
        $otp->status = 1;
        if ($otp->save()) {

            $data_array = [
                'email' => $request->email,
                'otp' => $otp_generate,
                'content' => 'Indeal: Your Verification Code is '
            ];
            $this->dispatch(new JobsOTP($data_array));

            return response()->json(['message' => 'OTP has been sent succesfully', 'status' => true]);
        } else {
            return response()->json(['message' => 'something went wrong please try again', 'status' => false]);
        }
    }


    public function verifyOtp(Request $request)
    {
        $validator = $request->validate([
            'email' => "required|email",
            'otp' => "required|between:4,4'",
        ]);
        $data = Otp::where('email', $request->email)->where('otp', $request->otp)->where('status', 1)->first();

        if (isset($data)) {
            $data->status = 0;
            $data->save();

            $user = new User();
            $user->first_name = $data->name;
            $user->email = $data->email;

            $user->referral_id = Str::random(Constant::REFERRAL_ID_LENGTH);
            $user->perent_referral_id = $data->referral_id;

            if ($user->save()) {
                return response()->json([
                    'redirect_url' => route('web.profile.update', base64_encode($user->id)),
                    'status' => true,
                    'message' => 'OTP has been verified',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong',
                ]);
            }
        } else {
            return response()->json([
                ' status' => false,
                'message' => 'OTP does not match',
            ]);
        }
    }



    function refer_user_count($referral_id)
    {
        $data = User::where('perent_referral_id', $referral_id)->count();
        return $data;
    }



    public function profileUpdate($id)
    {
        $user = User::where('id', base64_decode($id))->first();
        $state = State::where(['status' => Constant::ACTIVE])->pluck('name', 'id');
        if (isset($user)) {

            if (isset(Auth::user()->referral_id)) {
                $referal_user = User::where('perent_referral_id', Auth::user()->referral_id)->paginate(3);
            } else {
                $referal_user = [];
            }

            $city = City::where(['status' => Constant::ACTIVE, 'state_id' => $user->state_id])->pluck('name', 'id');
            $main_locality = MainLocality::where(['status' => Constant::ACTIVE, 'city_id' => $user->city_id])->pluck('name', 'id');

            $data = ['title' => 'User Dashbord', 'referal_user' => $referal_user,  'user' => $user, 'state' => $state, 'city' => $city, 'main_locality' => $main_locality];
            return view('web.pages.dashbord', $data);
        } else {
            return redirect()->back()->with('error', 'Data not found. ');
        }
    }


    public function updateProfile(Request $request, $id)
    {
        $user = User::where('id', base64_decode($id))->first();
        if (isset($user)) {
            $user->first_name = $request->get('name');
            $user->mobile = $request->get('mobile');
            $user->email = $request->get('email');
            $user->state_id = $request->get('state_id');
            $user->city_id = $request->get('city_id');
            $user->main_locality_id = $request->get('main_locality_id');
            $user->address = $request->get('address');
            $user->zip_code = $request->get('zip_code');



            if (isset($request->image)) {
                $strArray = explode('/', $user->image);
                $lastElement = end($strArray);
                deleteImage('/uploads/profile/' . $lastElement);
                $image_path =  uploadImage($request->image, 'uploads/profile/');
                $user->image = $image_path;
            }

            if (isset($request->password)) {
                $password = Hash::make($request->password);
                $user->password = $password;
            }


            if ($user->save()) {
                if (isset($request->password)) {
                    $user->assignRole(['5']);
                    Auth::guard('web')->attempt(['email' => $request->get('email'), 'password' => $request->password]);
                }

                return redirect()->back()->with('success', 'Profile details has been updated');
            } else {
                return redirect()->back()->with('error', 'Something went wrong');
            }
        } else {
            return redirect()->back()->with('error', 'Data not found.');
        }
    }

    public function family_member_update(Request $request, $id)
    {
        if (!empty($request->get('name'))) {
            $name = $request->get('name');
            $mobile = $request->get('mobile');
            $age = $request->get('age');
            FamilyMember::where('user_id', base64_decode($id))->delete();

            foreach ($name as $key => $item) {
                if (isset($item)) {
                    $data = new FamilyMember();
                    $data->mobile = $mobile[$key];
                    $data->age = $age[$key];
                    $data->user_id =  base64_decode($id);
                    $data->name = $item;
                    $data->save();
                }
            }

            return redirect()->back()->with('success', 'Family member has been updated');
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }





    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->password])) {
            if (Auth::user()->status == Constant::ACTIVE) {
                return response()->json(['status' => true, 'message' => 'Login successfully', 'redirect_url' => route('web.profile.update', base64_encode(Auth::user()->id))]);
            } else {

                return response()->json(['status' => false, 'message' => 'Your account has been blocked Please contact admin']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'These credentials do not match our records']);
        }
    }



    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('web.index')->with('success', 'Logout successfully');
    }




    public function curlRequest($url, $post_data)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post_data,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        echo $response;
    }

    public function getEmailByUser($email)
    {
        $user = User::where('email', $email)->first();
        return $user;
    }

    public function getUser($id)
    {
        $user = User::where('id', $id)->first();
        return $user;
    }








    public function pay_now(Request $request)
    {

        $secretKey = Constant::SECRETE_KEY;
        $user_data = $this->getUser(Auth::user()->id);
       
        $array = [
            "appId" => Constant::APP_ID,
            "orderId" => date('Ymdhis'),
            "orderAmount" => 500,
            "data_id" => 111,
            "orderCurrency" => 'INR',
            "orderNote" => 'Testing note',
            "customerName" => $user_data->first_name ?? '',
            "customerPhone" => $user_data->mobile ?? '',
            "customerEmail" => $user_data->email ?? '',
            "returnUrl" => URL::to('/payment-response'),
            "notifyUrl" => URL::to('/payment-response'),
        ];


        ksort($array);
        $signatureData = "";
        foreach ($array as $key => $value) {
            $signatureData .= $key . $value;
        }
        $signature = hash_hmac('sha256', $signatureData, $secretKey, true);
        $signature = base64_encode($signature);
        $url = "https://test.cashfree.com/billpay/checkout/post/submit";
        $array['signature'] = $signature;
        $this->curlRequest($url, $array);
    }


    function roleCheck($referral_id)
    {
        $data = User::where('referral_id', $referral_id)->first();
        if (isset($data)) {
            $data =   $data->getRoleNames();
            return $data[0];
        } else {
            return null;
        }
    }


    public function response(Request $request)
    {
        $data = new Transaction();
        $data->user_id = Auth::user()->id;
        $data->order_id = $request->get('orderId');
        $data->amount = $request->get('orderAmount');
        $data->reference_id = $request->get('referenceId');
        $data->status = $request->get('txStatus');
        $data->payment_mode = $request->get('paymentMode');
        $data->txn_msg = $request->get('txMsg');
        $data->txn_date_time = $request->get('txTime');
        $data->signature = $request->get('signature');
        if ($data->save()) {
            if ($data->status == 'SUCCESS') {

                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d', strtotime($start_date . ' + 12 months'));
                User::where('id', Auth::user()->id)->update(['subscription_status' => Constant::ACTIVE, 'start_date' => $start_date, 'end_date' => $end_date]);
                $role =  $this->roleCheck(Auth::user()->perent_referral_id);
                if ($role == 'user') {
                    $total =  $this->refer_user_count(Auth::user()->perent_referral_id);

                    if ($total <= 3) {
                        $ref_user =  User::where(['referral_id' => Auth::user()->perent_referral_id, 'subscription_status' => Constant::ACTIVE])->first();
                        if (isset($ref_user)) {
                            $current_date = $ref_user->end_date;
                            $current_date = date('Y-m-d', strtotime($current_date . ' + 01 months'));
                            $ref_user->end_date = $current_date;
                            $ref_user->save();
                        }
                    }
                } elseif ($role == 'employee') {
                    $employee_commission = getSettingValue('employee_commission');
                    $emloyee =  User::where(['referral_id' => Auth::user()->perent_referral_id])->first();
                    if (isset($emloyee)) {
                        $amount = $emloyee->wallet_amount ?? 0;
                        $total = $amount + $employee_commission;
                        $emloyee->wallet_amount = $total;
                        $emloyee->save();
                        $this->saveEarning($emloyee->id, $employee_commission);
                    }

                    $tm_commission = getSettingValue('tm_commission');
                    $tm =  User::where(['referral_id' => $emloyee->perent_referral_id])->first();
                    if (isset($tm)) {
                        $amount = $tm->wallet_amount ?? 0;
                        $total = $amount + $tm_commission;
                        $tm->wallet_amount = $total;
                        $tm->save();
                        $this->saveEarning($tm->id, $tm_commission);
                    }
                } elseif ($role == 'team leader') {
                    $tm_commission = getSettingValue('tm_commission');
                    $tm =  User::where(['referral_id' => Auth::user()->perent_referral_id])->first();
                    if (isset($tm)) {
                        $amount = $tm->wallet_amount ?? 0;
                        $total = $amount + $tm_commission;
                        $tm->wallet_amount = $total;
                        $tm->save();

                        $this->saveEarning($tm->id, $tm_commission);
                    }
                } elseif ($role == 'business') {
                    $business_commission = getSettingValue('business_commission');
                    $business =  User::where(['referral_id' => Auth::user()->perent_referral_id])->first();
                    if (isset($business)) {
                        $amount = $business->wallet_amount ?? 0;
                        $total = $amount + $business_commission;
                        $business->wallet_amount = $total;
                        $business->save();
                        $this->saveEarning($business->id, $business_commission);
                    }
                }

                return redirect()->route('payment.success', base64_encode($data->id))->with('success', 'Payment successfull');
            } else {
                return redirect()->route('payment.failure', base64_encode($data->id))->with('error', 'Payment Faild');
            }
        } else {
            return view('web.pages.server_error');
        }
    }


    function saveEarning($user_id, $amount)
    {
        $data = new Earning();
        $data->user_id = $user_id;
        $data->amount = $amount;
        $data->save();
    }


    public function success($id)
    {

        $data = Transaction::where('id', base64_decode($id))->first();
        if (isset($data)) {

            if ($data->user_id == Auth::user()->id) {
                $result = ['title' => 'Payment Successfully', 'data' => $data];
                return view('web.pages.payment_success', $result)->with('error', 'Payment faield');
            } else {
                return view('web.pages.server_error');
            }
        } else {
            return view('web.pages.server_error');
        }
    }


    public function failure()
    {
        $result = ['title' => 'Payment Successfully'];
        return view('web.pages.payment_failure', $result)->with('error', 'Payment faield');
    }
}
