<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\Expense;
use App\Models\Account;
use App\Models\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\WebController;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
use Auth;
use session;
class PaymentTransferController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $payment_obj, $transfer_obj, $account_obj;
    public function __construct()
    {
        $this->transfer_obj = new Transfer();
        $this->payment_obj = new Payment();
        $this->account_obj = new Account();
    }

    public function index()
    {   
    

        $data=$this->transfer_obj->with('payerData','receiverData')->where('reference_id','<',1)->latest()->get();
        return view('admin.payment.transfer.index', [
            'title' => 'Transfer Payment',
            'breadcrumb' => breadcrumb([
                'Transfer Payment' => route('admin.payment.transfer.index'),
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
                        'status' => route('admin.payment.transfer.status_update', $value->id),
                        'edit' => route('admin.payment.transfer.edit', $value->id),
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
        $a['bank'] = $this->account_obj->orderBy('name')->whereIn('acGroup',[3,4])->where('status', 'active')->get();
        $a['payment'] =$this->transfer_obj;
        $a['nextBill']=getNewSerialNo('transfer_receipt');
        $a['title']='Add Transfer Payment';
        $a['breadcrumb']=breadcrumb([
            'Payment Transfer' => route('admin.payment.transfer.index')
        ]);
        return view('admin.payment.transfer.addEditPayment')->with($a);
        
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
            'payer_id' => 'required',
            'receiver_id' => 'required',
            'txn_amount'=>'required'
          ];

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }


    try {
           
            $PayerAcc=$this->account_obj->where('id',$rd->input('payer_id'))->select('current_balance')->first();
            $ReceiverAcc=$this->account_obj->where('id',$rd->input('receiver_id'))->select('current_balance')->first();

            $txnPayerType='debit';
            $txnReceiverType='credit';
            $payerBalance=($PayerAcc->current_balance - $rd->input('txn_amount'));
            $receiverBalance=($ReceiverAcc->current_balance + $rd->input('txn_amount'));
            $date = Carbon::now();
            $sNo=getNewSerialNo('transfer_receipt');

            $TfLogs=$this->transfer_obj;
            $TfLogs->txn_date=general_date($rd->input('txn_date'));
            $TfLogs->reference_id = '';
            $TfLogs->reference_no=$sNo;
            $TfLogs->txn_method='cash';
            $TfLogs->reference_type ='transfer';
            $TfLogs->txn_type=$txnReceiverType;
            $TfLogs->txn_amount = $rd->input('txn_amount');
            $TfLogs->payer_party_id=$rd->input('payer_id');
            $TfLogs->receiver_party_id=$rd->input('receiver_id');
            $TfLogs->payment_referrence_no=$rd->input('payment_referrence_no');
            $TfLogs->remark =$rd->input('remark');
            $TfLogs->user_id =Auth::user()->id;

            if($TfLogs->save())
            {   
                //===== Financal Log entery for payer====
                $fRlog=$this->payment_obj;
                $fRlog->txn_date=general_date($rd->input('txn_date'));
                $fRlog->reference_id =$TfLogs->id;
                $fRlog->reference_no=$sNo;
                $fRlog->txn_method=$rd->input('txn_method');
                $fRlog->reference_type ='transfer';
                $fRlog->txn_type=$txnPayerType;
                $fRlog->txn_amount = $rd->input('txn_amount');
                $fRlog->party_id=$rd->input('receiver_id');
                $fRlog->payment_bank_id=$rd->input('payer_id');
                $fRlog->payment_referrence_no=$rd->input('payment_referrence_no');
                $fRlog->party_prevBal = $ReceiverAcc[0]['current_balance'];
                $fRlog->party_currentBal = $receiverBalance;
                $fRlog->remark = $rd->input('remark');
                $fRlog->user_id = Auth::user()->id;
                $fRlog->save();

                //===== Financal Log entery for receiver====
                $fLogs=new payment();
                $fLogs->txn_date=general_date($rd->input('txn_date'));
                $fLogs->reference_id = $TfLogs->id;
                $fLogs->reference_no=$sNo;
                $fLogs->txn_method=$rd->input('txn_method');
                $fLogs->reference_type ='transfer';
                $fLogs->txn_type=$txnReceiverType;
                $fLogs->txn_amount = $rd->input('txn_amount');
                $fLogs->party_id=$rd->input('payer_id');
                $fLogs->payment_bank_id=$rd->input('receiver_id');
                $fLogs->payment_referrence_no=$rd->input('payment_referrence_no');
                $fLogs->party_prevBal = $PayerAcc[0]['current_balance'];
                $fLogs->party_currentBal = $payerBalance;
                $fLogs->remark =$rd->input('remark');
                $fLogs->user_id = Auth::user()->id;
                $fLogs->save();

                //======Update Payer account======
                $this->account_obj->where('id','=',$rd->input('payer_id'))->decrement('current_balance',$rd->input('txn_amount'));

                 //======Update Receiver account======
                 $this->account_obj->where('id','=',$r->input('receiver_id'))->increment('current_balance',$rd->input('txn_amount')); 

                increaseSerialNo('transfer_receipt');

                Toastr::success('Payment done successfully', 'Success');
                //success_session('Payment done successfully');
                return redirect()->route('admin.payment.transfer.index');
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
        $a['title'] = 'Edit Transfer Payment';
        $a['breadcrumb'] = breadcrumb([
                    'Transfer' => route('admin.payment.transfer.index'),
                    'edit' => route('admin.payment.transfer.edit', $id),
                ]);
        $a['bank'] = $this->account_obj->orderBy('name')->whereIn('acGroup',[3,4])->get();
	    $a['payment'] = $this->transfer_obj->where('id',$id)->with('payerData','receiverData')->first();
        $a['nextBill']=$a['payment']->reference_no;
        Toastr::success('Please update carefully', 'Success');        
        return view('admin.payment.transfer.addEditPayment')->with($a);   
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
            'payer_id' => 'required',
            'receiver_id' => 'required',
            'txn_amount'=>'required'
        ];

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try{

            $txnPayerType='debit';
		    $txnReceiverType='credit';;
    
            $TfLogs=$this->transfer_obj->find($id);
            $TfLogs->txn_date=general_date($rd->input('txn_date'));
            $TfLogs->txn_amount = $rd->input('txn_amount');
            $TfLogs->payer_party_id=$rd->input('payer_id');
            $TfLogs->receiver_party_id=$rd->input('receiver_id'); //account_id
            $TfLogs->payment_referrence_no=$rd->input('payment_referrence_no');
            $TfLogs->remark =$rd->input('remark');
            
            if($TfLogs->save()){

                $fRLog = $this->payment_obj->where('reference_id', '=', $id)
                        ->where('txn_type',$txnPayerType)->where('reference_type','transfer')->firstOrFail();
                $fRLog->txn_date=general_date($rd->input('txn_date'));
                $fRLog->txn_amount = $rd->input('txn_amount');
                $fRLog->party_id=$rd->input('receiver_id');
                $fRLog->payment_bank_id=$rd->input('payer_id');
                $fRLog->payment_referrence_no=$rd->input('payment_referrence_no');
                $fRLog->remark =$rd->input('remark');
                $fRLog->user_id =Auth::user()->id;
                $fRLog->save();

                $fPLog = Payment::where('reference_id', '=', $id)
                        ->where('txn_type',$txnReceiverType)->where('reference_type','transfer')->firstOrFail();
                $fPLog->txn_date=general_date($rd->input('txn_date'));
                $fPLog->txn_amount = $rd->input('txn_amount');
                $fPLog->party_id=$rd->input('payer_id');
                $fPLog->payment_bank_id=$rd->input('receiver_id');
                $fPLog->payment_referrence_no=$rd->input('payment_referrence_no');
                $fPLog->remark =$rd->input('remark');
                $fPLog->user_id =Auth::user()->id;
                $fPLog->save();
                
                Toastr::success('Payment updated successfully', 'Success');
                return redirect()->route('admin.payment.transfer.index');
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
