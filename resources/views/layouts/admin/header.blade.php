<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <base href="{{route('front.dashboard')}}">
    <title>{{isset($title)?print_title($title).' | ':''}}{{ print_title(site_name) }}</title>
    <link href="{{Favicon}}" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
    <link rel="stylesheet" href="{{asset('assets/admin/css/style.bundle.css')}}">
    <link href="{{asset('assets/admin/css/login-2.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/admin/css/style.css')}}" rel="stylesheet" type="text/css"/>
    
    <!--Script World Start from here-->
    <script src="{{asset('assets/admin/vendors/general/jquery/dist/jquery.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/admin/vendors/general/jquery.validate.min.js')}}" type="text/javascript"></script>
     
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/additional-methods.min.js"></script> -->
    
    @yield('h_style')
    @include('general.header_includes')

    <style>
        .error {
            color: red;
        }

        .toast-title {
            color: white !important;
        }

        .logo_new_style {
            width: 200px;
            margin-top: 18px;
        }
        .admin_logo_size {
            max-width: 300px;
            max-height: 100px;
        }
    </style>
    @yield('h_script')
</head>
<body
    class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

