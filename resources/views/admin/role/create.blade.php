@extends('layouts.master')

@section('content')
@section('title')
    @lang('translation.Form_Layouts')
@endsection
@section('content')
    @include('components.breadcum')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white border-bottom" style="background-color: #103664;">
                    <h4 class="mb-0 text-white p-3">{{ isset($data) ? 'Edit Role' : 'Create New Role' }}</h4>
                </div>
                <div class="card-body p-4">
                    @if (isset($data) && !empty($data))
                        <form name="main_form" id="main_form" method="post" action="{{ route('admin.role.update', $data->id) }}">
                        @method('PATCH')
                    @else
                        <form name="main_form" id="main_form" method="post" action="{{ route('admin.role.store') }}">
                    @endif
                    {!! get_error_html($errors) !!}
                    @csrf

                    <!-- Role Information Section -->
                    <div class="bg-light p-4 rounded-3 mb-4">
                        <h5 class="mb-4" style="color: #103664;">Role Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <span class="text-danger">*</span> Title
                                </label>
                                <input type="text" name="name" id="name" class="form-control form-control-lg shadow-sm"
                                    value="{{ $data->name ?? '' }}" placeholder="Enter role title">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <select name="statusData" id="statusData" class="form-select form-select-lg shadow-sm">
                                    <option value="active" {{ isset($role) && $role->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ isset($role) && $role->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Section -->
                    <div class="bg-light p-4 rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0" style="color: #103664;">Permissions</h5>
                            <div>
                                <button type="button" id="select_all" class="btn btn-primary me-2" style="background-color: #103664; border-color: #103664;">
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
                                        <th class="py-3">Bulk Actions</th>
                                        <th class="py-3">Permissions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedPermissions as $category => $permissions)
                                        <tr>
                                            <td class="fw-bold" style="color: #103664;">{{ ucfirst($category) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary select-category me-2"
                                                    data-category="{{ $category }}" style="background-color: #103664; border-color: #103664;">
                                                    Select All
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary deselect-category"
                                                    data-category="{{ $category }}">
                                                    Deselect All
                                                </button>
                                            </td>
                                            <td>
                                                <div class="row g-3">
                                                    @foreach ($permissions as $permission)
                                                        <div class="col-md-4">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input permission-checkbox"
                                                                    name="permissions[]" id="permission_{{ $permission->id }}"
                                                                    value="{{ $permission->name }}" data-category="{{ $category }}"
                                                                    {{ isset($data) && $data->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
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

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-lg btn-primary px-4" id="save_changes"
                            style="background-color: #103664; border-color: #103664;">
                            Submit
                        </button>
                        <a href="{{ route('admin.role.index') }}" class="btn btn-lg btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            // Select all permissions
            $("#select_all").click(function() {
                $(".permission-checkbox").prop("checked", true);
            });

            // Deselect all permissions
            $("#deselect_all").click(function() {
                $(".permission-checkbox").prop("checked", false);
            });

            // Select category-specific permissions
            $(".select-category").click(function() {
                const category = $(this).data("category");
                $(`.permission-checkbox[data-category="${category}"]`).prop("checked", true);
            });

            // Deselect category-specific permissions
            $(".deselect-category").click(function() {
                const category = $(this).data("category");
                $(`.permission-checkbox[data-category="${category}"]`).prop("checked", false);
            });

            // Form Validation
            $("#main_form").validate({
                rules: {
                    name: {
                        required: true,
                    },
                    "permissions[]": {
                        required: true,
                    }
                },
                messages: {
                    name: {
                        required: "Please enter title",
                    },
                    "permissions[]": {
                        required: "Please select at least one permission",
                    }
                },
                submitHandler: function(form) {
                    addOverlay();
                    form.submit();
                }
            });
        });
    </script>

    <style>
        .btn-primary:hover {
            background-color: #0d2c52 !important;
            border-color: #0d2c52 !important;
        }

        .form-check-input:checked {
            background-color: #103664 !important;
            border-color: #103664 !important;
        }

        .card {
            border-radius: 1rem;
        }

        .card-header {
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: #103664;
            box-shadow: 0 0 0 0.25rem rgba(16, 54, 100, 0.25);
        }
    </style>
@endsection
