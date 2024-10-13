<?php

namespace App;

use App\Mail\General\User_Password_Reset_Mail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class User extends Authenticatable
{
    use SoftDeletes;

    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [];

    public static function AddTokenToUser()
    {
        $user = Auth::user();
        $token = token_generator();
        $device_id = request('device_id');
        DeviceToken::where('device_id', $device_id)->delete();
        $user->login_tokens()->create([
            'token' => $token,
            'type' => request('device_type'),
            'device_id' => $device_id,
            'push_token' => request('push_token'),
        ]);
        return $token;
    }

    public function login_tokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public static function password_reset($email = "", $flash = true)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            if ($user->status == "active") {
                $user->update([
                    'reset_token' => genUniqueStr('', 30, 'users', 'reset_token', true)
                ]);
                Mail::to($user->email)->send(new User_Password_Reset_Mail($user));
                if ($flash) {
                    success_session('Email sent Successfully');
                } else {
                    return ['status' => true, 'message' => 'Email sent Successfully'];
                }
            } else {
                if ($flash) {
                    error_session('User account disabled by administrator');
                } else {
                    return ['status' => false, 'message' => 'Email sent Successfully'];
                }

            }
        } else {
            if ($flash) {
                error_session(__('api.err_email_not_exits'));
            } else {
                return ['status' => false, 'message' => __('api.err_email_not_exits')];
            }
        }
    }

    public function scopeSimpleDetails($query)
    {
        return $query->select(['id', 'name', 'first_name', 'last_name', 'profile_image']);
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function getProfileImageAttribute($val)
    {
        return get_asset($val, false, get_constants('default.user_image'));
    }

    public function scopeAdminSearch($query, $search)
    {
        // $query->where('mobile', 'like', "%$search%")
        //     ->orWhere('country_code', 'like', "%$search%")
        //     ->orWhere('email', 'like', "%$search%")
        //     ->orWhere('name', 'like', "%$search%")
        //     ->orWhere('username', 'like', "%$search%");

            $query->Where('email', 'like', "%$search%")
            ->orWhere('name', 'like', "%$search%")
            ->orWhere('username', 'like', "%$search%");
    }


}
