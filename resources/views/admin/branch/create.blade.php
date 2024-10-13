@extends('layouts.master')

@section('content')
@section('title')
@lang('translation.Form_Layouts')
@endsection @section('content')
@include('components.breadcum')
<div class="row">
    <div class="col-12">
    </div>
    <div class="card">
        <div class="card-body">
            @if(isset($data) && !empty($data))
            <form class="" name="main_form" id="main_form" method="post" action="{{route('admin.branch.update',$data->id)}}">
                @method('PATCH')
                @else
                <form class="" name="main_form" id="main_form" method="post" action="{{route('admin.branch.store')}}">
                    @endif
                    {!! get_error_html($errors) !!}
                    @csrf
                    <div class="row m-2">
                       
                            <div class="form-group">
                                <label>Branch Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter branch" value="{{$data->name ?? ''}}">
                            </div>
                
                            <div class="form-group">
                                <label>Branch Address</label>
                                <textarea type="text" name="address" class="form-control" placeholder="Enter address">{{$data->address ?? ''}}</textarea>
                            </div>
                     
                    </div>
        </div>
    </div>
    <div class="kt-portlet__foot">
        <div class=" ">
            <div class="row">
                <div class="wd-sl-modalbtn">
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="save_changes">Submit</button>
                    <a href="{{route('admin.branch.index')}}" id="close"><button type="button" class="btn btn-outline-secondary waves-effect">Cancel</button></a>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
</div>
</div>
@endsection
@section('script')
<script>
    $(function() {

        $("#main_form").validate({

            rules: {
                name: {
                    required: true,

                },
                address: {
                    required: true,

                },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                address: {
                    required: "Please enter address",
                },

            },
            submitHandler: function(form) {
                addOverlay();
                form.submit();
            }
        });

    });
</script>
@endsection