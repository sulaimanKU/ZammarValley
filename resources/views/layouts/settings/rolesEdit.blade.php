@extends('layouts.index')

@section('content')
<div class="container-fluid py-4">
    <form action="{{ route('RolePermission.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Edit Role: {{ $role->name }}</h4>
                <p class="text-muted small">Update role identity and modify module permissions</p>
            </div>
            <div class="gap-2 d-flex">
                <a href="{{ route('setting.view') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Update Changes
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">ROLE NAME</label>
                            <input type="text" name="role_name" class="form-control" value="{{ $role->name }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-muted">DESCRIPTION</label>
                            <textarea name="description" class="form-control" rows="4">{{ $role->description }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                @foreach($groupedPermissions as $module => $permissions)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <span class="fw-bold text-primary"><i class="bi bi-box-seam me-2"></i>{{ $module ?: 'General' }}</span>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input select-all-module" type="checkbox" id="mod{{ Str::slug($module) }}">
                            <label class="form-check-label small fw-bold text-muted" for="mod{{ Str::slug($module) }}">Select All</label>
                        </div>
                    </div>
                    <div class="card-body bg-light-subtle">
                        <div class="row g-3">
                            @foreach($permissions as $perm)
                            <div class="col-md-4">
                                <div class="p-2 border rounded bg-white shadow-xs d-flex align-items-center {{ in_array($perm->id, $rolePermissions) ? 'border-primary bg-primary-subtle' : '' }}">
                                    <div class="form-check mb-0 w-100">
                                        <input class="form-check-input perm-checkbox" type="checkbox"
                                               name="permissions[]" value="{{ $perm->id }}" id="perm{{ $perm->id }}"
                                               {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label small d-block cursor-pointer" for="perm{{ $perm->id }}">
                                            {{ ucwords(str_replace(['_', 'view', 'create', 'edit', 'delete'], [' ', 'View', 'Add', 'Edit', 'Del'], $perm->name)) }}
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
    // Logic for 'Select All' per Module
    document.querySelectorAll('.select-all-module').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const cardBody = this.closest('.card').querySelector('.card-body');
            const checkboxes = cardBody.querySelectorAll('.perm-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                // Trigger change event to update visual styling
                cb.dispatchEvent(new Event('change'));
            });
        });
    });

    // Visual feedback for checkboxes
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const container = this.closest('.p-2');
            if(this.checked) {
                container.classList.add('border-primary', 'bg-primary-subtle');
            } else {
                container.classList.remove('border-primary', 'bg-primary-subtle');
            }
        });
    });
</script>
@endpush
