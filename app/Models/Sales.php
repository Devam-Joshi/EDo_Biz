<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table ='tbl_sale';
	
    public function saleDetails()
    {
	    return $this->hasMany(SalesDetail::class, 'order_id');
    }

    public function salesman()
    {
        return $this->belongsTo(Employee::class, 'salesman_id')->select('id','name');
    }

    public function account(){
        return $this->belongsTo(Account::class, 'account_id')->select('id','name','city_id','state_id');
    }
    public function gift()
    {
        return $this->belongsTo(GiftModel::class, 'gift_id');
    }

}
