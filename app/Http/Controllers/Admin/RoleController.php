<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebController;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;

class RoleController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $role_obj;
    public function __construct()
    {
        $this->role_obj = new Role();
    }

    public function index()
    {
        return view('admin.role.index', [
            'title' => 'Role',
            'breadcrumb' => breadcrumb([
                'Role' => route('admin.role.index'),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groupedPermissions = Permission::all()->groupBy(function ($permission) {
            return explode('_', $permission->name)[0]; // Extract category from permission name
        });
        return view('admin.role.create', [
            'title' => "Create Role",
            'groupedPermissions' => $groupedPermissions,
            'breadcrumb' => breadcrumb([
                'Role' => route('admin.role.index')
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($request->statusData == 'active') {
            $status = 1;
        } else {
            $status = 0;
        }

        $role = Role::create(['name' => $request->name, 'status' => $status]);
        $role->syncPermissions($request->permissions); // Assign permissions by name

        return redirect()->route('admin.role.index')->with('success', 'Role created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::findById($id);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions); // Assign permissions by name

        return redirect()->route('admin.role.index')->with('success', 'Role updated successfully.');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->role_obj->find($id);
        $groupedPermissions = Permission::all()->groupBy(function ($permission) {
            return explode('_', $permission->name)[0]; // Extract category from permission name
        });
        if (isset($data) && !empty($data)) {
            return view('admin.role.create', [
                'title' => 'Category Update',
                'groupedPermissions' => $groupedPermissions,
                'breadcrumb' => breadcrumb([
                    'Category' => route('admin.role.index'),
                    'edit' => route('admin.role.edit', $id),
                ]),
            ])->with(compact('data'));
        }
        return redirect()->route('admin.role.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'name' => ['required', 'max:255'],
    //     ]);
    //     $categories = $this->role_obj->find($id);
    //     if(isset($categories) && !empty($categories)){
    //         $return_data = $request->all();
    //         $this->role_obj->saveRole($return_data,$id,$categories);
    //         success_session('Role updated successfully');
    //     }
    //     else{
    //         error_session('Role not found');
    //     }
    //     return redirect()->route('admin.role.index');
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->role_obj::where('id', $id)->delete();
        if ($data) {
            success_session('Role deleted successfully');
        } else {
            error_session('Role not found');
        }
        return redirect()->route('admin.role.index');
    }

    public function listing(Request $request)
    {
        $data = $this->role_obj::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $param = [
                    'id' => $row->id,
                    'url' => [
                        'status' => route('admin.role.status_update', $row->id),
                    ],
                    'checked' => ($row->status == 'active') ? 'checked' : ''
                ];
                return $this->generate_switch($param);
            })
            ->addColumn('action', function ($row) {
                $param = [
                    'id' => $row->id,
                    'url' => [
                        'delete' => route('admin.role.destroy', $row->id),
                        'edit' => route('admin.role.edit', $row->id),
                        // 'view' => route('admin.news.show', $row->id),
                    ]
                ];
                return $this->generate_actions_buttons($param);
            })
            ->rawColumns(["status", "action"])
            ->make(true);
    }


    public function status_update($id = 0)
    {
        $data = ['status' => 0, 'message' => 'Role Not Found'];
        $find = $this->role_obj->find($id);
        if ($find) {
            $find->update(['status' => ($find->status == "inactive") ? "active" : "inactive"]);
            $data['status'] = 1;
            $data['message'] = 'Role status updated';
        }
        return $data;
    }

    public function getPermissionsForRole(Request $request)
    {
        $roleId = $request->input('role_id');
        $role = Role::find($roleId);

        if ($role) {
            // Get the permissions associated with the selected role
            $permissions = $role->permissions->pluck('name')->toArray();
            return response()->json(['permissions' => $permissions]);
        }

        return response()->json(['permissions' => []]);
    }
}
