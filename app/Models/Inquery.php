<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquery extends Model
{

    use HasFactory;
	protected $table ='tbl_sale_inquery';
    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = [];

	public function account()
    {
 		return $this->belongsTo(Account::class, 'account_id');
    }

    public function inqueryDetails()
    {
		return $this->hasMany(InqueryDetail::class, 'order_id');
    }
  
}
