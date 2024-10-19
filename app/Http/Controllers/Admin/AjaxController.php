<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;

use App\Models\Account;
class AjaxController extends Controller
{
    //=====01===Party/Account/Ledger list===============
    public function AccountList($group,$name){
        if($group=='*' || $group=='null'){
              $Acc=Account::where('name','LIKE','%'.$name.'%')->orWhere('acCode','LIKE','%'.$name.'%')->get();
        }else{
          if($group=='sc'){
              $Acc=Account::whereIn('acGroup',(['3','4']))
              ->where(function($q) use ($name) {
                      $q->where('name','LIKE','%'.$name.'%')
                            ->orWhere('acCode','LIKE','%'.$name.'%');
                      })
                  ->where('status',1)->get();
          }else{
              $Acc=Account::where('name','LIKE','%'.$name.'%')->orWhere('acCode','LIKE','%'.$name.'%')->get();		
          }
          
        }
            return Response::json($Acc);
      }

    //=====02====Account/Ledger/Party Details===========
    public function AccountDetail($id)
    {
      $acData=Account::where('id',$id)->first();
	  $currentBalance=partyCalculateClosing($id,'','');
	  $acData['current_balance']=$currentBalance['closing'];
      $acData['showbalance']=$currentBalance['showbalance'];
  		return Response::json($acData);

    }

}
