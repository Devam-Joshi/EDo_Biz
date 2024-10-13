@extends('layouts.master')
@section('title')
    @lang('translation.Data_Tables')
@endsection
@section('css')

@endsection

@section('content')

    @include('components.breadcum')
    <div class="row">
        <div class="col-12">
        </div>
        <div class="card">
            <div class="card-body">
                <form class="" name="main_form" id="main_form" method="post"
                      action="{{route('admin.content.update',$data->id)}}" enctype="multipart/form-data">
                    {!! get_error_html($errors) !!}
                    @csrf
                    @method('PATCH')
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-md-2 col-form-label"><span
                                class="text-danger">*</span>{{__('Title')}}</label>
                        <div class="col-md-10">
                            <input type="text" name="title" id="title" class="form-control" value="{{$data->title}}">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-md-2 col-form-label"><span
                                class="text-danger">*</span>{{__('Title')}}</label>
                        <div class="col-md-10">
                            <textarea name="content" id="content" cols="30" rows="10">
                                {!! $data->content !!}
                            </textarea>
                        </div>
                    </div>
                    <div class="kt-portlet__foot">
                        <div class=" ">
                            <div class="row">
                                <div class="wd-sl-modalbtn">
                                    <button type="submit"
                                            class="btn btn-primary waves-effect waves-light"
                                            id="save_changes">Submit
                                    </button>
                                    <a href="{{route("admin.content.index")}}" id="close">
                                        <button type="button"
                                                class="btn btn-outline-secondary waves-effect">
                                            Close
                                        </button>
                                    </a>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.6.2/tinymce.min.js"></script>
    <script>
        $(function () {
            tinymce.init({
                selector: 'textarea',
                height: 500,
                // theme: 'modern',
            });


            $("#main_form").validate({
                rules: {
                    title: {
                        required: true,
                    },
                },
                messages: {
                    title: {required: 'Please content title'},
                },
                invalidHandler: function (event, validator) {
                    var alert = $('#kt_form_1_msg');
                    alert.removeClass('kt--hide').show();
                    KTUtil.scrollTo('m_form_1_msg', -200);
                },
                submitHandler: function (form) {
                    addOverlay();
                    form.submit();
                }
            });
        });
    </script>
@endsection

