<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    protected $page = 'content';

    public function __construct(Request $request)
    {
        $this->model = new Content();
        $this->sortableColumns = ['id', 'title','name','status','created_at'];
        $this->middleware('permission:List Content|View Content', ['only' => ['index', 'view']]);
        $this->middleware('permission:Update Content', ['only' => ['index','form', 'manage']]);
    }

    public function form($id = null)
    {
        $data = Content::where('id', $id)->first();
        $role = Role::where('name', '!=',  'Admin')->pluck('name', 'id');

        if (isset($data)) {
            $result = ['title' => ucwords('Update ' . $this->page . ' Details'), 'page' => $this->page, 'role' => $role, 'data' => $data];
        } else {
            $result = ['title' => ucwords('Add New ' . $this->page), 'page' => $this->page, 'role' => $role];
        }
        return view('admin.' . $this->page . '.form', $result);
    }

    public function manage(Request $request)
    {
        $id = $request->get('id');
        $request->validate([
            'title' => 'required|max:30',
            'name'=> 'required|max:30',
            'description' => 'required'
        ]);

        if (isset($id)) {
            $data = Content::where('id', $request->id)->first();
        } else {
            $data = new User();
        }

        $data->title = $request->get('title');
        $data->name = $request->get('name');
        $data->description = $request->get('description');
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been saved successfully']);
        } else {
            return response()->json(['status' => false,  'message' => 'Something went wrong']);
        }
    }



    public function index(Request $request)
    {
        if ($request->ajax()) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request['search']['value'];

            $orderby = $request['order']['0']['column'];
            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw = $request['draw'];
            $sortableColumns = $this->sortableColumns;

            $start_date = ($request->get('start_date')) ? date('Y-m-d 00:00:01', strtotime($request->get('start_date'))) : date('Y-m-d 00:00:01', strtotime('-1 year', strtotime(date('Y-m-d'))));
            $end_date = ($request->get('end_date')) ? date('Y-m-d 23:59:59', strtotime($request->get('end_date'))) : date('Y-m-d 23:59:59');

            $totaldata = $this->getData($search, $sortableColumns[$orderby], $order);

            $totaldata = $totaldata->count();
            $response = $this->getData($search, $sortableColumns[$orderby], $order);
            $response->whereBetween('created_at', [$start_date, $end_date]);

            $response = $response->offset($start)->limit($limit)->orderBy('id', 'desc')->get();
            if (!$response) {
                $data = [];
                $paging = [];
            } else {
                $data = $response;
                $paging = $response;
            }
              
            $datas = [];
            $i = 1;
            //echo "<pre>";
           // print_r($data);die;
            foreach ($data as $value) {
                $row['id'] = $start + $i;
                $row['title'] = ucfirst($value->title);
                $row['name'] = $value->name;
                $row['status'] = statusAction($value->status,$value->id,array(),'statusAction',$this->page . '.status');
                $row['created_at'] = date('M d Y ', strtotime($value->created_at));

                $edit = '';
                if(Auth::user()->can('Update Content')){
                    $edit = editButton($this->page . '.form', ['id' => $value->id]);
                }

                $view = '';
                if(Auth::user()->can('View Content')){
                    $view = viewButton($this->page . '.view', ['id' => $value->id]);
                }

                $row['actions'] = createButton($edit);
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
        $data = ['title' => ucfirst($this->page . ' List'), 'page' => $this->page];
        return view('admin.' . $this->page . '.index', $data);
    }

    public function getData($search = null  ,$orderby = null, $order = null, $request = null)
    {
        $q = Content::where('id','>','0');
        $orderby = $orderby ? $orderby : 'created_at';
        $order = $order ? $order : 'desc';

        if ($search && !empty($search)) {
            $q->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%' . $search . '%')
                ->orwhere('name', 'LIKE', '%' . $search . '%')
                ->orwhere('description', $search);
            });
        }

        if (isset($request->status)) {
            $q->where('status', $request->status);
        }

        $response = $q->orderBy($orderby, $order);
        return $response;
    }

    public function view($id)
    {
        $data = Content::where('id', $id)->first();

        if (isset($data)) {
            $result = ['title' => ucwords($this->page . ' Details'), 'page' => $this->page, 'data' => $data];
            return view('admin.' . $this->page . '.view', $result);
        } else {
            $result = ['title' => ucwords('Error'), 'page' => $this->page];
            return view('admin.error.404_popup', $result);
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
        $data = Content::where('id', $id)->first();
        $data->status = $request->get('status');
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been '.$status.' Successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}




