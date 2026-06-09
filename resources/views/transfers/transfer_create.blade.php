@extends('layouts.index')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
/* ── Select2 skin to match the form ── */
.select2-container--default .select2-selection--single {
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    height: 42px;
    padding: 6px 10px;
    font-size: 13px;
    background: #fff;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #0f172a;
    line-height: 28px;
    padding-left: 0;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 8px;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30,58,138,.1);
    outline: none;
}
.select2-dropdown {
    border: 1.5px solid #1e3a8a;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.1);
    font-size: 13px;
    overflow: hidden;
}
.select2-search--dropdown .select2-search__field {
    border: 1.5px solid #e2e8f0;
    border-radius: 7px;
    padding: 7px 10px;
    font-size: 12px;
    width: 100%;
}
.select2-results__option--highlighted {
    background: #1e3a8a !important;
}
.select2-container { width: 100% !important; }
</style>
@endpush

@section('content')

<style>
/* ── Wrapper ── */
.tc-wrap { max-width: 900px; margin: 0 auto; padding: 0 16px 60px; }

/* ── Booking banner ── */
.booking-banner {
    background: linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#1d4ed8 100%);
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
}
.booking-banner::before {
    content:'';position:absolute;top:-40px;right:-20px;
    width:180px;height:180px;border-radius:50%;
    background:rgba(255,255,255,.04);
}
.bb-avatar {
    width: 50px; height: 50px; border-radius: 12px;
    background: rgba(255,255,255,.15); color: #fff;
    font-size: 20px; font-weight: 800;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.bb-name  { font-size: 15px; font-weight: 800; color: #fff; margin: 0 0 3px; }
.bb-meta  { font-size: 11px; color: rgba(255,255,255,.65); display: flex; gap: 8px; flex-wrap: wrap; }
.bb-meta span + span::before { content:'·'; margin-right:5px; opacity:.5; }
.bb-stats { display: flex; gap: 20px; flex-wrap: wrap; margin-left: auto; }
.bb-stat  { text-align: right; }
.bb-stat-label { font-size: 9px; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .5px; }
.bb-stat-value { font-size: 14px; font-weight: 800; color: #fff; }
.bb-stat-value.red   { color: #fca5a5; }
.bb-stat-value.green { color: #86efac; }

/* ── Progress bar inside banner ── */
.bb-progress {
    width: 100%;
    height: 4px;
    background: rgba(255,255,255,.15);
    border-radius: 4px;
    margin-top: 12px;
    overflow: hidden;
}
.bb-progress-fill {
    height: 100%;
    background: linear-gradient(90deg,#4ade80,#22c55e);
    border-radius: 4px;
    transition: width .4s;
}

/* ── Main card ── */
.tr-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 12px;
}

/* ── Transfer type tabs ── */
.type-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-bottom: 1px solid #e2e8f0;
}
.type-tab input { display: none; }
.type-tab-box {
    padding: 18px 22px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border-right: 1px solid #e2e8f0;
    transition: background .12s;
}
.type-tab:last-child .type-tab-box { border-right: none; }
.type-tab-box:hover { background: #f1f5f9; }
.type-tab input:checked + .type-tab-box {
    background: #fff;
    border-bottom: 2.5px solid #1d4ed8;
    margin-bottom: -1px;
}
.tab-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
    background: #e2e8f0;
}
.type-tab input:checked + .type-tab-box .tab-icon-own  { background: #eff6ff; }
.type-tab input:checked + .type-tab-box .tab-icon-swap { background: #fdf4ff; }
.tab-title { font-size: 13px; font-weight: 800; color: #0f172a; }
.tab-desc  { font-size: 11px; color: #94a3b8; margin-top: 1px; }
.type-tab input:checked + .type-tab-box .tab-title { color: #1d4ed8; }

/* ── Empty state ── */
.no-type {
    padding: 44px 28px;
    text-align: center;
    color: #94a3b8;
    font-size: 13px;
}

/* ── Detail panels ── */
.detail-panel { display: none; }
.detail-panel.visible { display: block; }

/* ── Section divider inside card ── */
.card-section {
    padding: 24px 28px;
    border-top: 1px solid #f1f5f9;
}
.card-section:first-child { border-top: none; }

/* ── Section heading inside card ── */
.csec-title {
    font-size: 11px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.csec-title::after { content:''; flex:1; height:1px; background:#f1f5f9; }

/* ── Info strip ── */
.info-strip {
    border-radius: 9px;
    padding: 11px 15px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.55;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.info-strip svg { flex-shrink: 0; margin-top: 1px; }
.is-blue   { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
.is-purple { background: #fdf4ff; color: #6b21a8; border: 1px solid #e9d5ff; }

/* ── Form fields ── */
.fc-row { margin-bottom: 16px; }
.fc-row:last-child { margin-bottom: 0; }
.fc-label {
    font-size: 12px; font-weight: 700; color: #374151;
    margin-bottom: 5px; display: block;
}
.fc-label span { color: #dc2626; }
.fc-label small { color: #94a3b8; font-weight: 400; margin-left: 4px; }
.fc-input, .fc-select, .fc-textarea {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    padding: 10px 13px;
    font-size: 13px;
    color: #0f172a;
    background: #fff;
    outline: none;
    font-family: inherit;
    transition: border-color .15s, box-shadow .15s;
}
.fc-input:focus, .fc-select:focus, .fc-textarea:focus {
    border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29,78,216,.07);
}
.fc-textarea { resize: vertical; min-height: 64px; }
.fc-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.fc-3col { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
.fc-err  { font-size: 11px; color: #dc2626; margin-top: 3px; }

/* ── Witness card ── */
.witness-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px;
}
.witness-card-head {
    font-size: 11px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.wnum {
    width: 20px; height: 20px; border-radius: 6px;
    background: #1d4ed8; color: white;
    font-size: 10px; font-weight: 800;
    display: inline-flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* ── Submit bar ── */
.submit-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 18px 28px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    flex-wrap: wrap;
}
.btn-create {
    background: #1d4ed8;
    color: #fff; border: none; border-radius: 9px;
    padding: 11px 22px; font-size: 13px; font-weight: 800;
    cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
    transition: background .15s, transform .1s; font-family: inherit;
}
.btn-create:hover { background: #1e40af; transform: translateY(-1px); }
.btn-cancel {
    background: #fff; color: #64748b; border: 1px solid #e2e8f0;
    border-radius: 9px; padding: 10px 18px; font-size: 13px;
    font-weight: 700; cursor: pointer; text-decoration: none;
    transition: border-color .15s; display: inline-block;
}
.btn-cancel:hover { border-color: #94a3b8; color: #374151; }
.submit-note {
    font-size: 11px; color: #94a3b8;
    display: flex; align-items: center; gap: 5px; margin-left: auto;
}
.deed-tag {
    font-size: 11px; color: #94a3b8;
    background: #f1f5f9; border-radius: 6px;
    padding: 4px 10px;
    font-family: monospace;
}
</style>

<div class="tc-wrap">

    {{-- ── Back link ── --}}
    <div style="margin-bottom:16px;">
        <a href="{{ route('transfers.search') }}"
           style="font-size:12px;font-weight:700;color:#64748b;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Change booking
        </a>
    </div>

    {{-- ── Title ── --}}
    <div style="margin-bottom:20px;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0 0 3px;">Create Transfer</h1>
            <p style="font-size:13px;color:#64748b;margin:0;">
                Select a transfer type, fill the form, and add witnesses to generate the legal deed.
            </p>
        </div>
        {{-- Transfer counter --}}
        <div style="display:flex;align-items:center;gap:8px;background:{{ $transfersRemaining > 0 ? '#f0fdf4' : '#fef2f2' }};border:1.5px solid {{ $transfersRemaining > 0 ? '#bbf7d0' : '#fecaca' }};border-radius:10px;padding:8px 14px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="{{ $transfersRemaining > 0 ? '#15803d' : '#dc2626' }}" style="width:15px;height:15px;flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
            </svg>
            <span style="font-size:12px;font-weight:700;color:{{ $transfersRemaining > 0 ? '#15803d' : '#dc2626' }};">
                {{ $transferCount }}/5 transfers used
                @if($transfersRemaining > 0)
                    · {{ $transfersRemaining }} remaining
                @else
                    · <strong>Limit reached</strong>
                @endif
            </span>
        </div>
    </div>

    {{-- ── Transfer limit hard block ── --}}
    @if($transfersRemaining <= 0)
    <div class="alert-flash alert-flash-error mb-4" style="flex-direction:column;align-items:center;text-align:center;padding:20px 24px;">
        <i class="fas fa-ban fa-2x mb-2"></i>
        <div style="font-size:14px;font-weight:800;margin-bottom:4px;">Transfer Limit Reached</div>
        <div style="font-size:12px;opacity:.85;">This plot has already been transferred 5 times. No further transfers are allowed.</div>
    </div>
    @endif

    {{-- ── Flash: success ── --}}
    @if(session('success'))
    <div class="alert-flash alert-flash-success mb-4">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- ── Flash: error ── --}}
    @if(session('error'))
    <div class="alert-flash alert-flash-error mb-4">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- ── Validation errors ── --}}
    @if($errors->any())
    <div class="alert-flash alert-flash-error mb-4" style="flex-direction:column;align-items:flex-start;">
        <div style="display:flex;align-items:center;gap:8px;font-weight:700;margin-bottom:6px;">
            <i class="fas fa-exclamation-triangle"></i> Please fix the following:
        </div>
        <ul style="margin:0;padding-left:20px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ── Booking Banner ── --}}
    @php
        $pct = $fromBooking->total_price > 0
            ? min(round((($totalPaid + ($totalDisc ?? 0)) / $fromBooking->total_price) * 100), 100) : 0;
    @endphp
    <div class="booking-banner">
        <div class="bb-avatar">{{ strtoupper(substr($fromBooking->customer->name, 0, 1)) }}</div>
        <div style="flex:1;min-width:0;">
            <p class="bb-name">{{ $fromBooking->customer->name }}</p>
            <div class="bb-meta">
                <span>{{ $fromBooking->customer->cnic }}</span>
                <span>{{ $fromBooking->customer_booking_id }}</span>
                <span>Plot #{{ $fromBooking->plot->plot_number ?? '—' }} · Block {{ $fromBooking->plot->block ?? '—' }}</span>
                <span>{{ $fromBooking->plot->size ?? '' }} {{ $fromBooking->plot->unit ?? '' }}</span>
                <span>Booked: {{ $fromBooking->booking_date ? \Carbon\Carbon::parse($fromBooking->booking_date)->format('d M Y') : '—' }}</span>
            </div>
        </div>
        <div class="bb-stats">
            <div class="bb-stat">
                <div class="bb-stat-label">Total Price</div>
                <div class="bb-stat-value">PKR {{ number_format($fromBooking->total_price) }}</div>
            </div>
            <div class="bb-stat">
                <div class="bb-stat-label">Paid</div>
                <div class="bb-stat-value green">PKR {{ number_format($totalPaid) }}</div>
            </div>
            <div class="bb-stat">
                <div class="bb-stat-label">Remaining</div>
                <div class="bb-stat-value {{ $remainingBalance > 0 ? 'red' : 'green' }}">
                    PKR {{ number_format($remainingBalance) }}
                </div>
            </div>
            <div class="bb-stat">
                <div class="bb-stat-label">Monthly Inst.</div>
                <div class="bb-stat-value">{{ $paidInstCount }}/{{ $fromBooking->total_installments ?? 0 }}</div>
            </div>
            @if(($fromBooking->quarterly_installments ?? 0) > 0)
            <div class="bb-stat">
                <div class="bb-stat-label">Quarterly Inst.</div>
                <div class="bb-stat-value" style="color:#b45309;">{{ $paidQtrCount }}/{{ $fromBooking->quarterly_installments }}</div>
            </div>
            @endif
            <div class="bb-stat">
                <div class="bb-stat-label">Progress</div>
                <div class="bb-stat-value">{{ $pct }}%</div>
            </div>
        </div>
        <div class="bb-progress">
            <div class="bb-progress-fill" style="width:{{ $pct }}%;"></div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         FORM
    ══════════════════════════════════════ --}}
    @if($transfersRemaining > 0)
    <form action="{{ route('transfers.store') }}" method="POST" id="transferForm">
        @csrf
        <input type="hidden" name="from_booking_id" value="{{ $fromBooking->id }}">

        {{-- ── CARD 1: Type + Type-specific fields ── --}}
        <div class="tr-card">

            {{-- Type tabs --}}
            <div class="type-tabs">
                <label class="type-tab">
                    <input type="radio" name="transfer_type" value="ownership" class="type-radio"
                           {{ old('transfer_type') === 'ownership' ? 'checked' : '' }}>
                    <div class="type-tab-box">
                        <div class="tab-icon tab-icon-own">🤝</div>
                        <div>
                            <div class="tab-title">Ownership Transfer</div>
                            <div class="tab-desc">Move full ownership to a new customer</div>
                        </div>
                    </div>
                </label>
                <label class="type-tab">
                    <input type="radio" name="transfer_type" value="swap" class="type-radio"
                           {{ old('transfer_type') === 'swap' ? 'checked' : '' }}>
                    <div class="type-tab-box">
                        <div class="tab-icon tab-icon-swap">🔄</div>
                        <div>
                            <div class="tab-title">Plot Swap</div>
                            <div class="tab-desc">Exchange plots between two completed bookings</div>
                        </div>
                    </div>
                </label>
            </div>

            {{-- No type selected placeholder --}}
            <div id="no-type" class="no-type">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#cbd5e1"
                     style="width:34px;height:34px;margin:0 auto 8px;display:block;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5"/>
                </svg>
                Select a transfer type above to continue
            </div>

            {{-- ─────── OWNERSHIP PANEL ─────── --}}
            <div id="panel-ownership"
                 class="detail-panel {{ old('transfer_type') === 'ownership' ? 'visible' : '' }}">

                <div class="card-section">
                    <div class="csec-title">Transfer Details</div>

                    @php
                        $qtrAmountPerQ = $fromBooking->quarterly_amount ?? 0;
                        $totalInstCount = $fromBooking->total_installments ?? 0;
                        $monthlyAmt = $fromBooking->monthly_installment ?? 0;
                    @endphp
                    <div class="info-strip is-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" style="width:15px;height:15px;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            <span>
                                Remaining balance of <strong>PKR {{ number_format($remainingBalance) }}</strong> will transfer to the new owner. Original booking will be marked <em>Transferred</em>.
                            </span>
                            @if($remainingInst > 0 || $remainingQtr > 0)
                            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:2px;">
                                @if($remainingInst > 0)
                                <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;">
                                    Monthly: {{ $paidInstCount }}/{{ $totalInstCount }} paid &rarr; <strong>{{ $remainingInst }} remaining</strong>
                                </span>
                                @endif
                                @if($remainingQtr > 0)
                                <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;">
                                    Quarterly: {{ $paidQtrCount }}/{{ $fromBooking->quarterly_installments ?? 0 }} paid &rarr;
                                    <strong>{{ $remainingQtr }} remaining @ PKR {{ number_format($qtrAmountPerQ) }}/qtr</strong>
                                </span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="fc-row" style="margin-bottom:16px;">
                        <label class="fc-label">Transfer Fee (PKR) <span style="color:#ef4444;">*</span></label>
                      <input type="number" name="transfer_fee" class="fc-input"
       placeholder="e.g. 25000" min="1" step="1" required
       value="{{ old('transfer_fee') }}">
                        <div style="font-size:10.5px;color:#94a3b8;margin-top:3px;">Amount the buyer will pay via Fee Management · Pre-filled from system settings</div>
                        @error('transfer_fee')<div class="fc-err">{{ $message }}</div>@enderror
                    </div>

                  <div class="fc-row" style="margin-bottom: 5px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <label class="fc-label" style="margin-bottom:0;">New Owner <span>*</span></label>
        <button type="button" onclick="openCustomerModal()"
                style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
            <i class="fas fa-plus-circle"></i> Quick Register
        </button>
    </div>
    <select name="to_customer_id" id="to_customer_id" class="fc-select">
        <option value="">— Select the new owner —</option>
        @foreach($customers as $c)
             @if($c->id !== $fromBooking->customer_id)
                <option value="{{ $c->id }}" {{ old('to_customer_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->name }} — {{ $c->cnic }}
                </option>
             @endif
        @endforeach
    </select>
</div>



                    <div class="fc-2col">
                        <div class="fc-row">
                            <label class="fc-label">Reason <small>(optional)</small></label>
                            <input type="text" name="reason" class="fc-input"
                                   placeholder="e.g. Family transfer, customer request"
                                   value="{{ old('reason') }}">
                        </div>
                        <div class="fc-row">
                            <label class="fc-label">Notes <small>(optional)</small></label>
                            <input type="text" name="notes" class="fc-input"
                                   placeholder="Any additional notes"
                                   value="{{ old('notes') }}">
                        </div>
                    </div>

                </div>{{-- /card-section --}}
            </div>{{-- /panel-ownership --}}

            {{-- ─────── SWAP PANEL ─────── --}}
            <div id="panel-swap"
                 class="detail-panel {{ old('transfer_type') === 'swap' ? 'visible' : '' }}">

                <div class="card-section">
                    <div class="csec-title">Swap Details</div>

                    <div class="info-strip is-purple">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" style="width:15px;height:15px;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                        <span>
                            Both bookings must be <strong>fully paid (completed)</strong>.
                            Plots are exchanged between customers — no balance is transferred.
                        </span>
                    </div>

                    @if($fromBooking->status !== 'completed')
                    <div style="background:#fff1f2;border:1px solid #fecaca;border-radius:9px;padding:12px 16px;margin-bottom:18px;font-size:12px;color:#dc2626;font-weight:700;display:flex;align-items:center;gap:8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                        </svg>
                        This booking is not fully paid. Swap requires both bookings to be completed.
                    </div>
                    @endif

                    <div class="fc-row">
                        <label class="fc-label">Swap With <span>*</span></label>
                        @if($swapBookings->isEmpty())
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;padding:18px;text-align:center;color:#94a3b8;font-size:13px;">
                                No completed bookings available for swapping.
                            </div>
                        @else
                        <select name="swap_from_booking_id" class="fc-select">
                            <option value="">— Choose the other completed booking —</option>
                            @foreach($swapBookings as $sb)
                            <option value="{{ $sb->id }}" {{ old('swap_from_booking_id') == $sb->id ? 'selected' : '' }}>
                                {{ $sb->customer->name }} — Plot #{{ $sb->plot->plot_number ?? '?' }} {{ $sb->plot->block ?? '' }} ({{ $sb->customer_booking_id }})
                            </option>
                            @endforeach
                        </select>
                        @endif
                        @error('swap_from_booking_id')<div class="fc-err">{{ $message }}</div>@enderror
                    </div>

                    <div class="fc-2col">
                        <div class="fc-row">
                            <label class="fc-label">Reason <small>(optional)</small></label>
                            <input type="text" name="reason" class="fc-input"
                                   placeholder="e.g. Mutual agreement"
                                   value="{{ old('reason') }}">
                        </div>
                        <div class="fc-row">
                            <label class="fc-label">Notes <small>(optional)</small></label>
                            <input type="text" name="notes" class="fc-input"
                                   placeholder="Any additional notes"
                                   value="{{ old('notes') }}">
                        </div>
                    </div>

                </div>{{-- /card-section --}}
            </div>{{-- /panel-swap --}}

        </div>{{-- /tr-card (type card) --}}

        {{-- ── CARD 2: Transfer Date (shared) ──────────────────────── --}}
        <div class="tr-card" id="witnessCard" style="{{ old('transfer_type') ? '' : 'display:none;' }}">
            <div class="card-section">
                <div class="csec-title">
                    <span style="background:#1d4ed8;color:white;width:18px;height:18px;border-radius:5px;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;flex-shrink:0;">D</span>
                    Transfer Date
                </div>
                <div class="fc-row" style="max-width:320px;">
                    <label class="fc-label">Date of Transfer <span>*</span></label>
                    <input type="date" name="transfer_date" id="sharedTransferDate" class="fc-input"
                           value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                    @error('transfer_date')<div class="fc-err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="card-section">
                <div class="csec-title">
                    <span style="background:#1d4ed8;color:white;width:18px;height:18px;border-radius:5px;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;flex-shrink:0;">W</span>
                    Witnesses
                    <small style="font-size:10px;font-weight:400;color:#94a3b8;text-transform:none;letter-spacing:0;">Required for the legal deed &amp; application form</small>
                </div>

                <div class="fc-2col">

                    {{-- Witness 1 --}}
                    <div class="witness-card">
                        <div class="witness-card-head">
                            <span class="wnum">1</span>
                            Witness No. 1
                        </div>
                        <div class="fc-row">
                            <label class="fc-label">Full Name <span>*</span></label>
                            <input type="text" name="witness1_name" class="fc-input"
                                   placeholder="e.g. Muhammad Ahmad"
                                   value="{{ old('witness1_name') }}">
                            @error('witness1_name')<div class="fc-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="fc-row">
                            <label class="fc-label">CNIC No. <span>*</span></label>
                            <input type="text" name="witness1_cnic" class="fc-input"
                                   placeholder="35202-xxxxxxx-x"
                                   value="{{ old('witness1_cnic') }}">
                            @error('witness1_cnic')<div class="fc-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="fc-row">
                            <label class="fc-label">Address <small>(optional)</small></label>
                            <input type="text" name="witness1_address" class="fc-input"
                                   placeholder="Residential address"
                                   value="{{ old('witness1_address') }}">
                        </div>
                    </div>

                    {{-- Witness 2 --}}
                    <div class="witness-card">
                        <div class="witness-card-head">
                            <span class="wnum">2</span>
                            Witness No. 2
                        </div>
                        <div class="fc-row">
                            <label class="fc-label">Full Name <span>*</span></label>
                            <input type="text" name="witness2_name" class="fc-input"
                                   placeholder="e.g. Khalid Mehmood"
                                   value="{{ old('witness2_name') }}">
                            @error('witness2_name')<div class="fc-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="fc-row">
                            <label class="fc-label">CNIC No. <span>*</span></label>
                            <input type="text" name="witness2_cnic" class="fc-input"
                                   placeholder="35202-xxxxxxx-x"
                                   value="{{ old('witness2_cnic') }}">
                            @error('witness2_cnic')<div class="fc-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="fc-row">
                            <label class="fc-label">Address <small>(optional)</small></label>
                            <input type="text" name="witness2_address" class="fc-input"
                                   placeholder="Residential address"
                                   value="{{ old('witness2_address') }}">
                        </div>
                    </div>

                </div>{{-- /fc-2col --}}
            </div>
        </div>{{-- /witness card --}}

        {{-- ── SUBMIT BAR ── --}}
        {{-- ── Confirm Modal ── --}}
        <div id="confirmModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);align-items:center;justify-content:center;">
            <div style="background:#fff;border-radius:14px;padding:28px 24px;width:100%;max-width:400px;box-shadow:0 20px 50px rgba(0,0,0,0.25);text-align:center;">
                <div style="font-size:28px;margin-bottom:12px;">⚠️</div>
                <h5 style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 8px;" id="confirmTitle">Confirm Transfer</h5>
                <p style="font-size:13px;color:#64748b;margin:0 0 22px;line-height:1.6;">This will update booking statuses immediately. Are you sure you want to proceed?</p>
                <div style="display:flex;gap:10px;justify-content:center;">
                    <button type="button" id="confirmYes" style="flex:1;background:linear-gradient(135deg,#1e3a8a,#3b82f6);color:#fff;border:none;border-radius:9px;padding:11px 20px;font-size:13px;font-weight:700;cursor:pointer;">
                        Yes, Create Transfer
                    </button>
                    <button type="button" id="confirmNo" style="flex:1;background:#f1f5f9;color:#0f172a;border:1px solid #e2e8f0;border-radius:9px;padding:11px 20px;font-size:13px;font-weight:700;cursor:pointer;">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <div class="tr-card" id="submitCard" style="{{ old('transfer_type') ? '' : 'display:none;' }}">
            <div class="submit-bar">
                <button type="button" class="btn-create" id="submitBtn" onclick="showTransferConfirm()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2.5" stroke="currentColor" style="width:14px;height:14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    <span id="submitLabel">Create Transfer</span>
                </button>
                <a href="{{ route('transfers.search') }}" class="btn-cancel">Cancel</a>
                <div class="submit-note">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" style="width:12px;height:12px;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                    </svg>
                    Transfer fee set above — buyer pays in Fee Management
                </div>
                <span class="deed-tag">{{ $deedNo }}</span>
            </div>
        </div>

    </form>
    @endif
</div>
<div id="customerModal" style="display:none; position: fixed; z-index: 10001; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(2px); overflow-y: auto;">
    <div style="background: #fff; margin: 2% auto; width: 750px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; position: relative;">

        <div style="background: #f8fafc; padding: 15px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; position: sticky; top: 0; z-index: 10;">
            <h3 style="margin:0; font-size: 18px; color: #1e293b;">Quick Customer Registration</h3>
            <span onclick="closeCustomerModal()" style="cursor:pointer; color: #94a3b8; font-size: 24px; line-height: 1;">&times;</span>
        </div>

        <form id="quickCustomerForm" enctype="multipart/form-data" style="padding: 25px;">
            @csrf
            <div id="modalErrors" style="display:none; background: #fef2f2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 13px;"></div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">

                <div style="grid-column: span 2; background: #eff6ff; color: #1e40af; padding: 8px 12px; font-weight: bold; border-radius: 4px; font-size: 14px;">
                    <i class="fas fa-user"></i> Owner Information
                </div>

                <div>
                    <label class="fc-label">Full Name <span>*</span></label>
                    <input type="text" name="name" class="fc-input" placeholder="Full Name" required>
                </div>
                <div>
                    <label class="fc-label">Guardian Name</label>
                    <input type="text" name="guardian_name" class="fc-input" placeholder="Father/Husband Name">
                </div>
                <div>
                    <label class="fc-label">CNIC <span>*</span></label>
                    <input type="text" name="cnic" id="modal_cnic_input" class="fc-input" placeholder="xxxxx-xxxxxxx-x" required>
                </div>
                <div>
                    <label class="fc-label">Nationality</label>
                    <input type="text" name="nationality" class="fc-input" placeholder="e.g. Pakistani">
                </div>
                <div>
                    <label class="fc-label">Occupation</label>
                    <input type="text" name="occupation" class="fc-input" placeholder="Job Title">
                </div>
                <div>
                    <label class="fc-label">Age</label>
                    <input type="number" name="age" class="fc-input" placeholder="Years">
                </div>

                <div>
                    <label class="fc-label">Mobile </label>
                    <input type="text" name="phone" class="fc-input" placeholder="03xx-xxxxxxx" required>
                </div>
                <div>
                    <label class="fc-label">Email Address</label>
                    <input type="email" name="email" class="fc-input" placeholder="email@example.com">
                </div>
                <div>
                    <label class="fc-label">Phone (Office)</label>
                    <input type="text" name="phone_off" class="fc-input">
                </div>
                <div>
                    <label class="fc-label">Phone (Res)</label>
                    <input type="text" name="phone_res" class="fc-input">
                </div>

                <div style="grid-column: span 2;">
                    <label class="fc-label">Residential Address</label>
                    <textarea name="residential_address" class="fc-input" rows="2"></textarea>
                </div>
                <div style="grid-column: span 2;">
                    <label class="fc-label">Postal Address</label>
                    <textarea name="postal_address" class="fc-input" rows="2"></textarea>
                </div>

                <div>
                    <label class="fc-label" style="font-size: 11px;">Customer Photo</label>
                    <input type="file" name="customer_pic" class="fc-input" style="padding: 4px;">
                </div>
                <div>
                    <label class="fc-label" style="font-size: 11px;">CNIC Copy (Front)</label>
                    <input type="file" name="cnic_pic" class="fc-input" style="padding: 4px;">
                </div>

                <div style="grid-column: span 2; background: #f0fdf4; color: #166534; padding: 8px 12px; font-weight: bold; border-radius: 4px; font-size: 14px; margin-top: 10px;">
                    <i class="fas fa-users"></i> Nominee Details
                </div>

                <div>
                    <label class="fc-label">Nominee Name</label>
                    <input type="text" name="nominee_name" class="fc-input">
                </div>
                <div>
                    <label class="fc-label">Relation</label>
                    <input type="text" name="nominee_relation" class="fc-input">
                </div>
                <div>
                    <label class="fc-label">Nominee CNIC</label>
                    <input type="text" name="nominee_cnic" class="fc-input">
                </div>
                <div>
                    <label class="fc-label">Nominee Address</label>
                    <input type="text" name="nominee_address" class="fc-input">
                </div>

                <div style="grid-column: span 2; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                    <div>
                        <label class="fc-label" style="font-size: 11px;">Nominee Photo</label>
                        <input type="file" name="nominee_pic" class="fc-input" style="padding: 4px;">
                    </div>
                    <div>
                        <label class="fc-label" style="font-size: 11px;">Nominee CNIC Front</label>
                        <input type="file" name="nominee_cnic_front" class="fc-input" style="padding: 4px;">
                    </div>
                    <div>
                        <label class="fc-label" style="font-size: 11px;">Nominee CNIC Back</label>
                        <input type="file" name="nominee_cnic_back" class="fc-input" style="padding: 4px;">
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="button" onclick="closeCustomerModal()" style="padding: 10px 20px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; cursor: pointer;">Cancel</button>
                <button type="submit" id="saveCustomerBtn" style="padding: 10px 25px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Complete Registration</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ── Initialize Select2 on the customer dropdown ────────────────
$(document).ready(function () {
    $('#to_customer_id').select2({
        placeholder: '— Search by name or CNIC —',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#to_customer_id').closest('.fc-row'),
    });
});

const submitLabels = {
    ownership: 'Create Ownership Transfer',
    swap:      'Create Plot Swap',
};

function onTypeChange() {
    const radios   = document.querySelectorAll('.type-radio');
    let selected   = '';
    radios.forEach(r => { if (r.checked) selected = r.value; });

    // Show/hide panels
    document.querySelectorAll('.detail-panel').forEach(p => p.classList.remove('visible'));
    document.getElementById('no-type').style.display = 'none';

    if (selected) {
        const panel = document.getElementById('panel-' + selected);
        if (panel) panel.classList.add('visible');
        document.getElementById('witnessCard').style.display = 'block';
        document.getElementById('submitCard').style.display  = 'block';
        document.getElementById('submitLabel').textContent   = submitLabels[selected] || 'Create Transfer';
    } else {
        document.getElementById('no-type').style.display    = 'block';
        document.getElementById('witnessCard').style.display = 'none';
        document.getElementById('submitCard').style.display  = 'none';
    }
}

// Custom confirm modal
function showTransferConfirm() {
    const type = document.querySelector('.type-radio:checked');
    if (!type) {
        alert('Please select a transfer type first.');
        return;
    }
    const labels = { ownership: 'Ownership Transfer', swap: 'Plot Swap', partial: 'Partial Transfer', internal: 'Internal Relocation' };
    document.getElementById('confirmTitle').textContent = 'Confirm ' + (labels[type.value] || 'Transfer');
    const modal = document.getElementById('confirmModal');
    modal.style.display = 'flex';
}
document.getElementById('confirmNo').addEventListener('click', function() {
    document.getElementById('confirmModal').style.display = 'none';
});
document.getElementById('confirmYes').addEventListener('click', function() {
    document.getElementById('confirmModal').style.display = 'none';
    document.getElementById('transferForm').submit();
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.type-radio').forEach(r => r.addEventListener('change', onTypeChange));
    const checked = document.querySelector('.type-radio:checked');
    if (checked) onTypeChange();
    else document.getElementById('no-type').style.display = 'block';
});
function openCustomerModal() {
    document.getElementById('customerModal').style.display = 'block';
    document.body.style.overflow = 'hidden'; // Stop scrolling
}

function closeCustomerModal() {
    document.getElementById('customerModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('quickCustomerForm').reset();
    document.getElementById('modalErrors').style.display = 'none';
}

document.getElementById('quickCustomerForm').onsubmit = function(e) {
    e.preventDefault();

    const btn = document.getElementById('saveCustomerBtn');
    const errDiv = document.getElementById('modalErrors');

    // 1. Visual feedback
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    errDiv.style.display = 'none';

    // 2. Prepare Data
    let formData = new FormData(this);

    // 3. Fetch Request
    fetch("{{ route('customers.quick-register') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            // CRITICAL: You must include the CSRF token for Laravel
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw data;
        return data;
    })
    .then(data => {
        if(data.success) {
            // Add the new customer as a selected option in Select2
            const newOption = new Option(
                `${data.customer.name} — ${data.customer.cnic}`,
                data.customer.id,
                true,
                true
            );
            $('#to_customer_id').append(newOption).trigger('change');

            alert(data.message);
            closeCustomerModal();
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerText = "Register & Select";
        errDiv.style.display = 'block';

        if (err.errors) {
            let errorMsg = '<ul style="margin:0; padding-left:15px;">';
            Object.values(err.errors).forEach(e => {
                errorMsg += `<li>${Array.isArray(e) ? e[0] : e}</li>`;
            });
            errorMsg += '</ul>';
            errDiv.innerHTML = errorMsg;
        } else {
            errDiv.innerText = err.message || "An unexpected error occurred.";
        }
    });
};
</script>
@endpush

@endsection
