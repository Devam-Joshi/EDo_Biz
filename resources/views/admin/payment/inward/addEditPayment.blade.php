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
      background: #fdf4f1;
      border: solid 2px #652001;
      border-radius: 5px;
      margin-top: 30px;
      margin-left: 9px;
      z-index: 9;
      box-shadow: 3px 9px 10px 6px #1c1c1d;
      max-height: 500px;
      overflow-x: scroll;
      padding: 10px;
      min-width:90%;

		}
		.SchListDiv p{
		 margin: 0.5px;
		 border-bottom: solid 1px #ced4da;
		}
		.SchListDiv p:hover{
		background: #57595a;
		cursor: pointer;
		font-weight: bold;
		color: #fff;
		}

#paymentForm .form-group{
    margin-top:10px;
}
</style>
@include('components.breadcum')
<div class="row">
    <div class="col-12">
    </div>
    <div class="card" id="paymentForm">
        {!! get_error_html($errors) !!}
            <div class="card-header">
            </div>
            <!-- /.card-header -->
                @if(isset($payment->id) && $payment->id!='' )
                    <form role="form" action="{{ route('admin.payment.inward.update', $payment->id) }}" method="post" enctype="multipart/form-data" autocomplete="off">
                    @method('PUT')
                @else
                    <form role="form" action="{{ route('admin.payment.inward.store') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                @endif
                @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 p-3">
                                <div class="row">
                                    <div class="col-md-4 bg-light">
                                        <div class="form-group">
                                            Bill No : 
                                            <input type="hidden"  name="paymentType" value="receipt" class="form-control" checked>
                                        </div>
                                    </div>
                                    <div class="col-md-8 ">
                                        <div class="text-center bg-light p-2">
                                            <span class="h5 text-info rounded p-2">{{$nextBill}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row"> 
                                    <div class="col-md-4 pt-2 bg-light">
                                        Payment Date <span class="text-danger">*</span>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group ">
                                            @php if(isset($payment['txn_date'])){$date=$payment['txn_date'];}else{$date=date('Y-m-d'); }@endphp
                                            <input type="date" class="form-control" name="txn_date" value="{{ date('Y-m-d',strtotime(old('txn_date',$date)))}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">   
                                    <div class="col-md-4 bg-light">
                                        Bank<span class="text-danger">*</span><em class="float-right text-success" id="bankCbal"></em>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <select class="select2 form-control" name="bankAcID" id="bankAcID"  required>
                                                <option value="">--Select Bank---</option>
                                                @foreach($bank as $bnk)
                                                <option value="{{$bnk->id}}" @php if(old('bankAcID',$payment->payment_bank_id)==$bnk->id){echo 'selected';}else{} @endphp>{{$bnk->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <div class="row">
                                    <div class="col-md-4 bg-light">
                                        <label>Payment Mode</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <select class="select2 form-control" name="txn_method" id="txn_method" >
                                                <option value="">--Select Mode---</option>
                                                    <option value="CASH" @php if(old('txn_method',$payment->txn_method)=='cash'){echo 'selected';}else{} @endphp>CASH</option>
                                                    <option value="CHEQUE" @php if(old('txn_method',$payment->txn_method)=='CHEQUE'){echo 'selected';}else{} @endphp>CHEQUE</option>
                                                    <option value="NEFT" @php if(old('txn_method',$payment->txn_method)=='NEFT'){echo 'selected';}else{} @endphp>NEFT</option>
                                                    <option value="IMPS" @php if(old('txn_method',$payment->txn_method)=='IMPS'){echo 'selected';}else{} @endphp>IMPS</option>
                                                    <option value="UPI" @php if(old('txn_method',$payment->txn_method)=='UPI'){echo 'selected';}else{} @endphp>UPI</option>
                                                    <option value="other" @php if(old('txn_method',$payment->txn_method)=='other'){echo 'selected';}else{} @endphp>other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">   
                                    <div class="col-md-4 bg-light">
                                        <label>Referrence No.</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="payment_referrence_no" value="{{ old('payment_referrence_no',$payment->payment_referrence_no)}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 bg-light">
                                        <div class="form-group">
                                            <label>Account Search <span class="text-danger">*</span></label>
                                        </div>
                                    </div>  
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input id="acountSearch" name="searchSupplier" class="form-control" placeholder="Type name/code">
                                            <input id="supplier_id" name="supplier_id" class="form-control" value="{{old('supplier_id',$payment->party_id)}}" hidden>
                                        </div>
                                        <div class="SchListDiv col-md-6" id="srchAcListDiv">
                                            <div id="srchAcList">
                                                <em disabled>--- no data found---</em>
                                            </div>
                                        </div>
                                        <div id="showAcData">
                                            @if(isset($payment->id))
                                            <span class="pull-left"><strong>{{$payment->accData->name}}</strong> <br>{{$payment->accData->phone.'('.$payment->accData->email.')'}}<br>Address:-{{$payment->accData->address.' '.$payment->accData->city.' '.$payment->accData->state}}</span>
                                            <span class="pull-right"><strong>{{$payment->accData->acCode}}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">   
                                    <div class="col-md-4 bg-light">
                                        <label>Amount (&#8377;)</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                        <input type="text" class="form-control" name="txn_amount" id="txn_amount" value="{{old('txn_amount',$payment->txn_amount)}}" onkeyup="calculate()">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">   
                                    <div class="col-md-4 bg-light">
                                        <label>Balance/Outstanding (&#8377;)</label>
                                    </div>
                                    <div class="col-md-8 cBalAmt">
                                        <div class="form-group">
                                        <input type="texr" class="form-control" id="currentBalance" value="{{old('currentBalance',$payment->party_prevBal)}}" name="currentBalance" readonly>
                                        </div>
                                        <div class="balAmt">
                                            <input type="text" class="form-control" name="balanceAmount" id="balanceAmount" value="{{ old('balanceAmount',$payment->party_currentBal)}}" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">   
                                    <div class="col-md-4 bg-light">
                                        <label>Remark</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <textarea class="form-control" name="remark" id="remark">{{ old('remark',$payment->remark)}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3 " id="srchPd">
                                    
                                <!--<div class="col-md-3 cBalAmt">
                                    <div class="form-group">
                                        <label>Total Balance <span class="text-danger">*</span></label>
                                        <input class="form-control" id="currentBalance" value="{{old('currentBalance',$payment->party_prevBal)}}" name="currentBalance" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input type="text" class="form-control" name="txn_amount" id="txn_amount" value="{{old('txn_amount',$payment->txn_amount)}}" onkeyup="calculate()">
                                    </div>
                                </div>
                                <div class="col-md-2 balAmt">
                                    <div class="form-group">
                                        <label>Balance Amt</label>
                                        <input type="text" class="form-control" name="balanceAmount" id="balanceAmount" value="{{ old('balanceAmount',$payment->party_currentBal)}}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Remark</label>
                                        <input type="text" class="form-control" name="remark" id="remark" value="{{ old('remark',$payment->remark)}}">
                                    </div>
                                </div>-->
                            </div>
                        </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-12 bg-light border shadow prevhistory">
                                        <center> Payment History</center>
                                        <table width="100%">
                                            <thead class="bg-warning"><tr><th>ID</th><th>Date</th><th>Type</th><th>Amt</th><tr></thead>
                                            <tbody id="prevDtl"></tbody>
                                        </table>
                                    </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                   
            
            
        </div>
        <!-- /.card -->
        <div class="kt-portlet__foot">
            <div class=" ">
                <div class="row">
                    <div class="wd-sl-modalbtn">
                        @php if(isset($payment->id) && $payment->id!='' ){ $btnText='Update';}else{ $btnText='Submit';} @endphp
                        <button type="submit" class="btn btn-primary  waves-effect waves-lightt">{{$btnText}}</button>
                        <a href="{{route('admin.payment.index')}}" id="close"><button type="button" class="btn btn-outline-secondary waves-effect">Cancel</button></a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
</div>
@endsection
@section('script')
<script>
/*=======Search Account/customer Data=======*/
	$('#acountSearch').on('keyup', function(){
		var nameKey = $(this).val();
		var type= '*';
		if(nameKey.length>=3){
		$('#srchAcListDiv').show(500);
		$.getJSON("{{url('admin/getAccountList')}}/" +type+'/'+nameKey , function(data){
			if(data.length>=1){
			var listData = $('#srchAcList').empty();
			$.each(data, function(key, value){
				var list = '<p onclick="getDetail('+value.id+')">'+value.acCode+' '+value.name+'</p>';
				listData.append(list);
			});
			}else{
				$('#srchAcList').html('<em>--- no data found---</em>');
			}
		});
		}else{
			$('#srchAcListDiv').hide();
		}
	});

	function getDetail(id)
	 {
		$.getJSON("{{url('admin/getAccountDetail')}}/"+id, function(d){
			var acDetail='<span class="pull-left"><strong>'+d.name+'</strong><br>'+d.phone+'('+d.email+')<br>Address:-'+d.address+' '+d.city+' '+d.state+'</span><div class="col-12"><strong>Balance: '+d.showbalance+'</strong></div>';
			$('#showAcData').html(acDetail);
			$('input[name ="supplier_id"]').val(id);
      $('input[name ="currentBalance"]').val(d.current_balance);
			$('#srchAcListDiv').hide();

		});

    //=====previous price of item for party===========
		prevHistory(id);
	 }

	function prevHistory(id){
		  var txnType='payment|receipt|expenses';
		$.getJSON("{{url('getLastprice')}}/payment/"+id+"/"+txnType, function(rd){
		  var $tr='';
				if(rd.length>=1){
			$.each(rd, function(key, value){
					console.log(value.reference_type);
					if(value.reference_type=='payment'){p='Pay';}else if(value.reference_type=='expenses'){p='Exp';}else{p='Rec';}
					$tr+='<tr><td>'+value.id+'</td><td><span class="text-danger">'+value.txn_date+'</span></td><td>'+p+'</td><td class="text-success text-right">'+value.txn_amount+'</td> </tr>';

				 });
		   $('#prevDtl').html($tr);
		  }else{
			$tr+='<tr><td colspan="4"><small>..No data found....</small></td></tr>';
			$('#prevDtl').html($tr);
		  }
		  });
		}

	function calculate(){
		var paymentType=$('input[name=paymentType]:checked').val();
		var totalBalance =0;txn_amount=0; BalAmount=0;
     totalBalance+=$('input[name ="currentBalance"]').val();
		 txn_amount+=$('input[name ="txn_amount"]').val();
		 BalAmount=parseFloat(totalBalance)-parseFloat(txn_amount);
		 $('input[name ="balanceAmount"]').val(BalAmount);

	}

	$('form').submit(function () {
		var billAmout=$('input[name ="txn_amount"]').val();;
    var AccID=$('input[name ="supplier_id"]').val();

		if(AccID=='' || AccID<=0){
			alert("Please select customer");
			return false;
		}else if(billAmout<=0 || billAmout=='' ){
			alert("bill item amout shold not 0.00");
			return false;
		}else{
			if ( confirm("Are you sure you wish to submit?") == false ) {
      	return false ;
		   } else {
		      return true ;
		   }
		}
});
function bankCurrentBal(){
	id=$('#bankAcID').find(":selected").val();
if(id!=null && id!='' && id>0){
		$.getJSON("{{url('getAccountDetail')}}/"+id, function(d){
			$('#bankCbal').text('Bal:-  Rs.'+parseInt(d[0].current_balance));
		});
	}
}

$('#bankAcID').on('change', function() {
    
	bankCurrentBal();
});

bankCurrentBal();
calculate();


@if(isset($payment->id))
 getDetail({{$payment->accData->id}});
 $('.cBalAmt,.balAmt').hide();
@endif
</script>
@if(isset($payment->id) && $payment->id!='' )
<script>prevHistory({{$payment->party_id}});</script>
@endif
<script>
    $(function() {

        $("#main_form").validate({

            rules: {
                title: {
                    required: true,

                },
                sequence: {
                    digits: true,

                },
            },
            messages: {
                title: {
                    required: "Please enter title",
                },
                sequence: {
                    digits: "Please enter only number",
                },

            },
            submitHandler: function(form) {
                addOverlay();
                form.submit();
            }
        });

    });
</script>
@endsection