@extends('layouts.index')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
@push('styles')
<style>
/* ── Pagination ─────────────────────────────────────── */
.cust-pagination .pagination {
    margin: 0;
    gap: 3px;
    display: flex;
    flex-wrap: wrap;
}
.cust-pagination .page-item .page-link {
    border: 1.5px solid #e2e8f0;
    border-radius: 8px !important;
    color: #1e3a8a;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 11px;
    min-width: 34px;
    text-align: center;
    line-height: 1.5;
    background: #fff;
    transition: all .15s;
}
.cust-pagination .page-item .page-link:hover {
    background: #eff6ff;
    border-color: #93c5fd;
}
.cust-pagination .page-item.active .page-link {
    background: #1e3a8a;
    border-color: #1e3a8a;
    color: #fff;
}
.cust-pagination .page-item.disabled .page-link {
    color: #cbd5e1;
    background: #f8fafc;
    border-color: #e2e8f0;
}
</style>
@endpush

<div class="container-fluid py-3 px-3 px-md-4">

{{-- ── Page Header ─────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <p class="page-header-title">
            <i class="bi bi-people-fill" style="margin-right:8px;opacity:.7;"></i>
            Client Directory
        </p>
        <p class="page-header-sub">Manage investors, view bookings and payment history</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;position:relative;z-index:1;">
        <a href="#" onclick="exportCustomers()" class="btn-soft" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.15);color:rgba(255,255,255,.8);">
            <i class="bi bi-download"></i> Export
        </a>
        <button class="btn-navy" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
            <i class="bi bi-plus-lg"></i> Add Client
        </button>
    </div>
</div>

{{-- ── Flash Messages ──────────────────────────────────────── --}}
@if(session('success'))
<div class="flash flash-success">
    <i class="bi bi-check-circle-fill" style="flex-shrink:0;font-size:1rem;"></i>
    {{ session('success') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button>
</div>
@endif
@if(session('error'))
<div class="flash flash-error">
    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;font-size:1rem;"></i>
    {{ session('error') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button>
</div>
@endif
@if($errors->any())
<div class="flash flash-error">
    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;font-size:1rem;"></i>
    <div><strong>Please fix:</strong><ul style="margin:4px 0 0 14px;padding:0;font-size:12px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>{ new bootstrap.Modal(document.getElementById('addCustomerModal')).show(); });</script>
@endif

{{-- ── Stat Row ─────────────────────────────────────────────── --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;"><i class="bi bi-people-fill" style="color:#1d4ed8;"></i></div>
        <div><div class="stat-label">Total Clients</div><div class="stat-val">{{ number_format($totalCustomers) }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7;"><i class="bi bi-check-circle-fill" style="color:#16a34a;"></i></div>
        <div><div class="stat-label">Active</div><div class="stat-val">{{ number_format($activeCustomers) }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fdf4ff;"><i class="bi bi-person-plus-fill" style="color:#7c3aed;"></i></div>
        <div><div class="stat-label">New This Month</div><div class="stat-val">{{ number_format($newThisMonth) }}</div></div>
    </div>
</div>

{{-- ── Main Card ────────────────────────────────────────────── --}}
<div class="main-card">
    <div class="card-toolbar">
        <form method="GET" action="{{ route('index.customer') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Search name, CNIC, phone…"
                       value="{{ request('search') }}" autocomplete="off">
            </div>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active"   {{ request('status')==='active'  ?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
            </select>
            @if(request('search') || request('status'))
            <a href="{{ route('index.customer') }}" class="btn-soft" style="padding:8px 12px;">
                <i class="bi bi-x-lg"></i> Clear
            </a>
            @endif
        </form>
        <span style="font-size:11px;color:var(--slate);font-weight:600;">
            {{ $customers->total() }} client{{ $customers->total()!==1?'s':'' }}
        </span>
    </div>

    <div class="cust-grid">
        @forelse($customers as $c)
        <div class="cust-card">
            <div class="cust-card-top">
                @if($c->customer_pic)
                    <img src="{{ asset($c->customer_pic) }}" class="cust-avatar" alt="{{ $c->name }}">
                @else
                    <div class="cust-avatar-placeholder">{{ strtoupper(substr($c->name,0,1)) }}</div>
                @endif
                <div style="min-width:0;">
                    <div class="cust-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $c->name }}</div>
                    <div class="cust-id">{{ $c->cnic }}</div>
                    <div style="margin-top:5px;">
                        <span class="{{ $c->status==='active'?'badge-active':'badge-inactive' }}">{{ ucfirst($c->status) }}</span>
                        <span class="badge-plots" style="margin-left:4px;">{{ $c->bookings_count }} plot{{ $c->bookings_count!==1?'s':'' }}</span>
                    </div>
                </div>
            </div>
            <div class="cust-card-body">
                @if($c->guardian_name)
                <div class="cust-row"><i class="bi bi-person" style="color:#94a3b8;"></i><span>S/O: <strong>{{ $c->guardian_name }}</strong></span></div>
                @endif
                <div class="cust-row"><i class="bi bi-phone" style="color:#3b82f6;"></i><strong>{{ $c->phone }}</strong></div>
                @if($c->email)
                <div class="cust-row"><i class="bi bi-envelope" style="color:#7c3aed;"></i><span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $c->email }}</span></div>
                @endif
                @if($c->city)
                <div class="cust-row"><i class="bi bi-geo-alt" style="color:#dc2626;"></i><span>{{ $c->city }}</span></div>
                @endif
                <div class="cust-row" style="margin-top:6px;"><i class="bi bi-calendar3" style="color:#94a3b8;"></i><span style="font-size:11px;">Joined {{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}</span></div>
            </div>
            <div class="cust-card-footer">
                <a href="{{ route('customers.show',$c->id) }}"
                   style="flex:1;text-align:center;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:7px;border-radius:9px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:5px;">
                    <i class="bi bi-eye"></i> View
                </a>
                <button onclick="openEditModal({{ $c->id }})"
                        style="flex:1;text-align:center;background:#f8fafc;border:1px solid var(--border);color:var(--slate);padding:7px;border-radius:9px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:5px;">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <form action="{{ route('customers.destroy',$c->id) }}" method="POST"
                      onsubmit="return confirm('Delete {{ addslashes($c->name) }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:7px 10px;border-radius:9px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column:1/-1;">
            <i class="bi bi-people"></i>
            <p style="font-size:14px;font-weight:700;margin:0 0 6px;">No clients found</p>
            <p style="font-size:12px;margin:0;">
                @if(request('search') || request('status'))
                    Try adjusting your search or <a href="{{ route('index.customer') }}">clear filters</a>.
                @else
                    Click "Add Client" to register your first customer.
                @endif
            </p>
        </div>
        @endforelse
    </div>

    <div style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <span style="font-size:12px;color:var(--slate);font-weight:600;">
            @if($customers->total() > 0)
                Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} client{{ $customers->total() !== 1 ? 's' : '' }}
                &nbsp;·&nbsp; Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }}
            @else
                No clients found
            @endif
        </span>
        @if($customers->hasPages())
        <div class="cust-pagination">{{ $customers->links() }}</div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ADD CUSTOMER MODAL
     ─────────────────────────────────────────────────────────
     FIX: <form> is ONLY inside .modal-body.
     The "Save Client" button in .modal-footer uses form="addCustomerForm"
     (HTML5 standard attribute) so it submits the form even though it
     lives outside the <form> tag. This preserves Bootstrap's flex
     layout for modal-dialog-scrollable so the footer stays visible.
════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New Client (Zamar Valley)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="addCustomerForm"
                      action="{{ route('customers.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    {{-- All fields start here --}}
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-2 bg-light border-start border-primary border-4 fw-bold text-primary mb-2">
                                <i class="bi bi-person-badge me-2"></i> Client Information
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control" placeholder="Father or Husband Name">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">CNIC <span class="text-danger">*</span></label>
                            <input type="text" name="cnic" class="form-control" placeholder="xxxxx-xxxxxxx-x" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="Pakistani">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Age</label>
                            <input type="number" name="age" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Occupation</label>
                            <input type="text" name="occupation" class="form-control" placeholder="Occupation">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="customer@email.com">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Mobile (WhatsApp) <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="03xx-xxxxxxx" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Phone (Office)</label>
                            <input type="text" name="phone_off" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Phone (Res)</label>
                            <input type="text" name="phone_res" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Residential Address</label>
                            <textarea name="residential_address" class="form-control" rows="2" placeholder="Primary Address"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Postal Address</label>
                            <textarea name="postal_address" class="form-control" rows="2" placeholder="Mailing Address"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Customer Photo</label>
                            <input type="file" name="customer_pic" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">CNIC Front Copy</label>
                            <input type="file" name="cnic_pic" class="form-control form-control-sm">
                        </div>

                        <div class="col-12 mt-4">
                            <div class="p-2 bg-light border-start border-success border-4 fw-bold text-success mb-2">
                                <i class="bi bi-people-fill me-2"></i> Nominee Details
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Nominee Name</label>
                            <input type="text" name="nominee_name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Relation</label>
                            <input type="text" name="nominee_relation" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Nominee CNIC</label>
                            <input type="text" name="nominee_cnic" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Nominee Address</label>
                            <input type="text" name="nominee_address" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Nominee Photo</label>
                            <input type="file" name="nominee_pic" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Nominee CNIC (Front)</label>
                            <input type="file" name="nominee_cnic_front" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Nominee CNIC (Back)</label>
                            <input type="file" name="nominee_cnic_back" class="form-control form-control-sm">
                        </div>
                    </div>
                    {{-- End of fields --}}

                </form>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addCustomerForm" class="btn btn-primary px-4 shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Save Zamar Valley Client
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     EDIT CUSTOMER MODAL
     Same fix: form inside modal-body, button uses form="editCustomerForm"
════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-fill me-2"></i>Edit Client</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- ✅ form lives ONLY inside modal-body --}}
            <div class="modal-body" id="editModalBody">
                {{-- JS injects the <form id="editCustomerForm"> here --}}
                <div id="editLoadingState" style="text-align:center;padding:40px;color:var(--slate);">
                    <div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading...
                </div>
            </div>

            {{-- ✅ footer always visible --}}
            <div class="modal-footer">
                <button type="button" class="btn-soft" data-bs-dismiss="modal">Cancel</button>
                {{-- form="editCustomerForm" — the form is rendered by JS inside modal-body --}}
                <button type="submit" form="editCustomerForm" class="btn-navy" id="editSubmitBtn">
                    <i class="bi bi-check-lg"></i> Update Client
                </button>
            </div>

        </div>
    </div>
</div>

</div>{{-- /container --}}
@endsection

@push('scripts')
<script>
// ── Open Edit Modal ───────────────────────────────────────────
function openEditModal(id) {
    const modalEl = document.getElementById('editCustomerModal');
    const modal   = new bootstrap.Modal(modalEl);
    const body    = document.getElementById('editModalBody');
    const btn     = document.getElementById('editSubmitBtn');

    // Show loading, disable submit while loading
    body.innerHTML = `<div style="text-align:center;padding:40px;color:#64748b;">
        <div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading...
    </div>`;
    btn.disabled = true;

    modal.show();

    // Fetch customer data via JSON
    fetch(`/customers/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(c => {
        // Inject the form inside modal-body with id="editCustomerForm"
        // The submit button in footer already has form="editCustomerForm" → works automatically
        body.innerHTML = buildEditForm(c, id);
        btn.disabled = false;
        initPreviews();
    })
    .catch(() => {
        body.innerHTML = `<div class="flash flash-error">
            <i class="bi bi-exclamation-circle-fill"></i> Failed to load customer data. Please try again.
        </div>`;
        btn.disabled = false;
    });
}

// ── Build edit form HTML dynamically ─────────────────────────
function buildEditForm(c, id) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // Helper to render existing images or placeholders
    const renderBox = (path, previewId, inputName, icon, label, colSize = 'col-md-3') => {
        const hasFile = path && path !== '';
        return `
            <div class="${colSize}">
                <label class="form-label-c">${label}</label>
                <div class="upload-box shadow-sm" onclick="document.getElementById('${inputName}').click()"
                     style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 12px; text-align: center; cursor: pointer; background: #ffffff;">
                    <img src="/${path}" class="upload-preview" id="${previewId}"
                         style="width: 100%; height: 90px; object-fit: cover; border-radius: 4px; margin-bottom: 8px; ${hasFile ? '' : 'display:none;'}">
                    <div id="placeholder_${previewId}" style="${hasFile ? 'display:none;' : ''}">
                        <i class="bi ${icon}" style="font-size: 1.8rem; color: #94a3b8;"></i>
                        <p style="font-size: 10px; color: #64748b; margin: 0;">Upload ${label}</p>
                    </div>
                    <input type="file" name="${inputName}" id="${inputName}" hidden
                           onchange="previewStepImage(this, '${previewId}', 'placeholder_${previewId}')">
                </div>
            </div>`;
    };

    return `
    <form id="editCustomerForm" action="/customers/${id}" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="${csrf}">
        <input type="hidden" name="_method" value="PUT">

        <div class="p-3 mb-4" style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; border-top: 4px solid #1e293b;">
            <h6 class="mb-3" style="font-weight: 800; color: #1e293b; display:flex; align-items:center;">
                <span style="background:#1e293b; color:white; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; font-size:12px; margin-right:8px;">1</span>
                CUSTOMER DETAILS
            </h6>
            <div class="row g-3">
                <div class="col-md-7">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label-c">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="${esc(c.name)}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-c">S/O, D/O, W/O</label>
                            <input type="text" name="guardian_name" class="form-control" value="${esc(c.guardian_name||'')}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-c">CNIC Number *</label>
                            <input type="text" name="cnic" class="form-control" value="${esc(c.cnic)}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-c">Phone / Mobile *</label>
                            <input type="text" name="phone" class="form-control" value="${esc(c.phone||c.mobile||'')}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-c">Age</label>
                            <input type="number" name="age" class="form-control" value="${esc(c.age||'')}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-c">Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="${esc(c.nationality||'')}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-c">Occupation</label>
                            <input type="text" name="occupation" class="form-control" value="${esc(c.occupation||'')}">
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="row g-2">
                        ${renderBox(c.customer_pic, 'prev_cust', 'customer_pic', 'bi-camera', 'Profile Pic', 'col-6')}
                        ${renderBox(c.cnic_pic, 'prev_cust_cnic', 'cnic_pic', 'bi-file-earmark-person', 'CNIC Doc', 'col-6')}
                    </div>
                </div>
            </div>

            <div class="row g-2 mt-2">
                <div class="col-md-6">
                    <label class="form-label-c">Residential Address</label>
                    <textarea name="residential_address" class="form-control" rows="2">${esc(c.residential_address||c.address||'')}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label-c">Postal Address</label>
                    <textarea name="postal_address" class="form-control" rows="2">${esc(c.postal_address||'')}</textarea>
                </div>
            </div>
        </div>

        <div class="p-3 mb-2" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; border-top: 4px solid #0ea5e9;">
            <h6 class="mb-3" style="font-weight: 800; color: #0ea5e9; display:flex; align-items:center;">
                <span style="background:#0ea5e9; color:white; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; font-size:12px; margin-right:8px;">2</span>
                NOMINEE / NEXT OF KIN
            </h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-c">Nominee Name</label>
                    <input type="text" name="nominee_name" class="form-control" value="${esc(c.nominee_name||'')}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-c">Relationship</label>
                    <input type="text" name="nominee_relation" class="form-control" value="${esc(c.nominee_relation||'')}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-c">Nominee CNIC</label>
                    <input type="text" name="nominee_cnic" class="form-control" value="${esc(c.nominee_cnic||'')}">
                </div>

                <div class="col-12">
                    <div class="row g-2">
                        ${renderBox(c.nominee_pic, 'prev_nom_p', 'nominee_pic', 'bi-person-bounding-box', 'Nominee Pic')}
                        ${renderBox(c.nominee_cnic_front, 'prev_nom_f', 'nominee_cnic_front', 'bi-card-heading', 'CNIC Front')}
                        ${renderBox(c.nominee_cnic_back, 'prev_nom_b', 'nominee_cnic_back', 'bi-card-list', 'CNIC Back')}
                        <div class="col-md-3">
                            <label class="form-label-c">Mailing Address</label>
                            <textarea name="nominee_address" class="form-control" style="height: 115px; font-size: 12px;">${esc(c.nominee_address||'')}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>`;
}

function esc(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/"/g,'&quot;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;');
}

// ── Image preview ─────────────────────────────────────────────
function previewStepImage(input, previewId, placeholderId) {
    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if(placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function initPreviews() {
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', function () { previewImage(this, this.dataset.preview); });
    });
}

// ── Reset edit modal on close ─────────────────────────────────
document.getElementById('editCustomerModal').addEventListener('hidden.bs.modal', function () {
    const body = document.getElementById('editModalBody');
    body.innerHTML = `<div style="text-align:center;padding:40px;color:#64748b;">
        <div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading...
    </div>`;
    document.getElementById('editSubmitBtn').disabled = true;
});

// ── Export stub ───────────────────────────────────────────────
function exportCustomers() {
    window.location.href = '{{ route("index.customer") }}?export=1';
}
</script>
@endpush
