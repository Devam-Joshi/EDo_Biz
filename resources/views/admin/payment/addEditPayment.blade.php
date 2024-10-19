@extends('layouts.master')
@section('content')
    @section('title')
    @lang('translation.Form_Layouts')
@endsection 
@section('content')
@include('components.breadcum')
<div class="row">
    <div class="col-12">
    </div>
    <div class="card">
        {!! get_error_html($errors) !!}
            <div class="card-header">
            </div>
            <!-- /.card-header -->
                @if(isset($payment->id) && $payment->id!='' )
                    <form role="form" action="{{ route('admin.payment.update', $payment->id) }}" method="post" enctype="multipart/form-data" autocomplete="off">
                    @method('PUT')
                @else
                    <form role="form" action="{{ route('admin.payment.store') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                @endif
                @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9 p-3">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group text-center">
                                            <input type="radio" class="form-control"  name="paymentType"  value="payment" @if(isset($payment['reference_type']) && $payment['reference_type']=='payment') checked @elseif(!isset($payment['reference_type'])) checked @else '' @endif style="height:30px"> Payment
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group text-center">
                                            <input type="radio"  name="paymentType" value="receipt" class="form-control" @if(isset($payment['reference_type']) && $payment['reference_type']=='receipt'): checked ? '' @endif style="height:30px"> Receive
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group text-center">
                                            <input type="radio"  name="paymentType" value="expenses" class="form-control" @if(isset($payment['reference_type']) && $payment['reference_type']=='expenses'): checked ? '' @endif style="height:30px"> Expenses
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group text-center">
                                            <label>Pay/Receipt No.<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="invoice_No" value="{{ old('invoice_No',$nextBill)}}" readonly style="height:30px">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group ">
                                            <label>Date <span class="text-danger">*</span></label>
                                            @php if(isset($payment['txn_date'])){$date=$payment['txn_date'];}else{$date=date('Y-m-d'); }@endphp
                                                <input type="date" class="form-control" name="txn_date" value="{{ date('Y-m-d',strtotime(old('txn_date',$date)))}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Bank<span class="text-danger">*</span><em class="float-right text-success" id="bankCbal"></em></label>
                                            <select class="select2 form-control" name="bankAcID" id="bankAcID"  required>
                                                <option value="">--Select Bank---</option>
                                                @foreach($bank as $bnk)
                                                <option value="{{$bnk->id}}" @php if(old('bankAcID',$payment->payment_bank_id)==$bnk->id){echo 'selected';}else{} @endphp>{{$bnk->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Payment Mode</label>
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Referrence No.</label>
                                            <input type="text" class="form-control" name="payment_referrence_no" value="{{ old('payment_referrence_no',$payment->payment_referrence_no)}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Account Search <span class="text-danger">*</span></label>
                                            <input id="acountSearch" name="searchSupplier" class="form-control" placeholder="Type name/code">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Account Detail</label>
                                        <input id="supplier_id" name="supplier_id" class="form-control" value="{{old('supplier_id',$payment->party_id)}}" hidden>
                                        <div id="showAcData">
                                            @if(isset($payment->id))
                                            
                                            <span class="pull-left"><strong>{{$payment->accData->name}}</strong> <br>{{$payment->accData->phone.'('.$payment->accData->email.')'}}<br>Address:-{{$payment->accData->address.' '.$payment->accData->city.' '.$payment->accData->state}}</span>
                                            <span class="pull-right"><strong>{{$payment->accData->acCode}}<br>Bal: </strong></span>
                                            @endif
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="SchListDiv col-md-6" id="srchAcListDiv">
                                    <div id="srchAcList">
                                    <em disabled>--- no data found---</em>
                                    </div>
                                </div>
                                <div class="row mt-3" id="srchPd">
                                <div class="col-md-3 cBalAmt">
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
                                </div>
                            </div>
                        </div>
                            <div class="col-md-3">
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
/*=======Search Account/customer Data=======*/
	$('#acountSearch').on('keyup', function(){
		var nameKey = $(this).val();
		var type= '*';
		if(nameKey.length>=3){
		$('#srchAcListDiv').show(500);
		$.getJSON("{{url('getAccountList')}}/" +type+'/'+nameKey , function(data){
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
		$.getJSON("{{url('getAccountDetail')}}/"+id, function(d){
			var acDetail='<span class="pull-left"><strong>'+d[0].name+'</strong><br>'+d[0].phone+'('+d[0].email+')<br>Address:-'+d[0].address+' '+d[0].city+' '+d[0].state+'</span><span class="pull-right"><strong>'+d[0].acCode+'<br>Bal: '+d[0].current_balance+'</strong></span>';
			$('#showAcData').html(acDetail);
			$('input[name ="supplier_id"]').val(id);
      $('input[name ="currentBalance"]').val(d[0].current_balance);
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