@extends('layouts.master')
@section('content')

@include('components.breadcum')
<div class="row">
    <div class="col-md-3">
        <div class="card ">
            <div class="card-body">
                <div class="card-title">
                    <div class="kt-widget__media text-center w-100">
                        {!! get_fancy_box_html(get_asset($data->profile_image)) !!}
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="font-weight-bold">Name:</span>
                    <a href="#">{{$data->name}}</a>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="font-weight-bold">Email:</span>
                    <a href="mailto:{{$data->email}}">{{$data->email}}</a>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="font-weight-bold">Status:</span>
                    <span class="kt-widget__data">{!! user_status($data->status,$data->deleted_at) !!}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card ">
            <div class="card-body">
                <div class="card-title">
                    User Details
                </div>
                <div>
                    More Details Not Available
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {});

</script>
@endsection
