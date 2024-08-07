<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;
use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Jobs\OTP as JobsOTP;
use App\Models\Otp;
use App\Models\User;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\UserAuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
//use Spatie\Permission\Models\Permission;

class OrderController extends Controller
{

     public function __construct(Request $request)
    {
        $this->model = new Order();
        $this->sortableColumns = ['id','user_id','item_name','item_price','quantity'];
    }
    
  
     public function order_list(Request $request)
    {
        if ($request->session()->missing('user')) {
           return redirect('');
           }
           
           if ($request->ajax()) {
            $name_search = $request->input('name_search');
            $item_search = $request->input('item_search');
            $status_search = $request->input('status');
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request['search']['value'];
            $orderby = $request['order']['0']['column'];
            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw = $request['draw'];
            $sortableColumns = $this->sortableColumns;

            $start_date = ($request->get('start_date')) ? date('Y-m-d 00:00:01', strtotime($request->get('start_date'))) : date('Y-m-d 00:00:01', strtotime('-1 year', strtotime(date('Y-m-d'))));
            $end_date = ($request->get('end_date')) ? date('Y-m-d 23:59:59', strtotime($request->get('end_date'))) : date('Y-m-d 23:59:59');
            $value = $request->session()->get('user');
            //print_r($value);dd();
            $totaldata = $this->getData($status_search,$name_search,$item_search,$search, $sortableColumns[$orderby], $order, $value->id);

            $totaldata = $totaldata->count();
            $response = $this->getData($status_search,$name_search,$item_search,$search, $sortableColumns[$orderby], $order,$value->id);
            $response = $response->offset($start)->limit($limit)->orderBy('id', 'desc')->get();
            if (!$response) {
                $data = [];
                $paging = [];
            } else {
                $data = $response;
                $paging = $response;
            }

            $datas = [];
           // print_r($data);die;
            $i = 1;
            foreach ($data as $value) {
                //echo $value->user_id;
                 $user_data= User::where('id','=',$value->user_id)->first();
              
                $img='<img src="'.url("uploads/business_image/".$value->profile_img).'"class="image-icon" alt="profile image" >';
                $row['id'] = $start + $i;
                $row['user_name']='';
                if(isset($user_data->name)){
                $row['user_name'] = $user_data->name;
            }

               
                $row['item_name'] = ucfirst($value->item_name);
                $row['price'] = "$".$value->item_price;  
                $row['qnty'] = $value->quantity;  
                  
                if($value->order_status == 0) {
                $status = '<button type="button" class="btn btn-warning">Pending</button>';
                } 
                if ($value->order_status == 1) {
                $status = '<button type="button" class="btn btn-info">Confirmed</button>';
                } 
                if ($value->order_status == 2) {
                $status = '<button type="button" class="btn btn-info">Shipped</button>';
                } 
                if ($value->order_status == 3) {
                $status = '<button type="button" class="btn btn-success">Delivered</button>';
                } 
                 if ($value->order_status == 4) {
                $status = '<button type="button" class="btn btn-danger">Cancelled</button>';
                }
                /* if ($value->order_status == 5) {
                $status = '<button type="button" class="btn btn-info">In-Tranist</button>';
                }
*/
                $row['status']=$status;
                $row['updated_at'] = date('M d Y ', strtotime($value->created_at));
                $edit = '';
                if($value->order_status == 0 || $value->order_status == 1 || $value->order_status == 2 || $value->order_status == 5){
                $edit='<a href="" id="editCompany" data-toggle="modal" data-target="#feedmodal" data-id="'.$value->id.'"><i class="zmdi zmdi-edit zmdi-hc-fw text-success"></i></a>';
              }
                $view='';
                $view = '<a href="'.route('order.view',encrypt($value->id)).'" ><i class="zmdi zmdi-eye zmdi-hc-fw text-primary"></i></a>';
                
                 

                    $row['actions'] =$view.$edit;

                //$row['actions'] = createButton($edit);
                $datas[] = $row;
                $i++;
                unset($u);
            }
            $return = [
                "draw" => intval($draw),
                "recordsFiltered" => intval($totaldata),
                "recordsTotal" => intval($totaldata),
                "data" => $datas,
            ];
            return $return;
        }
        $order_data = Order::Where('business_id',$request->session()->get('user')->id)->pluck('user_id');
        $users= User::whereIn('id',$order_data)->get();
        $data = ['title' => ucfirst('Business List'),'users_data' =>$users];
        return view('frontend/orderlist',$data);
    }

        public function update($id)
    {
        $category = Order::find($id);

        return response()->json([
          'data' => $category
        ]);
    }


        public function order_view(Request $request,$id1)
    {
          $id = decrypt($id1);
          $value = $request->session()->get('user');
          $order_items=Order::where('id','=',$id)->first();
          if(!isset($order_items->business_id)){
             return redirect('order_list');
          }
          if($order_items->business_id!=$value->id){
             return redirect('order_list');

        }
          $shipping_ads=DB::table('shipping_address')->where('id','=',$order_items->shipping_id)->first();
          $data['order_items']=$order_items;
          $data['item_image'] = DB::table('business_item_images')->where('item_id','=',$order_items->item_id)->first();
          $data['shipping_ads'] = DB::table('shipping_address')->where('id','=',$order_items->shipping_id)->first();

        if(isset($shipping_ads->city) && !empty($shipping_ads->city)){
         $data['city'] = DB::table('city')->where('id','=',$shipping_ads->city)->first();
         }
         else{
            $data['city']='';
         }

        if(isset($shipping_ads->country) && !empty($shipping_ads->country)){
         $data['country'] = DB::table('country')->where('id','=',$shipping_ads->country)->first();
        }
        else{
             $data['country']='';
        }
         return view('frontend/order_view',$data);
        }


  

        public function order_manage(Request $request)
    {
       // $value = $request->session()->get('user');
        $id = $request->get('id');
        if(isset($id) && !empty($id)){
            $data = Order::where('id', $request->id)->first();
        }
        else{
            $data = new Order();
        }



        $data->order_status = $request->get('status');
        if($request->get('status')==4 ){
        $data->order_cancel_date = date("Y-m-d"); 
         }
        if($request->get('status')==1){
        $data->order_confirmed_date = date("Y-m-d");
        }
        if($request->get('status')==2){
        $data->order_shipped_date = date("Y-m-d");
        }
        if($request->get('status')==3){
        $data->order_delivered_date = date("Y-m-d");
        }

             
            $user_data=User::find($data->user_id);
            $bsns_data=User::find($data->business_id);
            if ($data->save()) {

                if ($data->order_status == 1) {
                $status = 'Confirmed';
                $message='Hurray! Thanks for your order. Your order '.$data->item_name.' has been confirmed by the seller. Watch out for an update once it is dispatched.';
                } 
                elseif ($data->order_status == 2) {
                $status = 'Shipped';
                $message='Hi '.$user_data->name.', Great news your order is on its way! You can check your shipment details or track your order by now';
                } 
                elseif ($data->order_status == 3) {
                $status = 'Delivered';
                $message='Delivered: Hey '.$user_data->name.'! Your order has been successfully delivered! Not received it? Let us know. ('.$data->email.')';
                } 
                elseif ($data->order_status == 4) {
                $status ='Cancelled';
                $message='Cancelled: Oops!! '.$user_data->name.'! Your order has been Cancelled. Shop again!!';
                }

              /*  elseif ($data->order_status == 5) {
                $status ='In-Tranist';
                $message='Hey '.$user_data->name.'! Your order will be delivered today! Happy shopping!!';
                }*/



            // Notificatiion sent
            //$message='Thank you for ordering from '.$bsns_data->name.'.Your order has been '.$status.'.';
     if($data->order_status == 5 || $data->order_status == 4 || $data->order_status == 3 || $data->order_status == 2 || $data->order_status == 1){
            send_notification_FCM($title='Order '.$status, $message, $token=$user_data->fcm_token, $redirection =$data->id );
            $notify_data = new Notification();
            $notify_data->user_id = $user_data->id;
            $notify_data->title = 'Order '.$status;
            $notify_data->message = $message;
            $notify_data->notification_type = '0';
            $notify_data->is_read = '0';
            $notify_data->redirect_id = $data->id;
            $notify_data->notification_status = 'order';
            $notify_data->save();
            // Email sent
            $content = getEmailContentValue(7); 
            $emailval = $content->description;
            $subject = $content->title;
            $logo = url('images/logo.png'); 
            $logo = url('uploads/logo').'/'.getSettingValue('logo');
            $replace_data = [ 
                            '@business'=>$bsns_data->name,
                            '@logo' => $logo,
                            '@name'=>$user_data->name,
                            '@status'=>$status,

                    ];  
                  foreach ($replace_data as $key => $value) {
                            $emailval = str_replace($key, $value, $emailval);
                        } 
              if (sendMail($user_data->email, $emailval, $subject)) {
                        return response()->json(['status' => true, 'message' => 'Order status has been changed successfully']);
                    
                    } 
}
                }
                                                          

       
        return response()->json(['status' => true, 'message' => 'Order status has been changed successfully']);
    }



    public function getData($status_search=null,$name_search=null,$item_search=null,$search = null  ,$orderby = null, $order = null,$id='', $request = null)
    {
       
        $q = Order::where('business_id',$id)->where('is_delete','=','0');
        $orderby = $orderby ? $orderby : 'created_at';
        $order = $order ? $order : 'desc';

     /*   if ($search && !empty($search)) {
            $q->where(function ($query) use ($search) {
                $query->where('item_name', 'LIKE', '%' . $search . '%');            
               // ->orwhere('email_address', 'LIKE', '%' . $search . '%');
                
                //->orwhere('description', $search);
            });
        }*/

         if ($item_search && !empty($item_search)) {
               $q->where(function ($query) use ($item_search) {
                $query->where('item_name', 'LIKE', '%' . $item_search . '%');            
            });
         }
           if ($name_search && !empty($name_search)) {
               $q->where(function ($query) use ($name_search) {
                $query->where('user_id', $name_search );            
            });
         }

          if (isset($status_search) && $status_search !='') {
               $q->where(function ($query) use ($status_search) {
                $query->where('order_status',$status_search);            
            });
         }

        $response = $q->orderBy($orderby, $order);
        return $response;
    }


   

}
