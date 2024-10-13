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
                        <div class="kt-login__forgot d-block">
                            <div class="kt-login__head">
                                <h3 class="kt-login__title">Forgot Password</h3>
                            </div>
                            <form class="kt-form" name="form_forgot" id="form_forgot"
                                  action="{{route('admin.forgot_password_post')}}"
                                  method="post">
                                @csrf
                                {!! success_error_view_generator() !!}
                                @if ($errors->any())
                                    @foreach($errors->all() as $err)
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <div class="alert-text">{{$err}}</div>
                                            <div class="alert-close"><i class="flaticon2-cross kt-icon-sm"
                                                                        data-dismiss="alert"></i></div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="input-group d-flex flex-column">
                                    <input class="form-control w-100" type="text"
                                           placeholder="Enter Email address associated with your account"
                                           name="email" id="email" autocomplete="off">
                                </div>
                                <div class="kt-login__actions">
                                    <button id="kt_login_forgot_submit" type="submit"
                                            class="btn btn-pill kt-login__btn-primary">Submit
                                    </button>
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
    <script>
        $(function () {
            $(document).on('submit', '#form_forgot', function () {
                $(this).validate({
                    rules: {
                        email: {
                            required: true, email: true
                        },
                    },
                    messages: {
                        email: {
                            required: 'Please Enter Email',
                            email: 'Please Enter Valid Email Address',
                        },
                    },
                });
                return $(this).valid();
            });
        });
    </script>
@endsection

