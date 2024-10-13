<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = ['provider', 'provider_id'];


    public function user()
    {
        return $this->belongsTo(User::class)->where('type', 'user');
    }
}
