@extends('layouts.index')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.rpt-wrap { padding: 1.75rem; }

/* ── Header ── */
.rpt-header {
    background: linear-gradient(135deg, #0a0f1e 0%, #0f2460 50%, #1a3a9c 100%);
    border-radius: 18px; padding: 26px 32px; margin-bottom: 22px;
    position: relative; overflow: hidden;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;
}
.rpt-header::before {
    content: 'BOOKINGS'; position: absolute; right: -20px; top: -10px;
    font-size: 100px; font-weight: 900; color: rgba(255,255,255,.03);
    letter-spacing: -4px; line-height: 1; pointer-events: none;
}
.rpt-header-title { font-size: 1.25rem; font-weight: 800; color: #fff; margin: 0; position:relative;z-index:1; }
.rpt-header-sub   { font-size: 12px; color: rgba(255,255,255,.45); margin: 5px 0 0; position:relative;z-index:1; }
.rpt-header-actions { display: flex; gap: 10px; position: relative; z-index: 1; flex-wrap: wrap; }

/* ── Buttons ── */
.btn-soft-rpt {
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); color: #fff;
    padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 7px; cursor: pointer;
    transition: all .15s; text-decoration: none; white-space: nowrap;
}
.btn-soft-rpt:hover { background: rgba(255,255,255,.22); color:#fff; }
.btn-soft-rpt svg { width: 14px; height: 14px; }
.btn-navy { background: #1e3a8a; color: #fff; border: none; padding: 9px 18px; border-radius: 9px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px; cursor: pointer; white-space: nowrap; }
.btn-navy:hover { background: #1e40af; }
.btn-reset { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 9px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; text-decoration: none; }
.btn-reset:hover { background: #e2e8f0; }

/* ── Stat grid ── */
.stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 20px; }
@media(max-width:1100px){ .stat-grid{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:560px) { .stat-grid{ grid-template-columns:1fr; } }
.stat-card {
    background: #fff; border-radius: 14px; border: 1px solid #e4e9f2;
    padding: 20px 22px; box-shadow: 0 2px 8px rgba(15,23,42,.05);
    display: flex; align-items: center; gap: 16px;
    position: relative; overflow: hidden;
    transition: transform .15s, box-shadow .15s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(15,23,42,.08); }
.stat-card::after {
    content:''; position:absolute; right:-20px; top:-20px;
    width:80px; height:80px; border-radius:50%;
    background:var(--sc,#3b82f6); opacity:.06;
}
.stat-icon-box { width:48px; height:48px; border-radius:13px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-icon-box svg { width:24px; height:24px; }
.stat-info-label { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.7px; }
.stat-info-value { font-size:20px; font-weight:800; color:#0f172a; margin:4px 0 2px; line-height:1; }
.stat-info-value.sm { font-size:15px; }
.stat-info-sub  { font-size:11px; color:#94a3b8; }

/* ── Filter card ── */
.filter-card {
    background:#fff; border-radius:14px; border:1px solid #e4e9f2;
    padding:18px 22px; margin-bottom:20px;
    box-shadow:0 2px 8px rgba(15,23,42,.04);
}
.filter-heading { font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.8px; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
.filter-heading svg { width:14px; height:14px; }
.filter-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; align-items:end; }
.filter-label { font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.5px; display:block; margin-bottom:5px; }
.filter-control { border:1.5px solid #e2e8f0; border-radius:9px; font-size:13px; padding:9px 12px; width:100%; background:#fff; color:#0f172a; transition:border-color .15s; font-family:inherit; outline:none; }
.filter-control:focus { border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.08); }
.filter-actions { display:flex; gap:8px; grid-column: 1 / -1; }

/* ── Charts ── */
.chart-card {
    background:#fff; border-radius:14px; border:1px solid #e4e9f2;
    box-shadow:0 2px 8px rgba(15,23,42,.04); overflow:hidden; margin-bottom:20px;
}
.chart-card-head { display:flex; justify-content:space-between; align-items:center; padding:16px 22px; }
.chart-card-title { font-size:13px; font-weight:800; color:#0f172a; margin:0; }
.chart-card-sub   { font-size:11px; color:#94a3b8; margin:2px 0 0; }
.chart-card-divider { height:1px; background:#f1f5f9; }
.chart-card-body  { padding:20px 22px; }
.chart-tag { font-size:10px; font-weight:800; color:#1d4ed8; background:#eff6ff; border:1px solid #bfdbfe; padding:3px 10px; border-radius:20px; white-space:nowrap; }

/* ── Table card ── */
.table-card { background:#fff; border-radius:14px; border:1px solid #e4e9f2; box-shadow:0 2px 8px rgba(15,23,42,.04); overflow:hidden; margin-bottom:20px; }
.table-card-head { padding:16px 22px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
.table-card-title { font-size:13px; font-weight:800; color:#0f172a; margin:0; }
.table-card-sub   { font-size:11px; color:#94a3b8; margin:2px 0 0; }

/* ── Report table ── */
.rpt-table { width:100%; border-collapse:collapse; min-width:900px; }
.rpt-table thead th { font-size:10px; text-transform:uppercase; letter-spacing:.7px; color:#94a3b8; font-weight:700; background:#fafbfc; border-bottom:2px solid #f1f5f9; padding:11px 14px; white-space:nowrap; }
.rpt-table tbody td { padding:11px 14px; border-bottom:1px solid #f8fafc; font-size:12.5px; vertical-align:middle; }
.rpt-table tbody tr:last-child td { border-bottom:none; }
.rpt-table tbody tr:hover { background:#f9fafb; }
.rpt-table tfoot td { padding:12px 14px; background:#f8fafc; border-top:2px solid #e8edf3; font-weight:800; }

/* ── Status pills ── */
.spill { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
.spill-dot { width:6px; height:6px; border-radius:50%; }
.spill-active      { background:#eff6ff;color:#1d4ed8; } .spill-active .spill-dot      { background:#3b82f6; }
.spill-completed   { background:#dcfce7;color:#15803d; } .spill-completed .spill-dot   { background:#16a34a; }
.spill-transferred { background:#fdf4ff;color:#7c3aed; } .spill-transferred .spill-dot { background:#7c3aed; }
.spill-cancelled   { background:#fef2f2;color:#dc2626; } .spill-cancelled .spill-dot   { background:#dc2626; }
.spill-pending     { background:#fef9c3;color:#92400e; } .spill-pending .spill-dot     { background:#d97706; }
.spill-partial-transferred { background:#fff7ed;color:#ea580c; } .spill-partial-transferred .spill-dot { background:#ea580c; }
.spill-swapped     { background:#f0fdf4;color:#0f766e; } .spill-swapped .spill-dot     { background:#0d9488; }

/* ── Summary pills ── */
.sum-pill { font-size:11px; font-weight:700; padding:5px 12px; border-radius:20px; background:#f1f5f9; color:#0f172a; white-space:nowrap; }
.sum-pill.blue { background:#eff6ff; color:#1d4ed8; }
.sum-pill.green { background:#f0fdf4; color:#16a34a; }
.sum-pill.red  { background:#fef2f2; color:#dc2626; }

/* ── Mobile booking cards (< 768px) ── */
.mobile-cards { display:none; }
.mb-card {
    background:#fff; border:1px solid #e4e9f2; border-radius:14px;
    padding:16px; margin-bottom:12px;
    box-shadow:0 2px 6px rgba(15,23,42,.05);
}
.mb-card-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; gap:8px; }
.mb-ref { font-size:11px; font-weight:800; color:#1e3a8a; font-family:monospace; }
.mb-customer { font-size:13px; font-weight:700; color:#0f172a; margin:2px 0; }
.mb-cnic { font-size:10px; color:#94a3b8; }
.mb-plot { font-size:12px; font-weight:700; color:#0f172a; }
.mb-plot-sub { font-size:10px; color:#94a3b8; margin-top:2px; }
.mb-divider { height:1px; background:#f1f5f9; margin:10px 0; }
.mb-amounts { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
.mb-amt-box { background:#f8fafc; border-radius:8px; padding:8px 10px; text-align:center; }
.mb-amt-lbl { font-size:9px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.5px; display:block; margin-bottom:3px; }
.mb-amt-val { font-size:12px; font-weight:800; }
.mb-footer { display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; }
.mb-plan { font-size:10px; color:#64748b; }
.mb-date { font-size:10px; color:#94a3b8; }
.mb-actions { display:flex; gap:6px; }

/* ── Responsive breakpoints ── */
@media(max-width:767px){
    .rpt-wrap { padding:1rem; }
    .rpt-header { padding:18px 20px; gap:12px; }
    .rpt-header-title { font-size:1rem; }
    .rpt-header-sub { font-size:11px; }
    .rpt-header-actions { width:100%; }

    .table-card-head { flex-direction:column; align-items:flex-start; gap:10px; }
    .sum-pills-row { display:flex; flex-wrap:wrap; gap:6px; }

    /* hide desktop table, show mobile cards */
    .desktop-table { display:none !important; }
    .mobile-cards  { display:block; padding:16px 16px 4px; }
}

@media(max-width:480px){
    .mb-amounts { grid-template-columns:1fr 1fr; }
}

/* ── Print ── */
@media print {
    .no-print { display:none !important; }
    .rpt-wrap { padding:0; }
    .mobile-cards { display:none !important; }
    .desktop-table { display:block !important; }
    .stat-card, .table-card, .chart-card { box-shadow:none; border-color:#e2e8f0; }
    * { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
}
</style>
@endpush

@section('content')

@php
use Carbon\Carbon;

$plotPriceCats = $plotPriceCats ?? ['down_payment','installment','quarterly_installment','plot_balance','others'];

// Recalculate from the passed collection (already filtered)
$activeCount      = $all_bookings->where('status','active')->count();
$completedCount   = $all_bookings->where('status','completed')->count();
$cancelledCount   = $all_bookings->where('status','cancelled')->count();
$transferredCount = $all_bookings->whereIn('status',['transferred','partial_transferred'])->count();
$pendingCount     = $all_bookings->where('status','pending')->count();
$totalBookings    = $all_bookings->count();

// Cancellation breakdown from filtered set
$cancelledRefundFiltered   = $all_bookings->where('status','cancelled')->sum('cancellation_refund');
$cancelledCollectedFiltered = $all_bookings->where('status','cancelled')->sum(function($b) use($plotPriceCats) {
    return $b->payments->where('status','paid')->whereIn('payment_category',$plotPriceCats)->sum('amount_paid');
});

// Monthly data for charts
$months = [];
for ($i = 11; $i >= 0; $i--) {
    $m = Carbon::now()->subMonths($i);
    $monthKey = $m->format('Y-m');
    $months[] = [
        'label' => $m->format('M y'),
        'key'   => $monthKey,
        'count' => $all_bookings->filter(fn($b) =>
            Carbon::parse($b->booking_date)->format('Y-m') === $monthKey
        )->count(),
        // Actual cash collected in this calendar month (based on paid_date, not booking_date)
        'revenue' => $all_bookings->sum(fn($b) =>
            $b->payments
                ->where('status', 'paid')
                ->whereIn('payment_category', $plotPriceCats)
                ->filter(fn($p) => Carbon::parse($p->paid_date)->format('Y-m') === $monthKey)
                ->sum('amount_paid')
        ),
    ];
}
@endphp

<div class="rpt-wrap">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="rpt-header">
    <div>
        <p class="rpt-header-title">Booking Report</p>
        <p class="rpt-header-sub">
            Zamar Valley Real Estate &nbsp;·&nbsp;
            Generated: {{ now()->format('d M Y, h:i A') }}
            @if(request('from_date') || request('to_date'))
                &nbsp;·&nbsp; Filtered: {{ request('from_date','—') }} to {{ request('to_date','—') }}
            @endif
        </p>
    </div>
    <div class="rpt-header-actions no-print">
        <button onclick="window.print()" class="btn-soft-rpt">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
            Print
        </button>
    </div>
</div>

{{-- ══ STAT CARDS ═══════════════════════════════════════════════════ --}}
<div class="stat-grid" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr));">

    <div class="stat-card" style="--sc:#6366f1;">
        <div class="stat-icon-box" style="background:#eff6ff;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        </div>
        <div>
            <div class="stat-info-label">Total Bookings</div>
            <div class="stat-info-value">{{ $totalBookings }}</div>
            <div class="stat-info-sub">
                <span style="color:#16a34a;font-weight:700;">{{ $activeCount }} active</span>
                &nbsp;·&nbsp; {{ $completedCount }} completed
                &nbsp;·&nbsp; {{ $pendingCount }} pending
            </div>
        </div>
    </div>

    <div class="stat-card" style="--sc:#0f172a;">
        <div class="stat-icon-box" style="background:#f8fafc;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
        </div>
        <div>
            <div class="stat-info-label">Total Revenue</div>
            <div class="stat-info-value sm">PKR {{ number_format($totalRevenue) }}</div>
            <div class="stat-info-sub">Agreed contract value (excl. cancelled)</div>
        </div>
    </div>

    <div class="stat-card" style="--sc:#16a34a;">
        <div class="stat-icon-box" style="background:#f0fdf4;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181"/></svg>
        </div>
        <div>
            <div class="stat-info-label">Total Collected</div>
            <div class="stat-info-value sm">PKR {{ number_format($totalCollected) }}</div>
            <div class="stat-info-sub">Active bookings only (excl. cancelled)</div>
        </div>
    </div>

    <div class="stat-card" style="--sc:#dc2626;">
        <div class="stat-icon-box" style="background:#fef2f2;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-info-label">Remaining Balance</div>
            <div class="stat-info-value sm">PKR {{ number_format($totalOutstanding) }}</div>
            <div class="stat-info-sub">Global — all active/pending bookings</div>
        </div>
    </div>

    @if(($totalDiscount ?? 0) > 0)
    <div class="stat-card" style="--sc:#d97706;border-color:#fde68a;">
        <div class="stat-icon-box" style="background:#fffbeb;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185zM9.75 9h.008v.008H9.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 4.5h.008v.008h-.008V13.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
        </div>
        <div>
            <div class="stat-info-label">Discounts Given</div>
            <div class="stat-info-value sm" style="color:#d97706;">PKR {{ number_format($totalDiscount) }}</div>
            <div class="stat-info-sub">
                {{ $discountedCount ?? 0 }} booking{{ ($discountedCount ?? 0) != 1 ? 's' : '' }} discounted
                &nbsp;·&nbsp; Gross: PKR {{ number_format($grossRevenue ?? 0) }}
            </div>
        </div>
    </div>
    @endif

    <div class="stat-card" style="--sc:#dc2626;border-color:{{ $cancelledCount > 0 ? '#fecaca' : '#e4e9f2' }};">
        <div class="stat-icon-box" style="background:#fef2f2;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div>
            <div class="stat-info-label">Cancelled</div>
            <div class="stat-info-value" style="color:{{ $cancelledCount > 0 ? '#dc2626' : '#94a3b8' }};">{{ $cancelledCount }}</div>
            <div class="stat-info-sub">
                @if($cancelledCollectedFiltered > 0)
                    PKR {{ number_format($cancelledCollectedFiltered) }} received
                    @if($cancelledRefundFiltered > 0)
                        &nbsp;·&nbsp;PKR {{ number_format($cancelledRefundFiltered) }} refund
                    @endif
                @else
                    No cancellations in filter
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ══ CANCELLATION DETAIL (only when cancellations exist in filter) ══ --}}
@if($cancelledCount > 0)
<div style="background:#fff;border:1px solid #fecaca;border-radius:14px;padding:16px 22px;margin-bottom:18px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;box-shadow:0 2px 8px rgba(220,38,38,.05);">
    <div style="width:38px;height:38px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#dc2626" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:11px;font-weight:800;color:#991b1b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Cancellation Details (filtered set)</div>
        <div style="display:flex;gap:24px;flex-wrap:wrap;">
            <div><div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Count</div><div style="font-size:17px;font-weight:800;color:#dc2626;">{{ $cancelledCount }}</div></div>
            <div><div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Received Before Cancel</div><div style="font-size:17px;font-weight:800;color:#0f172a;">PKR {{ number_format($cancelledCollectedFiltered) }}</div></div>
            <div><div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Agreed Refund</div><div style="font-size:17px;font-weight:800;color:#b45309;">PKR {{ number_format($cancelledRefundFiltered) }}</div></div>
            @php $rptNetRetained = $cancelledCollectedFiltered - $cancelledRefundFiltered; @endphp
            <div><div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Net Retained</div><div style="font-size:17px;font-weight:800;color:{{ $rptNetRetained >= 0 ? '#16a34a' : '#dc2626' }};">PKR {{ number_format(abs($rptNetRetained)) }}</div><div style="font-size:10px;color:#94a3b8;">{{ $rptNetRetained >= 0 ? 'retained' : 'excess refund' }}</div></div>
        </div>
    </div>
</div>
@endif

{{-- ══ CHARTS ════════════════════════════════════════════════════════ --}}
<div class="chart-card no-print">
    <div class="chart-card-head">
        <div>
            <p class="chart-card-title">Monthly Bookings — Last 12 Months</p>
            <p class="chart-card-sub">Count of bookings recorded per month · latest highlighted</p>
        </div>
        <span class="chart-tag">Bar Chart</span>
    </div>
    <div class="chart-card-divider"></div>
    <div class="chart-card-body"><canvas id="monthlyChart" height="70"></canvas></div>
</div>

<div class="chart-card no-print">
    <div class="chart-card-head">
        <div>
            <p class="chart-card-title">Monthly Collections — Last 12 Months</p>
            <p class="chart-card-sub">Actual cash received per month across all bookings (PKR)</p>
        </div>
        <span class="chart-tag">Line Chart</span>
    </div>
    <div class="chart-card-divider"></div>
    <div class="chart-card-body"><canvas id="revenueChart" height="70"></canvas></div>
</div>

{{-- ══ FILTERS ═══════════════════════════════════════════════════════ --}}
<div class="filter-card no-print">
    <div class="filter-heading">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
        Filter Records
    </div>
    <form method="GET" action="{{ route('booking.reports') }}">
        <div class="filter-grid">
            <div>
                <label class="filter-label">From Date</label>
                <input type="date" name="from_date" class="filter-control" value="{{ request('from_date') }}">
            </div>
            <div>
                <label class="filter-label">To Date</label>
                <input type="date" name="to_date" class="filter-control" value="{{ request('to_date') }}">
            </div>
            <div>
                <label class="filter-label">Booking Type</label>
                <select name="booking_type" class="filter-control">
                    <option value="">All Types</option>
                    <option value="First Allotment" {{ request('booking_type')=='First Allotment'?'selected':'' }}>First Allotment</option>
                    <option value="Transfer"        {{ request('booking_type')=='Transfer'?'selected':'' }}>Transfer</option>
                </select>
            </div>
            <div>
                <label class="filter-label">Status</label>
                <select name="status" class="filter-control">
                    <option value="">All Status</option>
                    <option value="active"      {{ request('status')=='active'?'selected':'' }}>Active</option>
                    <option value="completed"   {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                    <option value="pending"     {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="transferred" {{ request('status')=='transferred'?'selected':'' }}>Transferred</option>
                    <option value="cancelled"   {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="filter-label">Payment Plan</label>
                <select name="payment_plan" class="filter-control">
                    <option value="">All Plans</option>
                    <option value="installment" {{ request('payment_plan')=='installment'?'selected':'' }}>Installment</option>
                    <option value="cash"        {{ request('payment_plan')=='cash'?'selected':'' }}>Full Cash</option>
                </select>
            </div>
            <div class="filter-actions" style="padding-top:1px;">
                <button type="submit" class="btn-navy">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    Apply
                </button>
                <a href="{{ route('booking.reports') }}" class="btn-reset">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ══ TABLE ══════════════════════════════════════════════════════════ --}}
<div class="table-card">
    <div class="table-card-head">
        <div>
            <p class="table-card-title">Booking Records</p>
            <p class="table-card-sub">
                {{ $totalBookings }} records
                {{ request()->hasAny(['from_date','to_date','booking_type','status','payment_plan']) ? '(filtered)' : '' }}
            </p>
        </div>
        <div class="sum-pills-row no-print">
            @if(($totalDiscount ?? 0) > 0)
            <span class="sum-pill" style="background:#fffbeb;color:#d97706;border:1px solid #fde68a;">Gross &nbsp; PKR {{ number_format($grossRevenue ?? $totalRevenue) }}</span>
            <span class="sum-pill" style="background:#fffbeb;color:#d97706;">Discount &nbsp; -PKR {{ number_format($totalDiscount) }}</span>
            @endif
            <span class="sum-pill">Contract &nbsp; PKR {{ number_format($totalRevenue) }}</span>
            <span class="sum-pill green">Collected &nbsp; PKR {{ number_format($totalCollected) }}</span>
            <span class="sum-pill red">Remaining &nbsp; PKR {{ number_format($totalRemaining) }}</span>
        </div>
    </div>

    {{-- Desktop table --}}
    <div class="desktop-table" style="overflow-x:auto;">
        <table class="rpt-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Booking Ref</th>
                    <th>Customer</th>
                    <th>Plot</th>
                    <th>Type</th>
                    <th>Original Price</th>
                    <th>Discount</th>
                    <th>Contract Price</th>
                    <th>Collected</th>
                    <th>Remaining</th>
                    <th>Plan</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th class="no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($all_bookings as $booking)
            @php
                $isClosedBooking = in_array($booking->status, ['transferred','cancelled','swapped','plot_relocated','completed']);

                // Actual collected from plot-price categories
                $collected = $booking->payments
                    ->where('status','paid')
                    ->whereIn('payment_category', $plotPriceCats)
                    ->sum('amount_paid');

                // total_price is already net (base minus plot discount) — do NOT subtract discount again.
                $remaining = $isClosedBooking ? 0 : max(0, (float)$booking->total_price - $collected);

                // Paid installment count
                $paidInstallments = $booking->payments
                    ->where('payment_category','installment')
                    ->where('status','paid')
                    ->count();

                $paidQuarters = $booking->payments
                    ->where('payment_category','quarterly_installment')
                    ->where('status','paid')
                    ->count();
            @endphp
            <tr>
                <td style="color:#94a3b8;font-size:11px;">{{ $loop->iteration }}</td>

                <td>
                    <span style="font-size:11px;font-weight:800;color:#1e3a8a;font-family:monospace;">
                        {{ $booking->customer_booking_id }}
                    </span>
                </td>

                <td>
                    <div style="font-weight:700;font-size:12px;">{{ $booking->customer->name ?? 'N/A' }}</div>
                    <div style="font-size:10px;color:#94a3b8;">{{ $booking->customer->cnic ?? '' }}</div>
                </td>

                <td>
                    <div style="font-weight:700;">#{{ $booking->plot->plot_number ?? 'N/A' }}</div>
                    <div style="font-size:10px;color:#94a3b8;">
                        {{ $booking->plot->block ?? '' }}
                        @if($booking->plot->size ?? null)
                            &nbsp;·&nbsp; {{ $booking->plot->size }} {{ $booking->plot->unit }}
                        @endif
                    </div>
                </td>

                <td>
                    <span style="font-size:10px;font-weight:700;background:#f1f5f9;color:#475569;padding:3px 10px;border-radius:20px;white-space:nowrap;">
                        {{ $booking->booking_type ?? 'N/A' }}
                    </span>
                </td>

                @php
                    $plotDiscount    = (float)($booking->plot->discount_amount ?? 0);
                    $paymentDiscount = $booking->payments
                        ->where('status','paid')
                        ->whereIn('payment_category', $plotPriceCats)
                        ->sum('discount_amount');
                    $totalDiscount   = $plotDiscount + $paymentDiscount;
                    $plotBasePrice   = (float)($booking->plot->custom_price ?? $booking->plot->base_price ?? $booking->total_price);
                    $origPrice       = $totalDiscount > 0 ? $plotBasePrice + $plotDiscount : $plotBasePrice;
                @endphp
                <td style="font-weight:600;color:#64748b;font-size:12px;">
                    PKR {{ number_format($origPrice) }}
                    @if($totalDiscount > 0)
                        <div style="font-size:9px;color:#94a3b8;">before discount</div>
                    @endif
                </td>

                <td>
                    @if($totalDiscount > 0)
                        <span style="background:#fffbeb;border:1px solid #fde68a;color:#d97706;padding:3px 9px;border-radius:8px;font-size:11px;font-weight:800;white-space:nowrap;">
                            - PKR {{ number_format($totalDiscount) }}
                        </span>
                        @if($plotDiscount > 0 && $booking->plot->discount_reason)
                            <div style="font-size:9px;color:#94a3b8;margin-top:2px;">{{ $booking->plot->discount_reason }}</div>
                        @endif
                        @if($paymentDiscount > 0)
                            <div style="font-size:9px;color:#94a3b8;margin-top:2px;">
                                Sett. discount: PKR {{ number_format($paymentDiscount) }}
                            </div>
                        @endif
                    @else
                        <span style="color:#94a3b8;font-size:11px;">—</span>
                    @endif
                </td>

                <td style="font-weight:700;color:#0f172a;">
                    PKR {{ number_format($booking->total_price) }}
                    @if($totalDiscount > 0)
                        <div style="font-size:9px;color:#16a34a;">after discount</div>
                    @endif
                </td>

                <td style="font-weight:700;color:#16a34a;">
                    PKR {{ number_format($collected) }}
                </td>

                <td>
                    @if($isClosedBooking)
                        <span style="font-weight:700;color:#94a3b8;font-size:11px;">— {{ ucfirst($booking->status) }}</span>
                    @elseif($remaining > 0)
                        <span style="font-weight:700;color:#dc2626;">PKR {{ number_format($remaining) }}</span>
                    @else
                        <span style="font-weight:700;color:#16a34a;font-size:11px;">✓ Cleared</span>
                    @endif
                </td>

                <td>
                    @if(($booking->total_installments ?? 0) > 0)
                        <div style="font-weight:600;font-size:12px;">
                            {{ $paidInstallments }}/{{ $booking->total_installments }} mo paid
                        </div>
                        <div style="font-size:10px;color:#94a3b8;">PKR {{ number_format($booking->monthly_installment) }}/mo</div>
                    @elseif(($booking->quarterly_installments ?? 0) > 0)
                        <div style="font-weight:600;font-size:12px;">
                            {{ $paidQuarters }}/{{ $booking->quarterly_installments }} qtr paid
                        </div>
                        <div style="font-size:10px;color:#94a3b8;">PKR {{ number_format($booking->quarterly_amount) }}/qtr</div>
                    @else
                        <span style="font-size:11px;color:#94a3b8;">— Cash / Lump Sum</span>
                    @endif
                </td>

                <td>
                    <div style="font-size:12px;font-weight:600;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</div>
                    <div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($booking->booking_date)->diffForHumans() }}</div>
                </td>

                <td>
                    <span class="spill spill-{{ str_replace('_','-',$booking->status) }}">
                        <span class="spill-dot"></span>
                        {{ ucfirst(str_replace('_',' ',$booking->status)) }}
                    </span>
                </td>

                <td class="no-print">
                    <div style="display:flex;gap:6px;">
                        <a href="{{ route('ledger.view', $booking->id) }}"
                           style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:4px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;">
                            <i class="fas fa-eye"></i> Ledger
                        </a>
                        <a href="{{ route('booking.detail.view', $booking->id) }}"
                           style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:4px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;">
                            <i class="fas fa-info-circle"></i> Detail
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="text-align:center;padding:50px 20px;color:#94a3b8;">
                    <i class="fas fa-book-open" style="font-size:3rem;opacity:.18;display:block;margin-bottom:12px;"></i>
                    <p style="font-weight:700;font-size:13px;margin:0 0 4px;">No bookings found</p>
                    <p style="font-size:12px;margin:0;">Try adjusting the filters above.</p>
                </td>
            </tr>
            @endforelse
            </tbody>

            @if($totalBookings > 0)
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right;color:#94a3b8;font-size:10px;letter-spacing:.7px;">TOTALS</td>
                    <td style="color:#64748b;font-size:12px;">PKR {{ number_format(($grossRevenue ?? $totalRevenue)) }}</td>
                    <td style="color:#d97706;font-size:12px;">
                        @if(($totalDiscount ?? 0) > 0)
                            - PKR {{ number_format($totalDiscount) }}
                        @else
                            —
                        @endif
                    </td>
                    <td style="color:#0f172a;font-size:13px;">PKR {{ number_format($totalRevenue) }}</td>
                    <td style="color:#16a34a;font-size:13px;">PKR {{ number_format($totalCollected) }}</td>
                    <td style="color:#dc2626;font-size:13px;">PKR {{ number_format($totalRemaining) }}</td>
                    <td colspan="4"></td>

                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards">
        @forelse($all_bookings as $booking)
        @php
            $mcClosed    = in_array($booking->status, ['transferred','cancelled','swapped','plot_relocated','completed']);
            $mcCollected = $booking->payments->where('status','paid')->whereIn('payment_category',$plotPriceCats)->sum('amount_paid');
            $mcDiscount  = (float)($booking->plot->discount_amount ?? 0)
                         + $booking->payments->where('status','paid')->whereIn('payment_category',$plotPriceCats)->sum('discount_amount');
            $mcRemaining = $mcClosed ? 0 : max(0,(float)$booking->total_price - $mcCollected);
            $mcPaid      = $booking->payments->where('payment_category','installment')->where('status','paid')->count();
            $mcQtr       = $booking->payments->where('payment_category','quarterly_installment')->where('status','paid')->count();
            $mcStatus    = str_replace('_','-',$booking->status);
        @endphp
        <div class="mb-card">
            <div class="mb-card-top">
                <div>
                    <div class="mb-ref">{{ $booking->customer_booking_id }}</div>
                    <div class="mb-customer">{{ $booking->customer->name ?? 'N/A' }}</div>
                    <div class="mb-cnic">{{ $booking->customer->cnic ?? '' }}</div>
                </div>
                <span class="spill spill-{{ $mcStatus }}">
                    <span class="spill-dot"></span>{{ ucfirst(str_replace('_',' ',$booking->status)) }}
                </span>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                <div>
                    <div class="mb-plot">#{{ $booking->plot->plot_number ?? 'N/A' }} &nbsp;
                        <span style="font-size:10px;font-weight:700;background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:20px;">{{ $booking->booking_type ?? '—' }}</span>
                    </div>
                    <div class="mb-plot-sub">
                        {{ $booking->plot->block ?? '' }}
                        @if($booking->plot->size ?? null) · {{ $booking->plot->size }} {{ $booking->plot->unit }} @endif
                    </div>
                </div>
                <div style="font-size:10px;color:#94a3b8;text-align:right;white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
                </div>
            </div>

            <div class="mb-divider"></div>

            @if($mcDiscount > 0)
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:6px 10px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center;font-size:11px;">
                <span style="color:#92400e;font-weight:700;">Discount Applied</span>
                <span style="color:#d97706;font-weight:800;">- PKR {{ number_format($mcDiscount) }}</span>
            </div>
            @endif

            <div class="mb-amounts">
                <div class="mb-amt-box">
                    <span class="mb-amt-lbl">Contract</span>
                    <span class="mb-amt-val" style="color:#0f172a;">{{ number_format($booking->total_price) }}</span>
                </div>
                <div class="mb-amt-box">
                    <span class="mb-amt-lbl">Collected</span>
                    <span class="mb-amt-val" style="color:#16a34a;">{{ number_format($mcCollected) }}</span>
                </div>
                <div class="mb-amt-box">
                    <span class="mb-amt-lbl">Remaining</span>
                    <span class="mb-amt-val" style="color:{{ $mcClosed ? '#94a3b8' : ($mcRemaining > 0 ? '#dc2626' : '#16a34a') }};">
                        @if($mcClosed) — @elseif($mcRemaining > 0) {{ number_format($mcRemaining) }} @else ✓ Done @endif
                    </span>
                </div>
            </div>

            <div class="mb-footer">
                <div class="mb-plan">
                    @if(($booking->total_installments ?? 0) > 0)
                        {{ $mcPaid }}/{{ $booking->total_installments }} installments paid
                    @elseif(($booking->quarterly_installments ?? 0) > 0)
                        {{ $mcQtr }}/{{ $booking->quarterly_installments }} quarters paid
                    @else
                        Cash / Lump Sum
                    @endif
                </div>
                <div class="mb-actions no-print">
                    <a href="{{ route('ledger.view', $booking->id) }}" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:5px 12px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;"><i class="fas fa-eye"></i> Ledger</a>
                    <a href="{{ route('booking.detail.view', $booking->id) }}" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:5px 12px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;"><i class="fas fa-info-circle"></i></a>
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
            <i class="fas fa-book-open" style="font-size:2.5rem;opacity:.18;display:block;margin-bottom:12px;"></i>
            <p style="font-weight:700;font-size:13px;margin:0;">No bookings found</p>
        </div>
        @endforelse
    </div>
</div>

</div>{{-- /rpt-wrap --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthLabels  = @json(array_column($months, 'label'));
    const monthCounts  = @json(array_column($months, 'count'));
    const monthRevenue = @json(array_column($months, 'revenue'));
    const grid = 'rgba(0,0,0,0.035)';

    // Highlight last bar dark
    const barColors = monthCounts.map((_, i) =>
        i === monthCounts.length - 1 ? '#1e3a8a' : '#bfdbfe'
    );

    // ── Monthly Bookings Bar Chart ───────────────────────────
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Bookings',
                data: monthCounts,
                backgroundColor: barColors,
                borderRadius: 7,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            layout: { padding: { top: 20 } },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9', padding: 12, cornerRadius: 8,
                    callbacks: { label: ctx => `  ${ctx.parsed.y} booking${ctx.parsed.y !== 1 ? 's' : ''}` }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
                y: { grid: { color: grid }, beginAtZero: true, ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } } }
            }
        }
    });

    // ── Revenue Line Chart ────────────────────────────────────
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Collected (PKR)',
                data: monthRevenue,
                borderColor: '#1e3a8a',
                backgroundColor: ctx => {
                    const { chart } = ctx;
                    const { ctx: c, chartArea } = chart;
                    if (!chartArea) return 'transparent';
                    const g = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    g.addColorStop(0, 'rgba(30,58,138,.12)');
                    g.addColorStop(1, 'rgba(30,58,138,.00)');
                    return g;
                },
                borderWidth: 2.5,
                pointBackgroundColor: '#1e3a8a',
                pointBorderColor: '#fff',
                pointBorderWidth: 2.5,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.42,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9', padding: 12, cornerRadius: 8,
                    callbacks: { label: ctx => `  PKR ${Number(ctx.parsed.y).toLocaleString()}` }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
                y: {
                    grid: { color: grid }, beginAtZero: true,
                    ticks: {
                        color: '#94a3b8', font: { size: 11 }, maxTicksLimit: 5,
                        callback: v => v >= 1e6 ? 'PKR '+(v/1e6).toFixed(1)+'M' : 'PKR '+(v/1e3).toFixed(0)+'K'
                    }
                }
            }
        }
    });
});
</script>
@endpush
