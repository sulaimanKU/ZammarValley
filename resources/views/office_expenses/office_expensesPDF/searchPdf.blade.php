<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Expenses Report — {{ $from ?? 'All' }} to {{ $to ?? 'All' }}</title>
<style>
@page { margin: 0.3in 0.4in; }
* { box-sizing: border-box; -webkit-print-color-adjust: exact; }
body { font-family: 'DejaVu Sans', 'Helvetica', sans-serif; font-size: 8.5px; color: #1e293b; line-height: 1.5; margin: 0; padding: 0; background: #fff; }

.watermark { position: fixed; top: 38%; left: 12%; font-size: 70px; color: rgba(226,232,240,0.28); transform: rotate(-40deg); z-index: -999; font-weight: 900; text-transform: uppercase; letter-spacing: 4px; }

/* Header */
.hdr { border-bottom: 3px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 14px; }
.soc-name { font-size: 20px; font-weight: 900; color: #1e3a8a; }
.soc-sub  { font-size: 7.5px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
.doc-badge { background: #1e3a8a; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase; display: inline-block; }

/* Info cards */
.info-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
.icard { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 7px 10px; }
.ilbl  { font-size: 6.5px; color: #64748b; text-transform: uppercase; letter-spacing: .6px; font-weight: 700; margin-bottom: 2px; }
.ival  { font-size: 11px; font-weight: 800; color: #0f172a; }

/* Filter pill bar */
.filter-bar { background: #f1f5f9; border-radius: 4px; padding: 5px 10px; font-size: 7.5px; color: #475569; margin-bottom: 12px; }

/* Summary row */
.sum-row { display: table; width: 100%; border-collapse: collapse; margin-bottom: 14px; }
.sum-cell { display: table-cell; text-align: center; border-radius: 6px; padding: 8px 6px; }
.sv { font-size: 13px; font-weight: 900; }
.sl { font-size: 7px; text-transform: uppercase; letter-spacing: .4px; opacity: .7; margin-top: 1px; }

/* Main table */
.exp-table { width: 100%; border-collapse: collapse; }
.exp-table th { background: #1e3a8a; color: #fff; padding: 6px 7px; font-size: 7.5px; text-transform: uppercase; letter-spacing: .4px; text-align: left; }
.exp-table td { padding: 6px 7px; border-bottom: 1px solid #f1f5f9; font-size: 8px; vertical-align: top; }
.exp-table tr:nth-child(even) td { background: #fafafa; }

/* Type badges */
.te { background: #fef2f2; color: #b91c1c; padding: 1px 5px; border-radius: 8px; font-size: 6.5px; font-weight: 800; text-transform: uppercase; }
.ti { background: #dcfce7; color: #15803d; padding: 1px 5px; border-radius: 8px; font-size: 6.5px; font-weight: 800; text-transform: uppercase; }
.tn { background: #d1fae5; color: #065f46; padding: 1px 5px; border-radius: 8px; font-size: 6.5px; font-weight: 800; text-transform: uppercase; }

/* Status */
.sa { background: #dcfce7; color: #15803d; padding: 1px 5px; border-radius: 8px; font-size: 6.5px; font-weight: 800; }
.sp { background: #fef9c3; color: #854d0e; padding: 1px 5px; border-radius: 8px; font-size: 6.5px; font-weight: 800; }

/* Totals */
.tfoot-row td { background: #1e3a8a; color: #fff; font-weight: 800; font-size: 9px; padding: 7px; }
.tr { text-align: right; }

/* Signature */
.sig { margin-top: 35px; }
.sig-box { text-align: center; border-top: 1px solid #94a3b8; padding-top: 4px; font-size: 7.5px; color: #64748b; width: 130px; }

/* Footer */
.doc-footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 6.5px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 3px; }
</style>
</head>
<body>

@php
use Carbon\Carbon;
use App\Models\SystemConfig;
$societyName = SystemConfig::get('society_name', 'ZAMAR VALLEY');
$nowFmt = now()->format('d M Y, h:i A');
$totalAll = $expenses->sum('amount');
$expTotal = $expenseTotal ?? $expenses->where('type','expense')->where('status','approved')->sum('amount');
$incTotal = $incomeTotal  ?? $expenses->where('type','income')->where('status','approved')->sum('amount');
$invTotal = $inventoryTotal ?? $expenses->where('type','inventory')->where('status','approved')->sum('amount');
$netBal   = $incTotal - $expTotal - $invTotal;

$fundLabels = [
    'plot_payments'   => 'Plot Pmts',
    'security_fee'    => 'Security',
    'registry_fee'    => 'Registry',
    'development_fee' => 'Dev. Fee',
    'transfer_fee'    => 'Transfer',
    'misc_income'     => 'Misc.',
];
@endphp

<div class="watermark">REPORT</div>

{{-- Header --}}
<div class="hdr">
    <table width="100%"><tr>
        <td>
            <div class="soc-name">{{ strtoupper($societyName) }}</div>
            <div class="soc-sub">Real Estate Development &amp; Society Management</div>
        </td>
        <td style="text-align:right;vertical-align:top;">
            <div class="doc-badge">Expenses &amp; Income Report</div>
            <div style="font-size:7px;color:#64748b;margin-top:4px;">Printed: {{ $nowFmt }}</div>
        </td>
    </tr></table>
</div>

{{-- Info strip --}}
<table class="info-table" cellspacing="4">
    <tr>
        <td width="22%"><div class="icard"><div class="ilbl">Period From</div><div class="ival">{{ $from ?? 'All' }}</div></div></td>
        <td width="22%"><div class="icard"><div class="ilbl">Period To</div><div class="ival">{{ $to ?? 'All' }}</div></div></td>
        <td width="22%"><div class="icard"><div class="ilbl">Total Records</div><div class="ival">{{ $expenses->count() }}</div></div></td>
        <td width="18%"><div class="icard"><div class="ilbl">Net Balance</div><div class="ival" style="color:{{ $netBal >= 0 ? '#15803d' : '#b91c1c' }};">PKR {{ number_format(abs($netBal)) }}</div></div></td>
        <td width="16%" style="text-align:center;vertical-align:middle;">
            @if(isset($qrCode))
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="55" height="55" style="border:1px solid #e2e8f0;padding:2px;">
            @endif
        </td>
    </tr>
</table>

{{-- Active filters --}}
@if(!empty($filterSummary))
<div class="filter-bar">
    <strong style="color:#1e3a8a;">Active Filters:</strong> &nbsp;{{ $filterSummary }}
</div>
@endif

{{-- Summary blocks --}}
<table style="width:100%;border-collapse:collapse;margin-bottom:14px;" cellspacing="3">
    <tr>
        <td style="text-align:center;background:#fef2f2;border-radius:5px;padding:8px;border:1px solid #fecaca;">
            <div style="font-size:12px;font-weight:900;color:#b91c1c;">PKR {{ number_format($expTotal) }}</div>
            <div style="font-size:7px;color:#b91c1c;font-weight:700;text-transform:uppercase;margin-top:1px;">Expenses (Approved)</div>
        </td>
        <td width="6"></td>
        <td style="text-align:center;background:#f0fdf4;border-radius:5px;padding:8px;border:1px solid #86efac;">
            <div style="font-size:12px;font-weight:900;color:#15803d;">PKR {{ number_format($incTotal) }}</div>
            <div style="font-size:7px;color:#15803d;font-weight:700;text-transform:uppercase;margin-top:1px;">Income (Approved)</div>
        </td>
        <td width="6"></td>
        <td style="text-align:center;background:#ecfdf5;border-radius:5px;padding:8px;border:1px solid #6ee7b7;">
            <div style="font-size:12px;font-weight:900;color:#065f46;">PKR {{ number_format($invTotal) }}</div>
            <div style="font-size:7px;color:#065f46;font-weight:700;text-transform:uppercase;margin-top:1px;">Inventory (Approved)</div>
        </td>
        <td width="6"></td>
        <td style="text-align:center;background:{{ $netBal >= 0 ? '#f0fdf4' : '#fef2f2' }};border-radius:5px;padding:8px;border:1px solid {{ $netBal >= 0 ? '#86efac' : '#fecaca' }};">
            <div style="font-size:12px;font-weight:900;color:{{ $netBal >= 0 ? '#15803d' : '#b91c1c' }};">{{ $netBal >= 0 ? '+' : '-' }} PKR {{ number_format(abs($netBal)) }}</div>
            <div style="font-size:7px;color:{{ $netBal >= 0 ? '#15803d' : '#b91c1c' }};font-weight:700;text-transform:uppercase;margin-top:1px;">Net Balance</div>
        </td>
    </tr>
</table>

{{-- Main table --}}
<table class="exp-table">
    <thead>
        <tr>
            <th width="4%">#</th>
            <th width="9%">Date</th>
            <th width="7%">Type</th>
            <th width="13%">Category</th>
            <th width="10%">Fund Source</th>
            <th>Paid To / Party</th>
            <th width="9%">Method</th>
            <th width="8%">Voucher</th>
            <th width="7%">Status</th>
            <th width="11%" style="text-align:right;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $rec)
        <tr>
            <td style="color:#94a3b8;">{{ $loop->iteration }}</td>
            <td>{{ Carbon::parse($rec->expense_date)->format('d-m-Y') }}</td>
            <td>
                @if($rec->type==='income') <span class="ti">Income</span>
                @elseif($rec->type==='inventory') <span class="tn">Inventory</span>
                @else <span class="te">Expense</span>
                @endif
            </td>
            <td><strong>{{ $rec->category }}</strong></td>
            <td style="font-size:7.5px;">{{ $rec->fund_source ? ($fundLabels[$rec->fund_source] ?? $rec->fund_source) : '—' }}</td>
            <td>
                <div style="font-weight:700;">{{ $rec->paid_to }}</div>
                @if($rec->remarks)<div style="font-size:7px;color:#64748b;max-width:120px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $rec->remarks }}</div>@endif
            </td>
            <td>{{ ucwords(str_replace('_',' ',$rec->payment_method ?? '')) }}</td>
            <td style="font-family:monospace;font-size:7.5px;color:#1d4ed8;">{{ $rec->voucher_no ?? '—' }}</td>
            <td>
                <span class="{{ $rec->status==='approved' ? 'sa' : 'sp' }}">{{ ucfirst($rec->status) }}</span>
            </td>
            <td style="text-align:right;font-weight:800;color:{{ $rec->type==='expense' ? '#b91c1c' : '#15803d' }};">
                {{ number_format($rec->amount) }}
            </td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;padding:18px;color:#94a3b8;font-style:italic;">No records match the selected filters.</td></tr>
        @endforelse
    </tbody>
    @if($expenses->count() > 0)
    <tfoot>
        <tr class="tfoot-row">
            <td colspan="8"></td>
            <td style="text-align:right;background:#1e3a8a;color:#fff;font-size:8.5px;">TOTAL (ALL)</td>
            <td style="text-align:right;background:#1e3a8a;color:#fff;font-size:11px;">PKR {{ number_format($totalAll) }}</td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- Signature --}}
<div class="sig">
    <table width="100%"><tr>
        <td width="33%" style="text-align:center;"><div class="sig-box" style="margin:0 auto;">Prepared By</div></td>
        <td width="33%" style="text-align:center;">
            <div style="width:60px;height:60px;border:2px dashed #cbd5e1;border-radius:50%;margin:-10px auto 0;padding-top:18px;color:#cbd5e1;font-weight:900;font-size:8px;text-align:center;">STAMP</div>
        </td>
        <td width="33%" style="text-align:center;"><div class="sig-box" style="margin:0 auto;">Authorized By</div></td>
    </tr></table>
</div>

<div class="doc-footer">
    {{ $societyName }} ERP &mdash; Expenses Report &mdash; {{ $expenses->count() }} records &mdash; Printed {{ $nowFmt }}
</div>

</body>
</html>
