<?php

namespace App\Http\Controllers\Admin;
use App\Models\Expense;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DB;
use App\Models\SerialNo;
use App\Models\SaleModel;
use App\Models\FinancialLogsModel;
use App\Models\Account;
use Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $acObj;
    public function __construct()
    {
        $this->flObj = new FinancialLogsModel();
    }


    public function index(Request $r)
    {
      $a['fromDate']=date('Y-m-d',strtotime('-1 month'));
      $a['toDate']=date('Y-m-d');
      $a['AccountID']='';
      $a['allAccount']=Account::get();
      $a['repTitle']='';
	    
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
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $a['bank'] = Account::latest()->whereIn('acGroup',[1,2,8])->where('status','1')->get();
		$a['payment'] =new FinancialLogsModel();
        $a['nextBill']=$this->NewbillNo;
        return view('admin.financial.addEditPayment')->with($a);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->except('_token');
        $rules = [
          'invoice_No' => 'required',
          'txn_date' => 'required',
          'supplier_id' => 'required',
          'txn_amount'=>'required'
        ];

		//print_r($_POST);
		//die();

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $payType=$request->input('paymentType');
        $Acc=Account::where('id',$request->input('supplier_id'))->select('current_balance')->get();
		$BnkAc=Account::where('id',$request->input('bankAcID'))->select('current_balance')->get();
        if($payType=='receipt'){
          $balance=($Acc[0]['current_balance']-$request->input('txn_amount'));
		  $Bankbalance=($BnkAc[0]['current_balance']+$request->input('txn_amount'));
		  $txnType='debit';
		  $txnBankType='credit';
        }else{
          $balance=($Acc[0]['current_balance']+$request->input('txn_amount'));
		  $Bankbalance=($BnkAc[0]['current_balance']-$request->input('txn_amount'));
		  $txnType='credit';
		  $txnBankType='debit';
        }


        $date = Carbon::now();
		$sNo=$this->NewbillNo;

        $fLogs=new FinancialLogsModel();
        $fLogs->txn_date=date("Y-m-d",strtotime($request->input('txn_date')));
        $fLogs->reference_id = '';
        $fLogs->reference_no=$sNo;
        $fLogs->txn_method=$request->input('txn_method');
        $fLogs->reference_type =$payType;
        $fLogs->txn_type=$txnType;
        $fLogs->txn_amount = $request->input('txn_amount');
        $fLogs->party_id=$request->input('supplier_id');
		$fLogs->payment_bank_id=$request->input('bankAcID');
		$fLogs->payment_referrence_no=$request->input('payment_referrence_no');
        $fLogs->party_prevBal = $Acc[0]['current_balance'];
        $fLogs->party_currentBal = $balance;
        $fLogs->remark =$request->input('remark');
        $fLogs->user_id =Auth::user()->id;
        $fLogs->save();

        $LastInsertedId = $fLogs->id;
        if($LastInsertedId){
			$fLogs=new FinancialLogsModel();
			$fLogs->txn_date=date("Y-m-d",strtotime($request->input('txn_date')));
			$fLogs->reference_id =$LastInsertedId;
			$fLogs->reference_no=$sNo;
			$fLogs->txn_method=$request->input('txn_method');
			$fLogs->reference_type =$payType;
			$fLogs->txn_type=$txnBankType;
            $fLogs->txn_amount = $request->input('txn_amount');
            //$fLogs->payment_bank_id=$request->input('bankAcID');
            $fLogs->payment_bank_id=$request->input('supplier_id');
            $fLogs->party_id=$request->input('bankAcID');
			$fLogs->payment_referrence_no=$request->input('payment_referrence_no');
			$fLogs->party_prevBal = $BnkAc[0]['current_balance'];
			$fLogs->party_currentBal = $Bankbalance;
			$fLogs->remark =$request->input('remark');
            $fLogs->user_id =Auth::user()->id;
			$fLogs->save();

          //======Update party account======
          Account::where('id','=',$request->input('supplier_id'))->decrement('current_balance',$request->input('txn_amount'));
		  $sn=SerialNo::where('name','=','payment_receipt')->increment('next_number',1);
        }

        Toastr::success('Payment done successfully', 'Success');
        return redirect()->back();
    }


    public function edit($id)
    {
      $a['bank'] = Account::latest()->whereIn('acGroup',[1,2,8])->get();
	    $a['payment'] =FinancialLogsModel::latest()->where('id',$id)->with('accData')->first();
      $a['nextBill']=$a['payment']->reference_no;
      return view('admin.financial.addEditPayment')->with($a);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $inputs = $request->except('_token');
        $rules = [
          'invoice_No' => 'required',
          'txn_date' => 'required',
          'supplier_id' => 'required',
          'txn_amount'=>'required'
        ];


        $validator = Validator::make($inputs, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

		$payType=$request->input('paymentType');
		if($payType=='receipt'){
          $txnType='debit';
		  $txnBankType='credit';
        }else{
          $txnType='credit';
		  $txnBankType='debit';
        }

        $fLogs=FinancialLogsModel::find($id);
        $fLogs->txn_date=date("Y-m-d",strtotime($request->input('txn_date')));
        $fLogs->reference_id = '';
        $fLogs->reference_no=$request->input('invoice_No');
        $fLogs->txn_method=$request->input('txn_method');
        $fLogs->reference_type =$payType;
        $fLogs->txn_type=$txnType;
        $fLogs->txn_amount = $request->input('txn_amount');
        $fLogs->party_id=$request->input('supplier_id');
		$fLogs->payment_bank_id=$request->input('bankAcID');
		$fLogs->payment_referrence_no=$request->input('payment_referrence_no');
        //$fLogs->party_prevBal = $Acc[0]['current_balance'];
        //$fLogs->party_currentBal = $balance;
        $fLogs->remark =$request->input('remark');
        $fLogs->user_id =Auth::user()->id;
        $fLogs->save();


			$fLogs=FinancialLogsModel::where('reference_id', '=', $id)->firstOrFail();
			$fLogs->txn_date=date("Y-m-d",strtotime($request->input('txn_date')));
			$fLogs->txn_method=$request->input('txn_method');
			$fLogs->reference_type =$payType;
			$fLogs->txn_type=$txnBankType;
			$fLogs->txn_amount = $request->input('txn_amount');
			$fLogs->party_id=$request->input('bankAcID');
			$fLogs->payment_bank_id=$request->input('supplier_id');
			$fLogs->payment_referrence_no=$request->input('payment_referrence_no');
			//$fLogs->party_prevBal = $BnkAc[0]['current_balance'];
			//$fLogs->party_currentBal = $Bankbalance;
			$fLogs->remark =$request->input('remark');
            $fLogs->user_id =Auth::user()->id;
			$fLogs->save();


  		Toastr::success('Payment updated successfully', 'Success');
         return redirect()->back();

        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */

	//===========Delete Financial Transaction============
    public function deleteFinancialBill($action,$id){
		$status='false';
		$code='0';
		$msg='';
		$tblD='';

		if($action=='payment' || $action=='receipt' || $action=='expenses'){
			/*====Delete 1st entery for Payment===*/
			$det1=FinancialLogsModel::where('id',$id)->where('reference_type',$action)->delete();
			/*====Delete 2nd entery for Payment===*/
			$det2=FinancialLogsModel::where('reference_id',$id)->where('reference_type',$action)->delete();

			$msg=$action.' deleted successfully ';
    		$status='true';
			$code=101;
			Toastr::success('Bill Deleted Successfully', 'Success');
			return redirect()->back();
		}else if($action=='transfer'){
			/*====Delete Both Transfer entery ===*/
			$det1=FinancialLogsModel::where('reference_id',$id)->where('reference_type','transfer')->delete();
			DB::table('tbl_transfer_logs')->where('id',$id)->delete();
			$msg=$action.' deleted successfully ';
    		$status='true';
			$code=101;
			Toastr::success('Bill Deleted Successfully', 'Success');
			return redirect()->back();
		}else{}

		$data['code']=$code;
		$data['message']=$msg;
		$data['status']=$status;

		 Toastr::success('Un-able to delete the bill', 'Danger');
        return redirect()->back();
		//return Response::json($data);
	}


    public function destroy($id)
    {
		if(isset($id) && $id!=''){
        FinancialLogsModel::where('id', '=', $id)->delete();
		FinancialLogsModel::where('reference_id', '=', $id)->whereIn('reference_type', ['receipt','payment'])->delete();

			}
        Toastr::success('Payment deleted successfully', 'Success');
        return redirect()->back();
    }



    public function today_expense()
    {
        $today = date('Y-m-d');
        $expenses = Expense::latest()->where('date', $today)->get();
        return view('admin.expense.today', compact('expenses'));
    }

    public function month_expense($month = null)
    {
        if ($month == null)
        {
            $month = date('F');
        }
        $expenses = Expense::latest()->where('month', $month)->get();
        return view('admin.expense.month', compact('expenses', 'month'));
    }

    public function yearly_expense($year = null)
    {
        if ($year == null)
        {
            $year = date('Y');
        }
        $expenses = Expense::latest()->where('year', $year)->get();
        $years = Expense::select('year')->distinct()->take(12)->get();
        return view('admin.expense.year', compact('expenses', 'year', 'years'));
    }


}
