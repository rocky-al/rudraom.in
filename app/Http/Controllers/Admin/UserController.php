<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    protected $page = 'users';

    public function __construct(Request $request)
    { 
        $this->model = new User();
        $this->sortableColumns = ['id', 'updated_at','email','name'];
        // $this->middleware('permission:List Email Template	|View Email Template', ['only' => ['index', 'view']]);
        // $this->middleware('permission:Update Email Template', ['only' => ['index','form', 'manage']]);
    }

    public function form($id = null)
    {
        $data = User::where('id', $id)->first();

        if(isset($data) && !empty($data)){
        $result = ['title' => ucwords('Change  Status'), 'page' => $this->page, 'data' => $data];
         }
        else {
            $result = ['title' => ucwords('Add ' . $this->page . ' Details'), 'page' => $this->page, 'data' => $data];
                    }
        return view('admin.' . $this->page . '.form', $result);
    }
   

    public function manage(Request $request)
    {
        $id = $request->get('id');

        if(isset($id) && !empty($id)){
            $data = User::where('id', $request->id)->first();
        }else{
            if(User::where('email', '=', $request->get('email'))->count() > 0){
                return response()->json(['status' => false,  'message' => 'User is already exist with given email id']);
            }elseif(User::where('email', '=', $request->get('mobile'))->count() > 0){
                return response()->json(['status' => false,  'message' => 'User is already exist with given phone number']);
            }
            $data = new User();
            $data->assignRole('user');
            //echo $data->getRoleNames(); die();

            $data->status = '1';
            $data->email_verified_at = date('Y-m-d');
            $data->verification_status = '1';
        }

        $data->status = $request->get('status');
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => 'User' . ' has been updated Successfully']);
        } else {
            return response()->json(['status' => false,  'message' => 'Something went wrong']);
        }
    }



    public function index(Request $request)
    {
        if ($request->ajax()) {

            $name_search = $request->input('name_search');
            $email_search = $request->input('email_search');
            $status_search = $request->input('status_search');
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request['search']['value'];
            $orderby = $request['order']['0']['column'];
            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw = $request['draw'];
            $sortableColumns = $this->sortableColumns;

            $start_date = ($request->get('start_date')) ? date('Y-m-d 00:00:01', strtotime($request->get('start_date'))) : date('Y-m-d 00:00:01', strtotime('-1 year', strtotime(date('Y-m-d'))));
            $end_date = ($request->get('end_date')) ? date('Y-m-d 23:59:59', strtotime($request->get('end_date'))) : date('Y-m-d 23:59:59');

            $totaldata = $this->getData($name_search,$email_search,$status_search, $sortableColumns[$orderby], $order);

            $totaldata = $totaldata->count();
            $response = $this->getData($name_search,$email_search,$status_search, $sortableColumns[$orderby], $order);
            $response = $response->offset($start)->limit($limit)->orderBy('id', 'desc')->get();
           // print_r($response);dd;
            if (!$response) {
                $data = [];
                $paging = [];
            } else {
                $data = $response;
                $paging = $response;
            }

            $datas = [];
            //print_r($data);die;
            $i = 1;
            foreach ($data as $value) {

                if(isset($value->image) && !empty($value->image)){
                    
                    $img='<div class="table_imgs"><a href="javascript:" onclick="imageZoom(\'uploads/user_profile\',\'' . $value->image . '\')"><img src="'.url("uploads/user_profile/".$value->image).'"class="profile-image-icon" alt="profile image" ></a></div>';

                }else{
                    $img='';

                }

                $row['id'] = $start + $i;
                $row['name'] = ucfirst($value->name);
                $row['email'] = $value->email;
                $row['mobile'] = $value->mobile;
                $row['profile_img'] = $img; 


                /* if ($value->status == 1) {
                $status = '<button type="button" class="btn btn-success">Active</button>';
                } 
                if($value->status == 0) {
                $status = '<button type="button" class="btn btn-danger">Inactive</button>';
                } 
                $row['status'] = $status;*/
                $row['status'] = statusAction($value->status,$value->id,array(),'statusAction',$this->page . '.status');
                $row['updated_at'] = date('M d Y ', strtotime($value->updated_at));

                $edit = '';
                //if(Auth::user()->can('Update Email Template')){
                    $edit = editButton($this->page . '.form', ['id' => $value->id]);
                //}

                $change_status = '';
                //if(Auth::Business()->can('Update Email Template')){
                    $change_status = change_status($this->page . '.form', ['id' => $value->id]);
                //}

                $view = '';
                //if(Auth::user()->can('View Email Template')){
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
        $data = ['title' => ucfirst('Users List'), 'page' => $this->page];
        return view('admin.' . $this->page . '.list', $data);
    }

    public function getData($name_search = null,$email_search= null,$status_search = null, $orderby = null, $order = null, $request = null)
    {
        $q = User::where('id','!=','1')->where('is_delete','=','0');
        $orderby = $orderby ? $orderby : 'created_at';
        $order = $order ? $order : 'desc';

        // if ($search && !empty($search)) {
        //     $q->where(function ($query) use ($search) {
        //         $query->where('name', 'LIKE', '%' . $search . '%')
        //         ->orwhere('email', 'LIKE', '%' . $search . '%')
        //         ->orwhere('mobile', 'LIKE', '%' . $search . '%');  
        //         //->orwhere('description', $search);
        //     });
        // }

        if ($name_search && !empty($name_search)) {
         $q->where('name','LIKE','%'.$name_search.'%');
        }
        if ($email_search && !empty($email_search)) {
            $q->where('email','LIKE','%'.$email_search.'%');
        }
        if (isset($status_search) && $status_search !='') {            
            $q->where('status',$status_search);
        }
        
        $response = $q->orderBy($orderby, $order);
        return $response;
    }


    public function view($id)
    {
        $data = User::where('id', $id)->first();
        if (isset($data)) {
            $result = ['title' => ucwords($this->page . ' Details'), 'page' => $this->page, 'data' => $data];
            return view('admin.' . $this->page . '.view', $result);
        } else {
            $result = ['title' => ucwords('Error'), 'page' => $this->page];
            return view('admin.error.404_popup', $result);
        }
    }


    public function delete($id)
    {
        $data = User::where('id', $id)->first();
        $data->is_delete = "1";
        $data->deleted_at = date('Y-m-d H:i:s');

        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ' User has been deleted Successfully']);
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
        $data = User::where('id', $id)->first();
        $data->status = $request->get('status');
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been '.$status.' Successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
