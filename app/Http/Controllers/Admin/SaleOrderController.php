<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inquery;
use App\Models\InqueryDetail;
use App\Models\SaleOrder;
use App\Models\SaleOrderDetail;
use App\Models\ProductModel;
use App\Models\SerialNo;
use App\Models\FinancialLogsModel;
use App\Models\Employee;
use DB;
use App\Models\StockModel;
use App\Models\Category;
use App\Models\Account;
use App\Models\Color;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Response;
use Auth;

class SaleOrderController extends WebController
{

    public $soObj,$soDetailObj;
    public function __construct()
    {
        $this->soObj = new SaleOrder();
        $this->inqDetailObj = new SaleOrderDetail();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


     public function index(Request $r)
     {
        
        $a['title'] = 'Sales Order';
        $a['breadcrumb'] = breadcrumb([ 'Sale Order' => route('admin.sale-order.index'),]);
        $a['repType']='Sale-order';
        $a['so']=SaleOrder::latest()->with('account','salesman')->get();
         return view('admin.sale-order.index')->with($a);
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $a['title']='Sale Order';
        $a['so'] = $this->soObj;
        $a['action']= 'NewSaleOrder';
        $a['nextBill']=getNewSerialNo('sale_order');
        $a['employees']=Employee::get();
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.sale-order.index'),
            ]);
        return view('admin.sale-order.addEditForm')->with($a);
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
            'account_id' => ['required'],
            'salesman_id'=>['required'],
            'bill_amount'=>['required'],
            'saleDate'=>['required'],
        ]);
        $return_data = $rd->all();  
        DB::beginTransaction();
        try {

                $InvNo=getNewSerialNo('sale_order');
                $bill = $this->soObj;
                $bill->account_id = $rd->account_id;
                $bill->invoice_No = $InvNo;
                $bill->saleDate = date("Y-m-d",strtotime($rd->saleDate));
                $bill->discount = $rd->DisTotal;
                $bill->other_charges = $rd->otherCharges;
                $bill->freight = $rd->freight;
                $bill->parcels = $rd->parcels;
                $bill->salesman_id = $rd->salesman_id;
                $bill->remark = $rd->remark;
                $bill->invoice_amt = $rd->sumNetTotal;
                $bill->bill_amount = $rd->bill_amount;
                $bill->tax_amount= $rd->sumTaxAmount;
                $bill->user_id=Auth::user()->id;
                $bill->branch_id=Auth::user()->branch_id;
                if($bill->save()){

                    $n=0;
                    foreach($rd->stockID as $Atr)
                    {
                        
                        $st=StockModel::where('id',$Atr)->first();

                        $saleProdDt=new SaleOrderDetail;
                        $saleProdDt->order_id = $bill->id;
                        $saleProdDt->account_id = $rd->account_id;
                        $saleProdDt->stock_id = $st->id;
                        $saleProdDt->sRate = $rd->AdpRate[$n];
                        $saleProdDt->actualQty = $rd->AdProdQty[$n];
                        $saleProdDt->sQty = $rd->AdProdQty[$n];
                        $saleProdDt->sNetAmount = $rd->AdNetAmt[$n];
                        $saleProdDt->taxRate = $rd->AdTaxRate[$n];
                        $saleProdDt->taxAmt = $rd->AdTaxAmt[$n];
                        $saleProdDt->isOffer = $st->isOffer;
                        $saleProdDt->actualPrice = $st->sale_price;
                        $saleProdDt->save();

                        $n++;
                    }
                    increaseSerialNo('sale_order');
                }
                DB::commit();
                Toastr::success('Sale inquery Successfully Created', 'Success!!!');
                return redirect()->route('admin.sale-order.index');


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
        $a['title']='Update Sale Order';
        $a['breadcrumb']=breadcrumb([
            'saleOrder' => route('admin.sale-order.index'),
            ]);
        $a['employees']= Employee::get();
        $a['so'] = $this->soObj::where('id',$id)->with('account.citydata','account.statedata')->first();
        $a['nextBill']=$a['so']->invoice_No;
        $a['requestID'] =$id;
        $a['action']= 'UpdateSaleOrder';

        $validOrderItems = DB::table('tbl_sale_order_detail as sod')
            ->join('tbl_products_stock as ps', 'sod.stock_id', '=', 'ps.id')
            ->whereRaw('sod.sQty <= ps.current_stock')
            ->whereNotIn('sod.stock_id', function ($query) {
                $query->select('sod_inner.order_id')
                    ->from('tbl_sale_order_details as sod_inner')
                    ->join('tbl_product_stock as ps_inner', 'sod_inner.stock_id', '=', 'ps_inner.id')
                    ->whereRaw('ps_inner.category_id = (SELECT category_id FROM tbl_product_stock WHERE id = sod_inner.stock_id)')
                    ->groupBy('sod_inner.order_id', 'ps_inner.category_id')
                    ->havingRaw('SUM(CASE WHEN ps_inner.current_stock < sod_inner.sQty THEN 1 ELSE 0 END) > 0');
            })
            ->get();
    
		$a['sod']= DB::table('tbl_sale_order_detail AS sl')
                        ->join('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
						->join('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
						->join('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
						->join('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
						->where('sl.order_id','=',$id)
						->where('sl.status','=','active')
						->select('sl.*','pd.name as prodName', 'atr.name as attrName','ct.name as catName')
						->get();
        
        return view('admin.sale-order.addEditForm')->with($a);
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
            'account_id' => ['required'],
            'salesman_id'=>['required'],
            'bill_amount'=>['required'],
            'saleDate'=>['required'],
        ]);

        DB::beginTransaction();
        try {
                $bill = $this->soObj->find($id);
                $bill->account_id = $rd->account_id;
                $bill->saleDate = date("Y-m-d",strtotime($rd->saleDate));
                $bill->discount = $rd->DisTotal;
                $bill->other_charges = $rd->otherCharges;
                $bill->freight = $rd->freight;
                $bill->parcels = $rd->parcels;
                $bill->salesman_id = $rd->salesman_id;
                $bill->remark = $rd->remark;
                $bill->invoice_amt = $rd->sumNetTotal;
                $bill->bill_amount = $rd->bill_amount;
                $bill->tax_amount= $rd->sumTaxAmount;
                
                if($bill->save()){

                    $n=0;
                    foreach($rd->stockID as $Atr)
                    {
                        if($rd->oldID[$n]==0){
                            $InqDt=new SaleOrderDetail;
                        }else{
                            $InqDt=SaleOrderDetail::find($rd->oldID[$n]);
                        }
                        
                        $st=StockModel::where('id',$Atr)->first();
                        $InqDt->order_id = $bill->id;
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
                return redirect()->route('admin.sale-order.index');
         
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

    /**
     * Convert New customer Inquery to  Existing Customer Inquery
     * - add new customer account 
     * - generate inquery with old data
     * 
     */
    public function convertInqueryToOrder($id){

        $a['title']='Inquery To Sale Order';
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.sale-order.index'),
            ]);
        $a['employees']= Employee::get();
        $a['so'] = Inquery::where('id',$id)->with('account')->first();
        $a['nextBill']= $InvNo=getNewSerialNo('sale_order');
        $a['requestID'] =$id;
        $a['action']= 'InqueryToSaleOrder';
	    $a['sod']= DB::table('tbl_sale_inquery_detail AS sl')
                        ->join('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
						->join('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
						->join('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
						->join('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
						->where('sl.order_id','=',$id)
						->where('sl.status','=','active')
						->select('sl.*','pd.name as prodName', 'atr.name as attrName','ct.name as catName')
						->get();
        
        return view('admin.sale-order.addEditForm')->with($a);

    }

    public function inqueryToOrderSave(Request $rd,$id){

        $rd->validate([
            'account_id' => ['required'],
            'salesman_id'=>['required'],
            'bill_amount'=>['required'],
            'saleDate'=>['required'],
        ]);
        
           $newInq= NewInquiry::where('id',$id)->first();
         
                DB::beginTransaction();
                try {
                        $InvNo=getNewSerialNo('sale_order');
                        $bill = $this->soObj;
                        $bill->order_type='so';
                        $bill->account_id = $newInq->account_id;
                        $bill->invoice_No = $InvNo;
                        $bill->saleDate = date("Y-m-d");
                        $bill->discount = $newInq->discount;
                        $bill->other_charges = $newInq->other_charges;
                        $bill->freight = $newInq->freight;
                        $bill->parcels = $newInq->parcels;
                        $bill->salesman_id = $newInq->salesman_id;
                        $bill->remark = $newInq->remark;
                        $bill->invoice_amt = $newInq->invoice_amt;
                        $bill->bill_amount = $newInq->bill_amount;
                        $bill->tax_amount= $newInq->tax_amount;
                        $bill->user_id=Auth::user()->id;
                        $bill->branch_id=Auth::user()->branch_id;
                        $bill->inquiry_id=$newInq->id;
                        if($bill->save()){
                            //====GET inquery Data====
                            $nInqDtl=NewInquiryDetail::where('order_id',$id)->get();
                            $n=0;
                            if($nInqDtl){
                                foreach($nInqDtl as $nid)
                                {   
                                    
                                    $saleProdDt=new SaleOrderDetail();
                                    $saleProdDt->order_id = $bill->id;
                                    $saleProdDt->account_id = $bill->account_id;
                                    $saleProdDt->stock_id = $nid->stock_id;
                                    $saleProdDt->sRate = $nid->sRate;
                                    $saleProdDt->actualQty = $nid->actualQty;
                                    $saleProdDt->sQty = $nid->sQty;
                                    $saleProdDt->sNetAmount = $nid->sNetAmount;
                                    $saleProdDt->taxRate = $nid->taxRate;
                                    $saleProdDt->taxAmt = $nid->taxAmt;
                                    $saleProdDt->isOffer = $nid->isOffer;
                                    $saleProdDt->actualPrice = $nid->actualPrice;
                                    $saleProdDt->save();
                                    $n++;
                                }
                            }
                            
                            increaseSerialNo('sale_order');
                            NewInquiry::where('id',$id)->delete();
                            NewInquiryDetail::where('order_id',$id)->delete();
                        }
                        DB::commit();
                        Toastr::success('Sale Order Successfully Created', 'Success!!!');
                        return redirect()->route('admin.sale-order.index');


                } catch (\Exception $e) {
                    DB::rollBack();
                    return $e->getMessage();
                    Toastr::error($e->getMessage(), 'Success!!!');
                }                    

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
