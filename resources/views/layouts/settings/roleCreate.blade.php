@extends('layouts.index')

@section('content')
<div class="container-fluid py-4">
    {{-- Ensure the route name matches your web.php --}}
    <form action="{{ route('role.store') }}" method="POST">
        @csrf

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Create New Role</h4>
                <p class="text-muted small">Group permissions by system modules</p>
            </div>
            <div class="gap-2 d-flex">
                <a href="{{ route('setting.view') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Save Role
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Role Name</label>
                            {{-- Change name="name" to name="role_name" to match your Controller --}}
                            <input type="text" name="role_name" class="form-control"
                                   placeholder="e.g. Finance Manager" value="{{ old('role_name') }}" required>
                            @error('role_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-muted text-uppercase">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="What can this user do?">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm small">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Select permissions from the modules on the right to define what this role can access.
                </div>
            </div>

            <div class="col-md-9">
                @foreach($groupedPermissions as $module => $permissions)
                <div class="card border-0 shadow-sm mb-3 overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle me-2" style="width:8px; height:8px;"></div>
                            <span class="fw-bold text-dark">{{ $module ?: 'General System' }}</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input select-all-module" type="checkbox" id="checkAll{{ Str::slug($module) }}">
                            <label class="form-check-label small fw-bold text-muted" for="checkAll{{ Str::slug($module) }}">Select Module</label>
                        </div>
                    </div>
                    <div class="card-body bg-light-subtle">
                        <div class="row g-3">
                            @foreach($permissions as $perm)
                            <div class="col-md-4 col-sm-6">
                                <div class="p-2 border rounded bg-white shadow-xs h-100 d-flex align-items-center transition-all hover-shadow">
                                    <div class="form-check mb-0 w-100">
                                        <input class="form-check-input perm-checkbox" type="checkbox"
                                               name="permissions[]" value="{{ $perm->id }}" id="perm{{ $perm->id }}">
                                        <label class="form-check-label small d-block cursor-pointer" for="perm{{ $perm->id }}">
                                            {{ ucwords(str_replace(['_', 'view', 'create', 'edit', 'delete'], [' ', 'View', 'Create', 'Edit', 'Delete'], $perm->name)) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Module 'Select All' Logic
    document.querySelectorAll('.select-all-module').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const cardBody = this.closest('.card').querySelector('.card-body');
            const checkboxes = cardBody.querySelectorAll('.perm-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    // Visual feedback when clicking a checkbox
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const parent = this.closest('.p-2');
            if(this.checked) {
                parent.style.borderColor = '#0d6efd';
                parent.style.backgroundColor = '#f0f7ff';
            } else {
                parent.style.borderColor = '#dee2e6';
                parent.style.backgroundColor = '#fff';
            }
        });
    });
</script>
@endpush
