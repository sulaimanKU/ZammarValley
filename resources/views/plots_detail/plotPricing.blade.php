@extends('layouts.index')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: #1a1a2e;">Pricing Plans</h2>
            <p class="text-muted mb-0 small">Manage plot pricing and installment plans</p>
        </div>
        <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addPricingPlanModal">
            <i class="fas fa-plus me-2"></i> Add Pricing Plan
        </button>
    </div>

    {{-- Flash Messages --}}

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            {{-- {{ route('pricing-plans.index') }} --}}
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="is_active" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Unit</label>
                    <select name="unit" class="form-select form-select-sm">
                        <option value="">All Units</option>
                        <option value="Marla" {{ request('unit') == 'Marla' ? 'selected' : '' }}>Marla</option>
                        <option value="Kanal" {{ request('unit') == 'Kanal' ? 'selected' : '' }}>Kanal</option>
                        <option value="Sqft" {{ request('unit') == 'Sqft' ? 'selected' : '' }}>Sqft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Pricing Plans Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fc;">
                        <tr>
                            <th class="px-4 py-3 text-muted small fw-semibold">#</th>
                            <th class="py-3 text-muted small fw-semibold">Category</th>
                            <th class="py-3 text-muted small fw-semibold">Size</th>
                            <th class="py-3 text-muted small fw-semibold">Base Price</th>
                            <th class="py-3 text-muted small fw-semibold">Down Payment</th>
                            <th class="py-3 text-muted small fw-semibold">Processing Fee</th>
                            <th class="py-3 text-muted small fw-semibold">Installments</th>
                            <th class="py-3 text-muted small fw-semibold">Per Installment</th>
                            <th class="py-3 text-muted small fw-semibold">Effective From</th>
                            <th class="py-3 text-muted small fw-semibold">Status</th>
                            <th class="py-3 text-muted small fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pricingPlans as $plan)
                        <tr>
                            <td class="px-4">{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2" style="background-color: #e8eaf6; color: #3949ab;">
                                    {{ $plan->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="fw-semibold">{{ $plan->size }} {{ $plan->unit }}</td>
                            <td>PKR {{ number_format($plan->base_price) }}</td>
                            <td>PKR {{ number_format($plan->down_payment) }}</td>
                            <td>PKR {{ number_format($plan->processing_fee) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $plan->total_installments }} months</span>
                            </td>
                            <td>PKR {{ number_format($plan->installment_amount) }}</td>
                            <td class="text-muted small">{{ \Carbon\Carbon::parse($plan->effective_from)->format('d M Y') }}</td>
                            <td>
                                @if($plan->is_active)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit Button --}}
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editPricingPlanModal"
                                        data-id="{{ $plan->id }}"
                                        data-category="{{ $plan->plot_category_id }}"
                                        data-size="{{ $plan->size }}"
                                        data-unit="{{ $plan->unit }}"
                                        data-base_price="{{ $plan->base_price }}"
                                        data-down_payment="{{ $plan->down_payment }}"
                                        data-processing_fee="{{ $plan->processing_fee }}"
                                        data-total_installments="{{ $plan->total_installments }}"
                                        data-installment_amount="{{ $plan->installment_amount }}"
                                        data-effective_from="{{ $plan->effective_from }}"
                                        data-is_active="{{ $plan->is_active }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Toggle Active --}}
                                    {{-- {{ route('pricing-plans.toggle', $plan->id) }} --}}
                                    {{-- <form action="" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $plan->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            title="{{ $plan->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas {{ $plan->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form> --}}

                                    {{-- Delete --}}

                                    <form action=" {{ route('plot-pricing.destroy', $plan->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this pricing plan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice-dollar fa-3x mb-3 d-block opacity-25"></i>
                                No pricing plans found.
                                <a href="#" data-bs-toggle="modal" data-bs-target="#addPricingPlanModal">Add one now</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($pricingPlans->hasPages())
            <div class="d-flex justify-content-end px-4 py-3 border-top">
                {{ $pricingPlans->links() }}
            </div>
            @endif
        </div>
    </div>

</div>


{{-- ==================== ADD MODAL ==================== --}}
<div class="modal fade" id="addPricingPlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Pricing Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action=" {{ route('pricing-plans.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-3">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="plot_category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Size <span class="text-danger">*</span></label>
                            <input type="number" name="size" step="0.01" class="form-control" placeholder="e.g. 5" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Unit <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Marla">Marla</option>
                                <option value="Kanal">Kanal</option>
                                <option value="Sqft">Sqft</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Base Price (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="base_price" step="0.01" class="form-control" placeholder="0.00" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Down Payment (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="down_payment" step="0.01" class="form-control" placeholder="0.00" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Processing Fee (PKR)</label>
                            <input type="number" name="processing_fee" step="0.01" class="form-control" placeholder="0.00">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Total Installments <span class="text-danger">*</span></label>
                            <input type="number" name="total_installments" class="form-control" placeholder="e.g. 36" >
                        </div>

                        {{-- <div class="col-md-6">
                            <label class="form-label small fw-semibold">Installment Amount (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="installment_amount" step="0.01" class="form-control" placeholder="0.00" >
                        </div> --}}

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Effective From <span class="text-danger">*</span></label>
                            <input type="date" name="effective_from" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ==================== EDIT MODAL ==================== --}}
<div class="modal fade" id="editPricingPlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Pricing Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
      <form id="editPricingPlanForm" action="" method="POST">
                    @csrf
                @method('PUT')
                <div class="modal-body pt-3">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="plot_category_id" id="edit_plot_category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Size <span class="text-danger">*</span></label>
                            <input type="number" name="size" id="edit_size" step="0.01" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Unit <span class="text-danger">*</span></label>
                            <select name="unit" id="edit_unit" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Marla">Marla</option>
                                <option value="Kanal">Kanal</option>
                                <option value="Sqft">Sqft</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Base Price (PKR)</label>
                            <input type="number" name="base_price" id="edit_base_price" step="0.01" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Down Payment (PKR)</label>
                            <input type="number" name="down_payment" id="edit_down_payment" step="0.01" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Processing Fee (PKR)</label>
                            <input type="number" name="processing_fee" id="edit_processing_fee" step="0.01" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Total Installments</label>
                            <input type="number" name="total_installments" id="edit_total_installments" class="form-control">
                        </div>



                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Effective From</label>
                            <input type="date" name="effective_from" id="edit_effective_from" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="is_active" id="edit_is_active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ==================== SCRIPTS ==================== --}}
@push('scripts')
<script>

document.getElementById('editPricingPlanModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const id  = btn.getAttribute('data-id');

    // ✅ Set action first
    document.getElementById('editPricingPlanForm').action = `/pricing-plan/${id}/update`;

    // Fill fields
    document.getElementById('edit_plot_category_id').value    = btn.getAttribute('data-category');
    document.getElementById('edit_size').value                = btn.getAttribute('data-size');
    document.getElementById('edit_unit').value                = btn.getAttribute('data-unit');
    document.getElementById('edit_base_price').value          = btn.getAttribute('data-base_price');
    document.getElementById('edit_down_payment').value        = btn.getAttribute('data-down_payment');
    document.getElementById('edit_processing_fee').value      = btn.getAttribute('data-processing_fee');
    document.getElementById('edit_total_installments').value  = btn.getAttribute('data-total_installments');
    document.getElementById('edit_effective_from').value      = btn.getAttribute('data-effective_from');
    document.getElementById('edit_is_active').value           = btn.getAttribute('data-is_active');
});
</script>
@endpush

@endsection
