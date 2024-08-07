<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    protected $page = 'permission';

    public function __construct(Request $request)
    {
        $this->model = new Permission();
        $this->sortableColumns = ['id', 'name', 'created_at'];
        //  $this->middleware('permission:Role Index', ['only' => ['index']]);
    }

    public function form(Request $request)
    {
        $result = ['title' => ucfirst('Add New Permission')];
        return view('admin.'. $this->page .'.form', $result);
    }
    
    public function manage(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|unique:roles|max:30',
            'module_id' => 'required',
        ]);

        $data = new Permission();
        $data->name = $request->get('name');
        $data->module_id = $request->get('module_id');
        $data->guard_name = 'web';
        if ($data->save()) {
            return response()->json(['status'=> true, 'message' => ucfirst($this->page) . ' has been created succesfully']);
        } else {
            return response()->json(['status'=>false,  'message' => 'Something went wrong']);
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

                $btn_group =  "<div class='btn-group' role='group' aria-label='Basic example'>
                <button type='button' data_value='$value->id' class='btn btn-primary model_open'><i class='bx bx-edit'></i></button>
              </div>";

                $row['actions'] = $btn_group;
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
        $data = ['title' => ucfirst($this->page . ' Index'), 'page' => $this->page ];
        return view('admin.' . $this->page . '.index', $data);
    }

    public function getData($search = null, $orderby = null, $order = null)
    {
        $q = DB::table('permissions');
        $orderby = $orderby ? $orderby : 'roles.created_at';
        $order = $order ? $order : 'desc';

        if ($search && !empty($search)) {
            $q->where(function ($query) use ($search) {
                $query->where('permissions.name', 'LIKE', '%' . $search . '%');
            });
        }
        $response = $q->orderBy($orderby, $order);
        return $response;
    }



    public function edit($id)
    {
        $user = Role::with('permissions')->where('id', $id)->first();
        $permission = Permission::all();
        $permissionArray = DB::table('role_has_permissions')->where('role_id', $id)->pluck('permission_id')->toArray();

        if (isset($user) && $user != null) {
            $data = ['title' => 'User Edit', 'user' => $user, 'permission' => $permission, 'permissionArrayData' => $permissionArray, 'page' => $this->page];
            return view('admin.' . $this->page . '.edit', $data);
        } else {
            return Redirect::back()->with('error', 'User Not found');
        }
    }

    public function update(Request $request, $id)
    {
        if (isset($id) && $id != null) {
            Role::where('id', $id)->update(['name' => $request->name]);
            $role = Role::where('id', $id)->first();

            DB::table('role_has_permissions')->where('role_id', $role->id)->delete();

            $role->givePermissionTo($request->permission);
            return Redirect::route('role.index')->with('success', 'Role has been updated succesfully');
        } else {
            return Redirect::back()->with('error', 'User Not found');
        }
    }
}
