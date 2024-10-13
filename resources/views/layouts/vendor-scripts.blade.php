<!-- JAVASCRIPT -->
<script>
       let loader_img = "{{asset('assets/admin/images/three-dots.svg')}}";
</script>
<script src="{{ URL::asset('assets/libs/jquery/jquery.min.js')}}"></script>

<!-- <script src="{{ URL::asset('assets/libs/bootstrap/js/bootstrap.min.js')}}"></script> -->

<script src="{{ URL::asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>


<script src="{{ URL::asset('assets/libs/metismenu/metismenu.min.js')}}"></script>

<script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js')}}"></script>

<script src="{{ URL::asset('assets/libs/node-waves/node-waves.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/notify.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/bootstrap-datepicker.js')}}"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js" integrity="sha512-efUTj3HdSPwWJ9gjfGR71X9cvsrthIA78/Fvd/IN+fttQVy7XWkOAXb295j8B3cmm/kFKVxjiNYzKw9IQJHIuQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script> -->
<script src="{{asset('assets/libs/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}"></script>

<!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->

<script src="{{ asset('assets/js/sweetalert.min.js')}}"></script>
<script src="{{ asset('assets/libs/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>

<script src="{{asset('assets/admin/js/common.js')}}"></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="{{asset('/assets/admin/vendors/general/validate/jquery.validate.min.js')}}"></script>


<div id="sent_to_grave">
    {{--  This div for using for deleteing Stuff  --}}
    <form action="" method="post">
        @csrf
        @method('DELETE')
    </form>
</div>
<script>
    function delete_record(event) {
                event.preventDefault();
                let el = this;
                swal({
                    // title: "Are you sure you want to delete this?",
                    text: "Are you sure you want to delete this?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#sent_to_grave form').attr('action', $(el).attr('href')).submit();
                    }
                });
            }
    $(document).ready(function(){
        $('#loader_display_d').hide();
        $(".input-mask").inputmask();

        $(document).on('click', '.btnDelete', delete_record);

        $(document).on('change', '.switch-status', function () {
            $.ajax({
                url: this.dataset.url,
                dataType: 'Json',
                success: function (r) {
                    let msg = r.message;
                    if (r.status) {
                        show_toastr_notification(msg)

                    } else {
                        show_toastr_notification(msg,412)
                    }
                    if (typeof oTable !== undefined) {
                        oTable.ajax.reload(null, false);
                    }
                }
            });
        });

    });

    $('#change-password').on('submit',function(event){
        event.preventDefault();
        var Id = $('#data_id').val();
        var current_password = $('#current-password').val();
        var password = $('#password').val();
        var password_confirm = $('#password-confirm').val();
        $('#current_passwordError').text('');
        $('#passwordError').text('');
        $('#password_confirmError').text('');
        $.ajax({
            url: "{{ url('update-password') }}" + "/" + Id,
            type:"POST",
            data:{
                "current_password": current_password,
                "password": password,
                "password_confirmation": password_confirm,
                "_token": "{{ csrf_token() }}",
            },
            success:function(response){
                $('#current_passwordError').text('');
                $('#passwordError').text('');
                $('#password_confirmError').text('');
                if(response.isSuccess == false){
                    $('#current_passwordError').text(response.Message);
                }else if(response.isSuccess == true){
                    setTimeout(function () {
                        window.location.href = "/";
                    }, 1000);
                }
            },
            error: function(response) {
                $('#current_passwordError').text(response.responseJSON.errors.current_password);
                $('#passwordError').text(response.responseJSON.errors.password);
                $('#password_confirmError').text(response.responseJSON.errors.password_confirmation);
            }
        });
    });
</script>

@yield('script')

<!-- App js -->
<script src="{{ URL::asset('assets/js/app.min.js')}}"></script>

@yield('script-bottom')
