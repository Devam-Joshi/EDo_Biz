@extends('layouts.master')

@section('content')
@section('title')
    @lang('translation.Form_Layouts')
@endsection

@section('content')
    @include('components.breadcum')

    <div class="row">
        <div class="col-12">
        </div>
        <div class="card">
            <div class="card-body">
                @if (isset($data) && !empty($data))
                    <form class="" name="main_form" id="main_form" method="post"
                        action="{{ route('admin.permission.update', $data->id) }}">
                        @method('PATCH')
                    @else
                        <form class="" name="main_form" id="main_form" method="post"
                            action="{{ route('admin.permission.store') }}">
                @endif
                {!! get_error_html($errors) !!}
                @csrf

                <!-- Check if $data is set for edit mode -->
                @if (isset($data) && !empty($data))
                    <div class="mb-3 row">
                        <label for="parent-category" class="col-md-2 col-form-label">
                            <span class="text-danger">*</span>Parent Permission
                        </label>
                        <div class="col-md-10">
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="">Select Parent Permission</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ isset($data->parent_id) && $data->parent_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="child-name" class="col-md-2 col-form-label">
                            <span class="text-danger">*</span>Child Permission Name
                        </label>
                        <div class="col-md-10">
                            <input type="text" name="child_name" id="child_name" class="form-control"
                                value="{{ $data->child_name ?? '' }}">
                        </div>
                    </div>
                @else
                    <!-- Create Mode: Show Tab Bar -->
                    <ul class="nav nav-tabs mb-4" id="categoryTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="main-tab" data-bs-toggle="tab"
                                data-bs-target="#main-category" type="button" role="tab" aria-controls="main-category"
                                aria-selected="true">Main Category</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="child-tab" data-bs-toggle="tab" data-bs-target="#child-category"
                                type="button" role="tab" aria-controls="child-category" aria-selected="false">Child
                                Category</button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="categoryTabContent">
                        <!-- Main Category Content -->
                        <div class="tab-pane fade show active" id="main-category" role="tabpanel"
                            aria-labelledby="main-tab">
                            <div class="mb-3 row">
                                <label for="main-name" class="col-md-2 col-form-label">
                                    <span class="text-danger">*</span>Main Permission Name
                                </label>
                                <div class="col-md-10">
                                    <input type="text" name="main_name" id="main_name" class="form-control"
                                        value="{{ old('main_name') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Child Category Content -->
                        <div class="tab-pane fade" id="child-category" role="tabpanel" aria-labelledby="child-tab">
                            <div class="mb-3 row">
                                <label for="parent-category" class="col-md-2 col-form-label">
                                    <span class="text-danger">*</span>Parent Permission
                                </label>
                                <div class="col-md-10">
                                    <select name="parent_id" id="parent_id" class="form-control">
                                        <option value="">Select Parent Permission</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="child-name" class="col-md-2 col-form-label">
                                    <span class="text-danger">*</span>Child Permission Name
                                </label>
                                <div class="col-md-10">
                                    <input type="text" name="child_name" id="child_name" class="form-control"
                                        value="{{ old('child_name') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <div class="kt-portlet__foot">
                    <div>
                        <div class="row">
                            <div class="wd-sl-modalbtn">
                                <button type="submit" class="btn btn-primary waves-effect waves-light"
                                    id="save_changes">Submit</button>
                                <a href="{{ route('admin.role.index') }}" id="close">
                                    <button type="button" class="btn btn-outline-secondary waves-effect">Cancel</button>
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
    <script>
        $(function() {
            // Form Validation
            $("#main_form").validate({
                rules: {
                    main_name: {
                        required: function() {
                            return $('#main-tab').hasClass('active');
                        }
                    },
                    parent_id: {
                        required: function() {
                            return $('#child-tab').hasClass('active');
                        }
                    },
                    child_name: {
                        required: function() {
                            return $('#child-tab').hasClass('active');
                        }
                    }
                },
                messages: {
                    main_name: {
                        required: "Please enter main permission name"
                    },
                    parent_id: {
                        required: "Please select a parent permission"
                    },
                    child_name: {
                        required: "Please enter child permission name"
                    }
                },
                submitHandler: function(form) {
                    addOverlay();
                    form.submit();
                }
            });
        });
    </script>
@endsection
