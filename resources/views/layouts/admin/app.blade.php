@include('layouts.admin.header')
@if(isset($header_panel))
    @yield('content')
@else
    @include('layouts.admin.header_nav')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        @yield('content')
    </div>
@endif


<!-- add Modal -->
<div class="modal fade wd-sl-importmodal" id="general_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="general_modal_heading">Heading</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body general_modal_content">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="main-btn general_modal_submit_btn">Submit</button>
                <button type="button" class="default-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


@yield('script')
@include('layouts.admin.footer')
