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
    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">{{ session('error') }}</div>
    @endif

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
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to User Management
                </a>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 24px;">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-dark">Edit User Account</h4>
                        <p class="text-muted small">Update information for <strong>{{ $user->name }}</strong></p>
                    </div>

                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="text-center mb-5">
                            <div class="position-relative d-inline-block">
                                <img src="{{ $user->profile_image ? asset('storage/user_image/'.$user->profile_image) : asset('assets/img/default-avatar.png') }}"
                                     id="preview"
                                     class="rounded-circle border p-1 shadow-sm"
                                     width="130" height="130"
                                     style="object-fit: cover; background: #f8f9fa;">

                                <label for="profile_image" class="position-absolute bottom-0 end-0 btn btn-primary rounded-circle shadow btn-sm p-2">
                                    <i class="fa-solid fa-camera"></i>
                                </label>
                                <input type="file" name="profile_image" id="profile_image" class="d-none" onchange="previewImage(event)">
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-regular fa-user text-muted"></i></span>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control bg-light border-0 py-3 px-3" style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control bg-light border-0 py-3 px-3" style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-phone text-muted"></i></span>
                                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="form-control bg-light border-0 py-3 px-3" placeholder="03xx-xxxxxxx" style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">CNIC Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-regular fa-id-card text-muted"></i></span>
                                    <input type="text" name="cnic_no" value="{{ old('cnic_no', $user->cnic_no) }}" class="form-control bg-light border-0 py-3 px-3" placeholder="xxxxx-xxxxxxx-x" style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Residential Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-location-dot text-muted"></i></span>
                                    <textarea name="address" class="form-control bg-light border-0 py-3 px-3" rows="2" placeholder="Enter full address" style="border-radius: 0 12px 12px 0;">{{ old('address', $user->address) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Role</label>
                                <select name="role" class="form-select bg-light border-0 py-3 px-4" style="border-radius: 12px;" {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(auth()->id() == $user->id)
                                    <input type="hidden" name="role" value="{{ $user->getRoleNames()->first() }}">
                                    <div class="form-text x-small text-danger">You cannot change your own role.</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
                                <select name="is_active" class="form-select bg-light border-0 py-3 px-4" style="border-radius: 12px;" {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                    <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @if(auth()->id() == $user->id)
                                    <input type="hidden" name="is_active" value="1">
                                @endif
                            </div>

                            <div class="col-12 mt-4">
                                <div class="p-4 rounded-4" style="background-color: #f8f9fa; border: 1px dashed #dee2e6;">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Security Update</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-white border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                        <input type="password" name="password" class="form-control bg-white border-0 py-2 px-3" placeholder="Enter new password (optional)" style="border-radius: 0 12px 12px 0;">
                                    </div>
                                    <div class="form-text small text-muted">Leave blank to keep the current password.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm" style="border-radius: 16px;">
                                Update Changes
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
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
