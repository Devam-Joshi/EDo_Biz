<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
  protected $table ='tbl_transfer_logs';

  public function payerData(){
    return $this->belongsTo(Account::class,'payer_party_id');
  }

  public function receiverData(){
    return $this->belongsTo(Account::class,'receiver_party_id');
  }
  
  public function createdBy(){
    return $this->belongsTo(User::class,'user_id');
  }
}
