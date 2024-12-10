<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inquery;
use App\Models\InqueryDetail;
use App\Models\NewInquiry;
use App\Models\NewInquiryDetail;
use App\Models\ProductModel;
use App\Models\SerialNo;
use App\Models\FinancialLogsModel;
use App\Models\Employee;
use DB;
use App\Models\StockModel;
use App\Models\Category;
use App\Models\Account;
use App\Models\Color;
use App\Models\State;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Response;
use Auth;

class InquiryNewController extends WebController
{

    public $inquiry_obj,$inquiryDetail_obj;
    public function __construct()
    {
        $this->inquiry_obj = new NewInquiry();
        $this->inquiryDetail_obj = new NewInquiryDetail();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $a['inquiry'] = $this->inquiry_obj->latest()->get();
        $a['title'] = 'Fresh Leads';
        $a['breadcrumb'] = breadcrumb([ 'Inquery' => route('admin.inquiry.index'),]);
        $a['repType']='Fresh Leas';
        return view('admin.inquiry.newinquiry')->with($a);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $a['title']='Create Fresh Lead';
        $a['inquiry'] = $this->inquiry_obj;
        $a['nextBill']=getNewSerialNo('new_inquery');
        $a['action']= 'CreateNewInquiry';
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.inquiry-new.index'),
            ]);
        $a['employees']= Employee::get();
        $a['state']= State::get();
        return view('admin.inquiry.addEditNewInq')->with($a);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $rd)
    {
        
        $rd->validate([
            'name' => ['required'],
            'phone'=>['required'],
            'state_id'=>['required'],
            'bill_amount'=>['required'],
        ]);
        $return_data = $rd->all();
        
        DB::beginTransaction();
        try {

                $InvNo=getNewSerialNo('new_inquery');
                $po = $this->inquiry_obj;
                $po->account_id = $rd->account_id;
                $po->invoice_No = $InvNo;
                $po->saleDate = date("Y-m-d",strtotime($rd->saleDate));
                $po->discount = $rd->DisTotal;
                $po->other_charges = $rd->otherCharges;
                $po->freight = $rd->freight;
                $po->parcels = $rd->parcels;
                $po->salesman_id = $rd->salesman_id;
                $po->remark = $rd->remark;
                $po->invoice_amt = $rd->sumNetTotal;
                $po->bill_amount = $rd->bill_amount;
                $po->tax_amount= $rd->sumTaxAmount;
                $po->user_id=Auth::user()->id;
                $po->branch_id=Auth::user()->branch_id;
                $po->inquiryFor = $rd->inqfor;
                $po->name = $rd->name;
                $po->phone = $rd->phone;
                $po->phone2 = $rd->phone2;
                $po->contactPerson = $rd->contact_person;
                $po->state_id = $rd->state_id;
                $po->city = $rd->city;
                $po->address = $rd->address;
                $po->priceGroup = $rd->priceGroup;
                $po->customerType =$rd->customer_type;
                if($po->save()){

                    $n=0;
                    foreach($rd->stockID as $Atr)
                    {
                        
                        $st=StockModel::where('id',$Atr)->first();

                        $InqDt=new NewInquiryDetail;
                        $InqDt->order_id = $po->id;
                        $InqDt->account_id = $rd->account_id;
                        $InqDt->stock_id = $st->id;
                        $InqDt->sRate = $rd->AdpRate[$n];
                        $InqDt->actualQty = $rd->AdProdQty[$n];
                        $InqDt->sQty = $rd->AdProdQty[$n];
                        //$InqDt->sDiscount = $rd->AdDiscount[$n];
                        $InqDt->sNetAmount = $rd->AdNetAmt[$n];
                        $InqDt->taxRate = $rd->AdTaxRate[$n];
                        $InqDt->taxAmt = $rd->AdTaxAmt[$n];

                        $InqDt->isOffer = $st->isOffer;
                        $InqDt->actualPrice = $st->sale_price;

                        $InqDt->save();

                        $n++;
                    }
                    increaseSerialNo('new_inquery');
                }
                DB::commit();
                Toastr::success('Sale inquery Successfully Created', 'Success!!!');
                return redirect()->route('admin.inquiry-new.index');
         
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
            Toastr::error($e->getMessage(), 'Success!!!');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        $a['title']='Update Fresh Leads';
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.inquiry-new.index'),
            ]);
        $a['employees']= Employee::get();
        $a['state']= State::get();
        $a['inquiry'] = $this->inquiry_obj::where('id',$id)->with('state')->first();
        $a['nextBill']=$a['inquiry']->invoice_No;
        $a['requestID'] =$id;
        $a['action']='UpdateNewInquery';
        
		$a['pdStk']= DB::table('tbl_new_inquery_detail AS sl')
                        ->join('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
						->join('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
						->join('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
						->join('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
						->where('sl.order_id','=',$id)
						->where('sl.status','=','active')
						->select('sl.*','pd.name as prodName', 'atr.name as attrName','ct.name as catName')
						->get();
		
			
        return view('admin.inquiry.addEditNewInq')->with($a);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $rd, $id)
    {
        $rd->validate([
            'name' => ['required'],
            'phone'=>['required'],
            'state_id'=>['required'],
            'bill_amount'=>['required'],
        ]);

        DB::beginTransaction();
        try {
                $po = $this->inquiry_obj->find($id);
                $po->account_id = $rd->account_id;
                $po->saleDate = date("Y-m-d",strtotime($rd->saleDate));
                $po->discount = $rd->DisTotal;
                $po->other_charges = $rd->otherCharges;
                $po->freight = $rd->freight;
                $po->parcels = $rd->parcels;
                $po->salesman_id = $rd->salesman_id;
                $po->remark = $rd->remark;
                $po->invoice_amt = $rd->sumNetTotal;
                $po->bill_amount = $rd->bill_amount;
                $po->tax_amount= $rd->sumTaxAmount;
                $po->inquiryFor = $rd->inqfor;
                $po->name = $rd->name;
                $po->phone = $rd->phone;
                $po->phone2 = $rd->phone2;
                $po->contactPerson = $rd->contact_person;
                $po->state_id = $rd->state_id;
                $po->city = $rd->city;
                $po->address = $rd->address;
                $po->priceGroup = $rd->priceGroup;
                $po->customerType =$rd->customer_type;
                if($po->save()){

                    $n=0;
                    foreach($rd->stockID as $Atr)
                    {
                        if($rd->oldID[$n]==0){
                            $InqDt=new NewInquiryDetail;
                        }else{
                            $InqDt=$this->inquiryDetail_obj->find($rd->oldID[$n]);
                        }
                        
                        $st=StockModel::where('id',$Atr)->first();
                        $InqDt->order_id = $po->id;
                        $InqDt->account_id = $rd->account_id;
                        $InqDt->stock_id = $st->id;
                        $InqDt->sRate = $rd->AdpRate[$n];
                        $InqDt->actualQty = $rd->AdProdQty[$n];
                        $InqDt->sQty = $rd->AdProdQty[$n];
                        $InqDt->sNetAmount = $rd->AdNetAmt[$n];
                        $InqDt->taxRate = $rd->AdTaxRate[$n];
                        $InqDt->taxAmt = $rd->AdTaxAmt[$n];
                        $InqDt->isOffer = $st->isOffer;
                        $InqDt->actualPrice = $st->sale_price;

                        $InqDt->save();

                        $n++;
                    }
                    increaseSerialNo('new_inquery');
                }
                DB::commit();
                Toastr::success('Sale inquery Successfully Created', 'Success!!!');
                return redirect()->route('admin.inquiry-new.index');
         
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
            Toastr::error($e->getMessage(), 'Success!!!');
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

    public function listing(Request $request)
    {
        $data = $this->color_obj::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $param = [
                    'id' => $row->id,
                    'url' => [
                        'status' => route('admin.color.status_update', $row->id),
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
                        'delete' => route('admin.color.destroy', $row->id),
                        'edit' => route('admin.color.edit', $row->id),
                        // 'view' => route('admin.news.show', $row->id),
                    ]
                ];
                return $this->generate_actions_buttons($param);
            })
            ->rawColumns(["status", "action"])
            ->make(true);
    }
}
