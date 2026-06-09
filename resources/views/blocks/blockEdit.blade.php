@extends('layouts.index')

@section('content')

<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-pencil-square text-primary me-2"></i>
            Edit Property Feature
        </h3>
        <p class="text-muted small mb-0">
            Update the feature name like Corner, Park Facing, Boulevard etc.
        </p>
    </div>

    <!-- Edit Card -->
    <div class="card border-0 shadow-sm" style="border-radius:15px;">
        <div class="card-body p-4">

           <form action="{{ route('blocks.update', $blockEdit->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-bold small text-secondary">
                Block NAME <span class="text-danger">*</span>
            </label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $blockEdit->name) }}"
                   class="form-control form-control-lg"
                   placeholder="Enter Block name..."
                   required>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold small text-secondary">
                Description (Optional)
            </label>
            <input type="text"
                   name="description"
                   value="{{ old('description', $blockEdit->description) }}"
                   class="form-control form-control-lg"
                   placeholder="e.g. Near Main Boulevard...">
        </div>

        <div class="col-12 mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                Update Block
            </button>

            <a href="{{ route('blocks.index') }}"
               class="btn btn-light btn-lg px-5 border">
                Cancel
            </a>
        </div>
    </div>
</form>

        </div>
    </div>

</div>

@endsection
