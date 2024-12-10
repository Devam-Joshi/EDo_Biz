<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{

    use HasFactory;protected $table ='tbl_employees';
    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = [];
	
    public function assignInquery()
	{    
		return $this->hasMany('App\InquerySale', 'salesman_id','id')->where('billing_status','0');
    }
    
  
}
