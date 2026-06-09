@extends('layouts.index')
@push('styles')
<style>
.det-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;overflow:hidden;margin-bottom:18px;}
.det-head{padding:13px 20px;display:flex;align-items:center;gap:10px;border-bottom:1px solid var(--border-color);}
.det-head-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.det-head h6{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;margin:0;}
.det-body{padding:22px;}
.det-sub{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--muted-text);padding:8px 0 12px;display:flex;align-items:center;gap:6px;border-bottom:1px solid var(--border-color);margin-bottom:14px;}
.det-row{display:flex;flex-direction:column;gap:2px;margin-bottom:14px;}
.det-lbl{font-size:10px;font-weight:700;color:var(--muted-text);text-transform:uppercase;letter-spacing:.5px;}
.det-val{font-size:13px;font-weight:600;color:var(--text-main);}
.det-val-muted{font-size:13px;color:var(--sub-text);}

/* plot info bar */
.bk-info-bar{display:flex;flex-wrap:wrap;gap:0;background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:14px;padding:20px 24px;margin-bottom:18px;align-items:center;}
.bib-item{text-align:center;padding:0 18px;}
.bib-item:first-child{padding-left:0;}
.bib-lbl{font-size:9px;font-weight:800;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.5px;display:block;}
.bib-val{font-size:14px;font-weight:800;color:#fff;display:block;margin-top:3px;}
.bib-sep{width:1px;background:rgba(255,255,255,.15);align-self:stretch;margin:0;}

/* status badge */
.bk-status-badge{padding:6px 16px;border-radius:20px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;}
.bks-pending{background:#fef9c3;color:#854d0e;}
.bks-active{background:#dcfce7;color:#15803d;}
.bks-on_hold{background:#fff7ed;color:#ea580c;}
.bks-completed{background:#eff6ff;color:#1d4ed8;}
.bks-cancelled{background:#fee2e2;color:#dc2626;}
.bks-transferred{background:#fdf4ff;color:#7c3aed;}
.bks-pending_transfer{background:#fef9c3;color:#854d0e;}
.bks-partial_transferred{background:#fff7ed;color:#ea580c;}
.bks-swapped{background:#f0fdf4;color:#0f766e;}
.bks-plot_relocated{background:#eff6ff;color:#0369a1;}

/* financial summary box */
.fin-summary{background:var(--hover-bg);border:1px solid var(--border-color);border-radius:10px;padding:16px;}
.fin-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border-color);}
.fin-row:last-child{border-bottom:none;}
.fin-lbl{font-size:12px;color:var(--sub-text);}
.fin-val{font-size:13px;font-weight:700;color:var(--text-main);}
.fin-row-total{background:var(--card-bg);border-radius:8px;padding:10px 12px;margin-top:8px;display:flex;justify-content:space-between;}
.fin-total-lbl{font-size:12px;font-weight:800;color:var(--text-main);}
.fin-total-val{font-size:15px;font-weight:800;}

/* fee flags */
.fee-flag{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:8px;font-size:11px;font-weight:700;}
.fee-yes{background:#dcfce7;color:#15803d;}
.fee-no{background:#f1f5f9;color:#94a3b8;}

/* plan change history */
.plan-hist-table{width:100%;border-collapse:collapse;}
.plan-hist-table th{font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:var(--muted-text);font-weight:700;padding:7px 12px;background:var(--table-head-bg);border-bottom:2px solid var(--border-color);}
.plan-hist-table td{padding:9px 12px;border-bottom:1px solid var(--table-border);font-size:12px;vertical-align:middle;}
.plan-hist-table tr:last-child td{border-bottom:none;}
.change-arrow{color:#94a3b8;margin:0 4px;}

/* photo frame */
.photo-frame{width:80px;height:80px;border-radius:10px;object-fit:cover;border:2px solid var(--border-color);}
.photo-placeholder{width:80px;height:80px;border-radius:10px;background:var(--hover-bg);border:2px solid var(--border-color);display:flex;align-items:center;justify-content:center;color:var(--muted-text);font-size:22px;}
.doc-thumb{height:70px;border-radius:8px;object-fit:cover;border:1px solid var(--border-color);}

/* payment table */
.pay-table{width:100%;border-collapse:collapse;}
.pay-table th{font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:var(--muted-text);font-weight:700;padding:8px 12px;background:var(--table-head-bg);border-bottom:2px solid var(--border-color);}
.pay-table td{padding:10px 12px;border-bottom:1px solid var(--table-border);font-size:12px;vertical-align:middle;}
.pay-table tr:last-child td{border-bottom:none;}
</style>
@endpush

@section('content')
<div class="ldg-wrap">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="alert-flash alert-flash-success mb-4" style="display:flex;align-items:center;gap:10px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-flash alert-flash-danger mb-4" style="display:flex;align-items:center;gap:10px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif
    @if(session('info'))
    <div class="alert-flash mb-4" style="display:flex;align-items:center;gap:10px;background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;border-radius:10px;padding:12px 16px;font-size:13px;font-weight:600;">
        <i class="fas fa-info-circle"></i> {{ session('info') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="rpt-header no-print">
        <div>
            <p class="rpt-header-title">Booking Detail</p>
            <p class="rpt-header-sub">Ref: {{ $detail->customer_booking_id }} · {{ \Carbon\Carbon::parse($detail->booking_date)->format('d M Y') }}</p>
        </div>
        <div class="rpt-header-actions">
            <a href="{{ route('index.booking') }}" class="btn-soft-header">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('downloadPDF', Hashids::encode($detail->id)) }}" target="_blank" class="btn-soft-header">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <button onclick="window.print()" class="btn-soft-header">
                <i class="fas fa-print"></i> Print
            </button>
            @if(in_array($detail->status, ['pending','active']))
            <a href="{{ route('booking.edit', $detail->id) }}" class="btn-navy">
                <i class="fas fa-pen"></i> Edit
            </a>
            @endif
        </div>
    </div>

    {{-- Booking info bar --}}
    <div class="bk-info-bar">
        <div class="bib-item">
            <span class="bib-lbl">Booking Ref</span>
            <span class="bib-val">{{ $detail->customer_booking_id }}</span>
        </div>
        <div class="bib-sep"></div>
        <div class="bib-item">
            <span class="bib-lbl">Plot No.</span>
            <span class="bib-val">#{{ $detail->plot->plot_number ?? '—' }}</span>
        </div>
        <div class="bib-sep"></div>
        <div class="bib-item">
            <span class="bib-lbl">Block</span>
            <span class="bib-val">{{ $detail->plot->block ?? '—' }}</span>
        </div>
        <div class="bib-sep"></div>
        <div class="bib-item">
            <span class="bib-lbl">Customer</span>
            <span class="bib-val">{{ $detail->customer->name ?? '—' }}</span>
        </div>
        <div class="bib-sep"></div>
        <div class="bib-item">
            <span class="bib-lbl">Total Price</span>
            <span class="bib-val">PKR {{ number_format($detail->total_price) }}</span>
        </div>
        <div class="bib-sep"></div>
        <div class="bib-item">
            <span class="bib-lbl">Booking Date</span>
            <span class="bib-val">{{ \Carbon\Carbon::parse($detail->booking_date)->format('d M Y') }}</span>
        </div>
        <div style="margin-left:auto;padding-left:18px;">
            <span class="bk-status-badge bks-{{ $detail->status }}">
                {{ ucfirst(str_replace('_',' ',$detail->status)) }}
            </span>
        </div>
    </div>

    <div class="row g-4">

        {{-- ══ LEFT COLUMN ══ --}}
        <div class="col-lg-8">

            {{-- I. Customer --}}
            <div class="det-card">
                <div class="det-head" style="background:linear-gradient(135deg,#065f46,#059669);">
                    <div class="det-head-icon" style="background:rgba(255,255,255,.15);color:#fff;"><i class="fas fa-user"></i></div>
                    <h6 style="color:#fff;">I. Applicant Information</h6>
                </div>
                <div class="det-body">

                    {{-- Photo + basic --}}
                    <div style="display:flex;gap:16px;margin-bottom:18px;align-items:flex-start;">
                        @if($detail->customer->customer_pic)
                            <img src="{{ asset($detail->customer->customer_pic) }}" class="photo-frame">
                        @else
                            <div class="photo-placeholder"><i class="fas fa-user"></i></div>
                        @endif
                        <div style="flex:1;">
                            <div style="font-size:16px;font-weight:800;color:var(--text-main);">{{ $detail->customer->name ?? '—' }}</div>
                            <div style="font-size:12px;color:var(--muted-text);">S/O or W/O {{ $detail->customer->guardian_name ?? '—' }}</div>
                            <div style="font-size:12px;color:var(--muted-text);margin-top:3px;">CNIC: {{ $detail->customer->cnic ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="det-sub"><i class="fas fa-id-card"></i> Personal</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Age</span><span class="det-val">{{ $detail->customer->age ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Nationality</span><span class="det-val">{{ $detail->customer->nationality ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Occupation</span><span class="det-val">{{ $detail->customer->occupation ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">City</span><span class="det-val">{{ $detail->customer->city ?? '—' }}</span></div></div>
                    </div>

                    <div class="det-sub"><i class="fas fa-phone"></i> Contact</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Mobile</span><span class="det-val">{{ $detail->customer->mobile ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Phone (Res.)</span><span class="det-val">{{ $detail->customer->phone_res ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Phone (Off.)</span><span class="det-val">{{ $detail->customer->phone_off ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Email</span><span class="det-val">{{ $detail->customer->email ?? '—' }}</span></div></div>
                    </div>

                    <div class="det-sub"><i class="fas fa-map-pin"></i> Address</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><div class="det-row"><span class="det-lbl">Postal Address</span><span class="det-val">{{ $detail->customer->postal_address ?? '—' }}</span></div></div>
                        <div class="col-md-6"><div class="det-row"><span class="det-lbl">Residential Address</span><span class="det-val">{{ $detail->customer->residential_address ?? '—' }}</span></div></div>
                    </div>

                    <div class="det-sub"><i class="fas fa-file-image"></i> CNIC Documents</div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        @if($detail->customer->cnic_pic)
                            <div>
                                <div style="font-size:10px;font-weight:700;color:var(--muted-text);margin-bottom:4px;">CNIC FRONT</div>
                                <img src="{{ asset($detail->customer->cnic_pic) }}" class="doc-thumb">
                            </div>
                        @endif
                        @if($detail->customer->cnic_pic_back ?? null)
                            <div>
                                <div style="font-size:10px;font-weight:700;color:var(--muted-text);margin-bottom:4px;">CNIC BACK</div>
                                <img src="{{ asset($detail->customer->cnic_pic_back) }}" class="doc-thumb">
                            </div>
                        @endif
                        @if(!$detail->customer->cnic_pic && !($detail->customer->cnic_pic_back ?? null))
                            <span style="font-size:12px;color:var(--muted-text);">No documents uploaded</span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- II. Nominee --}}
            @if($detail->customer->nominee_name)
            <div class="det-card">
                <div class="det-head" style="background:linear-gradient(135deg,#1e40af,#6366f1);">
                    <div class="det-head-icon" style="background:rgba(255,255,255,.15);color:#fff;"><i class="fas fa-user-friends"></i></div>
                    <h6 style="color:#fff;">II. Nominee Details</h6>
                </div>
                <div class="det-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Nominee Name</span><span class="det-val">{{ $detail->customer->nominee_name ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Relation</span><span class="det-val">{{ $detail->customer->nominee_relation ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Nominee CNIC</span><span class="det-val">{{ $detail->customer->nominee_cnic ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Nominee Address</span><span class="det-val">{{ $detail->customer->nominee_address ?? '—' }}</span></div></div>
                    </div>
                    {{-- Nominee docs --}}
                    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;">
                        @if($detail->customer->nominee_pic ?? null)
                            <div><div style="font-size:10px;font-weight:700;color:var(--muted-text);margin-bottom:4px;">PHOTO</div><img src="{{ asset($detail->customer->nominee_pic) }}" class="doc-thumb"></div>
                        @endif
                        @if($detail->customer->nominee_cnic_front ?? null)
                            <div><div style="font-size:10px;font-weight:700;color:var(--muted-text);margin-bottom:4px;">CNIC FRONT</div><img src="{{ asset($detail->customer->nominee_cnic_front) }}" class="doc-thumb"></div>
                        @endif
                        @if($detail->customer->nominee_cnic_back ?? null)
                            <div><div style="font-size:10px;font-weight:700;color:var(--muted-text);margin-bottom:4px;">CNIC BACK</div><img src="{{ asset($detail->customer->nominee_cnic_back) }}" class="doc-thumb"></div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- III. Plot --}}
            <div class="det-card">
                <div class="det-head" style="background:linear-gradient(135deg,#b45309,#d97706);">
                    <div class="det-head-icon" style="background:rgba(255,255,255,.15);color:#fff;"><i class="fas fa-map-marker-alt"></i></div>
                    <h6 style="color:#fff;">III. Plot Details</h6>
                </div>
                <div class="det-body">
                    <div class="row g-3">
                        <div class="col-md-2"><div class="det-row"><span class="det-lbl">Plot No.</span><span class="det-val" style="font-size:18px;font-weight:800;">#{{ $detail->plot->plot_number ?? '—' }}</span></div></div>
                        <div class="col-md-2"><div class="det-row"><span class="det-lbl">Size</span><span class="det-val">{{ $detail->plot->size ?? '—' }} {{ $detail->plot->unit ?? '' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Block</span><span class="det-val">{{ $detail->plot->block ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Street</span><span class="det-val">{{ $detail->plot->street_number ?? '—' }}</span></div></div>
                        <div class="col-md-2"><div class="det-row"><span class="det-lbl">Street Size</span><span class="det-val">{{ $detail->plot->street_size ? $detail->plot->street_size.' ft' : '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Sector</span><span class="det-val">{{ $detail->plot->sector ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Society</span><span class="det-val">{{ $detail->plot->society ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">City</span><span class="det-val">{{ $detail->plot->city ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Category</span><span class="det-val">{{ $detail->plot->category->name ?? '—' }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Price Type</span><span class="det-val">{{ ucfirst($detail->plot->price_type ?? '—') }}</span></div></div>
                        <div class="col-md-3"><div class="det-row"><span class="det-lbl">Feature</span><span class="det-val">{{ $detail->plot->property_features ?? '—' }}</span></div></div>
                    </div>
                    @if($detail->plot->plot_image)
                    <div style="margin-top:14px;">
                        <div style="font-size:10px;font-weight:700;color:var(--muted-text);margin-bottom:6px;">PLOT IMAGE</div>
                        <img src="{{ asset($detail->plot->plot_image) }}" style="max-height:120px;border-radius:8px;border:1px solid var(--border-color);">
                    </div>
                    @endif
                </div>
            </div>

            {{-- IV. Payment History --}}
            <div class="det-card">
                <div class="det-head" style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);">
                    <div class="det-head-icon" style="background:rgba(255,255,255,.15);color:#fff;"><i class="fas fa-history"></i></div>
                    <h6 style="color:#fff;">IV. Payment History</h6>
                    <span style="margin-left:auto;font-size:11px;font-weight:700;color:rgba(255,255,255,.7);">
                        {{ $detail->payments->where('status','paid')->count() }} payment(s) received
                    </span>
                </div>
                <div class="det-body" style="padding:0;">
                    @if($detail->payments->count())
                    <div style="overflow-x:auto;">
                    <table class="pay-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($detail->payments->sortByDesc('payment_date') as $i => $pay)
                        <tr>
                            <td style="color:var(--muted-text);">{{ $i + 1 }}</td>
                            <td>{{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') : '—' }}</td>
                            <td>
                                <span style="font-size:11px;font-weight:700;background:var(--hover-bg);padding:2px 8px;border-radius:6px;color:var(--sub-text);">
                                    {{ ucfirst(str_replace('_',' ',$pay->payment_category)) }}
                                </span>
                            </td>
                            <td style="font-weight:700;color:#15803d;">PKR {{ number_format($pay->amount_paid) }}</td>
                            <td style="font-size:11px;color:var(--sub-text);">{{ ucfirst(str_replace('_',' ',$pay->payment_method ?? '—')) }}</td>
                            <td style="font-size:11px;color:var(--sub-text);">{{ $pay->reference_no ?? '—' }}</td>
                            <td>
                                @if($pay->status === 'paid')
                                    <span style="font-size:10px;font-weight:700;background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:20px;">Paid</span>
                                @elseif($pay->status === 'pending')
                                    <span style="font-size:10px;font-weight:700;background:#fef9c3;color:#854d0e;padding:2px 8px;border-radius:20px;">Pending</span>
                                @else
                                    <span style="font-size:10px;font-weight:700;background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:20px;">{{ ucfirst($pay->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                    @else
                    <div style="text-align:center;padding:40px;color:var(--muted-text);">
                        <i class="fas fa-receipt" style="font-size:2rem;opacity:.2;display:block;margin-bottom:10px;"></i>
                        <p style="font-size:13px;">No payments recorded yet</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ══ RIGHT COLUMN ══ --}}
        <div class="col-lg-4">

            {{-- Financial Summary --}}
            <div class="det-card">
                <div class="det-head" style="background:var(--hover-bg);">
                    <div class="det-head-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-money-bill-wave"></i></div>
                    <h6 style="color:var(--text-main);">Financial Summary</h6>
                </div>
                <div class="det-body">
                @php
                    $plotCats  = ['down_payment','installment','monthly_installment',
                                  'quarterly_installment','plot_balance','others'];
                    $totalPaid = $detail->payments->where('status','paid')
                                    ->whereIn('payment_category', $plotCats)->sum('amount_paid');
                    $remaining = max(0, $detail->total_price - $totalPaid);
                @endphp

                    @php
                    // ── Discount calculations ──────────────────────────────
                    // 1. Plot-level discount: offered at booking time, baked into total_price
                    $plotDiscount   = (float)($detail->plot->discount_amount ?? 0);
                    $plotDiscReason = $detail->plot->discount_reason ?? null;
                    // Use the plot's actual stored base price (not a reverse-computation from total_price)
                    $rawBase        = (float)($detail->plot->custom_price ?? $detail->plot->base_price ?? 0);
                    $basePriceShown = ($plotDiscount > 0 && $rawBase > 0)
                        ? $rawBase
                        : (float)$detail->total_price;

                    // 2. Payment-level discount: lump-sum / settlement discounts
                    $discSentinel = 'Settlement discount — waived amount (not collected).';
                    $payDiscount  = $detail->payments
                        ->where('status','paid')
                        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)
                        ->sum('discount_amount')
                        + $detail->payments
                        ->where('status','paid')
                        ->filter(fn($p) => ($p->remarks ?? '') === $discSentinel)
                        ->sum('amount_paid');

                    $totalCredits = $totalPaid + $payDiscount;
                    $remainingAdj = max(0, $detail->total_price - $totalCredits);
                    $paidPctAdj   = $detail->total_price > 0
                        ? min(100, round(($totalCredits / $detail->total_price) * 100)) : 0;
                @endphp

                    <div class="fin-summary">
                        {{-- Base price row (only if plot discount exists) --}}
                        @if($plotDiscount > 0)
                        <div class="fin-row" style="background:#fffbeb;border-radius:6px;margin:-2px -4px 2px;padding:6px 8px;">
                            <span class="fin-lbl" style="color:#92400e;font-weight:700;">Base (Before Discount)</span>
                            <span class="fin-val" style="color:#92400e;text-decoration:line-through;">PKR {{ number_format($basePriceShown) }}</span>
                        </div>
                        <div class="fin-row" style="background:#fef9c3;border-radius:6px;margin:-2px -4px 4px;padding:6px 8px;border:1px solid #fde68a;">
                            <span class="fin-lbl" style="color:#854d0e;font-weight:700;">
                                <i class="fas fa-tag" style="margin-right:4px;color:#d97706;"></i>
                                Plot Discount{{ $plotDiscReason ? ' ('.$plotDiscReason.')' : '' }}
                            </span>
                            <span class="fin-val" style="color:#d97706;font-weight:800;">− PKR {{ number_format($plotDiscount) }}</span>
                        </div>
                        @endif

                        <div class="fin-row">
                            <span class="fin-lbl">{{ $plotDiscount > 0 ? 'Agreed Price (After Discount)' : 'Total Price' }}</span>
                            <span class="fin-val">PKR {{ number_format($detail->total_price) }}</span>
                        </div>
                        <div class="fin-row">
                            <span class="fin-lbl">Down Payment (agreed)</span>
                            <span class="fin-val">PKR {{ number_format($detail->down_payment ?? 0) }}</span>
                        </div>
                        @if($detail->quarterly_installments)
                        <div class="fin-row">
                            <span class="fin-lbl">Quarterly ({{ $detail->quarterly_installments }} × PKR {{ number_format($detail->quarterly_amount) }})</span>
                            <span class="fin-val">PKR {{ number_format($detail->quarterly_installments * $detail->quarterly_amount) }}</span>
                        </div>
                        @endif
                        @if($detail->total_installments)
                        <div class="fin-row">
                            <span class="fin-lbl">Monthly ({{ $detail->total_installments }} × PKR {{ number_format($detail->monthly_installment) }})</span>
                            <span class="fin-val">PKR {{ number_format($detail->total_installments * $detail->monthly_installment) }}</span>
                        </div>
                        @endif
                        <div class="fin-row">
                            <span class="fin-lbl">Total Received (Cash)</span>
                            <span class="fin-val" style="color:#15803d;">PKR {{ number_format($totalPaid) }}</span>
                        </div>

                        {{-- Payment-level discount (lump-sum settlement) --}}
                        @if($payDiscount > 0)
                        <div class="fin-row" style="background:#f0fdf4;border-radius:6px;margin:2px -4px;padding:6px 8px;border:1px solid #86efac;">
                            <span class="fin-lbl" style="color:#166534;font-weight:700;">
                                <i class="fas fa-percent" style="margin-right:4px;color:#16a34a;"></i>
                                Full-Payment Discount (Waived)
                            </span>
                            <span class="fin-val" style="color:#16a34a;font-weight:800;">− PKR {{ number_format($payDiscount) }}</span>
                        </div>
                        <div class="fin-row" style="background:#f8fafc;border-radius:6px;padding:4px 8px;">
                            <span class="fin-lbl" style="font-size:11px;color:var(--muted-text);">Total Credits (Cash + Discount)</span>
                            <span class="fin-val" style="color:#1d4ed8;">PKR {{ number_format($totalCredits) }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Progress bar --}}
                    <div style="margin-top:14px;">
                        <div style="display:flex;justify-content:space-between;font-size:11px;font-weight:700;margin-bottom:6px;">
                            <span style="color:var(--muted-text);">Payment Progress</span>
                            <span style="color:#1e3a8a;">{{ $paidPctAdj }}%</span>
                        </div>
                        <div style="height:8px;background:var(--hover-bg);border-radius:10px;overflow:hidden;">
                            <div style="height:100%;width:{{ $paidPctAdj }}%;background:linear-gradient(90deg,#1e3a8a,#3b82f6);border-radius:10px;transition:width .3s;"></div>
                        </div>
                    </div>

                    <div class="fin-row-total" style="margin-top:12px;">
                        <span class="fin-total-lbl">Remaining</span>
                        <span class="fin-total-val" style="color:{{ $remainingAdj > 0 ? '#dc2626' : '#15803d' }};">
                            {{ $remainingAdj > 0 ? 'PKR '.number_format($remainingAdj) : '✓ Cleared' }}
                        </span>
                    </div>

                    {{-- Discount summary badge --}}
                    @if($plotDiscount > 0 || $payDiscount > 0)
                    <div style="margin-top:10px;background:linear-gradient(135deg,#fef9c3,#fef3c7);border:1px solid #fde68a;border-radius:8px;padding:10px 12px;">
                        <div style="font-size:10px;font-weight:800;color:#92400e;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                            <i class="fas fa-gift" style="margin-right:4px;"></i> Discount Summary
                        </div>
                        @if($plotDiscount > 0)
                        <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:3px;">
                            <span style="color:#78350f;">Plot Discount (at booking)</span>
                            <span style="font-weight:700;color:#d97706;">PKR {{ number_format($plotDiscount) }}</span>
                        </div>
                        @endif
                        @if($payDiscount > 0)
                        <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:3px;">
                            <span style="color:#166534;">Full-Payment Discount (waived)</span>
                            <span style="font-weight:700;color:#16a34a;">PKR {{ number_format($payDiscount) }}</span>
                        </div>
                        @endif
                        <div style="border-top:1px solid #fde68a;margin-top:5px;padding-top:5px;display:flex;justify-content:space-between;font-size:12px;font-weight:800;">
                            <span style="color:#92400e;">Total Savings</span>
                            <span style="color:#d97706;">PKR {{ number_format($plotDiscount + $payDiscount) }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Fee flags --}}
                    <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
                        <div>
                            <div style="font-size:10px;font-weight:700;color:var(--muted-text);margin-bottom:4px;">REGISTRY FEE</div>
                            <span class="fee-flag {{ $detail->has_registry_fee ? 'fee-yes' : 'fee-no' }}">
                                <i class="fas fa-{{ $detail->has_registry_fee ? 'check' : 'times' }}"></i>
                                {{ $detail->has_registry_fee ? 'Applicable' : 'Not Applicable' }}
                            </span>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;color:var(--muted-text);margin-bottom:4px;">DEVELOPMENT FEE</div>
                            <span class="fee-flag {{ $detail->has_development_fee ? 'fee-yes' : 'fee-no' }}">
                                <i class="fas fa-{{ $detail->has_development_fee ? 'check' : 'times' }}"></i>
                                {{ $detail->has_development_fee ? 'Applicable' : 'Not Applicable' }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Booking Meta --}}
            <div class="det-card">
                <div class="det-head" style="background:var(--hover-bg);">
                    <div class="det-head-icon" style="background:#fdf4ff;color:#7c3aed;"><i class="fas fa-calendar-check"></i></div>
                    <h6 style="color:var(--text-main);">Booking Info</h6>
                </div>
                <div class="det-body">
                    <div class="det-row"><span class="det-lbl">Booking Date</span><span class="det-val">{{ \Carbon\Carbon::parse($detail->booking_date)->format('d M Y') }}</span></div>
                    <div class="det-row"><span class="det-lbl">Created</span><span class="det-val">{{ \Carbon\Carbon::parse($detail->created_at)->format('d M Y, h:i A') }}</span></div>
                    <div class="det-row"><span class="det-lbl">Created By</span><span class="det-val">{{ $detail->createdBy->name ?? '—' }}</span></div>
                    @if($detail->remarks)
                    <div class="det-row">
                        <span class="det-lbl">Remarks</span>
                        <span class="det-val-muted">{{ $detail->remarks }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="det-card">
                <div class="det-head" style="background:var(--hover-bg);">
                    <div class="det-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-bolt"></i></div>
                    <h6 style="color:var(--text-main);">Quick Actions</h6>
                </div>
                <div class="det-body" style="display:flex;flex-direction:column;gap:8px;">
                    <a href="{{ route('downloadPDF', Hashids::encode($detail->id)) }}" target="_blank"
                        style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--hover-bg);border:1px solid var(--border-color);border-radius:10px;text-decoration:none;color:var(--text-main);font-size:13px;font-weight:600;transition:all .15s;">
                        <i class="fas fa-file-pdf" style="color:#1d4ed8;width:16px;"></i> Download Receipt PDF
                    </a>
                    <a href="{{ route('booking.application.form', Hashids::encode($detail->id)) }}" target="_blank"
                        style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--hover-bg);border:1px solid var(--border-color);border-radius:10px;text-decoration:none;color:var(--text-main);font-size:13px;font-weight:600;transition:all .15s;">
                        <i class="fas fa-file-alt" style="color:#15803d;width:16px;"></i> Application Form PDF
                    </a>
                    <a href="{{ route('booking.agreement', $detail->id) }}" target="_blank"
                        style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--hover-bg);border:1px solid var(--border-color);border-radius:10px;text-decoration:none;color:var(--text-main);font-size:13px;font-weight:600;transition:all .15s;">
                        <i class="fas fa-file-contract" style="color:#7c3aed;width:16px;"></i> Agreement PDF
                    </a>
                    @php
                        $plotCatsCheck = ['down_payment','installment','monthly_installment',
                                          'quarterly_installment','plot_balance'];
                        $paidCheck = $detail->payments->where('status','paid')
                                        ->whereIn('payment_category',$plotCatsCheck)->sum('amount_paid');
                        $possReady = $paidCheck >= $detail->total_price;
                    @endphp
                    @if($possReady || $detail->status === 'completed')
                    <a href="{{ route('booking.possession.letter', $detail->id) }}" target="_blank"
                        style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;text-decoration:none;color:#15803d;font-size:13px;font-weight:700;transition:all .15s;">
                        <i class="fas fa-home" style="width:16px;"></i> Possession Letter PDF
                    </a>
                    @else
                    <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:12px;font-weight:600;color:#dc2626;">
                        <i class="fas fa-lock" style="width:16px;"></i> Possession Letter — Dues Pending
                    </div>
                    @endif

                    {{-- Change Installment Plan --}}
                    @if(in_array($detail->status, ['active','pending']) && $detail->total_installments)
                    @can('booking_plan_change')
                    <button type="button" onclick="openPlanModal()"
                        style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#fefce8;border:1.5px solid #fde68a;border-radius:10px;color:#92400e;font-size:13px;font-weight:700;cursor:pointer;width:100%;text-align:left;transition:all .15s;">
                        <i class="fas fa-sliders-h" style="width:16px;color:#d97706;"></i> Change Installment Plan
                    </button>
                    @endcan
                    @endif
                </div>
            </div>

        </div>

        {{-- ── Plan Change History (right column, below Quick Actions) ── --}}
        @if($detail->planChanges->isNotEmpty())
        <div class="col-lg-4" style="margin-top:-18px;">
            <div class="det-card">
                <div class="det-head" style="background:var(--hover-bg);">
                    <div class="det-head-icon" style="background:#fefce8;color:#d97706;"><i class="fas fa-history"></i></div>
                    <h6 style="color:var(--text-main);">Plan Change History</h6>
                    <span style="margin-left:auto;font-size:10px;color:var(--muted-text);font-weight:600;">
                        {{ $detail->planChanges->count() }} change(s)
                    </span>
                </div>
                <div class="det-body" style="padding:0;">
                    <table class="plan-hist-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Change</th>
                                <th>New Amt</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($detail->planChanges as $pc)
                        <tr>
                            <td style="font-size:11px;color:var(--muted-text);">
                                {{ \Carbon\Carbon::parse($pc->created_at)->format('d M Y') }}
                            </td>
                            <td>
                                <span style="font-weight:700;color:#1e3a8a;">{{ $pc->old_installments }}</span>
                                <span class="change-arrow">→</span>
                                <span style="font-weight:700;color:#15803d;">{{ $pc->new_installments }}</span>
                                <div style="font-size:10px;color:var(--muted-text);">{{ $pc->installments_paid }} paid</div>
                            </td>
                            <td style="font-weight:700;color:#15803d;font-size:11px;">
                                PKR {{ number_format($pc->new_monthly_amount) }}
                            </td>
                            <td style="font-size:11px;color:var(--muted-text);">
                                {{ $pc->changedBy->name ?? '—' }}
                                @if($pc->reason)
                                <div style="font-size:10px;font-style:italic;color:var(--muted-text);" title="{{ $pc->reason }}">
                                    {{ \Illuminate\Support\Str::limit($pc->reason, 25) }}
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>

</div>

{{-- ══ Change Installment Plan Modal ══ --}}
@if(in_array($detail->status, ['active','pending']) && $detail->total_installments)
@can('booking_plan_change')
@php
    $planCats       = ['down_payment','installment','quarterly_installment','plot_balance','others'];
    $planPaid       = $detail->payments->where('status','paid')->whereIn('payment_category',$planCats)->sum('amount_paid');
    $planRemain     = max(0, (float)$detail->total_price - (float)$planPaid);
    $instPaid       = $detail->payments->where('status','paid')->where('payment_category','installment')->count();
    $instLeft       = max(0, (int)$detail->total_installments - $instPaid);
    // Future quarterly dues — excluded from monthly installment calculation
    $planQtrTotal   = (int)($detail->quarterly_installments ?? 0);
    $planQtrAmount  = (float)($detail->quarterly_amount ?? 0);
    $planQtrPaid    = $detail->payments->where('status','paid')->where('payment_category','quarterly_installment')->count();
    $planQtrLeft    = max(0, $planQtrTotal - $planQtrPaid);
    $futureQtrTotal = $planQtrLeft * $planQtrAmount;
    $monthlyPortion = max(0, $planRemain - $futureQtrTotal);
@endphp
<div id="planModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.2);">

        {{-- Modal Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #f1f5f9;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;background:#fefce8;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-sliders-h" style="color:#d97706;font-size:14px;"></i>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:#0f172a;">Change Installment Plan</div>
                    <div style="font-size:11px;color:#94a3b8;">Ref: {{ $detail->customer_booking_id }}</div>
                </div>
            </div>
            <button onclick="closePlanModal()" style="background:none;border:none;cursor:pointer;font-size:22px;color:#94a3b8;line-height:1;">×</button>
        </div>

        {{-- Current Plan Summary --}}
        <div style="margin:16px 22px 0;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:13px 16px;">
            <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:10px;">Current Plan</div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;text-align:center;">
                <div>
                    <div style="font-size:10px;color:#94a3b8;font-weight:700;">Total</div>
                    <div style="font-size:16px;font-weight:800;color:#1e3a8a;">{{ $detail->total_installments }}</div>
                    <div style="font-size:10px;color:#94a3b8;">installments</div>
                </div>
                <div>
                    <div style="font-size:10px;color:#94a3b8;font-weight:700;">Paid</div>
                    <div style="font-size:16px;font-weight:800;color:#15803d;">{{ $instPaid }}</div>
                    <div style="font-size:10px;color:#94a3b8;">received</div>
                </div>
                <div>
                    <div style="font-size:10px;color:#94a3b8;font-weight:700;">Remaining</div>
                    <div style="font-size:16px;font-weight:800;color:#dc2626;">{{ $instLeft }}</div>
                    <div style="font-size:10px;color:#94a3b8;">left</div>
                </div>
            </div>
            <div style="margin-top:10px;padding-top:10px;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;font-size:12px;">
                <span style="color:#64748b;">Current monthly amount</span>
                <span style="font-weight:700;color:#1e3a8a;">PKR {{ number_format($detail->monthly_installment) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-top:4px;">
                <span style="color:#64748b;">Total remaining</span>
                <span style="font-weight:700;color:#dc2626;">PKR {{ number_format($planRemain) }}</span>
            </div>
            @if($futureQtrTotal > 0)
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-top:3px;">
                <span style="color:#94a3b8;">↳ Future quarterly ({{ $planQtrLeft }} × PKR {{ number_format($planQtrAmount) }})</span>
                <span style="color:#b45309;font-weight:600;">− PKR {{ number_format($futureQtrTotal) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-top:3px;border-top:1px solid #e2e8f0;padding-top:4px;">
                <span style="color:#64748b;font-weight:700;">Monthly portion</span>
                <span style="font-weight:800;color:#1e3a8a;">PKR {{ number_format($monthlyPortion) }}</span>
            </div>
            @endif
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('booking.change.plan', $detail->id) }}">
            @csrf
            <div style="padding:16px 22px;">

                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                        New Total Installments <span style="color:#dc2626;">*</span>
                        <small style="font-weight:400;color:#94a3b8;">(min: {{ $instPaid }} already paid)</small>
                    </label>
                    <input type="number"
                           name="new_total_installments"
                           id="newInstInput"
                           min="{{ max(1, $instPaid) }}"
                           value="{{ old('new_total_installments', $detail->total_installments) }}"
                           oninput="previewNewAmount()"
                           style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 13px;font-size:14px;font-weight:700;color:#0f172a;outline:none;transition:border-color .15s;"
                           onfocus="this.style.borderColor='#d97706'" onblur="this.style.borderColor='#e2e8f0'"
                           required>
                    @error('new_total_installments')
                    <div style="font-size:11px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Live Preview --}}
                <div id="planPreview" style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:14px;">
                    <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#15803d;margin-bottom:6px;">New Plan Preview</div>
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
                        <div style="text-align:center;">
                            <div style="font-size:10px;color:#64748b;">Remaining installments</div>
                            <div id="previewRemaining" style="font-size:18px;font-weight:800;color:#1d4ed8;">—</div>
                        </div>
                        <div style="font-size:20px;color:#94a3b8;">→</div>
                        <div style="text-align:center;">
                            <div style="font-size:10px;color:#64748b;">New monthly amount</div>
                            <div id="previewAmount" style="font-size:18px;font-weight:800;color:#15803d;">—</div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:6px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                        Reason <small style="font-weight:400;color:#94a3b8;">(optional)</small>
                    </label>
                    <textarea name="reason" rows="2"
                        placeholder="e.g. Customer requested extension due to financial hardship"
                        style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 13px;font-size:13px;color:#0f172a;outline:none;resize:vertical;font-family:inherit;transition:border-color .15s;"
                        onfocus="this.style.borderColor='#d97706'" onblur="this.style.borderColor='#e2e8f0'">{{ old('reason') }}</textarea>
                </div>

            </div>

            <div style="padding:14px 22px;border-top:1px solid #f1f5f9;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closePlanModal()"
                    style="background:#f1f5f9;color:#475569;border:none;border-radius:9px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                    Cancel
                </button>
                <button type="submit"
                    style="background:#d97706;color:#fff;border:none;border-radius:9px;padding:10px 22px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:7px;">
                    <i class="fas fa-check"></i> Confirm Change
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const _remaining   = {{ $planRemain }};
const _instPaid    = {{ $instPaid }};
const _futureQtr   = {{ $futureQtrTotal }};   // future quarterly total excluded from monthly

function openPlanModal() {
    document.getElementById('planModal').style.display = 'flex';
    previewNewAmount();
}
function closePlanModal() {
    document.getElementById('planModal').style.display = 'none';
}
document.getElementById('planModal').addEventListener('click', function(e) {
    if (e.target === this) closePlanModal();
});

function previewNewAmount() {
    const newTotal = parseInt(document.getElementById('newInstInput').value, 10);
    const previewRemaining = document.getElementById('previewRemaining');
    const previewAmount    = document.getElementById('previewAmount');
    const preview          = document.getElementById('planPreview');

    if (isNaN(newTotal) || newTotal < _instPaid) {
        previewRemaining.textContent = '—';
        previewAmount.textContent = '—';
        preview.style.borderColor = '#fecaca';
        preview.style.background  = '#fef2f2';
        return;
    }

    const remaining      = newTotal - _instPaid;
    const monthlyPortion = Math.max(0, _remaining - _futureQtr);
    const newAmt         = remaining > 0 ? Math.round(monthlyPortion / remaining) : 0;

    previewRemaining.textContent = remaining + ' months';
    previewAmount.textContent = 'PKR ' + newAmt.toLocaleString();
    preview.style.borderColor = '#86efac';
    preview.style.background  = '#f0fdf4';
}

// Auto-open if there were validation errors
@if($errors->hasBag('default') && $errors->has('new_total_installments'))
    document.addEventListener('DOMContentLoaded', openPlanModal);
@endif
</script>
@endcan
@endif

@endsection
