@extends('layouts.index')

@section('content')

<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-pencil-square text-primary me-2"></i>
            Edit Society
        </h3>
        <p class="text-muted small mb-0">
            Update the Society .
        </p>
    </div>

    <!-- Edit Card -->
    <div class="card border-0 shadow-sm" style="border-radius:15px;">
        <div class="card-body p-4">

            <form action="{{ route('society.update', $societyEdit->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 align-items-end">

                    <div class="col-md-9">
                        <label class="form-label fw-bold small text-secondary">
                            Society NAME <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               value="{{ old('name', $societyEdit->name) }}"
                               class="form-control form-control-lg"
                               placeholder="Enter Sector name..."
                               required>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Update Society
                        </button>

                        <a href="{{ route('society.view') }}"
                           class="btn btn-light btn-lg w-100 border">
                            Cancel
                        </a>
                    </div>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection
