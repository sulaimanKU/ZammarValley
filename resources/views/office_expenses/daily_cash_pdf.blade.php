<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; }
td, th { padding: 0; }

/* ── section title bar ── */
.sec-bar td { padding: 6px 10px; font-size: 11px; font-weight: bold; color: #fff; }

/* ── data tables ── */
.tbl th {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    padding: 5px 7px;
    font-size: 8.5px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #334155;
    text-align: left;
}
.tbl td {
    border: 1px solid #e2e8f0;
    padding: 5px 7px;
    font-size: 9px;
    vertical-align: middle;
}
.tbl tr.alt { background: #f8fafc; }
.tbl tfoot td {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    padding: 5px 7px;
    font-size: 9px;
    font-weight: bold;
}
.right { text-align: right; }
.center { text-align: center; }
.muted { color: #64748b; }
.empty { text-align: center; color: #94a3b8; font-style: italic; padding: 10px; }

/* ── summary table ── */
.sum-tbl td { padding: 7px 10px; border: 1px solid #e2e8f0; font-size: 10px; }
.sum-tbl .lbl { color: #475569; font-weight: bold; }
.sum-tbl .val { text-align: right; font-weight: bold; font-size: 12px; }

.spacer { height: 12px; }
</style>
</head>
<body>

@php
    $feeTypeMeta = [
        'registry'    => ['label' => 'Registry Fee',    'color' => '#1d4ed8'],
        'development' => ['label' => 'Development Fee', 'color' => '#15803d'],
        'security'    => ['label' => 'Security Fee',    'color' => '#7c3aed'],
        'transfer'    => ['label' => 'Transfer Fee',    'color' => '#ca8a04'],
    ];
@endphp

{{-- ══ LETTERHEAD ══ --}}
<table style="margin-bottom:10px; border-bottom: 2px solid #0f172a; padding-bottom:8px;">
<tr>
    @if(!empty($society['logo']) && $society['show_logo'])
    <td style="width:65px; vertical-align:middle;">
        <img src="{{ $society['logo'] }}" style="width:55px; height:55px; object-fit:contain;">
    </td>
    @endif
    <td style="vertical-align:middle; padding-left:10px;">
        <div style="font-size:18px; font-weight:bold; color:#0f172a;">{{ $society['name'] }}</div>
        <div style="font-size:9px; color:#64748b; margin-top:2px;">{{ $society['tagline'] }}</div>
        <div style="font-size:8.5px; color:#475569; margin-top:3px;">
            @if($society['phone']){{ $society['phone'] }}@endif
            @if($society['phone2']) &nbsp;|&nbsp; {{ $society['phone2'] }}@endif
            @if($society['address']) &nbsp;|&nbsp; {{ $society['address'] }}@endif
        </div>
    </td>
    <td style="vertical-align:middle; text-align:right;">
        <div style="font-size:15px; font-weight:bold; color:#0f172a;">DAILY CASH REPORT</div>
        <div style="font-size:10px; color:#334155; margin-top:3px;">{{ $isSingleDay ? $startDateObj->format('l, d F Y') : $startDateObj->format('d M Y') . ' to ' . $endDateObj->format('d M Y') }}</div>
        <div style="font-size:8px; color:#94a3b8; margin-top:2px;">Generated: {{ now()->format('d M Y  h:i A') }}</div>
    </td>
</tr>
</table>

{{-- ══ EXECUTIVE SUMMARY ══ --}}
<table style="margin-bottom:4px;">
<tr>
    <td style="font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:0.6px; color:#64748b; border-left:3px solid #0f172a; padding-left:7px;">
        Executive Summary
    </td>
</tr>
</table>

<table class="sum-tbl" style="margin-bottom:2px;">
<tr>
    <td class="lbl" style="width:25%; background:#f0fdf4;">&#8679; Plot Collections</td>
    <td class="val" style="width:15%; background:#f0fdf4; color:#15803d;">PKR {{ number_format($totalPlotIncome) }}</td>
    <td class="lbl" style="width:25%; background:#fef2f2;">&#8681; Office Expenses</td>
    <td class="val" style="width:15%; background:#fef2f2; color:#dc2626;">PKR {{ number_format($totalExpenses) }}</td>
    <td rowspan="4" style="width:20%; background:#0f172a; vertical-align:middle; text-align:center; padding:10px;">
        <div style="font-size:8px; font-weight:bold; color:rgba(255,255,255,.6); text-transform:uppercase; margin-bottom:4px;">
            Net Balance
        </div>
        <div style="font-size:16px; font-weight:bold; color:{{ $netBalance >= 0 ? '#4ade80' : '#f87171' }};">
            {{ $netBalance >= 0 ? '+' : '' }}PKR {{ number_format($netBalance) }}
        </div>
        <div style="font-size:8px; color:rgba(255,255,255,.5); margin-top:3px;">
            {{ $netBalance >= 0 ? 'Surplus' : 'Deficit' }}
        </div>
    </td>
</tr>
<tr>
    <td class="lbl" style="background:#eff6ff;">&#8679; Registry Collections</td>
    <td class="val" style="background:#eff6ff; color:#1d4ed8;">PKR {{ number_format($totalRegistryIncome) }}</td>
    <td class="lbl" style="background:#faf5ff;">&#8681; Inventory / Supplies</td>
    <td class="val" style="background:#faf5ff; color:#7c3aed;">PKR {{ number_format($totalInventory) }}</td>
</tr>
<tr>
    <td class="lbl" style="background:#fffbeb;">&#8679; Fee Collections</td>
    <td class="val" style="background:#fffbeb; color:#ca8a04;">PKR {{ number_format($totalOtherFeeIncome + $totalMiscPayments) }}</td>
    <td class="lbl" style="background:#fef2f2; font-weight:bold;">= Total Cash OUT</td>
    <td class="val" style="background:#fef2f2; color:#dc2626; font-size:13px;">PKR {{ number_format($totalExpenses + $totalInventory) }}</td>
</tr>
<tr>
    <td class="lbl" style="background:#f0fdf4; font-weight:bold;">= Total Cash IN</td>
    <td class="val" style="background:#f0fdf4; color:#15803d; font-size:13px;">PKR {{ number_format($totalIncome) }}</td>
    <td class="lbl" style="background:#ecfeff;">&#8644; Transfers Period</td>
    <td class="val" style="background:#ecfeff; color:#0891b2;">{{ $transfers->count() }} deed(s)</td>
</tr>
</table>

<div class="spacer"></div>

{{-- ════════════════════════════════════════════════ --}}
{{--  A. CASH IN                                     --}}
{{-- ════════════════════════════════════════════════ --}}
<table style="margin-bottom:1px;">
<tr class="sec-bar" style="background:#1e3a8a;">
    <td>&#8679; &nbsp; CASH IN &mdash; Total: PKR {{ number_format($totalIncome) }}</td>
</tr>
</table>

{{-- A1: Plot Payments --}}
<table style="margin-bottom:1px;">
<tr style="background:#166534;">
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; width:70%;">
        A1. Plot Payment Collections
    </td>
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; text-align:right;">
        PKR {{ number_format($totalPlotIncome) }} &nbsp;({{ $plotPayments->count() }} record/s)
    </td>
</tr>
</table>
<table class="tbl" style="margin-bottom:8px;">
<thead>
<tr>
    <th style="width:4%;">#</th>
    <th style="width:12%;">Booking Ref</th>
    <th style="width:20%;">Customer Name</th>
    <th style="width:8%;">Plot No.</th>
    <th style="width:12%;">Block</th>
    <th style="width:16%;">Payment Category</th>
    <th style="width:10%;">Method</th>
    <th style="width:18%; text-align:right;">Amount (PKR)</th>
</tr>
</thead>
<tbody>
@forelse($plotPayments as $p)
<tr class="{{ $loop->even ? 'alt' : '' }}">
    <td class="muted center">{{ $loop->iteration }}</td>
    <td style="font-family:monospace; font-size:8.5px;">{{ $p->booking->customer_booking_id ?? '—' }}</td>
    <td style="font-weight:bold;">{{ $p->booking->customer->name ?? '—' }}</td>
    <td>{{ $p->booking->plot->plot_number ?? '—' }}</td>
    <td>{{ $p->booking->plot->block ?? '—' }}</td>
    <td>{{ ucwords(str_replace('_',' ',$p->payment_category)) }}</td>
    <td>{{ $p->payment_type }}</td>
    <td class="right" style="color:#15803d; font-weight:bold;">{{ number_format($p->amount_paid) }}</td>
</tr>
@empty
<tr><td colspan="8" class="empty">No plot payments in this period</td></tr>
@endforelse
</tbody>
@if($plotPayments->count() > 0)
<tfoot>
<tr>
    <td colspan="7" class="right">Sub-Total — Plot Collections</td>
    <td class="right" style="color:#15803d;">{{ number_format($totalPlotIncome) }}</td>
</tr>
</tfoot>
@endif
</table>

{{-- A2: Registry Collections --}}
<table style="margin-bottom:1px;">
<tr style="background:#1e40af;">
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; width:70%;">
        A2. Registry Fee Collections
    </td>
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; text-align:right;">
        PKR {{ number_format($totalRegistryIncome) }} &nbsp;({{ $registryPayments->count() }} record/s)
    </td>
</tr>
</table>
<table class="tbl" style="margin-bottom:4px;">
<thead>
<tr>
    <th style="width:4%;">#</th>
    <th style="width:16%;">Booking Ref</th>
    <th style="width:25%;">Customer Name</th>
    <th style="width:12%;">Plot No.</th>
    <th style="width:15%;">Receipt No.</th>
    <th style="width:13%;">Mode</th>
    <th style="width:15%; text-align:right;">Amount (PKR)</th>
</tr>
</thead>
<tbody>
@forelse($registryPayments as $rp)
@php $latestPayment = $rp->payments?->last(); @endphp
<tr class="{{ $loop->even ? 'alt' : '' }}">
    <td class="muted center">{{ $loop->iteration }}</td>
    <td style="font-family:monospace; font-size:8.5px;">{{ $rp->booking->customer_booking_id ?? '—' }}</td>
    <td style="font-weight:bold;">{{ $rp->booking->customer->name ?? '—' }}</td>
    <td>{{ $rp->booking->plot->plot_number ?? '—' }}</td>
    <td style="font-family:monospace; font-size:8.5px;">{{ $latestPayment->receipt_no ?? '—' }}</td>
    <td>{{ $latestPayment->payment_mode ?? 'Cash' }}</td>
    <td class="right" style="color:#1d4ed8; font-weight:bold;">{{ number_format($rp->paid_amount) }}</td>
</tr>
@empty
<tr><td colspan="7" class="empty">No registry fees in this period</td></tr>
@endforelse
</tbody>
@if($registryPayments->count() > 0)
<tfoot>
<tr>
    <td colspan="6" class="right">Sub-Total — Registry Collections</td>
    <td class="right" style="color:#1d4ed8;">{{ number_format($totalRegistryIncome) }}</td>
</tr>
</tfoot>
@endif
</table>

{{-- A3: Fee & Misc Collections --}}
<table style="margin-bottom:1px;">
<tr style="background:#92400e;">
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; width:70%;">
        A3. Fee &amp; Miscellaneous Collections &mdash; Dev / Security / Transfer / Misc
    </td>
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; text-align:right;">
        PKR {{ number_format($totalOtherFeeIncome + $totalMiscPayments) }} &nbsp;({{ $feePayments->count() + $miscPayments->count() + $directTransferFees->count() }} record/s)
    </td>
</tr>
</table>
<table class="tbl" style="margin-bottom:4px;">
<thead>
<tr>
    <th style="width:4%;">#</th>
    <th style="width:14%;">Type</th>
    <th style="width:11%;">Booking Ref</th>
    <th style="width:18%;">Customer Name</th>
    <th style="width:8%;">Plot No.</th>
    <th style="width:10%;">Mode</th>
    <th style="width:17%;">Notes</th>
    <th style="width:18%; text-align:right;">Amount (PKR)</th>
</tr>
</thead>
<tbody>
@foreach($feePayments as $bf)
@php
    $ft     = $bf->fee_type ?? 'other';
    $ftMeta = $feeTypeMeta[$ft] ?? ['label' => ucfirst($ft), 'color' => '#475569'];
    $latestPayment = $bf->payments?->last();
@endphp
<tr class="{{ $loop->even ? 'alt' : '' }}">
    <td class="muted center">{{ $loop->iteration }}</td>
    <td style="font-weight:bold; color:{{ $ftMeta['color'] }};">{{ $ftMeta['label'] }}</td>
    <td style="font-family:monospace; font-size:8.5px;">{{ $bf->booking->customer_booking_id ?? '—' }}</td>
    <td style="font-weight:bold;">{{ $bf->booking->customer->name ?? '—' }}</td>
    <td>{{ $bf->booking->plot->plot_number ?? '—' }}</td>
    <td>{{ $latestPayment->payment_mode ?? 'Cash' }}</td>
    <td class="muted">{{ $latestPayment->notes ?? 'Milestone Updated' }}</td>
    <td class="right" style="color:#ca8a04; font-weight:bold;">{{ number_format($bf->paid_amount) }}</td>
</tr>
@endforeach

@foreach($miscPayments as $mp)
<tr class="{{ ($loop->iteration + $feePayments->count()) % 2 === 0 ? 'alt' : '' }}">
    <td class="muted center">{{ $loop->iteration + $feePayments->count() }}</td>
    <td style="font-weight:bold; color:#d97706;">Misc Payment</td>
    <td style="font-family:monospace; font-size:8.5px;">{{ $mp->booking->customer_booking_id ?? '—' }}</td>
    <td style="font-weight:bold;">{{ $mp->booking->customer->name ?? '—' }}</td>
    <td>{{ $mp->booking->plot->plot_number ?? '—' }}</td>
    <td>{{ $mp->payment_type }}</td>
    <td class="muted">{{ ucwords(str_replace('_',' ',$mp->payment_category)) }}</td>
    <td class="right" style="color:#ca8a04; font-weight:bold;">{{ number_format($mp->amount_paid) }}</td>
</tr>
@endforeach

@foreach($directTransferFees as $dtr)
<tr class="{{ ($loop->iteration + $feePayments->count() + $miscPayments->count()) % 2 === 0 ? 'alt' : '' }}">
    <td class="muted center">{{ $loop->iteration + $feePayments->count() + $miscPayments->count() }}</td>
    <td style="font-weight:bold; color:#ca8a04;">Transfer Fee</td>
    <td style="font-family:monospace; font-size:8.5px;">{{ $dtr->deed_no }}</td>
    <td style="font-weight:bold;">{{ $dtr->fromCustomer->name ?? '—' }} → {{ $dtr->toCustomer->name ?? '—' }}</td>
    <td>{{ $dtr->plot->plot_number ?? '—' }}</td>
    <td>{{ $dtr->payment_method ?? '—' }}</td>
    <td class="muted">Direct transfer payment</td>
    <td class="right" style="color:#ca8a04; font-weight:bold;">{{ number_format($dtr->transfer_fee) }}</td>
</tr>
@endforeach

@if($feePayments->isEmpty() && $miscPayments->isEmpty() && $directTransferFees->isEmpty())
<tr><td colspan="8" class="empty">No fee or miscellaneous payments in this period</td></tr>
@endif
</tbody>

@if($feePayments->count() > 0 || $miscPayments->count() > 0 || $directTransferFees->count() > 0)
<tfoot>
    @foreach($feeTypeMeta as $ft => $meta)
        @if($ft !== 'registry')
            @php
                $ftTotal = $feePayments->where('fee_type', $ft)->sum('paid_amount');
                if ($ft === 'transfer') $ftTotal += $totalDirectTransferFees;
            @endphp
            @if($ftTotal > 0)
            <tr>
                <td colspan="7" class="right" style="color:{{ $meta['color'] }};">{{ $meta['label'] }} Sub-Total</td>
                <td class="right" style="color:{{ $meta['color'] }};">{{ number_format($ftTotal) }}</td>
            </tr>
            @endif
        @endif
    @endforeach
    @if($totalMiscPayments > 0)
    <tr>
        <td colspan="7" class="right" style="color:#d97706;">Misc Payments Sub-Total</td>
        <td class="right" style="color:#d97706;">{{ number_format($totalMiscPayments) }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="7" class="right">Sub-Total — Fees & Misc</td>
        <td class="right" style="color:#ca8a04;">{{ number_format($totalOtherFeeIncome + $totalMiscPayments) }}</td>
    </tr>
</tfoot>
@endif
</table>

{{-- A4: Plot Transfers --}}
<table style="margin-bottom:1px;">
<tr style="background:#0e7490;">
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; width:70%;">
        A4. Plot Transfers &mdash; Ownership / Deed Transfers Processed
    </td>
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; text-align:right;">
        {{ $transfers->count() }} deed(s)
    </td>
</tr>
</table>
<table class="tbl" style="margin-bottom:8px;">
<thead>
<tr>
    <th style="width:4%;">#</th>
    <th style="width:12%;">Deed No</th>
    <th style="width:12%;">Type</th>
    <th style="width:18%;">From</th>
    <th style="width:18%;">To</th>
    <th style="width:10%;">Plot</th>
    <th style="width:13%;">Transfer Fee</th>
    <th style="width:8%;">Fee Status</th>
    <th style="width:5%;">Status</th>
</tr>
</thead>
<tbody>
@php
$trTypeLabels = ['ownership'=>'Ownership','swap'=>'Swap','partial'=>'Partial','internal'=>'Internal'];
@endphp
@forelse($transfers as $tr)
<tr class="{{ $loop->even ? 'alt' : '' }}">
    <td class="muted center">{{ $loop->iteration }}</td>
    <td style="font-family:monospace; font-size:8.5px; font-weight:bold; color:#0e7490;">{{ $tr->deed_no }}</td>
    <td>{{ $trTypeLabels[$tr->transfer_type] ?? ucfirst($tr->transfer_type) }}</td>
    <td style="font-weight:bold;">{{ $tr->fromCustomer->name ?? '—' }}</td>
    <td style="font-weight:bold;">{{ $tr->toCustomer->name ?? '—' }}</td>
    <td>{{ $tr->plot->plot_number ?? '—' }}</td>
    <td style="color:#0e7490; font-weight:bold;">{{ $tr->transfer_fee > 0 ? 'PKR '.number_format($tr->transfer_fee) : '—' }}</td>
    <td>{{ ucfirst($tr->transfer_fee_status) }}</td>
    <td>{{ ucfirst($tr->status) }}</td>
</tr>
@empty
<tr><td colspan="9" class="empty">No transfers processed in this period</td></tr>
@endforelse
</tbody>
</table>

{{-- ════════════════════════════════════════════════ --}}
{{--  B. CASH OUT                                    --}}
{{-- ════════════════════════════════════════════════ --}}
<table style="margin-bottom:1px;">
<tr class="sec-bar" style="background:#7f1d1d;">
    <td>&#8681; &nbsp; CASH OUT &mdash; Total: PKR {{ number_format($totalExpenses + $totalInventory) }}</td>
</tr>
</table>

{{-- B1: Office Expenses --}}
<table style="margin-bottom:1px;">
<tr style="background:#991b1b;">
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; width:70%;">
        B1. Office Expenses
    </td>
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; text-align:right;">
        PKR {{ number_format($totalExpenses) }} &nbsp;({{ $expenses->count() }} record/s)
    </td>
</tr>
</table>
<table class="tbl" style="margin-bottom:8px;">
<thead>
<tr>
    <th style="width:4%;">#</th>
    <th style="width:16%;">Category</th>
    <th style="width:18%;">Fund Source</th>
    <th style="width:18%;">Paid To</th>
    <th style="width:14%;">Method</th>
    <th style="width:18%;">Remarks</th>
    <th style="width:12%; text-align:right;">Amount (PKR)</th>
</tr>
</thead>
<tbody>
@forelse($expenses as $exp)
<tr class="{{ $loop->even ? 'alt' : '' }}">
    <td class="muted center">{{ $loop->iteration }}</td>
    <td style="font-weight:bold;">{{ $exp->category }}</td>
    <td class="muted">{{ $exp->fund_source ? ucwords(str_replace('_',' ',$exp->fund_source)) : '—' }}</td>
    <td style="font-weight:bold;">{{ $exp->paid_to }}</td>
    <td>{{ $exp->payment_method }}</td>
    <td class="muted">{{ $exp->remarks ?? '—' }}</td>
    <td class="right" style="color:#dc2626; font-weight:bold;">{{ number_format($exp->amount) }}</td>
</tr>
@empty
<tr><td colspan="7" class="empty">No expenses in this period</td></tr>
@endforelse
</tbody>
@if($expenses->count() > 0)
<tfoot>
<tr>
    <td colspan="6" class="right">Sub-Total — Office Expenses</td>
    <td class="right" style="color:#dc2626;">{{ number_format($totalExpenses) }}</td>
</tr>
</tfoot>
@endif
</table>

{{-- B2: Inventory --}}
<table style="margin-bottom:1px;">
<tr style="background:#5b21b6;">
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; width:70%;">
        B2. Inventory &amp; Supplies
    </td>
    <td style="padding:5px 10px; color:#fff; font-size:10px; font-weight:bold; text-align:right;">
        PKR {{ number_format($totalInventory) }} &nbsp;({{ $inventories->count() }} record/s)
    </td>
</tr>
</table>
<table class="tbl" style="margin-bottom:8px;">
<thead>
<tr>
    <th style="width:4%;">#</th>
    <th style="width:20%;">Category</th>
    <th style="width:24%;">Supplier / Item</th>
    <th style="width:16%;">Method</th>
    <th style="width:22%;">Notes</th>
    <th style="width:14%; text-align:right;">Amount (PKR)</th>
</tr>
</thead>
<tbody>
@forelse($inventories as $inv)
<tr class="{{ $loop->even ? 'alt' : '' }}">
    <td class="muted center">{{ $loop->iteration }}</td>
    <td style="font-weight:bold;">{{ $inv->category }}</td>
    <td>{{ $inv->paid_to }}</td>
    <td>{{ $inv->payment_method }}</td>
    <td class="muted">{{ $inv->remarks ?? '—' }}</td>
    <td class="right" style="color:#7c3aed; font-weight:bold;">{{ number_format($inv->amount) }}</td>
</tr>
@empty
<tr><td colspan="6" class="empty">No inventory records in this period</td></tr>
@endforelse
</tbody>
@if($inventories->count() > 0)
<tfoot>
<tr>
    <td colspan="5" class="right">Sub-Total — Inventory</td>
    <td class="right" style="color:#7c3aed;">{{ number_format($totalInventory) }}</td>
</tr>
</tfoot>
@endif
</table>

{{-- ════════════════════════════════════════════════ --}}
{{--  FINAL BALANCE TABLE                            --}}
{{-- ════════════════════════════════════════════════ --}}
<table style="margin-bottom:1px;">
<tr><td style="font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:0.6px; color:#64748b; border-left:3px solid #0f172a; padding-left:7px;">Final Balance</td></tr>
</table>
<table style="width:55%; border-collapse:collapse; border:1.5px solid #0f172a; margin-bottom:16px;">
<tr>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#f0fdf4; font-weight:bold; font-size:10px; color:#15803d;">&#43; Plot Collections</td>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#f0fdf4; text-align:right; font-weight:bold; color:#15803d;">PKR {{ number_format($totalPlotIncome) }}</td>
</tr>
<tr>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#eff6ff; font-weight:bold; font-size:10px; color:#1d4ed8;">&#43; Registry Collections</td>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#eff6ff; text-align:right; font-weight:bold; color:#1d4ed8;">PKR {{ number_format($totalRegistryIncome) }}</td>
</tr>
<tr>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#fefce8; font-weight:bold; font-size:10px; color:#ca8a04;">&#43; Fee Collections (inc. Misc)</td>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#fefce8; text-align:right; font-weight:bold; color:#ca8a04;">PKR {{ number_format($totalOtherFeeIncome + $totalMiscPayments) }}</td>
</tr>
<tr>
    <td style="padding:7px 10px; border:1.5px solid #15803d; background:#dcfce7; font-weight:bold; font-size:10px;">&nbsp;&nbsp;= Total Cash IN</td>
    <td style="padding:7px 10px; border:1.5px solid #15803d; background:#dcfce7; text-align:right; font-weight:bold; font-size:12px; color:#15803d;">PKR {{ number_format($totalIncome) }}</td>
</tr>
<tr>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#fef2f2; font-weight:bold; font-size:10px; color:#dc2626;">&#8722; Office Expenses</td>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#fef2f2; text-align:right; font-weight:bold; color:#dc2626;">PKR {{ number_format($totalExpenses) }}</td>
</tr>
<tr>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#faf5ff; font-weight:bold; font-size:10px; color:#7c3aed;">&#8722; Inventory &amp; Supplies</td>
    <td style="padding:6px 10px; border:1px solid #e2e8f0; background:#faf5ff; text-align:right; font-weight:bold; color:#7c3aed;">PKR {{ number_format($totalInventory) }}</td>
</tr>
<tr>
    <td style="padding:7px 10px; border:1.5px solid #dc2626; background:#fee2e2; font-weight:bold; font-size:10px;">&nbsp;&nbsp;= Total Cash OUT</td>
    <td style="padding:7px 10px; border:1.5px solid #dc2626; background:#fee2e2; text-align:right; font-weight:bold; font-size:12px; color:#dc2626;">PKR {{ number_format($totalExpenses + $totalInventory) }}</td>
</tr>
<tr style="background:{{ $netBalance >= 0 ? '#0f172a' : '#7f1d1d' }};">
    <td style="padding:9px 10px; border:1px solid #0f172a; color:#fff; font-weight:bold; font-size:11px;">
        {{ $netBalance >= 0 ? '✔ NET BALANCE (SURPLUS)' : '✘ NET BALANCE (DEFICIT)' }}
    </td>
    <td style="padding:9px 10px; border:1px solid #0f172a; text-align:right; font-weight:bold; font-size:14px; color:{{ $netBalance >= 0 ? '#4ade80' : '#f87171' }};">
        {{ $netBalance >= 0 ? '+' : '' }}PKR {{ number_format($netBalance) }}
    </td>
</tr>
</table>

{{-- ════════════════════════════════════════════════ --}}
{{--  SIGNATURES                                     --}}
{{-- ════════════════════════════════════════════════ --}}
<table style="width:100%; margin-top:20px;">
<tr>
    <td style="width:33%; text-align:center; padding-top:30px; border-top:1px solid #475569; font-size:9px; color:#475569;">Prepared By</td>
    <td style="width:5%;"></td>
    <td style="width:33%; text-align:center; padding-top:30px; border-top:1px solid #475569; font-size:9px; color:#475569;">Accounts / Finance</td>
    <td style="width:5%;"></td>
    <td style="width:24%; text-align:center; padding-top:30px; border-top:1px solid #475569; font-size:9px; color:#475569;">CEO / Owner</td>
</tr>
</table>

{{-- FOOTER --}}
<table style="width:100%; margin-top:10px; border-top:1px solid #cbd5e1; padding-top:6px;">
<tr>
    <td style="font-size:8px; color:#94a3b8;">{{ $society['name'] }} &nbsp;|&nbsp; Daily Cash Report &nbsp;|&nbsp; {{ $isSingleDay ? $startDateObj->format('d M Y') : $startDateObj->format('d M Y') . ' - ' . $endDateObj->format('d M Y') }}</td>
    <td style="font-size:8px; color:#94a3b8; text-align:right;">{{ $society['receipt_footer'] }}</td>
</tr>
</table>

</body>
</html>
