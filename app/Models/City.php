<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
	protected $table = 'tbl_city';


	public function state(){
		return $this->belongsTo(State::class,'state_id','id');
	   }
}
