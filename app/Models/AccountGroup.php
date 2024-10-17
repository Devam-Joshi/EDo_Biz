<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    protected $table = 'tbl_account_group';

    public function account(){
    		return $this->hasMany(Account::class,'acGroup');
    }

    // public function parent(){
    //     return $this->belongTo(AccountGroup::class, 'parent_id');
    // }

    public function child(){
        return $this->hasMany(AccountGroup::class, 'parent_id');
    }



}
