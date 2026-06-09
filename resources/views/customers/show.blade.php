@extends('layouts.index')

@section('content')
@push('styles')

<style>

</style>
@endpush

<div class="wrap">

{{-- ── Page Header ─────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <p style="font-size:1.05rem;font-weight:800;color:#fff;margin:0;position:relative;z-index:1;">
            <i class="bi bi-person-circle" style="margin-right:8px;opacity:.7;"></i>
            Client Profile
        </p>
        <p style="font-size:11px;color:rgba(255,255,255,.5);margin:4px 0 0;position:relative;z-index:1;">
            {{ $customer->name }} &nbsp;·&nbsp; {{ $customer->cnic }}
        </p>
    </div>
    <div style="display:flex;gap:10px;position:relative;z-index:1;flex-wrap:wrap;">
        <a href="{{ route('index.customer') }}" class="btn-soft" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.15);color:rgba(255,255,255,.8);">
            <i class="bi bi-arrow-left"></i> Back
        </a>

        {{-- Statement PDF --}}
        <a href="{{ route('customer.statement', $customer->id) }}" target="_blank"
           class="btn-soft" style="background:rgba(59,130,246,.25);border-color:rgba(147,197,253,.4);color:#bfdbfe;">
            <i class="bi bi-file-earmark-pdf"></i> Statement PDF
        </a>

        {{-- Send by Email --}}
        @if($customer->email)
        <form method="POST" action="{{ route('customer.statement.email', $customer->id) }}" style="display:inline;"
              onsubmit="return confirm('Send statement PDF to {{ addslashes($customer->email) }}?')">
            @csrf
            <button type="submit" class="btn-soft"
                    style="background:rgba(34,197,94,.2);border-color:rgba(134,239,172,.4);color:#86efac;cursor:pointer;">
                <i class="bi bi-envelope"></i> Email Statement
            </button>
        </form>
        @else
        <span class="btn-soft" title="No email on file"
              style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1);color:rgba(255,255,255,.3);cursor:not-allowed;">
            <i class="bi bi-envelope"></i> Email Statement
        </span>
        @endif

    </div>
</div>

<div class="row g-4">
    {{-- LEFT col --}}
    <div class="col-lg-4">

        {{-- Profile Card --}}
        <div class="card">
            <div class="card-head"><p class="card-title">Profile</p>
                <span class="{{ $customer->status === 'active' ? 'pill pill-green' : 'pill pill-red' }}">
                    <span class="pill-dot"></span> {{ ucfirst($customer->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="profile-box" style="margin-bottom:18px;">
                    @if($customer->customer_pic)
                        <img src="{{ asset( $customer->customer_pic) }}" class="profile-photo" alt="{{ $customer->name }}">
                    @else
                        <div class="profile-placeholder">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                    @endif
                    <div>
                        <div class="profile-name">{{ $customer->name }}</div>
                        @if($customer->guardian_name)
                        <div class="profile-sub">S/O {{ $customer->guardian_name }}</div>
                        @endif
                        <div class="profile-sub" style="margin-top:6px;">
                            Member since {{ \Carbon\Carbon::parse($customer->created_at)->format('M Y') }}
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <span class="info-label">CNIC</span>
                    <span class="info-val" style="font-family:monospace;">{{ $customer->cnic }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-val">{{ $customer->phone ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-val">{{ $customer->email ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">City</span>
                    <span class="info-val">{{ $customer->city ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address</span>
                    <span class="info-val" style="font-size:12px;">{{ $customer->address ?? '—' }}</span>
                </div>

                {{-- CNIC Document --}}
                @if($customer->cnic_pic)
                <div style="margin-top:16px;">
                    <div style="font-size:10px;font-weight:700;color:var(--slate);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">CNIC Document</div>
                    <a href="{{ asset( $customer->cnic_pic) }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:7px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:8px 14px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;">
                        <i class="bi bi-file-earmark-image"></i> View CNIC Document
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="card">
            <div class="card-head"><p class="card-title">Financial Summary</p></div>
            @php
                $fmt = function($n) {
                    $n = (float)$n;
                    if ($n >= 10000000) return 'PKR ' . number_format($n / 10000000, 2) . ' Cr';
                    if ($n >= 100000)   return 'PKR ' . number_format($n / 100000, 1) . ' Lac';
                    return 'PKR ' . number_format($n);
                };
            @endphp
            <div class="card-body">
                <div class="fin-grid">
                    <div class="fin-tile">
                        <div class="fin-label">Total Value</div>
                        <div class="fin-val" style="color:var(--navy);" title="PKR {{ number_format($totalValue) }}">{{ $fmt($totalValue) }}</div>
                    </div>
                    <div class="fin-tile">
                        <div class="fin-label">Total Paid</div>
                        <div class="fin-val" style="color:var(--green);" title="PKR {{ number_format($totalPaid) }}">{{ $fmt($totalPaid) }}</div>
                    </div>
                    <div class="fin-tile">
                        <div class="fin-label">Remaining</div>
                        <div class="fin-val" style="color:{{ $totalOutstanding > 0 ? '#dc2626' : '#16a34a' }};" title="PKR {{ number_format($totalOutstanding) }}">
                            {{ $fmt($totalOutstanding) }}
                        </div>
                    </div>
                    @if($totalDiscounts > 0)
                    <div class="fin-tile" style="border-top:2px solid #fde68a;">
                        <div class="fin-label" style="color:#92400e;">Total Discounts</div>
                        <div class="fin-val" style="color:#d97706;" title="PKR {{ number_format($totalDiscounts) }}">{{ $fmt($totalDiscounts) }}</div>
                    </div>
                    @endif
                </div>
                @if($totalValue > 0)
                @php $pct = min(100, round($totalPaid / $totalValue * 100)); @endphp
                <div style="margin-top:14px;">
                    <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--slate);margin-bottom:5px;">
                        <span>Payment Progress</span><span style="font-weight:700;">{{ $pct }}%</span>
                    </div>
                    <div style="height:7px;background:#e2e8f0;border-radius:10px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#1e3a8a,#3b82f6);border-radius:10px;"></div>
                    </div>
                </div>
                @endif
                @php
                    $hasTransferredOut = $bookingDetails->contains('is_transferred_out', true);
                @endphp
                @if($hasTransferredOut)
                <div style="margin-top:12px;padding:7px 10px;background:#f5f3ff;border:1px solid #e9d5ff;border-radius:8px;font-size:10px;color:#7c3aed;display:flex;align-items:flex-start;gap:6px;">
                    <span style="font-size:12px;line-height:1.2;">ℹ</span>
                    <span>Figures reflect <strong>current active plots only</strong>. Transferred-out bookings are shown in the table for history but are not counted in totals above.</span>
                </div>
                @endif
            </div>
        </div>

    </div>{{-- /col-4 --}}

    {{-- RIGHT col — Bookings --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-head">
                <div>
                    <p class="card-title">Booking Portfolio</p>
                    <p style="font-size:11px;color:var(--slate);margin:2px 0 0;">
                        {{ $customer->booking->count() }} booking{{ $customer->booking->count() !== 1 ? 's' : '' }}
                    </p>
                </div>
            </div>
            <div style="overflow-x:auto;">
               <table class="bk-table">
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Plot</th>
            <th>Base Price</th>
            <th>Discount</th>
            <th>Agreed Price</th>
            <th>Paid</th>
            <th>Remaining</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bookingDetails as $b)
            {{-- Row styling: transferred-out = muted purple stripe; transfer-in = blue tint --}}
            @php
                $rowStyle = '';
                if ($b['is_transferred_out'])      $rowStyle = 'background:#faf5ff;border-left:3px solid #a855f7;opacity:.82;';
                elseif ($b['is_transfer_in'])       $rowStyle = 'background:#eff6ff;border-left:3px solid #3b82f6;';
            @endphp
            <tr style="{{ $rowStyle }}">
                <td>
                    <strong style="font-size:11px;color:var(--blue);font-family:monospace;">
                        {{ $b['booking']->customer_booking_id }}
                    </strong>
                    @if($b['is_transfer_in'])
                        <div style="margin-top:3px;">
                            <span style="background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd;font-size:9px;font-weight:800;padding:2px 7px;border-radius:10px;display:inline-flex;align-items:center;gap:3px;">
                                <span>↓</span> Transfer In
                            </span>
                        </div>
                    @elseif($b['is_transferred_out'])
                        <div style="margin-top:3px;">
                            <span style="background:#f3e8ff;color:#7c3aed;border:1px solid #d8b4fe;font-size:9px;font-weight:800;padding:2px 7px;border-radius:10px;display:inline-flex;align-items:center;gap:3px;">
                                <span>↑</span> Transferred Out
                            </span>
                        </div>
                        <div style="margin-top:2px;font-size:9px;color:#9ca3af;font-style:italic;">not counted in totals</div>
                    @endif
                </td>
                <td>
                    <strong @if($b['is_transferred_out']) style="text-decoration:line-through;color:#9ca3af;" @endif>
                        #{{ $b['booking']->plot->plot_number ?? '—' }}
                    </strong>
                    @if($b['booking']->plot->block ?? null)
                        <span style="color:var(--slate);font-size:11px;">· {{ $b['booking']->plot->block }}</span>
                    @endif
                    <div style="font-size:10px;color:var(--slate);">
                        {{ $b['booking']->plot->size ?? '' }} {{ $b['booking']->plot->unit ?? '' }}
                    </div>
                </td>
                {{-- Base Price --}}
                <td style="{{ $b['is_transferred_out'] ? 'color:#9ca3af;' : 'color:#475569;' }}">
                    @if($b['base_price'] > 0)
                        PKR {{ number_format($b['base_price']) }}
                    @else
                        <span style="color:#9ca3af;">—</span>
                    @endif
                </td>

                {{-- Discount --}}
                <td>
                    @if($b['total_discount'] > 0)
                        <span style="color:#d97706;font-weight:700;">
                            − PKR {{ number_format($b['total_discount']) }}
                        </span>
                        @if($b['plot_discount'] > 0 && $b['pay_discount'] > 0)
                        <div style="font-size:9px;color:#92400e;margin-top:2px;">
                            Plot: {{ number_format($b['plot_discount']) }}<br>
                            Settlement: {{ number_format($b['pay_discount']) }}
                        </div>
                        @elseif($b['pay_discount'] > 0)
                        <div style="font-size:9px;color:#92400e;margin-top:2px;">Settlement discount</div>
                        @endif
                    @else
                        <span style="color:#9ca3af;font-size:11px;">—</span>
                    @endif
                </td>

                {{-- Agreed Price --}}
                <td style="font-weight:700;{{ $b['is_transferred_out'] ? 'color:#9ca3af;text-decoration:line-through;' : '' }}">
                    PKR {{ number_format($b['plot_price']) }}
                </td>

                <td style="{{ $b['is_transferred_out'] ? 'color:#9ca3af;text-decoration:line-through;' : 'color:var(--green);font-weight:700;' }}">
                    PKR {{ number_format($b['paid']) }}
                    @if($b['is_transferred_out'])
                        <div style="font-size:9px;color:#9ca3af;text-decoration:none;font-style:italic;">paid before transfer</div>
                    @endif
                </td>
                <td style="color:{{ $b['remaining'] > 0 ? '#dc2626' : '#16a34a' }};font-weight:700;">
                    @if($b['is_transferred_out'])
                        <span style="color:#7c3aed;font-size:11px;">— Transferred</span>
                    @else
                        PKR {{ number_format($b['remaining']) }}
                        @if($b['remaining'] == 0) <span style="font-size:10px;">✓</span> @endif
                    @endif
                </td>
                <td>
                    <span class="pill {{ $b['pill'] }}">
                        <span class="pill-dot"></span>
                        {{ ucfirst(str_replace('_', ' ', $b['booking']->status)) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('ledger.view', $b['booking']->id) }}"
                       style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:4px 10px;border-radius:7px;font-size:10px;font-weight:700;text-decoration:none;">
                        Ledger
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:40px;color:var(--slate);">
                    <i class="bi bi-clipboard-x" style="font-size:2rem;opacity:.3;display:block;margin-bottom:10px;"></i>
                    No bookings yet for this customer.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

            </div>

            {{-- Legend --}}
            <div style="padding:10px 16px 14px;display:flex;gap:18px;flex-wrap:wrap;border-top:1px solid #f1f5f9;">
                <div style="display:flex;align-items:center;gap:6px;font-size:10px;color:#64748b;">
                    <div style="width:10px;height:10px;border-radius:2px;background:#eff6ff;border:1px solid #3b82f6;flex-shrink:0;"></div>
                    Transfer In — plot received via ownership transfer
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:10px;color:#64748b;">
                    <div style="width:10px;height:10px;border-radius:2px;background:#faf5ff;border:1px solid #a855f7;flex-shrink:0;"></div>
                    Transferred Out — plot transferred to another owner; excluded from totals
                </div>
            </div>
        </div>
    </div>{{-- /col-8 --}}
</div>{{-- /row --}}
</div>{{-- /wrap --}}

@push('scripts')
<script>


/**
 * Opens the Edit Modal and fetches Customer data via AJAX
 * @param {number} id - The Customer ID
 */
function openEditFromShow(id) {
    const modalElement = document.getElementById('editCustomerModal');
    const modal = new bootstrap.Modal(modalElement);
    const body  = document.getElementById('editModalBody');
    const form  = document.getElementById('editForm');

    // Set the form action URL
    form.action = `/customers/${id}`;

    // Show Loading Spinner
    body.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <div class="text-muted small">Fetching client data...</div>
        </div>
    `;

    modal.show();

    // Fetch Data
    fetch(`/customers/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(customer => {
        body.innerHTML = buildEditForm(customer);
    })
    .catch(error => {
        body.innerHTML = `<div class="alert alert-danger m-3">Error loading data: ${error.message}</div>`;
    });
}

/**
 * Escapes HTML to prevent XSS
 */
function esc(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

/**
 * Generates the HTML for the Edit Form
 */
function buildEditForm(c, id) {
    const customerPic = c.customer_pic
        ? `<img src="/${c.customer_pic}" class="upload-preview" id="editPicPreview">`
        : `<img src="" class="upload-preview" id="editPicPreview" style="display:none;">`;

    const nomineePic = c.nominee_pic
        ? `<img src="/${c.nominee_pic}" class="upload-preview" id="editNomineePicPreview">`
        : `<img src="" class="upload-preview" id="editNomineePicPreview" style="display:none;">`;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    return `
    <form id="editCustomerForm"
          action="/customers/${id}"
          method="POST"
          enctype="multipart/form-data">

        <input type="hidden" name="_token" value="${csrf}">
        <input type="hidden" name="_method" value="PUT">

        <div class="row g-3">

            <!-- Basic Info -->
            <div class="col-md-6">
                <label class="form-label-c">Full Name *</label>
                <input type="text" name="name" class="form-control" value="${esc(c.name)}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Guardian / Father Name</label>
                <input type="text" name="guardian_name" class="form-control" value="${esc(c.guardian_name||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">CNIC *</label>
                <input type="text" name="cnic" class="form-control" value="${esc(c.cnic)}" placeholder="XXXXX-XXXXXXX-X" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Phone *</label>
                <input type="text" name="phone" class="form-control" value="${esc(c.phone)}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Mobile</label>
                <input type="text" name="mobile" class="form-control" value="${esc(c.mobile||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Phone (Office)</label>
                <input type="text" name="phone_off" class="form-control" value="${esc(c.phone_off||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Phone (Residence)</label>
                <input type="text" name="phone_res" class="form-control" value="${esc(c.phone_res||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Email</label>
                <input type="email" name="email" class="form-control" value="${esc(c.email||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">City</label>
                <input type="text" name="city" class="form-control" value="${esc(c.city||'')}">
            </div>

            <!-- Addresses & Occupation -->
            <div class="col-12">
                <label class="form-label-c">Residential Address</label>
                <textarea name="residential_address" class="form-control" rows="2">${esc(c.residential_address||'')}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label-c">Postal Address</label>
                <textarea name="postal_address" class="form-control" rows="2">${esc(c.postal_address||'')}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Occupation</label>
                <input type="text" name="occupation" class="form-control" value="${esc(c.occupation||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Age</label>
                <input type="number" name="age" class="form-control" value="${esc(c.age||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Nationality</label>
                <input type="text" name="nationality" class="form-control" value="${esc(c.nationality||'')}">
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label class="form-label-c">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="active" ${c.status==='active'?'selected':''}>Active</option>
                    <option value="inactive" ${c.status==='inactive'?'selected':''}>Inactive</option>
                </select>
            </div>

            <!-- Profile & CNIC -->
            <div class="col-md-6">
                <label class="form-label-c">Profile Photo</label>
                <div class="upload-box" onclick="document.getElementById('editCustomerPic').click()">
                    ${customerPic}
                    <i class="bi bi-camera" style="font-size:1.3rem;color:#94a3b8;display:block;margin-bottom:4px;"></i>
                    <span style="font-size:11px;color:#94a3b8;">Click to change photo</span>
                    <input type="file" id="editCustomerPic" name="customer_pic" accept="image/*" style="display:none;"
                           onchange="previewImage(this,'editPicPreview')">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label-c">CNIC Document</label>
                <div class="upload-box" onclick="document.getElementById('editCnicPic').click()">
                    <i class="bi bi-file-earmark-image" style="font-size:1.3rem;color:#94a3b8;display:block;margin-bottom:4px;"></i>
                    <span style="font-size:11px;color:#94a3b8;">
                        ${c.cnic_pic ? '✓ Document uploaded — click to replace' : 'Click to upload CNIC photo/PDF'}
                    </span>
                    <input type="file" id="editCnicPic" name="cnic_pic" accept="image/*,.pdf" style="display:none;">
                </div>
            </div>

            <!-- Nominee Details -->
            <h6 class="mt-3">Nominee Information</h6>
            <div class="col-md-6">
                <label class="form-label-c">Nominee Name</label>
                <input type="text" name="nominee_name" class="form-control" value="${esc(c.nominee_name||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Nominee Relation</label>
                <input type="text" name="nominee_relation" class="form-control" value="${esc(c.nominee_relation||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Nominee CNIC</label>
                <input type="text" name="nominee_cnic" class="form-control" value="${esc(c.nominee_cnic||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Nominee Address</label>
                <input type="text" name="nominee_address" class="form-control" value="${esc(c.nominee_address||'')}">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Nominee Photo</label>
                <div class="upload-box" onclick="document.getElementById('editNomineePic').click()">
                    ${nomineePic}
                    <i class="bi bi-camera" style="font-size:1.3rem;color:#94a3b8;display:block;margin-bottom:4px;"></i>
                    <span style="font-size:11px;color:#94a3b8;">Click to change photo</span>
                    <input type="file" id="editNomineePic" name="nominee_pic" accept="image/*" style="display:none;"
                           onchange="previewImage(this,'editNomineePicPreview')">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Nominee CNIC Front</label>
                <input type="file" name="nominee_cnic_front" accept="image/*,.pdf">
            </div>
            <div class="col-md-6">
                <label class="form-label-c">Nominee CNIC Back</label>
                <input type="file" name="nominee_cnic_back" accept="image/*,.pdf">
            </div>

        </div>
    </form>`;
}

/**
 * Handles Live Image Preview
 */
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.add('shadow-sm');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

</script>
@endpush
@endsection
