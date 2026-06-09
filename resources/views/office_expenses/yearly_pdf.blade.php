<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; }
td, th { padding: 0; }

/* Summary table */
.sum-tbl td { padding: 8px 11px; border: 1px solid #e2e8f0; font-size: 10px; }
.sum-tbl .lbl { color: #475569; font-weight: bold; }
.sum-tbl .val { text-align: right; font-weight: bold; font-size: 13px; }

/* Monthly breakdown table */
.mon-tbl th {
    background: #0f172a; color: #fff; padding: 7px 8px;
    font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.4px;
    border: 1px solid #1e293b; text-align: left;
}
.mon-tbl td { border: 1px solid #e2e8f0; padding: 7px 8px; font-size: 9px; vertical-align: middle; }
.mon-tbl tr.alt { background: #f8fafc; }
.mon-tbl tr.current { background: #fffbeb; }
.mon-tbl tfoot td {
    background: #0f172a; color: #fff; padding: 8px 8px;
    font-size: 10px; font-weight: bold; border: 1px solid #1e293b;
}
.mon-tbl tfoot td.in  { color: #86efac; }
.mon-tbl tfoot td.out { color: #fca5a5; }
.mon-tbl tfoot td.net-pos { color: #6ee7b7; font-size: 12px; }
.mon-tbl tfoot td.net-neg { color: #f87171; font-size: 12px; }

/* Fund source table */
.fund-tbl th {
    background: #1e3a8a; color: #fff; padding: 6px 8px;
    font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.4px; border: 1px solid #1e40af;
}
.fund-tbl td { border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 9px; vertical-align: middle; }
.fund-tbl tr.alt { background: #f8fafc; }

.right { text-align: right; }
.center { text-align: center; }
.muted { color: #64748b; }
.spacer { height: 10px; }
.pos { color: #15803d; font-weight: bold; }
.neg { color: #dc2626; font-weight: bold; }

/* Bar */
.bar-bg { background: #f1f5f9; border-radius: 3px; height: 5px; width: 100%; overflow: hidden; }
.bar-fill { height: 5px; border-radius: 3px; }
</style>
</head>
<body>

{{-- ══ LETTERHEAD ══ --}}
<table style="margin-bottom:10px; border-bottom: 2px solid #0f172a; padding-bottom:8px;">
<tr>
    @if(!empty($society['logo']) && $society['show_logo'])
    <td style="width:65px; vertical-align:middle;">
        <img src="{{ $society['logo'] }}" style="width:55px;height:55px;object-fit:contain;">
    </td>
    @endif
    <td style="vertical-align:middle; padding-left:10px;">
        <div style="font-size:18px;font-weight:bold;color:#0f172a;">{{ $society['name'] }}</div>
        <div style="font-size:9px;color:#64748b;margin-top:2px;">{{ $society['tagline'] }}</div>
        <div style="font-size:8.5px;color:#475569;margin-top:3px;">
            @if($society['phone']){{ $society['phone'] }}@endif
            @if($society['phone2']) &nbsp;|&nbsp; {{ $society['phone2'] }}@endif
            @if($society['address']) &nbsp;|&nbsp; {{ $society['address'] }}@endif
        </div>
    </td>
    <td style="vertical-align:middle;text-align:right;">
        <div style="font-size:16px;font-weight:bold;color:#0f172a;">ANNUAL CASH REPORT</div>
        <div style="font-size:20px;font-weight:bold;color:#1e3a8a;margin-top:2px;">{{ $year }}</div>
        <div style="font-size:8px;color:#94a3b8;margin-top:2px;">Generated: {{ now()->format('d M Y  h:i A') }}</div>
    </td>
</tr>
</table>

{{-- ══ ANNUAL EXECUTIVE SUMMARY ══ --}}
<table style="margin-bottom:4px;">
<tr><td style="font-size:9px;font-weight:bold;text-transform:uppercase;letter-spacing:0.6px;color:#64748b;border-left:3px solid #0f172a;padding-left:7px;">Annual Executive Summary — {{ $year }}</td></tr>
</table>

<table class="sum-tbl" style="margin-bottom:4px;">
<tr>
    <td class="lbl" style="width:20%;background:#f0fdf4;">&#8679; Plot Collections</td>
    <td class="val" style="width:13%;background:#f0fdf4;color:#15803d;">PKR {{ number_format($yearTotals['plot_income']) }}</td>
    <td class="lbl" style="width:20%;background:#fef2f2;">&#8681; Office Expenses</td>
    <td class="val" style="width:13%;background:#fef2f2;color:#dc2626;">PKR {{ number_format($yearTotals['expenses']) }}</td>
    <td rowspan="4" style="width:34%;background:#0f172a;vertical-align:middle;text-align:center;padding:14px;">
        <div style="font-size:8px;font-weight:bold;color:rgba(255,255,255,.6);text-transform:uppercase;margin-bottom:6px;">Annual Net Balance</div>
        <div style="font-size:22px;font-weight:bold;color:{{ $yearTotals['net'] >= 0 ? '#4ade80' : '#f87171' }};">
            {{ $yearTotals['net'] >= 0 ? '+' : '' }}PKR {{ number_format($yearTotals['net']) }}
        </div>
        <div style="font-size:8px;color:rgba(255,255,255,.5);margin-top:4px;">{{ $yearTotals['net'] >= 0 ? 'Surplus' : 'Deficit' }} for {{ $year }}</div>
        <div style="margin-top:10px;font-size:8px;color:rgba(255,255,255,.5);line-height:1.6;">
            Total In: PKR {{ number_format($yearTotals['total_income']) }}<br>
            Total Out: PKR {{ number_format($yearTotals['expenses'] + $yearTotals['inventory']) }}<br>
            Transfers: {{ $yearTotals['transfer_count'] }} deed(s)
        </div>
    </td>
</tr>
<tr>
    <td class="lbl" style="background:#fffbeb;">&#8679; Fee Collections</td>
    <td class="val" style="background:#fffbeb;color:#ca8a04;">PKR {{ number_format($yearTotals['fee_income']) }}</td>
    <td class="lbl" style="background:#faf5ff;">&#8681; Inventory / Supplies</td>
    <td class="val" style="background:#faf5ff;color:#7c3aed;">PKR {{ number_format($yearTotals['inventory']) }}</td>
</tr>
<tr>
    <td class="lbl" style="background:#eff6ff;">&#8679; Office Income</td>
    <td class="val" style="background:#eff6ff;color:#1d4ed8;">PKR {{ number_format($yearTotals['office_income']) }}</td>
    <td class="lbl" style="background:#fef2f2;font-weight:bold;">= Total Cash OUT</td>
    <td class="val" style="background:#fef2f2;color:#dc2626;font-size:13px;">PKR {{ number_format($yearTotals['expenses'] + $yearTotals['inventory']) }}</td>
</tr>
<tr>
    <td class="lbl" style="background:#fffbeb;">&#8679; Misc. Income</td>
    <td class="val" style="background:#fffbeb;color:#d97706;">PKR {{ number_format($yearTotals['misc_income']) }}</td>
    <td class="lbl" style="background:#ecfeff;">&#8644; Total Transfers</td>
    <td class="val" style="background:#ecfeff;color:#0891b2;">{{ $yearTotals['transfer_count'] }} deed(s)</td>
</tr>
<tr>
    <td class="lbl" style="background:#f0fdf4;font-weight:bold;">= Total Cash IN</td>
    <td class="val" style="background:#f0fdf4;color:#15803d;font-size:13px;" colspan="3">PKR {{ number_format($yearTotals['total_income']) }}</td>
</tr>
</table>

<div class="spacer"></div>

{{-- ══ MONTH-BY-MONTH BREAKDOWN ══ --}}
<table style="margin-bottom:4px;">
<tr><td style="font-size:9px;font-weight:bold;text-transform:uppercase;letter-spacing:0.6px;color:#64748b;border-left:3px solid #1e3a8a;padding-left:7px;">Month-by-Month Breakdown</td></tr>
</table>

@php $maxVal = collect($months)->max('total_income') ?: 1; @endphp

<table class="mon-tbl" style="margin-bottom:10px;">
<thead>
<tr>
    <th style="width:7%;">Month</th>
    <th style="width:10%;">Plot Income</th>
    <th style="width:9%;">Fee Income</th>
    <th style="width:9%;">Office Inc.</th>
    <th style="width:7%;">Misc Inc.</th>
    <th style="width:11%;background:#166534;">Total IN</th>
    <th style="width:9%;">Expenses</th>
    <th style="width:9%;">Inventory</th>
    <th style="width:11%;background:#991b1b;">Total OUT</th>
    <th style="width:6%;">Transfers</th>
    <th style="width:12%;text-align:right;">Net Balance</th>
</tr>
</thead>
<tbody>
@foreach($months as $m => $data)
@php
    $isCurrent = ($m == now()->month && (int)$year == now()->year);
    $totalOut  = $data['expenses'] + $data['inventory'];
    $hasData   = $data['total_income'] > 0 || $totalOut > 0;
    $net       = $data['net'];
@endphp
<tr class="{{ $isCurrent ? 'current' : ($loop->even ? 'alt' : '') }}" style="{{ !$hasData ? 'opacity:.4;' : '' }}">
    <td style="font-weight:bold;color:#0f172a;">
        {{ $data['name'] }}
        @if($isCurrent)<br><span style="font-size:7px;background:#fef9c3;color:#92400e;padding:1px 5px;border-radius:20px;">Current</span>@endif
    </td>
    <td class="pos">{{ $data['plot_income'] > 0 ? number_format($data['plot_income']) : '—' }}</td>
    <td style="color:#ca8a04;font-weight:{{ $data['fee_income'] > 0 ? 'bold' : 'normal' }};">{{ $data['fee_income'] > 0 ? number_format($data['fee_income']) : '—' }}</td>
    <td style="color:#1d4ed8;font-weight:{{ $data['office_income'] > 0 ? 'bold' : 'normal' }};">{{ $data['office_income'] > 0 ? number_format($data['office_income']) : '—' }}</td>
    <td style="color:#d97706;font-weight:{{ $data['misc_income'] > 0 ? 'bold' : 'normal' }};">{{ $data['misc_income'] > 0 ? number_format($data['misc_income']) : '—' }}</td>
    <td style="background:#f0fdf4;font-weight:bold;color:#15803d;">
        {{ $data['total_income'] > 0 ? 'PKR '.number_format($data['total_income']) : '—' }}
        @if($data['total_income'] > 0)
        <div class="bar-bg" style="margin-top:3px;">
            <div class="bar-fill" style="width:{{ round($data['total_income'] / $maxVal * 100) }}%;background:#16a34a;"></div>
        </div>
        @endif
    </td>
    <td class="neg">{{ $data['expenses'] > 0 ? number_format($data['expenses']) : '—' }}</td>
    <td style="color:#7c3aed;font-weight:{{ $data['inventory'] > 0 ? 'bold' : 'normal' }};">{{ $data['inventory'] > 0 ? number_format($data['inventory']) : '—' }}</td>
    <td style="background:#fef2f2;font-weight:bold;color:#dc2626;">
        {{ $totalOut > 0 ? 'PKR '.number_format($totalOut) : '—' }}
        @if($totalOut > 0)
        <div class="bar-bg" style="margin-top:3px;">
            <div class="bar-fill" style="width:{{ round($totalOut / $maxVal * 100) }}%;background:#dc2626;"></div>
        </div>
        @endif
    </td>
    <td class="center" style="color:#0891b2;font-weight:bold;">{{ $data['transfer_count'] > 0 ? $data['transfer_count'] : '—' }}</td>
    <td style="text-align:right;">
        @if($net > 0)
            <span style="background:#dcfce7;color:#15803d;font-weight:bold;padding:3px 7px;border-radius:6px;font-size:9px;">+{{ number_format($net) }}</span>
        @elseif($net < 0)
            <span style="background:#ffe4e6;color:#dc2626;font-weight:bold;padding:3px 7px;border-radius:6px;font-size:9px;">{{ number_format($net) }}</span>
        @else
            <span style="color:#94a3b8;font-size:9px;">0</span>
        @endif
    </td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr>
    <td style="color:#fff;">TOTAL</td>
    <td class="in">{{ number_format($yearTotals['plot_income']) }}</td>
    <td style="color:#fde68a;">{{ number_format($yearTotals['fee_income']) }}</td>
    <td style="color:#bfdbfe;">{{ number_format($yearTotals['office_income']) }}</td>
    <td style="color:#fde68a;">{{ number_format($yearTotals['misc_income']) }}</td>
    <td class="in" style="font-size:11px;">PKR {{ number_format($yearTotals['total_income']) }}</td>
    <td class="out">{{ number_format($yearTotals['expenses']) }}</td>
    <td style="color:#d8b4fe;">{{ number_format($yearTotals['inventory']) }}</td>
    <td class="out" style="font-size:11px;">PKR {{ number_format($yearTotals['expenses'] + $yearTotals['inventory']) }}</td>
    <td style="color:#67e8f9;">{{ $yearTotals['transfer_count'] }}</td>
    <td class="{{ $yearTotals['net'] >= 0 ? 'net-pos' : 'net-neg' }}" style="text-align:right;">
        {{ $yearTotals['net'] >= 0 ? '+' : '' }}PKR {{ number_format($yearTotals['net']) }}
    </td>
</tr>
</tfoot>
</table>

{{-- ══ INCOME BREAKDOWN BAR CHART (text-based) ══ --}}
<table style="margin-bottom:4px;">
<tr><td style="font-size:9px;font-weight:bold;text-transform:uppercase;letter-spacing:0.6px;color:#64748b;border-left:3px solid #16a34a;padding-left:7px;">Income Sources Breakdown — {{ $year }}</td></tr>
</table>
<table style="margin-bottom:10px;border-collapse:collapse;">
<tr>
    @php
        $incomeSources = [
            ['label'=>'Plot Collections', 'amount'=>$yearTotals['plot_income'],   'color'=>'#15803d','bg'=>'#f0fdf4'],
            ['label'=>'Fee Collections',  'amount'=>$yearTotals['fee_income'],    'color'=>'#ca8a04','bg'=>'#fffbeb'],
            ['label'=>'Office Income',    'amount'=>$yearTotals['office_income'], 'color'=>'#1d4ed8','bg'=>'#eff6ff'],
            ['label'=>'Misc. Income',     'amount'=>$yearTotals['misc_income'],   'color'=>'#d97706','bg'=>'#fefce8'],
        ];
    @endphp
    @foreach($incomeSources as $src)
    <td style="width:25%;padding:0 5px 0 0;vertical-align:top;">
        <div style="background:{{ $src['bg'] }};border:1px solid {{ $src['bg'] }};border-radius:8px;padding:10px 12px;">
            <div style="font-size:8px;color:#64748b;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">{{ $src['label'] }}</div>
            <div style="font-size:13px;font-weight:bold;color:{{ $src['color'] }};">PKR {{ number_format($src['amount']) }}</div>
            @if($yearTotals['total_income'] > 0)
            @php $pct = round($src['amount'] / $yearTotals['total_income'] * 100, 1); @endphp
            <div style="font-size:8px;color:#94a3b8;margin-top:3px;">{{ $pct }}% of total income</div>
            <div class="bar-bg" style="margin-top:4px;">
                <div class="bar-fill" style="width:{{ $pct }}%;background:{{ $src['color'] }};"></div>
            </div>
            @endif
        </div>
    </td>
    @endforeach
</tr>
</table>

{{-- ══ EXPENSE BREAKDOWN ══ --}}
<table style="margin-bottom:4px;">
<tr><td style="font-size:9px;font-weight:bold;text-transform:uppercase;letter-spacing:0.6px;color:#64748b;border-left:3px solid #dc2626;padding-left:7px;">Expense Breakdown — {{ $year }}</td></tr>
</table>
<table style="margin-bottom:12px;border-collapse:collapse;">
<tr>
    @php
        $outSources = [
            ['label'=>'Office Expenses',       'amount'=>$yearTotals['expenses'],  'color'=>'#dc2626','bg'=>'#fef2f2'],
            ['label'=>'Inventory & Supplies',  'amount'=>$yearTotals['inventory'], 'color'=>'#7c3aed','bg'=>'#faf5ff'],
        ];
        $yearTotalOut = $yearTotals['expenses'] + $yearTotals['inventory'];
    @endphp
    @foreach($outSources as $src)
    <td style="width:25%;padding:0 5px 0 0;vertical-align:top;">
        <div style="background:{{ $src['bg'] }};border:1px solid {{ $src['bg'] }};border-radius:8px;padding:10px 12px;">
            <div style="font-size:8px;color:#64748b;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">{{ $src['label'] }}</div>
            <div style="font-size:13px;font-weight:bold;color:{{ $src['color'] }};">PKR {{ number_format($src['amount']) }}</div>
            @if($yearTotalOut > 0)
            @php $pct = round($src['amount'] / $yearTotalOut * 100, 1); @endphp
            <div style="font-size:8px;color:#94a3b8;margin-top:3px;">{{ $pct }}% of total expenses</div>
            <div class="bar-bg" style="margin-top:4px;">
                <div class="bar-fill" style="width:{{ $pct }}%;background:{{ $src['color'] }};"></div>
            </div>
            @endif
        </div>
    </td>
    @endforeach
    <td style="width:25%;padding:0 5px 0 0;vertical-align:top;">
        <div style="background:#ecfeff;border:1px solid #ecfeff;border-radius:8px;padding:10px 12px;">
            <div style="font-size:8px;color:#64748b;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Total Transfers</div>
            <div style="font-size:13px;font-weight:bold;color:#0891b2;">{{ $yearTotals['transfer_count'] }} deed(s)</div>
            <div style="font-size:8px;color:#94a3b8;margin-top:3px;">Ownership changes in {{ $year }}</div>
        </div>
    </td>
    <td style="width:25%;padding:0;vertical-align:top;">
        <div style="background:{{ $yearTotals['net'] >= 0 ? '#f0fdf4' : '#fef2f2' }};border:1.5px solid {{ $yearTotals['net'] >= 0 ? '#86efac' : '#fecaca' }};border-radius:8px;padding:10px 12px;">
            <div style="font-size:8px;color:#64748b;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Annual Net</div>
            <div style="font-size:13px;font-weight:bold;color:{{ $yearTotals['net'] >= 0 ? '#15803d' : '#dc2626' }};">
                {{ $yearTotals['net'] >= 0 ? '+' : '' }}PKR {{ number_format($yearTotals['net']) }}
            </div>
            <div style="font-size:8px;color:#94a3b8;margin-top:3px;">{{ $yearTotals['net'] >= 0 ? 'Surplus' : 'Deficit' }}</div>
        </div>
    </td>
</tr>
</table>

{{-- SIGNATURES --}}
<table style="width:100%;margin-top:14px;">
<tr>
    <td style="width:33%;text-align:center;padding-top:28px;border-top:1px solid #475569;font-size:9px;color:#475569;">Prepared By</td>
    <td style="width:5%;"></td>
    <td style="width:33%;text-align:center;padding-top:28px;border-top:1px solid #475569;font-size:9px;color:#475569;">Accounts / Finance</td>
    <td style="width:5%;"></td>
    <td style="width:24%;text-align:center;padding-top:28px;border-top:1px solid #475569;font-size:9px;color:#475569;">CEO / Owner</td>
</tr>
</table>

{{-- FOOTER --}}
<table style="width:100%;margin-top:8px;border-top:1px solid #cbd5e1;padding-top:5px;">
<tr>
    <td style="font-size:8px;color:#94a3b8;">{{ $society['name'] }} &nbsp;|&nbsp; Annual Report &nbsp;|&nbsp; {{ $year }}</td>
    <td style="font-size:8px;color:#94a3b8;text-align:right;">{{ $society['receipt_footer'] }}</td>
</tr>
</table>

</body>
</html>
