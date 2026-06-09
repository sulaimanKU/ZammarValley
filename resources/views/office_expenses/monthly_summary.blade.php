@extends('layouts.index')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --navy: #0f172a; --blue: #1e3a8a; --green: #16a34a;
    --red: #dc2626; --slate: #64748b; --border: #e8edf3;
    --bg: #f1f5fb; --card: #ffffff; --radius: 14px;
    --shadow: 0 2px 12px rgba(15,23,42,.07);
}
* { box-sizing: border-box; }
body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; }

.page-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    border-radius: 18px; padding: 22px 28px; margin-bottom: 22px;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 14px; position: relative; overflow: hidden;
}
.page-header-title { font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0; }
.page-header-sub   { font-size: 11px; color: rgba(255,255,255,.5); margin: 4px 0 0; }

.year-bar {
    background: var(--card); border-radius: var(--radius);
    border: 1px solid var(--border); box-shadow: var(--shadow);
    padding: 14px 20px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}

.annual-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(180px,1fr));
    gap: 14px; margin-bottom: 22px;
}
.annual-card {
    border-radius: var(--radius); padding: 18px;
    display: flex; align-items: center; gap: 14px; transition: transform .2s;
}
.annual-card:hover { transform: translateY(-2px); }
.annual-card.income    { background: linear-gradient(135deg,#f0fdf4,#dcfce7); border:1.5px solid #bbf7d0; }
.annual-card.fee       { background: linear-gradient(135deg,#fffbeb,#fef3c7); border:1.5px solid #fde68a; }
.annual-card.misc      { background: linear-gradient(135deg,#fefce8,#fef9c3); border:1.5px solid #fde68a; }
.annual-card.inventory { background: linear-gradient(135deg,#fdf4ff,#f3e8ff); border:1.5px solid #e9d5ff; }
.annual-card.expense   { background: linear-gradient(135deg,#fef2f2,#ffe4e6); border:1.5px solid #fecaca; }
.annual-card.net       { background: #fff; border:1.5px solid var(--border); box-shadow:var(--shadow); }
.annual-card.transfer  { background: linear-gradient(135deg,#ecfeff,#cffafe); border:1.5px solid #a5f3fc; }
.annual-icon { width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0; }
.annual-label { font-size:10px;font-weight:700;color:var(--slate);text-transform:uppercase;letter-spacing:.6px; }
.annual-val   { font-size:18px;font-weight:800;margin-top:4px; }

.main-card { background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden; }
.card-toolbar { padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px; }
.card-title { font-size:13px;font-weight:800;color:var(--navy);margin:0; }

.monthly-table { width:100%;border-collapse:collapse; }
.monthly-table thead th {
    font-size:10px;text-transform:uppercase;letter-spacing:.6px;
    color:var(--slate);font-weight:700;background:#fafbfc;
    border-bottom:1.5px solid var(--border);padding:10px 12px;
    white-space:nowrap;
}
.monthly-table tbody td { padding:12px;border-bottom:1px solid #f8fafc;vertical-align:middle; }
.monthly-table tbody tr:last-child td { border-bottom:none; }
.monthly-table tbody tr:hover { background:#fafcff; }
.monthly-table tfoot td { padding:12px;background:#0f172a;color:#fff;font-weight:800;font-size:12px; }
.current-month { background:#fffbeb !important; }
.current-month td { border-bottom:1px solid #fde68a !important; }

.net-badge { font-size:11px;font-weight:800;padding:4px 10px;border-radius:8px;white-space:nowrap; }
.net-pos { background:#dcfce7;color:#15803d; }
.net-neg { background:#ffe4e6;color:#dc2626; }
.net-zero{ background:#f1f5f9;color:#475569; }

.pdf-btn {
    display:inline-flex;align-items:center;gap:6px;
    padding:7px 14px;border-radius:9px;font-size:11px;font-weight:700;
    border:none;cursor:pointer;text-decoration:none;transition:opacity .15s;
}
.pdf-btn:hover { opacity:.85; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">

{{-- Header --}}
<div class="page-header">
    <div>
        <p class="page-header-title"><i class="bi bi-bar-chart-fill" style="margin-right:8px;opacity:.7;"></i>Monthly Income vs Expense</p>
        <p class="page-header-sub">Full year breakdown — collections, fees, miscellaneous, transfers, and expenses</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;position:relative;z-index:1;">
        <a href="{{ route('reports.daily_cash') }}" style="background:rgba(255,255,255,.15);color:#fff;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="bi bi-calendar-day"></i> Daily Cash
        </a>
        <a href="{{ route('office_expenses.view') }}" style="background:rgba(255,255,255,.15);color:#fff;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

{{-- Year Switcher + PDF Buttons --}}
<div class="year-bar">
    <i class="bi bi-calendar-range" style="font-size:18px;color:var(--blue);"></i>
    <span style="font-size:13px;font-weight:700;color:var(--navy);">Year:</span>
    <div style="display:flex;gap:6px;flex-wrap:wrap;">
        @foreach($availableYears as $y)
        <a href="{{ route('reports.monthly_summary', ['year' => $y]) }}"
           style="padding:6px 14px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;
                  {{ $y == $year ? 'background:var(--blue);color:#fff;' : 'background:#f1f5f9;color:var(--slate);border:1px solid var(--border);' }}">
            {{ $y }}
        </a>
        @endforeach
    </div>
    <span style="font-size:16px;font-weight:800;color:var(--navy);">{{ $year }}</span>
    <div style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('reports.yearly_pdf', ['year' => $year]) }}"
           class="pdf-btn" style="background:#0f172a;color:#fff;">
            <i class="bi bi-file-earmark-pdf-fill"></i> {{ $year }} Annual PDF
        </a>
    </div>
</div>

{{-- Annual Summary Cards --}}
<div class="annual-grid">
    <div class="annual-card income">
        <div class="annual-icon" style="background:#f0fdf4;"><i class="bi bi-house-fill" style="color:#16a34a;font-size:1.3rem;"></i></div>
        <div>
            <div class="annual-label">Plot Collections</div>
            <div class="annual-val" style="color:#16a34a;">PKR {{ number_format(collect($months)->sum('plot_income')) }}</div>
            <div style="font-size:10px;color:#64748b;margin-top:2px;">Booking payments</div>
        </div>
    </div>
    <div class="annual-card fee">
        <div class="annual-icon" style="background:#fefce8;"><i class="bi bi-receipt-cutoff" style="color:#ca8a04;font-size:1.3rem;"></i></div>
        <div>
            <div class="annual-label">Fee Collections</div>
            <div class="annual-val" style="color:#ca8a04;">PKR {{ number_format($yearTotalFeeIncome) }}</div>
            <div style="font-size:10px;color:#64748b;margin-top:2px;">Registry, Dev, Security, Transfer</div>
        </div>
    </div>
    <div class="annual-card" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1.5px solid #93c5fd;">
        <div class="annual-icon" style="background:#eff6ff;"><i class="bi bi-arrow-down-circle-fill" style="color:#0369a1;font-size:1.3rem;"></i></div>
        <div>
            <div class="annual-label">Office Income</div>
            <div class="annual-val" style="color:#0369a1;">PKR {{ number_format(collect($months)->sum('office_income')) }}</div>
            <div style="font-size:10px;color:#64748b;margin-top:2px;">Tube well, rent, etc.</div>
        </div>
    </div>
    <div class="annual-card misc">
        <div class="annual-icon" style="background:#fefce8;"><i class="bi bi-cash-coin" style="color:#d97706;font-size:1.3rem;"></i></div>
        <div>
            <div class="annual-label">Misc. Income</div>
            <div class="annual-val" style="color:#d97706;">PKR {{ number_format($yearTotalMiscIncome) }}</div>
            <div style="font-size:10px;color:#64748b;margin-top:2px;">Unclassified receipts</div>
        </div>
    </div>
    <div class="annual-card transfer">
        <div class="annual-icon" style="background:#ecfeff;"><i class="bi bi-arrow-left-right" style="color:#0891b2;font-size:1.3rem;"></i></div>
        <div>
            <div class="annual-label">Total Transfers</div>
            <div class="annual-val" style="color:#0891b2;">{{ $yearTotalTransfers }}</div>
            <div style="font-size:10px;color:#64748b;margin-top:2px;">Deed / ownership changes</div>
        </div>
    </div>
    <div class="annual-card inventory">
        <div class="annual-icon" style="background:#f5f3ff;"><i class="bi bi-box-seam-fill" style="color:#7c3aed;font-size:1.3rem;"></i></div>
        <div>
            <div class="annual-label">Inventory</div>
            <div class="annual-val" style="color:#7c3aed;">PKR {{ number_format($yearTotalInventory) }}</div>
            <div style="font-size:10px;color:#64748b;margin-top:2px;">Materials & stock</div>
        </div>
    </div>
    <div class="annual-card expense">
        <div class="annual-icon" style="background:#fef2f2;"><i class="bi bi-arrow-up-circle-fill" style="color:#dc2626;font-size:1.3rem;"></i></div>
        <div>
            <div class="annual-label">Total Expenses</div>
            <div class="annual-val" style="color:#dc2626;">PKR {{ number_format($yearTotalExpenses) }}</div>
            <div style="font-size:10px;color:#64748b;margin-top:2px;">All approved expenditure</div>
        </div>
    </div>
    <div class="annual-card net">
        <div class="annual-icon" style="background:#eff6ff;"><i class="{{ $yearNet >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }} bi" style="color:{{ $yearNet >= 0 ? '#1d4ed8' : '#dc2626' }};font-size:1.3rem;"></i></div>
        <div>
            <div class="annual-label">Net Balance</div>
            <div class="annual-val" style="color:{{ $yearNet >= 0 ? '#1d4ed8' : '#dc2626' }};">
                {{ $yearNet >= 0 ? '+' : '' }}PKR {{ number_format($yearNet) }}
            </div>
            <div style="font-size:10px;color:#64748b;margin-top:2px;">Income minus all expenses</div>
        </div>
    </div>
</div>

{{-- Fund Sources Overview --}}
<div style="margin-bottom:22px;">
    <div style="font-size:12px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.7px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
        <i class="bi bi-wallet2"></i> Fund Sources — All-Time Collected vs. Used
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:12px;">
        @foreach($fundSources as $key => $fs)
        @php $pct = $fs['collected'] > 0 ? min(100, round($fs['used'] / $fs['collected'] * 100)) : 0; @endphp
        <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 18px;border-left:4px solid {{ $fs['color'] }};">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                <span style="font-size:18px;">{{ $fs['icon'] }}</span>
                <span style="font-size:12px;font-weight:800;color:#0f172a;">{{ $fs['label'] }}</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:5px;margin-bottom:10px;text-align:center;">
                <div style="background:#f0fdf4;border-radius:8px;padding:7px 4px;">
                    <div style="font-size:9px;color:#64748b;font-weight:700;margin-bottom:2px;text-transform:uppercase;">Collected</div>
                    <div style="font-size:11px;font-weight:800;color:#16a34a;">{{ number_format($fs['collected']) }}</div>
                </div>
                <div style="background:#fef2f2;border-radius:8px;padding:7px 4px;">
                    <div style="font-size:9px;color:#64748b;font-weight:700;margin-bottom:2px;text-transform:uppercase;">Used</div>
                    <div style="font-size:11px;font-weight:800;color:#dc2626;">{{ number_format($fs['used']) }}</div>
                </div>
                <div style="background:{{ $fs['bg'] }};border-radius:8px;padding:7px 4px;">
                    <div style="font-size:9px;color:#64748b;font-weight:700;margin-bottom:2px;text-transform:uppercase;">Left</div>
                    <div style="font-size:11px;font-weight:800;color:{{ $fs['color'] }};">{{ number_format($fs['remaining']) }}</div>
                </div>
            </div>
            <div style="background:#f1f5f9;border-radius:20px;height:5px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $fs['color'] }};border-radius:20px;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;color:#94a3b8;margin-top:4px;">
                <span>{{ $pct }}% used all-time</span>
                @if($fs['used_this_year'] > 0)
                <span style="color:{{ $fs['color'] }};font-weight:700;">PKR {{ number_format($fs['used_this_year']) }} in {{ $year }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Monthly Breakdown Table --}}
@php
    $maxIncome = collect($months)->max('total_income') ?: 1;
    $maxExp    = collect($months)->max(fn($m) => $m['expenses'] + $m['inventory']) ?: 1;
    $maxBar    = max($maxIncome, $maxExp);
@endphp
<div class="main-card">
    <div class="card-toolbar">
        <div>
            <p class="card-title">Month-by-Month Breakdown — {{ $year }}</p>
            <p style="font-size:11px;color:var(--slate);margin:2px 0 0;">All income sources, transfers, expenses and net balance per month</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button onclick="downloadYearlyPdf()" class="pdf-btn" style="background:#0f172a;color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> Yearly PDF
            </button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="monthly-table">
            <thead>
                <tr>
                    <th style="min-width:90px;">Month</th>
                    <th>Plot Income</th>
                    <th>Fee Income</th>
                    <th>Office Income</th>
                    <th>Misc Income</th>
                    <th>Total In</th>
                    <th>Expenses</th>
                    <th>Inventory</th>
                    <th>Transfers</th>
                    <th style="text-align:right;">Net Balance</th>
                    <th style="text-align:center;">Monthly PDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach($months as $m => $data)
                @php
                    $isCurrent = ($m == now()->month && $year == now()->year);
                    $netClass  = $data['net'] > 0 ? 'net-pos' : ($data['net'] < 0 ? 'net-neg' : 'net-zero');
                    $hasData   = $data['total_income'] > 0 || $data['expenses'] > 0 || $data['inventory'] > 0;
                    $totalOut  = $data['expenses'] + $data['inventory'];
                @endphp
                <tr class="{{ $isCurrent ? 'current-month' : '' }}" style="{{ !$hasData ? 'opacity:.45;' : '' }}">
                    <td>
                        <div style="font-weight:800;font-size:13px;color:var(--navy);">{{ $data['name'] }}</div>
                        @if($isCurrent)<span style="font-size:9px;background:#fef9c3;color:#92400e;padding:1px 7px;border-radius:20px;font-weight:700;">Current</span>@endif
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:#16a34a;">PKR {{ number_format($data['plot_income']) }}</div>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:#ca8a04;">PKR {{ number_format($data['fee_income']) }}</div>
                        <div style="font-size:9px;color:var(--slate);">Fees</div>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:#0369a1;">PKR {{ number_format($data['office_income']) }}</div>
                        <div style="font-size:9px;color:var(--slate);">Office</div>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:#d97706;">PKR {{ number_format($data['misc_income']) }}</div>
                        <div style="font-size:9px;color:var(--slate);">Misc</div>
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:800;color:#065f46;">PKR {{ number_format($data['total_income']) }}</div>
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:800;color:#dc2626;">PKR {{ number_format($data['expenses']) }}</div>
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:800;color:#7c3aed;">PKR {{ number_format($data['inventory']) }}</div>
                    </td>
                    <td style="text-align:center;">
                        @if($data['transfer_count'] > 0)
                        <span style="font-size:12px;font-weight:800;color:#0891b2;background:#ecfeff;padding:3px 10px;border-radius:8px;">{{ $data['transfer_count'] }}</span>
                        @else
                        <span style="color:#cbd5e1;font-size:11px;">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <span class="net-badge {{ $netClass }}">
                            {{ $data['net'] >= 0 ? '+' : '' }}PKR {{ number_format($data['net']) }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if($hasData)
                        <a href="{{ route('reports.monthly_pdf', ['year' => $year, 'month' => $m]) }}"
                           class="pdf-btn" style="background:#dc2626;color:#fff;font-size:10px;padding:5px 10px;">
                            <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                        </a>
                        @else
                        <span style="color:#cbd5e1;font-size:10px;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>ANNUAL TOTAL</td>
                    <td style="color:#86efac;">PKR {{ number_format(collect($months)->sum('plot_income')) }}</td>
                    <td style="color:#fde68a;">PKR {{ number_format($yearTotalFeeIncome) }}</td>
                    <td style="color:#7dd3fc;">PKR {{ number_format(collect($months)->sum('office_income')) }}</td>
                    <td style="color:#fde68a;">PKR {{ number_format($yearTotalMiscIncome) }}</td>
                    <td style="color:#6ee7b7;font-size:14px;">PKR {{ number_format($yearTotalIncome) }}</td>
                    <td style="color:#fca5a5;font-size:14px;">PKR {{ number_format($yearTotalExpenses) }}</td>
                    <td style="color:#d8b4fe;font-size:14px;">PKR {{ number_format($yearTotalInventory) }}</td>
                    <td style="color:#67e8f9;text-align:center;">{{ $yearTotalTransfers }}</td>
                    <td style="text-align:right;font-size:15px;color:{{ $yearNet >= 0 ? '#6ee7b7' : '#fca5a5' }};">
                        {{ $yearNet >= 0 ? '+' : '' }}PKR {{ number_format($yearNet) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
function downloadYearlyPdf() {
    window.location.href = '{{ route('reports.yearly_pdf', ['year' => $year]) }}';
}
</script>
@endpush
