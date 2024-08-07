<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use Spatie\Permission\Models\Permission; 

class CityController extends Controller
{
    protected $page = 'city';

    public function __construct(Request $request)
    {
        $this->model = new City();
        $this->sortableColumns = ['id', 'name', 'updated_at'];
        // $this->middleware('permission:List Email Template	|View Email Template', ['only' => ['index', 'view']]);
        // $this->middleware('permission:Update Email Template', ['only' => ['index','form', 'manage']]);
    }

    public function form($id = null)
    {
        $data = City::where('id', $id)->first();
        $country =Country::where('is_delete','=','0')->where('status','1')->get();
       // print_r($country);dd();

        if(isset($data) && !empty($data)){
        $result = ['title' => ucwords('Update ' . $this->page . ' Details'), 'page' => $this->page, 'data' => $data,'country'=>$country];
         }
        else {
            $result = ['title' => ucwords('Add ' . $this->page . ' Details'), 'page' => $this->page, 'data' => $data,'country'=>$country];
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
    /*    if(City::where('name', '=', $request->get('name'))->count() > 0){
                return response()->json(['status' => false,  'message' => 'City name is already exist ']);}*/


            if (isset($id) && !empty($id)) {
                $name_validate = City::where('country_id',request('country_id'))->where('name', request('name'))->where('id', '!=', $request->id)->where('is_delete','0')->first();
            } else {
                $name_validate = City::where('country_id',request('country_id'))->where('name', request('name'))->where('is_delete','0')->first();
             
            }
            if ($name_validate) {
                 return response()->json(['status' => false,  'message' => 'City name is already exist ']);
            }


        if(isset($id) && !empty($id)){
            $data = City::where('id', $request->id)->first();
        }else{
            
            $data = new City();
            //$data->assignRole('City');
            //echo $data->getRoleNames(); die();
            $data->status = '1';
        }

        $data->name = $request->get('name');
        $data->status = $request->get('status');
        $data->country_id=$request->get('country_id');
        
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
            $name_search = $request->input('name_search');
            $date_search= $request->input('date');
            $country_search=$request->input('country_id');
            $status_search= $request->input('status_search');
            $search = $request['search']['value'];
            $orderby = $request['order']['0']['column'];
            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw = $request['draw'];
            $sortableColumns = $this->sortableColumns;

            $start_date = ($request->get('start_date')) ? date('Y-m-d 00:00:01', strtotime($request->get('start_date'))) : date('Y-m-d 00:00:01', strtotime('-1 year', strtotime(date('Y-m-d'))));
            $end_date = ($request->get('end_date')) ? date('Y-m-d 23:59:59', strtotime($request->get('end_date'))) : date('Y-m-d 23:59:59');

            $totaldata = $this->getData($country_search,$date_search,$status_search,$name_search,$search, $sortableColumns[$orderby], $order);

            $totaldata = $totaldata->count();
            $response = $this->getData($country_search,$date_search,$status_search,$name_search,$search, $sortableColumns[$orderby], $order);
            $response = $response->offset($start)->limit($limit)->orderBy('id', 'desc')->get();
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
                $country=Country::where('id',$value->country_id)->first();
                if(isset($country->name)){
                $country_name=$country->name;
            }
            else{
                $country_name='N/A';
            }
                $row['id'] = $start + $i;
                $row['country']=$country_name;
                $row['name'] = ucfirst($value->name);
                $row['status'] = statusAction($value->status,$value->id,array(),'statusAction',$this->page . '.status');
                $row['updated_at'] = date('M d Y ', strtotime($value->created_at));

                $edit = '';
                //if(Auth::City()->can('Update Email Template')){
                    $edit = editButton($this->page . '.form', ['id' => $value->id]);
                //}

                $view = '';
                //if(Auth::City()->can('View Email Template')){
                    $view = viewButton($this->page . '.view', ['id' => $value->id]);
                //}

                $delete=deleteButton($this->page . '.delete', ['id' => $value->id]);

                $row['actions'] = createButton($edit .$delete);
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
        $country =Country::where('is_delete','=','0')->where('status','1')->get();
        $data = ['title' => ucfirst('City List'), 'page' => $this->page,'country'=>$country];
        return view('admin.' . $this->page . '.list', $data);
    }

    public function getData($country_search=null,$date_search=null,$status_search=null,$name_search=null,$search = null  ,$orderby = null, $order = null, $request = null)
    {
        $q = City::where('id','!=','0')->where('is_delete','=','0');
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
                $query->where('name', 'LIKE', '%' . $name_search . '%');
                
            });
        }
       if (isset($status_search) && $status_search !='') {            
               $q->where('status',$status_search);
           }

       if (isset($date_search) && $date_search !='') {            
               $q->where("created_at" ,'=', $date_search);
           }

       if (isset($country_search) && $country_search !='') {            
               $q->where("country_id" ,'=', $country_search);
           }


        $response = $q->orderBy($orderby, $order);
        return $response;
    }


    public function view($id)
    {
        $data = City::where('id', $id)->first();
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
        $data = City::where('id', $id)->first();
        $data->is_delete = "1";

        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been deleted Successfully']);
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
        $data = City::where('id', $id)->first();
        $data->status = $request->get('status');
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been '.$status.' successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
