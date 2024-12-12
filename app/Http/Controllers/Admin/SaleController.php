<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Sales;
use App\Models\SalesDetail;
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

class SaleController extends WebController
{

    public $soObj,$soDetailObj;
    public function __construct()
    {
        $this->soObj = new Sales();
        $this->inqDetailObj = new SalesDetail();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


     public function index(Request $r)
     {
        
        $a['title'] = 'Sale Invoice';
        $a['breadcrumb'] = breadcrumb([ 'Sale Invoice' => route('admin.sale.index'),]);
        $a['repType']='Sale';
        $a['so']=$this->soObj::latest()->with('account.citydata','account.statedata','salesman')->get();
         return view('admin.sale.index')->with($a);
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $a['title']='Create Sale Invoice';
        $a['so'] = $this->soObj;
        $a['action']= 'NewSale';
        $a['nextBill']=getNewSerialNo('sale_invoice');
        $a['employees']=Employee::get();
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.sale.index'),
            ]);
        return view('admin.sale.addEditForm')->with($a);
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

                $InvNo=getNewSerialNo('sale_invoice');
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

                        $saleProdDt=new SalesDetail;
                        $saleProdDt->order_id = $bill->id;
                        $saleProdDt->account_id = $rd->account_id;
                        $saleProdDt->stock_id = $st->id;
                        $saleProdDt->sRate = $rd->AdpRate[$n];
                        $saleProdDt->sQty = $rd->AdProdQty[$n];
                        $saleProdDt->sNetAmount = $rd->AdNetAmt[$n];
                        $saleProdDt->taxRate = $rd->AdTaxRate[$n];
                        $saleProdDt->taxAmt = $rd->AdTaxAmt[$n];
                        $saleProdDt->isOffer = $st->isOffer;
                        $saleProdDt->actualPrice = $st->sale_price;
                        $saleProdDt->save();
                        $n++;
                    }
                    increaseSerialNo('sale_invoice');
                }
                DB::commit();
                Toastr::success('Sale inquery Successfully Created', 'Success!!!');
                return redirect()->route('admin.sale.index');


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
        $a['title']='Update Sale Invoice';
        $a['breadcrumb']=breadcrumb([
            'saleInvoice' => route('admin.sale.index'),
            ]);
        $a['employees']= Employee::get();
        $a['so'] = $this->soObj::where('id',$id)->with('account.citydata','account.statedata','salesman')->first();
        $a['nextBill']=$a['so']->invoice_No;
        $a['requestID'] =$id;
        $a['action']= 'UpdateSaleInvoice';
        
	    $a['sod']= DB::table('tbl_sale_detail AS sl')
                        ->join('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
						->join('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
						->join('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
						->join('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
						->where('sl.order_id','=',$id)
						->where('sl.status','=','active')
						->select('sl.*','pd.name as prodName', 'atr.name as attrName','ct.name as catName')
						->get();
        
        return view('admin.sale.addEditForm')->with($a);
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
                            $InqDt=new SalesDetail;
                        }else{
                            $InqDt=SalesDetail::find($rd->oldID[$n]);
                        }
                        
                        $st=StockModel::where('id',$Atr)->first();
                        $InqDt->order_id = $bill->id;
                        $InqDt->account_id = $rd->account_id;
                        $InqDt->stock_id = $st->id;
                        $InqDt->sRate = $rd->AdpRate[$n];
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
                return redirect()->route('admin.sale.index');
         
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
    public function convertOrderToBill($id){

        $a['title']='Sales Order To Sale Bill';
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.sale.index'),
            ]);
        $a['employees']= Employee::get();
        $a['so'] = SaleOrder::where('id',$id)->with('account')->first();
        $a['nextBill']= $InvNo=getNewSerialNo('sale_invoice');
        $a['requestID'] =$id;
        $a['action']= 'OrderToSaleBill';
	    $a['sod']= DB::table('tbl_sale_order_detail AS sl')
                        ->join('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
						->join('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
						->join('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
						->join('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
						->where('sl.order_id','=',$id)
						->where('sl.status','=','active')
						->select('sl.*','pd.name as prodName', 'atr.name as attrName','ct.name as catName')
						->get();
        
        return view('admin.sale.addEditForm')->with($a);

    }

    

    public function convertOrderToReadyBill($id){

        $a['title']='Sales Order To Fulfill Sale Invoice';
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.sale.index'),
            ]);
        $a['employees']= Employee::get();
        $a['so'] = SaleOrder::where('id',$id)->with('account')->first();
        $a['nextBill']= $InvNo=getNewSerialNo('sale_invoice');
        $a['requestID'] =$id;
        $a['action']= 'OrderToSaleBill';

        function getFulfilledCategories($id)
        {
            // Subquery to find categories with insufficient stock
            $excludedCategories = DB::table('tbl_sale_order_detail as od')
                ->join('tbl_products_stock as ps', 'od.stock_id', '=', 'ps.id')
                ->where('od.order_id', $id)
                ->whereColumn('ps.current_stock', '<', 'od.sQty')
                ->distinct()
                ->pluck('ps.category_id');

            // Main query to get items from fulfilled categories
            $fulfilledItems = DB::table('tbl_sale_order_detail as sl')
                                ->join('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
                                ->join('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
                                ->join('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
                                ->join('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
                                ->where('sl.order_id','=',$id)
                                ->whereNotIn('st.category_id', $excludedCategories)
                                ->where('sl.status','=','active')
                                ->select('sl.*','pd.name as prodName', 'atr.name as attrName','ct.name as catName')
                                ->get();

            

            return $fulfilledItems;
        }
    
    

        
	    // $a['sod']= DB::table('tbl_sale_order_detail AS sl')
        //                 ->join('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
		// 				->join('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
		// 				->join('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
		// 				->join('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
		// 				->where('sl.order_id','=',$id)
		// 				->where('sl.status','=','active')
		// 				->select('sl.*','pd.name as prodName', 'atr.name as attrName','ct.name as catName')
		// 				->get();
        $a['sod']=getFulfilledCategories($id);
        return view('admin.sale.addEditForm')->with($a);

    }

    // public function saleOrderToBillSave(Request $rd,$id){

    //     $rd->validate([
    //         'account_id' => ['required'],
    //         'salesman_id'=>['required'],
    //         'bill_amount'=>['required'],
    //         'saleDate'=>['required'],
    //     ]);
        
    //        $slOdr= SaleOrder::where('id',$id)->first();
         
    //             DB::beginTransaction();
    //             try {
    //                     $InvNo=getNewSerialNo('sale_invoice');
    //                     $bill = $this->soObj;
    //                     $bill->order_type='so';
    //                     $bill->account_id = $slOdr->account_id;
    //                     $bill->invoice_No = $InvNo;
    //                     $bill->saleDate = date("Y-m-d");
    //                     $bill->discount = $slOdr->discount;
    //                     $bill->other_charges = $slOdr->other_charges;
    //                     $bill->freight = $slOdr->freight;
    //                     $bill->parcels = $slOdr->parcels;
    //                     $bill->salesman_id = $slOdr->salesman_id;
    //                     $bill->remark = $slOdr->remark;
    //                     $bill->invoice_amt = $slOdr->invoice_amt;
    //                     $bill->bill_amount = $slOdr->bill_amount;
    //                     $bill->tax_amount= $slOdr->tax_amount;
    //                     $bill->user_id=Auth::user()->id;
    //                     $bill->branch_id=Auth::user()->branch_id;
    //                     $bill->inquiry_id=$slOdr->id;
    //                     if($bill->save()){

    //                         //====GET inquery Data====
    //                         $nInqDtl=SaleOrderDetail::where('order_id',$id)->get();
    //                         $n=0;
    //                         if($nInqDtl){
    //                             foreach($nInqDtl as $nid)
    //                             {   
                                    
    //                                 $saleProdDt=new SalesDetail();
    //                                 $saleProdDt->order_id = $bill->id;
    //                                 $saleProdDt->account_id = $bill->account_id;
    //                                 $saleProdDt->stock_id = $nid->stock_id;
    //                                 $saleProdDt->sRate = $nid->sRate;
    //                                 $saleProdDt->sQty = $nid->sQty;
    //                                 $saleProdDt->sNetAmount = $nid->sNetAmount;
    //                                 $saleProdDt->taxRate = $nid->taxRate;
    //                                 $saleProdDt->taxAmt = $nid->taxAmt;
    //                                 $saleProdDt->isOffer = $nid->isOffer;
    //                                 $saleProdDt->actualPrice = $nid->actualPrice;
    //                                 $saleProdDt->save();
    //                                 $n++;
    //                             }
    //                         }
                            
    //                         increaseSerialNo('sale_invoice');
    //                         NewInquiry::where('id',$id)->delete();
    //                         NewInquiryDetail::where('order_id',$id)->delete();
    //                     }
    //                     DB::commit();
    //                     Toastr::success('Sale Invoice Successfully Created', 'Success!!!');
    //                     return redirect()->route('admin.sale.index');


    //             } catch (\Exception $e) {
    //                 DB::rollBack();
    //                 return $e->getMessage();
    //                 Toastr::error($e->getMessage(), 'Success!!!');
    //             }                    

    // }

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
