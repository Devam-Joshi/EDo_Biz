@extends('layouts.master')

@section('content')
@section('title')
    @lang('translation.Form_Layouts')
@endsection

@include('components.breadcum')

<div class="row">
    <div class="col-12">
    </div>
    <div class="card">
        <div class="card-body">
            <form class="" name="main_form" id="main_form" method="post"
                action="{{ route('admin.user.update', $data->id) }}" enctype="multipart/form-data">
                {!! get_error_html($errors) !!}
                @csrf
                @method('PATCH')
                <input type="hidden" name="country_code" id="country_code"
                    value="{{ empty($data->country_code) ? '+1' : $data->country_code }}">

                <!-- Profile Image Field -->
                <div class="mb-3 row">
                    <label for="profile_image" class="col-md-2 col-form-label"><span
                            class="text-danger">*</span>{{ __('Profile Image') }}</label>
                    <div class="col-md-10">
                        <input type="file" accept="image/*" id="profile_image" class="form-control"
                            name="profile_image">
                    </div>
                </div>

                <!-- First Name Field -->
                <div class="mb-3 row">
                    <label for="first_name" class="col-md-2 col-form-label"><span class="text-danger">*</span>First
                        Name</label>
                    <div class="col-md-10">
                        <input type="text" name="first_name" id="first_name" class="form-control"
                            value="{{ $data->first_name }}" maxlength="50">
                    </div>
                </div>

                <!-- Last Name Field -->
                <div class="mb-3 row">
                    <label for="last_name" class="col-md-2 col-form-label"><span class="text-danger">*</span>Last
                        Name</label>
                    <div class="col-md-10">
                        <input type="text" name="last_name" id="last_name" class="form-control"
                            value="{{ $data->last_name }}" maxlength="50">
                    </div>
                </div>

                <!-- Username Field -->
                <div class="mb-3 row">
                    <label for="username" class="col-md-2 col-form-label"><span
                            class="text-danger">*</span>Username</label>
                    <div class="col-md-10">
                        <input type="text" name="username" id="username" class="form-control"
                            value="{{ $data->username }}" maxlength="50">
                    </div>
                </div>

                <!-- Email Field -->
                <div class="mb-3 row">
                    <label for="email" class="col-md-2 col-form-label"><span
                            class="text-danger">*</span>Email</label>
                    <div class="col-md-10">
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ $data->email }}">
                    </div>
                </div>

                <!-- Permissions Field -->
                @if (isset($groupedPermissions) && $groupedPermissions->count() > 0)
                    <div class="col-12 bg-light p-4 rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0" style="color: #103664;">Permissions</h5>
                            <div>
                                <button type="button" id="select_all" class="btn btn-primary me-2"
                                    style="background-color: #103664; border-color: #103664;">
                                    Select All
                                </button>
                                <button type="button" id="deselect_all" class="btn btn-outline-secondary">
                                    Deselect All
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover border">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3">Category</th>
                                        <th class="py-3">Permissions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedPermissions as $category => $permissions)
                                        <tr>
                                            <td class="fw-bold" style="color: #103664;">
                                                {{ ucfirst($category) }}
                                            </td>
                                            <td>
                                                <div class="row g-3">
                                                    @foreach ($permissions as $permission)
                                                        <div class="col-md-4">
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                    class="form-check-input permission-checkbox"
                                                                    name="permissions[]" value="{{ $permission->name }}"
                                                                    id="permission_{{ $permission->id }}"
                                                                    {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="permission_{{ $permission->id }}">
                                                                    {{ $permission->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="kt-portlet__foot">
                    <div class="row">
                        <div class="wd-sl-modalbtn">
                            <button type="submit" class="btn btn-primary waves-effect waves-light"
                                id="save_changes">Update</button>
                            <a href="{{ route('admin.user.index') }}" id="close"><button type="button"
                                    class="btn btn-outline-secondary waves-effect">Cancel</button></a>
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
    $(document).ready(function() {
        // Select/Deselect all global buttons
        $('#select_all').click(function() {
            $('input.permission-checkbox').prop('checked', true);
        });

        $('#deselect_all').click(function() {
            $('input.permission-checkbox').prop('checked', false);
        });
    });
</script>
@endsection
