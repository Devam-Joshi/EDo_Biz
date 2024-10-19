<?php

namespace App\Http\Controllers\Admin;
use App\SaleModel;
use App\SaleDetailModel;
use App\ProductModel;
use App\SerialNo;
use App\FinancialLogsModel;
use App\CartDetailModel;

use DB;
use App\StockModel;
use App\Category;
use App\Models\Account;
use App\Color;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Response;
use Session;
use Auth;

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









	public function multiAction($action,$id)
	{
		$status='false';
		$code='0';
		$msg='';
		$tblD='';

		if($action=='stock_disable'){
			DB::table('tbl_products_stock')->where('id',$id)->update(array('status'=>'0',));
			$msg='Data updated successfully';
			$status='true';
			$code=100;
		}else if($action=='purchase-order'){$tblD='tbl_purchase_temp_detail';}
		else if($action=='purchase' || $action=='purchase-return'){$tblD='tbl_purchase_detail';}
		else if($action=='sale-order'){$tblD='tbl_sale_temp_detail';}
		else if($action=='sale' || $action=='sale-return'){$tblD='tbl_sale_detail';}
		else if($action=='sale-inquery'){$tblD='tbl_sale_inquery_detail';}
		else{}
		if($tblD!=''){

		    if($action=='purchase-order' || $action=='sale-order' || $action=='sale-inquery'){
		        	DB::table($tblD)->where('id',$id)->delete();
        			$msg=$action.' updated successfully '.$tblD;
        			$status='true';
        			$code=101;
		    }else{
    			DB::table($tblD)->where('id',$id)->update(array('status'=>'inactive',));
    			$msg=$action.' updated successfully '.$tblD;
    			$status='true';
    			$code=101;
		    }
		}

		$data['code']=$code;
		$data['message']=$msg;
		$data['status']=$status;
		return Response::json($data);
	}

//======manually Clear ORder for particular item======
public function manualClearOrder($action,$id){
		$status='false';
		$code='0';
		$msg='';
		$tblD='';

		if($action=='purchase-order'){$tblD='tbl_purchase_temp_detail';}
		else if($action=='sale-order'){$tblD='tbl_sale_temp_detail';}
		else{}
		if($tblD!=''){
					DB::table($tblD)->where('id',$id)->update(array('status'=>'inactive','clear_status'=>'M','sQty'=>0));
					$msg=$action.' updated successfully '.$tblD;
					$status='true';
					$code=101;
				}

		$data['code']=$code;
		$data['message']=$msg;
		$data['status']=$status;
		return Response::json($data);
	}

    //=01=======Partywise =Previous Sale Price=======================
    public function lastItemSalePrice($type,$partyID,$itemids){
        if($type=='sale' || $type=='purchase'){
			if($type=='sale'){$OdrDt='tbl_sale_detail';$Odr='tbl_sale_order';}else if($type=='purchase'){ $OdrDt='tbl_purchase_detail';$Odr='tbl_purchase_order';}else{}
            $ids=explode('|',$itemids);
            $data=DB::table($OdrDt.' AS sd')
				->join($Odr.' AS so', function($join) use($partyID,$ids){$join->on('so.id', '=', 'sd.order_id')->where('so.supplier_id','=',$partyID);})
				->where('sd.product_id',$ids[1])->where('sd.category_id',$ids[2])->where('sd.attribute_id',$ids[3])
				->select('sd.order_id','sd.sRate','sd.sQty',DB::raw("DATE_FORMAT(so.saleDate, '%d-%b-%Y') as sDate"))
        ->orderBy('so.id','desc')->limit(5)
				->get();

      }else{
        $reftype=explode('|',$itemids);
        $data=DB::table('tbl_financial_logs AS fl')
          ->leftjoin('tbl_account AS ac', function($join){$join->on('ac.id', '=', 'fl.party_id');})
          ->where('fl.party_id',$partyID)->whereIn('reference_type',$reftype)
          ->select('fl.*','ac.name as AcName',DB::raw("DATE_FORMAT(fl.created_at, '%d-%b-%Y') as sDate"))
          ->orderBy('fl.id','desc')
		  //->limit(10)
          ->get();
      }
        return Response::json($data);
    }
	//====01===ENDe=======================

	//====02====Product Color List=======================
    public function productAtrByParent($id,$catid=null){
			if(Auth::user()->user_type ==104){
				$pp=', "00" as purchase_price';
				$px='"00" as purchase_price';
			}elseif(Auth::user()->user_type ==103){
				$pp=', "00" as sale_price';
				$px='"00" as sale_price';
			}else{
				$pp=', "0" as "0"';
				$px=',"0" as "0"';
			}
      if($catid!=null){
          $subcat=StockModel::where('product_id',$id)->where('status',1)->where('category_id',$catid)->with('attr','category','product')->select('*',\DB::raw('(select sum(std.sQty) from tbl_sale_temp_detail as std where std.stock_id=tbl_products_stock.id and std.status="active") as pending_order'),\DB::raw('(select sum(ptd.sQty) from tbl_purchase_temp_detail as ptd where ptd.stock_id=tbl_products_stock.id and ptd.status="active") as pending_purchase'))->get();
      }else{
          //$subcat=StockModel::where('product_id',$id)->where('status',1)->with('attr','category','product')->get();
			$subcat=DB::select(DB::raw("select st.*,pm.name as product_name,pm.code as product_code,clr.name as color_name,ct.name as category_name,(select sum(sQty) from tbl_sale_temp_detail where stock_id=st.id and status='active') as pending_order,(select sum(sQty) from tbl_purchase_temp_detail where stock_id=st.id and status='active') as purchase_order
			".$pp."
			from tbl_products_stock AS st
			INNER JOIN tbl_products_master AS pm ON pm.id=st.product_id
			INNER JOIN tbl_color AS clr ON clr.id=st.attribute_id
			INNER JOIN tbl_categories AS ct ON ct.id=st.category_id
			where st.status='1' and st.product_id='".$id."' order by ct.name asc,clr.name asc"));
	  }
      return Response::json($subcat);
    }
//=====02===END=====================================


//=====03===PRoduct Current Stock===================
    public function StockByProdAttr($id){
      $stock=StockModel::where('id',$id)->where('status',1)->get();
  		return Response::json($stock);
    }



//=====05====Account/Ledger/Party Details===========
    public function AccountDetail($id)
    {
      $acData=Account::where('id',$id)->get();
	  $currentBalance=$this->partyCalculateClosing($id,'','');
	  $acData[0]['current_balance']	=$currentBalance['closing'];
  		return Response::json($acData);

    }

//=====06===Search Product By Name or Code========
public function searchProdName($name)
{
      $Acc= DB::table('tbl_products_master AS p')
  				->join('tbl_products_stock AS ps', function($join){
					$join->on('ps.product_id', '=', 'p.id');
					$join->where('ps.status','=', 1);
					})
                ->join('tbl_categories AS pc', function($join){
					$join->on('pc.id', '=', 'ps.category_id');
					})
  				->where('p.name','LIKE','%'.$name.'%')
				->where('ps.status',1)
				->orWhere('p.code','LIKE','%'.$name.'%')
				->where('ps.status',1)->where('p.status',1)
  				->select('p.id','p.code','p.name','ps.id AS stockID','ps.category_id AS catID','pc.name AS catName','ps.current_stock')
  				->groupBy('ps.category_id','ps.product_id')
                ->orderBy('p.id')
  				->get();

  		return Response::json($Acc);
}

//=====06===Search Product By Name or Code========
public function searchMyProdName($name)
{			
			$supplierId=Auth::user()->account_id;
      $prod= DB::table('tbl_products_master AS p')
  				->join('tbl_products_stock AS ps', function($join){
					$join->on('ps.product_id', '=', 'p.id');
					$join->where('ps.status','=', 1);
					})
          ->join('tbl_categories AS pc', function($join){
					$join->on('pc.id', '=', 'ps.category_id');
					})
					->join('tbl_product_assoc_account AS myp', function($join) use($supplierId){
						$join->on('myp.stock_id', '=', 'ps.id')->where('myp.account_id',$supplierId);
						})
  				->where('p.name','LIKE','%'.$name.'%')
				->where('ps.status',1)
				->orWhere('p.code','LIKE','%'.$name.'%')
				->where('ps.status',1)->where('p.status',1)
  				->select('p.id','p.code','p.name','ps.id AS stockID','ps.category_id AS catID','pc.name AS catName','ps.current_stock')
  				->groupBy('ps.category_id','ps.product_id')
                ->orderBy('p.id')
  				->get();

  		return Response::json($prod);
}
//=====07===Product Details By ID================
    public function itemdataByID($id){
      $itemMAster=ProductModel::where('id',$id)->get();
  		return Response::json($itemMAster);
    }

	 public function saleInvoice($type,$format,$id)
    {
        $billData =Category::all();
        return view('admin.bill_formats.saleinvoice01', compact('billData'));
    }

//=====08 ====Product  Pending order========
	public function saleProductPendingOrder($pdStockID){
		$stock=DB::table('tbl_sale_temp_detail AS sod')
				   ->join('tbl_products_master AS pm', 'pm.id', '=', 'sod.product_id')
				   ->join('tbl_sale_temp_order AS so', 'so.id', '=', 'sod.order_id')
				   ->join('tbl_color AS clr', 'clr.id', '=', 'sod.attribute_id')
				   ->join('tbl_categories AS ct','ct.id','=','sod.category_id')
				   ->join('tbl_account AS ac','ac.id','=','so.supplier_id')
				   ->where('sod.stock_id','=',$pdStockID)
				   ->where('sod.status','=','active')
				   ->select('sod.sQty','pm.name as product_name','pm.code as product_code','clr.name as color_name','ct.name as category_name','ac.name as AccountName','ac.type as accountType','so.invoice_No as invoiceNo','so.saleDate as orderDate')
				   ->get();
		return Response::json($stock);
		}
	//======09 == Product_category color wise pending sale order=====

    public function stockPendingSaleORder1($pdStockID){

          $subcat=DB::table('tbl_sale_temp_detail AS sod')
				   ->where('sod.stock_id','=',$pdStockID)
				   ->where('sod.status','=','active')
				   ->select(DB::raw('sum(sod.sQty) pending_order'))
				   ->get();

      return Response::json($subcat);
    }

	//======09.1 == Product_category color wise pending sale/Purchase order=====

    public function stockPendingSaleORder($pdStockID){
      $subcat=DB::select( DB::raw('select (select sum(sod.sQty) from tbl_sale_temp_detail as sod where sod.status="active" and sod.stock_id="'.$pdStockID.'") as pndSaleOdr,
          (select sum(pod.sQty) from tbl_purchase_temp_detail as pod where pod.status="active" and pod.stock_id="'.$pdStockID.'") as pndPurOdr'));

      return Response::json($subcat);
    }

//=====10 ====Product  Pending Purchase order========
	public function purchaseProductPendingOrder($pdStockID){
		$stock=DB::table('tbl_purchase_temp_detail AS sod')
				   ->join('tbl_products_master AS pm', 'pm.id', '=', 'sod.product_id')
				   ->join('tbl_purchase_temp_order AS so', 'so.id', '=', 'sod.order_id')
				   ->join('tbl_color AS clr', 'clr.id', '=', 'sod.attribute_id')
				   ->join('tbl_categories AS ct','ct.id','=','sod.category_id')
				   ->join('tbl_account AS ac','ac.id','=','so.supplier_id')
				   ->where('sod.stock_id','=',$pdStockID)
				   ->where('sod.status','=','active')
				   ->select('sod.sQty','pm.name as product_name','pm.code as product_code','clr.name as color_name','ct.name as category_name','ac.name as AccountName','ac.type as accountType','so.invoice_No as invoiceNo','so.saleDate as orderDate')
				   ->get();
		return Response::json($stock);
		}

//======11 == Product_category color wise pending Purchase order=====

    public function stockPendingPurchaseORder($pdStockID){

          $subcat=DB::table('tbl_purchase_temp_detail AS sod')
				   ->where('sod.stock_id','=',$pdStockID)
				   ->where('sod.status','=','active')
				   ->select(DB::raw('sum(sod.sQty) pending_order'))
				   ->get();

      return Response::json($subcat);
    }

//===12=======Partywise Productwise Pending order=======================
    public function partyProductPendingOrder($type,$partyID,$itemids){


			if($type=='sale-order'){$OdrDt='tbl_sale_temp_detail';$Odr='tbl_sale_temp_order';}
			else if($type=='purchase-order'){ $OdrDt='tbl_purchase_temp_detail';$Odr='tbl_purchase_temp_order';}
			else{
				$OdrDt='';
			}

		if($OdrDt!=''){
            $ids=explode('|',$itemids);
            $data=DB::table($OdrDt.' AS sd')
				->where('sd.account_id','=',$partyID)
				->where('sd.status','=','active')
				//->where('sd.product_id',$ids[1])->where('sd.category_id',$ids[2])->where('sd.attribute_id',$ids[3])
				->where('sd.stock_id',$ids[0])
				->select('sd.id','sd.sRate','sd.sQty',DB::raw("DATE_FORMAT(sd.created_at, '%d-%b-%Y') as sDate"))
				->get();

      }
        return Response::json($data);
    }

	//===13==========Account===CLOSIGN/CURRENT BALANCE=================
	public function partyCalculateClosing($id,$from=null,$to=null)
	{
		$account=Account::where('id',$id)->first();
		$april1=$_ENV['APP_YEAR'];
		$fromDate=$april1;
		$toDate=date('Y-m-d');

		if(isset($from,$to) && !empty($from) && !empty($to)){
			$fromDate=$from;
			$toDate=$to;
		}

		$prevDate=date('Y-m-d',strtotime($fromDate.'-1 day'));
	$totalDebit=FinancialLogsModel::where('party_id',$id)
																	->where('txn_type','debit')
																	->where('status',1)
																	->where('txn_date','>=',$april1)
																	->where('txn_date','<=',$toDate)
																	->sum('txn_amount');

		$totalCredit=FinancialLogsModel::where('party_id',$id)
																	->where('txn_type','credit')
																	->where('status',1)
																	->where('txn_date','>=',$april1)
																	->where('txn_date','<=',$toDate)
																	->sum('txn_amount');
		$acc= Account::where('id',$id)->select('openingBalance','opening_type')->first();

/*
		$rt = DB::select( DB::raw("SELECT ac.openingBalance,ac.opening_type, (select sum(db.txn_amount) from tbl_financial_logs as db where db.party_id=ac.id and db.txn_type='debit' and db.status=1 and (db.txn_date>='".$april1."' and db.txn_date<='".$toDate."')) as totalDebit,(select sum(cr.txn_amount) from tbl_financial_logs as cr where cr.party_id=ac.id and cr.txn_type='credit' and cr.status=1 and (cr.txn_date>='".$april1."' and cr.txn_date<='".$toDate."')) as totalcredit from tbl_account as ac WHERE ac.id='".$id."'"));

		if($rt[0]->totalDebit!=null){$totalDr=$rt[0]->totalDebit;}else{$totalDr=0;}
		if($rt[0]->totalcredit!=null){$totalCr=$rt[0]->totalcredit;}else{$totalCr=0;}


		if($rt[0]->opening_type=='Dr'){
			$closingBalance=$rt[0]->openingBalance+($totalDr-$totalCr);
		}else{
			$closingBalance=$rt[0]->openingBalance-($totalDr-$totalCr);
		}
		$a['opening']=$rt[0]->openingBalance;
		$a['debitTotal']=$totalDr;
		$a['creditTotal']=$totalCr;
		$a['closing']=$closingBalance;
   */
		//=======New Calculation ====
		
		if(!empty($acc->openingBalance)){ $opBal=$acc->openingBalance; }else{ $opBal=0;	}
		if($acc->opening_type=='Dr'){
			$closingBalance_new = $opBal + ($totalDebit-$totalCredit);
		}else{
			$closingBalance_new = $opBal - ($totalDebit-$totalCredit);
		}

		if($closingBalance_new >= 0 ){
			$closingType='Dr';
		}else{
			$closingType='Cr';
		}

		$a['opening']=$acc->openingBalance;
		$a['opening_type']=$acc->opening_type;
		$a['debitTotal']=$totalDebit;
		$a['creditTotal']=$totalCredit;
		$a['closing']=$closingBalance_new;
		$a['closing_type']=$closingType;
		return $a;
	}



	public function partyCalculateClosingNitin($id,$from=null,$to=null)
	{
		$account=Account::where('id',$id)->first();
		$april1=$_ENV['APP_YEAR'];
		$fromDate=$april1;
		$toDate=date('Y-m-d');

		if(isset($from,$to) && !empty($from) && !empty($to)){
			$fromDate=$from;
			$toDate=$to;
		}

		$prevDate=date('Y-m-d',strtotime($fromDate.'-1 day'));
	$totalDebit=FinancialLogsModel::where('party_id',$id)
																	->where('txn_type','debit')
																	->where('status',1)
																	->where('txn_date','>=',$april1)
																	->where('txn_date','<=',$toDate)
																	->sum('txn_amount');

		$totalCredit=FinancialLogsModel::where('party_id',$id)
																	->where('txn_type','credit')
																	->where('status',1)
																	->where('txn_date','>=',$april1)
																	->where('txn_date','<=',$toDate)
																	->sum('txn_amount');
		$acc= Account::where('id',$id)->select('openingBalance','opening_type')->first();

/*
		$rt = DB::select( DB::raw("SELECT ac.openingBalance,ac.opening_type, (select sum(db.txn_amount) from tbl_financial_logs as db where db.party_id=ac.id and db.txn_type='debit' and db.status=1 and (db.txn_date>='".$april1."' and db.txn_date<='".$toDate."')) as totalDebit,(select sum(cr.txn_amount) from tbl_financial_logs as cr where cr.party_id=ac.id and cr.txn_type='credit' and cr.status=1 and (cr.txn_date>='".$april1."' and cr.txn_date<='".$toDate."')) as totalcredit from tbl_account as ac WHERE ac.id='".$id."'"));

		if($rt[0]->totalDebit!=null){$totalDr=$rt[0]->totalDebit;}else{$totalDr=0;}
		if($rt[0]->totalcredit!=null){$totalCr=$rt[0]->totalcredit;}else{$totalCr=0;}


		if($rt[0]->opening_type=='Dr'){
			$closingBalance=$rt[0]->openingBalance+($totalDr-$totalCr);
		}else{
			$closingBalance=$rt[0]->openingBalance-($totalDr-$totalCr);
		}
		$a['opening']=$rt[0]->openingBalance;
		$a['debitTotal']=$totalDr;
		$a['creditTotal']=$totalCr;
		$a['closing']=$closingBalance;
   */
		//=======New Calculation ====
		
		if(!empty($acc->openingBalance)){ $opBal=$acc->openingBalance; }else{ $opBal=0;	}
		if($acc->opening_type=='Dr'){
		    if($totalCredit > $totalDebit){
		        $closingBalance_new =  ($totalCredit - $totalDebit) - $opBal;
		    }
		    else {
		    
			$closingBalance_new = $opBal + ($totalDebit-$totalCredit);
		    }
		}else{
			$closingBalance_new = $opBal - ($totalDebit-$totalCredit);
		}

		if($closingBalance_new >= 0 ){
			$closingType='Dr';
		}else{
			$closingType='Cr';
		}

		$a['opening']=$acc->openingBalance;
		$a['opening_type']=$acc->opening_type;
		$a['debitTotal']=$totalDebit;
		$a['creditTotal']=$totalCredit;
		$a['closing']=$closingBalance_new;
		$a['closing_type']=$closingType;
		return $a;
	}
	
	//=====14====Party Bill payment status===========
    public function partyBillOverdueStatus($billType,$ret=null,$id)
    {
			if($billType=='sale')
			{
				$tblOdr='tbl_sale_order';
				$odrType='sale';
			}else if($billType=='purchase'){
				$tblOdr='tbl_purchase_order';
				$odrType='purchase';
			}else{}

		$acData=Account::where('id',$id)->select('creditDays','overdue_amount')->first();

		if(empty($acData->creditDays) || $acData->creditDays==null || $acData->creditDays<=0){
			$crDays=0;
		}else{
			$crDays=$acData->creditDays;
		}

		if($ret=='amount')
		{
			$a['totalAmt']=$data=DB::table($tblOdr.' as bl')
					->where('bl.supplier_id',$id)
					->where('bl.payment_status','=',"0")
					->where('bl.order_type',$odrType)
					->whereRaw("DATEDIFF('" .date('Y-m-d'). "',saleDate)  >".$crDays)
					->sum('bill_amount');
		}else if($ret=='onlyAmount'){
			$a['totalAmt']=$data=DB::table($tblOdr.' as bl')
					->where('bl.supplier_id',$id)
					->where('bl.payment_status','=',"0")
					->where('bl.order_type',$odrType)
					->whereRaw("DATEDIFF('" .date('Y-m-d'). "',saleDate)  >".$crDays)
					->sum('bill_amount');
		}else if($ret=='oldNewOverDueAmt'){
			$a['totalAmt']=$data=DB::table($tblOdr.' as bl')
					->where('bl.supplier_id',$id)
					->where('bl.payment_status','=',"0")
					->where('bl.order_type',$odrType)
					->whereRaw("DATEDIFF('" .date('Y-m-d'). "',saleDate)  >".$crDays)
					->sum('bill_amount');
			$a['totalAmt']+=$acData->overdue_amount;
		}else if($ret=='onlyAmount'){
			$a['totalAmt']=$data=DB::table($tblOdr.' as bl')
					->where('bl.supplier_id',$id)
					->where('bl.payment_status','=',"0")
					->where('bl.order_type',$odrType)
					->whereRaw("DATEDIFF('" .date('Y-m-d'). "',saleDate)  >".$crDays)
					->sum('bill_amount');
		
		}else{
			$a['totalAmt']=$data=DB::table($tblOdr.' as bl')
					->where('bl.supplier_id',$id)
					->where('bl.payment_status','=',"0")
					->whereRaw("DATEDIFF('" .date('Y-m-d'). "',saleDate)  >".$crDays)
					->sum('bill_amount');

			$a['bills']=$data=DB::table($tblOdr.' as bl')
					->select('bl.*')
					->where('bl.supplier_id',$id)
					->where('bl.payment_status','=',"0")
					->whereRaw("DATEDIFF('" .date('Y-m-d'). "',saleDate)  >".$crDays)
					->get();
		}
			if($ret=='onlyAmount' || $ret=='oldNewOverDueAmt' ){return $a;}
  		return Response::json($a);

    }

	//=========15======UPDATE REMINDER DATE /Note for ORder=====
	public function updateORderReminder($odrtype,$id,$date,$note){
		$status='false';
		$msg='';
		$tblD='';

		if($odrtype=='purchase-order'){$tblD='tbl_purchase_temp_order';}
		else if($odrtype=='sale-order'){$tblD='tbl_sale_temp_order';}
		else{}

		if($tblD!=''){
			DB::table($tblD)->where('id',$id)->update(array('reminder_date'=>$date,'reminder_note'=>$note));
			$msg=' updated successfully';
			$status='true';

		}


		$data['message']=$msg;
		$data['status']=$status;
		return Response::json($data);
	}

//=========15.1=====Update Reminder note for  SALE bill =====
public function updateSaleBillReminder($odrtype,$id,$note)
{
	$status='false';
	$msg='';
	$tblD='';

	$tblD='tbl_sale_order';

	if($tblD!='')
	{
		DB::table($tblD)->where('id',$id)->update(array('reminder_date'=>date('Y-m-d'),'reminder_note'=>$note));
		$msg=' updated successfully';
		$status='true';
	}
	$data['message']=$msg;
	$data['status']=$status;
	return Response::json($data);
}


//========Activate inactive product variants=====
	public function activateStock($id){
		$status='false';
		$code='0';
		$msg='';

			$update=StockModel::where('id',$id)->update(array('status'=>'1',));
			if($update){
			$msg='Data updated successfully';
			$status='true';
			}

		$data['code']=$code;
		$data['message']=$msg;
		$data['status']=$status;
		return Response::json($data);
	}

	//====== Party Active Last Inquery=====
	public function PartyActiveInquery($supplierID){


          $a=DB::table('tbl_sale_inquery as i')					
					->leftJoin('employees as emp', 'emp.id', '=', 'i.salesman_id')
					->where('i.supplier_id',$supplierID)
				   ->where('i.billing_status','<>','1')
					 ->select('i.*','emp.name as salesMan')
				   ->get();

      return Response::json($a);
	}

	//====== Party Pending SALE ORDER =====
	public function PartyPendingSaldOdr($supplierID){


		$a=DB::table('tbl_sale_temp_detail as std')
		->leftJoin('tbl_sale_temp_order AS sto', 'sto.id', '=', 'std.order_id')
		->leftJoin('tbl_account as ac', 'ac.id', '=', 'sto.supplier_id')
		->leftJoin('employees as emp', 'emp.id', '=', 'sto.salesman_id')
		->where('std.status','active')
		->where('sto.supplier_id',$supplierID)
		->groupBy('std.order_id')
		->orderBy('std.order_id', 'desc')
		->select('sto.*','emp.name as salesMan',DB::raw('count(std.id) as itemCount'))
		->get();

return Response::json($a);
}
	//======== VIEW Product/product Category  Image=============
  public function productImage($proid,$catid=null)
  {
    $data=DB::select(DB::raw("select pm.name as product_name,ct.name as category_name,pm.image as prodImage,pg.image as prodcatImage FROM tbl_products_master AS pm
    LEFT JOIN tbl_products_image_gallery AS pg ON pg.product_id=pm.id and pg.category_id='".$catid."'
    INNER JOIN tbl_categories AS ct ON ct.id='".$catid."'
    where pm.id='".$proid."'"));
    return Response::json($data);
  }


//========Already Pending Order check by acc and stock it =========
	public function PendingOrdByAccStock($repType,$stockId,$accid){
    $stock=StockModel::where('id',$stockId)->first();
    if($repType=='sod'){
      $stock=DB::table('tbl_sale_temp_detail AS sod')
             ->join('tbl_products_master AS pm', 'pm.id', '=', 'sod.product_id')
             ->join('tbl_sale_temp_order AS so', 'so.id', '=', 'sod.order_id')
             ->join('tbl_color AS clr', 'clr.id', '=', 'sod.attribute_id')
             ->join('tbl_categories AS ct','ct.id','=','sod.category_id')
             ->join('tbl_account AS ac','ac.id','=','so.supplier_id')
             ->where('sod.product_id','=',$stock->product_id)
             ->where('sod.category_id','=',$stock->category_id)
             ->where('sod.account_id','=',$accid)
             ->where('sod.status','=','active')
             ->select('sod.sQty','pm.name as product_name','pm.code as product_code','clr.name as color_name','ct.name as category_name','ac.name as AccountName','so.invoice_No as invoiceNo','so.saleDate as orderDate')
             ->get();
    }else if($repType=='pod'){
      $stock=DB::table('tbl_purchase_temp_detail AS sod')
             ->join('tbl_products_master AS pm', 'pm.id', '=', 'sod.product_id')
             ->join('tbl_purchase_temp_order AS so', 'so.id', '=', 'sod.order_id')
             ->join('tbl_color AS clr', 'clr.id', '=', 'sod.attribute_id')
             ->join('tbl_categories AS ct','ct.id','=','sod.category_id')
             ->join('tbl_account AS ac','ac.id','=','so.supplier_id')
             ->where('sod.product_id','=',$stock->product_id)
             ->where('sod.category_id','=',$stock->category_id)
             ->where('sod.account_id','=',$accid)
             ->where('sod.status','=','active')
             ->select('sod.sQty','pm.name as product_name','pm.code as product_code','clr.name as color_name','ct.name as category_name','ac.name as AccountName','so.invoice_No as invoiceNo','so.saleDate as orderDate')
             ->get();
    }else{
      $stock='';
    }
  return Response::json($stock);
  }


 public function updateArivaldate($data){
	$status='false';
	if(!empty($data)){
		
		$d=explode('|',$data);
		$arvDate=date('Y-m-d',strtotime($d[3]));
		$userid=Auth::user()->id;
		try{
		$s=DB::table('tbl_purchase_temp_detail')->where('order_id',$d[0])->where('product_id',$d[1])->where('category_id',$d[2])
				->update([
							'arrival_date'=>$arvDate,
							'arrival_byid'=>$userid,
						]);
		$status='true';
		}catch(\Exception $e) {
			return $e->getMessage();
		  }
	}
	return $status;
 }

 /* ===== GET Counter Stock ======= */
 public function getCounterStock($stockId){
	$cs=DB::table('tbl_products_stock as st')
			->select('st.current_stock',DB::raw("(select sum(sQty) from tbl_sale_temp_detail where stock_id='".$stockId."' and status='active' ) as saleOrder "),DB::raw("(select sum(sQty)  from tbl_purchase_temp_detail where stock_id='".$stockId."' and status='active') as purchaseOrder "),)
			->where('id',$stockId)
			->first();
	return (($cs->current_stock + $cs->purchaseOrder) - $cs->saleOrder);
 }

 //=======Check StockAssociation with Account============
 public function checkStockAssociation($acid,$stockid){
	$stock=DB::table('tbl_product_assoc_account')
				->where('account_id',$acid)
				->where('stock_id',$stockid)
				->count();
	
	return Response::json($stock);
}

//=======Update Account Overdue Note============
public function partyOverdueNote($acid,$note){
	$stats='false';
	$note =trim(addslashes($note));
	if($note=='***'){
		$note='';
	}
	$stock=DB::table('tbl_account')
        ->where('id', $acid)  
        ->limit(1)
        ->update(array('overdue_note' => $note));

	if($stock){
		$status='true';
	}
	$a['status']=$status;
	$a['note']=$note;
	return Response::json($a);
}

}
