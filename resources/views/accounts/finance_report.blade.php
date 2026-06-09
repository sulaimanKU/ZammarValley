@extends('layouts.index')

@section('content')
@push('styles')
<style>
/* ═══════════════════════════════════════════════
   FINANCE REPORT — Zamar Valley
   Font: DM Sans (clean, modern, financial feel)
═══════════════════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800;900&family=DM+Mono:wght@400;500&display=swap');

.fr-wrap * { font-family: 'DM Sans', sans-serif; }
.fr-wrap { padding: 1.75rem; background: #f4f6fb; min-height: 100vh; }

/* ── Header ─────────────────────────────────── */
.fr-header {
    background: linear-gradient(135deg, #0a0f1e 0%, #0f2460 50%, #1a3a9c 100%);
    border-radius: 18px; padding: 26px 32px;
    margin-bottom: 22px; position: relative; overflow: hidden;
    display: flex; justify-content: space-between; align-items: center;
}
.fr-header::before {
    content: 'FINANCE'; position: absolute; right: -20px; top: -10px;
    font-size: 120px; font-weight: 900; color: rgba(255,255,255,.03);
    letter-spacing: -4px; line-height: 1; pointer-events: none;
}
.fr-header-title { font-size: 1.25rem; font-weight: 800; color: #fff; margin: 0; }
.fr-header-sub   { font-size: 12px; color: rgba(255,255,255,.45); margin: 5px 0 0; }
.fr-header-actions { display: flex; gap: 10px; position: relative; z-index: 1; }

/* ── Buttons ────────────────────────────────── */
.btn-soft { background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); color: #fff; padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 7px; cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap; }
.btn-soft:hover { background: rgba(255,255,255,.2); color: #fff; }
.btn-soft svg { width: 14px; height: 14px; }
.btn-excel { background: #16a34a; color: #fff; padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; transition: all .15s; white-space: nowrap; }
.btn-excel:hover { background: #15803d; }
.btn-excel svg { width: 15px; height: 15px; }

/* ── Stat Cards ─────────────────────────────── */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(200px,1fr)); gap: 16px; margin-bottom: 20px; }
@media(max-width:560px) { .stat-grid { grid-template-columns: 1fr; } }
.stat-card {
    background: #fff; border-radius: 14px;
    border: 1px solid #e4e9f2;
    padding: 20px 22px;
    box-shadow: 0 2px 8px rgba(15,23,42,.05);
    display: flex; align-items: center; gap: 16px;
    position: relative; overflow: hidden;
    transition: transform .15s, box-shadow .15s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(15,23,42,.08); }
.stat-card::after {
    content: ''; position: absolute; right: -20px; top: -20px;
    width: 80px; height: 80px; border-radius: 50%;
    background: var(--stat-color); opacity: .06;
}
.stat-icon {
    width: 48px; height: 48px; border-radius: 13px;
    background: var(--stat-bg);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-icon svg { width: 24px; height: 24px; }
.stat-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .7px; }
.stat-value { font-size: 20px; font-weight: 800; color: var(--stat-color); margin: 4px 0 2px; line-height: 1; }
.stat-sub   { font-size: 11px; color: #94a3b8; }

/* ── Filter Bar ─────────────────────────────── */
.filter-card {
    background: #fff; border-radius: 14px;
    border: 1px solid #e4e9f2;
    padding: 18px 22px; margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(15,23,42,.04);
}
.filter-title { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
.filter-title svg { width: 14px; height: 14px; }
.filter-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 12px; align-items: end; }
@media(max-width:1100px) { .filter-grid { grid-template-columns: 1fr 1fr; } }
.filter-group label { font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .5px; display: block; margin-bottom: 5px; }
.filter-control { border: 1.5px solid #e2e8f0; border-radius: 9px; font-size: 13px; padding: 9px 12px; width: 100%; background: #fff; color: #0f172a; transition: border-color .15s; font-family: 'DM Sans', sans-serif; outline: none; }
.filter-control:focus { border-color: #1e3a8a; box-shadow: 0 0 0 3px rgba(30,58,138,.08); }
.date-range-group { display: flex; gap: 8px; align-items: center; }
.date-range-group .filter-control { flex: 1; }
.date-sep { font-size: 12px; color: #94a3b8; font-weight: 600; white-space: nowrap; }
.filter-actions { display: flex; gap: 8px; }
.btn-filter { background: #1e3a8a; color: #fff; border: none; padding: 9px 20px; border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
.btn-reset  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 9px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; text-decoration: none; }
.btn-reset:hover { background: #e2e8f0; }

/* ── Charts row ─────────────────────────────── */
.charts-row { display: grid; grid-template-columns: 2fr 1fr; gap: 18px; margin-bottom: 20px; }
@media(max-width:1100px) { .charts-row { grid-template-columns: 1fr; } }

/* ── Panel card ─────────────────────────────── */
.panel {
    background: #fff; border-radius: 14px;
    border: 1px solid #e4e9f2;
    box-shadow: 0 2px 8px rgba(15,23,42,.04);
    overflow: hidden; margin-bottom: 20px;
}
.panel-head {
    padding: 16px 22px; border-bottom: 1px solid #f1f5f9;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;
}
.panel-title { font-size: 13px; font-weight: 800; color: #0f172a; margin: 0; }
.panel-sub   { font-size: 11px; color: #94a3b8; margin: 2px 0 0; }
.panel-body  { padding: 22px; }

/* ── Category breakdown ─────────────────────── */
.cat-breakdown { display: flex; flex-direction: column; gap: 12px; }
.cat-row { display: flex; align-items: center; gap: 12px; }
.cat-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.cat-name { font-size: 12px; font-weight: 700; color: #334155; flex: 1; }
.cat-bar-wrap { flex: 2; height: 7px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
.cat-bar-fill { height: 100%; border-radius: 10px; transition: width .6s ease; }
.cat-amt { font-size: 12px; font-weight: 800; color: #0f172a; min-width: 110px; text-align: right; font-family: 'DM Mono', monospace; }
.cat-pct { font-size: 10px; color: #94a3b8; min-width: 36px; text-align: right; }

/* ── Table ──────────────────────────────────── */
.fr-table-wrap { overflow-x: auto; }
.fr-table { width: 100%; border-collapse: collapse; min-width: 800px; }
.fr-table thead th {
    font-size: 10px; text-transform: uppercase; letter-spacing: .7px;
    color: #94a3b8; font-weight: 700; background: #fafbfc;
    border-bottom: 2px solid #f1f5f9; padding: 11px 16px; white-space: nowrap;
}
.fr-table tbody td { padding: 11px 16px; border-bottom: 1px solid #f8fafc; font-size: 12.5px; vertical-align: middle; }
.fr-table tbody tr:last-child td { border-bottom: none; }
.fr-table tbody tr:hover { background: #f9fafb; }
.fr-table tfoot td { padding: 12px 16px; background: #f8fafc; border-top: 2px solid #e8edf3; font-weight: 800; }

/* ── Status / category pills ────────────────── */
.pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; white-space: nowrap; }
.pill-dot { width: 6px; height: 6px; border-radius: 50%; }
.pill-paid     { background: #dcfce7; color: #15803d; } .pill-paid .pill-dot     { background: #16a34a; }
.pill-pending  { background: #fee2e2; color: #dc2626; } .pill-pending .pill-dot  { background: #dc2626; }
.pill-partial  { background: #fef9c3; color: #854d0e; } .pill-partial .pill-dot  { background: #ca8a04; }
.pill-overdue  { background: #fff1f2; color: #be123c; } .pill-overdue .pill-dot  { background: #e11d48; }
.pill-token    { background: #fdf4ff; color: #7c3aed; }
.pill-down     { background: #eff6ff; color: #1d4ed8; }
.pill-proc     { background: #fff7ed; color: #ea580c; }
.pill-install  { background: #f0fdf4; color: #16a34a; }
.pill-fine     { background: #fef2f2; color: #dc2626; }
.pill-other    { background: #f1f5f9; color: #475569; }
.pill-balance  { background: #f0f9ff; color: #0369a1; }
.pill-cash     { background: #f0fdf4; color: #166534; }
.pill-bank     { background: #eff6ff; color: #1e40af; }
.pill-cheque   { background: #fefce8; color: #854d0e; }
.pill-online   { background: #fdf4ff; color: #7c3aed; }

/* ── Overdue pulse animation ────────────────── */
@keyframes pulse-red {
    0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,.15); }
    50%       { box-shadow: 0 0 0 8px rgba(220,38,38,.0); }
}
.overdue-row { background: #fff8f8 !important; }
.overdue-row:hover { background: #fff1f1 !important; }
.overdue-days { font-size: 10px; font-weight: 800; color: #e11d48; background: #fff1f2; padding: 2px 7px; border-radius: 6px; }

/* ── Empty state ────────────────────────────── */
.empty-state { text-align: center; padding: 50px 20px; color: #94a3b8; }
.empty-state svg { width: 52px; height: 52px; opacity: .2; display: block; margin: 0 auto 12px; }

/* ── Summary tiles in overdue section ── */
.overdue-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 18px; }
.ov-tile { background: #fff8f8; border: 1px solid #fecaca; border-radius: 10px; padding: 14px 16px; text-align: center; }
.ov-tile-val { font-size: 20px; font-weight: 800; color: #dc2626; }
.ov-tile-lbl { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-top: 3px; }

/* ── Responsive ─────────────────────────────── */
@media (max-width: 768px) {
    .fr-wrap { padding: 1rem; }
    .fr-header { flex-direction: column; align-items: flex-start; gap: 14px; padding: 18px 20px; }
    .fr-header-actions { width: 100%; flex-wrap: wrap; }
    .stat-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
    .filter-grid { grid-template-columns: 1fr; }
    .date-range-group { flex-direction: column; gap: 6px; }
    .date-sep { display: none; }
    .mode-tiles { grid-template-columns: 1fr 1fr !important; }
    .overdue-stats { grid-template-columns: 1fr; }
    .filter-actions { flex-wrap: wrap; }
}
@media (max-width: 480px) {
    .stat-grid { grid-template-columns: 1fr; }
    .stat-value { font-size: 18px; }
    .mode-tiles { grid-template-columns: 1fr !important; }
    .fr-header-title { font-size: 1rem; }
}
/* ── Print ──────────────────────────────────── */
@media print {
    .fr-header-actions, .filter-card, .no-print { display: none !important; }
    .fr-wrap { background: #fff; padding: .5rem; }
    .stat-card, .panel { box-shadow: none; border: 1px solid #e2e8f0; }
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
@endpush

@php
use Carbon\Carbon;

// ── Category breakdown ──────────────────────
$cats = [
    'down_payment'     => ['label' => 'Down Payment',     'color' => '#1e3a8a'],
    'processing_fee'   => ['label' => 'Processing Fee',   'color' => '#0891b2'],
    'installment'      => ['label' => 'Installment',      'color' => '#7c3aed'],
    'security_fee'     => ['label' => 'Security Fee',     'color' => '#b45309'],
    'maintenance_fee'  => ['label' => 'Maintenance Fee',  'color' => '#15803d'],
    'development_fee'  => ['label' => 'Development Fee',  'color' => '#be185d'],
    'bifurcation_fee'  => ['label' => 'Bifurcation Fee',  'color' => '#0f766e'],
    'registry_fee'     => ['label' => 'Registry Fee',     'color' => '#9333ea'],
    'fine'             => ['label' => 'Fine',             'color' => '#dc2626'],
    'others'           => ['label' => 'Others',           'color' => '#64748b'],
    'plot_balance'     => ['label' => 'Plot Balance',     'color' => '#16a34a'],
];

$catTotals = [];
foreach ($cats as $key => $meta) {
    $catTotals[$key] = $payments->where('payment_category', $key)
                                ->where('status', 'paid')
                                ->sum('amount_paid');
}
$maxCat = max(array_values($catTotals)) ?: 1;

// ── Monthly income (last 12 months) ─────────
$months = [];
for ($i = 11; $i >= 0; $i--) {
    $m = Carbon::now()->subMonths($i);
    $months[] = [
        'label' => $m->format('M y'),
        'key'   => $m->format('Y-m'),
        'total' => $payments->filter(fn($p) =>
            $p->status === 'paid' &&
            Carbon::parse($p->paid_date)->format('Y-m') === $m->format('Y-m')
        )->sum('amount_paid'),
    ];
}

// ── Payment mode breakdown ──────────────────
$modes = [
    'cash'          => 'Cash',
    'bank_transfer' => 'Bank Transfer',
    'cheque'        => 'Cheque',
    'online'        => 'Online',
];
$modeTotals = [];
foreach ($modes as $key => $label) {
    $modeTotals[$key] = $payments->where('payment_type', $key)
                                 ->where('status', 'paid')
                                 ->sum('amount_paid');
}

$overduePayments = $overdueInstallments  ?? collect();
$upcoming        = $upcomingInstallments ?? collect();

// ── JS chart data ────────────────────────────
$donutLabels = [];
$donutData   = [];
$donutColors = [];
foreach ($cats as $key => $meta) {
    if (($catTotals[$key] ?? 0) > 0) {
        $donutLabels[] = $meta['label'];
        $donutData[]   = $catTotals[$key];
        $donutColors[] = $meta['color'];
    }
}

$exportFileName = 'ZamarValley_FinanceReport_' . now()->format('Y-m-d') . '.xlsx';


@endphp

<div class="fr-wrap">

{{-- ══════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════ --}}
<div class="fr-header">
    <div style="position:relative;z-index:1;">
        <p class="fr-header-title">Finance Report</p>
        <p class="fr-header-sub">
            Zamar Valley Real Estate &nbsp;·&nbsp;
            Generated: {{ now()->format('d M Y, h:i A') }}
            @if(request('date_from') || request('date_to'))
                &nbsp;·&nbsp; Filtered: {{ request('date_from','—') }} to {{ request('date_to','—') }}
            @endif
        </p>
    </div>
    <div class="fr-header-actions no-print">
        <button class="btn-soft" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
            </svg>
            Print PDF
        </button>
        <button class="btn-excel" id="exportExcelBtn" onclick="exportToExcel()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Export Excel
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════
     STAT CARDS
══════════════════════════════════════ --}}
<div class="stat-grid">

    {{-- 1. Total Collected (plot-price categories only) --}}
    <div class="stat-card" style="--stat-color:#1e3a8a; --stat-bg:#eff6ff;">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#1e3a8a">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Total Collected</div>
            <div class="stat-value">PKR {{ number_format($totalPlotReceived) }}</div>
            <div class="stat-sub">plot price payments (all time)</div>
        </div>
    </div>

    {{-- 2. Pending Balance --}}
    <div class="stat-card" style="--stat-color:#dc2626; --stat-bg:#fef2f2;">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#dc2626">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Remaining Balance</div>
            <div class="stat-value">PKR {{ number_format($totalPending) }}</div>
            <div class="stat-sub">across all bookings</div>
        </div>
    </div>

    {{-- 3. Overdue (pulses red if any) --}}
    <div class="stat-card" style="
        --stat-color:#e11d48; --stat-bg:#fff1f2;
        {{ $overduePayments->count() > 0 ? 'border:2px solid #fca5a5;background:#fff8f8;animation:pulse-red 2s infinite;' : '' }}
    ">
        <div class="stat-icon" style="{{ $overduePayments->count() > 0 ? 'background:#fef2f2;' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#e11d48">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Overdue</div>
            <div class="stat-value">{{ $overduePayments->count() }}</div>
            @if($overduePayments->count() > 0)
                <div class="stat-sub" style="color:#dc2626;font-weight:700;">
                    ⚠ Requires attention!
                    <a href="#overduePanel" style="color:#dc2626;text-decoration:underline;margin-left:4px;">View ↓</a>
                </div>
            @else
                <div class="stat-sub">All installments on track ✓</div>
            @endif
        </div>
    </div>

    {{-- 4. Avg Payment --}}
    <div class="stat-card" style="--stat-color:#16a34a; --stat-bg:#f0fdf4;">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#16a34a">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Avg. Payment</div>
            <div class="stat-value">PKR {{ number_format($avgPayment) }}</div>
            <div class="stat-sub">per paid transaction</div>
        </div>
    </div>

    {{-- 5. Total Project Value --}}
    <div class="stat-card" style="--stat-color:#6366f1; --stat-bg:#eef2ff;">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#6366f1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Contract Value</div>
            <div class="stat-value" style="font-size:16px;">PKR {{ number_format($totalPriceAllBookings) }}</div>
            <div class="stat-sub">agreed booking price (excl. cancelled)</div>
        </div>
    </div>

    {{-- 6. Discounts Given --}}
    @if(($totalDiscount ?? 0) > 0)
    <div class="stat-card" style="--stat-color:#d97706; --stat-bg:#fffbeb; border-color:#fde68a;">
        <div class="stat-icon" style="background:#fffbeb;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#d97706">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185zM9.75 9h.008v.008H9.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 4.5h.008v.008h-.008V13.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Discounts Given</div>
            <div class="stat-value" style="color:#d97706;font-size:16px;">- PKR {{ number_format($totalDiscount) }}</div>
            <div class="stat-sub">
                {{ $discountedBookingsCount ?? 0 }} booking{{ ($discountedBookingsCount ?? 0) != 1 ? 's' : '' }}
                &nbsp;·&nbsp; Gross was PKR {{ number_format($grossProjectValue ?? $totalPriceAllBookings) }}
            </div>
        </div>
    </div>
    @endif

    {{-- 7. Cancelled Bookings --}}
    <div class="stat-card" style="--stat-color:#dc2626; --stat-bg:#fef2f2;{{ ($cancelledCount ?? 0) > 0 ? 'border-color:#fecaca;' : '' }}">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#dc2626">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Cancelled Bookings</div>
            <div class="stat-value" style="color:{{ ($cancelledCount ?? 0) > 0 ? '#dc2626' : '#94a3b8' }};">{{ $cancelledCount ?? 0 }}</div>
            <div class="stat-sub">
                @if(($cancelledCollected ?? 0) > 0)
                    PKR {{ number_format($cancelledCollected) }} received
                @else
                    No payments collected
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ══ CANCELLATION PANEL (shown when cancellations exist) ══════════ --}}
@if(($cancelledCount ?? 0) > 0)
<div style="background:#fff;border:1px solid #fecaca;border-radius:14px;padding:18px 22px;margin-bottom:20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;box-shadow:0 2px 8px rgba(220,38,38,.05);">
    <div style="width:42px;height:42px;background:#fef2f2;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#dc2626" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:11px;font-weight:800;color:#991b1b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Cancellation Summary — All Time</div>
        <div style="display:flex;gap:28px;flex-wrap:wrap;">
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Bookings Cancelled</div>
                <div style="font-size:22px;font-weight:800;color:#dc2626;line-height:1.2;">{{ $cancelledCount }}</div>
            </div>
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Received Before Cancel</div>
                <div style="font-size:22px;font-weight:800;color:#0f172a;line-height:1.2;">PKR {{ number_format($cancelledCollected) }}</div>
                <div style="font-size:10px;color:#94a3b8;">actual cash in</div>
            </div>
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Agreed Refund</div>
                <div style="font-size:22px;font-weight:800;color:#b45309;line-height:1.2;">PKR {{ number_format($cancelledRefundTotal) }}</div>
                <div style="font-size:10px;color:#94a3b8;">to be returned</div>
            </div>
            @php $frNetRetained = ($cancelledCollected ?? 0) - ($cancelledRefundTotal ?? 0); @endphp
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Net Retained</div>
                <div style="font-size:22px;font-weight:800;color:{{ $frNetRetained >= 0 ? '#16a34a' : '#dc2626' }};line-height:1.2;">PKR {{ number_format(abs($frNetRetained)) }}</div>
                <div style="font-size:10px;color:#94a3b8;">{{ $frNetRetained >= 0 ? 'retained by society' : 'excess refund owed' }}</div>
            </div>
        </div>
    </div>
    <a href="{{ route('index.booking', ['status'=>'cancelled']) }}"
       style="font-size:11px;font-weight:700;color:#dc2626;text-decoration:none;white-space:nowrap;padding:8px 14px;border:1.5px solid #fecaca;border-radius:9px;background:#fef2f2;">
        View Cancelled →
    </a>
</div>
@endif

{{-- ══ DISCOUNT SUMMARY (shown when discounts exist) ═══════════════ --}}
@if(($totalDiscount ?? 0) > 0)
<div style="background:#fff;border:1.5px solid #fde68a;border-radius:14px;padding:18px 22px;margin-bottom:20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;box-shadow:0 2px 8px rgba(217,119,6,.05);">
    <div style="width:42px;height:42px;background:#fffbeb;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#d97706" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185zM9.75 9h.008v.008H9.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 4.5h.008v.008h-.008V13.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:11px;font-weight:800;color:#92400e;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Discount Summary — All Active Bookings</div>
        <div style="display:flex;gap:28px;flex-wrap:wrap;">
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Gross Value</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;line-height:1.2;">PKR {{ number_format($grossProjectValue ?? $totalPriceAllBookings) }}</div>
                <div style="font-size:10px;color:#94a3b8;">before plot discounts</div>
            </div>
            @if(($totalPlotDiscounts ?? 0) > 0)
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Plot Discounts</div>
                <div style="font-size:20px;font-weight:800;color:#d97706;line-height:1.2;">- PKR {{ number_format($totalPlotDiscounts) }}</div>
                <div style="font-size:10px;color:#94a3b8;">{{ $discountedBookingsCount ?? 0 }} booking{{ ($discountedBookingsCount ?? 0) != 1 ? 's' : '' }}</div>
            </div>
            @endif
            @if(($totalPaymentDiscounts ?? 0) > 0)
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Settlement Discounts</div>
                <div style="font-size:20px;font-weight:800;color:#ea580c;line-height:1.2;">- PKR {{ number_format($totalPaymentDiscounts) }}</div>
                <div style="font-size:10px;color:#94a3b8;">{{ $settlementDiscountCount ?? 0 }} early settlement{{ ($settlementDiscountCount ?? 0) != 1 ? 's' : '' }}</div>
            </div>
            @endif
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Total Discounted</div>
                <div style="font-size:20px;font-weight:800;color:#d97706;line-height:1.2;">- PKR {{ number_format($totalDiscount) }}</div>
                <div style="font-size:10px;color:#94a3b8;">both types combined</div>
            </div>
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Net Contract Value</div>
                <div style="font-size:20px;font-weight:800;color:#1e3a8a;line-height:1.2;">PKR {{ number_format($totalPriceAllBookings) }}</div>
                <div style="font-size:10px;color:#94a3b8;">agreed booking price</div>
            </div>
            @php
                $discountPct = ($grossProjectValue ?? 0) > 0
                    ? round(($totalDiscount / $grossProjectValue) * 100, 1) : 0;
            @endphp
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Discount Rate</div>
                <div style="font-size:20px;font-weight:800;color:#d97706;line-height:1.2;">{{ $discountPct }}%</div>
                <div style="font-size:10px;color:#94a3b8;">of gross value</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════
     FILTER BAR
══════════════════════════════════════ --}}
<div class="filter-card no-print">
    <div class="filter-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
        </svg>
        Filter Report
    </div>
    <form method="GET" action="{{ route('finance.report') }}">
        <div class="filter-grid">

            <div class="filter-group">
                <label>Date Range</label>
                <div class="date-range-group">
                    <input type="date" name="date_from" class="filter-control" value="{{ request('date_from') }}">
                    <span class="date-sep">→</span>
                    <input type="date" name="date_to" class="filter-control" value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="filter-group">
                <label>Payment Category</label>
                <select name="category" class="filter-control">
                    <option value="">All Categories</option>
                    <option value="token"          {{ request('category') == 'token'          ? 'selected' : '' }}>Token Amount</option>
                    <option value="down_payment"   {{ request('category') == 'down_payment'   ? 'selected' : '' }}>Down Payment</option>
                    <option value="processing_fee" {{ request('category') == 'processing_fee' ? 'selected' : '' }}>Processing Fee</option>
                    <option value="installment"    {{ request('category') == 'installment'    ? 'selected' : '' }}>Installments</option>
                    <option value="fine"           {{ request('category') == 'fine'           ? 'selected' : '' }}>Fine / Penalty</option>
                    <option value="others"         {{ request('category') == 'others'         ? 'selected' : '' }}>Others</option>
                    <option value="plot_balance"   {{ request('category') == 'plot_balance'   ? 'selected' : '' }}>Plot Balance</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Block / Plot</label>
                <select name="block" class="filter-control">
                    <option value="">All Blocks</option>
                    @foreach($blocks as $block)
                        <option value="{{ $block }}" {{ request('block') == $block ? 'selected' : '' }}>
                            Block {{ $block }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Payment Mode</label>
                <select name="payment_mode" class="filter-control">
                    <option value="">All Modes</option>
                    <option value="cash"          {{ request('payment_mode') == 'cash'          ? 'selected' : '' }}>Cash</option>
                    <option value="bank_transfer"  {{ request('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cheque"         {{ request('payment_mode') == 'cheque'        ? 'selected' : '' }}>Cheque</option>
                    <option value="online"         {{ request('payment_mode') == 'online'        ? 'selected' : '' }}>Online</option>
                </select>
            </div>

            <div class="filter-group">
                <label>&nbsp;</label>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                             stroke="currentColor" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        Apply
                    </button>
                    <a href="{{ route('finance.report') }}" class="btn-reset">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                             stroke="currentColor" style="width:13px;height:13px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                        </svg>
                        Reset
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>

{{-- ══════════════════════════════════════
     CHARTS ROW
══════════════════════════════════════ --}}
<div class="charts-row">

    <div class="panel">
        <div class="panel-head">
            <div>
                <p class="panel-title">Monthly Collections</p>
                <p class="panel-sub">Last 12 months — Zamar Valley receipts only (PKR)</p>
            </div>
            <div style="font-size:11px;font-weight:700;color:#94a3b8;">
                Peak: <span style="color:#1e3a8a;">PKR {{ number_format(max(array_column($months, 'total'))) }}</span>
            </div>
        </div>
        <div class="panel-body" style="padding:16px 20px;">
            <div style="position:relative; height:220px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div>
                <p class="panel-title">By Category</p>
                <p class="panel-sub">Zamar collections only</p>
            </div>
        </div>
        <div class="panel-body" style="position:relative;">
            <div style="position:relative; height:180px; width:180px; margin:0 auto 20px;">
                <canvas id="catDonut"></canvas>
            </div>
            <div class="cat-breakdown">
                @foreach($catTotals as $key => $amt)
                    @if($amt > 0)
                    <div class="cat-row">
                        <span class="cat-dot" style="background:{{ $cats[$key]['color'] }};"></span>
                        <span class="cat-name">{{ $cats[$key]['label'] }}</span>
                        <div class="cat-bar-wrap">
                            <div class="cat-bar-fill"
                                 style="width:{{ $maxCat > 0 ? round(($amt / $maxCat) * 100) : 0 }}%;
                                        background:{{ $cats[$key]['color'] }};"></div>
                        </div>
                        <span class="cat-amt">PKR {{ number_format($amt) }}</span>
                        <span class="cat-pct">{{ $totalCollected > 0 ? round(($amt / $totalCollected) * 100) : 0 }}%</span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     PAYMENT MODE TILES
══════════════════════════════════════ --}}
@php
$modeStyles = [
    'cash'          => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0',
                        'icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z'],
    'bank_transfer' => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '#bfdbfe',
                        'icon' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z'],
    'cheque'        => ['bg' => '#fefce8', 'color' => '#ca8a04', 'border' => '#fde68a',
                        'icon' => 'M16.5 6v.75a3.75 3.75 0 01-7.5 0V6a3.75 3.75 0 017.5 0zM11.25 9.75h1.5m-1.5 0a3.375 3.375 0 00-3.375 3.375v.75m4.875-4.125a3.375 3.375 0 013.375 3.375v.75M6 15.75h12M6 18.75h12'],
    'online'        => ['bg' => '#fdf4ff', 'color' => '#7c3aed', 'border' => '#e9d5ff',
                        'icon' => 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3'],
];
@endphp
<div class="mode-tiles" style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    @foreach($modeTotals as $key => $amt)
        @php $ms = $modeStyles[$key]; @endphp
        <div style="background:{{ $ms['bg'] }};border:1px solid {{ $ms['border'] }};border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;background:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid {{ $ms['border'] }};">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="{{ $ms['color'] }}" style="width:20px;height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ms['icon'] }}"/>
                </svg>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">{{ $modes[$key] }}</div>
                <div style="font-size:15px;font-weight:800;color:{{ $ms['color'] }};margin-top:2px;">PKR {{ number_format($amt) }}</div>
                <div style="font-size:10px;color:#94a3b8;margin-top:1px;">
                    {{ $payments->where('payment_type', $key)->where('status', 'paid')->count() }} txns
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ══════════════════════════════════════
     OVERDUE INSTALLMENTS
══════════════════════════════════════ --}}
<div class="panel" id="overduePanel" style="border-color:{{ $overduePayments->count() > 0 ? '#fecaca' : '#e4e9f2' }};">
    <div class="panel-head" style="background:{{ $overduePayments->count() > 0 ? '#fff8f8' : '#fafbfc' }};border-bottom-color:{{ $overduePayments->count() > 0 ? '#fecaca' : '#f1f5f9' }};">
        <div>
            <p class="panel-title" style="color:{{ $overduePayments->count() > 0 ? '#dc2626' : '#0f172a' }};">
                ⚠ Overdue Installments
            </p>
            <p class="panel-sub">
                {{ $overduePayments->count() > 0 ? 'Payments past grace period — requires immediate attention' : 'No overdue installments — all customers are on track' }}
            </p>
        </div>
        <span style="background:{{ $overduePayments->count() > 0 ? '#fef2f2' : '#f0fdf4' }};border:1px solid {{ $overduePayments->count() > 0 ? '#fecaca' : '#bbf7d0' }};color:{{ $overduePayments->count() > 0 ? '#dc2626' : '#16a34a' }};padding:5px 14px;border-radius:20px;font-size:11px;font-weight:800;">
            {{ $overduePayments->count() > 0 ? $overduePayments->count() . ' overdue' : '✓ All clear' }}
        </span>
    </div>

    @if($overduePayments->count() > 0)
    <div class="panel-body" style="padding:16px;">
        <div class="overdue-stats">
            <div class="ov-tile">
                <div class="ov-tile-val">{{ $overduePayments->count() }}</div>
                <div class="ov-tile-lbl">Overdue Records</div>
            </div>
            <div class="ov-tile">
                <div class="ov-tile-val">PKR {{ number_format($overduePayments->sum('monthly_installment')) }}</div>
                <div class="ov-tile-lbl">Total Overdue Amount</div>
            </div>
            <div class="ov-tile">
                <div class="ov-tile-val">{{ $overduePayments->max('days_overdue') ?? 0 }} days</div>
                <div class="ov-tile-lbl">Max Days Overdue</div>
            </div>
        </div>
        <div class="fr-table-wrap">
            <table class="fr-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Plot</th>
                        <th>Installment No.</th>
                        <th>Due Date</th>
                        <th>Amount Due</th>
                        <th>Days Overdue</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overduePayments as $ov)
                    <tr class="overdue-row">
                        <td style="color:#94a3b8;font-size:11px;">{{ $loop->iteration }}</td>
                        <td>
                            <div style="font-weight:700;font-size:12px;">{{ $ov->booking->customer->name }}</div>
                            <div style="font-size:10px;color:#94a3b8;">{{ $ov->booking->customer->phone }}</div>
                        </td>
                        <td>
                            <span style="font-weight:700;color:#1e3a8a;">Plot #{{ $ov->booking->plot->plot_number }}</span>
                            <span style="font-size:10px;color:#94a3b8;"> — {{ $ov->booking->plot->block }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-size:11px;font-weight:800;background:#fff1f2;border:1px solid #fecaca;color:#dc2626;padding:2px 9px;border-radius:8px;">
                                #{{ $ov->next_installment }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:12px;font-weight:600;color:#dc2626;">
                                {{ \Carbon\Carbon::parse($ov->due_date)->format('d M Y') }}
                            </div>
                            <div style="font-size:10px;color:#94a3b8;">
                                {{ \Carbon\Carbon::parse($ov->due_date)->diffForHumans() }}
                            </div>
                        </td>
                        <td>
                            <strong style="color:#dc2626;font-size:13px;">
                                PKR {{ number_format($ov->monthly_installment) }}
                            </strong>
                        </td>
                        <td><span class="overdue-days">{{ $ov->days_overdue }} days</span></td>
                        <td>
                            <a href="{{ route('ledger.view', $ov->booking->id) }}"
                               class="pill pill-paid" style="text-decoration:none;font-size:10px;">
                                View Ledger
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div style="text-align:center;padding:40px 20px;">
        <div style="font-size:2.5rem;margin-bottom:10px;">✅</div>
        <div style="font-size:13px;font-weight:700;color:#16a34a;">All installments are on track</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">No overdue payments found across all active bookings</div>
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════
     UPCOMING INSTALLMENTS
══════════════════════════════════════ --}}
@if($upcoming->count() > 0)
<div class="panel" style="border-color:#bfdbfe;margin-bottom:20px;">
    <div class="panel-head" style="background:#f8fbff;border-bottom-color:#bfdbfe;">
        <div>
            <p class="panel-title" style="color:#1e3a8a;">⏰ Upcoming Installments</p>
            <p class="panel-sub">Due within the next 30 days</p>
        </div>
        <span style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:800;">
            {{ $upcoming->count() }} upcoming
        </span>
    </div>
    <div class="panel-body" style="padding:16px;">
        <div class="fr-table-wrap">
            <table class="fr-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Plot</th>
                        <th>Installment No.</th>
                        <th>Due Date</th>
                        <th>Amount Due</th>
                        <th>Days Until Due</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcoming as $up)
                    <tr style="background:{{ $up->days_until_due <= 3 ? '#fffbeb' : '#fff' }};">
                        <td style="color:#94a3b8;font-size:11px;">{{ $loop->iteration }}</td>
                        <td>
                            <div style="font-weight:700;font-size:12px;">{{ $up->booking->customer->name }}</div>
                            <div style="font-size:10px;color:#94a3b8;">{{ $up->booking->customer->phone }}</div>
                        </td>
                        <td>
                            <span style="font-weight:700;color:#1e3a8a;">Plot #{{ $up->booking->plot->plot_number }}</span>
                            <span style="font-size:10px;color:#94a3b8;"> — {{ $up->booking->plot->block }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-size:11px;font-weight:800;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:2px 9px;border-radius:8px;">
                                #{{ $up->next_installment }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:12px;font-weight:600;color:#1e3a8a;">
                                {{ \Carbon\Carbon::parse($up->due_date)->format('d M Y') }}
                            </div>
                            <div style="font-size:10px;color:#94a3b8;">
                                {{ \Carbon\Carbon::parse($up->due_date)->diffForHumans() }}
                            </div>
                        </td>
                        <td>
                            <strong style="color:#1e3a8a;font-size:13px;">
                                PKR {{ number_format($up->monthly_installment) }}
                            </strong>
                        </td>
                        <td>
                            @if($up->days_until_due <= 3)
                                <span style="font-size:10px;font-weight:800;background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:2px 9px;border-radius:8px;">
                                    {{ $up->days_until_due }}d ⚡
                                </span>
                            @else
                                <span style="font-size:10px;font-weight:700;color:#1d4ed8;background:#eff6ff;padding:2px 9px;border-radius:8px;">
                                    {{ $up->days_until_due }}d
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('ledger.view', $up->booking->id) }}"
                               style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:4px 10px;border-radius:7px;font-size:10px;font-weight:700;text-decoration:none;">
                                View Ledger
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════
     FULL TRANSACTIONS TABLE
══════════════════════════════════════ --}}
<div class="panel" id="transactionsTable">
    <div class="panel-head">
        <div>
            <p class="panel-title">All Transactions</p>
            <p class="panel-sub">
                {{ $payments->count() }} records
                {{ request()->hasAny(['date_from','date_to','category','block','payment_mode']) ? '(filtered)' : '' }}
                &nbsp;·&nbsp;
                <span style="color:#92400e;font-style:italic;">External/historical records excluded</span>
            </p>
        </div>
        <span style="font-size:12px;font-weight:700;color:#94a3b8;align-self:center;">
            Paid Total: <strong style="color:#16a34a;">PKR {{ number_format($totalCollected) }}</strong>
        </span>
    </div>
    <div class="fr-table-wrap">
        <table class="fr-table" id="mainTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Receipt No.</th>
                    <th>Customer</th>
                    <th>Plot</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Due Date</th>
                    <th>Paid Date</th>
                    <th>Status</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments->sortByDesc('paid_date') as $p)
                <tr>
                    <td style="color:#94a3b8;font-size:11px;">{{ $loop->iteration }}</td>
                    <td>
                        <strong style="font-size:12px;color:#1e3a8a;font-family:'DM Mono',monospace;">
                            {{ $p->receipt_no ?? '—' }}
                        </strong>
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:12px;">{{ $p->booking->customer->name ?? '—' }}</div>
                        <div style="font-size:10px;color:#94a3b8;">{{ $p->booking->customer_booking_id ?? '' }}</div>
                    </td>
                    <td>
                        <span style="font-weight:700;color:#0f172a;">Plot #{{ $p->booking->plot->plot_number ?? '—' }}</span>
                        <span style="font-size:10px;color:#94a3b8;"> {{ $p->booking->plot->block ?? '' }}</span>
                    </td>
                    <td>
                        @php
                            $pillMap = [
                                'token'          => 'pill-token',
                                'down_payment'   => 'pill-down',
                                'processing_fee' => 'pill-proc',
                                'installment'    => 'pill-install',
                                'fine'           => 'pill-fine',
                                'others'         => 'pill-other',
                                'plot_balance'   => 'pill-balance',
                            ];
                            $pillClass = $pillMap[strtolower($p->payment_category ?? '')] ?? 'pill-other';
                        @endphp
                        <span class="pill {{ $pillClass }}">
                            {{ ucwords(str_replace('_', ' ', $p->payment_category ?? '—')) }}
                        </span>
                        @if($p->installment_no)
                            <span style="font-size:10px;color:#94a3b8;margin-left:4px;">#{{ $p->installment_no }}</span>
                        @endif
                    </td>
                    <td>
                        <strong style="color:#16a34a;font-size:13px;font-family:'DM Mono',monospace;">
                            PKR {{ number_format($p->amount_paid) }}
                        </strong>
                    </td>
                    <td>
                        @php
                            $modeMap = [
                                'cash'          => 'pill-cash',
                                'bank_transfer' => 'pill-bank',
                                'cheque'        => 'pill-cheque',
                                'online'        => 'pill-online',
                            ];
                        @endphp
                        <span class="pill {{ $modeMap[strtolower($p->payment_type ?? '')] ?? 'pill-other' }}">
                            {{ ucfirst(str_replace('_', ' ', $p->payment_type ?? '—')) }}
                        </span>
                    </td>
                    <td>
                        @if($p->due_date)
                            <div style="font-size:12px;font-weight:600;color:#0f172a;">
                                {{ \Carbon\Carbon::parse($p->due_date)->format('d M Y') }}
                            </div>
                            @if($p->status !== 'paid' && \Carbon\Carbon::parse($p->due_date)->isPast())
                                <div style="font-size:10px;font-weight:700;color:#dc2626;">OVERDUE</div>
                            @else
                                <div style="font-size:10px;color:#94a3b8;">
                                    {{ \Carbon\Carbon::parse($p->due_date)->diffForHumans() }}
                                </div>
                            @endif
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($p->paid_date)
                            <div style="font-size:12px;font-weight:600;">
                                {{ \Carbon\Carbon::parse($p->paid_date)->format('d M Y') }}
                            </div>
                            <div style="font-size:10px;color:#94a3b8;">
                                {{ \Carbon\Carbon::parse($p->paid_date)->diffForHumans() }}
                            </div>
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="pill pill-{{ strtolower($p->status ?? 'pending') }}">
                            <span class="pill-dot"></span>
                            {{ ucfirst($p->status ?? 'Pending') }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('payment.receipt', $p->id) }}" target="_blank"
                           style="background:#fff0f0;border:1px solid #fecaca;color:#dc2626;padding:4px 10px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" style="width:12px;height:12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                            PDF
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                        </svg>
                        <p style="font-weight:700;font-size:13px;margin:0 0 4px;">No transactions found</p>
                        <p style="font-size:12px;margin:0;">Try adjusting the filters above.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($payments->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align:right;color:#94a3b8;font-size:10px;letter-spacing:.7px;font-weight:700;">
                        PAID TOTAL
                    </td>
                    <td style="color:#16a34a;font-size:15px;font-family:'DM Mono',monospace;font-weight:800;">
                        PKR {{ number_format($totalCollected) }}
                    </td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

</div>{{-- /fr-wrap --}}

@endsection

@push('scripts')
<script>
window.ZV_FINANCE = {
    monthLabels: @json(array_column($months, 'label')),
    monthData:   @json(array_column($months, 'total')),
    catLabels:   @json($donutLabels),
    catData:     @json($donutData),
    catColors:   @json($donutColors),
    fileName:    @json($exportFileName),
};
</script>
@endpush
