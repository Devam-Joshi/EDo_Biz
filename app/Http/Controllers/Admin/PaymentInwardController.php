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
        $data=$this->payment_obj::where('reference_type', 'Payment')->with('accData')->get();
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
        $a['title']='Add InwardPAyment';
        $a['breadcrumb']=breadcrumb([
            'Category' => route('admin.category.index')
        ]);
        return view('admin.payment.inward.addEditPayment')->with($a);
        
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
          'txn_date' => 'required',
          'supplier_id' => 'required',
          'txn_amount'=>'required'
        ];

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }


    try {
            $this->buildXMLHeader();
          
          
            $payType='receipt';
            $Acc=$this->account_obj->where('id',$request->input('supplier_id'))->select('current_balance')->first();
            $BnkAc=$this->account_obj->where('id',$request->input('bankAcID'))->select('current_balance')->first();
        
            $balance=($Acc->current_balance - $request->input('txn_amount'));
            $Bankbalance=($BnkAc->current_balance + $request->input('txn_amount'));
            $txnType='debit';
            $txnBankType='credit';

            $date = Carbon::now();
            $sNo=getNewSerialNo('payment_receipt');

            $fLogs=$this->payment_obj;
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
            $fLogs->party_prevBal = $Acc->current_balance;
            $fLogs->party_currentBal = $balance;
            $fLogs->remark =$request->input('remark');
            $fLogs->user_id =Auth::user()->id;
            $fLogs->save();

            $LastInsertedId = $fLogs->id;
            if($LastInsertedId){
                $fLogs=$this->payment_obj;
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
                $fLogs->party_prevBal = $BnkAc->current_balance;
                $fLogs->party_currentBal = $Bankbalance;
                $fLogs->remark =$request->input('remark');
                $fLogs->user_id =Auth::user()->id;
                $fLogs->save();

            //======Update party account======
            $this->account_obj->where('id','=',$request->input('supplier_id'))->decrement('current_balance',$request->input('txn_amount'));
            increaseSerialNo('payment_receipt');

            Toastr::success('Payment done successfully', 'Success');
            success_session('Payment done successfully');
            return redirect()->route('admin.capayment.inward.index');
            }
        } catch (\Exception $e) {
            success_error($e->getMessage());

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
        $parent_category = $this->category_obj->orderBy('name')->where('status', 'active')->get();
        $select_cat = $this->category_map_model->where('parent_id', $id)->pluck('category_id')->toArray();
        $data = $this->parent_cat_obj->find($id);
        if (isset($data) && !empty($data)) {
            return view('admin.category.create', [
                'title' => 'Category Update',
                'category' => $parent_category,
                'select_cat' => $select_cat,
                'breadcrumb' => breadcrumb([
                    'Category' => route('admin.category.index'),
                    'edit' => route('admin.category.edit', $id),
                ]),
            ])->with(compact('data'));
        }
        return redirect()->route('admin.category.index');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'mastercatName' => ['required', 'max:255'],
            'mastercatDesc' => ['required']
        ]);
        if ($id != null) {
            $mct = ParentCatModel::find($id);
            $mct->name = $request->mastercatName;
            $mct->description = $request->mastercatDesc;
            if ($mct->save()) {
                if (!empty($request->input('categories'))) {
                    CategoryMapModel::where('parent_id', $mct->id)->delete();
                    foreach ($request->input('categories') as $catID) {
                        $map = new CategoryMapModel();
                        $map->parent_id = $mct->id;
                        $map->category_id = $catID;
                        $map->save();
                    }
                    success_session('Parenet Category updated successfully');
                } else {
                    success_session('Parent Category successfully Update without Child categories');
                }
            } else {
                error_session('Unable to update data', 'Danger');
            }
            return redirect()->route('admin.category.index');
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
        $data = $this->parent_cat_obj::where('id', $id)->first();
        if ($data) {
            $data->delete();
            success_session('Parenet Category deleted successfully');
        } else {
            error_session('Parenet Category not found');
        }
        return redirect()->route('admin.category.index');
    }

    public function listing11(Request $request)
    {
        $data = $this->parent_cat_obj::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $param = [
                    'id' => $row->id,
                    'url' => [
                        'status' => route('admin.category.status_update', $row->id),
                    ],
                    'checked' => ($row->status == 'active') ? 'checked' : ''
                ];
                return  $this->generate_switch($param);
            })
            ->addColumn('description', function ($row) {
                return "-";
            })
            ->addColumn('action', function ($row) {
                $param = [
                    'id' => $row->id,
                    'url' => [
                        'delete' => route('admin.category.destroy', $row->id),
                        'edit' => route('admin.category.edit', $row->id),
                        // 'view' => route('admin.news.show', $row->id),
                    ]
                ];
                return $this->generate_actions_buttons($param);
            })
            ->rawColumns(["status", "action"])
            ->make(true);
    }
}
