@extends('layouts.index')

@push('styles')
<style>

</style>
@endpush

@section('content')
<div class="main-content-wrapper">
    <div class="premium-container">
        <div class="form-content">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1 class="heading-title fw-bold m-0" style="color: #1b2559;">Create Category</h1>
                <a href="{{ route('categories.view') }}" class="text-muted text-decoration-none">
                    <i class="bi bi-x-lg fs-5"></i>
                </a>
            </div>

            <form id="catForm" action="{{ route('categories.store') }}" method="POST">
                @csrf

                <span class="section-header">Basic Category Details</span>

                <label class="friendly-label">CATEGORY NAME</label>
                <input type="text" name="name" class="modern-input" placeholder="e.g. Executive Block" required>

                <div class="row">
                    <div class="col-md-6">
                        <label class="friendly-label">ID SHORT CODE (PREFIX)</label>
                        <input type="text" name="prefix" class="modern-input" placeholder="e.g. EXE">
                    </div>

                    <div class="col-md-6">
                        <label class="friendly-label">PROPERTY CLASS</label>
                        <select name="property_type" class="modern-input">
                            @foreach ($propFeature as $pf)
                                <option value="{{ $pf->name }}">{{ $pf->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check2-circle me-2"></i> Save Category
                    </button>
                </div>

                <div class="mt-4 text-center">
                    <small class="text-muted">Only the name, prefix, and type will be stored in this category template.</small>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
