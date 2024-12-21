@extends('layouts.master')

@section('title', 'Create User')

@section('content')
    <div class="container-fluid py-5 bg-light">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-10">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Create User</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.user.saveNew') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <!-- Name Field -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Enter full name" required>
                                </div>

                                <!-- Username Field -->
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-control"
                                        placeholder="Enter username" required>
                                </div>

                                <!-- Password Field -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Enter password" required>
                                </div>

                                <!-- Email Field -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="Enter email address" required>
                                </div>

                                <!-- Profile Image Field -->
                                <div class="col-md-6">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input type="file" name="profile_image" id="profile_image" class="form-control"
                                        accept="image/*">
                                </div>

                                <!-- Status Field -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="statusData" id="statusData" class="form-select" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>

                                {{-- Branch Data --}}
                                @if (isset($branches) && $branches->count() > 0)
                                    <div class="col-12">
                                        <label class="form-label">Select Branch</label>
                                        <div class="row">
                                            <select name="branch" id="branch" class="form-select" required>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <!-- Roles Field -->
                                <div class="col-md-6">
                                    <label for="role" class="form-label">Role</label>
                                    <select name="role" id="role" class="form-select" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ isset($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                                        <th class="py-3">Bulk Actions</th>
                                                        <th class="py-3">Permissions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($groupedPermissions as $category => $permissions)
                                                        <tr>
                                                            <td class="fw-bold" style="color: #103664;">
                                                                {{ ucfirst($category) }}</td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-primary select-category me-2"
                                                                    data-category="{{ $category }}"
                                                                    style="background-color: #103664; border-color: #103664;">
                                                                    Select All
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary deselect-category"
                                                                    data-category="{{ $category }}">
                                                                    Deselect All
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <div class="row g-3">
                                                                    @foreach ($permissions as $permission)
                                                                        <div class="col-md-4">
                                                                            <div class="form-check">
                                                                                <input type="checkbox"
                                                                                    class="form-check-input permission-checkbox"
                                                                                    name="permissions[]"
                                                                                    id="permission_{{ $permission->id }}"
                                                                                    value="{{ $permission->name }}"
                                                                                    data-category="{{ $category }}"
                                                                                    {{ isset($user) && $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
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
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">Create User</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Select/Deselect all global buttons
            $('#select_all').click(function() {
                $('input.permission-checkbox').prop('checked', true);
            });

            $('#deselect_all').click(function() {
                $('input.permission-checkbox').prop('checked', false);
            });

            // Select/Deselect all within a category
            $('.select-category').click(function() {
                var category = $(this).data('category');
                $('input[data-category="' + category + '"]').prop('checked', true);
            });

            $('.deselect-category').click(function() {
                var category = $(this).data('category');
                $('input[data-category="' + category + '"]').prop('checked', false);
            });

            // Handle role change and update permissions
            $('#role').on('change', function() {
                var roleId = $(this).val();

                // Fetch permissions for the selected role via AJAX
                $.ajax({
                    url: '{{ route('admin.role.permissions') }}', // Create a route that returns the permissions for the selected role
                    method: 'GET',
                    data: {
                        role_id: roleId
                    },
                    success: function(response) {
                        // Reset all permissions checkboxes
                        $('input.permission-checkbox').prop('checked', false);

                        // Loop through the permissions and check the appropriate ones
                        response.permissions.forEach(function(permission) {
                            $('input.permission-checkbox[value="' + permission + '"]')
                                .prop('checked', true);
                        });
                    }
                });
            });
        });
    </script>

@endsection
