<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\Expense;
use App\Models\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\WebController;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
use Auth;
use session;
class PaymentInwardController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $payment_obj, $account_obj;
    public function __construct()
    {
        $this->payment_obj = new Payment();
        $this->account_obj = new Account();
    }

    public function index()
    {  
        $data=$this->payment_obj::where('reference_type', 'receipt')->where('reference_id','<',1)->with('accData')->latest()->get();
        return view('admin.payment.inward.index', [
            'title' => 'Inward Payment',
            'breadcrumb' => breadcrumb([
                'Inward Payment' => route('admin.payment.inward.index'),
            ]),
            'txn' =>$data,
        ]);
    }

    public function listing()
    {
        $datatable_filter = datatable_filters();
        $offset = $datatable_filter['offset'];
        $search = $datatable_filter['search'];
        $return_data = array(
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0
        );
        $main = Payment::where('reference_type', 'Payment')->with('accData');
        $return_data['recordsTotal'] = $main->count();
        if (!empty($search)) {
            $main->where(function ($query) use ($search) {
                $query->AdminSearch($search);
            });
        }
        $return_data['recordsFiltered'] = $main->count();
        $all_data = $main->orderBy($datatable_filter['sort'], $datatable_filter['order'])
            ->offset($offset)
            ->limit($datatable_filter['limit'])
            ->get();
        if (!empty($all_data)) {
            foreach ($all_data as $key => $value) {
                $param = [
                    'id' => $value->id,
                    'url' => [
                        'status' => route('admin.payment.inward.status_update', $value->id),
                        'edit' => route('admin.payment.inward.edit', $value->id),
                       // 'delete' => route('admin.user.destroy', $value->id),
                        //'view' => route('admin.user.show', $value->id),
                    ],
                    'checked' => ($value->status == '1') ? 'checked' : ''
                ];
                $return_data['data'][] = array(
                    'id' => $offset + $key + 1,
                    'billno' =>$value->reference_no,
                    'date' =>$value->txn_date,
                    'name' => $value->account->name,
                    'amount' => $value->txn_amount,
                    //'status' => $this->generate_switch($param),
                    'action' => $this->generate_actions_buttons($param),
                );
            }
        }
        return $return_data;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $a['allAccount'] = $this->account_obj->orderBy('name')->where('status', 'active')->get();
        $a['bank'] = $this->account_obj->orderBy('name')->where('status', 'active')->get();
        $a['bank'] = Account::latest()->whereIn('acGroup',[1,2,8])->where('status','1')->get();
		$a['payment'] =new Payment();
        $a['nextBill']=getNewSerialNo('payment_receipt');
        $a['title']='Add Inward Payment';
        $a['breadcrumb']=breadcrumb([
            'Payment Inward' => route('admin.payment.inward.index')
        ]);
        return view('admin.payment.inward.addEditPayment')->with($a);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $rd
     * @return \Illuminate\Http\Response
     */
    public function store(Request $rd)
    {
        $inputs = $rd->except('_token');
        $rules = [
          'txn_date' => 'required',
          'account_id' => 'required',
          'txn_amount'=>'required'
        ];

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }


    try {
           
            
            $Acc=$this->account_obj->where('id',$rd->input('account_id'))->select('current_balance')->first();
            $BnkAc=$this->account_obj->where('id',$rd->input('bankAcID'))->select('current_balance')->first();
        
            $balance=($Acc->current_balance - $rd->input('txn_amount'));
            $Bankbalance=($BnkAc->current_balance + $rd->input('txn_amount'));
            $payType='receipt';
            $txnType='debit';
            $txnBankType='credit';

            $date = Carbon::now();
            $sNo=getNewSerialNo('payment_receipt');

            $fLogs=$this->payment_obj;
            $fLogs->txn_date=general_date($rd->input('txn_date'));
            $fLogs->reference_id = '';
            $fLogs->reference_no=$sNo;
            $fLogs->txn_method=$rd->input('txn_method');
            $fLogs->reference_type =$payType;
            $fLogs->txn_type=$txnType;
            $fLogs->txn_amount = $rd->input('txn_amount');
            $fLogs->party_id=$rd->input('account_id');
            $fLogs->payment_bank_id=$rd->input('bankAcID');
            $fLogs->payment_referrence_no=$rd->input('payment_referrence_no');
            $fLogs->party_prevBal = $Acc->current_balance;
            $fLogs->party_currentBal = $balance;
            $fLogs->remark =$rd->input('remark');
            $fLogs->user_id =Auth::user()->id;

            if($fLogs->save())
            {   
                //===== Reversal Enter for Bank====
                $fRlog=new Payment;
                $fRlog->txn_date=general_date($rd->input('txn_date'));
                $fRlog->reference_id =$fLogs->id;
                $fRlog->reference_no=$sNo;
                $fRlog->txn_method=$rd->input('txn_method');
                $fRlog->reference_type =$payType;
                $fRlog->txn_type=$txnBankType;
                $fRlog->txn_amount = $rd->input('txn_amount');
                //$fRlog->payment_bank_id=$rd->input('bankAcID');
                $fRlog->payment_bank_id=$rd->input('account_id');
                $fRlog->party_id=$rd->input('bankAcID');
                $fRlog->payment_referrence_no=$rd->input('payment_referrence_no');
                $fRlog->party_prevBal = $BnkAc->current_balance;
                $fRlog->party_currentBal = $Bankbalance;
                $fRlog->remark =$rd->input('remark');
                $fRlog->user_id =Auth::user()->id;
                $fRlog->save();

                //======Update party account======
                $this->account_obj->where('id','=',$rd->input('account_id'))->decrement('current_balance',$rd->input('txn_amount'));
                increaseSerialNo('payment_receipt');

                Toastr::success('Payment done successfully', 'Success');
                //success_session('Payment done successfully');
                return redirect()->route('admin.payment.inward.index');
            }else{

            }
        } catch (\Exception $e) {
            //success_error($e->getMessage());
            return $e;
            return redirect()->back();
        }
       
       

      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $a['title'] = 'Edit Inward Payment';
        $a['breadcrumb'] = breadcrumb([
                    'Payment' => route('admin.payment.inward.index'),
                    'edit' => route('admin.payment.inward.edit', $id),
                ]);
        $a['bank'] = $this->account_obj->orderBy('name')->whereIn('acGroup',[1,2,8])->get();
	    $a['payment'] = $this->payment_obj->where('id',$id)->with('accData')->latest()->first();
        $a['nextBill']=$a['payment']->reference_no;
        Toastr::success('Please update carefully', 'Success');        
        return view('admin.payment.inward.addEditPayment')->with($a);   
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $rd
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $rd, $id)
    {
    
        $inputs = $rd->except('_token');
        $rules = [
            'txn_date' => 'required',
            'account_id' => 'required',
            'txn_amount'=>'required'
        ];


        $validator = Validator::make($inputs, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try{

            $payType='receipt';
            $txnType='debit';
            $txnBankType='credit';
    
            $fLogs=$this->payment_obj->find($id);
            $fLogs->txn_date=general_date($rd->input('txn_date'));
            $fLogs->txn_method=$rd->input('txn_method');
            $fLogs->reference_type =$payType;
            $fLogs->txn_type=$txnType;
            $fLogs->txn_amount = $rd->input('txn_amount');
            $fLogs->party_id=$rd->input('account_id');
            $fLogs->payment_bank_id=$rd->input('bankAcID');
            $fLogs->payment_referrence_no=$rd->input('payment_referrence_no');
            //$fLogs->party_prevBal = $Acc[0]['current_balance'];
            //$fLogs->party_currentBal = $balance;
            $fLogs->remark =$rd->input('remark');
            $fLogs->user_id =Auth::user()->id;

            if($fLogs->save())
            {
                $fRLog = $this->payment_obj->where('reference_id', '=', $id)->firstOrFail();
                $fRLog->txn_date=general_date($rd->input('txn_date'));
                $fRLog->txn_method=$rd->input('txn_method');
                $fRLog->reference_type=$payType;
                $fRLog->txn_type=$txnBankType;
                $fRLog->txn_amount = $rd->input('txn_amount');
                $fRLog->party_id=$rd->input('bankAcID');
                $fRLog->payment_bank_id=$rd->input('account_id');
                $fRLog->payment_referrence_no=$rd->input('payment_referrence_no');
                //$fRLog->party_prevBal = $BnkAc[0]['current_balance'];
                //$fRLog->party_currentBal = $Bankbalance;
                $fRLog->remark =$rd->input('remark');
                $fRLog->user_id =Auth::user()->id;
                $fRLog->save();
                
                Toastr::success('Payment updated successfully', 'Success');
                return redirect()->route('admin.payment.inward.index');
            }else{
                Toastr::error('Unable to update the Payment', 'Error');
            } 
            return redirect()->back();

        } catch (\Exception $e) {
            //success_error($e->getMessage());
            return $e;
            return $e->getMessage();
            return redirect()->back();
        }

    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
