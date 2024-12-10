<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;

use App\Models\Account;
use App\Models\NewInquiry;
use App\Models\Product;
use App\Models\StockModel;
use DB;
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

    //=====02====Account/Ledger/Party Details===========
    public function AccountDetail($id)
    {
      $acData=Account::where('id',$id)->with('statedata','citydata')->first();
	    $currentBalance=partyCalculateClosing($id,'','');
	    $acData['current_balance']=$currentBalance['closing'];
      $acData['showbalance']=$currentBalance['showbalance'];
  		return Response::json($acData);

    }

    //======03===Partywise Previous Sale / Purchase======
    public function lastItemSalePrice($type,$partyID,$itemids)
    {
      if($type=='sale'){$OdrDt='tbl_sale_detail';$Odr='tbl_sale_order';}else if($type=='purchase'){ $OdrDt='tbl_purchase_detail';$Odr='tbl_purchase_order';}else{}
            $ids=explode('|',$itemids);
            $data=DB::table($OdrDt.' AS sd')
        ->join($Odr.' AS so', function($join) use($partyID,$ids){$join->on('so.id', '=', 'sd.order_id')->where('so.supplier_id','=',$partyID);})
        ->where('sd.product_id',$ids[1])->where('sd.category_id',$ids[2])->where('sd.attribute_id',$ids[3])
        ->select('sd.order_id','sd.sRate','sd.sQty',DB::raw("DATE_FORMAT(so.saleDate, '%d-%b-%Y') as sDate"))
        ->orderBy('so.id','desc')->limit(5)
        ->get();

      return Response::json($data);
    }

    //======04===Partywise Previous Financial Transection ======
    public function accPrevPayment($type,$partyID,$itemids)
    {
      $reftype=explode('|',$itemids);
      $data=DB::table('tbl_financial_logs AS fl')
        ->leftjoin('tbl_account AS ac', function($join){$join->on('ac.id', '=', 'fl.party_id');})
        ->where('fl.party_id',$partyID)->whereIn('reference_type',$reftype)
        ->select('fl.*','ac.name as AcName',DB::raw("DATE_FORMAT(fl.created_at, '%d-%b-%Y') as sDate"))
        ->orderBy('fl.id','desc')
        ->limit(10)
        ->get();
    
      return Response::json($data);
    }

    //======05===Account Bills======
    public function AccountBills($type,$partyID,$itemids)
    {
      if($type=='sale')
      {
        $OdrDt='tbl_sale_detail';
        $Odr='tbl_sale_order';
      }else if($type=='purchase'){ 
        $OdrDt='tbl_purchase_detail';
        $Odr='tbl_purchase_order';
      }else{}

            $ids=explode('|',$itemids);
            $data=DB::table($OdrDt.' AS sd')
        ->join($Odr.' AS so', function($join) use($partyID,$ids){$join->on('so.id', '=', 'sd.order_id')->where('so.supplier_id','=',$partyID);})
        ->where('sd.product_id',$ids[1])->where('sd.category_id',$ids[2])->where('sd.attribute_id',$ids[3])
        ->select('sd.order_id','sd.sRate','sd.sQty',DB::raw("DATE_FORMAT(so.saleDate, '%d-%b-%Y') as sDate"))
        ->orderBy('so.id','desc')->limit(5)
        ->get();

      return Response::json($data);
    }

    //====Account Search for New Inquery============
    public function AccNewInqSearch($group,$keyword){
      $status=false;
      $html='';
      $html2='';
      $Acc=Account::where('name','LIKE','%'.$keyword.'%')
                  ->orWhere('acCode','LIKE','%'.$keyword.'%')
                  ->orWhere('phone','LIKE','%'.$keyword.'%')
                  ->orWhere('phone2','LIKE','%'.$keyword.'%')
                  ->orWhere('contactPerson','LIKE','%'.$keyword.'%')
                  ->orWhere('address','LIKE','%'.$keyword.'%')
                  ->with('statedata','citydata')->get();
      $newInq= NewInquiry::where('name','LIKE','%'.$keyword.'%')
      ->orWhere('phone','LIKE','%'.$keyword.'%')
      ->orWhere('phone2','LIKE','%'.$keyword.'%')
      ->orWhere('contactPerson','LIKE','%'.$keyword.'%')
      ->orWhere('address','LIKE','%'.$keyword.'%')
      ->with('state')
      ->get();
                  
      if($Acc){
        $status=true;
        $html='<h3> Registered Customer</h3><table class="w-100 table table-info table-hover">
                  <thead class="table-dark">
                      <tr>
                          <th>Sno</th>
                          <th>Name </th>
                          <th>Phone No </th>
                          <th>State/City </th>
                          <th>Address </th>
                      </tr>
                  </thead>';
         $i=1; 
         $tr='';          
        foreach($Acc as $ac)
        {
          $state='';
          $city='';
          if(!empty($ac->statedata)){
            $state=$ac->statedata->name;
          }
          if(!empty($ac->citydata)){
            $city=$ac->citydata->name;
          }
          $tr.='<tr class="accinfo" acid="'.$ac->id.'">
                  <td>'.$i.'</td>
                  <td >'.$ac->name.'</td>
                  <td>'.$ac->phone.'<br>'.$ac->phone2.'</td>
                  <td>'.$state.' | '.$city.'</td>
                  <td>'.$ac->address.'</td>
                </tr>';
                $i++;
        }
        $html.='<tbody>'.$tr.'</tbody>';
      }
      /*====New Inquery === */
      if($newInq){
        $status=true;
        $html2='<h3> Previous Inquery </h3>
        <table class="w-100 table table-warning table-hover">
                  <thead class="table-dark">
                      <tr>
                          <th>Inq No</th>
                          <th>Name </th>
                          <th>Phone No </th>
                          <th>State/City </th>
                          <th>Address </th>
                      </tr>
                  </thead>';
        $i=1; 
        $tr='';          
        foreach($newInq as $ac)
        {
          $state='';
          $city=$ac->city;
          if(!empty($ac->state)){
            $state=$ac->state->name;
          }
          if(!empty($ac->citydata)){
            $city=$ac->citydata->name;
          }
          $inqDate= date('d-M-y',strtotime($ac->saleDate));
          $tr.='<tr>
                  <td><a href="'.route('admin.inquiry-new.edit', $ac->id).'">'.$ac->invoice_No.'</a><br><small>'.$inqDate.'</small></td>
                  <td>'.$ac->name.'</td>
                  <td>'.$ac->phone.'<br>'.$ac->phone2.'</td>
                  <td>'.$state.' | '.$city.'</td>
                  <td>'.$ac->address.'</td>
                </tr>';
                $i++;
        }
        $html2.='<tbody>'.$tr.'</tbody>';
      }
    $a['acc']=$html;
    $a['inq']=$html2;  
    $a['status']=$status;  
    return $a;

    }


//========Search Product By Name ========
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

//========Search Product By Name ========
public function searchProdQrCode($qrcode,$catall=null)
{     
    $html=''; 
    $qrdata='' ;
    $qrstockid=\App\Models\QrCode::Where('qrcode',$qrcode)->first();
    
    if(!empty($qrstockid))
      {
        $qrdata=\App\Models\StockModel::where('id',$qrstockid->stock_id)->with('product','category')->first();
        
        if(!empty($catall)){
          $stock=\App\Models\StockModel::where('product_id',$qrdata->product_id)->where('category_id',$qrdata->category_id)->where('status',1)->get();      
        }else{
          $stock=\App\Models\StockModel::where('id',$qrstockid->stock_id)->with('product','attr','category')->get();
        }

      if($stock->count()>0)
      {   
        
          foreach($stock as $pd){

            $html.='<tr id="'.$pd->id.'" class="variantsRow">
                    <td class="itemId">'.$pd->id.'
                    <input type="hidden" name="stTaxRate" class="stTaxRate" value="'.$pd->tax_rate.'">
                    </td>
                    <td class="itemAtr">'.$pd->attr?->name.'</td>
                    <td class="itemAstock">'.$pd->current_stock.'</td>
                    <td>'.$pd->sale_price.'</td>
                    <td width="50"><input type="number" name="AdQty[]" class="inpt AdQty"></td>
                    <td width="50" class="hide"><input type="number" name="AdSprice[]" class="inpt AdSprice" value="'.$pd->sale_price.'" disabled></td>
                    <td width="50"><input type="number" name="AdNet[]" class="inpt AdNet" disabled></td>
                    <td class="itmStatus"></td>;
            </tr>';

          }
          $html.='<tr>
                    <td colspan="100%" class="text-center p-2 AdBtn"><span class="btn btn-success rounded btn-sm hide" id="addItemBtn" onclick="addItemToBill()">Add Items</span></td>
                  </tr>';  
       }
      }
      $a['data']=$html;
      $a['prodinfo']=$qrdata;
       return $a;
      
}


    public function searchProdVariants($prodid, $catid){
      $prod=StockModel::where('product_id',$prodid)->where('category_id',$catid)->with('attr')->get();
      $html='<tr><td colspan="100%">--- No data found---</td></tr>'; 
      if($prod->count()>0)
      {   
        $html='';
          foreach($prod as $pd){

            $html.='<tr id="'.$pd->id.'" class="variantsRow">
                    <td class="itemId">'.$pd->id.'
                    <input type="hidden" name="stTaxRate" class="stTaxRate" value="'.$pd->tax_rate.'">
                    </td>
                    <td class="itemAtr">'.$pd->attr?->name.'</td>
                    <td class="itemAstock">'.$pd->current_stock.'</td>
                    <td>'.$pd->sale_price.'</td>
                    <td width="50"><input type="number" name="AdQty[]" class="inpt AdQty"></td>
                    <td width="50" class="hide"><input type="number" name="AdSprice[]" class="inpt AdSprice" value="'.$pd->sale_price.'" disabled></td>
                    <td width="50"><input type="number" name="AdNet[]" class="inpt AdNet" disabled></td>
                    <td class="itmStatus"></td>;
            </tr>';

          }
          $html.='<tr>
                    <td colspan="100%" class="text-center p-2 AdBtn"><span class="btn btn-success rounded btn-sm hide" id="addItemBtn" onclick="addItemToBill()">Add Items</span></td>
                  </tr>';  
       }
       return $html;
    }  

    //=====Generate Qr Code whose qrcode is note generated ========
    public function GenerateQrString(){
      generateQRCodesForStock();
      return 'Qr created successfully';

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
      }else if($action=='purchase-order-itemRemove'){$tblD='tbl_purchase_order_detail';}
      else if($action=='purchase-itemRemove' || $action=='purchase-return-itemRemove'){$tblD='tbl_purchase_detail';}
      else if($action=='sale-order-itemRemove'){$tblD='tbl_sale_order_detail';}
      else if($action=='sale-itemRemove' || $action=='sale-return-itemRemove'){$tblD='tbl_sale_detail';}
      else if($action=='sale-inquery-itemRemove'){$tblD='tbl_sale_inquery_detail';}
      else if($action=='sale-new-inquery-itemRemove'){$tblD='tbl_new_inquery_detail';}
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

}

