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
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Response;
use Auth;

class InqueryController extends WebController
{

    public $inqObj,$inqDetailObj;
    public function __construct()
    {
        $this->inqObj = new Inquery();
        $this->inqDetailObj = new InqueryDetail();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


     public function index(Request $r)
     {
         $a['inquiry'] = $this->inqObj->with('account')->latest()->limit(20)->get();
         $a['title'] = 'Sales Inquery';
         $a['breadcrumb'] = breadcrumb([ 'Inquery' => route('admin.inquiry.index'),]);
         $a['repType']='Inquery';
         return view('admin.inquiry.index')->with($a);
     }



    public function index11(Request $r)
    {
        $a['inquery'] = $this->inqObj->latest()->get();
        $a['title'] = 'Sales Inquery';
        $a['breadcrumb'] = breadcrumb([ 'Inquery' => route('admin.inquiry.index'),]);
        $a['repType']='Sale Inquery';
        return view('admin.inquery.index')->with($a);
        
        //return $rd;
		$a['accounts'] = Account::latest()->where('acGroup','!=',1)->where('acGroup','!=',1)->get();
		$a['salesman'] = Employee::withCount('assignInquery')->get();
		

		$cond='';
		$tbl='tbl_sale_inquery_detail';
		$tblOdr='tbl_sale_inquery';
		$Title='Billwise Sale-Inquery Report';
		$pageTitle='Sale Inquery Billwise Detail';
		$repGroup='order';
		$newInqCount=0;
		
		if(isset($_POST['status']) && $_POST['status']!=null)
		{
				if($_POST['status']=='*')
				{
          $cond='1=1';
					$Title.=' (All)';
				}else if($_POST['status']=='2'){
          $cond='odr.billing_status="2"';
					$Title.=' (Cancelled)';
				}else if($_POST['status']=='1'){
          $cond='odr.billing_status="1"';
					$Title.=' (Converted To Order)';
				}else{

				}
		}else{
				$cond='odr.billing_status="0"';
				$Title.=' (Pending)';
			}
					

        if(isset($_POST['fromdate'],$_POST['todate']) && $_POST['fromdate']!=null && $_POST['todate']!=null)
  			{
  				$cond.=" and (odr.saleDate>='".$_POST['fromdate']." 00 00 01' and odr.saleDate<='".$_POST['todate']." 23 59 59')";
  				$Title.= '<small>('.$_POST['fromdate'].' - '.$_POST['todate'].')</small>';
  			}

			  if(isset($_POST['AccountID']) && $_POST['AccountID']>=1)
				{
					$cond.=" and odr.supplier_id='".$_POST['AccountID']."'";
					$a = Account::where('id','=',$_POST['AccountID'])->first();
					$Title.=' <br> <small>'.$a->name.'</small> | ';
				}
				
				if(isset($_POST['salesMan']) && $_POST['salesMan']>=1)
				{
					$cond.=" and odr.salesman_id='".$_POST['salesMan']."'";
					$a = Employee::where('id','=',$_POST['salesMan'])->first();
					$Title.=' <br> <small>'.$a->name.'</small> | ';
				}

			$sod = DB::select( DB::raw("SELECT odr.*,emp.name salesMan,ur.name as userName,ac.name supplierName,cur.name cancelByName from ".$tblOdr." as odr 
																left Join employees as emp ON emp.id=odr.salesman_id  
																left Join users as ur ON ur.id=odr.user_id
																left Join users as cur ON cur.id=odr.cancel_by
																left Join tbl_account as ac ON ac.id=odr.supplier_id WHERE ".$cond.' order by  CASE WHEN odr.supplier_id = 26 THEN 9999999
																ELSE 1 END desc ,odr.id desc'));
																
			 $newInqCount = DB::select( DB::raw("SELECT min(odr.id) as minid from ".$tblOdr." as odr 
					left Join employees as emp ON emp.id=odr.salesman_id  
					left Join users as ur ON ur.id=odr.user_id  
					left Join tbl_account as ac ON ac.id=odr.supplier_id WHERE ".$cond.' and odr.salesman_id=26 order by odr.id desc'));
			
		if(count($sod)>=1)
		{
			foreach($sod as $pod)
			{
				$odrCatGroup= DB::table($tbl.' AS sl')
					   ->leftJoin('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
					   ->leftJoin('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
					   ->leftJoin('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
					   ->leftJoin('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
					   ->where('sl.order_id','=',$pod->id)
					   ->select('st.product_id','st.category_id','sl.sRate','sl.sDiscount',
					   DB::raw('SUM(sl.sNetAmount) as sNetAmount'),
					   DB::raw('SUM(sl.sQty) as TotalQty'),'pd.name as prodName', 'atr.name as attrName','ct.name as catName')
						->groupBy('sl.product_id','sl.category_id','sl.sRate')
					   ->get();

			 	$std=DB::table($tbl.' AS sd')
					   ->leftJoin('tbl_products_stock AS st', 'st.id', '=', 'sd.stock_id')
					   ->leftJoin('tbl_products_master AS pm', 'pm.id', '=', 'st.product_id')
					   ->leftJoin('tbl_color AS clr', 'clr.id', '=', 'st.attribute_id')
					   ->leftJoin('tbl_categories AS ct','ct.id','=','st.category_id')
					   ->where('sd.order_id','=',$pod->id)
					   ->select('sd.*','pm.name as product_name','pm.code as product_code','clr.name as color_name','ct.name as category_name')
					   ->get();
			
				$pendingOdr=DB::table('tbl_sale_temp_detail as std')
				->leftJoin('tbl_sale_temp_order AS sto', 'sto.id', '=', 'std.order_id')
				->leftJoin('tbl_account as ac', 'ac.id', '=', 'sto.supplier_id')
				->leftJoin('employees as emp', 'emp.id', '=', 'sto.salesman_id')
				->where('std.status','active')
				->where('sto.supplier_id',$pod->supplier_id)
				->groupBy('std.order_id')
				->orderBy('std.order_id', 'desc')
				->select('sto.*','emp.name as salesMan',DB::raw('count(std.id) as itemCount'))
				->get();
				
				
				$pendCart=DB::table('tbl_cart_detail as crd')
				->leftJoin('tbl_cart AS crt', 'crt.id', '=', 'crd.order_id')
				->leftJoin('tbl_account as ac', 'ac.id', '=', 'crt.account_id')
				->leftJoin('employees as emp', 'emp.id', '=', 'crt.salesman_id')
				->where('crt.account_id',$pod->supplier_id)
				->groupBy('crd.order_id')
				->orderBy('crd.order_id', 'desc')
				->select('crt.*','emp.name as salesMan',DB::raw('count(crd.id) as itemCount'))
				->get();
				
				$salesManInqCount=DB::table('tbl_sale_inquery')->where('salesman_id',$pod->salesman_id)->where('billing_status',0)->count();

			$ac = Account::where('id','=',$pod->supplier_id)->get();
			
			$pod->acc=$ac;
			$pod->dt=$std;
			$pod->pdCatGr=$odrCatGroup;
			$pod->pendingOdr=$pendingOdr;
			$pod->pendingCrt=$pendCart;
			$pod->salesManInQCount=$salesManInqCount;
			$po[]=$pod;
		  }
		}else{$po='';	}

        return view('admin.sale_inquery.inqueryList-detail', compact('newInqCount','po','accounts','Title','pageTitle','repGroup','salesman','repType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $a['title']='Add Inquery';
        $a['inquiry'] = $this->inqObj;
        $a['action']= 'CreateInquiry';
        $a['nextBill']=getNewSerialNo('sale_inquery');
        $a['employees']=Employee::get();
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.inquiry.index'),
            ]);
        return view('admin.inquiry.addEditForm')->with($a);
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

                $InvNo=getNewSerialNo('sale_inquery');
                $po = $this->inqObj;
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
                if($po->save()){

                    $n=0;
                    foreach($rd->stockID as $Atr)
                    {
                        
                        $st=StockModel::where('id',$Atr)->first();

                        $saleProdDt= new InqueryDetail();
                        $saleProdDt->order_id = $po->id;
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
                    increaseSerialNo('sale_inquery');
                }
                DB::commit();
                Toastr::success('Sale inquery Successfully Created', 'Success!!!');
                return redirect()->route('admin.inquiry.index');


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
        $a['title']='Update Inquery';
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.inquiry.index'),
            ]);
        $a['employees']= Employee::get();
        $a['inquiry'] = $this->inqObj::where('id',$id)->with('account.citydata','account.statedata')->first();
        $a['nextBill']=$a['inquiry']->invoice_No;
        $a['requestID'] =$id;
        $a['action']= 'UpdatreInquiry';
        
		$a['pdStk']= DB::table('tbl_sale_inquery_detail AS sl')
                        ->join('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
						->join('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
						->join('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
						->join('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
						->where('sl.order_id','=',$id)
						->where('sl.status','=','active')
						->select('sl.*','pd.name as prodName', 'atr.name as attrName','ct.name as catName')
						->get();
        
        return view('admin.inquiry.addEditForm')->with($a);
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
                $po = $this->inqObj->find($id);
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
                if($po->save()){

                    $n=0;
                    foreach($rd->stockID as $Atr)
                    {
                        if($rd->oldID[$n]==0){
                            $InqDt=new InqueryDetail();
                        }else{
                            $InqDt=$this->inqDetailObj->find($rd->oldID[$n]);
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
                return redirect()->route('admin.inquiry.index');
         
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
    public function convertToInquiry($id){

        $a['title']='Convert To Inquery';
        $a['breadcrumb']=breadcrumb([
            'Inquery' => route('admin.inquiry.index'),
            ]);
        $a['employees']= Employee::get();
        $a['inquiry'] = NewInquiry::where('id',$id)->first();
        $a['nextBill']=$a['inquiry']->invoice_No;
        $a['requestID'] =$id;
        $a['action']= 'ConvertInquiry';
		$a['pdStk']= DB::table('tbl_new_inquery_detail AS sl')
                        ->join('tbl_products_stock AS st', 'st.id', '=', 'sl.stock_id')
						->join('tbl_products_master AS pd', 'pd.id', '=', 'st.product_id')
						->join('tbl_color AS atr', 'atr.id', '=', 'st.attribute_id')
						->join('tbl_categories AS ct', 'ct.id', '=', 'st.category_id')
						->where('sl.order_id','=',$id)
						->where('sl.status','=','active')
						->select('sl.*','pd.name as prodName', 'atr.name as attrName','ct.name as catName')
						->get();
        
        return view('admin.inquiry.convertInquiry')->with($a);

    }

    public function convertInquirySave(Request $rd,$id){
        
        //return $rd;
        $phone=$rd->phone;
        $phone2=$rd->phone2;
        $name=$rd->name;
        $accCheck=Account::where(function($q) use ($phone,$phone2,$name){
                                        $q->where('phone',$phone)
                                        ->orWhere('phone2',$phone)
                                        ->orWhere('phone',$phone2)
                                        ->orWhere('phone2',$phone2);
                                    })
                                    ->where('name',$name)->count();
        if($accCheck>=1){
            Toastr::error('Account Already registered with name or phone no.', 'Success!!!');
            return redirect()->back();
        }else{
            $newInq= NewInquiry::where('id',$id)->first();
            $ac= new Account;
            $ac->acCode=getNewSerialNo('account_code');
            $ac->name=$newInq->name;
            $ac->phone=$newInq->phone;
            $ac->phone2=$newInq->phone2;
            $ac->email=$newInq->email;
            $ac->address=$newInq->address;
            $ac->city=$newInq->city;
            $ac->state=$newInq->state;
            $ac->state_id=$newInq->state_id;
            $ac->country='india';
            $ac->contactPerson=$newInq->contactPerson;
            $ac->acGroup=4;
            $ac->type=$newInq->customerType;
            $ac->priceGroup = $newInq->priceGroup;
            $ac->branch_id=$newInq->branch_id;
            
            if($ac->save())
            {
                increaseSerialNo('account_code');
                DB::beginTransaction();
                try {
                        $InvNo=getNewSerialNo('sale_inquery');
                        $po = $this->inqObj;
                        $po->account_id = $ac->id;
                        $po->invoice_No = $InvNo;
                        $po->saleDate = date("Y-m-d");
                        $po->discount = $newInq->discount;
                        $po->other_charges = $newInq->other_charges;
                        $po->freight = $newInq->freight;
                        $po->parcels = $newInq->parcels;
                        $po->salesman_id = $newInq->salesman_id;
                        $po->remark = $newInq->remark;
                        $po->invoice_amt = $newInq->invoice_amt;
                        $po->bill_amount = $newInq->bill_amount;
                        $po->tax_amount= $newInq->tax_amount;
                        $po->user_id=Auth::user()->id;
                        $po->branch_id=Auth::user()->branch_id;
                        $po->inquiryFor = $newInq->inqfor;
                        if($po->save()){

                            $nInqDtl=NewInquiryDetail::where('order_id',$id)->get();
                            $n=0;
                            if($nInqDtl){
                                foreach($nInqDtl as $nid)
                                {   
                                    $saleProdDt=new InqueryDetail;
                                    $saleProdDt->order_id = $po->id;
                                    $saleProdDt->account_id = $po->account_id;
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
                            
                            increaseSerialNo('sale_inquery');
                            NewInquiry::where('id',$id)->delete();
                            NewInquiryDetail::where('order_id',$id)->delete();
                        }
                        DB::commit();
                        Toastr::success('Sale inquery Successfully Created', 'Success!!!');
                        return redirect()->route('admin.inquiry.index');


                } catch (\Exception $e) {
                    DB::rollBack();
                    return $e->getMessage();
                    Toastr::error($e->getMessage(), 'Success!!!');
                }   

            }else{
                Toastr::error('Unable to create Account.', 'Success!!!');
                return redirect()->back();
            }
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
