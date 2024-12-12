@extends('layouts.master')
@section('content')
    @section('title')
    @lang('translation.Form_Layouts')
@endsection 
@section('content')
<style>
    .SchListDiv{
                display:none;
                position: absolute;
                background: #fff;
                border: solid 2px #652001;
                border-radius: 5px;
                margin-top: 30px;
                margin-left: 9px;
                z-index: 9;
                box-shadow: 3px 9px 10px 6px #1c1c1d;
                max-height: 500px;
                /* overflow-x: scroll; */
                padding: 10px;
                min-width:500px;
            }

#paymentForm .form-group{
    margin-top:10px;
}

.billForm .form-group label{
   
}

.bilno {
    font-size: 18px;
    color: #1aa79c;
}
.form-floating .form-control,.form-floating .form-select {
    height: calc(3.5rem + 0px);
    padding: 1rem .75rem;
    border-radius: 0.5rem;
    Border:none;
    border-bottom: solid 1px #34c38f;
    background: linear-gradient(1deg, #86ffd321, #fff, transparent);
}



        .highlight {
            background-color: #e9ecef;
        }
        .productRow {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom:solid 1px #dbd6d6;
            cursor:pointer;
        }
        .productRow:hover{
            background:#1aa79c;
            color:#fff;
            
        }
        .inpt{
            width:100%;
            line-height:1.0;
            border-radius:5px;
            border:none;
            border-bottom:solid 1px red;
            text-align:center;
        }
     
        #prodVarientsDiv td,#prodVarientsDiv th {
            padding: 10px 2px 0px 2px !important;
        }

        #addTblBody input, tfoot input {
            width: 90%;
            border: none;
            color: red;
            padding: 0px;
            background: transparent;
            text-align: center;
            /* pointer-events: none; */
        }
</style>
@include('components.breadcum')
<div class="row">
    <div class="col-12">
    </div>
    <div class="card" >
        {!! get_error_html($errors) !!}
        <div class="row">
            <div class="col-md-5">
                <form class="app-search d-none d-lg-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Customer Search By Name, Email, Phone no. address" id="clientSearchForm">
                        <span class="bx bx-search-alt"></span>
                    </div>
                </form>
            </div>
            <div class="col-md-7">
                <h3 class="card-title">    
                    <a href="{{ route('admin.sale.index') }}" class="btn btn-info float-end mt-2">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>Back</a>
                </h3>
            </div>
        <!-- === form == -->
        </div>
        <div class="customerSearchlist">
            <div class="row">
                <div class="col-md-8 regCustomer">
                        <!-- registered customer -->
                </div>
            </div>
        </div>
                            <!-- /.card-header -->

                            <!-- form start;-->
                            @php 
                                if($so->id && $so->account){
                                    $accountId= $so->account?->id;
								    $priceGroup=$so->account?->priceGroup;
                                }else{
                                    $accountId='';
                                    $priceGroup='';
                                }
                            @endphp
							@if(isset($so->id) && $so->id!='' && $action=='UpdateSale')
								<form role="form" action="{{ route('admin.sale.update', $so->id) }}" method="post" enctype="multipart/form-data" autocomplete="off" id="billingForm">
								 @method('PUT')
							@else
								<form role="form" action="{{ route('admin.sale.store') }}" method="post" enctype="multipart/form-data" autocomplete="off" id="billingForm">
								
							@endif
                                <input type="hidden" name="action" id="action" value="{{$action ?? ''}}" />
                                <input type="hidden" name="requestID" value="{{$requestID??''}}" />
                                @csrf

               
                    <div class="card-body billForm bglight1">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-4"><label>Serial No.</label></div>
                                    <div class="col-md-8">
                                        <span class="bilno">{{$nextBill}}</span>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-md-4">
                                        <label>Date <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            @php if($so->saleDate){$date=$so->saleDate;}else{$date=date('Y-m-d');} @endphp
                                            <input type="text" class="form-control datepicker" name="saleDate" value="{{ date('d-M-Y',strtotime(old('saleDate',$date)))}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-md-4">
                                        <label>Sales Person <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group" >
                                            <select class="select2 form-control" name="salesman_id" id="salesman_id" >
                                                <option value="">--Select Salesman---</option>
                                                @foreach($employees as $sman)
                                                    <option maxAllow="{{$sman->maxInqAllowed}}" AsignedInq="{{$sman->assign_inquery_count}}" value="{{$sman->id}}" @php if(old('salesman_id',$so->salesman_id)==$sman->id){echo 'selected';}else{} @endphp>{{$sman->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 border">
                                <div class="p-1 bg-dark text-white">Customer Information</div>
                                <div class=""  id="acinfo">
                                <input type="hidden" id="account_id" name="account_id" value="{{old('account_id',$accountId ?? '')}}" class="form-control">
                                <input type="hidden" id="price_group" name="price_group" value="{{old('price_group',$priceGroup ?? '3')}}" class="form-control">
                                <div id="resultDisplay"></div>
                                    <div id="showAcData">
                                    @php 
                                        $prodSearchDesabled='';
                                    @endphp
                                        @if($so->account)
                                            <span class="pull-left">
                                                <strong>{{$so->account?->name}}</strong>
                                                <br>{{$so->account?->phone.','.$so->account?->phone2.' ('.$so->account?->email.')'}}<br>
                                                {{ $so->account?->contactPerson }}
                                                <br>Address:-{{$so->account?->address.' '.$so->account?->citydata?->name.' '.$so->account?->statedata?->name}}
                                            </span>
                                        @else 
                                        @endif
                                    </div>
                                </div>   
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="row">
                                        <div class="col-md-12" id="prevDtl" style="min-height:100px">
                                            <label>Information</label>
                                        </div>
                                        <div class="col-md-12" id="pendSaleOdr">													
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body border">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Product Search By Name<span class="text-danger">*</span></label>
                                    <input id="prodData" name="prodData" class="form-control" placeholder="Product By Name">
                                </div>
                               
                                <div id="suggestions" class="SchListDiv mt-2">
                                        <!--- Data Render on  Product search -->
                                </div>
                                <div id="selectedProd">
                                    <!--- Data Render on Searched Product Click -->
                                </div>
                                <div class="form-group">
                                    <label>Search QRCODE <i class="fa fa-qrcode text-danger"></i> <span class="text-danger">*</span>
                                    <input type="checkbox" name="catall" id="catall">All</label>
                                    <input id="prodQrData" name="prodQrData" class="form-control" placeholder="Search By QRcode">
                                </div>
                            </div>
                            <div class="col-md-9" id="prodVarientsDiv">
                                <div class="row">
                                    <div class="col-12">
                                        <input type="hidden" id="seletedProdName" value="">
                                        <input type="hidden" id="seletedCatName" value="">
                                        <table class="table table-hover table-striped border">
                                            <thead class="bg-primary text-white">
                                                <tr>
                                                    <th>StockID</th>
                                                    <th>Atrribute</th>
                                                    <th>stock</stock>
                                                    <th width="10%">Price</th>
                                                    <th width="10%">Qty</th>
                                                    <th width="10%" class="hide">Rate</th>
                                                    <th width="15%">Value</th>
                                                    <th>#</th>
                                                </tr>
                                            </thead>
                                            <tbody id="prodAllVariants">
                                                <td class="text-center i" colspan="100%">--- Please select to product--</td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
									<div class="row hide" id="srchPd">
										<div class="col-md-3">
											<div class="form-group">
                                            <label>Product Detail <span class="text-danger">*</span></label>
                                            <input id="allData" name="allData" hidden>
											  <input class="form-control" id="itemInfo" name="itemInfo" disabled>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
											  <label>Category <span class="text-danger">*</span></label>
											  <input class="form-control" id="itemCatInfo" name="itemCatInfo" disabled>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
											  <label>color <span class="text-danger">*</span></label>
											  <input class="form-control" id="itemAttInfo" name="itemAttInfo" disabled>
											</div>
										</div>
										<div class="col-md-1">
											<div class="form-group">
                                                <label>Qty &nbsp;<small id="avlQty" class="pul-right bg-warning"></small></label>
                                                <input type="text" class="form-control" name="pQty" id="pQty" value="{{old('pQty')}}" onkeyup="QtyRateCalculate()">
											</div>
										</div>
										<div class="col-md-1" hidden>
											<div class="form-group">
												<label>MRP</label>
												<input type="text" class="form-control" name="Mrp" id="Mrp" value="{{ old('Mrp')}}">
												<input type="text" class="form-control" name="pTaxRate" id="pTaxRate" value="{{ old('pTaxRate')}}">
											</div>
										</div>
										<div class="col-md-1">
											<div class="form-group">
												<label>Rate  &nbsp;<small id="priceGroup" class="bg-danger rounded-circle"></small></label>
												<input type="text" class="form-control" name="pRate" id="pRate" value="{{ old('pRate')}}" onkeyup="QtyRateCalculate()">
											</div>
										</div>
										<div class="col-md-1" hidden>
											<div class="form-group">
												<label>Discount</label>
												<input type="text" class="form-control" name="pDiscount" id="pDiscount" value="0" onkeyup="QtyRateCalculate()">
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Net Amt</label>
												<input type="text" class="form-control" name="pNetAmount" id="pNetAmount" value="{{ old('pNetAmount')}}" disabled>
											</div>
										</div>
										<div class="col-md-1">
											<div class="form-group">
												<label></label>
												<button type="button" class="btn-sm btn-warning hide" id="addBtn" onclick="addNewRow()"><i class="fa fa-plus" aria-hidden="true"></i>Add</button>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<table id="example1" class="table table-bordered table-striped text-center  bg-light">
											<thead class="bg-primary text-white">
												<tr>
													<th width="50px">S.no</th>
													<th width="25%">Product</th>
													<th width="20%">Category</th>
													<th width="15%">Color</th>
													<th width="8%">Qty</th>
													<th hidden>MRP</th>
													<th>Rate</th>
													<th hidden>Dis.</th>
													<th>Net Amt</th>
													<th>#</th>
												</tr>
											</thead>
											<tbody id="addTblBody">
											@php
											$n=1;
											if(isset($sod) && count($sod)>=1)
											{
												foreach($sod as $stk)
												{
													$ActualodrQty='';
													
													if($stk->taxAmt>0){
														$taxCat=' *';
													}else{
														$taxCat='';
													}
												 $AdAllID=$stk->stock_id.'|'.$stk->product_id.'|'.$stk->category_id.'|'.$stk->attribute_id;
												echo '<tr><td>'.$n.'</td>
														<td class="text-start"><input type="hidden" name="oldID[]" class="old" value="'.$stk->id.'">
                                                            <input type="hidden" name="stockID[]" value="'.$stk->stock_id.'">
															<input type="hidden" name="AdTaxRate[]" value="'.$stk->taxRate.'">
															<input type="hidden" name="AdTaxAmt[]" value="'.$stk->taxAmt.'">
															'.$stk->prodName.'</td>
													<td class="text-start">'.$stk->catName.' '.$taxCat.'</td>
													<td class="text-start">'.$stk->attrName.'</td>
													<td>'.$ActualodrQty.'<input name="AdProdQty[]" value="'.$stk->sQty.'" class="apQty" onkeyup="editIt(this)"></td>
													<td hidden></td>
													<td><input name="AdpRate[]" value="'.$stk->sRate.'" class="apRate" onkeyup="editIt(this)"></td>
													<td hidden></td>
													<td><input name="AdNetAmt[]" value="'.$stk->sNetAmount.'" class="apAmt" onkeyup="editIt(this)" readonly></td>
													<td><i class="fa fa-trash text-danger" aria-hidden="true" onclick="DeleteRow(this);" role="button"></i></td>
                                                </tr>';
													$n++;
												}
											}
											@endphp
											</tbody>
											<tfoot>
                                            <tr id="blankItemRow">
                                                <td colspan="100%" class="text-center">---- Bill Items ----</td>
                                            </tr>
											<tr>
											<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td><input type="text" name="sumQtyTotal"></td>
													<td hidden><input type="text" name="sumRateTotal"></td>
													<td hidden><input type="text" name="sumDisTotal"></td>
													<td><input type="text" name="sumNetTotal"></td>
													<td></td>
												</tr>
											</tfoot>
										</table>
										</div>
										<div class="col-md-4">
											<div class="form-group">
                      						 <label>Note :-</label>
											  <textarea class="form-control" row="2" name="remark">{{ old('remark',$so->remark)}}</textarea>
											</div>
										</div>
										<div class="col-md-4">
											<div class="row">
												<div class="col-md-6">Other Charges</div>
												<div class="col-md-6">
													<input type="text" name="otherCharges" class="form-control" value="{{old('otherCharges',$so->other_charges)}}" onkeyup="calculate()">
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">Freight</div>
												<div class="col-md-6">
													<input type="text" name="freight" class="form-control" value="{{old('freight',$so->freight)}}" onkeyup="calculate()">
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">Parcels</div>
												<div class="col-md-6">
													<input type="text" name="parcels" value="{{old('parcels',$so->parcels)}}" class="form-control">
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="row" hidden>
												<div class="col-md-6">Total Discount</div>
												<div class="col-md-6">
                                                    <span ID='showTotalDis' hidden>0.00</span>
                                                    <input type="text" id="DisTotal" name="DisTotal"  hidden>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">Bill Discount <span class='text-danger AcDisRate'></span></div>
												<div class="col-md-6">
													<input type="text" name="billDiscount" class="form-control" value="{{old('discount',$so->discount)}}" onkeyup="calculate()">
                          							<input type="hidden" id="DisRate" name="DisRate" value="{{old('DisRate',$disRate ?? 0)}}">
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">GST</div>
												<div class="col-md-6">
												 <input type="number" name="sumTaxAmount" class="form-control" id="sumTaxAmount" readonly>
												</div>
												<div class="col-md-6">G Total ({{$so->bill_amount}})</div>
												<div class="col-md-6">
												<input type="hidden" name="bill_amount" class="form-control" id="bill_amount" value="{{old('bill_amount',$so->bill_amount)}}" >
												<h3 class="grandTotal"></h3>
												</div>
											</div>
										</div>
									</div>
									<div class="row">

									</div>
                                 </div>
                                <!-- /.card-body -->
            
        </div>
        <!-- /.card -->
        <div class="kt-portlet__foot">
            <div class=" ">
                <div class="row">
                    <div class="wd-sl-modalbtn">
                        <button type="submit" class="btn btn-primary  waves-effect waves-lightt">@if($action=='OrderToSaleBill') Convert to Sale Invoice @elseif($action=='UpdateSale') Update Invoice @else Create Invoice @endif</button>
                        <a href="{{route('admin.inquiry-new.index')}}" id="close"><button type="button" class="btn btn-outline-secondary waves-effect">Cancel</button></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Button to Open the Modal -->


    </form>
</div>
</div>
</div>
@endsection
@section('script')
<script>
//====On Page load===
$(function(){
    calculate();
});


/*=======Search Account/customer Data=======*/
	$('#clientSearchForm').on('keyup', function(){
		var nameKey = $(this).val();
		var type= '*';
        $('.regCustomer').html("");
        $('.PrevInq').html("");
		if(nameKey.length>=4){
		$('.customerSearchlist').show(500);
		$.get("{{url('admin/search-account-newinquery')}}/" +type+'/'+nameKey , function(data){

            $('.regCustomer').html(data.acc);
            $('.PrevInq').html(data.inq);
          
		});
		}else{
			$('.customerSearchlist').hide();
		}
	});
</script>
<script>

/* =====  ===== */
    $('.datepicker').datepicker({
        toDisplay: 'dd-mm-yyyy',
         toValue: 'yyyy-mm-dd',
         format: 'dd-M-yyyy'
    });

    
</script>

<script>
    /* ====== New Customer Add Form Data ====== */
    $(document).ready(function() {
        $('.showAcInfo').click(function() {
           
        });
    });

</script>

<script>
   
    let currentIndex = -1;

    $('#prodData').on('input', function (e) {
        var query = $(this).val();
        if (query.length < 3) {
            $('#suggestions').empty().hide();
            return;
        }

        $.ajax({
            url: "{{url('admin/search-prod-name')}}/"+query,
            method: 'GET',
            //data: { name },
            success: function (data) {
                var filteredProducts = data.filter(product => 
                    product.name.toLowerCase().includes(query.toLowerCase())
                );

                if (filteredProducts.length > 0) {
                    var suggestionList = filteredProducts.map((product, index) => `
                        <div class="row productRow" data-prodid="${product.id}" data-catid="${product.catID}">
                            <div class="col-2 spCode">${product.code}</div>
                            <div class="col-5 spName">${product.name}</div>
                            <div class="col-5 spCatname">${product.catName}</div>
                        </div>
                    `).join('');

                    $('#suggestions').html(suggestionList).show();
                    currentIndex = -1; // Reset the current index
                } else {
                    $('#suggestions').empty().hide();
                }
            },
            error: function () {
                $('#suggestions').empty().show();
                console.error('Error fetching data');
            }
        });
    });

    $('#prodData').on('keydown', function (e) {
        const items = $('.productRow');

        if (e.key === 'ArrowDown') {
            currentIndex = Math.min(currentIndex + 1, items.length - 1);
        } else if (e.key === 'ArrowUp') {
            currentIndex = Math.max(currentIndex - 1, -1);
        } else if (e.key === 'Enter' && currentIndex >= 0) {
            const selectedId = $(items[currentIndex]).data('id');
            alert('Selected Product ID: ' + selectedId); // Handle selection
            $('#suggestions').empty().hide();
            return;
        }

        items.removeClass('highlight');
        if (currentIndex >= 0) {
            $(items[currentIndex]).addClass('highlight');
        }
    });



    $(document).on('click', '.productRow', function () {
        const ProdId = $(this).data('prodid');
        const CatId = $(this).data('catid');
        var prodCode=$(this).find('.spCode').text();
        var prodName=$(this).find('.spName').text();
        var prodCatName=$(this).find('.spCatname').text();
        var ProdInfo='<strong class="h5 text-danger">'+prodName+'</strong> ('+prodCode+')<br>'+prodCatName;
        $('#selectedProd').html(ProdInfo);
        $('#suggestions').empty().hide();
        $('#seletedProdName').val(prodName);
        $('#seletedCatName').val(prodCatName);
        $.ajax({
            url: "{{url('admin/product-all-active-variants')}}/" + ProdId+'/'+CatId,
            method: 'GET',
            //data: { name },
            success: function (data) {
                $('#prodAllVariants').html(data);
            },error: function () {
                $('#prodAllVariants').empty();
                console.error('Error fetching data');
            }
        });
     
    });

    /* ===== Product By QR Code ===== */
    $('#prodQrData').on('keyup', function (e) {
        
        var qrcode=$(this).val();
        if(qrcode.length>=3)
        {
            if($('#catall').is(":checked")){
                catall='all';
            }else{
                catall='';     
            }

            $.ajax({
                    url: "{{url('admin/search-prod-qr')}}/" + qrcode+'/'+catall,
                    method: 'GET',
                    //data: { name },
                    success: function (d) {
                        $('#prodAllVariants').html(d.data);
                        var prodCode=d.prodinfo.product.code;
                        var prodName=d.prodinfo.product.name;
                        var prodCatName=d.prodinfo.category.name;

                        var ProdInfo='<strong class="h5 text-danger">'+prodName+'</strong> ('+prodCode+')<br>'+prodCatName;
                        $('#selectedProd').html(ProdInfo);
                        $('#seletedProdName').val(prodName);
                        $('#seletedCatName').val(prodCatName);
                    },
                    error: function (){
                        $('#prodAllVariants').empty();
                        console.error('Error fetching data');
                    }
                });
        }
        }); 
     

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.input-group').length) {
            $('#suggestions').empty().hide();
        }
    });

  
     $('#prodAllVariants').on('input', '.AdQty , .AdSprice', function () {
            const $row = $(this).closest('tr');
            QtyRateCalculate($row);
        });
  
     function QtyRateCalculate(e){
            
        const qty=parseFloat($(e).find('.AdQty').val());
        const rate=parseFloat($(e).find('.AdSprice').val());
        const stock=parseFloat($(e).find('.itemAstock').text());
        
            // Validate input
            if (isNaN(rate) || isNaN(qty)) {
                $(e).find('.AdNet').val(''); // Clear total if input is invalid
                $(e).find('.itmStatus').html('');
                $('#addItemBtn').addClass('hide');
                return;
            }
            // Calculate total
            const total = rate * qty;
            $(e).find('.AdNet').val(total.toFixed(2));
            $('#addItemBtn').removeClass('hide');
            if(stock<qty){
                $(e).find('.itmStatus').html('<i class="fa fa-check-circle text-danger"></i>');
            }else{
                $(e).find('.itmStatus').html('<i class="fa fa-check-circle text-success"></i>');
            }

           
     }
    function addItemToBill(){
        const prodName=$('#seletedProdName').val();
        var catName=$('#seletedCatName').val();
        $('#prodAllVariants .variantsRow').each(function(){
            const vrntRow=$(this);
            const stockID=vrntRow.find('.itemId').text();
            const attrName=vrntRow.find('.itemAtr').text();
            var pQty=vrntRow.find('.AdQty').val();
            var pRate=vrntRow.find('.AdSprice').val();
            var stNetAmt=parseFloat(vrntRow.find('.AdNet').val());
            var stTaxRate=vrntRow.find('.stTaxRate').val();
            var stTaxAmt=(stNetAmt*stTaxRate/100);
            if(stTaxAmt>0){
                catName=catName+' *';
            }
            
            if (stNetAmt>0) {
                const AttrRow='<tr><td></td><td class="text-start"><input name="oldID[]" value="0" hidden><input name="stockID[]" value="'+stockID+'" hidden><input type="hidden" name="AdTaxRate[]" value="'+stTaxRate+'"><input type="hidden" name="AdTaxAmt[]" value="'+stTaxAmt+'">'+prodName+'</td><td class="text-start">'+catName+'</td><td class="text-start">'+attrName+'</td><td><input name="AdProdQty[]" value="'+pQty+'" class="apQty" onkeyup="editIt(this)"></td><td hidden></td><td><input name="AdpRate[]" value="'+pRate+'" class="apRate" onkeyup="editIt(this)"></td><td hidden></td><td><input name="AdNetAmt[]" value="'+stNetAmt+'" class="apAmt" onkeyup="editIt(this)" readonly></td><td><i class="fa fa-times-circle" aria-hidden="true" onclick="DeleteRow(this);" role="button"></i></td></tr>';

                $('#addTblBody').find('#blankItemRow').remove();
                RowSno();
                $("#addTblBody").append(AttrRow);
                $('#prodAllVariants').find(vrntRow).remove();
                calculate();
            }else{

            }

            

        });
     
    };

     /*=====Add new Row =======*/
	function addItemToBill222(){
        var srchPdIDs=$('#allData').val();
        var allId=srchPdIDs.split("|"); //(stock_id|Prodduct_id|category_id|attribute_id);
        var stockID=allId[0];
        var prodID=allId[1];
        var catID=allId[2];
        var attrID=allId[3];
        var prodName=$('#itemInfo').val();
        var catName=$('#itemCatInfo').val();
        var attrName=$('#prodAttribute').find("option:selected").text();
        var pQty=$('#pQty').val();
        var stMrp=$('#Mrp').val();
        var stPurPrice=$('#pRate').val();
        var stDiscount=$('#pDiscount').val();
        var stNetAmt=$('#pNetAmount').val();
		var stTaxRate=$('#pTaxRate').val();
		var stTaxAmt=(stNetAmt*stTaxRate/100);
		if(stTaxAmt>0){
			catName=catName+' *';
		}


        if((prodID=='' || prodID==undefined ) && attrID==undefined ){
          $('#prodData').focus();
         alert("Please select Product");

        }else if(attrID=="" ){
          $('#prodAttribute').focus();
         alert("Please select color");
        }else if(pQty<=0 || isNaN(pQty) ){
          $('#pQty').focus();
         alert("Please Add Qty");
        }else if(stPurPrice=="" || isNaN(stPurPrice)){
          $('#pRate').focus();
         alert("Please enter sale Rate");
        }else if(isNaN(stNetAmt)){
          $('#pQty').focus();
         alert("Please check qty and price");
        }else{
           var defRow=$('#nb').length;

           if(defRow>=1){
               $('#nb').closest('tr').remove()
           }

          $("#addTblBody").append(AttrRow);
          RowSno();
          calculate();
          clearAfterItemAdd();
        }
	}
     	/*========SET Serial No. to added product List=======*/
	function RowSno(){
		$( "#addTblBody tr" ).each(function( index ) {
			$(this).find('td').first().text(index+1);
		});
	}
    
     function calculate(){
		var SumQty =0;SumDis=0; sumNetAmt=0; billDis=0;otherCharge=0;freight=0;SumtaxAmt=0;

     /*=== Account Discount Rate ===*/
      disRate=$('input[name ="DisRate"]').val();
     /*====*/

		 $("input[name*='AdProdQty']").each( function(){ SumQty += parseFloat($(this).val());	});
		 $("input[name*='AdNetAmt']").each( function(){ sumNetAmt += parseFloat($(this).val());	});
		 $("input[name*='AdTaxAmt']").each( function(){ SumtaxAmt += parseFloat($(this).val());	});

		 billDis+=$('input[name ="billDiscount"]').val();
		 otherCharge+=$('input[name ="otherCharges"]').val();
		 freight+=$('input[name ="freight"]').val();
		 DisTotal=parseFloat(billDis);
		 $('input[name ="sumQtyTotal"]').val(SumQty.toFixed(2));
		 $('input[name ="sumDisTotal"]').val(SumDis.toFixed(2));
		 $('input[name ="DisTotal"]').val(DisTotal);
		 $('input[name ="sumNetTotal"]').val(sumNetAmt.toFixed(2));
		 $('input[name ="sumTaxAmount"]').val(SumtaxAmt.toFixed(2));

     if(disRate>0){
       calDis=sumNetAmt*(disRate/100);
       $('input[name="billDiscount"]').val(calDis.toFixed(2));
     }
		 

		 var bill_amount=(sumNetAmt+SumtaxAmt+parseFloat(otherCharge)-parseFloat(billDis)+parseFloat(freight)).toFixed(2);
		 $('#bill_amount').val(bill_amount);
		 $('.grandTotal').text(bill_amount);

	}
</script>

<script>
 $(document).delegate(".accinfo","click",function(e){
    var acid=$(this).attr('acid');
    $.ajax({
            url: "{{url('admin/getAccountDetail')}}/" + acid,
            method: 'GET',
            //data: { name },
            success: function (d) {
                 // Get form values
                    const name = d.name;
                    const email = d.email;
                    const phone = d.phone;
                    const phone2 = d.phone2;
                    const customerType=d.type;
                    const statename= d.statedata.name;
                    const contactPerson = d.contactPerson;
                    const city = d.citydata.name;
                    const address = d.address;
                    $('#account_id').val(d.id);
                    // Create result display
                    $('#resultDisplay').html(`
                        <p><strong clas="h5"> ${name} <small>(${customerType})</small></strong></p>
                        <p>Phone: ${phone}, ${phone2}<br>
                        ${email}<br>
                        <strong>Address:</strong>${address}, ${city} , ${statename}<br>
                        <strong>Contact Person:</strong> ${contactPerson}</p>
                    `);
                    $('.customerSearchlist').hide();
            },
            error: function (){
                $('#prodAllVariants').empty();
                console.error('Error fetching data');
            }
        });
})


/*=======REMOVE/Delete Row ======*/
function DeleteRow(obj)
    {
	  $oldID=$(obj).closest('tr').find(".old").val();
      $requestType=$('#action').val();

      if(confirm('Are you sure you want to delete this item Permanently?'))
        {
            if($oldID>=1 && $requestType=='UpdateSale'){
                $.getJSON("{{url('admin/action')}}/"+'sale-itemRemove/'+$oldID, function(data){
                    if(data.status=='true' && data.code=='101'){
                        calculate();
                       alert('Please submit the bill to update the bill amount');
                       
                    }else{
                        console.log(' not done`');
                    }
                });
            
            }else{

            }
            RowSno();
            calculate();
            $(obj).closest('tr').remove()
            $('#prodAttribute').focus();
        }else{
			return false;
		} 
	}
/* ==== form validate before submit===== */

$("#billingForm").validate({
    ignore: [],
rules: {
    "account_id":{required: true},
    "salesman_id": {
        required: true,
    },
    "bill_amount":{
        required:true
    },
    
},
messages: {
    "account_id":{
        required: function() {
            toastr.error('Please Select Customer')
        },
    },
    "salesman_id": {
        required: function() {
            toastr.error('Please select sales Person')
        },
    },
    "bill_amount": {
        required: function() {
            toastr.error('Please Add Product to bill')
        },
    },
   
},
submitHandler: function(form) { // for demo
       // toastr.success('success')
       // return false; // for demo
       return true;
    }
});

window.setTimeout(function() {
        $("#form").show() 
    }, 3000);
</script>
@endsection