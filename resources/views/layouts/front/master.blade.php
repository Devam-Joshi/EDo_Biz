<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>FlexTms</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <!-- Favicons -->
    <link href="{{ asset('master_assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="{{ asset('master_assets/vendor/animate.css/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master_assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master_assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('master_assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master_assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master_assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master_assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="{{ asset('master_assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('master_assets/css/responsive.css') }}" rel="stylesheet">
    @yield('css')

</head>



<body>
<!-- ======= Top Bar ======= -->
@include('layouts.front.master.topbar')


<!-- header start -->
@include('layouts.front.master.header')
<!-- End Header -->



<!-- ======= Hero Section ======= -->
@yield('banner')
<!-- End Hero -->

<main id="main">
    @yield('main')
</main>








<!-- End #main -->

{{--<section class="request-for">--}}
{{--    <div class="container">--}}
{{--        <div class="request-title">--}}
{{--            <h3>REQUEST FOR QUOTE</h3>--}}
{{--        </div>--}}
{{--        <div class="title-input">--}}
{{--            <input type="email" class="form-control form-wrap-name" id="exampleFormControlInput1"--}}
{{--                   placeholder="Enter email address">--}}
{{--            <i class="fa fa-envelope fa-lg fa-fw" aria-hidden="true"></i>--}}
{{--            <button type="submit"><i class="fas fa-paper-plane send"></i></button>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}

@include('layouts.front.master.footer')





<!-- Vendor JS Files -->
<script src="{{asset('master_assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{asset('master_assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{asset('master_assets/vendor/php-email-form/validate.js') }}"></script>
<script src="{{asset('master_assets/vendor/purecounter/purecounter.js') }}"></script>
<!-- Template Main JS File -->
<script src="{{asset('master_assets/js/main.js') }}"></script>
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js" integrity="sha512-XZEy8UQ9rngkxQVugAdOuBRDmJ5N4vCuNXCh8KlniZgDKTvf7zl75QBtaVG1lEhMFe2a2DuA22nZYY+qsI2/xA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{asset('master_assets/js/header.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@yield('script')

</body>


</html>
