@extends('layouts.index')
@push('styles')
<style>
.add-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;overflow:hidden;margin-bottom:18px;}
.add-card-head{padding:13px 20px;background:var(--hover-bg);border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.add-card-head-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.add-card-head h6{font-size:11px;font-weight:800;color:var(--text-main);text-transform:uppercase;letter-spacing:.7px;margin:0;}
.add-card-body{padding:22px;}
.submit-bar{position:sticky;bottom:16px;z-index:10;background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;box-shadow:0 -2px 16px rgba(30,58,138,.08);}
input[type=number]{-moz-appearance:textfield;}
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
#inst_row{display:none;}
.disc-panel { display:none; background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1.5px solid #86efac; border-radius:12px; padding:16px 20px; margin-top:12px; }
.disc-panel.show { display:flex; flex-wrap:wrap; gap:12px; align-items:center; }
.disc-cell { display:flex; flex-direction:column; align-items:center; min-width:100px; }
.disc-cell-lbl { font-size:9px; font-weight:800; color:#166534; text-transform:uppercase; letter-spacing:.5px; }
.disc-cell-val { font-size:15px; font-weight:800; margin-top:3px; }
.disc-sep { width:1px; height:40px; background:#86efac; flex-shrink:0; }
.disc-arrow { font-size:18px; color:#4ade80; font-weight:800; }
</style>
@endpush

@section('content')
<div class="ldg-wrap">

    <div class="rpt-header no-print">
        <div>
            <p class="rpt-header-title">Edit Plot #{{ $plot->plot_number }}</p>
            <p class="rpt-header-sub">
                <a href="{{ route('index.plots') }}" style="color:rgba(255,255,255,.6);text-decoration:none;">
                    <i class="fas fa-arrow-left me-1"></i> Back to All Plots
                </a>
                &nbsp;·&nbsp; System Ref: #{{ $plot->id }}
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-flash alert-flash-success mb-4">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
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

    {{-- Status Banner --}}
    @if($priceLocked)
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:12px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
        <i class="fas fa-lock" style="color:#b45309;font-size:15px;"></i>
        <div>
            <strong style="color:#92400e;font-size:12px;">Pricing Locked</strong>
            <div style="color:#78350f;font-size:11.5px;margin-top:2px;">
                This plot is <strong>{{ ucfirst($plot->status) }}</strong> and payments have been recorded.
                Price fields are read-only to protect payment history.
                You can still edit plot details, location, description, and image.
            </div>
        </div>
    </div>
    @elseif(in_array($plot->status, ['booked', 'sold']))
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
        <i class="fas fa-info-circle" style="color:#1d4ed8;font-size:15px;"></i>
        <div>
            <strong style="color:#1e3a8a;font-size:12px;">Plot is {{ ucfirst($plot->status) }}</strong>
            <div style="color:#1e40af;font-size:11.5px;margin-top:2px;">
                No payments yet — all fields including pricing are editable.
                @if($activeBooking) Linked to Booking #{{ $activeBooking->id }}.@endif
            </div>
        </div>
    </div>
    @elseif($plot->status === 'available')
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
        <i class="fas fa-check-circle" style="color:#15803d;font-size:15px;"></i>
        <div style="color:#14532d;font-size:11.5px;">
            <strong>Available</strong> — All fields are freely editable.
        </div>
    </div>
    @endif

    <form action="{{ route('plots.update', $plot->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- ══ CARD 1: PRICING ══ --}}
    <div class="add-card">
        <div class="add-card-head">
            <div class="add-card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-tags"></i></div>
            <h6>Pricing
                @if($priceLocked)
                    <span style="font-size:10px;font-weight:600;color:#b45309;background:#fef3c7;border-radius:4px;padding:2px 7px;margin-left:8px;">
                        <i class="fas fa-lock me-1"></i>Locked
                    </span>
                @endif
            </h6>
        </div>
        <div class="add-card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="plot_category_id" class="form-select" {{ $priceLocked ? 'disabled' : '' }}>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('plot_category_id', $plot->plot_category_id) == $cat->id ? 'selected':'' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @if($priceLocked) <input type="hidden" name="plot_category_id" value="{{ $plot->plot_category_id }}"> @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Price Type</label>
                    <select name="price_type" id="pt_select" class="form-select" {{ $priceLocked ? 'disabled' : '' }}>
                        <option value="cash"        {{ old('price_type', $plot->price_type) == 'cash'        ? 'selected':'' }}>Cash</option>
                        <option value="installment" {{ old('price_type', $plot->price_type) == 'installment' ? 'selected':'' }}>Instalment</option>
                    </select>
                    @if($priceLocked) <input type="hidden" name="price_type" value="{{ $plot->price_type }}"> @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Base Price (PKR)
                        @if($priceLocked) <i class="fas fa-lock text-warning ms-1" style="font-size:9px;"></i> @endif
                    </label>
                    <input type="number" name="base_price" id="bp_input" step="any" class="form-control"
                        placeholder="0" value="{{ old('base_price', $plot->base_price) }}"
                        {{ $priceLocked ? 'readonly' : '' }}
                        style="{{ $priceLocked ? 'background:#f8f4e8;cursor:not-allowed;' : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Down Payment (PKR)
                        @if($priceLocked) <i class="fas fa-lock text-warning ms-1" style="font-size:9px;"></i> @endif
                    </label>
                    <input type="number" name="down_payment" id="dp_input" step="any" class="form-control"
                        placeholder="0" value="{{ old('down_payment', $plot->down_payment) }}"
                        {{ $priceLocked ? 'readonly' : '' }}
                        style="{{ $priceLocked ? 'background:#f8f4e8;cursor:not-allowed;' : '' }}">
                </div>
            </div>

            {{-- Discount row — always editable (offers can be applied even on booked/sold plots) --}}
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div style="font-size:10px;font-weight:800;color:#15803d;text-transform:uppercase;letter-spacing:.6px;padding:8px 0 2px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-tag"></i> Discount / Special Offer
                        <span style="font-size:10px;font-weight:400;color:var(--muted-text);">— optional, applies to effective/final price (always editable)</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Discount Amount (PKR)</label>
                    <input type="number" name="discount_amount" id="disc_input" step="any"
                        class="form-control" placeholder="e.g. 50000"
                        value="{{ old('discount_amount', $plot->discount_amount) }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Reason / Offer Label</label>
                    <input type="text" name="discount_reason" id="disc_reason"
                        class="form-control" placeholder="e.g. Eid Special, Loyalty Discount"
                        value="{{ old('discount_reason', $plot->discount_reason) }}" maxlength="150">
                </div>
            </div>

            {{-- Discount breakdown panel --}}
            @php $initDisc = (float)($plot->discount_amount ?? 0); $initBase = (float)($plot->base_price ?? 0); @endphp
            <div class="disc-panel {{ $initDisc > 0 && $initBase > 0 ? 'show' : '' }}" id="disc_panel">
                <div class="disc-cell">
                    <span class="disc-cell-lbl">Original Price</span>
                    <span class="disc-cell-val" id="dp_orig" style="color:#64748b;text-decoration:line-through;font-size:13px;">
                        {{ $initBase > 0 ? 'PKR '.number_format($initBase) : '—' }}
                    </span>
                </div>
                <div class="disc-sep"></div>
                <div class="disc-cell">
                    <span class="disc-cell-lbl">Discount</span>
                    <span class="disc-cell-val" id="dp_disc" style="color:#dc2626;">
                        {{ $initDisc > 0 ? '-PKR '.number_format($initDisc) : '—' }}
                    </span>
                    <span id="dp_pct" style="font-size:10px;font-weight:700;color:#dc2626;background:#fee2e2;border-radius:10px;padding:1px 7px;margin-top:3px;">
                        {{ ($initBase > 0 && $initDisc > 0) ? '-'.round($initDisc/$initBase*100,1).'%' : '0%' }}
                    </span>
                </div>
                <div class="disc-arrow">=</div>
                <div class="disc-cell">
                    <span class="disc-cell-lbl">Final / Effective Price</span>
                    <span class="disc-cell-val" id="dp_final" style="color:#15803d;font-size:20px;">
                        {{ ($initBase > 0 && $initDisc > 0) ? 'PKR '.number_format(max(0,$initBase-$initDisc)) : '—' }}
                    </span>
                    <span style="font-size:10px;font-weight:600;color:#15803d;margin-top:2px;">This will be the booking price</span>
                </div>
                <div class="disc-sep"></div>
                <div class="disc-cell">
                    <span class="disc-cell-lbl">Customer Saves</span>
                    <span class="disc-cell-val" id="dp_save" style="color:#7c3aed;">
                        {{ $initDisc > 0 ? 'PKR '.number_format($initDisc) : '—' }}
                    </span>
                </div>
            </div>

            {{-- instalment row --}}
            <div class="row g-3 mt-1" id="inst_row">
                <div class="col-md-3">
        <label class="form-label small fw-semibold">Quarterly Installments
            @if($priceLocked) <i class="fas fa-lock text-warning ms-1" style="font-size:9px;"></i> @endif
        </label>
        <input type="number" name="quarterly_installments" id="qi_input"
            class="form-control"
            value="{{ old('quarterly_installments', $plot->quarterly_installments) }}"
            {{ $priceLocked ? 'readonly' : '' }}
            style="{{ $priceLocked ? 'background:#f8f4e8;cursor:not-allowed;' : '' }}">
    </div>

    <div class="col-md-3">
        <label class="form-label small fw-semibold">Quarterly Amount (PKR)
            @if($priceLocked) <i class="fas fa-lock text-warning ms-1" style="font-size:9px;"></i> @endif
        </label>
        <input type="number" name="quarterly_amount" id="qa_input"
            class="form-control"
            value="{{ old('quarterly_amount', $plot->quarterly_amount) }}"
            {{ $priceLocked ? 'readonly' : '' }}
            style="{{ $priceLocked ? 'background:#f8f4e8;cursor:not-allowed;' : '' }}">
    </div>

    {{-- Monthly --}}
    <div class="col-md-3">
        <label class="form-label small fw-semibold">Total Monthly Installments
            @if($priceLocked) <i class="fas fa-lock text-warning ms-1" style="font-size:9px;"></i> @endif
        </label>
        <input type="number" name="total_installments" id="ti_input"
            class="form-control"
            value="{{ old('total_installments', $plot->total_installments) }}"
            {{ $priceLocked ? 'readonly' : '' }}
            style="{{ $priceLocked ? 'background:#f8f4e8;cursor:not-allowed;' : '' }}">
    </div>

    <div class="col-md-3">
        <label class="form-label small fw-semibold">
            Monthly Installment (Auto)
        </label>
        <input type="number" name="installment_amount" id="ia_input"
            class="form-control"
            value="{{ old('installment_amount', $plot->installment_amount) }}"
            readonly style="background:#f1f5f9;">
    </div>

        </div>
    </div>

    {{-- ══ CARD 2: FEE SETTINGS ══ --}}
    <div class="add-card">
        <div class="add-card-head">
            <div class="add-card-head-icon" style="background:#fdf4ff;color:#7c3aed;"><i class="fas fa-file-invoice-dollar"></i></div>
            <h6>Fee Settings</h6>
        </div>
        <div class="add-card-body">
            <div class="row g-4">

                {{-- Registry Fee --}}
                <div class="col-md-4">
                    <div style="background:var(--hover-bg);border:1px solid var(--border-color);border-radius:10px;padding:16px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:28px;height:28px;border-radius:7px;background:#eff6ff;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:12px;">
                                    <i class="fas fa-file-signature"></i>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:var(--text-main);">Registry Fee</span>
                            </div>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;margin:0;">
                                <input type="checkbox" name="has_registry_fee" id="chk_registry"
                                    value="1" {{ old('has_registry_fee', $plot->has_registry_fee) ? 'checked' : '' }}
                                    onchange="toggleFeeAmount('registry')"
                                    style="width:16px;height:16px;cursor:pointer;">
                                <span style="font-size:11px;color:var(--muted-text);">Enable</span>
                            </label>
                        </div>
                        <div id="amt_registry" style="{{ old('has_registry_fee', $plot->has_registry_fee) ? '' : 'display:none;' }}">
                            <label class="form-label small fw-semibold" style="font-size:11px;">Amount (PKR)</label>
                            <input type="number" name="registry_fee_amount" step="any" class="form-control"
                                placeholder="e.g. 50000"
                                value="{{ old('registry_fee_amount', $plot->registry_fee_amount) }}">
                        </div>
                        <div id="no_registry" style="{{ old('has_registry_fee', $plot->has_registry_fee) ? 'display:none;' : '' }}">
                            <span style="font-size:11px;color:var(--muted-text);">Not applicable for this plot</span>
                        </div>
                    </div>
                </div>

                {{-- Development Fee --}}
                <div class="col-md-4">
                    <div style="background:var(--hover-bg);border:1px solid var(--border-color);border-radius:10px;padding:16px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:28px;height:28px;border-radius:7px;background:#f0fdf4;color:#15803d;display:flex;align-items:center;justify-content:center;font-size:12px;">
                                    <i class="fas fa-hard-hat"></i>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:var(--text-main);">Development Fee</span>
                            </div>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;margin:0;">
                                <input type="checkbox" name="has_development_fee" id="chk_development"
                                    value="1" {{ old('has_development_fee', $plot->has_development_fee) ? 'checked' : '' }}
                                    onchange="toggleFeeAmount('development')"
                                    style="width:16px;height:16px;cursor:pointer;">
                                <span style="font-size:11px;color:var(--muted-text);">Enable</span>
                            </label>
                        </div>
                        <div id="amt_development" style="{{ old('has_development_fee', $plot->has_development_fee) ? '' : 'display:none;' }}">
                            <label class="form-label small fw-semibold" style="font-size:11px;">Amount (PKR)</label>
                            <input type="number" name="development_fee_amount" step="any" class="form-control"
                                placeholder="e.g. 30000"
                                value="{{ old('development_fee_amount', $plot->development_fee_amount) }}">
                        </div>
                        <div id="no_development" style="{{ old('has_development_fee', $plot->has_development_fee) ? 'display:none;' : '' }}">
                            <span style="font-size:11px;color:var(--muted-text);">Not applicable for this plot</span>
                        </div>
                    </div>
                </div>

                {{-- Security Fee --}}
                <div class="col-md-4">
                    <div style="background:var(--hover-bg);border:1px solid var(--border-color);border-radius:10px;padding:16px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:28px;height:28px;border-radius:7px;background:#fef3c7;color:#b45309;display:flex;align-items:center;justify-content:center;font-size:12px;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:var(--text-main);">Security Fee</span>
                            </div>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;margin:0;">
                                <input type="checkbox" name="has_security_fee" id="chk_security"
                                    value="1" {{ old('has_security_fee', $plot->has_security_fee) ? 'checked' : '' }}
                                    onchange="toggleFeeAmount('security')"
                                    style="width:16px;height:16px;cursor:pointer;">
                                <span style="font-size:11px;color:var(--muted-text);">Enable</span>
                            </label>
                        </div>
                        <div id="amt_security" style="{{ old('has_security_fee', $plot->has_security_fee) ? '' : 'display:none;' }}">
                            <label class="form-label small fw-semibold" style="font-size:11px;">Monthly Rate (PKR)</label>
                            <input type="number" name="security_fee_amount" step="any" class="form-control"
                                placeholder="e.g. 1500"
                                value="{{ old('security_fee_amount', $plot->security_fee_amount) }}">
                            <small style="color:var(--muted-text);font-size:10.5px;margin-top:4px;display:block;">
                                <i class="fas fa-sync-alt me-1"></i> Recurring monthly charge
                            </small>
                        </div>
                        <div id="no_security" style="{{ old('has_security_fee', $plot->has_security_fee) ? 'display:none;' : '' }}">
                            <span style="font-size:11px;color:var(--muted-text);">Not applicable for this plot</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══ CARD 3: PLOT DETAILS ══ --}}
    <div class="add-card">
        <div class="add-card-head">
            <div class="add-card-head-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-map-marker-alt"></i></div>
            <h6>Plot Details</h6>
        </div>
        <div class="add-card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Plot Number</label>
                    <input type="text" name="plot_number" class="form-control"
                        value="{{ old('plot_number', $plot->plot_number) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Size</label>
                    <input type="number" name="size" step="any" class="form-control"
                        value="{{ old('size', $plot->size) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Unit</label>
                    <select name="unit" class="form-select">
                        <option value="">Select</option>
                        <option value="Marla" {{ old('unit', $plot->unit) == 'Marla' ? 'selected':'' }}>Marla</option>
                        <option value="Kanal" {{ old('unit', $plot->unit) == 'Kanal' ? 'selected':'' }}>Kanal</option>
                        <option value="Sqft"  {{ old('unit', $plot->unit) == 'Sqft'  ? 'selected':'' }}>Sqft</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="available" {{ old('status', $plot->status) == 'available' ? 'selected':'' }}>Available</option>
                        <option value="booked"    {{ old('status', $plot->status) == 'booked'    ? 'selected':'' }}>Booked</option>
                        <option value="sold"      {{ old('status', $plot->status) == 'sold'      ? 'selected':'' }}>Sold</option>
                        <option value="reserved"  {{ old('status', $plot->status) == 'reserved'  ? 'selected':'' }}>Reserved</option>
                        <option value="on_hold"   {{ old('status', $plot->status) == 'on_hold'   ? 'selected':'' }}>On Hold</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Primary Feature</label>
                    <select name="property_features" class="form-select">
                        <option value="">None</option>
                        @foreach($features as $f)
                            <option value="{{ $f->name }}"
                                {{ old('property_features', $plot->property_features) == $f->name ? 'selected':'' }}>
                                {{ $f->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="1"
                        placeholder="Optional notes…">{{ old('description', $plot->description) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ CARD 3: LOCATION ══ --}}
    <div class="add-card">
        <div class="add-card-head">
            <div class="add-card-head-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-map"></i></div>
            <h6>Location</h6>
        </div>
        <div class="add-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Block</label>
                    <select name="block" class="form-select">
                        <option value="">Select Block</option>
                        @foreach($blocks as $b)
                            <option value="{{ $b->name }}"
                                {{ old('block', $plot->block) == $b->name ? 'selected':'' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Street Number</label>
                    <input type="text" name="street_number" class="form-control"
                        placeholder="e.g. Street 5"
                        value="{{ old('street_number', $plot->street_number) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Street Size (ft)</label>
                    <select name="street_size" class="form-select">
                        <option value="">Select</option>
                        @foreach([20,25,30,35,40,50,60,70] as $ss)
                            <option value="{{ $ss }}"
                                {{ old('street_size', $plot->street_size) == $ss ? 'selected':'' }}>
                                {{ $ss }} ft
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">City</label>
                    <select name="city" class="form-select">
                        <option value="">Select City</option>
                        @foreach($cities as $c)
                            <option value="{{ $c->name }}"
                                {{ old('city', $plot->city) == $c->name ? 'selected':'' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Society</label>
                    <select name="society" class="form-select">
                        <option value="">Select Society</option>
                        @foreach($societies as $s)
                            <option value="{{ $s->name }}"
                                {{ old('society', $plot->society) == $s->name ? 'selected':'' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Sector</label>
                    <select name="sector" class="form-select">
                        <option value="">Select Sector</option>
                        @foreach($sectors as $sec)
                            <option value="{{ $sec->name }}"
                                {{ old('sector', $plot->sector) == $sec->name ? 'selected':'' }}>
                                {{ $sec->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
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
                {{-- show existing image --}}
                @if($plot->plot_image)
                <div class="col-auto">
                    <img src="{{ asset($plot->plot_image) }}" class="rounded border"
                        style="height:64px;width:64px;object-fit:cover;">
                    <div style="font-size:10px;color:var(--muted-text);margin-top:4px;">Current</div>
                </div>
                @endif
                <div class="col-md-5">
                    <input type="file" name="plot_image" id="plot_image"
                        class="form-control" accept="image/*">
                    <small class="text-muted">JPG or PNG — leave blank to keep current</small>
                </div>
                <div class="col-auto">
                    <div id="img_preview" class="d-none">
                        <img id="img_preview_src" src="" class="rounded border"
                            style="height:64px;width:64px;object-fit:cover;">
                        <div style="font-size:10px;color:var(--muted-text);margin-top:4px;">New</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="submit-bar">
        <span style="font-size:12px;color:var(--muted-text);">
            @if($priceLocked)
                <i class="fas fa-lock me-1" style="color:#b45309;"></i>
                <span style="color:#b45309;">Pricing locked — non-price fields only</span>
            @else
                <i class="fas fa-info-circle me-1"></i> All fields optional — save only what changed
            @endif
        </span>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('index.plots') }}" class="btn-soft">
                <i class="fas fa-times me-1"></i> Discard
            </a>
            <button type="submit" class="btn-navy">
                <i class="fas fa-save me-1"></i> Save Changes
            </button>
        </div>
    </div>

    </form>
</div>

<script>
function toggleFeeAmount(type) {
    var checked = document.getElementById('chk_' + type).checked;
    document.getElementById('amt_' + type).style.display = checked ? '' : 'none';
    document.getElementById('no_' + type).style.display  = checked ? 'none' : '';
}

(function() {
    var ptEl   = document.getElementById('pt_select');
    var irEl   = document.getElementById('inst_row');
    var bpEl   = document.getElementById('bp_input');
    var dpEl   = document.getElementById('dp_input');
    var discEl = document.getElementById('disc_input');
    var qiEl   = document.getElementById('qi_input');
    var qaEl   = document.getElementById('qa_input');
    var tiEl   = document.getElementById('ti_input');
    var iaEl   = document.getElementById('ia_input');

    function fmt(n) { return 'PKR ' + Math.round(n).toLocaleString(); }

    function toggleRow() {
        irEl.style.display = ptEl.value === 'installment' ? 'flex' : 'none';
    }

    function calc() {
        var base     = parseFloat(bpEl.value)   || 0;
        var disc     = parseFloat(discEl.value) || 0;
        var effPrice = Math.max(0, base - disc);
        var down     = parseFloat(dpEl.value)   || 0;
        var qi       = parseInt(qiEl.value)     || 0;
        var qa       = parseFloat(qaEl.value)   || 0;
        var ti       = parseInt(tiEl.value)     || 0;

        // Discount breakdown panel
        var panel = document.getElementById('disc_panel');
        if (base > 0 && disc > 0 && disc < base) {
            var pct = ((disc / base) * 100).toFixed(1);
            document.getElementById('dp_orig').textContent  = 'PKR ' + Math.round(base).toLocaleString();
            document.getElementById('dp_disc').textContent  = '-PKR ' + Math.round(disc).toLocaleString();
            document.getElementById('dp_pct').textContent   = '-' + pct + '%';
            document.getElementById('dp_final').textContent = 'PKR ' + Math.round(effPrice).toLocaleString();
            document.getElementById('dp_save').textContent  = 'PKR ' + Math.round(disc).toLocaleString();
            panel.classList.add('show');
        } else {
            panel.classList.remove('show');
        }

        if (!base && !down && !qi && !qa && !ti) {
            iaEl.value = '';
            return;
        }

        var qTotal    = qi * qa;
        var remaining = effPrice - down - qTotal;

        if (remaining <= 0 || ti <= 0) {
            iaEl.value = '';
            return;
        }

        iaEl.value = Math.round(remaining / ti);
    }

    ptEl.addEventListener('change', function() { toggleRow(); calc(); });

    [bpEl, dpEl, discEl, qiEl, qaEl, tiEl].forEach(function(el) {
        if (el) {
            el.addEventListener('input', calc);
            el.addEventListener('keyup', calc);
        }
    });

    // Prevent scroll-wheel accidentally changing numbers
    document.querySelectorAll('input[type=number]').forEach(function(el) {
        el.addEventListener('wheel', function(e) { e.preventDefault(); }, { passive: false });
    });

    toggleRow();
    calc();
})();
</script>

@endsection
