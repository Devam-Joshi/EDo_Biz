{{--
Tingle Lib Start
--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/tingle/0.15.3/tingle.min.js"></script>--}}
{{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tingle/0.15.3/tingle.css"/>--}}
{{--
Tingle Lib Finnish
--}}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{asset('assets/admin/js/common.js')}}"></script>
<script>
    let loader_img = "{{asset('assets/admin/images/three-dots.svg')}}";
</script>
<script>
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>
<style>
    .col-form-label {
        text-transform: capitalize;
    }

    thead th {
        text-transform: capitalize;
    }

    .img_new {
        height: 200px;
        max-width: 200px;
    }

    /*
        Loader
    */
    #overlayDocument {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #000;
        filter: alpha(opacity=50);
        -moz-opacity: 0.5;
        -khtml-opacity: 0.5;
        opacity: 0.5;
        z-index: 10000;
        text-align: center
    }

    #overlayDocument img {
        position: absolute;
        top: 50%;
        margin: 0 auto;
        left: 0;
        right: 0;
    }

</style>
