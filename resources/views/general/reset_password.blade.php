@extends('layouts.admin.app')

@section('h_style')
    <style>
        .login_bg_main {
            background-image: url({{asset('assets/admin/images/misc/bg-1.jpg')}});
        }
    </style>
@endsection

@section('content')
    <div class="kt-grid kt-grid--ver kt-grid--root">
        <div class="kt-grid kt-grid--hor kt-grid--root kt-login kt-login--v2 kt-login--signin" id="kt_login">
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor login_bg_main">
                <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper">
                    <div class="kt-login__container">
                        <div class="kt-login__logo">
                            <a href="{{route('admin.login')}}">
                                <img src="{{site_logo}}" class="admin_logo_size">
                            </a>
                        </div>
                        <div class="kt-login__signin">
                            <div class="kt-login__head">
                                <h3 class="kt-login__title">Password reset</h3>
                            </div>
                            <form class="kt-form login-form" action="{{ route('front.forgot_password_post') }}"
                                  name="form_login"
                                  id="form_form"
                                  method="post">
                                <input type="hidden" name="reset_token" value="{{$token}}">
                                @csrf
                                {!! get_error_html($errors) !!}
                                {!! success_error_view_generator() !!}
                                <div class="input-group d-flex flex-column">
                                    <input class="w-100 form-control" type="password"
                                           placeholder="New password"
                                           name="password" id="password" value="" autofocus>
                                </div>
                                <div class="input-group d-flex flex-column">
                                    <input class="w-100 form-control" type="password"
                                           placeholder="confirm password"
                                           name="cnf_password" id="cnf_password">
                                </div>
                                <div class="kt-login__actions">
                                    <button id="kt_login_signin_submit"
                                            class="btn btn-pill kt-login__btn-primary">{{__('Reset Password')}}</button>
                                    <a href="{{route('admin.login')}}" id="kt_login_forgot_cancel"
                                       class="btn btn-pill kt-login__btn-secondary">Login
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        $("#form_form").validate({
            rules: {
                password: {required: true},
                cnf_password: {required: true, equalTo: "#password"}
            },
            messages: {
                password: {required: 'Please enter new password'},
                cnf_password: {
                    required: 'Please enter Confirm password',
                    equalTo: "confirm password does not match"
                },
            },
            invalidHandler: function (event, validator) {
                var alert = $('#kt_form_1_msg');
                alert.removeClass('kt--hide').show();
                //KTUtil.scrollTo('m_form_1_msg', -200);
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    </script>
@endsection
