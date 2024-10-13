@extends('layouts.master')

@section('content')
@section('title')
@lang('translation.Form_Layouts')
@endsection @section('content')

@include('components.breadcum')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            <form class="" name="form_cpass" id="form_cpass" method="post" enctype="multipart/form-data" action="{{route('admin.site_settings')}}">
                @csrf
                {!! success_error_view_generator() !!}
                {!! get_error_html($errors) !!}
                <div >
                    @foreach($fields as $key=>$field)
                        @if(in_array($field['type'],['text','number','email','url','file']))

                        <div class="mb-3 row">
                            <label for="example-text-input" class="col-md-2 col-form-label"><span class="text-danger">*</span>{{$field['label']}}</label>
                            <div class="col-md-10">
                                
                                <input type="{{$field['type']}}" name="{{$field['id']}}"
                                           id="{{$field['unique_name']}}"
                                           class="form-control"
                                           placeholder="Please Enter {{$field['label']}}"
                                           value="{{$field['value']}}"
                                        {!! echo_extra_for_site_setting($field['extra']) !!}
                                        {{($field['type']=="file")?"":"required"}}>
                            </div>
                        </div>

                            
                        @elseif($field['type']=="select"&&!empty($field['options']))

                        <div class="mb-3 row">
                            <label for="example-text-input" class="col-md-2 col-form-label"> <span class="text-danger">*</span>{{$field['label']}}</label>
                            <div class="col-md-10">
                                <select {!! echo_extra_for_site_setting($field['extra']) !!} name="{{$field['id']}}"
                                            class="form-control" id="{{$field['unique_name']}}" required>
                                        @foreach(json_decode($field['options']) as $plan)
                                            <option
                                                value="{{$plan->value}}" {{$plan->value==$field['value']?"selected":""}}>{{$plan->name}}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                            
                        @endif
                    @endforeach
                </div>
                <div class="kt-portlet__foot">
                    <div class=" ">
                        <div class="row">
                            <div class="wd-sl-modalbtn">
                                <button type="submit" class="btn btn-primary waves-effect waves-light" id="save_changes">Submit</button>
                                    <a href="{{route(getDashboardRouteName())}}" id="close"><button type="button" class="btn btn-outline-secondary waves-effect">Close</button></a>
                                <!-- <button type="submit" class="btn btn-success"
                                        id="btn-submit-dev">Submit
                                </button>
                                <a href="{{route(getDashboardRouteName())}}"
                                   class="btn btn-secondary">Cancel</a> -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>
       
@endsection


@section('script')
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(function () {
            $(document).on('submit', '#form_cpass', function () {
                $("#form_cpass").validate({
                    rules: {
                        opassword: {
                            required: true,
                        },
                        npassword: {
                            required: true,
                            minlength: 6,
                        },
                        cpassword: {
                            required: true,
                            equalTo: "#npassword",
                        }
                    },
                    messages: {
                        opassword: {
                            required: "Please Enter Old Password.",
                        },
                        npassword: {
                            required: "Please Enter New Password.",
                            minlength: "Please Enter minimum 6 character for password",

                        },
                        cpassword: {
                            required: "Please Enter Config Password.",
                            equalTo: "Confirm Password does not match with new password.",
                        }
                    },
                });
                return $('#form_cpass').valid();
            });
        });
    </script>
@endsection
