@extends('layouts.index')

@push('styles')
<style>
    .input-group-text { border-radius: 12px 0 0 12px; }
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.05);
        border: 1px solid #0d6efd !important;
    }
    .form-label { margin-bottom: 0.4rem; }
</style>
@endpush

@section('content')
<div class="container py-5">
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">

            <div class="mb-4">
                <a href="{{ route('index.user') }}" class="text-decoration-none text-muted small fw-bold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to User List
                </a>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 24px;">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-5">
                        <h4 class="fw-bold text-dark">Create System User</h4>
                        <p class="text-muted small">Setup credentials and personal information for Zammar Valley staff</p>
                    </div>

                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="text-center mb-5">
                            <div class="position-relative d-inline-block">
                                <img src="{{ asset('assets/img/default-avatar.png') }}"
                                     id="preview"
                                     class="rounded-circle border p-1 shadow-sm"
                                     width="130" height="130"
                                     style="object-fit: cover; background: #f8f9fa;">

                                <label for="profile_image" class="position-absolute bottom-0 end-0 btn btn-primary rounded-circle shadow btn-sm p-2">
                                    <i class="fa-solid fa-camera"></i>
                                </label>
                                <input type="file" name="profile_image" id="profile_image" class="d-none" onchange="previewImage(event)">
                            </div>
                            <div class="mt-2 small text-muted">Upload Profile Photo</div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-regular fa-user text-muted"></i></span>
                                    <input type="text" name="name" class="form-control bg-light border-0 py-3 px-3" placeholder="Enter full name" value="{{ old('name') }}" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control bg-light border-0 py-3 px-3" placeholder="email@zammarvalley.com" value="{{ old('email') }}" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-phone text-muted"></i></span>
                                    <input type="text" name="phone_number" class="form-control bg-light border-0 py-3 px-3" placeholder="03xx-xxxxxxx" value="{{ old('phone_number') }}" style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">CNIC Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-regular fa-id-card text-muted"></i></span>
                                    <input type="text" name="cnic_no" class="form-control bg-light border-0 py-3 px-3" placeholder="42xxx-xxxxxxx-x" value="{{ old('cnic_no') }}" style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Residential Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-location-dot text-muted"></i></span>
                                    <textarea name="address" class="form-control bg-light border-0 py-3 px-3" rows="2" placeholder="Enter full address" style="border-radius: 0 12px 12px 0;">{{ old('address') }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">System Role</label>
                                <select name="role" class="form-select bg-light border-0 py-3 px-4" style="border-radius: 12px;" required>
                                    <option value="" selected disabled>Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Account Status</label>
                                <select name="is_active" class="form-select bg-light border-0 py-3 px-4" style="border-radius: 12px;">
                                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                    <input type="password" name="password" class="form-control bg-light border-0 py-3 px-3" placeholder="Create a strong password" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm" style="border-radius: 16px;">
                                <i class="fa-solid fa-user-plus me-2"></i> Create User Account
                            </button>
                            <a href="{{ route('index.user') }}" class="btn btn-link text-decoration-none text-muted">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
