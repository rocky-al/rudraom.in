<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $page = 'notification';

    public function __construct(Request $request)
    {
        $this->model = new Notification();
        $this->sortableColumns = ['id', 'first_name', 'mobile', 'email', '', 'status', 'created_at'];
        $this->middleware('permission:List Notification|Add Notification', ['only' => ['index', 'view']]);
        $this->middleware('permission:Add Notification', ['only' => ['manage', 'form']]);
    }

    public function form($id = null)
    {
        $data = User::select('*')
                           ->where('is_delete', '=', '0')
                           ->where('id', '!=', '1')
                           ->get();
       
       /* echo '<pre>';
        print_r($data);die;*/
        if (isset($data)) {
            $result = ['title' => ucwords('Update ' . $this->page . ' Details'), 'page' => $this->page];
        } else {
            $result = ['title' => ucwords('Add New ' . $this->page), 'page' => $this->page];
        }
        return view('admin.' . $this->page . '.form', ['result'=>$result,'data'=>$data]);
    }


    // send message notification


    public function send_message(Request $request)
    {   

       if($request->chkall=="on"){

         $request->validate([
            'title' => 'required',
            'message' => 'required|max:400',
            ]);

         $data = User::select('*')
                        ->where('id', '!=', '1')
                        ->where('is_delete', '=', '0')
                        ->get();


        foreach ($data as  $value) {

            //print_r($value);dd;

              $data = new Notification();

                $data->message = $request->message;
                 $data->title = $request->title;
                $data->user_id = $value['id'];

                $data->save();
                
             
            }



         return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been sent Successfully']);
}

        else{

        $request->validate([
            'title' => 'required',
            'message' => 'required|max:400',
            
        ]);

            foreach ($request->user as  $value) {

              $data = new Notification();

                $data->message = $request->message;
                $data->title = $request->title;
                $data->user_id = $value;

                $data->save();
                
             
            }

    return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been sent successfully']);
}

    }



    public function manage(Request $request)
    {
        $id = $request->get('id');
        $request->validate([
            'title' => 'required',
            'description' => 'required|max:400',
        ]);

        $user_type = $request->get('user_type');
        if ($user_type == 1) {
            $query = User::whereHas(
                'roles',
                function ($q) {
                    $q->whereIn('name', ['team leader', 'employee']);
                }
            );
            $user_id = $query->pluck('id');
        } elseif ($user_type == 2) {
            $query = User::whereHas(
                'roles',
                function ($q) {
                    $q->whereIn('name', ['team leader',]);
                }
            );
            $user_id = $query->pluck('id');
        } elseif ($user_type == 3) {
            $query = User::whereHas(
                'roles',
                function ($q) {
                    $q->whereIn('name', ['employee']);
                }
            );
            $user_id = $query->pluck('id');
        }


        if (!empty($user_id)) {
            foreach ($user_id as $key => $item) {
                $data = new Notification();
                $data->title = $request->get('title');
                $data->description = $request->get('description');
                $data->sender_id = Auth::user()->id;
                $data->receiver_id = $item;
                $data->notification_type = 0;
                $data->save();
            }
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been sent successfully']);
        } else {
            return response()->json(['status' => false,  'message' => 'User not found']);
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
                $row['title'] = $value->title ?? '-';
                $row['sender'] = 'Owner';
                $row['receiver'] = $value->receiver->first_name ?? '-';
                $row['created_at'] = $value->created_at;
                $view = '';
                if (Auth::user()->can('View Notification')) {
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

    public function getData($search = null, $orderby = null, $order = null, $request = null)
    {

        $user_role = Auth::user()->getRoleNames();
        $check = $user_role[0];

        if($check == 'admin'){
            $q = Notification::where('id' ,'!=', 0);
        }else{
            $q = Notification::where('receiver_id' , Auth::user()->id);
        }


       
        $orderby = $orderby ? $orderby : 'users.created_at';
        $order = $order ? $order : 'desc';

        if ($search && !empty($search)) {
            $q->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%' . $search . '%')
                    ->orwhere('description', 'LIKE', '%' . $search . '%');
            });
        }
        $response = $q->orderBy($orderby, $order);
        return $response;
    }


    public function view($id)
    {
        $data = Notification::where('id', $id)->first();
        if (isset($data)) {
            $result = ['title' => ucwords($this->page . ' Details'), 'page' => $this->page, 'data' => $data];
            return view('admin.' . $this->page . '.view', $result);
        } else {
            $result = ['title' => ucwords('Error'), 'page' => $this->page];
            return view('admin.error.404_popup', $result);
        }
    }
}
