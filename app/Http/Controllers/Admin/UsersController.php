<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\User;
use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends WebController
{
    public function index()
    {
        return view('admin.user.index', [
            'title' => 'Users',
            'breadcrumb' => breadcrumb([
                'Users' => route('admin.user.index'),
            ]),
        ]);
    }

    public function listing()
    {
        $datatable_filter = datatable_filters();
        $offset = $datatable_filter['offset'];
        $search = $datatable_filter['search'];
        $return_data = array(
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0
        );
        $main = User::where('type', 'user');
        $return_data['recordsTotal'] = $main->count();
        if (!empty($search)) {
            $main->where(function ($query) use ($search) {
                $query->AdminSearch($search);
            });
        }
        $return_data['recordsFiltered'] = $main->count();
        $all_data = $main->orderBy($datatable_filter['sort'], $datatable_filter['order'])
            ->offset($offset)
            ->limit($datatable_filter['limit'])
            ->get();
        if (!empty($all_data)) {
            foreach ($all_data as $key => $value) {
                $param = [
                    'id' => $value->id,
                    'url' => [
                        'status' => route('admin.user.listing', $value->id),
                        'edit' => route('admin.user.edit', $value->id),
                        'delete' => route('admin.user.destroy', $value->id),
                        //'view' => route('admin.user.show', $value->id),
                    ],
                    'checked' => ($value->status == 'active') ? 'checked' : ''
                ];
                $return_data['data'][] = array(
                    'id' => $offset + $key + 1,
                    'profile_image' => get_fancy_box_html($value['profile_image']),
                    'name' => $value->name,
                    'email' => $value->email,
                    'mobile_number' => $value->country_code . ' ' . $value->mobile,
                    'status' => $this->generate_switch($param),
                    'action' => $this->generate_actions_buttons($param),
                );
            }
        }
        return $return_data;
    }


    public function destroy($id)
    {
        $data = User::where('id', $id)->first();
        if ($data) {
            $data->delete();
            success_session('User Deleted successfully');
        } else {
            error_session('User not found');
        }
        return redirect()->route('admin.user.index');
    }

    public function status_update($id = 0)
    {
        $data = ['status' => 0, 'message' => 'User Not Found'];
        $find = User::find($id);
        if ($find) {
            $find->update(['status' => ($find->status == "inactive") ? "active" : "inactive"]);
            $data['status'] = 1;
            $data['message'] = 'User status updated';
        }
        return $data;
    }

    public function show($id)
    {
        $data = User::where(['type' => 'user', 'id' => $id])->first();
        if ($data) {
            return view('admin.user.view', [
                'title' => 'View user',
                'data' => $data,
                'breadcrumb' => breadcrumb([
                    'user' => route('admin.user.index'),
                    'view' => route('admin.user.show', $id)
                ]),
            ]);
        }
        error_session('user not found');
        return redirect()->route('admin.user.index');
    }


    public function edit($id)
    {
        $data = User::find($id);
        $groupedPermissions = Permission::all()->groupBy(function ($permission) {
            return explode('_', $permission->name)[0]; // Extract category from permission name (first part before '_')
        });
        $userPermissions = $data->permissions->pluck('name')->toArray();
        if ($data) {
            $title = "Update user";
            return view('admin.user.edit', [
                'title' => $title,
                'groupedPermissions' => $groupedPermissions,
                'userPermissions' => $userPermissions,
                'data' => $data,
                'breadcrumb' => breadcrumb([
                    'User' => route('admin.user.index'),
                    'edit' => route('admin.user.edit', $data->id)
                ]),
            ]);
        }
        error_session('user not found');
        return redirect()->route('admin.user.index');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Handle the profile image update (if uploaded)
        if ($request->hasFile('profile_image')) {
            $profile_image = $request->profile_image;
            $up = upload_file('profile_image', 'user_profile_image');
            if ($up) {
                un_link_file($profile_image); // Remove old profile image if necessary
                $profile_image = $up; // Assign the new profile image path
            }
            $user->profile_image = $profile_image;
        }

        // Update the user data
        $user->update([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
        ]);

        // Sync permissions (similar to how it's done in create)
        if ($request->filled('permissions')) {
            $permissionIds = \Spatie\Permission\Models\Permission::whereIn('name', $request->input('permissions'))->pluck('id');
            $user->syncPermissions($permissionIds);
        }

        return redirect()->route('admin.user.index')->with('success', 'User updated successfully!');
    }



    public function create()
    {
        $permissions = Permission::all(); // Fetch all permissions
        $branches = Branch::all();
        $roles = Role::all();
        $groupedPermissions = Permission::all()->groupBy(function ($permission) {
            return explode('_', $permission->name)[0]; // Extract category from permission name (first part before '_')
        });

        return view('admin.user.create', compact('groupedPermissions', 'roles', 'branches'));
    }

    public function save(Request $request)
    {
        // Handle file upload if there's a profile image
        if ($request->hasFile('profile_image')) {
            $profile_image = $request->profile_image;
            $up = upload_file('profile_image', 'user_profile_image');
            if ($up) {
                un_link_file($profile_image); // Remove old profile image if necessary
                $profile_image = $up; // Assign the new profile image path
            }
        }

        // Create the user
        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'password' => bcrypt($request->input('password')), // Hash password
            'email' => $request->input('email'),
            'profile_image' => $profile_image ?? "", // If no image, leave empty
            'status' => $request->input('statusData'), // Active or inactive status
            'branch_id' => $request->input('branch'), // Assign the selected branch
            'type' => 'user', // Admin or User type
        ]);

        // Assign the role to the user using Spatie's helper method
        if ($request->filled('role')) {
            $role = Role::findById($request->input('role')); // Fetch the selected role by ID
            if ($role) {
                $user->assignRole($role); // Assign the role to the user
            }
        }

        // Sync permissions using Spatie's helper method
        if ($request->filled('permissions')) {
            // Fetch permission IDs based on the permission names from the request
            $permissionIds = \Spatie\Permission\Models\Permission::whereIn('name', $request->input('permissions'))->pluck('id');

            // Sync the permissions by their IDs
            $user->syncPermissions($permissionIds);
        }

        // Return success message after creating the user
        return redirect()->route('admin.user.index')->with('success', 'User created successfully!');
    }
}
