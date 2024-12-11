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

                                <!-- Type Field -->
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Type</label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
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
                                <!-- Permissions Field -->
                                @if (isset($permissions) && $permissions->count() > 0)
                                    <div class="col-12">
                                        <label class="form-label">Permissions</label>
                                        <div class="row">
                                            @foreach ($permissions as $permission)
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input type="checkbox" name="permissions[]"
                                                            value="{{ $permission->name }}"
                                                            id="permission_{{ $permission->id }}" class="form-check-input">
                                                        <label for="permission_{{ $permission->id }}"
                                                            class="form-check-label">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
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
