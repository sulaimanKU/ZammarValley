@extends('layouts.index')

@push('styles')
<style>
    .main-content-wrapper { min-height: 100vh; padding: 40px; background-color: #f4f7fe; }
    .detail-card {
        background: white;
        border-radius: 30px;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.03);
        overflow: hidden;
        max-width: 900px;
        margin: 0 auto;
    }
    .spec-label { font-size: 0.75rem; text-transform: uppercase; color: #a3aed0; font-weight: 700; letter-spacing: 1px; }
    .spec-value { font-size: 1.2rem; color: #2b3674; font-weight: 700; margin-top: 5px; }
    .category-badge {
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="main-content-wrapper">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 mx-auto" style="max-width: 900px;">
            <div>
                <a href="{{ route('categories.view') }}" class="text-decoration-none small fw-bold text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Back to All Categories
                </a>
                <h2 class="fw-bold mt-2" style="color: #2b3674;">Category Details</h2>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-light px-4 fw-bold" style="border-radius: 12px; border: 1px solid #e0e5f2;">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            </div>
        </div>

        <div class="detail-card p-5">
            <div class="row align-items-center mb-5">
                <div class="col-auto">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; color: white;">
                        <i class="bi bi-tag-fill fs-2"></i>
                    </div>
                </div>
                <div class="col">
                    <h3 class="fw-bold mb-1" style="color: #1b2559;">{{ $category->name }}</h3>
                    <span class="category-badge {{ $category->property_type == 'Residential' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}"
                          style="background: {{ $category->property_type == 'Residential' ? '#e6f6ed' : '#fff5e6' }};">
                        {{ strtoupper($category->property_type) }} UNIT
                    </span>
                </div>
            </div>

            <hr class="my-5 opacity-25">

            <div class="row g-5">
                <div class="col-md-4">
                    <span class="spec-label">Internal Short Code</span>
                    <div class="spec-value">{{ $category->prefix ?? '---' }}</div>
                </div>

                <div class="col-md-4">
                    <span class="spec-label">Created Date</span>
                    <div class="spec-value">{{ $category->created_at->format('d M, Y') }}</div>
                </div>

                <div class="col-md-4">
                    <span class="spec-label">Last Updated</span>
                    <div class="spec-value">{{ $category->updated_at->diffForHumans() }}</div>
                </div>
            </div>

            <div class="mt-5 p-4 rounded-4" style="background: #f8faff; border: 1px solid #edf1f7;">
                <p class="mb-0 text-muted small">
                    <i class="bi bi-info-circle me-2"></i>
                    This category is currently being used to organize plots in your inventory. Changing the name or prefix will update all associated records.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
