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
        <div class="card-body">
            <form class="" name="form_cpass" id="form_cpass" method="post" action="{{route('admin.update_password')}}">
            
                    @csrf
                    {!! success_error_view_generator() !!}
                    {!! get_error_html($errors) !!}
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>Old Password</label>
                        <div class="col-md-10">
                            <input type="password" name="opassword" id="opassword" class="form-control" placeholder="Please Enter Old Password." data-error-container="#error_opassword">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-md-2 col-form-label"> <span class="text-danger">*</span>New Password</label>
                        <div class="col-md-10">
                          <input type="password" name="npassword" id="npassword" class="form-control" placeholder="Please Enter New Password." data-error-container="#error_npassword">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>Confirm Password</label>
                        <div class="col-md-10">
                          <input type="password" name="cpassword" id="cpassword" class="form-control" placeholder="Please Enter Confirm Password." data-error-container="#error_cpassword">
                        </div>
                    </div>
                        
                       
                
              
                    
                    <div class="kt-portlet__foot">
                        <div class=" ">
                            <div class="row">
                                <div class="wd-sl-modalbtn">
                                     <button type="submit" class="btn btn-primary waves-effect waves-light" id="save_changes">Submit</button>
                                     <a href="{{route(getDashboardRouteName())}}" id="close"><button type="button" class="btn btn-outline-secondary waves-effect">Close</button></a>
                                    
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
        $(document).on('submit', '#form_cpass', function() {
            $("#form_cpass").validate({
                rules: {
                    opassword: {
                        required: true,
                    },
                    npassword: {
                        required: true,
                        minlength: 6,
                    },
                    cpassword: {
                        required: true,
                        equalTo: "#npassword",
                    }
                },
                messages: {
                    opassword: {
                        required: "Please Enter Old Password.",
                    },
                    npassword: {
                        required: "Please Enter New Password.",
                        minlength: "Please Enter minimum 6 character for password",

                    },
                    cpassword: {
                        required: "Please Enter Config Password.",
                        equalTo: "Confirm Password does not match with new password.",
                    }
                },
            });
            return $('#form_cpass').valid();
        });
    });
</script>
@endsection