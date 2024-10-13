<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>  {{isset($title)?print_title($title).' | ':''}}{{ print_title(site_name) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{isset($title)?print_title($title).' | ':''}}{{ print_title(site_name) }}" name="description" />
    <meta content="{{isset($title)?print_title($title).' | ':''}}{{ print_title(site_name) }}" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link href="{{Favicon}}" rel="icon" type="image/x-icon">
    
    @include('layouts.head-css')
</head>

@section('body')
    <body data-sidebar="dark">
    <div class="loading" id="loader_display_d" style="z-index: 9999;">Loading&#8230;</div>
@show
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
       
        @include('layouts.sidebar')

        
        
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    
    <!-- /Right-bar -->
    
    <!-- JAVASCRIPT -->
    
    @include('layouts.vendor-scripts')

    <!-- add Modal -->
    
<div class="modal fade  bs-example-modal-center_cust1 general_modal" id="general_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="general_modal_heading">Heading</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body general_modal_content">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-pill btn-primary waves-effect waves-light  general_modal_submit_btn">Submit</button>
                <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade  general_modal_second" id="general_modal_second" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="general_modal_heading_second">Heading</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body general_modal_content_second">
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-pill btn-primary waves-effect waves-light  general_modal_submit_btn_second">Submit</button>
                <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
        </div>
    </div>
</div>
    
</body>

</html>
