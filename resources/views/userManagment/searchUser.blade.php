@extends('layouts.index')

@section('content')
<div class="container py-5">
      @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            @endif
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="mb-4">
                <a href="{{ route('index.user') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Back to User Management
                </a>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Edit User Account</h4>
                        <p class="text-muted small">Update information for <strong>{{ $user->name }}</strong></p>
                    </div>

                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="text-center mb-5">
                            <div class="position-relative d-inline-block">
                                <img src="{{ $user->profile_image ? asset('storage/user_image/'.$user->profile_image) : asset('default-avatar.png') }}"
                                     id="preview" class="rounded-circle border p-1" width="120" height="120" style="object-fit: cover;">

                                <label for="profile_image" class="position-absolute bottom-0 end-0 btn btn-primary rounded-circle shadow-sm btn-sm">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                                <input type="file" name="profile_image" id="profile_image" class="d-none" onchange="previewImage(event)">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 12px;">
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="form-control bg-light border-0 py-3 px-4" style="border-radius: 12px;">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Role</label>
                                <select name="role" class="form-select bg-light border-0 py-3 px-4" style="border-radius: 12px;">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
                                <select name="is_active" class="form-select bg-light border-0 py-3 px-4" style="border-radius: 12px;">
                                    <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}>Active </option>
                                    <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-5 p-3 rounded-4" style="background-color: #f8f9fa;">
                            <label class="form-label small fw-bold text-muted text-uppercase">Security</label>
                            <input type="password" name="password" class="form-control border-0 py-2" placeholder="Enter new password (optional)">
                            <div class="form-text small">Leave blank to keep the current password.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="border-radius: 15px;">
                            Update Changes
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
