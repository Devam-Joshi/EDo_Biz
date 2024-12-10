<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
	protected $table = 'tbl_sale_detail';
	
	public function account(){
	    return $this->belongsTo(Account::class,'account_id','id');
	}
}
