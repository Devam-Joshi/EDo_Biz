<script>
    $(function () {
        let url = window.location;
        {{--        @if(session('error'))--}}
        {{--        toastr.error('{{session('error')}}');--}}
        {{--        @elseif(session('success'))--}}
        {{--        toastr.success('{{session('success')}}');--}}
        {{--        @endif--}}
        // $('.kt-menu__item  a[href="' + url + '"]').parent('li').parent('ul').parent('div').parent('li').addClass('kt-menu__item--active').addClass('kt-menu__item--open');
        // $('.kt-menu__item  a[href="' + url + '"]').parent('li').addClass('kt-menu__item--active').addClass('kt-menu__item--open');
    });
</script>
<script>
    $(function () {
        @if(session('flash_notification'))
        @foreach (session('flash_notification') as $message)
        @if($message['level']=="danger")
        toastr.error('{{$message['message']}}', 'error');
        @else
        toastr.success('{{$message['message']}}', 'success');
        @endif
        @endforeach
        @endif
    });
</script>
@yield('f_script')
@include('general.footer_scripts')
</body>
</html>
