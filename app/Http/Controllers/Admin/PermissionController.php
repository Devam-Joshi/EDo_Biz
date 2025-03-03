<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;

class PermissionController extends WebController
{

    public $perm_obj;
    public function __construct()
    {
        $this->perm_obj = new Permission();
    }
    public function index()
    {
        return view('admin.permission.index', [
            'title' => 'Permission',
            'breadcrumb' => breadcrumb([
                'Permission' => route('admin.permission.index'),
            ]),
        ]);
    }

    public function create()
    {
        $categories = $this->perm_obj->where('parent_id', null)->get();
        return view('admin.permission.create', [
            'title' => "Create Permission",
            'categories' => $categories,
            'breadcrumb' => breadcrumb([
                'Permission' => route('admin.permission.index')
            ]),
        ]);
    }


    public function listing(Request $request)
    {
        $data = $this->perm_obj::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $param = [
                    'id' => $row->id,
                    'url' => [
                        'delete' => route('admin.permission.destroy', $row->id),
                        'edit' => route('admin.permission.edit', $row->id),
                        // 'view' => route('admin.news.show', $row->id),
                    ]
                ];
                return $this->generate_actions_buttons($param);
            })
            ->rawColumns(["status", "action"])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'main_name' => 'nullable|string|max:255',
            'child_name' => 'nullable|string|max:255'
        ]);
        if ($request->has('main_name') && $request->main_name !== null) {
            $parentPermission = Permission::create([
                'name' => $request->main_name,
                'parent_id' => null,
                'guard_name' => 'web',
            ]);
            $childPermissions = ['view', 'edit', 'delete', 'create'];
            foreach ($childPermissions as $action) {
                Permission::create([
                    'name' => "{$request->main_name}_{$action}",
                    'parent_id' => $parentPermission->id,
                    'guard_name' => 'web',
                ]);
            }
        }
        if ($request->has('child_name') && $request->child_name !== null) {
            if (!$request->has('parent_id')) {
                return redirect()->back()->withErrors(['message' => 'Parent Permission is required for Child Permission.']);
            }
            $parentPermission = Permission::find($request->parent_id);
            if ($parentPermission) {
                Permission::create([
                    'name' => $request->child_name,
                    'parent_id' => $request->parent_id,
                    'guard_name' => 'web',
                ]);
            } else {
                return redirect()->back()->withErrors(['message' => 'Selected Parent Permission does not exist.']);
            }
        }
        return redirect()->route('admin.permission.index')->with('success', 'Permission created successfully!');
    }


    public function edit($id)
    {
        $data = $this->perm_obj->find($id);
        $categories = $this->perm_obj->where('parent_id', null)->get();
        if (isset($data) && !empty($data)) {
            return view('admin.permission.create', [
                'title' => 'Category Update',
                'categories' => $categories,
                'breadcrumb' => breadcrumb([
                    'Category' => route('admin.permission.index'),
                    'edit' => route('admin.permission.edit', $id),
                ]),
            ])->with(compact('data'));
        }
        return redirect()->route('admin.permission.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'child_name' => ['required', 'max:255'],
        ]);
        $data = $this->perm_obj::find($id);
        if (isset($data) && !empty($data)) {
            $data->name = $request->child_name;
            $data->save();

            success_session('Permission updated successfully');
        } else {
            error_session('Permission not found');
        }
        return redirect()->route('admin.permission.index');
    }

    public function destroy($id)
    {
        $data = $this->perm_obj::where('id', $id)->delete();
        if ($data) {
            success_session('Permission deleted successfully');
        } else {
            error_session('Permission not found');
        }
        return redirect()->route('admin.permission.index');
    }
}
