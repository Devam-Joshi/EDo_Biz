@extends('layouts.admin.app')

@section('h_style')
<style>
        .login_bg_main {
            background-color: #B857E5 !important;
          
        }
        .admin_logo_size{
   
    border-radius: 80px;
    padding: 10px;
    margin-top: 20px;
    height: inherit;
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
                                <h3 class="kt-login__title">{{ __('panel.lbl_login') }}</h3>
                            </div>
                            <form class="kt-form login-form" action="{{ route('admin.login_post') }}" name="form_login"
                                  id="form_login"
                                  method="post">
                                @csrf
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <div class="alert-text">{{session('error')}}</div>
                                        <div class="alert-close"><i class="flaticon2-cross kt-icon-sm"
                                                                    data-dismiss="alert"></i></div>
                                    </div>
                                @elseif(session('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <div class="alert-text">{{session('success')}}</div>
                                        <div class="alert-close"><i class="flaticon2-cross kt-icon-sm"
                                                                    data-dismiss="alert"></i></div>
                                    </div>
                                @endif
                                <div class="input-group d-flex flex-column">
                                    <input class="w-100 form-control" type="text"
                                           placeholder="Username/Email"
                                           name="username" id="username" value="{{old('username')}}" autofocus>
                                </div>
                                <div class="input-group d-flex flex-column">
                                    <input class="w-100 form-control" type="password"
                                           placeholder="{{__('Password')}}"
                                           name="password" id="password" value="{{old('password')}}">
                                </div>
                                <div class="row kt-login__extra">
                                    <div class="col">
                                        <label class="kt-checkbox">
                                            <input type="checkbox"
                                                   name="remember"{{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col kt-align-right">
                                        <a href="{{route('admin.forgot_password')}}" id="kt_login_forgot"
                                           class="kt-link kt-login__link">{{__('Forgot Password')}}</a>
                                    </div>
                                </div>
                                <div class="kt-login__actions">
                                    <button id="kt_login_signin_submit"
                                            class="btn btn-pill kt-login__btn-primary">{{__('Login')}}</button>
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
    <script>
        $(function () {
            var displaySignInForm = function () {
                $('#kt_login').removeClass('kt-login--forgot');
                $('#kt_login').removeClass('kt-login--signup');
                $('#kt_login').addClass('kt-login--signin');
                KTUtil.animateClass($('.kt-login__signin')[0], 'flipInX animated');
            }

            var displayForgotForm = function () {
                $('#kt_login').removeClass('kt-login--signin');
                $('#kt_login').removeClass('kt-login--signup');
                $('#kt_login').addClass('kt-login--forgot');
                KTUtil.animateClass($(".kt-login__forgot")[0], 'flipInX animated');
            }

            $(document).on('submit', '#form_login', function () {
                $(this).validate({
                    rules: {
                        username: {required: true,},
                        password: {required: true}
                    },
                    messages: {
                        username: {required: 'Please enter username or email'},
                        password: {required: 'Please enter password'}
                    },
                });
                return $(this).valid();
            });
        });
    </script>
@endsection

