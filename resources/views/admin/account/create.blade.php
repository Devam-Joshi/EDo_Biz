@extends('layouts.master')

@section('content')
@section('title')
@lang('translation.Form_Layouts')
@endsection @section('content')
@include('components.breadcum')
<div class="row">
    <div class="col-12">
    </div>
    <div class="card">
       
           <!-- form start -->
           @if(isset($account->id) && $account->id!='' )
            <form role="form" action="{{ route('admin.account.update', $account->id) }}" method="post" enctype="multipart/form-data">
            @method('PUT')
        @else
            <form role="form" action="{{ route('admin.account.store') }}" method="post" enctype="multipart/form-data">
        @endif
                                @csrf
                                <div class="card-body">
                                {!! get_error_html($errors) !!}
                                @csrf
                                  <div class="row">
                                    <div class="col-md-3 border bg-light shadow">
                                      <div class="row">
                                        <div class="col-md-12">
                                           <div class="form-group">
                                              <label>Code <span class="text-danger">*</span></label>
                                                @php if(isset($account->id) && $account->id!='' ){$nextBill=$account->acCode;} @endphp
                                              <input type="text" class="form-control" name="acCode" value="{{ old('acCode',$nextBill)}}" placeholder="Enter Code" readonly>
                                           </div>
                                       </div>
                                       <div class="col-md-12">
                                         <div class="form-group center">
                                             <label for="exampleInputFile">Logo/Icon</label>
                                             <img src="{{ !empty($account->photo)?asset('assets/uploads/account/'.$account->photo):asset('assets/uploads/account/accounticon.png')}}" width="150px">
                                             <div class="input-group">
                                                 <div class="custom-file">
                                                    <input type="file" name="photo" class="custom-file-input" id="exampleInputFile">
                                                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                                 </div>
                                             </div>
                                         </div>
                                       </div>
                                       <div class="col-md-6">
                                         <div class="form-group">
                                            <label>Credit Days</label>
                                            <input type="text" class="form-control" name="creditDays" value="{{ old('creditDays',$account->creditDays )}}" placeholder="Credit days">
                                         </div>
                                       </div><div class="col-md-6">
                                         <div class="form-group" data-title="Alert User/Admin about credit before overdue.">
                                            <label>Cr. Alert Days</label>
                                            <input type="text" class="form-control" name="creditAlertDays" value="{{ old('creditAlertDays',$account->creditAlertDays )}}" placeholder="Cr. Alert days">
                                         </div>
                                       </div>                                      

                                       <div class="col-md-6">
                                         <div class="form-group">
                                             <label>Discount Rate</label>
                                             <input type="text" class="form-control" name="discountRate" value="{{ old('discountRate',$account->discount_rate )}}">
                                         </div>
                                       </div>
                                       <div class="col-md-5">
                                          <div class="form-group">
                                              <label>Dr/Cr</label>
                                               <select name="opening_type" id="" class="form-control">
                                                  <option value="" disabled>--type--</option>
                                                  <option value="Cr" {{ old('opening_type',$account->opening_type) == 'Cr' ? 'selected' : '' }}>Debit</option>
                                                  <option value="Dr" {{ old('opening_type',$account->opening_type) == 'Dr' ? 'selected' : '' }}>Credit</option>
                                              </select>
                                          </div>
                                        </div>
                                        <div class="col-md-7">
                                          <div class="form-group">
                                              <label>Op. Balance</label>
                                              <input type="text" class="form-control" name="openingBalance" value="{{ old('openingBalance',$account->openingBalance )}}" placeholder="Enter Bank Branch">
                                          </div>
                                        </div>
                                        <div class="col-md-12">
                                          <div class="form-group">
                                              <label>Ac type(online/offline) <span class="text-danger">*</span></label>
                                               <select name="visit_type" id="" class="form-control" required>
                                                  <option value="" >--select type--</option>
                                                  <option value="0" {{ old('visit_type',$account->visit_type) == '0' ? 'selected' : '' }}>Offline(Visited)</option>
                                                  <option value="1" {{ old('visit_type',$account->visit_type) == '1' ? 'selected' : '' }}>Online(Telephonic)</option>
                                              </select>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-9">

                                    <div class="row">
                                      <div class="col-md-4">
                                          <div class="form-group">
                                              <label>Name <span class="text-danger">*</span></label>
                                              <input type="text" class="form-control" name="name" value="{{ old('name',$account->name)}}" placeholder="Enter Name" required>
                                          </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email',$account->email)}}"  placeholder="Enter Email">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone" value="{{ old('phone',$account->phone)}}" placeholder="Enter Phone">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Contact Person</label>
                                            <input type="text" class="form-control" name="contactPerson" value="{{ old('contactPerson',$account->contactPerson)}}" placeholder="Enter Contact Person">
                                        </div>
                                      </div>
                                      <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input type="text" class="form-control" name="address" value="{{ old('address',$account->address)}}" placeholder="Enter Address">
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label>State</label>
                                          <select name="state_id" class="form-control">
                                            <option value="">--Select State--</option>
                                            @if(!empty($state))
                                              @foreach($state as $st)
                                                <option value="{{$st->id}}" {{old('state_id',$account->state_id)==$st->id?'selected':''}}>{{$st->name}}</option>
                                              @endforeach
                                            @endif
                                          </select>
                                          <input type="hidden" class="form-control" name="state" value="{{ old('state',$account->state)}}" placeholder="Enter State">                                            
                                        </div>
                                      </div>
									                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>City</label>
                                            <select name="city_id" class="form-control">
                                              <option value="">--Select City--</option>
                                              @if(!empty($city))
                                                @foreach($city as $ct)
                                                  <option value="{{$ct->id}}" {{old('city_id',$account->city_id)==$ct->id?'selected':''}}>{{$ct->name}}</option>
                                                @endforeach
                                              @endif
                                            </select>
                                            <input type="hidden" class="form-control" name="city" value="{{ old('city',$account->city)}}" placeholder="Enter City">   
                                        </div>
                                      </div>
									                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>PinCode</label>
                                            <input type="text" class="form-control" name="pinCode" value="{{ old('pinCode',$account->pinCode)}}" placeholder="Enter Pincode">
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <input type="text" class="form-control" name="country" value="{{ old('country',$account->country)?? 'India'}}" placeholder="Country Name">
                                        </div>
                                      </div>
                                    </div>
                                      <hr><br>
                                      <div class="row">
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select name="type" id="" class="form-control">
                                              <option value="" disabled>Select a Type</option>
                                              <option value="1" {{ old('type',$account->type) == 1 ? 'selected' : '' }}>Distributor</option>
                                              <option value="2" {{ old('type',$account->type) == 2 ? 'selected' : '' }}>Whole Seller</option>
                                              <option value="3" {{ old('type',$account->type) == 3 ? 'selected' : '' }}>other</option>
                                            </select>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label>Group <span class="text-danger">*</span></label>
                                          <select name="acGroup" id="" class="form-control" required>
                                              <option value="" disabled>Select Ac Group</option>
                                              @if(isset($acgroup))
                                                  @foreach($acgroup as $category)
                                                      <optgroup label="{{ $category->name }}">
                                                              @foreach($category->child as $child)
                                                                  <option value="{{ $child->id }}" {{old('acGroup',$account->acGroup)==$child->id ? 'selected':''}}>&#8594;{{ $child->name }}</option>

                                                                  @foreach($child->child as $cat)
                                                                      <option value="{{ $cat->id }}" {{old('acGroup',$account->acGroup)==$cat->id ? 'selected':''}}>&nbsp;&nbsp;&nbsp;&#8226;&nbsp;{{ $cat->name }}</option>
                                                                  @endforeach
                                                              @endforeach
                                                      </optgroup>
                                                  @endforeach
                                              @endif
                                          
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Price Group</label>
                                            <select name="priceGroup" id="" class="form-control">
                                                <option value="" disabled>Select Price Group</option>
                                                <option value="1" {{ old('priceGroup',$account->priceGroup) == 1 ? 'selected' : '' }}>Retail Price</option>
                                                <option value="2" {{ old('priceGroup',$account->priceGroup) == 2 ? 'selected' : '' }}>WS Price</option>
                                            </select>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>CSTN</label>
                                            <input type="text" class="form-control" name="CSTN_No" value="{{ old('CSTN_No',$account->CSTN_No )}}" placeholder="Enter Account Holder">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>GSTN</label>
                                            <input type="text" class="form-control" name="GSTN_No" value="{{ old('GSTN_No',$account->GSTN_No )}}" placeholder="Enter Account Number">
                                        </div>
                                      </div>
                                      @if(isset($account->id) && $account->id!='')
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Overdue Amount</label>
                                            <input type="number" class="form-control" name="old_overdue" value="{{ old('old_overdue',$account->overdue_amount )}}" placeholder="Enter Account Number">
                                        </div>
                                      </div>
                                      @endif
                                      <hr class="mt-3">
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Referred By</label>
                                            <input type="text" class="form-control" name="referredBy" value="{{ old('referredBy',$account->referred_by )}}">
                                        </div>
                                      </div>
                                      <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Term & Condition </label>
                                            <input type="text" class="form-control" name="tnc" value="{{ old('tnc',$account->term_cond )}}">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Transport name</label>
                                            <input type="text" class="form-control" name="transport" value="{{ old('transport',$account->transport )}}">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tranportation Pay By</label>
                                            <input type="radio" class="" name="payby" value="1" {{ old('payby',$account->payby )=='1'?'checked':''}}>Pay
                                            <input type="radio" class="" name="payby" value="2" {{ old('payby',$account->payby )=='2'?'checked':''}}> Payto
                                        </div>
                                      </div>
                                  </div>
                                </div>
                              </div>
                                  <div class="row" hidden>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Account Holder</label>
                                            <input type="text" class="form-control" name="account_holder" value="{{ old('account_holder',$account->account_holder )}}" placeholder="Enter Account Holder">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Account Number</label>
                                            <input type="text" class="form-control" name="account_number" value="{{ old('account_number',$account->account_number )}}" placeholder="Enter Account Number">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bank Name</label>
                                            <input type="text" class="form-control" name="bank_name" value="{{ old('bank_name',$account->bank_name )}}" placeholder="Enter Bank Name">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bank Branch</label>
                                            <input type="text" class="form-control" name="bank_branch" value="{{ old('bank_branch',$account->bank_branch )}}" placeholder="Enter Bank Branch">
                                        </div>
                                      </div>
                                    </div>

                                @if(isset($account->id) && $account->id!='' && ($account->acGroup=='3' || $account->acGroup=='4') )
                                  <!-- ==== Allow Login ==== -->
                                  <hr>
                                  <div class="row">
                                    <div class="col-md-12 border bg-warning p-2">
                                      <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                              <label> Login Allowed</label>
                                              <select class="form-control" name="allowLogin" id="allowLogin">
                                                <option> -- Select Yes/No -- </option>
                                                <option value="Y" {{ old('allowLogin',$account->allow_login) == 'Y' ? 'selected' : '' }}>Yes</option>
                                                <option value="N" {{ old('allowLogin',$account->allow_login) == 'N' ? 'selected' : '' }}>No</option>
                                              </select>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                                  <hr>
                                  <div class="row">
                                    <div class="col-md-3">
                                      <div class="form-group">
                                        <label> Status</label>
                                        <select class="form-control" name="status">
                                          <option value="1" {{ old('status',$account->status) == 1 ? 'selected' : '' }}>Active</option>
                                          @if(isset($account->id))
                                          <option value="0" {{ old('status',$account->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                          @endif
                                        </select>
                                      </div>
                                    </div>
                                    <div class="col-md-3">
                                      <div class="form-group">
                                        <label> Blocked Status (yes/No)</label>
                                        <select class="form-control blockedStatus" name="blockedStatus" onchange="addbloccknote()">
                                          <option value="0" {{ old('blockedStatus',$account->block_status) == 0 ? 'selected' : '' }}>No</option>
                                          <option value="1" {{ old('blockedStatus',$account->block_status) == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>Date | Remark</label>
                                        <input type="text" class="form-control" name="blockedRemark" placeholder=" Date /By User/Remarks" value="{{ old('blockedRemark',$account->block_remark )}}">
                                        </select>
                                      </div>
                                      @if(isset($account->id) && $account->id>=1 && $account->block_status==1 && !empty($account->blockby))
                                          <span class="bg-dark">Blocked By : <em>{{$account->blockby->name}}</em></span>
                                      @endif
                                    </div>
                                  </div>
                                </div>
                                <!-- /.card-body -->
                               
                            
        </div>
    </div>
    <div class="kt-portlet__foot">
        <div class=" ">
            <div class="row">
                <div class="wd-sl-modalbtn">
                @php if(isset($account->id) && $account->id!='' ){ $btnText='Update';}else{ $btnText='Create';}@endphp
                    <button type="submit" class="btn btn-primary waves-effect waves-light"  id="save_changes">{{$btnText}}</button>
                    <a href="{{route('admin.account.index')}}" id="close"><button type="button" class="btn btn-outline-secondary waves-effect">Cancel</button></a>
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
    $(function() {

        $("#main_form").validate({

            rules: {
                name: {
                    required: true,

                },
                address: {
                    required: true,

                },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                address: {
                    required: "Please enter address",
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