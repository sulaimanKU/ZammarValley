@extends('layouts.index')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>

/* ══ PAGE WRAPPER ══ */
.dw { padding:24px 28px 56px; max-width:1600px; margin:0 auto; box-sizing:border-box; }
@media(max-width:768px){ .dw{ padding:16px 14px 40px; } }

/* ══ Section dividers ══ */
.sec-lbl {
    font-size:10px; font-weight:800; color:var(--muted-text);
    text-transform:uppercase; letter-spacing:1px;
    margin:28px 0 12px; display:flex; align-items:center; gap:10px;
}
.sec-lbl::after { content:''; flex:1; height:1px; background:var(--border-color); }

/* ══ Hero banner ══ */
.hero-strip {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
    border-radius:16px; padding:26px 30px; margin-bottom:20px;
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:16px; position:relative; overflow:hidden;
    box-shadow:0 8px 32px rgba(30,58,138,.25);
}
.hero-strip::before { content:''; position:absolute; top:-60px; right:-40px; width:240px; height:240px; border-radius:50%; background:rgba(255,255,255,.03); }
.hero-strip::after  { content:''; position:absolute; bottom:-80px; left:30%; width:300px; height:300px; border-radius:50%; background:rgba(255,255,255,.02); }
.hs-name { font-size:20px; font-weight:800; color:#fff; margin:0 0 4px; letter-spacing:-.2px; }
.hs-sub  { font-size:12px; color:rgba(255,255,255,.45); margin:0; }
.hs-tags { display:flex; gap:6px; margin-top:12px; flex-wrap:wrap; }
.hs-tag  { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); border-radius:8px; padding:4px 12px; font-size:11px; font-weight:700; color:rgba(255,255,255,.8); display:inline-flex; align-items:center; gap:5px; }
.hs-tag.warn  { background:rgba(251,191,36,.15); border-color:rgba(251,191,36,.3); color:#fbbf24; }
.hs-tag.alert { background:rgba(239,68,68,.15);  border-color:rgba(239,68,68,.3);  color:#fca5a5; }
.hs-tag.hold  { background:rgba(245,158,11,.15); border-color:rgba(245,158,11,.3); color:#fcd34d; }

/* ══ Hero stats (right side) ══ */
.hero-right { display:flex; gap:28px; flex-wrap:wrap; position:relative; z-index:1; align-items:center; }
.hero-stat { text-align:right; }
.hero-stat-lbl { font-size:9px; font-weight:800; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:.8px; margin-bottom:3px; }
.hero-stat-val { font-size:20px; font-weight:800; line-height:1.1; }
.hero-stat-sub { font-size:10px; color:rgba(255,255,255,.35); margin-top:2px; }
.hero-divider  { width:1px; height:50px; background:rgba(255,255,255,.12); flex-shrink:0; }

/* ══ Alert banners ══ */
.dash-alert { border-radius:12px; padding:12px 18px; margin-bottom:10px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; font-size:12px; font-weight:600; }
.dash-alert.amber { background:#fffbeb; border:1.5px solid #fde68a; color:#92400e; }
.dash-alert.red   { background:#fef2f2; border:1.5px solid #fecaca; color:#dc2626; }
.dash-alert a { margin-left:auto; padding:6px 14px; border-radius:8px; font-size:11px; font-weight:800; text-decoration:none; white-space:nowrap; }
.dash-alert.amber a { background:#d97706; color:#fff; }
.dash-alert.red   a { background:#dc2626; color:#fff; }

/* ══ KPI cards (top 4 summary boxes) ══ */
.kpi-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:14px; margin-bottom:14px; }

.kpi {
    background:var(--card); border:1px solid var(--border-color);
    border-radius:14px; padding:20px 22px 18px;
    position:relative; overflow:hidden;
    transition:box-shadow .18s, transform .18s;
    text-decoration:none; color:inherit; display:block;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
}
.kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--kpi-color,#6366f1); border-radius:14px 14px 0 0; }
.kpi:hover { box-shadow:0 8px 24px rgba(0,0,0,.09); transform:translateY(-2px); }
.kpi-lbl   { font-size:10px; font-weight:800; color:var(--muted-text); text-transform:uppercase; letter-spacing:.8px; margin-bottom:10px; }
.kpi-val   { font-size:26px; font-weight:800; color:var(--navy); line-height:1; margin-bottom:5px; }
.kpi-exact { font-size:11px; color:var(--muted-text); margin-bottom:8px; }
.kpi-sub   { font-size:11px; color:var(--sub-text); }
.kpi-sub .up { color:#16a34a; font-weight:700; }
.kpi-sub .dn { color:#dc2626; font-weight:700; }
.kpi-icon  { position:absolute; top:18px; right:18px; width:38px; height:38px; border-radius:10px; background:var(--kpi-bg,#f1f5f9); display:flex; align-items:center; justify-content:center; font-size:1.1rem; }

/* ══ Collection progress bar ══ */
.collect-bar { background:var(--card); border:1px solid var(--border-color); border-radius:12px; padding:16px 22px; margin-bottom:14px; display:flex; align-items:center; gap:18px; flex-wrap:wrap; box-shadow:0 1px 4px rgba(0,0,0,.05); }
.collect-bar-track { flex:1; min-width:180px; height:8px; background:var(--border-color); border-radius:8px; overflow:hidden; }
.collect-bar-fill  { height:100%; background:linear-gradient(90deg,#1d4ed8,#10b981); border-radius:8px; transition:width .7s cubic-bezier(.4,0,.2,1); }

/* ══ Stat card grids ══ */
.sg   { display:grid; gap:12px; margin-bottom:12px; }
.sg-3 { grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); }
.sg-4 { grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); }
.sg-6 { grid-template-columns:repeat(auto-fill,minmax(170px,1fr)); }

/* ══ Stat card ══ */
.sc {
    background:var(--card); border-radius:14px; border:1px solid var(--border-color);
    padding:18px 20px; display:flex; align-items:center; gap:14px;
    position:relative; overflow:hidden;
    transition:box-shadow .18s, transform .18s;
    text-decoration:none; color:inherit;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
}
.sc:hover { box-shadow:0 6px 20px rgba(0,0,0,.08); transform:translateY(-2px); }
.sc::after { content:''; position:absolute; bottom:0; left:0; right:0; height:2px; background:var(--sc-accent,#6366f1); opacity:0; transition:opacity .2s; }
.sc:hover::after { opacity:1; }
.sc-icon  { width:44px; height:44px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.sc-body  { flex:1; min-width:0; }
.sc-lbl   { font-size:10px; font-weight:800; color:var(--muted-text); text-transform:uppercase; letter-spacing:.7px; margin-bottom:4px; }
.sc-val   { font-size:20px; font-weight:800; color:var(--navy); line-height:1.1; margin-bottom:2px; }
.sc-marla { font-size:13px; font-weight:700; margin-bottom:3px; }
.sc-sub   { font-size:10px; color:var(--muted-text); }
.sc-sub .up  { color:#16a34a; font-weight:700; }
.sc-sub .dn  { color:#dc2626; font-weight:700; }
.sc-sub .wn  { color:#d97706; font-weight:700; }
.sc-exact { font-size:10px; color:var(--sub-text); margin-top:1px; }

/* ══ Plot stock panel ══ */
.inv-panel { background:var(--card); border:1px solid var(--border-color); border-radius:14px; padding:20px 24px; margin-bottom:14px; box-shadow:0 1px 4px rgba(0,0,0,.05); }
.inv-row   { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:18px; flex-wrap:wrap; }
.inv-stat  { text-align:center; padding:0 8px; }
.inv-stat-lbl { font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.7px; margin-bottom:4px; }
.inv-stat-val { font-size:20px; font-weight:800; line-height:1; margin-bottom:3px; }
.inv-stat-sub { font-size:10px; color:var(--muted-text); }
.inv-vdiv { width:1px; height:54px; background:var(--border-color); flex-shrink:0; }
.inv-total { text-align:left; padding:0; }
.inv-total .inv-stat-lbl { font-size:9px; color:var(--muted-text); }
.inv-total .inv-stat-val { font-size:26px; color:var(--navy); }
.inv-total .inv-stat-sub { font-size:11px; color:var(--sub-text); margin-top:2px; }
.inv-bar { height:10px; background:var(--border-color); border-radius:8px; overflow:hidden; display:flex; }
.inv-bar div { transition:width .6s cubic-bezier(.4,0,.2,1); }
.inv-legend { display:flex; gap:18px; margin-top:9px; font-size:10px; font-weight:700; flex-wrap:wrap; }
.inv-legend span { display:flex; align-items:center; gap:4px; }
.inv-legend-dot { width:8px; height:8px; border-radius:2px; display:inline-block; }

/* ══ Quick action buttons ══ */
.qa-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:10px; margin-bottom:12px; }
.qa-btn {
    background:var(--card); border:1px solid var(--border-color);
    border-radius:12px; padding:16px 10px; text-align:center;
    text-decoration:none; color:var(--sub-text);
    transition:all .18s; display:flex; flex-direction:column;
    align-items:center; gap:7px; box-shadow:0 1px 4px rgba(0,0,0,.05);
}
.qa-btn:hover { border-color:var(--qa-c); background:var(--qa-bg); transform:translateY(-2px); box-shadow:0 5px 16px rgba(0,0,0,.07); color:var(--qa-c); }
.qa-btn i    { font-size:1.3rem; }
.qa-btn span { font-size:11px; font-weight:700; color:var(--text-main); }

/* ══ Content card ══ */
.mc { background:var(--card); border-radius:14px; border:1px solid var(--border-color); overflow:hidden; margin-bottom:14px; box-shadow:0 1px 4px rgba(0,0,0,.05); }
.mc-head  { display:flex; align-items:center; justify-content:space-between; padding:14px 18px; border-bottom:1px solid var(--table-border); flex-wrap:wrap; gap:8px; background:var(--table-head-bg); }
.mc-title { font-size:13px; font-weight:800; color:var(--navy); margin:0; }
.mc-sub   { font-size:11px; color:var(--muted-text); margin:2px 0 0; }
.mc-body  { padding:18px; }
.mc-lnk   { font-size:11px; font-weight:700; color:#1d4ed8; text-decoration:none; white-space:nowrap; }

/* ══ Chart layout ══ */
.charts-grid { display:grid; grid-template-columns:2fr 1fr 1fr; gap:14px; }
@media(max-width:1100px){ .charts-grid{ grid-template-columns:1fr 1fr; } }
@media(max-width:640px) { .charts-grid{ grid-template-columns:1fr; } }
.bot-grid { display:grid; grid-template-columns:2fr 1fr; gap:14px; }
@media(max-width:900px){ .bot-grid{ grid-template-columns:1fr; } }

/* ══ Data table ══ */
.dt { width:100%; border-collapse:collapse; min-width:520px; }
.dt thead th { font-size:10px; text-transform:uppercase; letter-spacing:.5px; color:var(--muted-text); font-weight:800; background:var(--table-head-bg); border-bottom:1px solid var(--table-border); padding:10px 13px; white-space:nowrap; }
.dt tbody td { padding:11px 13px; border-bottom:1px solid var(--table-border); font-size:12px; vertical-align:middle; color:var(--text-main); }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover td { background:var(--table-row-hover); }

/* ══ Status pills ══ */
.pill { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:10px; font-weight:700; white-space:nowrap; }
.pill-dot { width:5px; height:5px; border-radius:50%; }
.pill-blue   { background:var(--sky);   color:#0369a1; } .pill-blue   .pill-dot { background:#0ea5e9; }
.pill-green  { background:var(--lime);  color:#15803d; } .pill-green  .pill-dot { background:#16a34a; }
.pill-amber  { background:var(--amber-l); color:#92400e; } .pill-amber  .pill-dot { background:#d97706; }
.pill-red    { background:var(--red-l); color:#dc2626; } .pill-red    .pill-dot { background:#dc2626; }
.pill-purple { background:var(--purp-l);color:#7c3aed; } .pill-purple .pill-dot { background:#7c3aed; }
.pill-slate  { background:var(--border-color); color:#475569; } .pill-slate .pill-dot { background:var(--muted-text); }

/* ══ Misc ══ */
.pbar { height:5px; background:var(--border-color); border-radius:6px; overflow:hidden; }
.pbar-fill { height:100%; border-radius:6px; transition:width .4s; }
.tc-row { display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid var(--table-border); color:var(--text-main); }
.tc-row:last-child { border-bottom:none; }
.tc-av  { width:32px; height:32px; border-radius:9px; font-size:13px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rp-row { display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid var(--table-border); color:var(--text-main); }
.rp-row:last-child { border-bottom:none; }
.rp-ic  { width:32px; height:32px; border-radius:9px; background:var(--lime); color:#16a34a; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:14px; }
</style>
@endpush

@section('content')

@php
$fmt   = function($n){ $n=(float)$n; if($n>=10000000) return 'PKR '.number_format($n/10000000,2).' Cr'; if($n>=100000) return 'PKR '.number_format($n/100000,1).' Lac'; if($n>=1000) return 'PKR '.number_format($n/1000,1).'K'; return 'PKR '.number_format($n); };
$exact = fn($n) => 'PKR '.number_format((float)$n);

$catColorMap=['down_payment'=>'#1d4ed8','installment'=>'#16a34a','quarterly_installment'=>'#059669','processing_fee'=>'#ea580c','plot_balance'=>'#0369a1','fine'=>'#dc2626','security_fee'=>'#7c3aed','maintenance_fee'=>'#065f46','development_fee'=>'#a16207','registry_fee'=>'#475569','bifurcation_fee'=>'#c2410c','others'=>'#94a3b8'];
$catLabels=[]; $catVals=[]; $catColors=[];
foreach($categoryBreakdown as $k=>$v){ $catLabels[]=ucwords(str_replace('_',' ',$k)); $catVals[]=(float)$v; $catColors[]=$catColorMap[$k]??'#cbd5e1'; }
$mlJson=json_encode(array_values($monthlyLabels));
$mcJson=json_encode(array_values($monthlyCollection));
$psLabels=json_encode(array_keys($plotStatusBreakdown));
$psVals=json_encode(array_values($plotStatusBreakdown));
$catLbJ=json_encode($catLabels); $catVlJ=json_encode($catVals); $catClJ=json_encode($catColors);

$hr=now()->hour;
$greeting=$hr<12?'Good morning':($hr<17?'Good afternoon':'Good evening');
$greetEmoji=$hr<12?'☀️':($hr<17?'🌤️':'🌙');

// total_price is already NET (base minus offer discount), so plot discounts are baked in.
// Credits = cash received + settlement (lump-sum) discounts only.
$totalCreditsDash = $totalCollection + ($totalPaymentDiscounts ?? 0);

// Overall progress = (totalPlotValue - totalRemaining) / totalPlotValue.
// This is the authoritative figure because:
//   • $totalRemaining is computed per-booking with status logic:
//     completed/transferred bookings always have remaining = 0.
//   • If a booking is marked "completed" the admin confirms all dues are cleared,
//     so it must count as 100% regardless of whether individual payment rows
//     arithmetically sum to total_price.
//   • Using cash+discounts / totalPlotValue under-counts whenever a booking
//     was settled via lump sum or manually completed.
$collectionPct = $totalPlotValue > 0
    ? min(round(($totalPlotValue - $totalRemaining) / $totalPlotValue * 100, 1), 100)
    : 0;
$cashOnlyPct   = $totalPlotValue > 0
    ? min(round($totalCollection / $totalPlotValue * 100, 1), 100)
    : 0;

// "Status-settled" gap: portion of progress covered by completed/transferred
// booking statuses beyond what's recorded as cash+discount in plot_payments.
$totalStatusSettled = max(0, ($totalPlotValue - $totalRemaining) - $totalCreditsDash);

// Plot inventory percentages for stacked bar
$tm           = max((float)$totalMarlas, 1);
$pctSold      = min(round($soldMarlas   / $tm * 100), 100);
$pctBooked    = min(round($bookedMarlas / $tm * 100), 100);
$pctAvailable = max(0, 100 - $pctSold - $pctBooked);

// Remaining = unsold (available + booked)
$toDisplay = function(float $m): string {
    if ($m >= 20) {
        $k = floor($m / 20); $r = $m - ($k * 20);
        return $r > 0 ? "{$k} Kanal ".number_format($r,0)." Marla" : "{$k} Kanal";
    }
    return number_format($m,0).' Marla';
};
$remainingMarlas  = (float)$availableMarlas + (float)$bookedMarlas;
$remainingDisplay = $toDisplay($remainingMarlas);

$holdCount = $onHoldBookings ?? 0;
@endphp

<div class="dw">

{{-- ══ HERO ══════════════════════════════════════════════════ --}}
<div class="hero-strip">
    <div style="position:relative;z-index:1;">
        <p class="hs-name">{{ $greeting }}, {{ Auth::user()->name }}! {{ $greetEmoji }}</p>
        <p class="hs-sub">{{ now()->format('l, d F Y') }} &nbsp;·&nbsp; Zamar Valley Management System</p>
        <div class="hs-tags">
            <div class="hs-tag">
                <i class="bi bi-circle-fill" style="color:#4ade80;font-size:7px;"></i> System Online
            </div>
            @if($pendingBookings > 0)
            <div class="hs-tag warn">
                <i class="bi bi-clock-history"></i> {{ $pendingBookings }} Pending
            </div>
            @endif
            @if($pendingFeeCount > 0)
            <div class="hs-tag alert">
                <i class="bi bi-lock-fill"></i> {{ $pendingFeeCount }} Transfer Fees Due
            </div>
            @endif
            @if($overdueInstallments->count() > 0)
            <div class="hs-tag alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ $overdueInstallments->count() }} Overdue
            </div>
            @endif
            @if($holdCount > 0)
            <div class="hs-tag hold">
                <i class="bi bi-pause-circle-fill"></i> {{ $holdCount }} On Hold
            </div>
            @endif
        </div>
    </div>

    <div class="hero-right">
        <div class="hero-stat">
            <div class="hero-stat-lbl">{{ now()->format('F Y') }}</div>
            <div class="hero-stat-val" style="color:#4ade80;">{{ $fmt($thisMonthCollection) }}</div>
            <div class="hero-stat-sub">{{ $exact($thisMonthCollection) }}</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
            <div class="hero-stat-lbl">Cash Received</div>
            <div class="hero-stat-val" style="color:#fff;">{{ $fmt($totalCollection) }}</div>
            <div class="hero-stat-sub">{{ $exact($totalCollection) }}</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
            <div class="hero-stat-lbl">Progress (incl. discounts)</div>
            <div class="hero-stat-val" style="color:#93c5fd;">{{ $collectionPct }}%</div>
            <div class="hero-stat-sub">{{ $cashOnlyPct }}% cash only</div>
        </div>
    </div>
</div>

{{-- ══ ALERT BANNERS ══════════════════════════════════════════ --}}
@if($pendingFeeCount > 0)
<div class="dash-alert amber">
    <i class="bi bi-exclamation-triangle-fill" style="flex-shrink:0;"></i>
    <span>
        <strong>{{ $pendingFeeCount }} transfer{{ $pendingFeeCount > 1 ? 's' : '' }}</strong>
        {{ $pendingFeeCount > 1 ? 'have' : 'has' }} unpaid transfer fees — buyer's booking is pending.
    </span>
    <a href="{{ route('fee.management') }}">Pay Fees →</a>
</div>
@endif
@if($overdueInstallments->count() > 0)
<div class="dash-alert red">
    <i class="bi bi-clock-fill" style="flex-shrink:0;"></i>
    <span>
        <strong>{{ $overdueInstallments->count() }} installment{{ $overdueInstallments->count() > 1 ? 's' : '' }}</strong>
        overdue — past grace period.
    </span>
    <a href="{{ route('finance.report') }}">View All →</a>
</div>
@endif

{{-- ══ KEY METRICS ════════════════════════════════════════════ --}}
<p class="sec-lbl">Key Metrics</p>
<div class="kpi-grid">

    <a href="{{ route('index.account') }}" class="kpi" style="--kpi-color:#6366f1;--kpi-bg:#eef2ff;">
        <div class="kpi-icon"><i class="bi bi-bank" style="color:#6366f1;"></i></div>
        <div class="kpi-lbl">Project Value</div>
        <div class="kpi-val" style="color:#6366f1;">{{ $fmt($totalPlotValue) }}</div>
        <div class="kpi-exact">{{ $exact($totalPlotValue) }}</div>
        <div class="kpi-sub">Total of all booking prices</div>
    </a>

    <a href="{{ route('index.account') }}" class="kpi" style="--kpi-color:#16a34a;--kpi-bg:#f0fdf4;">
        <div class="kpi-icon"><i class="bi bi-cash-stack" style="color:#16a34a;"></i></div>
        <div class="kpi-lbl">Cash Received</div>
        <div class="kpi-val" style="color:#16a34a;">{{ $fmt($totalCollection) }}</div>
        <div class="kpi-exact">{{ $exact($totalCollection) }}</div>
        <div class="kpi-sub"><span class="up">{{ $cashOnlyPct }}%</span> cash &nbsp;·&nbsp; <span class="up">{{ $collectionPct }}%</span> incl. discounts</div>
    </a>

    <a href="{{ route('index.account') }}" class="kpi" style="--kpi-color:#dc2626;--kpi-bg:#fef2f2;">
        <div class="kpi-icon"><i class="bi bi-hourglass-split" style="color:#dc2626;"></i></div>
        <div class="kpi-lbl">Remaining Balance</div>
        <div class="kpi-val" style="color:#dc2626;">{{ $fmt($totalRemaining) }}</div>
        <div class="kpi-exact">{{ $exact($totalRemaining) }}</div>
        <div class="kpi-sub">Still to be collected</div>
    </a>

    <a href="{{ route('finance.report') }}" class="kpi" style="--kpi-color:#0369a1;--kpi-bg:#f0f9ff;">
        <div class="kpi-icon"><i class="bi bi-calendar-month-fill" style="color:#0369a1;"></i></div>
        <div class="kpi-lbl">{{ now()->format('F') }} Collections</div>
        <div class="kpi-val" style="color:#0369a1;">{{ $fmt($thisMonthCollection) }}</div>
        <div class="kpi-exact">{{ $exact($thisMonthCollection) }}</div>
        <div class="kpi-sub">Received this month</div>
    </a>

    @if(($totalDiscount ?? 0) > 0)
    <a href="{{ route('booking.reports') }}" class="kpi" style="--kpi-color:#d97706;--kpi-bg:#fffbeb;">
        <div class="kpi-icon"><i class="bi bi-tags-fill" style="color:#d97706;"></i></div>
        <div class="kpi-lbl">Total Discounts Given</div>
        <div class="kpi-val" style="color:#d97706;">{{ $fmt($totalDiscount ?? 0) }}</div>
        <div class="kpi-exact">{{ $exact($totalDiscount ?? 0) }}</div>
        <div class="kpi-sub" style="line-height:1.6;">
            @if(($totalPlotDiscounts ?? 0) > 0)
                <span style="color:#92400e;">Plot:</span> {{ $fmt($totalPlotDiscounts) }}
                across <strong>{{ $discountedBookingsCount ?? 0 }}</strong>
                booking{{ ($discountedBookingsCount ?? 0) != 1 ? 's' : '' }}
            @endif
            @if(($totalPaymentDiscounts ?? 0) > 0)
                @if(($totalPlotDiscounts ?? 0) > 0)<br>@endif
                <span style="color:#92400e;">Settlement:</span> {{ $fmt($totalPaymentDiscounts) }}
                across <strong>{{ $settlementDiscountCount ?? 0 }}</strong>
                booking{{ ($settlementDiscountCount ?? 0) != 1 ? 's' : '' }}
            @endif
        </div>
    </a>
    @endif

</div>

{{-- Collection progress bar --}}
<div class="collect-bar" style="flex-wrap:wrap;row-gap:4px;">
    <div style="font-size:11px;font-weight:800;color:var(--sub-text);white-space:nowrap;min-width:130px;">
        Payment Progress
    </div>
    {{-- Stacked bar: cash (green) + settlement discounts (orange) + status-settled (blue) --}}
    @php
        $barCash      = $totalPlotValue > 0 ? min(round($totalCollection / $totalPlotValue * 100, 1), 100) : 0;
        $barPayDisc   = $totalPlotValue > 0 ? min(round(($totalPaymentDiscounts ?? 0) / $totalPlotValue * 100, 1), 100 - $barCash) : 0;
        $barStatusAdj = $totalPlotValue > 0 ? min(round($totalStatusSettled / $totalPlotValue * 100, 1), 100 - $barCash - $barPayDisc) : 0;
    @endphp
    <div class="collect-bar-track" style="position:relative;overflow:hidden;flex:1;min-width:120px;">
        <div style="position:absolute;left:0;top:0;bottom:0;width:{{ $barCash }}%;background:var(--green);border-radius:inherit;transition:width .4s;"></div>
        @if($barPayDisc > 0)
        <div style="position:absolute;left:{{ $barCash }}%;top:0;bottom:0;width:{{ $barPayDisc }}%;background:#f97316;transition:width .4s;"></div>
        @endif
        @if($barStatusAdj > 0)
        <div style="position:absolute;left:{{ $barCash + $barPayDisc }}%;top:0;bottom:0;width:{{ $barStatusAdj }}%;background:#3b82f6;opacity:.7;transition:width .4s;"></div>
        @endif
    </div>
    <div style="font-size:15px;font-weight:800;color:var(--navy);white-space:nowrap;">{{ $collectionPct }}%</div>
    <div style="font-size:11px;color:var(--muted-text);flex-basis:100%;display:flex;flex-wrap:wrap;gap:10px;padding-top:2px;">
        <span>
            <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:var(--green);margin-right:3px;vertical-align:middle;"></span>
            Cash: <strong>{{ $fmt($totalCollection) }}</strong> ({{ $cashOnlyPct }}%)
        </span>
        @if(($totalPaymentDiscounts ?? 0) > 0)
        <span>
            <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#f97316;margin-right:3px;vertical-align:middle;"></span>
            Full-Pay Discount: <strong style="color:#ea580c;">{{ $fmt($totalPaymentDiscounts) }}</strong>
        </span>
        @endif
        @if($totalStatusSettled > 0)
        <span>
            <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#3b82f6;margin-right:3px;vertical-align:middle;opacity:.7;"></span>
            Settled (completed bookings): <strong style="color:#1d4ed8;">{{ $fmt($totalStatusSettled) }}</strong>
        </span>
        @endif
        <span style="color:#94a3b8;">of {{ $fmt($totalPlotValue) }}</span>
        @if(($totalPlotDiscounts ?? 0) > 0)
        <span style="color:#94a3b8;">&nbsp;·&nbsp; Orig. (before offer discounts): {{ $fmt($grossPlotValue) }}</span>
        @endif
    </div>
</div>

{{-- ══ PLOT INVENTORY ════════════════════════════════════════ --}}
<p class="sec-lbl">Plot Inventory</p>

{{-- Summary panel --}}
<div class="inv-panel">
    <div class="inv-row">

        {{-- Total (left, bigger) --}}
        <div class="inv-stat inv-total">
            <div class="inv-stat-lbl">Total Inventory</div>
            <div class="inv-stat-val">{{ $totalDisplay }}</div>
            <div class="inv-stat-sub">{{ number_format($totalPlots) }} plots total</div>
        </div>

        <div class="inv-vdiv"></div>

        {{-- Available --}}
        <div class="inv-stat">
            <div class="inv-stat-lbl" style="color:#16a34a;">Available</div>
            <div class="inv-stat-val" style="color:#16a34a;">{{ $availableDisplay }}</div>
            <div class="inv-stat-sub">{{ number_format($availablePlots) }} plots</div>
        </div>

        <div class="inv-vdiv"></div>

        {{-- Booked --}}
        <div class="inv-stat">
            <div class="inv-stat-lbl" style="color:#d97706;">Booked</div>
            <div class="inv-stat-val" style="color:#d97706;">{{ $bookedDisplay }}</div>
            <div class="inv-stat-sub">{{ number_format($bookedPlots) }} plots</div>
        </div>

        <div class="inv-vdiv"></div>

        {{-- Sold --}}
        <div class="inv-stat">
            <div class="inv-stat-lbl" style="color:#1d4ed8;">Sold</div>
            <div class="inv-stat-val" style="color:#1d4ed8;">{{ $soldDisplay }}</div>
            <div class="inv-stat-sub">{{ number_format($soldPlots) }} plots</div>
        </div>

        <div class="inv-vdiv"></div>

        {{-- Remaining (available + booked = not yet sold) --}}
        <div class="inv-stat">
            <div class="inv-stat-lbl" style="color:#7c3aed;">Remaining</div>
            <div class="inv-stat-val" style="color:#7c3aed;">{{ $remainingDisplay }}</div>
            <div class="inv-stat-sub">{{ number_format($availablePlots + $bookedPlots) }} plots</div>
        </div>

    </div>

    {{-- Stacked bar --}}
    <div class="inv-bar">
        <div style="width:{{ $pctSold }}%;background:#1d4ed8;" title="Sold {{ $pctSold }}%"></div>
        <div style="width:{{ $pctBooked }}%;background:#f59e0b;" title="Booked {{ $pctBooked }}%"></div>
        <div style="width:{{ $pctAvailable }}%;background:#10b981;" title="Available {{ $pctAvailable }}%"></div>
    </div>
    <div class="inv-legend">
        <span style="color:#1d4ed8;"><span class="inv-legend-dot" style="background:#1d4ed8;"></span>Sold {{ $pctSold }}%</span>
        <span style="color:#d97706;"><span class="inv-legend-dot" style="background:#f59e0b;"></span>Booked {{ $pctBooked }}%</span>
        <span style="color:#16a34a;"><span class="inv-legend-dot" style="background:#10b981;"></span>Available {{ $pctAvailable }}%</span>
    </div>
</div>

{{-- 4 plot stat cards --}}
<div class="sg sg-4">
    <a href="{{ route('index.plots') }}" class="sc" style="--sc-accent:#6366f1;">
        <div class="sc-icon" style="background:#f1f5f9;"><i class="bi bi-houses-fill" style="color:#6366f1;font-size:1.2rem;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Total Plots</div>
            <div class="sc-val">{{ number_format($totalPlots) }}</div>
            <div class="sc-marla" style="color:#6366f1;">{{ $totalDisplay }}</div>
            <div class="sc-sub">All inventory</div>
        </div>
    </a>
    <a href="{{ route('index.plots', ['status'=>'available']) }}" class="sc" style="--sc-accent:#10b981;">
        <div class="sc-icon" style="background:#f0fdf4;"><i class="bi bi-check-circle-fill" style="color:#10b981;font-size:1.2rem;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Available</div>
            <div class="sc-val" style="color:#16a34a;">{{ number_format($availablePlots) }}</div>
            <div class="sc-marla" style="color:#16a34a;">{{ $availableDisplay }}</div>
            <div class="sc-sub">Ready to book</div>
        </div>
    </a>
    <a href="{{ route('index.plots', ['status'=>'booked']) }}" class="sc" style="--sc-accent:#f59e0b;">
        <div class="sc-icon" style="background:#fffbeb;"><i class="bi bi-calendar-check-fill" style="color:#f59e0b;font-size:1.2rem;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Booked</div>
            <div class="sc-val" style="color:#d97706;">{{ number_format($bookedPlots) }}</div>
            <div class="sc-marla" style="color:#d97706;">{{ $bookedDisplay }}</div>
            <div class="sc-sub">Active reservations</div>
        </div>
    </a>
    <a href="{{ route('index.plots', ['status'=>'sold']) }}" class="sc" style="--sc-accent:#3b82f6;">
        <div class="sc-icon" style="background:#eff6ff;"><i class="bi bi-bag-check-fill" style="color:#3b82f6;font-size:1.2rem;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Sold</div>
            <div class="sc-val" style="color:#1d4ed8;">{{ number_format($soldPlots) }}</div>
            <div class="sc-marla" style="color:#1d4ed8;">{{ $soldDisplay }}</div>
            <div class="sc-sub">Completed sales</div>
        </div>
    </a>
</div>

{{-- ══ BOOKINGS ════════════════════════════════════════════════ --}}
<p class="sec-lbl">Bookings</p>
<div class="sg sg-4" style="grid-template-columns:repeat(auto-fill,minmax(170px,1fr))">
    <a href="{{ route('index.booking') }}" class="sc" style="--sc-accent:#6366f1;">
        <div class="sc-icon" style="background:#f1f5f9;"><i class="bi bi-journal-bookmark-fill" style="color:#6366f1;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Total</div>
            <div class="sc-val">{{ number_format($totalBookings) }}</div>
            <div class="sc-sub"><span class="up">+{{ $newBookingsThisMonth }}</span> this month</div>
        </div>
    </a>
    <a href="{{ route('index.booking', ['status'=>'active']) }}" class="sc" style="--sc-accent:#3b82f6;">
        <div class="sc-icon" style="background:#eff6ff;"><i class="bi bi-activity" style="color:#3b82f6;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Active</div>
            <div class="sc-val" style="color:#1d4ed8;">{{ number_format($activeBookings) }}</div>
            <div class="sc-sub">Paying installments</div>
        </div>
    </a>
    <a href="{{ route('index.booking', ['status'=>'completed']) }}" class="sc" style="--sc-accent:#16a34a;">
        <div class="sc-icon" style="background:#f0fdf4;"><i class="bi bi-check-circle-fill" style="color:#16a34a;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Completed</div>
            <div class="sc-val" style="color:#16a34a;">{{ number_format($completedBookings) }}</div>
            <div class="sc-sub">Fully paid</div>
        </div>
    </a>
    <a href="{{ route('index.booking', ['status'=>'pending']) }}" class="sc" style="--sc-accent:#d97706;">
        <div class="sc-icon" style="background:#fffbeb;"><i class="bi bi-hourglass-split" style="color:#d97706;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Pending</div>
            <div class="sc-val" style="color:#d97706;">{{ number_format($pendingBookings) }}</div>
            <div class="sc-sub">Awaiting activation</div>
        </div>
    </a>
    <a href="{{ route('index.booking', ['status'=>'transferred']) }}" class="sc" style="--sc-accent:#7c3aed;">
        <div class="sc-icon" style="background:#fdf4ff;"><i class="bi bi-arrow-left-right" style="color:#7c3aed;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Transferred</div>
            <div class="sc-val" style="color:#7c3aed;">{{ number_format($transferredBookings) }}</div>
            <div class="sc-sub">{{ $pendingTransferCount > 0 ? $pendingTransferCount.' pending' : 'All done' }}</div>
        </div>
    </a>
    <a href="{{ route('index.booking') }}" class="sc"
       style="--sc-accent:#f59e0b;{{ $holdCount > 0 ? 'border-color:#fbbf24;background:#fffdf7;' : '' }}">
        <div class="sc-icon" style="background:#fef3c7;"><i class="bi bi-pause-circle-fill" style="color:#d97706;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">On Hold</div>
            <div class="sc-val" style="color:{{ $holdCount > 0 ? '#d97706' : '#94a3b8' }};">{{ number_format($holdCount) }}</div>
            <div class="sc-sub">{{ $holdCount > 0 ? 'Payments blocked' : 'None on hold' }}</div>
        </div>
    </a>
    <a href="{{ route('index.booking', ['status'=>'cancelled']) }}" class="sc"
       style="--sc-accent:#dc2626;{{ $cancelledBookings > 0 ? 'border-color:#fecaca;background:#fff8f8;' : '' }}">
        <div class="sc-icon" style="background:#fef2f2;"><i class="bi bi-x-circle-fill" style="color:#dc2626;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Cancelled</div>
            <div class="sc-val" style="color:{{ $cancelledBookings > 0 ? '#dc2626' : '#94a3b8' }};">{{ number_format($cancelledBookings) }}</div>
            <div class="sc-sub">
                @if($cancelledCollected > 0)
                    PKR {{ number_format($cancelledCollected) }} received
                @else
                    No cancellations
                @endif
            </div>
        </div>
    </a>
</div>

{{-- ══ CANCELLATION SUMMARY ════════════════════════════════════ --}}
@if($cancelledBookings > 0)
<div style="background:#fff;border:1px solid #fecaca;border-radius:14px;padding:18px 22px;margin-bottom:18px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;box-shadow:0 2px 8px rgba(220,38,38,.06);">
    <div style="width:44px;height:44px;background:#fef2f2;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="bi bi-x-circle-fill" style="color:#dc2626;font-size:1.2rem;"></i>
    </div>
    <div style="flex:1;min-width:180px;">
        <div style="font-size:12px;font-weight:800;color:#991b1b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Cancellation Summary</div>
        <div style="display:flex;gap:28px;flex-wrap:wrap;">
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Cancelled</div>
                <div style="font-size:18px;font-weight:800;color:#dc2626;">{{ number_format($cancelledBookings) }}</div>
                <div style="font-size:10px;color:#94a3b8;">bookings</div>
            </div>
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Collected Before Cancel</div>
                <div style="font-size:18px;font-weight:800;color:#0f172a;">PKR {{ number_format($cancelledCollected) }}</div>
                <div style="font-size:10px;color:#94a3b8;">payments received</div>
            </div>
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Agreed Refund</div>
                <div style="font-size:18px;font-weight:800;color:#b45309;">PKR {{ number_format($cancelledRefundTotal) }}</div>
                <div style="font-size:10px;color:#94a3b8;">to be returned</div>
            </div>
            @if($cancelledCollected > 0 && $cancelledRefundTotal >= 0)
            <div>
                <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Net Retained</div>
                @php $netRetained = $cancelledCollected - $cancelledRefundTotal; @endphp
                <div style="font-size:18px;font-weight:800;color:{{ $netRetained >= 0 ? '#16a34a' : '#dc2626' }};">PKR {{ number_format(abs($netRetained)) }}</div>
                <div style="font-size:10px;color:#94a3b8;">{{ $netRetained >= 0 ? 'retained' : 'excess refund' }}</div>
            </div>
            @endif
        </div>
    </div>
    <a href="{{ route('index.booking', ['status'=>'cancelled']) }}"
       style="font-size:11px;font-weight:700;color:#dc2626;text-decoration:none;white-space:nowrap;padding:8px 14px;border:1.5px solid #fecaca;border-radius:9px;background:#fef2f2;">
        View All →
    </a>
</div>
@endif

{{-- ══ FEE COLLECTIONS ════════════════════════════════════════ --}}
<p class="sec-lbl">Fee Collections</p>
<div class="sg sg-4">

    {{-- Registry --}}
    <a href="{{ route('fee.management') }}" class="sc" style="--sc-accent:#1d4ed8;">
        <div class="sc-icon" style="background:#eff6ff;"><i class="bi bi-file-earmark-text-fill" style="color:#1d4ed8;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Registry Fees</div>
            <div class="sc-val" style="color:{{ $registryFeesTotal > 0 ? '#1d4ed8' : '#94a3b8' }};">{{ $fmt($registryFeesPaid) }}</div>
            <div class="sc-exact">{{ $exact($registryFeesPaid) }}</div>
            <div class="sc-sub">
                @if($registryFeesTotal <= 0)
                    <span style="color:#94a3b8;">No fees billed</span>
                @elseif($registryFeesRemaining > 0)
                    <span class="dn">{{ $fmt($registryFeesRemaining) }} remaining</span>
                @else
                    <span class="up">Cleared ✓</span>
                @endif
            </div>
        </div>
    </a>

    {{-- Development --}}
    <a href="{{ route('fee.management') }}" class="sc" style="--sc-accent:#16a34a;">
        <div class="sc-icon" style="background:#f0fdf4;"><i class="bi bi-building-fill" style="color:#16a34a;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Development Fees</div>
            <div class="sc-val" style="color:{{ $developmentFeesTotal > 0 ? '#16a34a' : '#94a3b8' }};">{{ $fmt($developmentFeesPaid) }}</div>
            <div class="sc-exact">{{ $exact($developmentFeesPaid) }}</div>
            <div class="sc-sub">
                @if($developmentFeesTotal <= 0)
                    <span style="color:#94a3b8;">No fees billed</span>
                @elseif($developmentFeesRemaining > 0)
                    <span class="dn">{{ $fmt($developmentFeesRemaining) }} remaining</span>
                @else
                    <span class="up">Cleared ✓</span>
                @endif
            </div>
        </div>
    </a>

    {{-- Security --}}
    <a href="{{ route('fee.management') }}" class="sc" style="--sc-accent:#7c3aed;">
        <div class="sc-icon" style="background:#fdf4ff;"><i class="bi bi-shield-lock-fill" style="color:#7c3aed;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Security Fees</div>
            <div class="sc-val" style="color:{{ $securityFeesPaid > 0 ? '#7c3aed' : '#94a3b8' }};">{{ $fmt($securityFeesPaid) }}</div>
            <div class="sc-exact">{{ $exact($securityFeesPaid) }}</div>
            <div class="sc-sub">
                @if($securityFeesPaid > 0)
                    <span class="up">{{ $fmt($securityFeesPaid) }} collected</span>
                @else
                    <span style="color:#94a3b8;">No payments yet</span>
                @endif
                &nbsp;·&nbsp;<span style="color:#94a3b8;font-size:10px;">Recurring</span>
            </div>
        </div>
    </a>

    {{-- Transfer --}}
    <a href="{{ route('fee.management') }}" class="sc" style="--sc-accent:#ca8a04;">
        <div class="sc-icon" style="background:#fefce8;"><i class="bi bi-arrow-repeat" style="color:#ca8a04;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Transfer Fees</div>
            <div class="sc-val" style="color:{{ $transferFeesTotal > 0 ? '#ca8a04' : '#94a3b8' }};">{{ $fmt($transferFeesPaid) }}</div>
            <div class="sc-exact">{{ $exact($transferFeesPaid) }}</div>
            <div class="sc-sub">
                @if($transferFeesTotal <= 0)
                    <span style="color:#94a3b8;">No transfers billed</span>
                @elseif($pendingFeeCount > 0)
                    <span class="wn">{{ $pendingFeeCount }} pending</span>
                    @if($transferFeesRemaining > 0)&nbsp;·&nbsp;<span class="dn">{{ $fmt($transferFeesRemaining) }} due</span>@endif
                @else
                    <span class="up">All cleared ✓</span>&nbsp;·&nbsp;{{ $exact($transferFeesTotal) }} billed
                @endif
            </div>
        </div>
    </a>

</div>

{{-- ══ OPERATIONS ══════════════════════════════════════════════ --}}
<p class="sec-lbl">Operations</p>
<div class="sg sg-3">
    <a href="{{ route('index.transfer') }}" class="sc" style="--sc-accent:#0ea5e9;">
        <div class="sc-icon" style="background:#f0f9ff;"><i class="bi bi-arrow-left-right" style="color:#0ea5e9;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Transfers</div>
            <div class="sc-val" style="color:#0ea5e9;">{{ number_format($totalTransfers) }}</div>
            <div class="sc-sub"><span class="up">{{ $completedTransfers }} done</span>@if($pendingTransfers > 0)&nbsp;·&nbsp;<span class="wn">{{ $pendingTransfers }} pending</span>@endif</div>
        </div>
    </a>
    <a href="{{ route('index.customer') }}" class="sc" style="--sc-accent:#7c3aed;">
        <div class="sc-icon" style="background:#fdf4ff;"><i class="bi bi-people-fill" style="color:#7c3aed;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Customers</div>
            <div class="sc-val" style="color:#7c3aed;">{{ number_format($totalCustomers) }}</div>
            <div class="sc-sub"><span class="up">+{{ $newThisMonth }}</span> this month &nbsp;·&nbsp; {{ $activeCustomers }} active</div>
        </div>
    </a>
    <a href="{{ route('office_expenses.view') }}" class="sc" style="--sc-accent:{{ $netBalance >= 0 ? '#16a34a' : '#dc2626' }};">
        <div class="sc-icon" style="background:{{ $netBalance >= 0 ? '#f0fdf4' : '#fef2f2' }};"><i class="bi bi-wallet2" style="color:{{ $netBalance >= 0 ? '#16a34a' : '#dc2626' }};"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Office Net Balance</div>
            <div class="sc-val" style="color:{{ $netBalance >= 0 ? '#16a34a' : '#dc2626' }};">{{ $fmt(abs($netBalance)) }}</div>
            <div class="sc-exact">{{ $netBalance >= 0 ? '↑ Surplus' : '↓ Deficit' }}</div>
            <div class="sc-sub">In: {{ $fmt($totalIncome) }} · Out: {{ $fmt($totalExpenses) }}</div>
        </div>
    </a>
</div>
<div class="sg sg-3">
    <a href="{{ route('office_expenses.view') }}" class="sc" style="--sc-accent:#dc2626;">
        <div class="sc-icon" style="background:#fef2f2;"><i class="bi bi-arrow-up-circle-fill" style="color:#dc2626;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Office Expenses</div>
            <div class="sc-val" style="color:#dc2626;">{{ $fmt($totalExpenses) }}</div>
            <div class="sc-exact">{{ $exact($totalExpenses) }}</div>
            <div class="sc-sub"><span class="dn">{{ $fmt($thisMonthExpenses) }}</span> this month</div>
        </div>
    </a>
    <a href="{{ route('office_expenses.view') }}" class="sc" style="--sc-accent:#16a34a;">
        <div class="sc-icon" style="background:#f0fdf4;"><i class="bi bi-arrow-down-circle-fill" style="color:#16a34a;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Office Income</div>
            <div class="sc-val" style="color:#16a34a;">{{ $fmt($totalIncome) }}</div>
            <div class="sc-exact">{{ $exact($totalIncome) }}</div>
            <div class="sc-sub"><span class="up">{{ $fmt($thisMonthIncome) }}</span> this month</div>
        </div>
    </a>
    <a href="{{ route('office_expenses.view') }}" class="sc" style="--sc-accent:#7c3aed;">
        <div class="sc-icon" style="background:#fdf4ff;"><i class="bi bi-box-seam-fill" style="color:#7c3aed;"></i></div>
        <div class="sc-body">
            <div class="sc-lbl">Inventory Value</div>
            <div class="sc-val" style="color:#7c3aed;">{{ $fmt($totalInventory) }}</div>
            <div class="sc-exact">{{ $exact($totalInventory) }}</div>
            <div class="sc-sub">Materials &amp; supplies</div>
        </div>
    </a>
</div>

{{-- ══ ANALYTICS ══════════════════════════════════════════════ --}}
<p class="sec-lbl">Analytics</p>
<div class="charts-grid">
    <div class="mc">
        <div class="mc-head">
            <div>
                <p class="mc-title">Monthly Collection — Last 6 Months</p>
                <p class="mc-sub">Plot price payments only</p>
            </div>
            <span style="font-size:11px;font-weight:800;color:#16a34a;">{{ $fmt($thisMonthCollection) }} this month</span>
        </div>
        <div class="mc-body"><canvas id="barChart" height="130"></canvas></div>
    </div>
    <div class="mc">
        <div class="mc-head"><div><p class="mc-title">Plot Status</p><p class="mc-sub">Inventory breakdown</p></div></div>
        <div class="mc-body" style="display:flex;flex-direction:column;align-items:center;">
            <canvas id="plotDoughnut" width="130" height="130"></canvas>
            <div style="margin-top:14px;width:100%;">
                @php $pc=['#10b981','#f59e0b','#3b82f6']; $pi=0; @endphp
                @foreach($plotStatusBreakdown as $lbl=>$val)
                <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:6px;align-items:center;">
                    <span style="display:flex;align-items:center;gap:6px;">
                        <span style="width:7px;height:7px;border-radius:50%;background:{{ $pc[$pi] }};display:inline-block;"></span>
                        <span style="font-weight:600;color:#64748b;">{{ $lbl }}</span>
                    </span>
                    <strong>{{ number_format($val) }}</strong>
                </div>
                @php $pi++; @endphp
                @endforeach
            </div>
        </div>
    </div>
    <div class="mc">
        <div class="mc-head"><div><p class="mc-title">Payment Mix</p><p class="mc-sub">By category</p></div></div>
        <div class="mc-body" style="display:flex;flex-direction:column;align-items:center;">
            <canvas id="catDoughnut" width="130" height="130"></canvas>
            <div style="margin-top:14px;width:100%;">
                @foreach($catLabels as $idx=>$lbl)
                <div style="display:flex;justify-content:space-between;font-size:10px;margin-bottom:5px;align-items:center;">
                    <span style="display:flex;align-items:center;gap:5px;">
                        <span style="width:6px;height:6px;border-radius:50%;background:{{ $catColors[$idx] }};display:inline-block;flex-shrink:0;"></span>
                        <span style="font-weight:600;color:#64748b;">{{ $lbl }}</span>
                    </span>
                    <strong>{{ $fmt($catVals[$idx]) }}</strong>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ══ QUICK ACTIONS ══════════════════════════════════════════ --}}
<p class="sec-lbl">Quick Actions</p>
<div class="qa-grid">
    @php $qa=[
        ['icon'=>'plus-circle-fill', 'label'=>'New Booking', 'route'=>'booking.search',  'c'=>'#1d4ed8','bg'=>'#eff6ff'],
        ['icon'=>'arrow-repeat',     'label'=>'Transfer',    'route'=>'transfers.search', 'c'=>'#0ea5e9','bg'=>'#f0f9ff'],
        ['icon'=>'wallet2',          'label'=>'Payment',     'route'=>'index.account',    'c'=>'#16a34a','bg'=>'#f0fdf4'],
        ['icon'=>'credit-card-fill', 'label'=>'Fee Mgmt',    'route'=>'fee.management',   'c'=>'#7c3aed','bg'=>'#fdf4ff'],
        ['icon'=>'graph-up-arrow',   'label'=>'Reports',     'route'=>'finance.report',   'c'=>'#ef4444','bg'=>'#fef2f2'],
        ['icon'=>'grid-3x3-gap-fill','label'=>'Plots',       'route'=>'index.plots',      'c'=>'#334155','bg'=>'#f8fafc'],
    ]; @endphp
    @foreach($qa as $a)
    <a href="{{ route($a['route']) }}" class="qa-btn" style="--qa-c:{{ $a['c'] }};--qa-bg:{{ $a['bg'] }};">
        <i class="bi bi-{{ $a['icon'] }}" style="color:{{ $a['c'] }};"></i>
        <span>{{ $a['label'] }}</span>
    </a>
    @endforeach
</div>

{{-- ══ RECENT ACTIVITY ════════════════════════════════════════ --}}
<p class="sec-lbl">Recent Activity</p>
<div class="bot-grid">
    <div class="mc">
        <div class="mc-head">
            <div>
                <p class="mc-title">Recent Bookings</p>
                <p class="mc-sub">Latest {{ $recentBookings->count() }} entries</p>
            </div>
            <a href="{{ route('index.booking') }}" class="mc-lnk">View All →</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="dt">
                <thead>
                    <tr><th>Ref</th><th>Customer</th><th>Plot</th><th>Total</th><th>Disscount</th><th>Paid</th><th>Remaining</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $b)
                    @php
                        $bClosed = in_array($b->status, ['transferred','cancelled','swapped','plot_relocated','completed']);
                        $bPaid = $b->payments->where('status','paid')->whereIn('payment_category',['down_payment','installment','quarterly_installment','plot_balance','others'])->sum('amount_paid');
                        $bDiscSentinel = 'Settlement discount — waived amount (not collected).';
                        $bDiscount = (float)($b->plot->discount_amount ?? 0)
                            + $b->payments->where(fn($p) => ($p->remarks ?? '') !== $bDiscSentinel)->sum('discount_amount');
                        $bRem  = $bClosed ? 0 : max(0, ($b->total_price ?? 0) - $bPaid);
                        $bp    = match($b->status) {
                            'active'      => 'pill-blue',
                            'completed'   => 'pill-green',
                            'transferred' => 'pill-purple',
                            'pending'     => 'pill-amber',
                            'cancelled'   => 'pill-red',
                            default       => 'pill-slate'
                        };
                    @endphp
                    <tr>
                        <td><strong style="font-size:11px;color:#1d4ed8;font-family:monospace;">{{ $b->customer_booking_id }}</strong></td>
                        <td style="font-size:12px;font-weight:600;">{{ $b->customer->name ?? '—' }}</td>
                        <td style="font-size:11px;font-weight:600;">#{{ $b->plot->plot_number ?? '—' }} <span style="color:#94a3b8;font-size:10px;">{{ $b->plot->block ?? '' }}</span></td>
                        <td style="font-size:11px;font-weight:700;">{{ $fmt($b->total_price) }}</td>
                        <td style="font-size:11px;font-weight:700;">{{ $bDiscount > 0 ? $fmt($bDiscount) : '—' }}</td>

                        <td style="font-size:11px;font-weight:700;color:#16a34a;">{{ $fmt($bPaid) }}</td>
                        <td style="font-size:11px;font-weight:700;color:{{ $bClosed ? '#94a3b8' : ($bRem > 0 ? '#dc2626' : '#16a34a') }};">
                            @if($bClosed) — @elseif($bRem > 0) {{ $fmt($bRem) }} @else ✓ Done @endif
                        </td>
                        <td><span class="pill {{ $bp }}"><span class="pill-dot"></span>{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
                        <td>
                            <a href="{{ route('ledger.view', $b->id) }}"
                               style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:4px 10px;border-radius:7px;font-size:10px;font-weight:700;text-decoration:none;">
                                Ledger
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:30px;color:#94a3b8;">No bookings yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:14px;">

        <div class="mc">
            <div class="mc-head">
                <div><p class="mc-title">Top Customers</p><p class="mc-sub">By total paid</p></div>
                <a href="{{ route('index.customer') }}" class="mc-lnk">All →</a>
            </div>
            <div class="mc-body" style="padding-top:8px;">
                @php $avC=['#eff6ff:#1d4ed8','#fdf4ff:#7c3aed','#f0fdf4:#16a34a','#fff7ed:#ea580c','#fef9c3:#ca8a04']; @endphp
                @forelse($topCustomers as $i=>$tc)
                @php [$bg,$fg]=explode(':',$avC[$i%5]); @endphp
                <div class="tc-row">
                    <div class="tc-av" style="background:{{ $bg }};color:{{ $fg }};">{{ strtoupper(substr($tc->name,0,1)) }}</div>
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $tc->name }}</div>
                        <div style="font-size:10px;color:#94a3b8;">Rank #{{ $i+1 }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-size:12px;font-weight:800;color:#16a34a;">{{ $fmt($tc->total_paid) }}</div>
                        <div style="font-size:10px;color:#94a3b8;">{{ $exact($tc->total_paid) }}</div>
                    </div>
                </div>
                @empty
                <p style="font-size:12px;color:#94a3b8;text-align:center;padding:16px 0;">No data</p>
                @endforelse
            </div>
        </div>

        <div class="mc">
            <div class="mc-head">
                <div><p class="mc-title">Recent Payments</p><p class="mc-sub">Last {{ $recentPayments->count() }}</p></div>
                <a href="{{ route('index.account') }}" class="mc-lnk">All →</a>
            </div>
            <div class="mc-body" style="padding-top:8px;">
                @forelse($recentPayments as $p)
                <div class="rp-row">
                    <div class="rp-ic"><i class="bi bi-cash"></i></div>
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $p->booking->customer->name ?? '—' }}</div>
                        <div style="font-size:10px;color:#94a3b8;">{{ ucwords(str_replace('_',' ',$p->payment_category)) }} · {{ \Carbon\Carbon::parse($p->paid_date)->format('d M Y') }}</div>
                    </div>
                    <div style="text-align:right;font-size:12px;font-weight:800;color:#16a34a;white-space:nowrap;flex-shrink:0;">{{ $fmt($p->amount_paid) }}</div>
                </div>
                @empty
                <p style="font-size:12px;color:#94a3b8;text-align:center;padding:16px 0;">No payments</p>
                @endforelse
            </div>
        </div>

        <div class="mc">
            <div class="mc-head"><div><p class="mc-title">Booking Breakdown</p></div></div>
            <div class="mc-body" style="padding-top:8px;">
                @php
                    $bkSum = [
                        ['Active',      $activeBookings,      '#3b82f6'],
                        ['Completed',   $completedBookings,   '#16a34a'],
                        ['Pending',     $pendingBookings,     '#d97706'],
                        ['Transferred', $transferredBookings, '#7c3aed'],
                        ['Cancelled',   $cancelledBookings,   '#dc2626'],
                    ];
                    $tBk = max($totalBookings, 1);
                @endphp
                @foreach($bkSum as [$lbl,$val,$col])
                <div style="margin-bottom:10px;">
                    <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:4px;">
                        <span style="font-weight:600;color:#64748b;">{{ $lbl }}</span>
                        <strong style="color:{{ $col }};">{{ number_format($val) }}</strong>
                    </div>
                    <div class="pbar">
                        <div class="pbar-fill" style="width:{{ min(round($val/$tBk*100),100) }}%;background:{{ $col }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- ══ INSTALLMENT ALERTS ══════════════════════════════════════ --}}
<p class="sec-lbl">Installment Alerts</p>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px;">

    {{-- Overdue --}}
    <div class="mc" style="border-color:{{ $overdueInstallments->count()>0?'#fca5a5':'#e2e8f0' }};margin-bottom:0;">
        <div class="mc-head" style="{{ $overdueInstallments->count()>0?'background:#fff8f8;border-bottom-color:#fecaca;':'' }}">
            <div>
                <p class="mc-title" style="color:{{ $overdueInstallments->count()>0?'#dc2626':'#0f172a' }};">⚠ Overdue Installments</p>
                <p class="mc-sub">{{ $overdueInstallments->count()>0?'Follow-up required':'No overdue' }}</p>
            </div>
            <span style="background:{{ $overdueInstallments->count()>0?'#fef2f2':'#f0fdf4' }};border:1px solid {{ $overdueInstallments->count()>0?'#fecaca':'#bbf7d0' }};color:{{ $overdueInstallments->count()>0?'#dc2626':'#16a34a' }};padding:3px 10px;border-radius:20px;font-size:10px;font-weight:800;">
                {{ $overdueInstallments->count()>0 ? $overdueInstallments->count().' overdue' : '✓ All clear' }}
            </span>
        </div>
        @if($overdueInstallments->count()>0)
        <div style="overflow-x:auto;">
            <table class="dt">
                <thead><tr><th>Customer</th><th>Plot</th><th>Month</th><th>Due</th><th>Days</th><th>Amount</th><th></th></tr></thead>
                <tbody>
                    @foreach($overdueInstallments->take(5) as $ov)
                    <tr style="background:#fff8f8;">
                        <td>
                            <div style="font-size:12px;font-weight:700;">{{ $ov->booking->customer->name ?? '—' }}</div>
                            <div style="font-size:10px;color:#94a3b8;">{{ $ov->booking->customer->phone ?? '' }}</div>
                        </td>
                        <td style="font-size:11px;font-weight:700;color:#1e3a8a;">#{{ $ov->booking->plot->plot_number ?? '—' }}</td>
                        <td><span style="font-size:11px;font-weight:800;background:#fef2f2;border:1px solid #fca5a5;color:#dc2626;padding:1px 7px;border-radius:6px;">#{{ $ov->next_installment }}</span></td>
                        <td style="font-size:11px;color:#dc2626;font-weight:600;">{{ \Carbon\Carbon::parse($ov->due_date)->format('d M Y') }}</td>
                        <td><span style="font-size:10px;font-weight:800;background:#fef2f2;color:#dc2626;padding:2px 7px;border-radius:5px;">{{ $ov->days_overdue }}d</span></td>
                        <td style="font-size:11px;font-weight:700;color:#dc2626;">{{ $exact($ov->monthly_installment) }}</td>
                        <td><a href="{{ route('ledger.view',$ov->booking->id) }}" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:3px 9px;border-radius:7px;font-size:10px;font-weight:700;text-decoration:none;">Ledger</a></td>
                    </tr>
                    @endforeach
                    @if($overdueInstallments->count()>5)
                    <tr><td colspan="7" style="text-align:center;padding:8px;font-size:11px;color:#94a3b8;">+{{ $overdueInstallments->count()-5 }} more — <a href="{{ route('finance.report') }}" style="color:#1d4ed8;font-weight:700;">View All</a></td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center;padding:30px;">
            <div style="font-size:2.2rem;">✅</div>
            <div style="font-size:12px;font-weight:700;color:#16a34a;margin-top:8px;">All installments on track</div>
        </div>
        @endif
    </div>

    {{-- Upcoming --}}
    <div class="mc" style="border-color:{{ $upcomingInstallments->count()>0?'#bfdbfe':'#e2e8f0' }};margin-bottom:0;">
        <div class="mc-head" style="{{ $upcomingInstallments->count()>0?'background:#f8fbff;border-bottom-color:#bfdbfe;':'' }}">
            <div>
                <p class="mc-title" style="color:{{ $upcomingInstallments->count()>0?'#1e3a8a':'#0f172a' }};">⏰ Upcoming — 30 Days</p>
                <p class="mc-sub">{{ $upcomingInstallments->count()>0?'Due soon':'Nothing due' }}</p>
            </div>
            <span style="background:{{ $upcomingInstallments->count()>0?'#eff6ff':'#f8fafc' }};border:1px solid {{ $upcomingInstallments->count()>0?'#bfdbfe':'#e2e8f0' }};color:{{ $upcomingInstallments->count()>0?'#1d4ed8':'#94a3b8' }};padding:3px 10px;border-radius:20px;font-size:10px;font-weight:800;">
                {{ $upcomingInstallments->count()>0 ? $upcomingInstallments->count().' upcoming' : 'None' }}
            </span>
        </div>
        @if($upcomingInstallments->count()>0)
        <div style="overflow-x:auto;">
            <table class="dt">
                <thead><tr><th>Customer</th><th>Plot</th><th>Month</th><th>Due</th><th>In</th><th>Amount</th><th></th></tr></thead>
                <tbody>
                    @foreach($upcomingInstallments->take(5) as $up)
                    <tr style="{{ $up->days_until_due<=3?'background:#fffbeb;':'' }}">
                        <td>
                            <div style="font-size:12px;font-weight:700;">{{ $up->booking->customer->name ?? '—' }}</div>
                            <div style="font-size:10px;color:#94a3b8;">{{ $up->booking->customer->phone ?? '' }}</div>
                        </td>
                        <td style="font-size:11px;font-weight:700;color:#1e3a8a;">#{{ $up->booking->plot->plot_number ?? '—' }}</td>
                        <td><span style="font-size:11px;font-weight:800;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:1px 7px;border-radius:6px;">#{{ $up->next_installment }}</span></td>
                        <td style="font-size:11px;color:#1e3a8a;font-weight:600;">{{ \Carbon\Carbon::parse($up->due_date)->format('d M Y') }}</td>
                        <td>
                            @if($up->days_until_due<=3)
                                <span style="font-size:10px;font-weight:800;background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:2px 7px;border-radius:5px;">{{ $up->days_until_due }}d ⚡</span>
                            @else
                                <span style="font-size:10px;font-weight:700;color:#1d4ed8;background:#eff6ff;padding:2px 7px;border-radius:5px;">{{ $up->days_until_due }}d</span>
                            @endif
                        </td>
                        <td style="font-size:11px;font-weight:700;color:#1e3a8a;">{{ $exact($up->monthly_installment) }}</td>
                        <td><a href="{{ route('ledger.view',$up->booking->id) }}" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:3px 9px;border-radius:7px;font-size:10px;font-weight:700;text-decoration:none;">Ledger</a></td>
                    </tr>
                    @endforeach
                    @if($upcomingInstallments->count()>5)
                    <tr><td colspan="7" style="text-align:center;padding:8px;font-size:11px;color:#94a3b8;">+{{ $upcomingInstallments->count()-5 }} more — <a href="{{ route('finance.report') }}" style="color:#1d4ed8;font-weight:700;">View All</a></td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center;padding:30px;">
            <div style="font-size:2.2rem;">📅</div>
            <div style="font-size:12px;font-weight:700;color:#64748b;margin-top:8px;">Nothing due soon</div>
        </div>
        @endif
    </div>

</div>

</div>{{-- /dw --}}
@endsection

@push('scripts')
<script>
window.ZV={
    ml:{!! $mlJson !!},
    mc:{!! $mcJson !!},
    ps:{l:{!! $psLabels !!},v:{!! $psVals !!}},
    ct:{l:{!! $catLbJ !!},v:{!! $catVlJ !!},c:{!! $catClJ !!}}
};
const fmtPKR=v=>{ v=parseFloat(v)||0; if(v>=10000000) return 'PKR '+(v/10000000).toFixed(2)+' Cr'; if(v>=100000) return 'PKR '+(v/100000).toFixed(1)+' L'; return 'PKR '+v.toLocaleString('en-PK'); };
window.addEventListener('load',function(){
    ['barChart','plotDoughnut','catDoughnut'].forEach(id=>{ const el=document.getElementById(id); if(!el) return; const ex=Chart.getChart(el); if(ex) ex.destroy(); });
    const d=window.ZV, grid='rgba(0,0,0,0.03)';
    const mcData=d.mc, mlData=d.ml;
    const nonZ=mcData.filter(x=>x>0);
    const maxV=nonZ.length?Math.max(...mcData):0, minV=nonZ.length?Math.min(...nonZ):0;
    const maxI=mcData.indexOf(maxV), minI=mcData.indexOf(minV), lastI=mcData.length-1;
    const barBg=mcData.map((v,i)=>i===lastI?'#16a34a':i===maxI?'#1d4ed8':i===minI&&v>0?'#fca5a5':'#bfdbfe');

    new Chart(document.getElementById('barChart'),{
        type:'bar',
        data:{labels:mlData,datasets:[{label:'Collection',data:mcData,backgroundColor:barBg,borderRadius:7,borderSkipped:false}]},
        options:{responsive:true,maintainAspectRatio:true,layout:{padding:{top:20}},
            plugins:{legend:{display:false},tooltip:{backgroundColor:'#1e293b',titleColor:'#94a3b8',bodyColor:'#f1f5f9',padding:12,cornerRadius:8,callbacks:{label:ctx=>'  '+fmtPKR(ctx.parsed.y)}}},
            scales:{x:{grid:{display:false},ticks:{color:'#94a3b8',font:{size:11}}},y:{grid:{color:grid},beginAtZero:true,ticks:{color:'#94a3b8',font:{size:11},maxTicksLimit:5,callback:v=>fmtPKR(v)}}}}
    });

    const psTotal=d.ps.v.reduce((a,b)=>a+b,0);
    new Chart(document.getElementById('plotDoughnut'),{type:'doughnut',data:{labels:d.ps.l,datasets:[{data:d.ps.v,backgroundColor:['#10b981','#f59e0b','#3b82f6'],borderWidth:2,borderColor:'#fff',hoverOffset:6}]},options:{cutout:'70%',plugins:{legend:{display:false},tooltip:{backgroundColor:'#1e293b',bodyColor:'#f1f5f9',padding:10,cornerRadius:8,callbacks:{label:ctx=>'  '+ctx.label+': '+ctx.parsed+' ('+(psTotal?Math.round(ctx.parsed/psTotal*100):0)+'%)'}}}}});

    const catTotal=d.ct.v.reduce((a,b)=>a+b,0);
    new Chart(document.getElementById('catDoughnut'),{type:'doughnut',data:{labels:d.ct.l,datasets:[{data:d.ct.v,backgroundColor:d.ct.c,borderWidth:2,borderColor:'#fff',hoverOffset:6}]},options:{cutout:'70%',plugins:{legend:{display:false},tooltip:{backgroundColor:'#1e293b',bodyColor:'#f1f5f9',padding:10,cornerRadius:8,callbacks:{label:ctx=>'  '+ctx.label+': '+fmtPKR(ctx.parsed)+' ('+(catTotal?Math.round(ctx.parsed/catTotal*100):0)+'%)'}}}}});
});
</script>
@endpush
