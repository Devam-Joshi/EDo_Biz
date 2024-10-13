@extends('layouts.master')

@section('content')
@section('title')
@lang('translation.Form_Layouts')
@endsection @section('content')
@include('components.breadcum')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="" name="main_form" id="main_form" class="main_form" method="post" enctype="multipart/form-data" action="{{route('admin.post_profile')}}">
                    {!! get_error_html($errors) !!}
                    {!! success_error_view_generator() !!}
                    @csrf
                    <div>
                        <div class="mb-3 row">
                            <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>Profile Image</label>
                            <div class="col-md-8">
                                <input type="file" accept="image/*" id="profile_image" class="form-control" name="profile_image">
                            </div>
                            <div class="col-md-2">
                                <a target="_blanck" href="{{$user->profile_image}}"><img src="{{$user->profile_image}}" alt="img" height="40" width="40" /></a>

                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>{{__('Name')}}</label>
                            <div class="col-md-10">
                                <input type="text" name="name" id="name" value="{{$user->name}}" class="form-control" maxlength="25">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>{{__('Username')}}</label>
                            <div class="col-md-10">
                                <input type="text" name="username" id="username" class="form-control" value="{{$user->username}}" maxlength="25">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>{{__('Email')}}</label>
                            <div class="col-md-10">
                                <input type="email" name="email" id="email" class="form-control" value="{{$user->email}}">
                            </div>
                        </div>

                    </div>
                    <div class="wd-sl-modalbtn">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="save_changes">Save Changes</button>
                        <a href="{{route(getDashboardRouteName())}}"><button type="button" class="btn btn-outline-secondary waves-effect">Close</button></a>
                    </div>
                    <!-- <div class="kt-portlet__foot">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="save_changes">Submit</button>
                                    <a href="{{route(getDashboardRouteName())}}" id="close"><button type="button" class="btn btn-outline-secondary waves-effect">Close</button></a>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </form>

            </div>
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
                    required: true
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        type: 'get',
                        url: "{{route('front.availability_checker')}}",
                        data: {
                            'type': "email",
                            'val': function() {
                                return $('#email').val();
                            }
                        },
                    },
                },
                username: {
                    required: true,
                    remote: {
                        type: 'get',
                        url: "{{route('front.availability_checker')}}",
                        data: {
                            'type': "username",
                            'val': function() {
                                return $('#username').val();
                            }
                        },
                    },
                },
            },
            messages: {
                email: {
                    required: 'Please Enter Email address',
                    remote: "this email is already taken",
                },
                name: {
                    required: 'Please Enter Name'
                },
                username: {
                    remote: "this username is already taken",
                }
            },
            invalidHandler: function(event, validator) {
                var alert = $('#kt_form_1_msg');
                alert.removeClass('kt--hide').show();
                // KTUtil.scrollTo('m_form_1_msg', -200);
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
@endsection