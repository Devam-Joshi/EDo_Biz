<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewInquiry extends Model
{

    use HasFactory;
	protected $table ='tbl_new_inquery';
    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = [];

	public function state()
    {
 		return $this->belongsTo(State::class, 'state_id');
    }

    public function newInqDetails()
    {
		return $this->hasMany(NewInquiryDetail::class, 'order_id');
    }
  
}
