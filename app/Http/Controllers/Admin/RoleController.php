<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $page = 'role';

    public function __construct(Request $request)
    {
        $this->model = new Role();
        $this->sortableColumns = ['id', 'name', 'created_at'];
        // $this->middleware('permission:role_list|role_add|role_edit', ['only' => ['index', 'view']]);
        // $this->middleware('permission:role_add', ['only' => ['create', 'add']]);
        // $this->middleware('permission:role_edit', ['only' => ['edit', 'update']]);
    }

    public function add(Request $request)
    {
        $result = ['title' => 'Add New Role', 'page' => $this->page];
        return view('admin.role.add', $result);
    }



    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles|max:30',
        ]);

        $role = new Role();
        $role->name = $request->get('name');
        $role->guard_name = 'web';
        if ($role->save()) {
            $role->givePermissionTo($request->permission_id);
            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been created succesfully', 'redirect_url' => route('role.index')]);
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

            $response = $response->offset($start)->limit($limit)->get();
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
                $row['created_at'] = $value->created_at;
                $edit = editAction($this->page . '.edit', ['id' => $value->id]);
                $row['actions'] = createAction($edit);
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

        $data = ['title' => ucfirst($this->page . ' Index'), 'page' => $this->page];
        return view('admin.' . $this->page . '.index', $data);
    }



    public function getData($search = null, $orderby = null, $order = null)
    {
        $q = DB::table('roles');
        $orderby = $orderby ? $orderby : 'roles.created_at';
        $order = $order ? $order : 'desc';

        if ($search && !empty($search)) {
            $q->where(function ($query) use ($search) {
                $query->where('roles.name', 'LIKE', '%' . $search . '%');
            });
        }
        $response = $q->orderBy($orderby, $order);
        return $response;
    }



    public function edit($id)
    {
        $data =  Role::where('id', $id)->first();
        $role_permission =    $data->getAllPermissions()->pluck('id')->toArray();
        $all_permission = Permission::all();
        if (isset($data)) {
            $result = ['title' => 'Role Edit', 'data' => $data, 'role_permission' => $role_permission, 'all_permission' => $all_permission, 'page' => $this->page];
            return view('admin.' . $this->page . '.edit', $result);
        } else {
            return Redirect::back()->with('error', 'Record Not found');
        }
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        if (isset($id)) {
            Role::where('id', $id)->update(['name' => $request->name]);
            $role = Role::where('id', $id)->first();

            DB::table('role_has_permissions')->where('role_id', $role->id)->delete();

            $role->givePermissionTo($request->permission_id);

            return response()->json(['status' => true, 'message' => ucfirst($this->page) . ' has been Updated succesfully', 'redirect_url' => '']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong.', 'redirect_url' => back()]);
        }
    }
}
