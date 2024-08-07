<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    protected $page = 'feedback';

    public function __construct(Request $request)
    {
        $this->model = new Feedback();
        $this->sortableColumns = ['id', 'name', 'status', 'created_at'];
        $this->middleware('permission:Feedback List|Feedback View', ['only' => ['index', 'view']]);
    }

  
    public function view($id)
    {
        $data = Feedback::where('id', $id)->first();

        if (isset($data)) {
            $result = ['title' => ucwords($this->page . ' Details'), 'page' => $this->page, 'data' => $data];
            return view('admin.' . $this->page . '.view', $result);
        } else {
            $result = ['title' => ucwords('Error'), 'page' => $this->page];
            return view('admin.error.404_popup', $result);
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
                $row['name'] = ucfirst($value->name);
                $row['phone'] = $value->phone;
                $row['email'] = $value->email;
                $row['subject'] = ucfirst($value->subject);
                $row['created_at'] = $value->created_at;

               


                $view = '';
                if(Auth::user()->can('Feedback View')){
                    $view = viewButton($this->page . '.view', ['id' => $value->id]);
                }

                

                $row['actions'] = createButton($view);
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
        $q = Feedback::where('id' ,'!=', 0);
        $orderby = $orderby ? $orderby : 'created_at';
        $order = $order ? $order : 'desc';

        if ($search && !empty($search)) {
            $q->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            });
        }

        if (isset($request->status)) {
            $q->where('status', $request->status);
        }

        $response = $q->orderBy($orderby, $order);
        return $response;
    }

   


}
