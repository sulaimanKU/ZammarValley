@extends('layouts.index')

@section('content')
<div class="main-page-wrapper">
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Property Blocks</h3>
                <p class="text-muted small mb-0">Define sectors/blocks for the project (e.g. Block A, Executive Block)</p>
            </div>
            <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="collapse" data-bs-target="#addBox">
                <i class="bi bi-plus-lg me-2"></i> Add New Block
            </button>
        </div>

        <div class="collapse mb-4" id="addBox">
            <div class="quick-add-box p-4 shadow-sm border-0 glass-card">
                <form action="{{route('blocks.store')}}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-secondary">Block Name*</label>
                            <input type="text" name="name" class="form-control form-control-lg" placeholder="e.g. Block A" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-secondary">Short Description (Optional)</label>
                            <input type="text" name="description" class="form-control form-control-lg" placeholder="e.g. Near Main Entrance">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark btn-lg w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table table-hover custom-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 80px;">SR #</th>
                            <th>Block Details</th>
                            <th>Created On</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($blocks as $block)
                        <tr>
                            <td class="ps-4 text-secondary">{{$loop->iteration}}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="feature-icon me-3 bg-light p-2 rounded text-primary">
                                       <i class="fa-solid fa-table-cells-large"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark">{{$block->name}}</span>
                                        @if($block->description)
                                            <small class="text-muted">{{$block->description}}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{$block->created_at->format('d M, Y')}}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('blocks.edit', $block->id) }}" class="btn btn-sm btn-outline-primary border-0 me-1">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('blocks.destroy', $block->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Remove this block?')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
