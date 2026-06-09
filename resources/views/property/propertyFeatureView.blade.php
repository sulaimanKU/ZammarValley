@extends('layouts.index')

@push('styles')
<style>
    /* Professional padding to ensure no sidebar touching */
    .main-page-wrapper {
        padding: 2.5rem;
        /* background-color: #f8f9fa; */
        min-height: 100vh;
    }

    .glass-card {
        background: #ffffff;
        border-radius: 15px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .custom-table thead {
        background-color: #fcfcfc;
        border-bottom: 2px solid #f1f1f1;
    }

    .custom-table th {
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.8px;
        padding: 1.2rem 1rem;
    }

    .custom-table td {
        padding: 1.2rem 1rem;
        vertical-align: middle;
    }

    /* Icon Box */
    .feature-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f7ff;
        color: #007bff;
        font-size: 1.1rem;
    }

    /* Add Form Box */
    .quick-add-box {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 15px;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="main-page-wrapper">
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Property Features</h3>
                <p class="text-muted small mb-0">Manage and define specific plot attributes</p>
            </div>
            <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="collapse" data-bs-target="#addBox">
                <i class="bi bi-plus-lg me-2"></i> Add New Feature
            </button>
        </div>

        <div class="collapse" id="addBox">
            <div class="quick-add-box p-4">
                <form action="{{route('property.feature.store')}}" class="row g-3 align-items-end" method="POST">
                    @csrf
                    <div class="col-md-9">
                        <label class="form-label fw-bold small text-secondary">FEATURE NAME*(Like corner,Main Road etc)</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Enter feature name...">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-dark btn-lg w-100">Save Feature</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table table-hover custom-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 100px;">SR #</th>
                            <th>Feature Name</th>
                            <th>Created On</th>

                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pro_features as $pro_feature)

                        <tr>
                            <td class="ps-4">{{$loop->iteration}}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="feature-icon me-3">
                                        <i class="bi bi-bounding-box"></i>
                                    </div>
                                    <span class="fw-bold">{{$pro_feature->name}}</span>
                                </div>
                            </td>
                            <td>{{$pro_feature->created_at}}</td>

                           <td class="text-end pe-4">
    <a href="{{ route('property.feature.edit', $pro_feature->id) }}"
       class="btn btn-sm btn-outline-primary border-0 me-2">
        <i class="bi bi-pencil-square"></i>
    </a>

    <form action="{{ route('property.feature.destroy', $pro_feature->id) }}"
          method="POST"
          style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="btn btn-sm btn-outline-danger border-0"
                onclick="return confirm('Are you sure?')">
            <i class="bi bi-trash3"></i>
        </button>
    </form>
</td>
                        </tr>
                        <tr>


                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
