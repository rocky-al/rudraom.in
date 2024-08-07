<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    protected $page = 'emailTemplate';

    public function __construct(Request $request)
    {
        $this->model = new EmailTemplate();
        $this->sortableColumns = ['id', 'title', 'updated_at'];
        $this->middleware('permission:List Email Template	|View Email Template', ['only' => ['index', 'view']]);
        $this->middleware('permission:Update Email Template', ['only' => ['index','form', 'manage']]);
    }

    public function form($id = null)
    {
        $data = EmailTemplate::where('id', $id)->first();
        $result = ['title' => ucwords('Update Email Template Details'), 'page' => $this->page, 'data' => $data];
        return view('admin.' . $this->page . '.form', $result);
    }

    public function manage(Request $request)
    {
        $id = $request->get('id');
        $request->validate([
            'title' => 'required|max:30',
            'subject' => 'required|max:30',
            'description' => 'required|max:10000'
        ]);

        $data = EmailTemplate::where('id', $request->id)->first();
        $data->title = $request->get('title');
        $data->subject = $request->get('subject');
        $data->description = $request->get('description');
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => 'Email Template ' . ' has been updated successfully']);
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
            foreach ($data as $value) {
                $row['id'] = $start + $i;
                $row['title'] = ucfirst($value->title);
                $row['subject'] = ucfirst($value->subject);
                
                $row['status'] = statusAction($value->status,$value->id,array(),'statusAction',$this->page . '.status');
                $row['updated_at'] = date('M d Y ', strtotime($value->updated_at)); 

                $edit = '';
                if(Auth::user()->can('Update Email Template')){
                    $edit = editButton($this->page . '.form', ['id' => $value->id]);
                }

                $view = '';
                if(Auth::user()->can('View Email Template')){
                    $view = viewButton($this->page . '.view', ['id' => $value->id]);
                }

                $row['actions'] = createButton($edit . $view);
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
        $data = ['title' => ucfirst('Email Template List'), 'page' => $this->page];
        return view('admin.' . $this->page . '.index', $data);
    }

    public function getData($search = null  ,$orderby = null, $order = null, $request = null)
    {
        $q = EmailTemplate::select("*");
        $orderby = $orderby ? $orderby : 'created_at';
        $order = $order ? $order : 'desc';

        if ($search && !empty($search)) {
            $q->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%' . $search . '%')
                ->orwhere('subject', 'LIKE', '%' . $search . '%');
                
                //->orwhere('description', $search);
            });
        }
        $response = $q->orderBy($orderby, $order);
        return $response;
    }

 
    public function view($id)
    {
        $data = EmailTemplate::where('id', $id)->first();
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
        $data = EmailTemplate::where('id', $id)->first();
        $data->status = $request->get('status');
        if ($data->save()) {
            return response()->json(['status' => true, 'message' => 'Email Template ' . ' has been '.$status.' successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }
} 
