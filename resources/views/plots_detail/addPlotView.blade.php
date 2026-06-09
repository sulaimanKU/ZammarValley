@extends('layouts.index')
@push('styles')
<style>
.add-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;overflow:hidden;margin-bottom:18px;}
.add-card-head{padding:13px 20px;background:var(--hover-bg);border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.add-card-head-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.add-card-head h6{font-size:11px;font-weight:800;color:var(--text-main);text-transform:uppercase;letter-spacing:.7px;margin:0;}
.add-card-head small{font-size:11px;color:var(--muted-text);margin-left:4px;}
.add-card-body{padding:22px;}
.submit-bar{position:sticky;bottom:16px;z-index:10;background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;box-shadow:0 -2px 16px rgba(30,58,138,.08);}
input[type=number]{-moz-appearance:textfield;}
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}

/* ── pricing breakdown bar ── */
.price-breakdown {
    display:none;
    background:var(--hover-bg);
    border:1px solid var(--border-color);
    border-radius:10px;
    padding:14px 18px;
    margin-top:14px;
    gap:0;
}
.price-breakdown.show { display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
.pb-item { display:flex; flex-direction:column; align-items:center; min-width:110px; }
.pb-lbl  { font-size:9px; font-weight:800; color:var(--muted-text); text-transform:uppercase; letter-spacing:.5px; }
.pb-val  { font-size:14px; font-weight:800; color:var(--text-main); margin-top:2px; }
.pb-sep  { width:1px; height:36px; background:var(--border-color); flex-shrink:0; }
.pb-eq   { font-size:18px; font-weight:800; color:var(--muted-text); padding:0 4px; }
.pb-total-ok  { color:#15803d; }
.pb-total-err { color:#dc2626; }

/* balance warning */
.balance-warn {
    display:none;
    background:#fef2f2;
    border:1px solid #fecaca;
    border-radius:8px;
    padding:10px 14px;
    font-size:12px;
    color:#dc2626;
    margin-top:10px;
    align-items:center;
    gap:8px;
}
.balance-warn.show { display:flex; }
.balance-ok {
    display:none;
    background:#f0fdf4;
    border:1px solid #bbf7d0;
    border-radius:8px;
    padding:10px 14px;
    font-size:12px;
    color:#15803d;
    margin-top:10px;
    align-items:center;
    gap:8px;
}
.balance-ok.show { display:flex; }

/* ── Discount breakdown panel ── */
.disc-panel { display:none; background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1.5px solid #86efac; border-radius:12px; padding:16px 20px; margin-top:12px; }
.disc-panel.show { display:flex; flex-wrap:wrap; gap:12px; align-items:center; }
.disc-cell { display:flex; flex-direction:column; align-items:center; min-width:100px; }
.disc-cell-lbl { font-size:9px; font-weight:800; color:#166534; text-transform:uppercase; letter-spacing:.5px; }
.disc-cell-val { font-size:15px; font-weight:800; margin-top:3px; }
.disc-sep { width:1px; height:40px; background:#86efac; flex-shrink:0; }
.disc-arrow { font-size:18px; color:#4ade80; font-weight:800; }

/* ── Fee Cards (same as booking form) ── */
.fee-card-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
@media(max-width:768px){.fee-card-grid{grid-template-columns:1fr;}}
.fee-card{display:block;background:var(--hover-bg);border:2px solid var(--border-color);border-radius:12px;padding:16px 16px 14px;cursor:pointer;transition:border-color .2s,background .2s;position:relative;user-select:none;}
.fee-card.active{border-color:#3b82f6;background:rgba(59,130,246,.07);}
.fee-card-top{display:flex;align-items:flex-start;gap:12px;}
.fee-card-ico{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;}
.fee-card-info{flex:1;}
.fee-card-title{font-size:13px;font-weight:700;color:var(--text-main);}
.fee-card-sub{font-size:11px;color:var(--muted-text);margin-top:2px;}
.fee-switch{width:40px;height:22px;flex-shrink:0;position:relative;margin-top:2px;}
.fee-switch input{opacity:0;width:0;height:0;position:absolute;}
.fee-slider{position:absolute;inset:0;background:#cbd5e1;border-radius:22px;transition:.2s;cursor:pointer;}
.fee-slider:before{content:'';position:absolute;width:16px;height:16px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.2);}
.fee-card.active .fee-slider{background:#3b82f6;}
.fee-card.active .fee-slider:before{transform:translateX(18px);}
.fee-amt-box{display:none;margin-top:12px;padding-top:12px;border-top:1px solid var(--border-color);}
.fee-card.active .fee-amt-box{display:block;}
</style>
@endpush

@section('content')
<div class="ldg-wrap">

    <div class="rpt-header no-print">
        <div>
            <p class="rpt-header-title">Add New Plot</p>
            <p class="rpt-header-sub">
                <a href="{{ route('index.plots') }}" style="color:rgba(255,255,255,.6);text-decoration:none;">
                    <i class="fas fa-arrow-left me-1"></i> Back to All Plots
                </a>
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-flash alert-flash-success mb-4" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <span><i class="fas fa-check-circle"></i> {{ session('success') }}</span>
            @if(session('new_plot_id'))
            <a href="{{ route('booking.create', session('new_plot_id')) }}"
               class="btn-navy"
               style="white-space:nowrap;flex-shrink:0;">
                <i class="fas fa-calendar-plus me-1"></i> Add Booking for Plot #{{ session('new_plot_number') }}
            </a>
            @endif
        </div>
    @endif
    @if(session('error'))
        <div class="alert-flash alert-flash-error mb-4">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert-flash alert-flash-error mb-4">
            <i class="fas fa-exclamation-circle"></i>
            <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        </div>
    @endif

    <form action="{{ route('plots.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- ══ CARD 1: PRICING ══ --}}
    <div class="add-card">
        <div class="add-card-head">
            <div class="add-card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-tags"></i></div>
            <h6>Pricing</h6>
            <small>— status is set to Available by default</small>
        </div>
        <div class="add-card-body">

            {{-- Row 1 --}}
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="plot_category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('plot_category_id') == $cat->id ? 'selected':'' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Price Type</label>
                    <select name="price_type" id="pt_select" class="form-select">
                        <option value="cash"        {{ old('price_type','cash') == 'cash'        ? 'selected':'' }}>Cash</option>
                        <option value="installment" {{ old('price_type')        == 'installment' ? 'selected':'' }}>Instalment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Base Price (PKR)</label>
                    <input type="number" name="base_price" id="bp_input" step="any"
                        class="form-control" placeholder="0" value="{{ old('base_price') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Down Payment (PKR)</label>
                    <input type="number" name="down_payment" id="dp_input" step="any"
                        class="form-control" placeholder="0" value="{{ old('down_payment') }}">
                </div>
            </div>

            {{-- Discount row --}}
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div style="font-size:10px;font-weight:800;color:#15803d;text-transform:uppercase;letter-spacing:.6px;padding:8px 0 2px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-tag"></i> Discount / Special Offer
                        <span style="font-size:10px;font-weight:400;color:var(--muted-text);">— optional, applied before instalment calculation</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Discount Amount (PKR)</label>
                    <input type="number" name="discount_amount" id="disc_input" step="any"
                        class="form-control" placeholder="e.g. 50000" value="{{ old('discount_amount') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Reason / Offer Label</label>
                    <input type="text" name="discount_reason" id="disc_reason"
                        class="form-control" placeholder="e.g. Eid Special, Loyalty Discount, Early Bird"
                        value="{{ old('discount_reason') }}" maxlength="150">
                </div>
            </div>

            {{-- Discount breakdown panel (live) --}}
            <div class="disc-panel" id="disc_panel">
                <div class="disc-cell">
                    <span class="disc-cell-lbl">Original Price</span>
                    <span class="disc-cell-val" id="dp_orig" style="color:#64748b;text-decoration:line-through;font-size:13px;">—</span>
                </div>
                <div class="disc-sep"></div>
                <div class="disc-cell">
                    <span class="disc-cell-lbl">Discount</span>
                    <span class="disc-cell-val" id="dp_disc" style="color:#dc2626;">—</span>
                    <span id="dp_pct" style="font-size:10px;font-weight:700;color:#dc2626;background:#fee2e2;border-radius:10px;padding:1px 7px;margin-top:3px;">0%</span>
                </div>
                <div class="disc-arrow">=</div>
                <div class="disc-cell">
                    <span class="disc-cell-lbl">Final / Effective Price</span>
                    <span class="disc-cell-val" id="dp_final" style="color:#15803d;font-size:20px;">—</span>
                    <span style="font-size:10px;font-weight:600;color:#15803d;margin-top:2px;">This will be the booking price</span>
                </div>
                <div class="disc-sep"></div>
                <div class="disc-cell">
                    <span class="disc-cell-lbl">Customer Saves</span>
                    <span class="disc-cell-val" id="dp_save" style="color:#7c3aed;">—</span>
                </div>
            </div>

            {{-- Instalment rows — shown only when installment selected --}}
            <div id="inst_rows" style="display:none;">

                {{-- Quarterly --}}
                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <div style="font-size:10px;font-weight:800;color:#b45309;text-transform:uppercase;letter-spacing:.6px;padding:8px 0 4px;">
                            <i class="fas fa-calendar-alt me-1"></i> Quarterly Instalments (every 3 months)
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">No. of Quarterly Payments</label>
                        <input type="number" name="quarterly_installments" id="qi_input" step="any"
                            class="form-control" placeholder="e.g. 6" value="{{ old('quarterly_installments') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">
                            Amount / Quarter (PKR)
                            <small class="text-muted fw-normal">— enter manually</small>
                        </label>
                        <input type="number" name="quarterly_amount" id="qa_input" step="any"
                            class="form-control" placeholder="e.g. 275000"
                            value="{{ old('quarterly_amount') }}">
                    </div>
                    <div class="col-md-3" style="display:flex;align-items:flex-end;">
                        <div style="font-size:11px;color:var(--muted-text);padding-bottom:10px;">
                            <i class="fas fa-info-circle me-1"></i>
                            Total quarterly = <strong id="q_total_display">—</strong>
                        </div>
                    </div>
                </div>

                {{-- Monthly --}}
                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <div style="font-size:10px;font-weight:800;color:#1d4ed8;text-transform:uppercase;letter-spacing:.6px;padding:8px 0 4px;">
                            <i class="fas fa-calendar me-1"></i> Monthly Instalments
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">No. of Monthly Payments</label>
                        <input type="number" name="total_installments" id="ti_input" step="any"
                            class="form-control" placeholder="e.g. 18" value="{{ old('total_installments') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">
                            Amount / Month (PKR)
                            <small class="text-muted fw-normal">— auto</small>
                        </label>
                        <input type="number" name="installment_amount" id="ia_input" step="any"
                            class="form-control" placeholder="auto-calculated"
                            value="{{ old('installment_amount') }}" readonly style="background:var(--hover-bg);">
                    </div>
                    <div class="col-md-3" style="display:flex;align-items:flex-end;">
                        <div style="font-size:11px;color:var(--muted-text);padding-bottom:10px;">
                            <i class="fas fa-info-circle me-1"></i>
                            Total monthly = <strong id="m_total_display">—</strong>
                        </div>
                    </div>
                </div>

                {{-- Price breakdown --}}
                <div class="price-breakdown" id="price_breakdown">
                    <div class="pb-item">
                        <span class="pb-lbl">Down Payment</span>
                        <span class="pb-val" id="pb_down">—</span>
                    </div>
                    <div class="pb-sep"></div>
                    <div class="pb-item">
                        <span class="pb-lbl">Quarterly Total</span>
                        <span class="pb-val" id="pb_q">—</span>
                    </div>
                    <div class="pb-sep"></div>
                    <div class="pb-item">
                        <span class="pb-lbl">Monthly Total</span>
                        <span class="pb-val" id="pb_m">—</span>
                    </div>
                    <div class="pb-eq">=</div>
                    <div class="pb-item">
                        <span class="pb-lbl">Sum</span>
                        <span class="pb-val" id="pb_sum">—</span>
                    </div>
                    <div class="pb-eq">/</div>
                    <div class="pb-item">
                        <span class="pb-lbl">Base Price</span>
                        <span class="pb-val" id="pb_base">—</span>
                    </div>
                    <div class="pb-item" style="min-width:80px;">
                        <span class="pb-lbl">Balance</span>
                        <span class="pb-val" id="pb_balance">—</span>
                    </div>
                </div>

                {{-- Balance warnings --}}
                <div class="balance-warn" id="balance_warn">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="balance_warn_text"></span>
                </div>
                <div class="balance-ok" id="balance_ok">
                    <i class="fas fa-check-circle"></i>
                    <span>Pricing is balanced — Down + Quarterly + Monthly = Base Price</span>
                </div>

            </div>{{-- /inst_rows --}}

        </div>
    </div>

  {{-- ══ CARD 2: PLOT DETAILS & LOCATION (merged) ══ --}}
<div class="add-card">
    <div class="add-card-head">
        <div class="add-card-head-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-map-marker-alt"></i></div>
        <h6>Plot Details & Location</h6>
    </div>
    <div class="add-card-body">
        <div class="row g-3">

            {{-- ── Row 1: Block · Street · Plot Number (first as requested) ── --}}
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Block</label>
                <select name="block" class="form-select">
                    <option value="">Select Block</option>
                    @foreach($blocks as $b)
                        <option value="{{ $b->name }}" {{ old('block') == $b->name ? 'selected':'' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Street Number</label>
                <input type="text" name="street_number" class="form-control"
                    placeholder="e.g. Street 5" value="{{ old('street_number') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Plot Number</label>
                <input type="text" name="plot_number" class="form-control"
                    placeholder="e.g. 101" value="{{ old('plot_number') }}">
            </div>

            {{-- ── Row 2: Size · Unit · Street Size ── --}}
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Size</label>
                <input type="number" name="size" step="any" class="form-control"
                    placeholder="e.g. 5" value="{{ old('size') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="">Select</option>
                    <option value="Marla" {{ old('unit') == 'Marla' ? 'selected':'' }}>Marla</option>
                    <option value="Kanal" {{ old('unit') == 'Kanal' ? 'selected':'' }}>Kanal</option>
                    <option value="Sqft"  {{ old('unit') == 'Sqft'  ? 'selected':'' }}>Sqft</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Street Size (ft)</label>
                <select name="street_size" class="form-select">
                    <option value="">Select</option>
                    <option value="20"  {{ old('street_size') == '20'  ? 'selected':'' }}>20 ft</option>
                    <option value="25"  {{ old('street_size') == '25'  ? 'selected':'' }}>25 ft</option>
                    <option value="30"  {{ old('street_size') == '30'  ? 'selected':'' }}>30 ft</option>
                    <option value="35"  {{ old('street_size') == '35'  ? 'selected':'' }}>35 ft</option>
                    <option value="40"  {{ old('street_size') == '40'  ? 'selected':'' }}>40 ft</option>
                    <option value="50"  {{ old('street_size') == '50'  ? 'selected':'' }}>50 ft</option>
                    <option value="60"  {{ old('street_size') == '60'  ? 'selected':'' }}>60 ft</option>
                    <option value="70"  {{ old('street_size') == '70'  ? 'selected':'' }}>70 ft</option>
                </select>
            </div>

            {{-- ── Row 3: City · Society · Sector ── --}}
            <div class="col-md-4">
                <label class="form-label small fw-semibold">City</label>
                <select name="city" class="form-select">

                    @foreach($cities as $c)
                        <option value="{{ $c->name }}" {{ old('city') == $c->name ? 'selected':'' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Society</label>
                <select name="society" class="form-select">

                    @foreach($societies as $s)
                        <option value="{{ $s->name }}" {{ old('society') == $s->name ? 'selected':'' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Sector</label>
                <select name="sector" class="form-select">

                    @foreach($sectors as $sec)
                        <option value="{{ $sec->name }}" {{ old('sector') == $sec->name ? 'selected':'' }}>
                            {{ $sec->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ── Row 4: Primary Feature · Description ── --}}
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Primary Feature</label>
                <select name="property_features" class="form-select">
                    <option value="">None</option>
                    @foreach($features as $f)
                        <option value="{{ $f->name }}" {{ old('property_features') == $f->name ? 'selected':'' }}>
                            {{ $f->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="1"
                    placeholder="Optional notes…">{{ old('description') }}</textarea>
            </div>

        </div>
    </div>
</div>

    {{-- ══ CARD 3: APPLICABLE FEES ══ --}}
    <div class="add-card">
        <div class="add-card-head">
            <div class="add-card-head-icon" style="background:#fff7ed;color:#b45309;"><i class="fas fa-receipt"></i></div>
            <h6>Applicable Fees</h6>
        </div>
        <div class="add-card-body" style="display:flex;flex-direction:column;gap:12px;">

            @php
            $fees = [
                ['key'=>'reg', 'flag'=>'has_registry_fee',    'amt_field'=>'registry_fee_amount',    'icon'=>'fas fa-stamp',      'color'=>'#b45309', 'bg'=>'rgba(180,83,9,.09)',   'border'=>'#fed7aa', 'label'=>'Registry Fee',    'sub'=>'Property registration'],
                ['key'=>'dev', 'flag'=>'has_development_fee', 'amt_field'=>'development_fee_amount', 'icon'=>'fas fa-hard-hat',   'color'=>'#1d4ed8', 'bg'=>'rgba(29,78,216,.09)',  'border'=>'#bfdbfe', 'label'=>'Development Fee', 'sub'=>'Society development'],
                ['key'=>'sec', 'flag'=>'has_security_fee',    'amt_field'=>'security_fee_amount',    'icon'=>'fas fa-shield-alt', 'color'=>'#059669', 'bg'=>'rgba(5,150,105,.09)',  'border'=>'#a7f3d0', 'label'=>'Security Fee',    'sub'=>'Refundable deposit'],
            ];
            @endphp

            @foreach($fees as $f)
            @php
                $k       = $f['key'];
                $isYes   = old($f['flag']) ? true : false;
            @endphp

            {{-- hidden flag input — set by JS --}}
            <input type="hidden" name="{{ $f['flag'] }}" id="flag_{{ $k }}" value="{{ $isYes ? '1' : '0' }}">

            <div style="border:1.5px solid {{ $f['border'] }};border-radius:12px;overflow:hidden;" id="fee_wrap_{{ $k }}">

                {{-- Header row: icon · name · Yes/No --}}
                <div style="display:flex;align-items:center;gap:12px;padding:13px 16px;background:{{ $f['bg'] }};">
                    <div style="width:36px;height:36px;border-radius:9px;background:#fff;border:1px solid {{ $f['border'] }};display:flex;align-items:center;justify-content:center;color:{{ $f['color'] }};font-size:15px;flex-shrink:0;">
                        <i class="{{ $f['icon'] }}"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:700;color:{{ $f['color'] }};">{{ $f['label'] }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ $f['sub'] }}</div>
                    </div>
                    <div style="display:flex;gap:6px;flex-shrink:0;">
                        <button type="button" id="btn_yes_{{ $k }}"
                            onclick="feeToggle('{{ $k }}', true)"
                            style="padding:5px 18px;font-size:12px;font-weight:700;border-radius:8px;border:1.5px solid {{ $f['border'] }};cursor:pointer;transition:.15s;
                                   background:{{ $isYes ? $f['color'] : '#fff' }};color:{{ $isYes ? '#fff' : '#64748b' }};">
                            Yes
                        </button>
                        <button type="button" id="btn_no_{{ $k }}"
                            onclick="feeToggle('{{ $k }}', false)"
                            style="padding:5px 18px;font-size:12px;font-weight:700;border-radius:8px;border:1.5px solid #e2e8f0;cursor:pointer;transition:.15s;
                                   background:{{ !$isYes ? '#64748b' : '#fff' }};color:{{ !$isYes ? '#fff' : '#64748b' }};">
                            No
                        </button>
                    </div>
                </div>

                {{-- Expandable amount field --}}
                <div id="fee_fields_{{ $k }}" style="display:{{ $isYes ? 'block' : 'none' }};padding:14px 16px;border-top:1px solid {{ $f['border'] }};background:#fff;">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Amount (PKR)</label>
                            <input type="number" name="{{ $f['amt_field'] }}" step="any" class="form-control"
                                placeholder="e.g. 50000" value="{{ old($f['amt_field']) }}">
                        </div>
                    </div>
                </div>

            </div>
            @endforeach

        </div>
    </div>

    {{-- ══ CARD 4: IMAGE ══ --}}
    <div class="add-card">
        <div class="add-card-head">
            <div class="add-card-head-icon" style="background:#f3e8ff;color:#7c3aed;"><i class="fas fa-image"></i></div>
            <h6>Plot Image <small class="text-muted fw-normal text-lowercase">(optional)</small></h6>
        </div>
        <div class="add-card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <input type="file" name="plot_image" id="plot_image" class="form-control" accept="image/*">
                </div>
                <div class="col-auto">
                    <div id="img_preview" class="d-none">
                        <img id="img_preview_src" src="" class="rounded border"
                            style="height:64px;width:64px;object-fit:cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="submit-bar">
        <span id="submit_bar_hint" style="font-size:12px;color:var(--muted-text);">
            <i class="fas fa-info-circle me-1"></i> Status defaults to Available
        </span>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('index.plots') }}" class="btn-soft">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" class="btn-navy" id="submit_btn">
                <i class="fas fa-save me-1"></i> Save Plot
            </button>
        </div>
    </div>

    </form>
</div>

<script>
(function() {
    var ptEl   = document.getElementById('pt_select');
    var irEl   = document.getElementById('inst_rows');
    var bpEl   = document.getElementById('bp_input');
    var dpEl   = document.getElementById('dp_input');
    var discEl = document.getElementById('disc_input');
    var qiEl   = document.getElementById('qi_input');
    var qaEl   = document.getElementById('qa_input');
    var tiEl   = document.getElementById('ti_input');
    var iaEl   = document.getElementById('ia_input');

    function fmt(n) {
        return 'PKR ' + Math.round(n).toLocaleString();
    }

    function toggleRow() {
        irEl.style.display = ptEl.value === 'installment' ? 'block' : 'none';
        if (ptEl.value === 'installment') calc();
    }

    function calc() {
        var base     = parseFloat(bpEl.value)   || 0;
        var disc     = parseFloat(discEl.value) || 0;
        var effPrice = Math.max(0, base - disc);   // effective price after discount
        var down     = parseFloat(dpEl.value)   || 0;
        var qi       = parseInt(qiEl.value)     || 0;
        var qa       = parseFloat(qaEl.value)   || 0;
        var ti       = parseInt(tiEl.value)     || 0;

        // Discount breakdown panel
        var panel = document.getElementById('disc_panel');
        if (base > 0 && disc > 0 && disc < base) {
            var pct = ((disc / base) * 100).toFixed(1);
            document.getElementById('dp_orig').textContent = fmt(base);
            document.getElementById('dp_disc').textContent = '-' + fmt(disc);
            document.getElementById('dp_pct').textContent  = '-' + pct + '%';
            document.getElementById('dp_final').textContent = fmt(effPrice);
            document.getElementById('dp_save').textContent  = fmt(disc);
            panel.classList.add('show');
        } else {
            panel.classList.remove('show');
        }

        // Quarterly total
        var qTotal = qi * qa;

        // Remaining for monthly = effective price − down − quarterly
        var afterQ = effPrice - down - qTotal;

        var ia = 0;
        if (ti > 0 && afterQ > 0) {
            ia = Math.round(afterQ / ti);
        }
        iaEl.value = (ti > 0 && effPrice > 0) ? ia : '';

        var mTotal = ia * ti;
        var sum    = down + qTotal + mTotal;
        var bal    = effPrice - sum;   // balance against effective price

        document.getElementById('q_total_display').textContent = qi > 0 ? fmt(qTotal) : '—';
        document.getElementById('m_total_display').textContent = ti > 0 ? fmt(mTotal) : '—';

        var showBreakdown = effPrice > 0 && (down > 0 || qi > 0 || ti > 0);
        var bd = document.getElementById('price_breakdown');
        bd.classList.toggle('show', showBreakdown);

        if (showBreakdown) {
            document.getElementById('pb_down').textContent  = fmt(down);
            document.getElementById('pb_q').textContent     = fmt(qTotal);
            document.getElementById('pb_m').textContent     = fmt(mTotal);
            document.getElementById('pb_sum').textContent   = fmt(sum);
            // Show effective price (after discount) as the reference
            document.getElementById('pb_base').textContent  = disc > 0 ? fmt(effPrice)+' (after discount)' : fmt(base);
            var balEl = document.getElementById('pb_balance');
            balEl.textContent = fmt(Math.abs(bal));
            balEl.className   = 'pb-val ' + (Math.abs(bal) < 1 ? 'pb-total-ok' : 'pb-total-err');
        }

        var warnEl = document.getElementById('balance_warn');
        var okEl   = document.getElementById('balance_ok');
        var warnTx = document.getElementById('balance_warn_text');

        if (effPrice > 0 && (qi > 0 || ti > 0)) {
            if (Math.abs(bal) < 1) {
                warnEl.classList.remove('show');
                okEl.classList.add('show');
            } else if (bal > 0) {
                okEl.classList.remove('show');
                warnTx.textContent = 'PKR ' + Math.round(bal).toLocaleString() + ' still unallocated — adjust counts or check base price.';
                warnEl.classList.add('show');
            } else {
                okEl.classList.remove('show');
                warnTx.textContent = 'Over-allocated by PKR ' + Math.round(Math.abs(bal)).toLocaleString() + ' — reduce counts or lower instalment amounts.';
                warnEl.classList.add('show');
            }
        } else {
            warnEl.classList.remove('show');
            okEl.classList.remove('show');
        }
    }

    ptEl.addEventListener('change', toggleRow);
    bpEl.addEventListener('input',  calc);
    discEl.addEventListener('input', calc);
    dpEl.addEventListener('input',  calc);
    qiEl.addEventListener('input',  calc);
    qaEl.addEventListener('input',  calc);
    tiEl.addEventListener('input',  calc);

    toggleRow();

    document.querySelectorAll('input[type=number]').forEach(function(el) {
        el.addEventListener('wheel', function(e) { e.preventDefault(); }, { passive: false });
    });

    document.getElementById('plot_image').addEventListener('change', function() {
        var f = this.files[0];
        if (!f) return;
        var r = new FileReader();
        r.onload = function(e) {
            document.getElementById('img_preview_src').src = e.target.result;
            document.getElementById('img_preview').classList.remove('d-none');
        };
        r.readAsDataURL(f);
    });
})();

// Fee Yes/No toggle
function feeToggle(key, isYes) {
    var colors  = { reg: '#b45309', dev: '#1d4ed8', sec: '#059669' };
    var borders = { reg: '#fed7aa', dev: '#bfdbfe', sec: '#a7f3d0' };
    var color   = colors[key]  || '#64748b';
    var border  = borders[key] || '#e2e8f0';

    document.getElementById('flag_' + key).value = isYes ? '1' : '0';
    document.getElementById('fee_fields_' + key).style.display = isYes ? 'block' : 'none';

    var btnYes = document.getElementById('btn_yes_' + key);
    var btnNo  = document.getElementById('btn_no_'  + key);

    btnYes.style.background  = isYes ? color    : '#fff';
    btnYes.style.color       = isYes ? '#fff'   : '#64748b';
    btnYes.style.borderColor = isYes ? color    : border;

    btnNo.style.background  = !isYes ? '#64748b' : '#fff';
    btnNo.style.color       = !isYes ? '#fff'    : '#64748b';
    btnNo.style.borderColor = '#e2e8f0';
}

</script>

@if(session('new_plot_id'))
{{-- ── BOOKING PROMPT MODAL ── --}}
<div id="bookingPromptModal" style="
    display:flex;position:fixed;inset:0;z-index:9999;
    align-items:center;justify-content:center;
    background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);">
    <div style="
        background:var(--card-bg,#fff);border:1px solid var(--border-color,#e2e8f0);
        border-radius:16px;padding:32px 28px;width:100%;max-width:420px;
        box-shadow:0 20px 60px rgba(0,0,0,0.25);text-align:center;">

        <div style="width:56px;height:56px;background:linear-gradient(135deg,#16a34a,#22c55e);
            border-radius:14px;display:flex;align-items:center;justify-content:center;
            margin:0 auto 16px;font-size:24px;">
            ✅
        </div>

        <h5 style="font-size:16px;font-weight:800;color:var(--text-main,#0f172a);margin:0 0 6px;">
            Plot #{{ session('new_plot_number') }} Added!
        </h5>
        <p style="font-size:13px;color:var(--muted-text,#64748b);margin:0 0 24px;line-height:1.6;">
            Do you want to create a booking for this plot now?
        </p>

        <div style="display:flex;gap:10px;justify-content:center;">
            <a href="{{ route('booking.create', session('new_plot_id')) }}"
               style="flex:1;background:linear-gradient(135deg,#1e3a8a,#3b82f6);color:#fff;
                      border:none;border-radius:10px;padding:11px 20px;font-size:13px;
                      font-weight:700;text-decoration:none;display:block;text-align:center;">
                <i class="fas fa-calendar-plus me-1"></i> Yes, Create Booking
            </a>
            <button onclick="document.getElementById('bookingPromptModal').style.display='none';"
               style="flex:1;background:var(--hover-bg,#f1f5f9);color:var(--text-main,#0f172a);
                      border:1px solid var(--border-color,#e2e8f0);border-radius:10px;
                      padding:11px 20px;font-size:13px;font-weight:700;cursor:pointer;">
                No, Add Another Plot
            </button>
        </div>
    </div>
</div>
@endif

@endsection
