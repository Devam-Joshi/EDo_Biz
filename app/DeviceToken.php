<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $table = "device_tokens";

    protected $guarded = [];

    public static function get_user_tokens($user_id = 0)
    {
        return DeviceToken::where('user_id', $user_id)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
