@extends('layouts.index')

@section('content')
@push('styles')
<style>


</style>
@endpush

<div class="inventory-container">

    <div class="top-header-row shadow-sm">
        <h5 class="header-title">Created Category List</h5>

        <div class="btn-group-custom">



            <a href="{{ route('categories.create') }}" class="btn-primary-custom">
                <i class="bi bi-plus-circle"></i> Create New Category
            </a>

            @can('add_plot')
            <a href="{{route('plot.add')}}" class="btn-primary-custom">
                <i class="bi bi-plus-circle"></i> Create New Plot
            </a>
            @endcan
        </div>
    </div>

    <div class="table-card shadow-sm">
        <div class="table-responsive">
           <table class="table table-hover mb-0 plotsTable">
    <thead>
        <tr>
            <th># S.No</th>
            <th class="ps-4">Plan Name</th>
            <th>Property Type</th>


             <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $category)
        <tr>
            <td class="text-muted">{{ $loop->iteration }}</td>
            <td>
                <a href="#" class="fw-bold text-primary text-decoration-none">
                    {{ $category->name }}
                </a>
                <div class="small text-muted" style="font-size: 0.7rem;">Code: {{ $category->prefix }}</div>
            </td>
            <td>
                <span class="badge-status-pending">{{ ucfirst($category->property_type) }}</span>
            </td>






            <td class="text-end">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{route('categories.show', $category->id)}}" class="action-view-btn">
                        <i class="bi bi-eye-fill"></i>
                    </a>

                    <a href="{{route('categories.edit', $category->id)}}" class="action-view-btn" style="background:#f1f5f9; color:#64748b;">
                        <i class="bi bi-pencil-fill"></i>
                    </a>

                    <form action="{{route('plot-categories.destroy',$category->id)}}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-view-btn border-0" style="background:#fee2e2; color:#ef4444;" onclick="return confirm('Delete this category?')">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
        </div>
    </div>
</div>
@endsection
