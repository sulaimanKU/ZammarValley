@extends('layouts.index')
@push('styles')
<style>
.bk-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;overflow:hidden;margin-bottom:18px;}
.bk-head{padding:14px 20px;display:flex;align-items:center;gap:10px;border-bottom:1px solid var(--border-color);}
.bk-head-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;color:#fff;flex-shrink:0;}
.bk-head h6{font-size:11px;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.7px;margin:0;}
.bk-body{padding:22px;}
.bk-sub{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--muted-text);padding:8px 0 12px;display:flex;align-items:center;gap:6px;border-bottom:1px solid var(--border-color);margin-bottom:14px;}

/* plot info bar */
.plot-info-bar{display:flex;flex-wrap:wrap;gap:14px;align-items:center;background:linear-gradient(135deg,#1e3a8a,#3b82f6);border-radius:12px;padding:16px 20px;margin-bottom:4px;}
.pib-item{text-align:center;}
.pib-lbl{font-size:9px;font-weight:800;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.5px;display:block;}
.pib-val{font-size:14px;font-weight:800;color:#fff;display:block;margin-top:2px;}
.pib-sep{width:1px;height:36px;background:rgba(255,255,255,.2);}
.pib-change{margin-left:auto;background:rgba(255,255,255,.15);border:none;color:#fff;border-radius:8px;padding:7px 14px;font-size:12px;cursor:pointer;text-decoration:none;}
.pib-change:hover{background:rgba(255,255,255,.25);color:#fff;}

/* fee cards */
.fee-card-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:18px;}
@media(max-width:768px){.fee-card-grid{grid-template-columns:1fr;}}
.fee-card{display:block;background:var(--hover-bg);border:2px solid var(--border-color);border-radius:12px;padding:16px 16px 14px;cursor:pointer;transition:border-color .2s,background .2s;position:relative;user-select:none;}
.fee-card.active{border-color:#3b82f6;background:rgba(59,130,246,.07);}
.fee-card-top{display:flex;align-items:flex-start;gap:12px;}
.fee-card-ico{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;}
.fee-card-info{flex:1;}
.fee-card-title{font-size:13px;font-weight:700;color:var(--text-main);}
.fee-card-sub{font-size:11px;color:var(--muted-text);margin-top:2px;}
/* toggle switch */
.fee-switch{width:40px;height:22px;flex-shrink:0;position:relative;margin-top:2px;}
.fee-switch input{opacity:0;width:0;height:0;position:absolute;}
.fee-slider{position:absolute;inset:0;background:#cbd5e1;border-radius:22px;transition:.2s;cursor:pointer;}
.fee-slider:before{content:'';position:absolute;width:16px;height:16px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.2);}
.fee-card.active .fee-slider{background:#3b82f6;}
.fee-card.active .fee-slider:before{transform:translateX(18px);}
/* amount reveal */
.fee-amt-box{display:none;margin-top:12px;padding-top:12px;border-top:1px solid var(--border-color);}
.fee-card.active .fee-amt-box{display:block;}

/* declaration */
.decl-box{background:var(--hover-bg);border:1px solid var(--border-color);border-radius:10px;padding:18px;font-size:12px;color:var(--sub-text);line-height:1.8;margin-bottom:18px;}
.decl-box strong{color:var(--text-main);}
.decl-fill{border-bottom:1px solid var(--border-color);min-width:80px;display:inline-block;text-align:center;font-weight:700;color:var(--text-main);}

.submit-bar{position:sticky;bottom:16px;z-index:10;background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;box-shadow:0 -2px 16px rgba(30,58,138,.08);}
input[type=number]{-moz-appearance:textfield;}
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
</style>
@endpush

@section('content')
@can('add_book')
<div class="ldg-wrap">

    <div class="rpt-header no-print">
        <div>
            <p class="rpt-header-title">New Booking</p>
            <p class="rpt-header-sub">{{ $config['name'] }} · Plot #{{ $plot->plot_number }} · {{ $plot->block }}</p>
        </div>
        <div class="rpt-header-actions">
            <a href="{{ route('booking.search') }}" class="btn-soft-header">
                <i class="fas fa-arrow-left"></i> Change Plot
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert-flash alert-flash-error mb-4">
            <i class="fas fa-exclamation-circle"></i>
            <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        </div>
    @endif

    <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="plot_id" value="{{ $plot->id }}">

    {{-- ══ PLOT INFO BAR ══ --}}
    <div class="plot-info-bar">
        <div class="pib-item"><span class="pib-lbl">Plot No.</span><span class="pib-val">#{{ $plot->plot_number }}</span></div>
        <div class="pib-sep"></div>
        <div class="pib-item"><span class="pib-lbl">Block</span><span class="pib-val">{{ $plot->block }}</span></div>
        <div class="pib-sep"></div>
        <div class="pib-item"><span class="pib-lbl">Street</span><span class="pib-val">{{ $plot->street_number ?? '—' }}</span></div>
        <div class="pib-sep"></div>
        <div class="pib-item"><span class="pib-lbl">Size</span><span class="pib-val">{{ $plot->size }} {{ $plot->unit }}</span></div>
        <div class="pib-sep"></div>
        @if($plot->discount_amount > 0)
        <div class="pib-item">
            <span class="pib-lbl">Original Price</span>
            <span class="pib-val" style="text-decoration:line-through;color:#94a3b8;font-size:11px;">PKR {{ number_format($plot->base_price) }}</span>
        </div>
        <div class="pib-sep"></div>
        <div class="pib-item">
            <span class="pib-lbl">After Discount</span>
            <span class="pib-val" style="color:#15803d;">PKR {{ number_format($plot->base_price - $plot->discount_amount) }}</span>
        </div>
        @else
        <div class="pib-item"><span class="pib-lbl">Base Price</span><span class="pib-val">{{ $plot->base_price ? 'PKR '.number_format($plot->base_price) : '—' }}</span></div>
        @endif
        <div class="pib-sep"></div>
        <div class="pib-item"><span class="pib-lbl">Type</span><span class="pib-val">{{ ucfirst($plot->price_type) }}</span></div>
        <a href="{{ route('booking.search') }}" class="pib-change">
            <i class="fas fa-exchange-alt me-1"></i> Change Plot
        </a>
    </div>

    {{-- ══ SECTION I: APPLICANT ══ --}}
    <div class="bk-card">
        <div class="bk-head" style="background:linear-gradient(135deg,#065f46,#059669);">
            <div class="bk-head-icon" style="background:rgba(255,255,255,.15);"><i class="fas fa-user"></i></div>
            <h6>I. Applicant Information</h6>
        </div>
        <div class="bk-body">

            <div class="bk-sub"><i class="fas fa-id-card"></i> Personal Details</div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name') }}" placeholder="As per CNIC" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Father / Husband Name <span class="text-danger">*</span></label>
                    <input type="text" name="guardian_name" class="form-control"
                        value="{{ old('guardian_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">CNIC No. <span class="text-danger">*</span></label>
                    <input type="text" name="cnic" class="form-control"
                        placeholder="35202-1234567-1" value="{{ old('cnic') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Age</label>
                    <input type="number" name="age" class="form-control"
                        placeholder="e.g. 35" value="{{ old('age') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Nationality</label>
                    <input type="text" name="nationality" class="form-control"
                        value="{{ old('nationality', 'Pakistani') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Occupation</label>
                    <input type="text" name="occupation" class="form-control"
                        placeholder="e.g. Business, Service" value="{{ old('occupation') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                </div>
            </div>

            <div class="bk-sub"><i class="fas fa-phone"></i> Contact</div>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Mobile <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control"
                        placeholder="03XXXXXXXXX" value="{{ old('mobile') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Phone (Res.)</label>
                    <input type="text" name="phone_res" class="form-control"
                        placeholder="051-XXXXXXX" value="{{ old('phone_res') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Phone (Off.)</label>
                    <input type="text" name="phone_off" class="form-control"
                        placeholder="051-XXXXXXX" value="{{ old('phone_off') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
            </div>

            <div class="bk-sub"><i class="fas fa-map-pin"></i> Address</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Postal Address</label>
                    <input type="text" name="postal_address" class="form-control"
                        placeholder="Address for correspondence" value="{{ old('postal_address') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Residential Address</label>
                    <input type="text" name="residential_address" class="form-control"
                        placeholder="Current home address" value="{{ old('residential_address') }}">
                </div>
            </div>

            <div class="bk-sub"><i class="fas fa-file-image"></i> Applicant Documents</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Applicant Photo</label>
                    <input type="file" name="customer_pic" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">CNIC Front</label>
                    <input type="file" name="cnic_pic" class="form-control" accept="image/*,.pdf">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">CNIC Back</label>
                    <input type="file" name="cnic_pic_back" class="form-control" accept="image/*,.pdf">
                </div>
            </div>

        </div>
    </div>

    {{-- ══ SECTION II: NOMINEE ══ --}}
    <div class="bk-card">
        <div class="bk-head" style="background:linear-gradient(135deg,#1e40af,#6366f1);">
            <div class="bk-head-icon" style="background:rgba(255,255,255,.15);"><i class="fas fa-user-friends"></i></div>
            <h6>II. Nominee Details</h6>
        </div>
        <div class="bk-body">
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Nominee Full Name</label>
                    <input type="text" name="nominee_name" class="form-control" value="{{ old('nominee_name') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Relation with Applicant</label>
                    <input type="text" name="nominee_relation" class="form-control"
                        placeholder="e.g. Son, Wife, Brother" value="{{ old('nominee_relation') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Nominee CNIC No.</label>
                    <input type="text" name="nominee_cnic" class="form-control"
                        placeholder="35202-1234567-1" value="{{ old('nominee_cnic') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Nominee Address</label>
                    <input type="text" name="nominee_address" class="form-control" value="{{ old('nominee_address') }}">
                </div>
            </div>

            <div class="bk-sub"><i class="fas fa-file-image"></i> Nominee Documents</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Nominee Photo</label>
                    <input type="file" name="nominee_pic" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Nominee CNIC Front</label>
                    <input type="file" name="nominee_cnic_front" class="form-control" accept="image/*,.pdf">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Nominee CNIC Back</label>
                    <input type="file" name="nominee_cnic_back" class="form-control" accept="image/*,.pdf">
                </div>
            </div>
        </div>
    </div>

    {{-- ══ SECTION II: FINANCIAL ══ --}}
    <div class="bk-card">
        <div class="bk-head" style="background:linear-gradient(135deg,#1e40af,#3b82f6);">
            <div class="bk-head-icon" style="background:rgba(255,255,255,.15);"><i class="fas fa-money-bill-wave"></i></div>
            <h6>III. Financial Details</h6>
        </div>
        <div class="bk-body">

            {{-- Discount notice --}}
            @if($plot->discount_amount > 0)
            @php
                $effPrice   = max(0, (float)$plot->base_price - (float)$plot->discount_amount);
                $discPct    = $plot->base_price > 0 ? round($plot->discount_amount / $plot->base_price * 100, 1) : 0;
            @endphp
            <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                <i class="fas fa-tag" style="color:#15803d;font-size:16px;"></i>
                <div style="flex:1;">
                    <div style="font-size:12px;font-weight:800;color:#15803d;">
                        Discount Applied: {{ $discPct }}% off
                        @if($plot->discount_reason) — {{ $plot->discount_reason }} @endif
                    </div>
                    <div style="font-size:11px;color:#166534;margin-top:2px;">
                        Original: <span style="text-decoration:line-through;">PKR {{ number_format($plot->base_price) }}</span>
                        &nbsp;→&nbsp;
                        Saving: PKR {{ number_format($plot->discount_amount) }}
                        &nbsp;→&nbsp;
                        <strong>Final: PKR {{ number_format($effPrice) }}</strong>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Total Price (PKR) <span class="text-danger">*</span>
                        @if($plot->discount_amount > 0)
                        <span style="font-size:9px;font-weight:700;background:#f0fdf4;color:#15803d;padding:1px 6px;border-radius:10px;margin-left:4px;">After Discount</span>
                        @endif
                    </label>
                    <input type="number" name="total_price" id="fin_total" class="form-control"
                        value="{{ old('total_price', $plot->discount_amount > 0 ? max(0, $plot->base_price - $plot->discount_amount) : $plot->base_price) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Down Payment (PKR)</label>
                    <input type="number" name="down_payment" id="fin_down" class="form-control"
                        value="{{ old('down_payment', $plot->down_payment) }}" placeholder="0">
                </div>

                @if($plot->price_type === 'installment')
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Quarterly Payments</label>
                    <input type="number" name="quarterly_installments" id="fin_qi" class="form-control"
                        value="{{ old('quarterly_installments', $plot->quarterly_installments) }}" placeholder="e.g. 6">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Amount / Quarter (PKR) <small class="text-muted">— auto</small></label>
                    <input type="number" name="quarterly_amount" id="fin_qa" class="form-control"
                        value="{{ old('quarterly_amount', $plot->quarterly_amount) }}"
                        placeholder="auto" readonly style="background:var(--hover-bg);">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Monthly Payments</label>
                    <input type="number" name="total_installments" id="fin_ti" class="form-control"
                        value="{{ old('total_installments', $plot->total_installments) }}" placeholder="e.g. 18">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Amount / Month (PKR) <small class="text-muted">— auto</small></label>
                    <input type="number" name="monthly_installment" id="fin_monthly" class="form-control"
                        value="{{ old('monthly_installment', $plot->installment_amount) }}"
                        placeholder="auto" readonly style="background:var(--hover-bg);">
                </div>
                @endif
            </div>

            {{-- Applicable Fees — read-only, sourced from plot settings --}}
            @php
                $pf        = $plotFees ?? [];
                $hasReg    = (bool)($pf['has_registry_fee']    ?? false);
                $hasDev    = (bool)($pf['has_development_fee'] ?? false);
                $hasSec    = (bool)($pf['has_security_fee']    ?? false);
                $regAmt    = $pf['registry_fee_amount']    ?? null;
                $devAmt    = $pf['development_fee_amount'] ?? null;
                $secAmt    = $pf['security_fee_amount']    ?? null;
                $anyFee    = $hasReg || $hasDev || $hasSec;
            @endphp

            {{-- Hidden inputs — always submitted so the controller gets the right flags & amounts --}}
            @if($hasReg)
                <input type="hidden" name="has_registry_fee"    value="1">
                <input type="hidden" name="registry_fee_amount" value="{{ $regAmt }}">
            @endif
            @if($hasDev)
                <input type="hidden" name="has_development_fee"    value="1">
                <input type="hidden" name="development_fee_amount" value="{{ $devAmt }}">
            @endif
            @if($hasSec)
                <input type="hidden" name="has_security_fee"    value="1">
                <input type="hidden" name="security_fee_amount" value="{{ $secAmt }}">
            @endif

            <div style="margin-top:18px;">
                <div class="bk-sub" style="margin-bottom:14px;"><i class="fas fa-receipt"></i> Applicable Fees <span style="font-size:10px;font-weight:600;color:var(--muted-text);margin-left:6px;">— inherited from plot</span></div>

                @if($anyFee)
                @php
                $feeList = [];
                if($hasReg) $feeList[] = ['key'=>'reg','flag'=>'has_registry_fee',   'amt_field'=>'registry_fee_amount',   'amt'=>$regAmt,'icon'=>'fas fa-stamp',     'color'=>'#b45309','bg'=>'rgba(180,83,9,.07)', 'border'=>'#fcd9a8','label'=>'Registry Fee',   'sub'=>'Property registration'];
                if($hasDev) $feeList[] = ['key'=>'dev','flag'=>'has_development_fee','amt_field'=>'development_fee_amount','amt'=>$devAmt,'icon'=>'fas fa-hard-hat',  'color'=>'#1d4ed8','bg'=>'rgba(29,78,216,.07)','border'=>'#bfdbfe','label'=>'Development Fee','sub'=>'Society development'];
                if($hasSec) $feeList[] = ['key'=>'sec','flag'=>'has_security_fee',   'amt_field'=>'security_fee_amount',   'amt'=>$secAmt,'icon'=>'fas fa-shield-alt','color'=>'#059669','bg'=>'rgba(5,150,105,.07)','border'=>'#a7f3d0','label'=>'Security Fee',   'sub'=>'Refundable deposit'];
                @endphp

                <div style="display:flex;flex-direction:column;gap:14px;">
                @foreach($feeList as $fl)
                @php
                    $fk      = $fl['key'];
                    $oldAmt  = old($fk.'_paid_amount');
                    $oldDate = old($fk.'_paid_date');
                @endphp

                <div style="border:1.5px solid {{ $fl['border'] }};border-radius:13px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.04);">

                    {{-- ── Top bar: icon · name · amount badge · Pending/Paid status ── --}}
                    <div style="display:flex;align-items:center;gap:12px;padding:13px 16px;background:{{ $fl['bg'] }};">
                        <div style="width:38px;height:38px;border-radius:10px;background:#fff;border:1px solid {{ $fl['border'] }};display:flex;align-items:center;justify-content:center;color:{{ $fl['color'] }};font-size:16px;flex-shrink:0;">
                            <i class="{{ $fl['icon'] }}"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:{{ $fl['color'] }};">{{ $fl['label'] }}</div>
                            <div style="font-size:11px;color:#64748b;">{{ $fl['sub'] }}</div>
                            {{-- Placeholder for backdated security fee hint --}}
                            <div id="{{ $fl['key'] }}_due_hint" style="margin-top:2px;"></div>
                        </div>
                        @if($fl['amt'])
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-size:10px;color:#64748b;font-weight:600;">Total Due</div>
                            <div style="font-size:14px;font-weight:800;color:{{ $fl['color'] }};">PKR {{ number_format($fl['amt']) }}</div>
                        </div>
                        @endif
                        {{-- Dynamic paid/pending badge — updated by JS --}}
                        <div id="badge_{{ $fk }}" style="flex-shrink:0;margin-left:6px;">
                            <span id="badge_pending_{{ $fk }}" style="font-size:10px;font-weight:700;background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:20px;padding:3px 10px;white-space:nowrap;">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                            <span id="badge_paid_{{ $fk }}" style="display:none;font-size:10px;font-weight:700;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px;white-space:nowrap;">
                                <i class="fas fa-check-circle"></i> Paid at Booking
                            </span>
                        </div>
                    </div>

                    {{-- ── Payment fields ── --}}
                    <div style="padding:14px 16px;background:#fff;border-top:1px solid {{ $fl['border'] }};">
                        
                        @if($fk === 'sec')
                        <div style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:12px;">
                            <i class="fas fa-calendar-alt me-1" style="color:{{ $fl['color'] }};"></i>
                            Security Fee Duration
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-md-3">
                                <label class="form-label" style="font-size:11px;font-weight:600;margin-bottom:4px;">Applied From</label>
                                <input type="date" name="security_fee_start_date" class="form-control form-control-sm"
                                    value="{{ old('security_fee_start_date') }}"
                                    onchange="calcSecurityDue()">
                                <div style="font-size:9px;color:#94a3b8;margin-top:2px;">Defaults to Booking Date</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" style="font-size:11px;font-weight:600;margin-bottom:4px;">Applied To</label>
                                <input type="date" name="security_fee_end_date" class="form-control form-control-sm"
                                    value="{{ old('security_fee_end_date') }}"
                                    onchange="calcSecurityDue()">
                                <div style="font-size:9px;color:#94a3b8;margin-top:2px;">Optional (Leave for future)</div>
                            </div>
                        </div>
                        @endif

                        <div style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:12px;">
                            <i class="fas fa-money-bill-wave me-1" style="color:{{ $fl['color'] }};"></i>
                            Record Payment at Booking
                            <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:10.5px;color:#94a3b8;"> — leave blank to pay later via Fee Management</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <label class="form-label" style="font-size:11px;font-weight:600;margin-bottom:4px;">Amount Paid (PKR)</label>
                                <input type="number" id="fpa_{{ $fk }}" name="{{ $fk }}_paid_amount" step="any" class="form-control form-control-sm"
                                    placeholder="{{ $fl['amt'] ? number_format($fl['amt'],0,'.',''): '' }}"
                                    value="{{ $oldAmt }}"
                                    oninput="feePayBadge('{{ $fk }}')">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" style="font-size:11px;font-weight:600;margin-bottom:4px;">Date Paid</label>
                                <input type="date" name="{{ $fk }}_paid_date" class="form-control form-control-sm"
                                    value="{{ old($fk.'_paid_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" style="font-size:11px;font-weight:600;margin-bottom:4px;">Payment Mode</label>
                                <select name="{{ $fk }}_payment_mode" class="form-select form-select-sm">
                                    <option value="cash"   {{ old($fk.'_payment_mode','cash')=='cash'  ?'selected':'' }}>Cash</option>
                                    <option value="cheque" {{ old($fk.'_payment_mode')=='cheque'?'selected':'' }}>Cheque</option>
                                    <option value="bank"   {{ old($fk.'_payment_mode')=='bank'  ?'selected':'' }}>Bank Transfer</option>
                                    <option value="online" {{ old($fk.'_payment_mode')=='online' ?'selected':'' }}>Online</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" style="font-size:11px;font-weight:600;margin-bottom:4px;">Receipt / Remarks</label>
                                <input type="text" name="{{ $fk }}_receipt_no" class="form-control form-control-sm"
                                    placeholder="Optional" value="{{ old($fk.'_receipt_no') }}">
                            </div>
                        </div>
                    </div>

                </div>
                @endforeach
                </div>

                @else
                <div style="padding:14px 16px;background:var(--hover-bg);border:1px solid var(--border-color);border-radius:10px;font-size:12px;color:var(--muted-text);">
                    <i class="fas fa-info-circle me-1"></i> No fees applicable for this plot.
                </div>
                @endif
            </div>

            <script>
            // Swap badge when amount field has a value
            function feePayBadge(key) {
                var val = parseFloat(document.getElementById('fpa_' + key).value) || 0;
                document.getElementById('badge_pending_' + key).style.display = val > 0 ? 'none'   : '';
                document.getElementById('badge_paid_'    + key).style.display = val > 0 ? ''       : 'none';
            }
            // Init for old() repopulation
            ['reg','dev','sec'].forEach(function(k) {
                var el = document.getElementById('fpa_' + k);
                if (el) feePayBadge(k);
            });

            // Calculate months due for security fee if booking date is backdated
            function calcSecurityDue() {
                const bDateInput = document.getElementsByName('booking_date')[0];
                const sDateInput = document.getElementsByName('security_fee_start_date')[0];
                const eDateInput = document.getElementsByName('security_fee_end_date')[0];
                const hintEl = document.getElementById('sec_due_hint');
                const rate = {{ (float)($plot->security_fee_amount ?? 0) }};

                if (!bDateInput || !hintEl || rate <= 0) return;

                const effectiveStartVal = sDateInput.value || bDateInput.value;
                const bDate = new Date(effectiveStartVal);
                if (isNaN(bDate.getTime())) return;

                const now = eDateInput.value ? new Date(eDateInput.value) : new Date();
                const startM = new Date(bDate.getFullYear(), bDate.getMonth(), 1);
                const nowM   = new Date(now.getFullYear(), now.getMonth(), 1);

                let months = (nowM.getFullYear() - startM.getFullYear()) * 12 + (nowM.getMonth() - startM.getMonth()) + 1;
                if (months < 1) {
                    hintEl.innerHTML = '';
                    return;
                }

                const total = months * rate;
                hintEl.innerHTML = `<div style="font-size:10px;font-weight:700;color:#dc2626;">
                    <i class="fas fa-exclamation-circle"></i> ${months} month(s) due: PKR ${total.toLocaleString()}
                    <button type="button" style="background:none;border:none;color:#1d4ed8;padding:0;font-size:10px;font-weight:800;text-decoration:underline;cursor:pointer;margin-left:5px;"
                        onclick="document.getElementById('fpa_sec').value=${total};feePayBadge('sec');">
                        Pay All
                    </button>
                </div>`;
            }
            // Run on load
            setTimeout(calcSecurityDue, 500);
            </script>

        </div>
    </div>

    {{-- ══ SECTION III: BOOKING META ══ --}}
    <div class="bk-card">
        <div class="bk-head" style="background:linear-gradient(135deg,#581c87,#7c3aed);">
            <div class="bk-head-icon" style="background:rgba(255,255,255,.15);"><i class="fas fa-calendar-check"></i></div>
            <h6>IV. Booking Details</h6>
        </div>
        <div class="bk-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Allotment / Booking No. <span class="text-danger">*</span></label>
                    <input type="text" name="customer_booking_id" class="form-control"
                        placeholder="e.g. FA-001, ZV-2026-001" value="{{ old('customer_booking_id') }}" required>
                    <div style="font-size:10.5px;color:#94a3b8;margin-top:3px;">Your internal reference number</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Booking Date <span class="text-danger">*</span></label>
                    <input type="date" name="booking_date" class="form-control"
                        value="{{ old('booking_date', date('Y-m-d')) }}" 
                        onchange="if(typeof calcSecurityDue === 'function') calcSecurityDue();"
                        required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Amount Enclosed (PKR)</label>
                    <input type="number" name="amount_enclosed" id="amt_enclosed" class="form-control"
                        placeholder="0" value="{{ old('amount_enclosed') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Bank Draft / Pay Order No.</label>
                    <input type="text" name="instrument_no" class="form-control"
                        placeholder="Draft No." value="{{ old('instrument_no') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Drawn On (Bank)</label>
                    <input type="text" name="drawn_on" class="form-control"
                        placeholder="e.g. HBL Islamabad" value="{{ old('drawn_on') }}">
                </div>

                <div class="col-md-9">
                    <label class="form-label small fw-semibold">Remarks</label>
                    <input type="text" name="remarks" class="form-control"
                        placeholder="Any additional notes…" value="{{ old('remarks') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="submit-bar">
        <div style="font-size:12px;color:var(--muted-text);">
            <i class="fas fa-map-marker-alt me-1" style="color:#1e3a8a;"></i>
            Booking for: <strong>Plot #{{ $plot->plot_number }} — {{ $plot->block }}</strong>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('booking.search') }}" class="btn-soft">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" class="btn-navy">
                <i class="fas fa-shield-alt me-1"></i> Save Booking
            </button>
        </div>
    </div>

    </form>
</div>
@endcan
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Select all elements
    var finTotal   = document.getElementById('fin_total');
    var finDown    = document.getElementById('fin_down');
    var finQi      = document.getElementById('fin_qi');
    var finQa      = document.getElementById('fin_qa');
    var finTi      = document.getElementById('fin_ti');
    var finMonthly = document.getElementById('fin_monthly');

    // Fee card toggles
    document.querySelectorAll('.fee-chk').forEach(function(chk) {
        var card = chk.closest('.fee-card');
        chk.addEventListener('change', function() {
            card.classList.toggle('active', chk.checked);
        });
    });
    // Clicking amount inputs should not propagate to the label (which would toggle the checkbox)
    document.querySelectorAll('.fee-amt-box input').forEach(function(inp) {
        inp.addEventListener('click', function(e) { e.stopPropagation(); });
    });

    // Auto-calc financials
    function calcFinancials() {
        if (!finTi) return;
        var total = parseFloat(finTotal?.value) || 0;
        var down  = parseFloat(finDown?.value) || 0;
        var qi    = parseInt(finQi?.value) || 0;
        var qa    = parseFloat(finQa?.value) || 0;
        var ti    = parseInt(finTi?.value) || 0;

        var afterQtr = total - down - (qi * qa);
        if (finMonthly) {
            finMonthly.value = (ti > 0 && afterQtr > 0) ? Math.round(afterQtr / ti) : '';
        }
    }

    [finTotal, finDown, finQi, finQa, finTi].forEach(function(el){
        el?.addEventListener('input', calcFinancials);
    });

    // Disable scroll on number inputs
    document.querySelectorAll('input[type=number]').forEach(function(el) {
        el.addEventListener('wheel', function(e) { e.preventDefault(); }, { passive: false });
    });

});
</script>
