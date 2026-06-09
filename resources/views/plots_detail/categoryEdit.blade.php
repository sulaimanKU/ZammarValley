@extends('layouts.index')

@section('content')
<div class="main-content-wrapper" style="background: #f0f2f9; padding: 40px;">
    <div class="container">
        <div class="bg-white shadow-lg rounded-4 overflow-hidden" style="max-width: 800px; margin: 0 auto;">
            <div class="p-4 bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">Edit Category: {{ $category->name }}</h4>
                <a href="{{ route('categories.view') }}" class="btn btn-sm btn-light fw-bold">Cancel</a>
            </div>

            <form action="{{ route('categories.update', $category->id) }}" method="POST" class="p-5">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-12">
                        <label class="fw-bold small text-muted mb-2">CATEGORY NAME</label>
                        <input type="text" name="name" value="{{ $category->name }}" class="form-control shadow-sm py-3" placeholder="e.g. Executive Block" required>
                    </div>

                    <div class="col-md-6">
                        <label class="fw-bold small text-muted mb-2">PREFIX CODE</label>
                        <input type="text" name="prefix" value="{{ $category->prefix }}" class="form-control shadow-sm py-3" placeholder="e.g. EXE">
                    </div>

                    <div class="col-md-6">
                        <label class="fw-bold small text-muted mb-2">PROPERTY TYPE</label>
                        <select name="property_type" class="form-select shadow-sm py-3">
@foreach ($propFeature as $pf)
        <option value="{{ $pf->name }}" {{ $category->property_type == $pf->name ? 'selected' : '' }}>
            {{ $pf->name }}
        </option>
    @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded-3 bg-light d-flex justify-content-between">
                            <small class="text-muted">Created: {{ $category->created_at->format('d M, Y') }}</small>
                            <small class="text-muted">Last Updated: {{ $category->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow">
                            Update Category Basic Info
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
