<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use App\Models\FinancialLogsModel;
use App\Models\Expense;
use App\Models\SaleModel;
use App\Models\Account;
use Auth;
use DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $r)
    {
      $a['fromDate']=date('Y-m-d',strtotime('-1 month'));
      $a['toDate']=date('Y-m-d');
      $a['AccountID']='';
      $a['allAccount']=Account::get();
      $a['repTitle']='';
	  $a['title'] ='Financial Transaction';
      $a['breadcrumb'] =breadcrumb(['Branch' => route('admin.payment.index'),]);

      $txn=FinancialLogsModel::with('accData','payaccount')        
            ->leftjoin('users AS u', function($join){$join->on('u.id', '=', 'tbl_financial_logs.user_id');})
            ->whereIn('reference_type',['receipt','payment','expenses'])
            ->orderBy('id','desc')
            ->groupBy('reference_no');

        if(!empty($r->fromDate!='') && !empty($r->toDate!=''))
        {
          $a['fromDate']=date('Y-m-d',strtotime($r->fromDate));
          $a['toDate']=date('Y-m-d',strtotime($r->toDate));
        }

        $txn->where('txn_date','>=',$a['fromDate']);
        $txn->where('txn_date','<=',$a['toDate']);
        $a['repTitle'].='['.$a['fromDate'].' | '.$a['toDate'].']';
    		//=======filter by Account/Supplier =======
    		if($r->AccountID!='' && $r->AccountID!='*')
    		{
    			$txn->where('party_id',$r->AccountID);
    			$a['AccountID']=$r->AccountID;
                $ac=Account::where('id',$r->AccountID)->first();
                $a['repTitle'].='[ '.$ac->name.' ]';
    		}

      $a['txn']=$txn->select('tbl_financial_logs.*','u.name as user_name')->get();
        return view('admin.financial.index')->with($a);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $a['bank'] = Account::latest()->whereIn('acGroup',[1,2,8])->where('status','1')->get();
		$a['payment'] =new FinancialLogsModel();
        $a['nextBill']=getNewSerialNo('payment_receipt');
        $a['title'] ='Financial Transaction';
        $a['breadcrumb'] =breadcrumb(['Branch' => route('admin.payment.index'),]);

        return view('admin.financial.addEditPayment')->with($a);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
