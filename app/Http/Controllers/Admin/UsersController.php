<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\User;
use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Permission;
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
        if ($data) {
            $title = "Update user";
            return view('admin.user.edit', [
                'title' => $title,
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
        $data = User::find($id);
        if ($data) {

            $request->validate([
                'first_name' => ['required', 'max:255'],
                'last_name' => ['required', 'max:255'],
                //'country_code' => ['required'],
                //'mobile' => ['required', Rule::unique('users', 'mobile')->ignore($id)->where('country_code', $request->country_code)->whereNull('deleted_at')],
                'email' => ['required', 'email', Rule::unique('users')->ignore($id)->whereNull('deleted_at')],
                'profile_image' => ['file', 'image'],
            ]);
            $profile_image = $data->getRawOriginal('profile_image');
            if ($request->hasFile('profile_image')) {
                $up = upload_file('profile_image', 'user_profile_image');
                if ($up) {
                    un_link_file($profile_image);
                    $profile_image = $up;
                }
            }
            $userdata = [
                'email' => $request->email,
                'profile_image' => $profile_image,
                'name' => $request->first_name . ' ' . $request->last_name,
            ];
            $data->update($userdata);
            success_session('user updated successfully');
        } else {
            error_session('user not found');
        }
        return redirect()->route('admin.user.index');
    }


    public function create()
    {
        $permissions = Permission::all(); // Fetch all permissions
        $branches = Branch::all();
        return view('admin.user.create', compact('permissions','branches'));
    }

    public function save(Request $request)
    {

        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'username' => 'required|string|max:255|unique:users,username',
        //     'password' => 'required|string|min:6',
        //     'email' => 'required|email|max:255|unique:users,email',
        //     'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        //     'statusData' => 'required|in:active,inactive',
        //     'type' => 'required|in:admin,user',
        //     'branch' => 'required',
        //     'permissions' => 'array',
        //     'permissions.*' => 'exists:permissions,name',
        // ]);

        if ($request->hasFile('profile_image')) {
            $profile_image = $request->profile_image;
            $up = upload_file('profile_image', 'user_profile_image');
            if ($up) {
                un_link_file($profile_image);
                $profile_image = $up;
            }
        }
        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'password' => bcrypt($request->input('password')),
            'email' => $request->input('email'),
            'profile_image' => $profile_image ?? "",
            'status' => $request->input('statusData'),
            'branch_id' => $request->input('branch'),
            'type' => $request->input('type'),
        ]);


        // Sync permissions (using permission names)
        if ($request->filled('permissions')) {
            $user->syncPermissions($request->input('permissions'));
        }

        return redirect()->route('admin.user.index')->with('success', 'User created successfully!');
    }
}
