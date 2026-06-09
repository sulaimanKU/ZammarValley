@extends('layouts.index')
@push('styles')
<style>
/* ── Stats ── */
.bk-stat-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:14px;margin-bottom:22px;}
@media(max-width:1100px){.bk-stat-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:580px) {.bk-stat-grid{grid-template-columns:1fr 1fr;}}
.bk-stat{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px;transition:box-shadow .15s;}
.bk-stat:hover{box-shadow:0 4px 14px rgba(0,0,0,.07);}
.bk-stat-icon{width:40px;height:40px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:15px;}
.bk-stat-label{font-size:10px;font-weight:700;color:var(--muted-text);text-transform:uppercase;letter-spacing:.5px;}
.bk-stat-val{font-size:18px;font-weight:800;color:var(--text-main);line-height:1.2;}

/* ── Filter bar ── */
.bk-filter-wrap{background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;padding:14px 18px;margin-bottom:18px;}
.bk-filter-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.bk-search-input-wrap{flex:1;min-width:220px;position:relative;}
.bk-search-input-wrap i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted-text);font-size:13px;pointer-events:none;}
.bk-search-input-wrap input{width:100%;border:1.5px solid var(--border-color);border-radius:10px;padding:9px 14px 9px 38px;font-size:13px;background:var(--input-bg);color:var(--text-main);outline:none;font-family:'Poppins',sans-serif;transition:border-color .15s;}
.bk-search-input-wrap input:focus{border-color:#1e3a8a;}
.bk-status-select{border:1.5px solid var(--border-color);border-radius:10px;padding:9px 14px;font-size:12px;background:var(--input-bg);color:var(--text-main);outline:none;font-family:'Poppins',sans-serif;min-width:140px;}
/* fee filter chips */
.bk-fee-chips{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;padding-top:10px;border-top:1px solid var(--border-color);}
.fee-chip{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;border:1.5px solid;text-decoration:none;transition:all .15s;}
.fee-chip-reg{background:#fff7ed;color:#b45309;border-color:#fde68a;}.fee-chip-reg:hover,.fee-chip-reg.active{background:#fde68a;color:#92400e;}
.fee-chip-dev{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}.fee-chip-dev:hover,.fee-chip-dev.active{background:#bfdbfe;color:#1e40af;}
.fee-chip-sec{background:#f0fdf4;color:#15803d;border-color:#bbf7d0;}.fee-chip-sec:hover,.fee-chip-sec.active{background:#bbf7d0;color:#166534;}

/* ── Table ── */
.bk-table-wrap{background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;overflow:hidden;}
.bk-table{width:100%;border-collapse:collapse;}
.bk-table thead th{font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--muted-text);font-weight:700;background:var(--table-head-bg);border-bottom:2px solid var(--border-color);padding:11px 14px;white-space:nowrap;}
.bk-table tbody td{padding:12px 14px;border-bottom:1px solid var(--table-border);font-size:12.5px;vertical-align:middle;color:var(--text-main);}
.bk-table tbody tr:last-child td{border-bottom:none;}
.bk-table tbody tr:hover td{background:var(--table-row-hover);}
.bk-table tbody tr.row-on-hold td{background:#fffbeb !important;}
.bk-table tbody tr.row-on-hold:hover td{background:#fef3c7 !important;}

/* ── Status pills ── */
.sp{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap;}
.sp-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0;}
.sp-pending{background:#fef9c3;color:#854d0e;}.sp-pending .sp-dot{background:#ca8a04;}
.sp-active{background:#dcfce7;color:#15803d;}.sp-active .sp-dot{background:#16a34a;}
.sp-completed{background:#eff6ff;color:#1d4ed8;}.sp-completed .sp-dot{background:#3b82f6;}
.sp-cancelled{background:#fee2e2;color:#dc2626;}.sp-cancelled .sp-dot{background:#dc2626;}
.sp-transferred{background:#fdf4ff;color:#7c3aed;}.sp-transferred .sp-dot{background:#7c3aed;}
.sp-partial-transferred{background:#fff7ed;color:#ea580c;}.sp-partial-transferred .sp-dot{background:#ea580c;}
.sp-pending-transfer{background:#fef9c3;color:#854d0e;}.sp-pending-transfer .sp-dot{background:#ca8a04;}
.sp-swapped{background:#f0fdf4;color:#0f766e;}.sp-swapped .sp-dot{background:#0d9488;}
.sp-plot-relocated{background:#eff6ff;color:#0369a1;}.sp-plot-relocated .sp-dot{background:#0284c7;}

/* ── Hold badges ── */
.hold-badge{display:inline-flex;align-items:center;gap:4px;background:#fef3c7;border:1px solid #fbbf24;color:#92400e;border-radius:6px;padding:2px 8px;font-size:10px;font-weight:800;margin-top:3px;cursor:pointer;transition:background .15s;}
.hold-badge:hover{background:#fde68a;}
.hold-reason{font-size:10px;color:#92400e;margin-top:2px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:pointer;}

/* ── Action buttons ── */
.ab{width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:12px;text-decoration:none;cursor:pointer;border:1px solid transparent;transition:all .15s;flex-shrink:0;}
.ab-view  {background:#f1f5f9;color:#475569;border-color:#e2e8f0;}.ab-view:hover{background:#e2e8f0;color:#1e293b;}
.ab-ledger{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}.ab-ledger:hover{background:#bfdbfe;color:#1e40af;}
.ab-edit  {background:#fff7ed;color:#ea580c;border-color:#fed7aa;}.ab-edit:hover{background:#fed7aa;}
.ab-pdf   {background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}.ab-pdf:hover{background:#bfdbfe;}
.ab-form  {background:#f0fdf4;color:#15803d;border-color:#bbf7d0;}.ab-form:hover{background:#bbf7d0;}
.ab-agree {background:#fdf4ff;color:#7c3aed;border-color:#e9d5ff;}.ab-agree:hover{background:#e9d5ff;}
.ab-possess{background:#1e3a8a;color:#fff;border-color:#1e3a8a;}.ab-possess:hover{background:#1e40af;}
.ab-del   {background:#fef2f2;color:#dc2626;border-color:#fecaca;}.ab-del:hover{background:#fecaca;}
.ab-hold  {background:#fef3c7;color:#92400e;border-color:#fbbf24;}.ab-hold:hover{background:#fbbf24;}
.ab-unhold{background:#dcfce7;color:#15803d;border-color:#86efac;}.ab-unhold:hover{background:#86efac;}
.ab-docs  {background:#f8fafc;color:#334155;border-color:#cbd5e1;}.ab-docs:hover{background:#e2e8f0;color:#1e293b;}
/* button group layout */
.ab-group{display:flex;align-items:center;gap:3px;justify-content:flex-end;}
.ab-sep{width:1px;height:20px;background:var(--border-color);margin:0 2px;flex-shrink:0;}
.ab-dd-wrap{position:relative;}
/* documents dropdown */
.ab-docs-menu{position:fixed;background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:6px;min-width:192px;box-shadow:0 8px 28px rgba(0,0,0,.13);z-index:9999;display:none;}
.ab-docs-menu.open{display:block;}
.ab-docs-item,.ab-docs-btn{display:flex;align-items:center;gap:9px;width:100%;padding:8px 10px;border-radius:8px;font-size:12px;font-weight:600;color:var(--text-main);text-decoration:none;white-space:nowrap;transition:background .12s;background:transparent;border:none;cursor:pointer;font-family:inherit;text-align:left;box-sizing:border-box;}
.ab-docs-item:hover,.ab-docs-btn:hover{background:var(--hover-bg);color:var(--text-main);}
.ab-docs-item i,.ab-docs-btn i{width:15px;text-align:center;font-size:13px;flex-shrink:0;}
.ab-docs-item.muted{color:#94a3b8;cursor:default;}.ab-docs-item.muted:hover{background:transparent;}
.ab-docs-sep{height:1px;background:var(--border-color);margin:4px 6px;}
.ab-docs-lbl{font-size:9px;font-weight:800;color:var(--muted-text);text-transform:uppercase;letter-spacing:.6px;padding:5px 10px 2px;}
/* fee chip paid style */
.fee-chip-paid{background:#f0fdf4;color:#166534;border-color:#86efac;}.fee-chip-paid:hover,.fee-chip-paid.active{background:#86efac;color:#14532d;}
.bk-fee-group{display:flex;align-items:center;gap:5px;}
.bk-fee-group-lbl{font-size:10px;font-weight:800;color:var(--muted-text);white-space:nowrap;}

/* ── Possession status ── */
.possess-ready  {display:inline-flex;align-items:center;gap:5px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:8px;padding:4px 10px;font-size:10px;font-weight:700;}
.possess-blocked{display:inline-flex;align-items:center;gap:5px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:8px;padding:4px 10px;font-size:10px;font-weight:700;cursor:help;}

/* ── Foot / pagination ── */
.bk-foot{padding:14px 20px;border-top:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.bk-foot-info{font-size:12px;color:var(--muted-text);}

/* ── Shared modal backdrop ── */
.bk-modal-bd{position:fixed;inset:0;background:rgba(0,0,0,.46);z-index:9998;display:none;align-items:center;justify-content:center;padding:20px;}
.bk-modal-bd.show{display:flex;}
.bk-modal{background:#fff;border-radius:16px;padding:28px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.22);position:relative;}

/* Hold input modal */
.hold-modal-title{font-size:16px;font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:8px;}
.hold-modal-sub{font-size:12px;color:#64748b;margin-bottom:18px;}
.hm-label{font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;}
.hm-textarea{width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 13px;font-size:13px;font-family:inherit;resize:vertical;min-height:80px;outline:none;box-sizing:border-box;}
.hm-textarea:focus{border-color:#f59e0b;box-shadow:0 0 0 3px rgba(245,158,11,.1);}
.hm-hint{font-size:11px;color:#94a3b8;margin-top:6px;}
.btn-hold-confirm{background:#f59e0b;color:#fff;border:none;border-radius:9px;padding:10px 22px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px;}
.btn-hold-confirm:hover{background:#d97706;}
.btn-modal-cancel{background:#f1f5f9;color:#64748b;border:none;border-radius:9px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;}
.btn-modal-cancel:hover{background:#e2e8f0;}

/* Hold detail modal specifics */
.hd-remarks-box{background:#fffbeb;border:1.5px solid #fbbf24;border-radius:10px;padding:14px 16px;font-size:13px;font-weight:600;color:#92400e;line-height:1.65;min-height:54px;word-break:break-word;}
.hd-meta-tile{background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;padding:10px 14px;flex:1;min-width:140px;}
.hd-meta-lbl{font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:3px;}
.hd-meta-val{font-size:12px;font-weight:700;color:#0f172a;}
</style>
@endpush

@section('content')
<div class="ldg-wrap">

    {{-- ══ HEADER ══════════════════════════════════════════════════ --}}
    <div class="rpt-header no-print">
        <div>
            <p class="rpt-header-title">Booking Management</p>
            <p class="rpt-header-sub">All bookings · payments · possession · documents</p>
        </div>
        <div class="rpt-header-actions">
            @can('booking_create')
            <a href="{{ route('booking.search') }}" class="btn-navy">
                <i class="fas fa-plus"></i> New Booking
            </a>
            @endcan
        </div>
    </div>

    {{-- ══ FLASH ═══════════════════════════════════════════════════ --}}
    @if(session('success'))
    <div class="alert-flash alert-flash-success mb-4">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-flash alert-flash-danger mb-4">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    {{-- ══ STAT CARDS ══════════════════════════════════════════════ --}}
    @php
        $col = $all_bookings->getCollection();
        $statCounts = [
            'total'       => $all_bookings->total(),
            'active'      => $col->where('status','active')->count(),
            'pending'     => $col->where('status','pending')->count(),
            'completed'   => $col->where('status','completed')->count(),
            'transferred' => $col->whereIn('status',['transferred','partial_transferred','pending_transfer'])->count(),
            'on_hold'     => $col->filter(fn($b) => $holdMap[$b->id] ?? false)->count(),
        ];
    @endphp
    <div class="bk-stat-grid">
        <div class="bk-stat">
            <div class="bk-stat-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-bookmark"></i></div>
            <div><div class="bk-stat-label">Total</div><div class="bk-stat-val">{{ $statCounts['total'] }}</div></div>
        </div>
        <div class="bk-stat">
            <div class="bk-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check-circle"></i></div>
            <div><div class="bk-stat-label">Active</div><div class="bk-stat-val">{{ $statCounts['active'] }}</div></div>
        </div>
        <div class="bk-stat">
            <div class="bk-stat-icon" style="background:#fef9c3;color:#854d0e;"><i class="fas fa-clock"></i></div>
            <div><div class="bk-stat-label">Pending</div><div class="bk-stat-val">{{ $statCounts['pending'] }}</div></div>
        </div>
        <div class="bk-stat">
            <div class="bk-stat-icon" style="background:#fdf4ff;color:#7c3aed;"><i class="fas fa-exchange-alt"></i></div>
            <div><div class="bk-stat-label">Transferred</div><div class="bk-stat-val">{{ $statCounts['transferred'] }}</div></div>
        </div>
        <div class="bk-stat">
            <div class="bk-stat-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fas fa-trophy"></i></div>
            <div><div class="bk-stat-label">Completed</div><div class="bk-stat-val">{{ $statCounts['completed'] }}</div></div>
        </div>
        <div class="bk-stat" style="border-color:{{ $statCounts['on_hold']>0?'#fbbf24':'var(--border-color)' }};">
            <div class="bk-stat-icon" style="background:#fef3c7;color:#92400e;"><i class="fas fa-pause-circle"></i></div>
            <div>
                <div class="bk-stat-label">On Hold</div>
                <div class="bk-stat-val" style="color:{{ $statCounts['on_hold']>0?'#92400e':'var(--text-main)' }};">{{ $statCounts['on_hold'] }}</div>
            </div>
        </div>
    </div>

    {{-- ══ FILTER BAR ══════════════════════════════════════════════ --}}
    <form method="GET" action="{{ url()->current() }}" class="bk-filter-wrap">
        <div class="bk-filter-row">
            <div class="bk-search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search"
                    placeholder="Search Booking ID, Customer, CNIC, Phone, Plot…"
                    value="{{ request('search') }}" autocomplete="off">
            </div>
            <select name="status" class="bk-status-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['pending'=>'Pending','active'=>'Active','completed'=>'Completed','transferred'=>'Transferred','cancelled'=>'Cancelled','on_hold'=>'On Hold'] as $val => $lbl)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-navy" style="padding:9px 18px;font-size:12px;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request()->anyFilled(['search','status','fee_filter']))
            <a href="{{ url()->current() }}" class="btn-soft" style="padding:9px 14px;font-size:12px;">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
        </div>

        {{-- Fee filter chips — Pending + Paid per fee type --}}
        @php $ff = request('fee_filter'); @endphp
        <div class="bk-fee-chips" style="gap:6px 10px;">
            <span style="font-size:10px;font-weight:700;color:var(--muted-text);text-transform:uppercase;letter-spacing:.5px;align-self:center;">Fee Filter:</span>

            {{-- Registry --}}
            <div class="bk-fee-group">
                <span class="bk-fee-group-lbl"><i class="fas fa-stamp" style="color:#b45309;margin-right:3px;font-size:9px;"></i>Registry</span>
                <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except('fee_filter','page'), ['fee_filter'=>'pending_registry'])) }}"
                   class="fee-chip fee-chip-reg {{ $ff==='pending_registry'?'active':'' }}" title="Show bookings with unpaid registry fee">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except('fee_filter','page'), ['fee_filter'=>'paid_registry'])) }}"
                   class="fee-chip fee-chip-paid {{ $ff==='paid_registry'?'active':'' }}" title="Show bookings with paid registry fee">
                    <i class="fas fa-check-circle"></i> Paid
                </a>
            </div>

            <span style="color:var(--border-color);font-size:14px;align-self:center;">|</span>

            {{-- Development --}}
            <div class="bk-fee-group">
                <span class="bk-fee-group-lbl"><i class="fas fa-hard-hat" style="color:#1d4ed8;margin-right:3px;font-size:9px;"></i>Development</span>
                <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except('fee_filter','page'), ['fee_filter'=>'pending_development'])) }}"
                   class="fee-chip fee-chip-dev {{ $ff==='pending_development'?'active':'' }}" title="Show bookings with unpaid development fee">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except('fee_filter','page'), ['fee_filter'=>'paid_development'])) }}"
                   class="fee-chip fee-chip-paid {{ $ff==='paid_development'?'active':'' }}" title="Show bookings with paid development fee">
                    <i class="fas fa-check-circle"></i> Paid
                </a>
            </div>

            <span style="color:var(--border-color);font-size:14px;align-self:center;">|</span>

            {{-- Security --}}
            <div class="bk-fee-group">
                <span class="bk-fee-group-lbl"><i class="fas fa-shield-alt" style="color:#15803d;margin-right:3px;font-size:9px;"></i>Security</span>
                <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except('fee_filter','page'), ['fee_filter'=>'pending_security'])) }}"
                   class="fee-chip fee-chip-sec {{ $ff==='pending_security'?'active':'' }}" title="Show bookings with unpaid security fee">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except('fee_filter','page'), ['fee_filter'=>'paid_security'])) }}"
                   class="fee-chip fee-chip-paid {{ $ff==='paid_security'?'active':'' }}" title="Show bookings with paid security fee">
                    <i class="fas fa-check-circle"></i> Paid
                </a>
            </div>

            @if($ff)
            <a href="{{ url()->current() }}?{{ http_build_query(request()->except('fee_filter','page')) }}"
               class="fee-chip" style="background:#fee2e2;color:#dc2626;border-color:#fecaca;margin-left:4px;">
                <i class="fas fa-times"></i> Clear Fee Filter
            </a>
            @endif
        </div>
    </form>

    {{-- ══ TABLE ════════════════════════════════════════════════════ --}}
    <div class="bk-table-wrap">
        <div style="overflow-x:auto;">
        <table class="bk-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Booking Ref</th>
                    <th>Customer</th>
                    <th>Plot</th>
                    <th>Financials</th>
                    <th>Fees</th>
                    <th>Possession</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
@forelse($all_bookings as $booking)
@php
    $st       = $booking->status;
    $rt       = $transferMap[$booking->id] ?? null;
    $isOnHold = $holdMap[$booking->id] ?? false;
    $holdInfo = $holdInfoMap[$booking->id] ?? null;
    $feeOk    = $feeStatusMap[$booking->id] ?? [
        'registry_ok'=>true,'development_ok'=>true,'security_ok'=>true,'all_ok'=>true,
        'registry_required'=>false,'development_required'=>false,'security_required'=>false,
        'registry_bill'=>null,'development_bill'=>null,'security_bill'=>null,
    ];

    // Plot balance check
    $plotCats     = ['down_payment','installment','quarterly_installment','plot_balance','others'];
    $discSentinel = 'Settlement discount — waived amount (not collected).';

    // Cash actually collected (exclude settlement discount sentinel records)
    $cashPaid = (float)\App\Models\PlotPayment::where('booking_id', $booking->id)
        ->where('status', 'paid')
        ->whereIn('payment_category', $plotCats)
        ->where('remarks', '!=', $discSentinel)
        ->sum('amount_paid');

    // Discount credits: old sentinel-style records + new discount_amount column entries
    $discPaid = (float)\App\Models\PlotPayment::where('booking_id', $booking->id)
        ->where('status', 'paid')
        ->whereIn('payment_category', $plotCats)
        ->selectRaw('COALESCE(SUM(CASE WHEN remarks = ? THEN amount_paid ELSE discount_amount END), 0) AS d', [$discSentinel])
        ->value('d');

    $totalCredited = $cashPaid + $discPaid;

    // Settled statuses mean remaining = 0 by business definition (completed/transferred/etc.)
    $isSettledSt = in_array($st, ['completed','transferred','swapped','plot_relocated','partial_transferred']);
    if ($isSettledSt) {
        $remaining     = 0;
        $plotFullyPaid = true;
    } else {
        $remaining     = max(0, (float)($booking->total_price ?? 0) - $totalCredited);
        $plotFullyPaid = ($booking->total_price == 0) || $remaining <= 0;
    }

    // Only the CURRENT owner (active/completed) can receive a possession letter.
    // Transferred/swapped/etc. means the plot was handed off — no possession for them.
    $isCurrentOwner  = in_array($st, ['active', 'completed']);
    $possessionReady = $plotFullyPaid && $feeOk['all_ok'] && $isCurrentOwner;

    // Transfer gate: fees must be cleared first
    $transferable = $feeOk['all_ok']
                 && $isCurrentOwner
                 && !$isOnHold;

    // Block reason strings
    $possessionBlockReasons = [];
    $transferBlockReasons   = [];
    if (!$plotFullyPaid)             { $possessionBlockReasons[]='Plot balance outstanding'; $transferBlockReasons[]='Plot balance outstanding'; }
    if (!$feeOk['registry_ok'])      { $possessionBlockReasons[]='Registry fee not paid';   $transferBlockReasons[]='Registry fee not paid'; }
    if (!$feeOk['development_ok'])   { $possessionBlockReasons[]='Development fee not paid';$transferBlockReasons[]='Development fee not paid'; }
    if (!$feeOk['security_ok'])      { $possessionBlockReasons[]='Security fee not paid'; }
    if ($isOnHold)                     $transferBlockReasons[] = 'Booking is on hold';
    if (!$isCurrentOwner)              $possessionBlockReasons[] = 'Plot ownership transferred';

    // Hold info for the detail modal (safe JS strings)
    $holdRef      = addslashes($booking->customer_booking_id ?? '');
    $holdCust     = addslashes($booking->customer->name ?? '');
    $holdRemarks  = addslashes($holdInfo['remarks'] ?? 'No reason recorded.');
    $holdDate     = $holdInfo['created_at'] ?? '';
    $holdId       = $booking->id;

    $transferBlockMsg = implode(', ', $transferBlockReasons);
@endphp

<tr class="{{ $isOnHold ? 'row-on-hold' : '' }}">

    {{-- # --}}
    <td style="color:var(--muted-text);font-size:11px;">
        {{ ($all_bookings->currentPage()-1) * $all_bookings->perPage() + $loop->iteration }}
    </td>

    {{-- Booking Ref + badges --}}
    <td>
        <span style="font-weight:800;color:#1e3a8a;font-size:12px;">
            {{ $booking->customer_booking_id ?? 'N/A' }}
        </span>

        {{-- ON HOLD — clickable badge opens detail modal --}}
        @if($isOnHold)
        <div class="hold-badge"
             onclick="showHoldDetail('{{ $holdRef }}','{{ $holdCust }}','{{ $holdRemarks }}','{{ $holdDate }}',{{ $holdId }})"
             title="Click to view hold details">
            <i class="fas fa-pause-circle"></i> ON HOLD
            <i class="fas fa-info-circle" style="font-size:8px;opacity:.7;"></i>
        </div>
        @if(!empty($holdInfo['remarks']))
        <div class="hold-reason"
             onclick="showHoldDetail('{{ $holdRef }}','{{ $holdCust }}','{{ $holdRemarks }}','{{ $holdDate }}',{{ $holdId }})"
             title="{{ $holdInfo['remarks'] }}">
            {{ $holdInfo['remarks'] }}
        </div>
        @endif
        @endif

        {{-- Fee warning badge — only for active/completed/pending, only for required+unpaid fees --}}
        @if(!$isOnHold && in_array($st,['active','completed','pending']))
        @php
            $dueLabels = [];
            if($feeOk['registry_required']    && !$feeOk['registry_ok'])    $dueLabels[] = 'Registry';
            if($feeOk['development_required'] && !$feeOk['development_ok']) $dueLabels[] = 'Dev.';
            if($feeOk['security_required']    && !$feeOk['security_ok'])    $dueLabels[] = 'Security';
        @endphp
        @if(count($dueLabels))
        <div style="display:inline-flex;align-items:center;gap:3px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:6px;padding:2px 7px;font-size:9px;font-weight:800;margin-top:3px;">
            <i class="fas fa-lock" style="font-size:8px;"></i> {{ implode(' + ', $dueLabels) }} Fee Due
        </div>
        @endif
        @endif
    </td>

    {{-- Customer --}}
    <td>
        <div style="font-weight:700;">{{ $booking->customer->name ?? '—' }}</div>
        <div style="font-size:10px;color:var(--muted-text);">{{ $booking->customer->cnic ?? '' }}</div>
        <div style="font-size:10px;color:var(--muted-text);">
            <i class="fas fa-phone" style="font-size:9px;"></i>
            {{ $booking->customer->phone ?? $booking->customer->mobile ?? '' }}
        </div>
    </td>

    {{-- Plot --}}
    <td>
        <div style="font-weight:700;">#{{ $booking->plot->plot_number ?? '—' }}</div>
        <div style="font-size:10px;color:var(--muted-text);">
            {{ $booking->plot->block ?? '' }}
            @if($booking->plot->size ?? null)
                · {{ $booking->plot->size }} {{ $booking->plot->unit ?? '' }}
            @endif
        </div>
    </td>

    {{-- Financials --}}
    <td>
        <div style="font-weight:800;color:#15803d;">PKR {{ number_format($booking->total_price) }}</div>
        @if($booking->down_payment)
        <div style="font-size:10px;color:var(--muted-text);">Down: PKR {{ number_format($booking->down_payment) }}</div>
        @endif
        @if($booking->total_installments)
        <div style="font-size:10px;color:var(--muted-text);">
            {{ $booking->total_installments }} mo @ PKR {{ number_format($booking->monthly_installment) }}
        </div>
        @endif
        <div style="margin-top:5px;padding-top:5px;border-top:1px dashed var(--border-color);">
            <div style="font-size:10px;font-weight:700;color:#16a34a;">
                <i class="fas fa-check-circle" style="font-size:8px;"></i> Paid: PKR {{ number_format($cashPaid) }}
            </div>
            @if($remaining > 0)
            <div style="font-size:10px;font-weight:700;color:#dc2626;">
                <i class="fas fa-clock" style="font-size:8px;"></i> Due: PKR {{ number_format($remaining) }}
            </div>
            @else
            <div style="font-size:10px;font-weight:700;color:#16a34a;">
                <i class="fas fa-trophy" style="font-size:8px;"></i> Fully Paid
            </div>
            @endif
        </div>
    </td>

    {{-- Fees --}}
    <td>
        @if($st === 'cancelled')
        <span style="font-size:11px;color:var(--muted-text);">—</span>
        @elseif($st === 'pending_transfer')
        <span style="display:inline-flex;align-items:center;gap:4px;background:#fef9c3;border:1px solid #fde68a;color:#854d0e;border-radius:6px;padding:2px 7px;font-size:9px;font-weight:700;white-space:nowrap;">
            <i class="fas fa-clock" style="font-size:8px;"></i> Transfer Pending
        </span>
        @else
        @php
            $feeRows = [
                ['key'=>'registry_ok',    'req'=>'registry_required',    'icon'=>'fa-stamp',      'label'=>'Registry',     'color'=>'#b45309', 'bg'=>'#fff7ed', 'border'=>'#fde68a'],
                ['key'=>'development_ok', 'req'=>'development_required', 'icon'=>'fa-hard-hat',   'label'=>'Development',  'color'=>'#1d4ed8', 'bg'=>'#eff6ff', 'border'=>'#bfdbfe'],
                ['key'=>'security_ok',    'req'=>'security_required',    'icon'=>'fa-shield-alt', 'label'=>'Security',     'color'=>'#15803d', 'bg'=>'#f0fdf4', 'border'=>'#bbf7d0'],
            ];
        @endphp
        @foreach($feeRows as $fr)
            @if($feeOk[$fr['req']])
            @php
                $billKey  = str_replace('_ok', '_bill', $fr['key']); // e.g. registry_bill
                $billObj  = $feeOk[$billKey] ?? null;
                $billAmt  = $billObj ? (float)$billObj->amount      : 0;
                $billPaid = $billObj ? (float)$billObj->paid_amount  : 0;
                $billRem  = max(0, $billAmt - $billPaid);
            @endphp
                @if($feeOk[$fr['key']])
                <div style="display:inline-flex;align-items:center;gap:4px;background:{{ $fr['bg'] }};border:1px solid {{ $fr['border'] }};color:{{ $fr['color'] }};border-radius:6px;padding:2px 7px;font-size:9px;font-weight:700;margin-bottom:2px;white-space:nowrap;">
                    <i class="fas {{ $fr['icon'] }}" style="font-size:8px;"></i>
                    {{ $fr['label'] }}
                    @if($fr['key'] === 'security_ok' && $feeOk['sec_months_total'])
                        {{ $feeOk['sec_months_paid'] }}/{{ $feeOk['sec_months_total'] }} mo.
                    @elseif($billAmt > 0)
                        <span style="color:#15803d;">✓ {{ number_format($billAmt) }}</span>
                    @else
                        <span style="color:#15803d;">✓</span>
                    @endif
                </div><br>
                @else
                <div style="display:inline-flex;align-items:center;gap:4px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:6px;padding:2px 7px;font-size:9px;font-weight:700;margin-bottom:2px;white-space:nowrap;">
                    <i class="fas {{ $fr['icon'] }}" style="font-size:8px;"></i>
                    @if($fr['key'] === 'security_ok' && $feeOk['sec_months_unpaid'])
                        Security {{ $feeOk['sec_months_unpaid'] }} mo. due
                    @elseif($billRem > 0)
                        {{ $fr['label'] }} {{ number_format($billRem) }} due
                    @else
                        {{ $fr['label'] }} Pending
                    @endif
                </div><br>
                @endif
            @endif
        @endforeach
        @if(!$feeOk['registry_required'] && !$feeOk['development_required'] && !$feeOk['security_required'])
        <span style="font-size:11px;color:var(--muted-text);">—</span>
        @endif
        @endif
    </td>

    {{-- Possession --}}
    <td>
        @if($possessionReady)
            <span class="possess-ready"><i class="fas fa-check-circle"></i> Ready</span>
        @elseif(!$isCurrentOwner)
            <span style="font-size:10px;font-weight:700;color:#7c3aed;background:#fdf4ff;padding:2px 8px;border-radius:20px;white-space:nowrap;">
                <i class="fas fa-exchange-alt"></i> Transferred
            </span>
        @elseif($plotFullyPaid && !$feeOk['all_ok'])
            <span class="possess-blocked" title="{{ implode(' · ', $possessionBlockReasons) }}">
                <i class="fas fa-lock"></i>
                @php $dueF=[];if(!$feeOk['registry_ok'])$dueF[]='Reg';if(!$feeOk['development_ok'])$dueF[]='Dev';if(!$feeOk['security_ok'])$dueF[]='Sec'; @endphp
                {{ implode('+', $dueF) }} Fee Due
            </span>
        @elseif(in_array($st, ['active','pending']))
            <span class="possess-blocked"><i class="fas fa-lock"></i> Balance Due</span>
        @else
            <span style="font-size:11px;color:var(--muted-text);">—</span>
        @endif
    </td>

    {{-- Date --}}
    <td>
        <div style="font-size:12px;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</div>
        <div style="font-size:10px;color:var(--muted-text);">{{ \Carbon\Carbon::parse($booking->created_at)->diffForHumans() }}</div>
    </td>

    {{-- Status --}}
    <td>
        <span class="sp sp-{{ str_replace('_','-',$st) }}">
            <span class="sp-dot"></span>
            {{ ucfirst(str_replace('_',' ',$st)) }}
        </span>
    </td>

    {{-- Actions --}}
    <td>
        <div style="display:grid;grid-template-columns:repeat(3,32px);gap:6px;justify-content:end;margin-left:auto;width:fit-content;">

            {{-- View Ledger — locked for pending_transfer --}}
            @if($st === 'pending_transfer')
            <button class="ab ab-ledger" disabled type="button"
                    title="Ledger locked — transfer in progress"
                    style="opacity:.4;cursor:not-allowed;">
                <i class="fas fa-lock"></i>
            </button>
            @else
            <a href="{{ route('ledger.view', $booking->id) }}" class="ab ab-ledger" title="View Ledger">
                <i class="fas fa-book-open"></i>
            </a>
            @endif

            {{-- Booking Detail --}}
            <a href="{{ route('booking.detail.view', $booking->id) }}" class="ab ab-view" title="Booking Detail">
                <i class="fas fa-info-circle"></i>
            </a>

            {{-- Fee Management --}}
            <a href="{{ route('fee.management', ['q' => $booking->customer_booking_id]) }}" 
               class="ab" style="background:#fdf4ff;color:#7c3aed;border-color:#e9d5ff;" title="Fee Management">
                <i class="fas fa-receipt"></i>
            </a>

            {{-- Edit --}}
            @if(in_array($st, ['pending','active']) && !$isOnHold)
            <a href="{{ route('booking.edit', $booking->id) }}" class="ab ab-edit" title="Edit Booking">
                <i class="fas fa-pen"></i>
            </a>
            @else
            <div class="ab" style="opacity:.2;"><i class="fas fa-pen"></i></div>
            @endif

            {{-- Application Form --}}
            @if($st !== 'cancelled')
            <a href="{{ route('booking.application.form', Hashids::encode($booking->id)) }}"
               target="_blank" class="ab ab-form" title="Application Form">
                <i class="fas fa-file-alt"></i>
            </a>
            @else
            <div class="ab" style="opacity:.2;"><i class="fas fa-file-alt"></i></div>
            @endif

            {{-- Agreement --}}
            @if($st !== 'cancelled')
            <a href="{{ route('booking.agreement', $booking->id) }}"
               target="_blank" class="ab ab-agree" title="Agreement">
                <i class="fas fa-file-contract"></i>
            </a>
            @else
            <div class="ab" style="opacity:.2;"><i class="fas fa-file-contract"></i></div>
            @endif

            {{-- Possession Letter --}}
            @if($possessionReady)
            <a href="{{ route('booking.possession.letter', $booking->id) }}"
               target="_blank" class="ab ab-possess" title="Possession Letter">
                <i class="fas fa-home"></i>
            </a>
            @elseif($isCurrentOwner)
            <button class="ab" disabled type="button"
                    title="Possession locked — {{ implode(', ', $possessionBlockReasons) }}"
                    style="background:#fef2f2;color:#fca5a5;border-color:#fecaca;cursor:not-allowed;">
                <i class="fas fa-lock"></i>
            </button>
            @else
            <div class="ab" style="opacity:.2;"><i class="fas fa-home"></i></div>
            @endif

            {{-- Transfer / Deed --}}
            @if($rt)
            <a href="{{ route('transfers.deed', $rt->id) }}" target="_blank"
               class="ab ab-pdf" style="background:#fff0f0;color:#dc2626;border-color:#fecaca;"
               title="Transfer Deed">
                <i class="fas fa-exchange-alt"></i>
            </a>
            @elseif(in_array($st, ['active','completed']))
                @if($transferable)
                <a href="{{ route('transfers.search', ['booking_id' => $booking->id]) }}"
                   class="ab ab-pdf" style="background:#fffbeb;color:#92400e;border-color:#fde68a;"
                   title="Initiate Transfer">
                    <i class="fas fa-exchange-alt"></i>
                </a>
                @else
                <button class="ab" disabled type="button"
                        title="Transfer blocked — {{ $transferBlockMsg }}"
                        style="background:#fef2f2;color:#fca5a5;border-color:#fecaca;cursor:not-allowed;"
                        onclick="alert('Transfer blocked:\n{{ addslashes($transferBlockMsg) }}')">
                    <i class="fas fa-ban"></i>
                </button>
                @endif
            @else
            <div class="ab" style="opacity:.2;"><i class="fas fa-exchange-alt"></i></div>
            @endif

            {{-- Weekly Offer Letter --}}
            @if(in_array($st, ['active','pending','completed']))
            <a href="{{ route('booking.weekly.offer', $booking->id) }}"
               target="_blank" class="ab"
               style="background:#fdf4ff;color:#7c3aed;border-color:#e9d5ff;"
               title="Weekly Offer Letter">
                <i class="fas fa-calendar-week"></i>
            </a>
            @else
            <div class="ab" style="opacity:.2;"><i class="fas fa-calendar-week"></i></div>
            @endif

            {{-- Customer Statement PDF --}}
            <a href="{{ route('customer.statement', $booking->customer_id) }}"
               target="_blank" class="ab"
               style="background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;"
               title="Customer Statement PDF">
                <i class="fas fa-file-invoice"></i>
            </a>

            {{-- Send Statement by Email --}}
            @if($booking->customer->email)
            <form method="POST" action="{{ route('customer.statement.email', $booking->customer_id) }}"
                  style="display:contents;"
                  onsubmit="return confirm('Send statement to {{ addslashes($booking->customer->email) }}?')">
                @csrf
                <button type="submit" class="ab"
                        style="background:#f0fdf4;color:#15803d;border-color:#86efac;"
                        title="Email Statement to {{ $booking->customer->email }}">
                    <i class="fas fa-envelope"></i>
                </button>
            </form>
            @else
            <div class="ab" style="opacity:.2;"><i class="fas fa-envelope"></i></div>
            @endif

            {{-- Hold / Unhold --}}
            @if(in_array($st, ['active','pending','pending_transfer']))
                @if($isOnHold)
                <form method="POST" action="{{ route('booking.unhold', $booking->id) }}"
                      onsubmit="return confirm('Release hold on {{ $booking->customer_booking_id }}?\nPayments will be accepted again.')"
                      style="display:contents;">
                    @csrf
                    <button type="submit" class="ab ab-unhold" title="Release Hold">
                        <i class="fas fa-play-circle"></i>
                    </button>
                </form>
                @else
                <button type="button" class="ab ab-hold" title="Put on Hold"
                        onclick="openHoldModal({{ $booking->id }},'{{ addslashes($booking->customer_booking_id) }}','{{ addslashes($booking->customer->name ?? '') }}')">
                    <i class="fas fa-pause-circle"></i>
                </button>
                @endif
            @else
            <div class="ab" style="opacity:.2;"><i class="fas fa-pause-circle"></i></div>
            @endif

            {{-- Delete --}}
            @if(in_array($st, ['pending','cancelled']))
            <form method="POST" action="{{ route('booking.destroy', $booking->id) }}" style="display:contents;"
                  onsubmit="return confirm('Delete booking {{ $booking->customer_booking_id }}?\nThis cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="ab ab-del" title="Delete Booking">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @else
            <div class="ab" style="opacity:.2;"><i class="fas fa-trash"></i></div>
            @endif

        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="10">
        <div style="text-align:center;padding:60px 20px;color:var(--muted-text);">
            <i class="fas fa-book-open" style="font-size:3rem;opacity:.18;display:block;margin-bottom:12px;"></i>
            @if(request()->filled('search'))
                <p style="font-weight:700;margin:0 0 4px;">No bookings match "{{ request('search') }}"</p>
                <p style="font-size:12px;margin:0;">Try a different name, CNIC, or plot number.</p>
            @else
                <p style="font-weight:700;margin:0;">No bookings yet</p>
            @endif
        </div>
    </td>
</tr>
@endforelse
            </tbody>
        </table>
        </div>

        @if($all_bookings->hasPages())
        <div class="bk-foot">
            <div class="bk-foot-info">
                Showing {{ $all_bookings->firstItem() }}–{{ $all_bookings->lastItem() }}
                of {{ $all_bookings->total() }} bookings
            </div>
            {{ $all_bookings->links() }}
        </div>
        @endif
    </div>

</div>{{-- /ldg-wrap --}}


{{-- ══════════════════════════════════════════════════════════════
     HOLD DETAIL MODAL — click badge/reason to view full info
══════════════════════════════════════════════════════════════ --}}
<div class="bk-modal-bd" id="holdDetailBd">
    <div class="bk-modal" style="max-width:480px;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:42px;height:42px;background:#fef3c7;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-pause-circle" style="color:#d97706;font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:800;color:#0f172a;">Booking On Hold</div>
                    <div id="hd-ref" style="font-size:11px;color:#94a3b8;margin-top:1px;font-family:monospace;"></div>
                </div>
            </div>
            <button onclick="closeHoldDetail()"
                    style="background:#f1f5f9;border:none;border-radius:8px;width:30px;height:30px;cursor:pointer;font-size:16px;color:#64748b;display:flex;align-items:center;justify-content:center;">
                &times;
            </button>
        </div>

        {{-- Customer --}}
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;margin-bottom:14px;display:flex;gap:12px;align-items:center;">
            <div style="width:36px;height:36px;background:#eff6ff;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-user" style="color:#1d4ed8;font-size:14px;"></i>
            </div>
            <div>
                <div id="hd-customer" style="font-size:13px;font-weight:700;color:#0f172a;"></div>
                <div style="font-size:11px;color:#94a3b8;">Booking customer</div>
            </div>
        </div>

        {{-- Remarks --}}
        <div style="margin-bottom:14px;">
            <div style="font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px;">
                <i class="fas fa-comment-alt"></i> Hold Reason / Remarks
            </div>
            <div id="hd-remarks" class="hd-remarks-box"></div>
        </div>

        {{-- Meta tiles --}}
        <div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;">
            <div class="hd-meta-tile">
                <div class="hd-meta-lbl"><i class="fas fa-clock"></i> Held Since</div>
                <div id="hd-date" class="hd-meta-val">—</div>
            </div>
            <div style="background:#fef3c7;border:1px solid #fbbf24;border-radius:9px;padding:10px 14px;flex:1;min-width:140px;">
                <div class="hd-meta-lbl" style="color:#92400e;"><i class="fas fa-ban"></i> Effect</div>
                <div style="font-size:12px;font-weight:800;color:#92400e;">
                    <i class="fas fa-times-circle"></i> Payments Blocked
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:8px;padding-top:14px;border-top:1px solid #f1f5f9;flex-wrap:wrap;">
            <form method="POST" id="hd-unhold-form" style="display:inline;">
                @csrf
                <button type="submit"
                        onclick="return confirm('Release hold? Payments will be accepted again.')"
                        style="background:#15803d;color:#fff;border:none;border-radius:9px;padding:10px 20px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fas fa-play-circle"></i> Release Hold
                </button>
            </form>
       <a id="hd-ledger-link" href="#" target="_blank"
   style="background:#eff6ff;color:#1d4ed8;border:1.5px solid #bfdbfe;border-radius:9px;padding:10px 14px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
    <i class="fas fa-eye"></i> Ledger
</a>
            <button onclick="closeHoldDetail()"
                    style="background:#f1f5f9;color:#64748b;border:none;border-radius:9px;padding:10px 14px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;margin-left:auto;">
                Close
            </button>
        </div>

    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     HOLD INPUT MODAL — to put a booking on hold
══════════════════════════════════════════════════════════════ --}}
<div class="bk-modal-bd" id="holdModalBd">
    <div class="bk-modal">
        <div class="hold-modal-title">
            <i class="fas fa-pause-circle" style="color:#f59e0b;"></i>
            Put Booking On Hold
        </div>
        <p class="hold-modal-sub" id="holdModalSub">Booking — Customer</p>

        <form method="POST" id="holdForm">
            @csrf
            <label class="hm-label" for="holdRemarks">
                Reason for hold <span style="color:#dc2626;">*</span>
            </label>
            <textarea name="remarks" id="holdRemarks" class="hm-textarea"
                      placeholder="e.g. Dispute between parties, cheque bounced, pending documentation…"
                      required></textarea>
            <p class="hm-hint">
                <i class="fas fa-info-circle"></i>
                All payments will be blocked until the hold is released.
            </p>
            <div class="hold-modal-actions" style="display:flex;gap:8px;margin-top:16px;">
                <button type="submit" class="btn-hold-confirm">
                    <i class="fas fa-pause-circle"></i> Confirm Hold
                </button>
                <button type="button" class="btn-modal-cancel" onclick="closeHoldModal()">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>


@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════
// HOLD DETAIL MODAL
// ══════════════════════════════════════════════════════════════
function showHoldDetail(ref, customerName, remarks, createdAt, bookingId) {
    document.getElementById('hd-ref').textContent      = ref;
    document.getElementById('hd-customer').textContent = customerName;
    document.getElementById('hd-remarks').textContent  = remarks || 'No reason recorded.';
    document.getElementById('hd-ledger-link').href = '{{ url("/ledger") }}/' + bookingId + '/view';
    document.getElementById('hd-unhold-form').action   = '{{ url("/bookings") }}/' + bookingId + '/unhold';

    // Format the date nicely
    if (createdAt) {
        try {
            const d = new Date(createdAt);
            document.getElementById('hd-date').textContent = d.toLocaleDateString('en-PK', {
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit', hour12: true
            });
        } catch(e) {
            document.getElementById('hd-date').textContent = createdAt;
        }
    } else {
        document.getElementById('hd-date').textContent = '—';
    }

    document.getElementById('holdDetailBd').classList.add('show');
}

function closeHoldDetail() {
    document.getElementById('holdDetailBd').classList.remove('show');
}

document.getElementById('holdDetailBd').addEventListener('click', function(e) {
    if (e.target === this) closeHoldDetail();
});

// ══════════════════════════════════════════════════════════════
// HOLD INPUT MODAL
// ══════════════════════════════════════════════════════════════
function openHoldModal(bookingId, ref, customerName) {
    document.getElementById('holdForm').action        = '{{ url("/bookings") }}/' + bookingId + '/hold';
    document.getElementById('holdModalSub').textContent = ref + ' — ' + customerName;
    document.getElementById('holdRemarks').value      = '';
    document.getElementById('holdModalBd').classList.add('show');
    setTimeout(() => document.getElementById('holdRemarks').focus(), 150);
}

function closeHoldModal() {
    document.getElementById('holdModalBd').classList.remove('show');
}

document.getElementById('holdModalBd').addEventListener('click', function(e) {
    if (e.target === this) closeHoldModal();
});

// ESC closes either modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeHoldDetail();
        closeHoldModal();
        document.querySelectorAll('.ab-docs-menu.open').forEach(function(m){ m.classList.remove('open'); });
    }
});

// ══════════════════════════════════════════════════════════════
// DOCUMENTS DROPDOWN
// ══════════════════════════════════════════════════════════════
function openDocsMenu(btn, menuId) {
    var all = document.querySelectorAll('.ab-docs-menu.open');
    all.forEach(function(m){ m.classList.remove('open'); });

    var menu = document.getElementById(menuId);
    if (!menu) return;

    var rect = btn.getBoundingClientRect();
    var menuW = 192; // matches min-width in CSS

    // Position below the button, right-aligned to it
    menu.style.top  = (rect.bottom + window.scrollY + 5) + 'px';
    var left = rect.right + window.scrollX - menuW;
    if (left < 8) left = 8; // prevent going off left edge
    menu.style.left = left + 'px';
    menu.classList.add('open');
}

// Close on outside click or scroll
document.addEventListener('click', function(e) {
    if (!e.target.closest('.ab-dd-wrap') && !e.target.closest('.ab-docs-menu')) {
        document.querySelectorAll('.ab-docs-menu.open').forEach(function(m){ m.classList.remove('open'); });
    }
});
document.addEventListener('scroll', function() {
    document.querySelectorAll('.ab-docs-menu.open').forEach(function(m){ m.classList.remove('open'); });
}, true);
</script>
@endpush

@endsection
