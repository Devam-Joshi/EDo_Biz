<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

    use HasFactory;protected $table ='tbl_account';
    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = [];
    
    public function acGroupData(){
		return $this->belongsTo(AccountGroup::class, 'acGroup');
	}

	public function currentbalance(){
		return app('App\Http\Controllers\AjaxController')->partyCalculateClosing($this->id);
	}

	public function citydata(){
		return $this->belongsTo(City::class, 'city_id');
	}

	public function statedata(){
		return $this->belongsTo(State::class, 'state_id');
	}

	public function updateBy(){
		return $this->belongsTo(UserModel::class, 'user_id')->select('id','name');
	}

	public function blockBy(){
		return $this->belongsTo(UserModel::class, 'block_by')->select('id','name');
	}
    
  
}
