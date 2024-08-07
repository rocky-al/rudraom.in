<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\Query;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use Spatie\Permission\Models\Permission; 

class QueryController extends Controller
{
    protected $page = 'query';

    public function __construct(Request $request)
    {
        $this->model = new Query();
        $this->sortableColumns = ['id', 'user_id', 'updated_at'];
        // $this->middleware('permission:List Email Template	|View Email Template', ['only' => ['index', 'view']]);
        // $this->middleware('permission:Update Email Template', ['only' => ['index','form', 'manage']]);
    }

    public function form($id = null)
    {
        $data = Query::where('id', $id)->first();

        if(isset($data) && !empty($data)){
        $result = ['title' => ucwords('Update ' . $this->page . ' Details'), 'page' => $this->page, 'data' => $data];
         }
        else {
            $result = ['title' => ucwords('Add ' . $this->page . ' Details'), 'page' => $this->page, 'data' => $data];
                    }
        return view('admin.' . $this->page . '.form', $result);
    }
   

    public function manage(Request $request)
    {
        $id = $request->get('id');

        $request->validate([
           // 'name' => 'required|regex:(^([a-zA-z0-9-_ ]+)?$)',
           'name' => 'required',
        ]);
    /*    if(Category::where('name', '=', $request->get('name'))->count() > 0){
                return response()->json(['status' => false,  'message' => 'Category name is already exist ']);}*/


            if (isset($id) && !empty($id)) {
                $name_validate = Query::where('name', request('name'))->where('id', '!=', $request->id)->where('is_delete','0')->first();
            } else {
                $name_validate = Query::where('name', request('name'))->where('is_delete','0')->first();
             
            }
            if ($name_validate) {
                 return response()->json(['status' => false,  'message' => 'Category name is already exist ']);
            }


        if(isset($id) && !empty($id)){
            $data = Query::where('id', $request->id)->first();
        }else{
            
            $data = new Query();
            //$data->assignRole('Category');
            //echo $data->getRoleNames(); die();
            $data->status = '1';
        }

        $data->name = $request->get('name');
        $data->status = $request->get('status');
        
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been updated Successfully']);
        } else {
            return response()->json(['status' => false,  'message' => 'Something went wrong']);
        }
    }



    public function index(Request $request)
    {
        if ($request->ajax()) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $name_search = $request->input('username_search');
            $order_id= $request->input('order_id');
            $status_search= $request->input('status_search');
            $search = $request['search']['value'];
            $orderby = $request['order']['0']['column'];
            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw = $request['draw'];
            $sortableColumns = $this->sortableColumns;

            $start_date = ($request->get('start_date')) ? date('Y-m-d 00:00:01', strtotime($request->get('start_date'))) : date('Y-m-d 00:00:01', strtotime('-1 year', strtotime(date('Y-m-d'))));
            $end_date = ($request->get('end_date')) ? date('Y-m-d 23:59:59', strtotime($request->get('end_date'))) : date('Y-m-d 23:59:59');

            $totaldata = $this->getData($order_id,$status_search,$name_search,$search, $sortableColumns[$orderby], $order);

            $totaldata = $totaldata->count();
            $response = $this->getData($order_id,$status_search,$name_search,$search, $sortableColumns[$orderby], $order);
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
                $row['id'] = $start + $i;
                $row['name'] = ucfirst($value->name);
                $row['item_name'] = ucfirst($value->item_name);
                $row['order_id'] = ucfirst($value->order_id);
                $string = ucfirst($value->message);
                $limitedString = substr($string, 0, 50) . (strlen($string) > 50 ? "..." : "");
                $row['message'] =  $limitedString;
                $row['status'] = statusAction($value->status,$value->id,array(),'statusAction',$this->page . '.status');
                $row['updated_at'] = date('M d Y ', strtotime($value->created_at));

                $edit = '';
                //if(Auth::Category()->can('Update Email Template')){
                    $edit = editButton($this->page . '.form', ['id' => $value->id]);
                //}

                $view = '';
                //if(Auth::Category()->can('View Email Template')){
                    $view = viewButton($this->page . '.view', ['id' => $value->id]);
                //}

                $delete=deleteButton($this->page . '.delete', ['id' => $value->id]);

                $row['actions'] = createButton($view.$delete);
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
       
        $order_data = Query::pluck('user_id');
        $users= User::whereIn('id',$order_data)->get();
         $data = ['title' => ucfirst('raise query List'), 'page' => $this->page,'users_data' =>$users];
        return view('admin.' . $this->page . '.list', $data);
    }

    public function getData($order_id=null,$status_search=null,$name_search=null,$search = null  ,$orderby = null, $order = null, $request = null)
    {
        $q = Query::select('users.name','orders.item_name','queries.*')
                   ->join('users', 'users.id', '=', 'queries.user_id')
                   ->join('orders','orders.id', '=', 'queries.order_id')
                   ->where('queries.id','!=','0')
                   ->whereNull('queries.deleted_at');

        $orderby = $orderby ? $orderby : 'created_at';
        $order = $order ? $order : 'desc';

     /*   if ($search && !empty($search)) {
            $q->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
                //->orwhere('email', 'LIKE', '%' . $search . '%')
                //->orwhere('mobile', 'LIKE', '%' . $search . '%');  
                //->orwhere('description', $search);
            });
        }*/

       if ($name_search && !empty($name_search)) {
            $q->where(function ($query) use ($name_search) {
                $query->where('users.id', 'LIKE', '%' . $name_search . '%');
                
            });
        }
       if (isset($order_id) && $order_id !='') {            
               $q->where('queries.order_id',$order_id);
           }

       // if (isset($date_search) && $date_search !='') {            
       //         $q->where("created_at" ,'=', $date_search);
       //     }

        $response = $q->orderBy($orderby, $order);
        return $response;
    }


    public function view($id)
    {
        $data =Query::select('users.name','orders.item_name','queries.*')
                   ->join('users', 'users.id', '=', 'queries.user_id')
                   ->join('orders','orders.id', '=', 'queries.order_id')
                   ->where('queries.id', $id)
                   ->first();
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
        $data = Query::where('id', $id)->first();
        $data->delete();
        return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been deleted Successfully']);

        // if ($data->save()) {
        //     return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been deleted Successfully']);
        // } else {
        //     return response()->json(['status' => false, 'message' => 'Something went wrong']);
        // }
    }



    public function status(Request $request)
    {     
        $id = $request->get('id');
        if($request->get('status')==Constant::ACTIVE){
            $status = 'active';
        }else{
            $status = 'in-active';
        }
        $data = Query::where('id', $id)->first();
        $data->status = $request->get('status');
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been '.$status.' successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
