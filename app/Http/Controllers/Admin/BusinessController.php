<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Category;
use App\Models\BusinessImages;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
//use Spatie\Permission\Models\Permission;

class BusinessController extends Controller
{
    protected $page = 'business';

    public function __construct(Request $request)
    {
        $this->model = new Business();
        $this->sortableColumns = ['id', 'name','phone_no','license_no','profile_img','registration_date','updated_at'];
    }

    public function form($id = null)
    {
        $data = Business::where('id', $id)->first();

        if(isset($data) && !empty($data)){
        $result = ['title' => ucwords('Change Status'), 'page' => $this->page, 'data' => $data];
         }
        else {
            $result = ['title' => ucwords('Add' . $this->page . ' Details'), 'page' => $this->page, 'data' => $data];
                    }
        return view('admin.' . $this->page . '.form', $result);
    }
   

    public function manage(Request $request)
    {
       // print_r($request->all());die;
        $id = $request->get('id');
        if(isset($id) && !empty($id)){
            $data = Business::where('id', $request->id)->first();
        }
        $data->reject_reason = $request->get('reason');
        $data->status = $request->get('status');
        $mail_sent=$request->get('status');
        if($mail_sent==1){
        $pass=Str::random(6);
        $data->password=Hash::make($pass);
          }
       if ($data->save()) {

                if($mail_sent==2 || $mail_sent==1){
                    if($mail_sent==1){
                    $content = getEmailContentValue(4);
                     }
                     else{
                    $content = getEmailContentValue(3);
                     }
                  //  print_r($content);die;
                    $emailval = $content->description;
                    $subject = $content->title;
                  
                    if(empty(getSettingValue('logo'))){
                        $logo = url('images/logo.png');
                    }else{
                        $logo = url('uploads/logo').'/'.getSettingValue('logo');
                    }
                    if($mail_sent==2){
                    $replace_data = [
                            '@link_value' => Constant::APP_URL.'reset-password/',
                            '@logo' => $logo,
                            '@name'=>$data->name,
                            '@reject_reason'=>$data->reject_reason,
                        ];
                    }
                    else{
                         $replace_data = [
                            '@link_value' => Constant::APP_URL,
                            '@logo' => $logo,
                            '@name'=>$data->name,
                            '@email'=>$data->email_address,
                            '@pass'=>$pass,
                        ];
                    }
                    foreach ($replace_data as $key => $value) {
                            $emailval = str_replace($key, $value, $emailval);
                        }
                    if (sendMail($data->email_address, $emailval, $subject)) {
                        return response()->json(['status' => true, 'message' => 'A mail has been sent successfully']);
                    
                    } else {
                        return response()->json(['status' => false, 'message' => 'Something went wrong. Please try again.']);
                    }
                } 
                   return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been updated successfully']);
                   
       } 


        else {
            return response()->json(['status' => false,  'message' => 'Something went wrong']);
        }
 
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $name_search = $request->input('name_search');
            $email_search = $request->input('email_search');
            $status_search = $request->input('status_search');

            $phone_search = $request->input('phone_search');
            $licence_search = $request->input('licence_search');
            $category_search = $request->input('category_search');

            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request['search']['value'];
            $orderby = $request['order']['0']['column'];
            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw = $request['draw'];
            $sortableColumns = $this->sortableColumns;

            $start_date = ($request->get('start_date')) ? date('Y-m-d 00:00:01', strtotime($request->get('start_date'))) : date('Y-m-d 00:00:01', strtotime('-1 year', strtotime(date('Y-m-d'))));
            $end_date = ($request->get('end_date')) ? date('Y-m-d 23:59:59', strtotime($request->get('end_date'))) : date('Y-m-d 23:59:59');

            $totaldata = $this->getData($name_search,$email_search,$status_search,$phone_search,$licence_search,$category_search, $sortableColumns[$orderby], $order);

            $totaldata = $totaldata->count();
            $response = $this->getData($name_search,$email_search,$status_search,$phone_search,$licence_search,$category_search, $sortableColumns[$orderby], $order);
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

                $category = Category::where('id', $value->category_id)->first();

                $category_data = 
                $img='<div class="table_imgs"><a href="javascript:" onclick="imageZoom(\'uploads/business_profile\',\'' . $value->profile_img . '\')"><img src="'.url("uploads/business_profile/".$value->profile_img).'"class="profile-image-icon" alt="profile image" ></a></div>';
                $row['id'] = $start + $i;
                //$row['name'] = ucfirst($value->name);
                $row['name']='<a href="javascript:" class="dropdown-item model_open bussness_names"  type="button" url="' . customeRoute($this->page . '.view', ['id' => $value->id]) . '"> '.ucfirst($value->name).' </a>';
                $row['email'] = $value->email_address;
                $row['mobile'] = $value->phone_no;
                $row['address'] = $value->address;
                $row['license_no'] = $value->license_no;
                $row['registration_date'] = date('M d Y ', strtotime($value->registration_date));
                $row['profile_img'] = $img;
                $row['category_name'] = $category->name;
              
                if ($value->status == 1) {
                $status = '<button type="button" class="btn btn-primary disabled">Approved</button>';
                } 
                if($value->status == 0) {
              //  $status = '<button type="button" class="btn btn-warning">Pending</button>';
                  $status = '<a class="model_open" url="' . customeRoute($this->page . '.form',  ['id' => $value->id]) . '"><button type="button" class="btn btn-warning">Pending</button></a>';
                } 
                if($value->status == 2) {
               // $status = '<button type="button" class="btn btn-danger">Rejected</button>';
                    $status = '<button type="button" class="btn btn-danger disabled">Rejected</button>';  
                }

                $row['status']=$status;

                $row['is_active'] = statusAction($value->is_active,$value->id,array(),'statusAction',$this->page . '.status');
              

              /*  $change_status = '';
                if($value->status != 1){
                    $change_status = change_status($this->page . '.form', ['id' => $value->id]);
                }*/

                $view = '';
                //if(Auth::Business()->can('View Email Template')){
                    $view = viewButton($this->page . '.view', ['id' => $value->id]);
                //}

                $delete=deleteButton($this->page . '.delete', ['id' => $value->id]);

                $row['actions'] = createButton($view. $delete);
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

        $category = Category::where('status', '1')->where('is_delete', '0')->get();
         

        $data = ['title' => ucfirst('Business List'), 'page' => $this->page,'category_data' =>$category];
        return view('admin.' . $this->page . '.list', $data);
    }

    public function getData($name_search= null,$email_search= null,$status_search= null,$phone_search= null,$licence_search= null,$category_search= null,$orderby = null, $order = null, $request = null)
    {
        $q = Business::where('id','!=','0')->where('is_delete','=','0');
        $orderby = $orderby ? $orderby : 'created_at';
        $order = $order ? $order : 'desc';

        // if ($search && !empty($search)) {
        //     $q->where(function ($query) use ($search) {
        //         $query->where('name', 'LIKE', '%' . $search . '%')
        //         ->orwhere('email_address', 'LIKE', '%' . $search . '%')
        //         ->orwhere('license_no', 'LIKE', '%' . $search . '%')
        //         ->orwhere('phone_no', 'LIKE', '%' . $search . '%');  
        //         //->orwhere('description', $search);
        //     });
        // }

        if ($name_search && !empty($name_search)) {
            $q->where('name','LIKE','%'.$name_search.'%');
           }
           if ($email_search && !empty($email_search)) {
               $q->where('email_address','LIKE','%'.$email_search.'%');
           }
           if (isset($status_search) && $status_search !='') {            
               $q->where('status',$status_search);
           }
           if ($phone_search && !empty($phone_search)) {
            $q->where('phone_no','LIKE','%'.$phone_search.'%');
           }
            if ($licence_search && !empty($licence_search)) {
                $q->where('license_no','LIKE','%'.$licence_search.'%');
            }
          if (isset($status_search) && $status_search !='') {            
               $q->where('status',$status_search);
           }
           if (isset($category_search) && $category_search !='') {            
            $q->where('category_id',$category_search);
        }

        $response = $q->orderBy($orderby, $order);
        return $response;
    }


    public function view($id)
    {
        $data = Business::where('id', $id)->first();

        $category = Category::where('id', $data->category_id)->first();
        $BusinessImages = BusinessImages::where('business_id', $data->id)->get();
        

        if (isset($data)) {
            $result = ['title' => ucwords($this->page . ' Details'), 'page' => $this->page, 'data' => $data,'category' => $category,'businessImages' => $BusinessImages];
            return view('admin.' . $this->page . '.view', $result);
        } else {
            $result = ['title' => ucwords('Error'), 'page' => $this->page];
            return view('admin.error.404_popup', $result);
        }
    }


    public function delete($id)
    {
        $data = Business::where('id', $id)->first();
        $data->is_delete = "1";

        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been deleted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }



    public function status(Request $request)
    {     
        $id = $request->get('id');
        if($request->get('status')==Constant::ACTIVE){
            $status = 'active';
        }else{
            $status = 'in-active';
        }
        $data = Business::where('id', $id)->first();
        $data->is_active = $request->get('status');
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been '.$status.' successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
