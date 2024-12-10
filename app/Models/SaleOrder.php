<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
     protected $table ='tbl_sale_order';
	
	public function account()
    {
		return $this->belongsTo(Account::class, 'account_id');
    }


    public function saleDetails()
    {
		return $this->hasMany(SaleOrderDetail::class, 'order_id');
    }

    public function salesman()
    {
        return $this->belongsTo(Employee::class, 'salesman_id')->select('id','name');
    }
}
