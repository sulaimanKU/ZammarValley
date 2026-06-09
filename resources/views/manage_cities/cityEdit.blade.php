@extends('layouts.index')

@section('content')

<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-pencil-square text-primary me-2"></i>
            Edit City
        </h3>
        <p class="text-muted small mb-0">
            Update the City name .
        </p>
    </div>

    <!-- Edit Card -->
    <div class="card border-0 shadow-sm" style="border-radius:15px;">
        <div class="card-body p-4">

            <form action="{{ route('city.update', $city->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 align-items-end">

                    <div class="col-md-9">
                        <label class="form-label fw-bold small text-secondary">
                            City NAME <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               value="{{ old('name', $city->name) }}"
                               class="form-control form-control-lg"
                               placeholder="Enter city name..."
                               required>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Update City
                        </button>

                        <a href="{{ route('city.view') }}"
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
