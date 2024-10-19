<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table ='tbl_financial_logs';

    public function accData(){
      return $this->belongsTo(Account::class,'party_id');
    }

    public function account(){
      return $this->belongsTo(Account::class,'party_id');
    }
    public function payaccount(){
      return $this->belongsTo(Account::class,'payment_bank_id','id');
  }
}
