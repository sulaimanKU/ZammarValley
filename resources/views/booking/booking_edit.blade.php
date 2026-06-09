@extends('layouts.index')

@section('title', 'Edit Booking — ' . $booking->customer_booking_id)

@push('styles')
<style>

</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <div class="booking-id"><i class="bi bi-pencil-square me-2"></i>Editing Booking</div>
                <h1>{{ $booking->customer->name ?? 'Customer' }}</h1>
                <div class="mt-2" style="color:#bfdbfe; font-size:13px;">
                    <i class="bi bi-geo-alt-fill me-1"></i>
                    Plot #{{ $booking->plot->plot_number ?? '—' }}
                    {{ $booking->plot->block ? '· Block ' . $booking->plot->block : '' }}
                    {{ $booking->plot->size ? '· ' . $booking->plot->size . ' ' . ($booking->plot->unit ?? '') : '' }}
                </div>
            </div>
            <div class="text-end">
                <div class="status-chip status-{{ $booking->status }} mb-2">
                    <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i>
                    {{ ucfirst($booking->status) }}
                </div>
                <div style="color:#93c5fd; font-size:12px;">{{ $booking->customer_booking_id }}</div>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Editable fields notice --}}
    <div class="alert d-flex align-items-start gap-3 mb-4"
         style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:14px 18px;font-size:13px;">
        <i class="bi bi-info-circle-fill mt-1" style="color:#2563eb;flex-shrink:0;font-size:16px;"></i>
        <div>
            <strong style="color:#1e40af;">What you can edit here:</strong>
            <span style="color:#1e40af;"> Booking Reference, Booking Date, Remarks &amp; Fee Settings (Registry / Development / Security).</span>
            <br>
            <span style="color:#475569;">
                Financial figures (total price, down payment, installments) are <strong>read-only</strong> — manage payments from the
                <a href="{{ route('booking.detail.view', $booking->id) }}" style="color:#2563eb;font-weight:700;">Booking Detail</a> page.
            </span>
        </div>
    </div>

    @if($booking->status === 'active')
    <div class="edit-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div><strong>Active Booking.</strong> Only date and remarks can be updated here.</div>
    </div>
    @endif

    {{-- enctype needed for file uploads --}}
    <form action="{{ route('booking.update', $booking->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">

                {{-- CARD 1: CUSTOMER (read-only) --}}
                @php $cust = $booking->customer; @endphp
                <div class="edit-card">
                    <div class="edit-card-header">
                        <div class="section-icon icon-customer"><i class="bi bi-person-fill"></i></div>
                        <div>
                            <h5>Customer Information</h5>
                            <p>Read-only — update via <a href="{{ route('customers.edit', $cust->id ?? 0) }}" style="color:#2563eb;">Customers section</a></p>
                        </div>
                        @if($cust && $cust->customer_pic)
                        <img src="{{ asset($cust->customer_pic) }}"
                             style="width:52px;height:52px;border-radius:10px;object-fit:cover;border:2px solid #e2e8f0;margin-left:auto;">
                        @endif
                    </div>
                    <div class="edit-card-body">
                        <div class="customer-lock-notice">
                            <i class="bi bi-lock-fill"></i>
                            <span>Customer details are read-only here. To update, go to the Customers section.</span>
                        </div>

                        {{-- Personal --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted" style="font-size:11px;">Full Name</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->name ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted" style="font-size:11px;">Father / Husband Name</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->guardian_name ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted" style="font-size:11px;">CNIC</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->cnic ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Mobile</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->mobile ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Phone (Res.)</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->phone_res ?? $cust->phone ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Occupation</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->occupation ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">City</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->city ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size:11px;">Postal Address</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->postal_address ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size:11px;">Residential Address</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->residential_address ?? '—' }}" readonly>
                            </div>
                        </div>

                        {{-- Nominee (if exists) --}}
                        @if($cust && $cust->nominee_name)
                        <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--muted-text,#94a3b8);margin:10px 0 8px;border-top:1px solid #e2e8f0;padding-top:10px;">
                            Nominee Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Nominee Name</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->nominee_name }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Relation</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->nominee_relation ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Nominee CNIC</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->nominee_cnic ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Nominee Address</label>
                                <input type="text" class="form-control bg-light" value="{{ $cust->nominee_address ?? '—' }}" readonly>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                {{-- CARD 2: PLOT (read-only) --}}
                <div class="edit-card">
                    <div class="edit-card-header">
                        <div class="section-icon icon-plot"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <h5>Plot Details</h5>
                            <p>Plot cannot be changed after booking — transfer module handles this</p>
                        </div>
                    </div>
                    <div class="edit-card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Plot No.</label>
                                <input type="text" class="form-control" value="#{{ $booking->plot->plot_number ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Block</label>
                                <input type="text" class="form-control" value="{{ $booking->plot->block ?? '—' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Size</label>
                                <input type="text" class="form-control" value="{{ $booking->plot->size ?? '—' }} {{ $booking->plot->unit ?? '' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control" value="{{ $booking->plot->category->name ?? '—' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 3: FINANCIALS (read-only display) --}}
                @php $hasPaidPayments = $booking->payments->where('status','paid')->count() > 0; @endphp
                {{-- Pass financial values as hidden so the controller receives them unchanged --}}
                <input type="hidden" name="total_price"         value="{{ $booking->total_price }}">
                <input type="hidden" name="down_payment"        value="{{ $booking->down_payment }}">
                <input type="hidden" name="total_installments"  value="{{ $booking->total_installments ?? 0 }}">
                <input type="hidden" name="monthly_installment" value="{{ $booking->monthly_installment ?? 0 }}">
                <input type="hidden" name="plan_type"           value="{{ ($booking->total_installments ?? 0) > 0 ? 'installment' : 'full' }}">

                <div class="edit-card" style="border-left:4px solid #64748b;">
                    <div class="edit-card-header" style="background:#f8fafc;">
                        <div class="section-icon" style="background:#e2e8f0;color:#64748b;"><i class="bi bi-cash-stack"></i></div>
                        <div>
                            <h5 style="color:#475569;">Financial Summary <span style="font-size:11px;font-weight:600;color:#94a3b8;">(Read-only)</span></h5>
                            <p>Manage payments from the Booking Detail page</p>
                        </div>
                        <a href="{{ route('booking.detail.view', $booking->id) }}"
                           class="ms-auto btn btn-sm btn-outline-secondary" style="font-size:11px;white-space:nowrap;">
                            <i class="bi bi-arrow-right me-1"></i>Go to Payments
                        </a>
                    </div>
                    <div class="edit-card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Total Plot Price</label>
                                <div class="form-control bg-light" style="font-weight:700;color:#1e3a8a;">
                                    PKR {{ number_format($booking->total_price) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Down Payment</label>
                                <div class="form-control bg-light" style="font-weight:700;color:#15803d;">
                                    PKR {{ number_format($booking->down_payment ?? 0) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Remaining Balance</label>
                                <div class="form-control bg-light" style="font-weight:700;color:#d97706;">
                                    PKR {{ number_format(max(0, $booking->total_price - ($booking->down_payment ?? 0))) }}
                                </div>
                            </div>
                            @if(($booking->total_installments ?? 0) > 0)
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">
                                    Monthly × {{ $booking->total_installments }}
                                </label>
                                <div class="form-control bg-light" style="font-weight:700;color:#7c3aed;">
                                    PKR {{ number_format($booking->monthly_installment ?? 0) }}
                                </div>
                            </div>
                            @else
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size:11px;">Plan Type</label>
                                <div class="form-control bg-light" style="font-weight:700;">Full / Lump Sum</div>
                            </div>
                            @endif
                        </div>

                        @if($hasPaidPayments)
                        <div class="mt-3 d-flex align-items-center gap-2" style="font-size:12px;color:#15803d;">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ $booking->payments->where('status','paid')->count() }} payment(s) recorded —
                            PKR {{ number_format($booking->payments->where('status','paid')->sum('amount_paid')) }} received so far.</span>
                        </div>
                        @endif
                    </div>
                </div>


                {{-- CARD 4: FEE SETTINGS --}}
                @php
                    $feeMap = [
                        'registry'    => ['label' => 'Registry Fee',    'flag' => 'has_registry_fee',    'icon' => '📋', 'color' => '#1d4ed8', 'bg' => '#eff6ff', 'border' => '#bfdbfe', 'hasAmount' => true],
                        'development' => ['label' => 'Development Fee', 'flag' => 'has_development_fee', 'icon' => '🏗️', 'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#bbf7d0', 'hasAmount' => true],
                        'security'    => ['label' => 'Security Fee',    'flag' => 'has_security_fee',    'icon' => '🔒', 'color' => '#7c3aed', 'bg' => '#fdf4ff', 'border' => '#e9d5ff', 'hasAmount' => false],
                    ];
                @endphp
                <div class="edit-card" style="border-left:4px solid #f59e0b;">
                    <div class="edit-card-header" style="background:#fffbeb;">
                        <div class="section-icon" style="background:#fde68a;color:#92400e;"><i class="bi bi-receipt-cutoff"></i></div>
                        <div>
                            <h5 style="color:#78350f;">Fee Settings</h5>
                            <p>Toggle fees and update amounts — cannot remove a fee that already has payments</p>
                        </div>
                    </div>
                    <div class="edit-card-body">
                        <div class="row g-3">
                        @foreach($feeMap as $type => $meta)
                        @php
                            $feeRec    = $booking->bookingFees->where('fee_type', $type)->first();
                            $isEnabled = old('has_'.$type.'_fee') !== null
                                ? (bool) old('has_'.$type.'_fee')
                                : (bool) $booking->{'has_'.$type.'_fee'};
                            $feeAmount = old($type.'_fee_amount', $feeRec?->amount ?? ($booking->plot->{$type.'_fee_amount'} ?? ''));
                            $paidAmt   = (float)($feeRec?->paid_amount ?? 0);
                            $hasPayments = $paidAmt > 0;
                            $amtFieldName = $type . '_fee_amount';
                        @endphp
                        <div class="col-12">
                            <div style="background:{{ $isEnabled ? $meta['bg'] : '#f8fafc' }};border:1.5px solid {{ $isEnabled ? $meta['border'] : '#e2e8f0' }};border-radius:10px;padding:14px 16px;transition:all .15s;" id="fee-card-{{ $type }}">
                                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                                    {{-- Toggle --}}
                                    <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:200px;">
                                        <span style="font-size:18px;">{{ $meta['icon'] }}</span>
                                        <div>
                                            <div style="font-size:13px;font-weight:800;color:{{ $isEnabled ? $meta['color'] : '#94a3b8' }};">{{ $meta['label'] }}</div>
                                            @if($type === 'security')
                                            <div style="font-size:11px;color:#94a3b8;margin-top:1px;">Monthly · PKR {{ number_format($booking->plot->security_fee_amount ?? 0) }}/month</div>
                                            @elseif($feeRec)
                                            <div style="font-size:11px;color:#94a3b8;margin-top:1px;">
                                                Billed: PKR {{ number_format($feeRec->amount) }}
                                                @if($paidAmt > 0)· <span style="color:#16a34a;font-weight:700;">PKR {{ number_format($paidAmt) }} paid</span>@endif
                                            </div>
                                            @else
                                            <div style="font-size:11px;color:#94a3b8;margin-top:1px;">Not configured yet</div>
                                            @endif
                                        </div>
                                        <div class="form-check form-switch ms-auto">
                                            <input class="form-check-input fee-toggle" type="checkbox"
                                                   name="has_{{ $type }}_fee" value="1"
                                                   id="toggle_{{ $type }}"
                                                   data-target="amt-{{ $type }}"
                                                   data-card="fee-card-{{ $type }}"
                                                   data-bg="{{ $meta['bg'] }}"
                                                   data-border="{{ $meta['border'] }}"
                                                   data-color="{{ $meta['color'] }}"
                                                   {{ $isEnabled ? 'checked' : '' }}
                                                   {{ $hasPayments ? 'disabled title="Cannot disable — payments exist"' : '' }}
                                                   style="width:36px;height:20px;cursor:pointer;">
                                        </div>
                                    </div>
                                    {{-- Amount (registry + development only) --}}
                                    @if($meta['hasAmount'])
                                    <div id="amt-{{ $type }}" style="{{ $isEnabled ? '' : 'display:none;' }}flex:1;min-width:180px;">
                                        <label style="font-size:11px;font-weight:700;color:#64748b;display:block;margin-bottom:4px;">Fee Amount (PKR)</label>
                                        <input type="number"
                                               name="{{ $amtFieldName }}"
                                               id="{{ $amtFieldName }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($amtFieldName, $feeAmount) }}"
                                               min="{{ $paidAmt > 0 ? $paidAmt : 0 }}"
                                               step="1"
                                               placeholder="e.g. 50000"
                                               {{ !$isEnabled ? 'disabled' : '' }}>
                                        @if($hasPayments)
                                        <div style="font-size:10px;color:#d97706;margin-top:3px;">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Minimum PKR {{ number_format($paidAmt) }} (already paid)
                                        </div>
                                        @endif
                                        @error($amtFieldName)
                                        <div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @elseif($type === 'security')
                                    <div id="amt-{{ $type }}" style="{{ $isEnabled ? '' : 'display:none;' }}flex:2;min-width:300px;">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label style="font-size:11px;font-weight:700;color:#64748b;display:block;margin-bottom:4px;">Applied From</label>
                                                <input type="date" name="security_fee_start_date" class="form-control form-control-sm"
                                                    value="{{ old('security_fee_start_date', $booking->security_fee_start_date ? $booking->security_fee_start_date->format('Y-m-d') : '') }}"
                                                    {{ !$isEnabled ? 'disabled' : '' }}>
                                                <div style="font-size:9px;color:#94a3b8;margin-top:2px;">Defaults to Booking Date</div>
                                            </div>
                                            <div class="col-6">
                                                <label style="font-size:11px;font-weight:700;color:#64748b;display:block;margin-bottom:4px;">Applied To</label>
                                                <input type="date" name="security_fee_end_date" class="form-control form-control-sm"
                                                    value="{{ old('security_fee_end_date', $booking->security_fee_end_date ? $booking->security_fee_end_date->format('Y-m-d') : '') }}"
                                                    {{ !$isEnabled ? 'disabled' : '' }}>
                                                <div style="font-size:9px;color:#94a3b8;margin-top:2px;">Optional</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @if($hasPayments && $type !== 'security')
                                <div style="margin-top:10px;background:#fffbeb;border:1px solid #fde68a;border-radius:7px;padding:7px 10px;font-size:11px;color:#92400e;display:flex;align-items:center;gap:6px;">
                                    <i class="bi bi-lock-fill"></i>
                                    Toggle locked — PKR {{ number_format($paidAmt) }} already collected. You can increase the amount but not disable this fee.
                                </div>
                                @elseif($hasPayments && $type === 'security')
                                <div style="margin-top:10px;background:#fffbeb;border:1px solid #fde68a;border-radius:7px;padding:7px 10px;font-size:11px;color:#92400e;display:flex;align-items:center;gap:6px;">
                                    <i class="bi bi-lock-fill"></i>
                                    Toggle locked — PKR {{ number_format($paidAmt) }} in security payments recorded.
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        </div>
                    </div>
                </div>

            </div>{{-- end col-lg-8 --}}

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-4">

                {{-- CARD 5: BOOKING META --}}
                <div class="edit-card">
                    <div class="edit-card-header">
                        <div class="section-icon icon-meta"><i class="bi bi-calendar3"></i></div>
                        <div>
                            <h5>Booking Info</h5>
                            <p>Reference, date and type</p>
                        </div>
                    </div>
                    <div class="edit-card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Booking Reference No. <span class="text-danger">*</span></label>
                                <input type="text" name="customer_booking_id"
                                       class="form-control @error('customer_booking_id') is-invalid @enderror"
                                       value="{{ old('customer_booking_id', $booking->customer_booking_id) }}"
                                       placeholder="e.g. ZV-2026-001" required>
                                @error('customer_booking_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                                    <i class="bi bi-exclamation-circle me-1"></i>Must be unique — no two bookings can share the same reference.
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Booking Date <span class="text-danger">*</span></label>
                                <input type="date" name="booking_date"
                                       class="form-control @error('booking_date') is-invalid @enderror"
                                       value="{{ old('booking_date', \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d')) }}"
                                       required>
                                @error('booking_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Booking Type</label>
                                <input type="text" class="form-control bg-light" value="{{ $booking->booking_type ?? 'First Allotment' }}" readonly>
                                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                                    <i class="bi bi-info-circle me-1"></i>Use the Transfer module to change ownership.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 6: REMARKS --}}
                <div class="edit-card">
                    <div class="edit-card-header">
                        <div class="section-icon" style="background:#fee2e2;color:#dc2626;"><i class="bi bi-chat-left-text-fill"></i></div>
                        <div><h5>Remarks</h5><p>Internal notes</p></div>
                    </div>
                    <div class="edit-card-body">
                        <textarea name="remarks" class="form-control" rows="4"
                                  placeholder="Any notes about this booking...">{{ old('remarks', $booking->remarks) }}</textarea>
                    </div>
                </div>

                {{-- CARD 7: PAYMENT STATUS (read-only summary) --}}
                @php
                    $totalPaidAmount   = $booking->payments->where('status','paid')->sum('amount_paid');
                    $outstandingAmount = $booking->total_price - $totalPaidAmount;
                @endphp
                <div class="edit-card">
                    <div class="edit-card-header">
                        <div class="section-icon" style="background:#dcfce7;color:#15803d;"><i class="bi bi-receipt"></i></div>
                        <div><h5>Payment Status</h5><p>Summary — manage payments from booking detail</p></div>
                    </div>
                    <div class="edit-card-body p-0">
                        <table class="table table-sm mb-0" style="font-size:12px;">
                            <tr>
                                <td class="ps-3" style="color:#64748b;border:none;padding:8px 12px;">Total Agreed</td>
                                <td class="text-end pe-3 fw-bold" style="border:none;padding:8px 12px;">PKR {{ number_format($booking->total_price) }}</td>
                            </tr>
                            <tr style="background:#f8fafc;">
                                <td class="ps-3" style="color:#15803d;border:none;padding:8px 12px;">Total Paid</td>
                                <td class="text-end pe-3 fw-bold" style="color:#15803d;border:none;padding:8px 12px;">PKR {{ number_format($totalPaidAmount) }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3" style="color:#d97706;border:none;padding:8px 12px;">Outstanding</td>
                                <td class="text-end pe-3 fw-bold" style="color:#d97706;border:none;padding:8px 12px;">PKR {{ number_format(max(0, $outstandingAmount)) }}</td>
                            </tr>
                            @if($booking->total_installments > 0)
                            <tr style="background:#f8fafc;">
                                <td class="ps-3" style="color:#7c3aed;border:none;padding:8px 12px;">Installments Paid</td>
                                <td class="text-end pe-3 fw-bold" style="color:#7c3aed;border:none;padding:8px 12px;">
                                    {{ $booking->payments->where('payment_category','installment')->where('status','paid')->count() }}
                                    / {{ $booking->total_installments }}
                                </td>
                            </tr>
                            @endif
                        </table>
                        <div class="px-3 py-2" style="background:#f0f9ff; border-top:1px solid #e0f2fe; font-size:11px; color:#0369a1;">
                            <i class="bi bi-info-circle me-1"></i>
                            Payment records are managed from the
                            <a href="{{ route('booking.detail.view', $booking->id) }}" style="color:#0369a1;font-weight:600;">Booking Detail</a> page — add, edit, or void payments there.
                        </div>
                    </div>
                </div>

            </div>
        </div>{{-- end row --}}

        {{-- SUBMIT BAR --}}
        <div class="submit-bar mt-2">
            <div style="font-size:12px; color:#94a3b8;">
                <i class="bi bi-pencil me-1 text-primary"></i>
                Saves <strong style="color:#64748b;">Booking Date</strong>, <strong style="color:#64748b;">Remarks</strong>, and <strong style="color:#64748b;">Fee Settings</strong>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url()->previous() }}" class="btn-cancel-edit">
                    <i class="bi bi-x-lg"></i> Cancel
                </a>
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-lg"></i> Save Changes
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.fee-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        var card      = document.getElementById(this.dataset.card);
        var amtWrap   = this.dataset.target ? document.getElementById(this.dataset.target) : null;
        var allInputs = amtWrap ? amtWrap.querySelectorAll('input') : [];
        var enabled   = this.checked;

        if (card) {
            card.style.background  = enabled ? this.dataset.bg     : '#f8fafc';
            card.style.borderColor = enabled ? this.dataset.border  : '#e2e8f0';
            var label = card.querySelector('[style*="font-weight:800"]');
            if (label) label.style.color = enabled ? this.dataset.color : '#94a3b8';
        }
        if (amtWrap)  amtWrap.style.display  = enabled ? '' : 'none';
        allInputs.forEach(function(inp) {
            inp.disabled = !enabled;
        });
    });
});
</script>
@endpush
