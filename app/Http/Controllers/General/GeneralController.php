<?php

namespace App\Http\Controllers\General;

use App\GeneralSettings;
use App\Http\Controllers\WebController;
use App\Http\Requests\Admin\General\PasswordUpdateRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GeneralController extends WebController
{
    public function Panel_Login()
    {
        return view('general.login', [
            'header_panel' => false,
            'title' => __('admin.lbl_login'),
        ]);
    }

    public function login(Request $request)
    {
        $remember = ($request->remember) ? true : false;
        $request->validate(['username' => 'required', 'password' => 'required']);
        $find_field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? "email" : "username";
        $creds = [$find_field => $request->username, 'password' => $request->password, 'status' => 'active'];
        if (Auth::attempt($creds, $remember)) {
            return redirect()->route(getDashboardRouteName());
        } else {
            if ($find_field == "username") {
                flash_session('error', 'Please enter valid username or password');
            } else {
                flash_session('error', 'Please enter valid email or password');
            }
        }
        return redirect()->back();
    }

    public function Panel_Pass_Forget()
    {
        return view('general.password_reset', [
            'header_panel' => false,
            'title' => __('admin.lbl_forgot_password'),
        ]);
    }

    public function ForgetPassword(Request $request)
    {
        User::password_reset($request->email);
        return redirect()->back();
    }


    public function totalusers(){
        $users = User::select(DB::raw('date(created_at) as userdate'),'created_at',DB::raw('count(id) as totaluser'))->where('type','user')->groupBy('userdate')->get();

        $maindata = [];
        // echo $current_date_time = Carbon::now()->toDateTimeString();

        // die;


        if(count($users) > 0){
             foreach( $users as $user) {

                 $detail=  [];
                  $detail[]= strtotime($user->userdate.' 23:00:00') * 1000;
                  $detail[]= $user->totaluser;
                 $maindata[] = $detail;
             }
        }
         return $maindata;
    }


    public function Admin_dashboard(Request $request)
    {
        $user_data = $request->user();
        $user_selected_country = $user_data->country;
        return view('admin.general.dashboard', [
            'title' => __('admin.lbl_dashboard'),
            'user_count' => User::where(['type' => 'user'])->count(),
        ]);
    }


    public function get_update_password(Request $request)
    {
        $title = 'Change Password';
        $user_data = $request->user();
        $view = ($user_data->type == "vendor") ? 'vendor.general.update_password' : 'admin.general.update_password';
        return view($view, [
            'title' => $title,
            'breadcrumb' => breadcrumb([
                $title => route('admin.get_update_password'),
            ]),
        ]);
    }

    public function get_site_settings()
    {
        $title = 'Site setting';
        return view('admin.general.site_settings', [
            'title' => $title,
            'fields' => GeneralSettings::where('status', 'active')->orderBy('order_number', 'DESC')->get(),
            'breadcrumb' => breadcrumb([
                $title => route('admin.get_site_settings'),
            ]),
        ]);
    }

    public function site_settings(Request $request)
    {
        $all_req = $request->except('_token');
        foreach ($all_req as $key => $value) {
            $setting = GeneralSettings::find($key);
            if ($request->hasFile($key)) {
                $up = $this->upload_file($key, 'admin_upload');
                if ($up) {
                    un_link_file($setting->value);
                    $setting->update(['value' => $up]);
                }
            } else {
                $setting->update(['value' => $value]);
            }
        }
        success_session(__('admin.site_setting_updated'));
        return redirect()->route('admin.get_site_settings');
    }

    public function update_password(PasswordUpdateRequest $request)
    {
        $request->update_pass();
        return redirect()->back();
    }

    public function get_profile(Request $request)
    {
        $user_data = $request->user();
        $view = ($user_data->type == "vendor") ? 'vendor.general.profile' : 'general.profile';
        return view($view, [
            'title' => 'Profile',
            'user' => $user_data,
            'breadcrumb' => breadcrumb([
                'Profile' => route('admin.profile'),
            ])
        ]);
    }

    public function logout()
    {
        $name = getDashboardRouteName();
        Auth::logout();
        return redirect()->route($name);
    }

    public function availability_checker(Request $request)
    {
        $count = 0;
        $type = $request->type;
        $val = $request->val;
        $user_id = Auth::id() ?? 0;
        if ($type == "username" || $type == "email") {
            $count = User::where($type, $val)->where('id', '!=', $user_id)->count();
        }
        return $count ? "false" : "true";
    }

    public function user_availability_checker(Request $request)
    {
        $id = $request->id ?? 0;
        $query = User::where('id', '!=', $id);
        if ($request->username) {
            $query = $query->where('username', $request->username);
        } elseif ($request->email) {
            $query = $query->where('email', $request->email);
        } elseif ($request->number && $request->country_code) {
            $query = $query->where(['mobile' => $request->number, 'country_code' => $request->country_code]);
        } else {
            return 'false';
        }

        return $query->count() ? "false" : "true";
    }

    public function post_profile(Request $request)
    {
        $user_data = $request->user();
        $rules = [
            'profile_image' => ['file', 'image'], 
            'name' => ['required', 'max:255'],
            'username' => ['required', 'max:255', Rule::unique('users')->ignore($user_data->id)->whereNull('deleted_at')],
            'email' => ['required', 'max:255', Rule::unique('users')->ignore($user_data->id)->whereNull('deleted_at')],
        ];
        $req = $request->validate($rules);

       

        $user_data->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ]);
        $profile_image = $user_data->getRawOriginal('profile_image');

        
        if ($request->hasFile('profile_image')) {
            $up = upload_file('profile_image', 'user_profile_image');
            if ($up) {
                un_link_file($profile_image);
                $profile_image = $up;
                $user_data->update([
                    'profile_image' => $profile_image,
                ]);
            }
        }
        success_session('Profile Updated successfully');
        return redirect()->back();
    }

    public function forgot_password_view($token)
    {
//        $user = User::where(['status' => 'active', 'reset_token' => $token])->first();
//        if ($user) {
        return view('general.reset_password', [
            'token' => $token,
            'header_panel' => false,
            'title' => 'Password reset',
        ]);
//        }
//        error_session('Invalid password token');
//        return redirect()->route('admin.login');
    }

    public function forgot_password_post(Request $request)
    {
        $request->validate([
            'reset_token' => ['required', Rule::exists('users', 'reset_token')->whereNull('deleted_at')],
            'password' => ['required'],
        ], [
            'reset_token.exists' => 'Invalid password token',
        ]);
        $user = User::where('reset_token', $request->reset_token)->first();
        $user->update(['reset_token' => null, 'password' => $request->password]);
        success_session('Password Updated successfully');
        return redirect()->back();
    }

    public function ClearCache()
    {
        Artisan::call('optimize:clear');
        return "Cleared!";
    }


}
