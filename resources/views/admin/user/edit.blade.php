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
            <form class="" name="main_form" id="main_form" method="post" action="{{route('admin.user.update',$data->id)}}" enctype="multipart/form-data">
                 {!! get_error_html($errors) !!}
                @csrf
                @method('PATCH')
                <input type="hidden" name="country_code" id="country_code"
                       value="{{empty($data->country_code)?"+1":$data->country_code}}">

                 <div class="mb-3 row">
                    <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>{{__('Profile Image')}}</label>
                    <div class="col-md-10">
                        <input type="file" accept="image/*" id="profile_image" class="form-control" name="profile_image">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>First Name</label>
                    <div class="col-md-10">
                        <input type="text" name="first_name" id="first_name" class="form-control" value="{{$data->first_name}}" maxlength="50">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>last Name</label>
                    <div class="col-md-10">
                        <input type="text" name="last_name" id="last_name" class="form-control" value="{{$data->last_name}}" maxlength="50">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>Username</label>
                    <div class="col-md-10">
                   
                             <input type="text" name="username" id="username" class="form-control" value="{{$data->username}}" maxlength="50">
                    </div>
                   
                </div>

                <!-- <div class="mb-3 row">
                    <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>{{__('Number')}}</label>
                    <div class="col-md-10">
                        <input type="text" id="number" name="mobile" class="form-control" maxlength="10" value="{{(empty($data->country_code)?"+1":$data->country_code)." ".$data->mobile}}" minlength="5">
                    </div>
                    <label id="number-error" class="error" for="number"></label>
                </div> -->

                <div class="mb-3 row">
                    <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>email</label>
                    <div class="col-md-10">
                        <input type="email" name="email" id="email" class="form-control" value="{{$data->email}}">
                    </div>
                </div>
                
                
               

                


                <div class="kt-portlet__foot">
                    <div class=" ">
                        <div class="row">
                            <div class="wd-sl-modalbtn">
                                <button  type="submit" class="btn btn-primary waves-effect waves-light" id="save_changes">Submit</button>
                                <a href="{{route('admin.user.index')}}" id="close"><button type="button" class="btn btn-outline-secondary waves-effect">Cancel</button></a>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/utils.js"></script>
    
<script>
        $(function () {
            let id = "{{$data->id}}";
            $('#number').intlTelInput({
                nationalMode: false,
                separateDialCode: true,
                formatOnDisplay: false,
            }).on("countrychange", function () {
                $('#country_code').val('+' + $(this).intlTelInput("getSelectedCountryData").dialCode);
            });

            $("#main_form").validate({
                rules: {
                    first_name: {required: true},
                    last_name: {required: true},
                    username: {required: true},
                    // mobile: {
                    //     required: true,
                    //     digits: true,
                    //     remote: {
                    //         type: 'get',
                    //         url: "{{route('front.user_availability_checker')}}",
                    //         data: {
                    //             id: id,
                    //             country_code: function () {
                    //                 return $('#country_code').val();
                    //             },
                    //             number: function () {
                    //                 return $('#number').val();
                    //             }
                    //         }
                    //     },
                    // },
                    username: {
                        required: true,
                        remote: {
                            type: 'get',
                            url: "{{route('front.user_availability_checker')}}",
                            data: {
                                id: id,
                                username: function () {
                                    return $('#username').val();
                                }
                            }
                        },
                    },
                    email: {
                        required: true,
                        remote: {
                            type: 'get',
                            url: "{{route('front.user_availability_checker')}}",
                            data: {
                                id: id,
                                email: function () {
                                    return $('#email').val();
                                }
                            }
                        },
                    },
                },
                messages: {
                    first_name: {required: "Please enter first name"},
                    last_name: {required: "Please enter last name"},
                    mobile: {required: 'Please enter number', remote: "This number is already taken"},
                    username: {required: 'Please enter username', remote: "This username is already taken"},
                    email: {required: 'Please enter email', remote: "This email is already taken"},
                },
                submitHandler: function (form) {
                    addOverlay();
                    form.submit();
                }
            });
        });
    </script>
@endsection