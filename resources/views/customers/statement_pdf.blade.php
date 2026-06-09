<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Account Statement — {{ $customer->name }}</title>
<style>
@page { margin: 0.35in 0.4in; }
* { box-sizing: border-box; }
body { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #1e293b; line-height: 1.35; margin: 0; padding: 0; background: #fff; }

/* Header */
.hdr { background: #1e3a8a; padding: 14px 20px; color: #fff; }
.hdr table { width: 100%; border-collapse: collapse; }
.brand { font-size: 20px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; }
.brand-sub { font-size: 8px; color: #93c5fd; margin-top: 2px; }
.doc-label { font-size: 13px; font-weight: 800; color: #fbbf24; text-transform: uppercase; letter-spacing: 1px; }
.doc-sub { font-size: 8px; color: #bfdbfe; margin-top: 3px; }
.doc-date { font-size: 8px; color: #93c5fd; margin-top: 2px; }

/* Customer strip */
.cust-strip { background: #f8fafc; border: 1px solid #e2e8f0; padding: 9px 14px; margin-top: 10px; border-radius: 4px; }
.cust-strip table { width: 100%; border-collapse: collapse; }
.cust-strip td { font-size: 8.5px; padding: 2px 6px; vertical-align: top; }
.cust-label { font-weight: 700; color: #64748b; text-transform: uppercase; font-size: 7.5px; }
.cust-val { color: #1e293b; font-weight: 600; }

/* Summary boxes */
.summary-row { display: table; width: 100%; border-collapse: collapse; margin: 10px 0; }
.summary-box { display: table-cell; width: 33.33%; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 12px; text-align: center; }
.summary-box + .summary-box { margin-left: 6px; }
.s-label { font-size: 7.5px; text-transform: uppercase; font-weight: 700; color: #64748b; }
.s-val { font-size: 14px; font-weight: 900; margin-top: 2px; }
.s-green { color: #15803d; }
.s-blue  { color: #1d4ed8; }
.s-red   { color: #dc2626; }

/* Section title */
.sec-title { font-size: 8.5px; font-weight: 800; color: #1e3a8a; text-transform: uppercase;
    border-bottom: 1.5px solid #1e3a8a; padding-bottom: 3px; margin: 14px 0 8px; display: block; }

/* Booking card */
.bk-card { border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 12px; overflow: hidden; page-break-inside: avoid; }
.bk-card-head { background: #1e3a8a; color: #fff; padding: 6px 10px; }
.bk-card-transferred { background: #6d28d9; }
.bk-card-transfer-in { background: #1d4ed8; }
.bk-card-head table { width: 100%; border-collapse: collapse; }
.bk-ref { font-size: 9.5px; font-weight: 800; }
.bk-status { font-size: 8px; font-weight: 700; padding: 2px 8px; border-radius: 10px; border: 1px solid rgba(255,255,255,.4); }
.bk-status-active { background: #dcfce7; color: #15803d; border-color: #86efac; }
.bk-status-completed { background: #dbeafe; color: #1d4ed8; border-color: #93c5fd; }
.bk-status-transferred { background: #ede9fe; color: #7c3aed; border-color: #c4b5fd; }
.bk-status-pending { background: #fef9c3; color: #854d0e; border-color: #fde047; }
.transfer-badge { font-size: 7.5px; font-weight: 800; padding: 2px 7px; border-radius: 10px; margin-left: 6px; }
.badge-transfer-in  { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
.badge-transfer-out { background: #f3e8ff; color: #7c3aed; border: 1px solid #d8b4fe; }
.excluded-note { font-size: 7px; color: #9ca3af; font-style: italic; }

/* Info grid */
.info-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; }
.info-grid-cell { display: table-cell; padding: 7px 10px; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; vertical-align: top; width: 25%; }
.info-grid-cell:last-child { border-right: none; }
.ig-label { font-size: 7.5px; text-transform: uppercase; font-weight: 700; color: #94a3b8; margin-bottom: 2px; }
.ig-val { font-size: 9px; font-weight: 600; color: #1e293b; }

/* Payment bar */
.pay-bar-wrap { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
.pay-bar-label { font-size: 7.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
.pay-bar { background: #e2e8f0; border-radius: 4px; height: 8px; overflow: hidden; }
.pay-bar-fill { background: #22c55e; height: 8px; border-radius: 4px; }
.pay-bar-stats { display: table; width: 100%; border-collapse: collapse; margin-top: 4px; }
.pay-stat { display: table-cell; text-align: center; font-size: 8px; }
.pay-stat-label { color: #64748b; font-size: 7px; }
.pay-stat-val { font-weight: 700; }
.ps-green { color: #15803d; }
.ps-red { color: #dc2626; }
.ps-blue { color: #1d4ed8; }

/* Fee table */
.fee-table { width: 100%; border-collapse: collapse; }
.fee-table th { background: #f1f5f9; font-size: 7.5px; text-transform: uppercase; color: #64748b; font-weight: 700; padding: 5px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
.fee-table td { padding: 5px 10px; border-bottom: 1px solid #f1f5f9; font-size: 8.5px; }
.fee-table tr:last-child td { border-bottom: none; }
.badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 7.5px; font-weight: 700; }
.badge-paid { background: #dcfce7; color: #15803d; }
.badge-pending { background: #fef9c3; color: #854d0e; }
.badge-na { background: #f1f5f9; color: #94a3b8; }

/* Payment ledger */
.ledger-table { width: 100%; border-collapse: collapse; }
.ledger-table th { background: #1e3a8a; color: #fff; padding: 5px 8px; font-size: 7.5px; text-transform: uppercase; text-align: left; }
.ledger-table td { padding: 4px 8px; border-bottom: 1px solid #f1f5f9; font-size: 8px; }
.ledger-table tr:nth-child(even) td { background: #fcfdfe; }
.ledger-table .amt { font-weight: 700; color: #15803d; text-align: right; }
.ledger-table .cat { color: #64748b; font-size: 7.5px; }

/* Footer */
.page-footer { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; text-align: center; font-size: 7.5px; color: #94a3b8; }
.watermark { position: fixed; top: 35%; left: 10%; font-size: 55px; color: rgba(30,58,138,0.03); transform: rotate(-40deg); z-index: -1000; font-weight: 900; text-transform: uppercase; letter-spacing: 8px; }
</style>
</head>
<body>

<div class="watermark">{{ $sc['watermark_text'] ?? ($sc['name'] ?? 'ZAMAR VALLEY') }}</div>

{{-- ── HEADER ── --}}
<div class="hdr">
    <table>
        <tr>
            <td style="width:60%;vertical-align:middle;">
                @if(!empty($sc['logo_base64']))
                <img src="{{ $sc['logo_base64'] }}" style="height:36px;margin-bottom:4px;display:block;" alt="Logo">
                @endif
                <div class="brand">{{ $sc['name'] ?? 'Zamar Valley' }}</div>
                <div class="brand-sub">{{ $sc['address'] ?? '' }}</div>
            </td>
            <td style="text-align:right;vertical-align:middle;">
                <div class="doc-label">Account Statement</div>
                <div class="doc-sub">Customer Portfolio Summary</div>
                <div class="doc-date">Generated: {{ \Carbon\Carbon::now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── CUSTOMER INFO ── --}}
<div class="cust-strip">
    <table>
        <tr>
            <td style="width:40%;">
                <div class="cust-label">Customer Name</div>
                <div class="cust-val" style="font-size:11px;">{{ $customer->name }}</div>
            </td>
            <td style="width:25%;">
                <div class="cust-label">CNIC</div>
                <div class="cust-val">{{ $customer->cnic ?? '—' }}</div>
            </td>
            <td style="width:20%;">
                <div class="cust-label">Mobile</div>
                <div class="cust-val">{{ $customer->mobile ?? $customer->phone ?? '—' }}</div>
            </td>
            <td style="width:15%;text-align:right;">
                <div class="cust-label">Total Plots</div>
                <div class="cust-val" style="font-size:14px;font-weight:900;color:#1e3a8a;">{{ $bookingData->count() }}</div>
            </td>
        </tr>
        @if($customer->residential_address || $customer->email)
        <tr>
            <td colspan="2" style="padding-top:4px;">
                <div class="cust-label">Address</div>
                <div class="cust-val">{{ $customer->residential_address ?? $customer->address ?? '—' }}</div>
            </td>
            <td colspan="2" style="padding-top:4px;">
                <div class="cust-label">Email</div>
                <div class="cust-val">{{ $customer->email ?? '—' }}</div>
            </td>
        </tr>
        @endif
    </table>
</div>

{{-- ── GRAND SUMMARY BOXES ── --}}
@php
    $grandDiscount = $bookingData->sum('discount');
@endphp
<table style="width:100%;border-collapse:separate;border-spacing:6px 0;margin:10px 0;">
    <tr>
        <td style="width:25%;border:1.5px solid #e2e8f0;border-radius:4px;padding:8px 10px;text-align:center;background:#f8fafc;">
            <div class="s-label">Total Plot Value</div>
            <div class="s-val s-blue">PKR {{ number_format($grandTotal) }}</div>
            <div style="font-size:7px;color:#94a3b8;margin-top:2px;">all bookings combined</div>
        </td>
        <td style="width:25%;border:1.5px solid #dcfce7;border-radius:4px;padding:8px 10px;text-align:center;background:#f0fdf4;">
            <div class="s-label">Total Paid</div>
            <div class="s-val s-green">PKR {{ number_format($grandPaid) }}</div>
            <div style="font-size:7px;color:#94a3b8;margin-top:2px;">cash received</div>
        </td>
        @if($grandDiscount > 0)
        <td style="width:25%;border:1.5px solid #fde68a;border-radius:4px;padding:8px 10px;text-align:center;background:#fffbeb;">
            <div class="s-label" style="color:#92400e;">Total Discounts</div>
            <div class="s-val" style="color:#d97706;">PKR {{ number_format($grandDiscount) }}</div>
            <div style="font-size:7px;color:#b45309;margin-top:2px;">★ savings on all plots</div>
        </td>
        @endif
        <td style="width:{{ $grandDiscount > 0 ? '25%' : '50%' }};border:1.5px solid #fee2e2;border-radius:4px;padding:8px 10px;text-align:center;background:#fef2f2;">
            <div class="s-label">Total Remaining</div>
            <div class="s-val s-red">PKR {{ number_format($grandRemaining) }}</div>
            @if($grandDiscount > 0)
            <div style="font-size:7px;color:#94a3b8;margin-top:2px;">after discounts applied</div>
            @endif
        </td>
    </tr>
</table>
@if($bookingData->contains('is_transferred_out', true))
<div style="background:#f5f3ff;border:1px solid #e9d5ff;border-radius:4px;padding:6px 10px;margin-bottom:8px;font-size:7.5px;color:#7c3aed;">
    <strong>Note:</strong> The figures above reflect <strong>current active plots only</strong>.
    Transferred-out bookings are listed below for historical reference but are not counted in the totals.
</div>
@endif

{{-- ── PER-BOOKING DETAIL ── --}}
<span class="sec-title">Plot-wise Breakdown</span>

@foreach($bookingData as $row)
@php
    $b       = $row['booking'];
    $plot    = $b->plot;
    $st      = $b->status;
    $pct     = $b->total_price > 0 ? min(100, round($row['paid'] / $b->total_price * 100)) : 0;
    $statusClass = match($st) {
        'active'       => 'bk-status-active',
        'completed'    => 'bk-status-completed',
        'transferred','partial_transferred','swapped','plot_relocated' => 'bk-status-transferred',
        default        => 'bk-status-pending',
    };
    $headClass = $row['is_transferred_out'] ? 'bk-card-head bk-card-transferred'
               : ($row['is_transfer_in']    ? 'bk-card-head bk-card-transfer-in'
               :                              'bk-card-head');
    $cardBorder = $row['is_transferred_out'] ? 'border:1.5px solid #d8b4fe;opacity:.88;'
                : ($row['is_transfer_in']    ? 'border:1.5px solid #93c5fd;'
                :                              '');
@endphp
<div class="bk-card" style="{{ $cardBorder }}{{ !$loop->last ? 'page-break-after:always;' : '' }}">

    {{-- Card Header --}}
    <div class="{{ $headClass }}">
        <table>
            <tr>
                <td>
                    <span class="bk-ref">{{ $b->customer_booking_id }}</span>
                    @if($row['is_transferred_out'])
                        <span class="transfer-badge badge-transfer-out">↑ Transferred Out</span>
                        <span class="excluded-note" style="color:#e9d5ff;margin-left:4px;">not counted in totals</span>
                    @elseif($row['is_transfer_in'])
                        <span class="transfer-badge badge-transfer-in">↓ Transfer In</span>
                    @endif
                </td>
                <td style="text-align:right;">
                    <span class="bk-status {{ $statusClass }}">{{ ucfirst(str_replace('_',' ',$st)) }}</span>
                    <span style="font-size:7.5px;color:#bfdbfe;margin-left:8px;">{{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Plot Info Grid --}}
    <div class="info-grid">
        <div class="info-grid-cell">
            <div class="ig-label">Plot Number</div>
            <div class="ig-val">{{ $plot->plot_number ?? '—' }}</div>
        </div>
        <div class="info-grid-cell">
            <div class="ig-label">Block / Street</div>
            <div class="ig-val">{{ $plot->block ?? '—' }} / {{ $plot->street_number ?? '—' }}</div>
        </div>
        <div class="info-grid-cell">
            <div class="ig-label">Size</div>
            <div class="ig-val">{{ $plot->size ?? '—' }} {{ $plot->unit ?? '' }}</div>
        </div>
        <div class="info-grid-cell">
            <div class="ig-label">Category</div>
            <div class="ig-val">{{ $plot->category->name ?? '—' }}</div>
        </div>
    </div>

    {{-- Payment Plan Row --}}
    @php
        $stmtPlotDisc   = (float)($b->plot->discount_amount ?? 0);
        $stmtPlotDiscReason = $b->plot->discount_reason ?? null;
        $stmtDiscSent   = 'Settlement discount — waived amount (not collected).';
        $stmtPayDisc    = $b->payments
            ->filter(fn($p) => ($p->remarks ?? '') !== $stmtDiscSent)
            ->sum('discount_amount')
            + $b->payments
            ->filter(fn($p) => ($p->remarks ?? '') === $stmtDiscSent)
            ->sum('amount_paid');
        $stmtTotalSavings = $stmtPlotDisc + $stmtPayDisc;
    @endphp
    <div class="info-grid" style="border-top:1px solid #e2e8f0;">
        <div class="info-grid-cell">
            @if($row['is_transfer_in'])
                <div class="ig-label">Full Plot Value</div>
                <div class="ig-val" style="color:#1d4ed8;font-weight:800;">PKR {{ number_format($row['full_plot_price']) }}</div>
                <div class="ig-label" style="margin-top:4px;">Your Obligation</div>
                <div class="ig-val" style="color:#7c3aed;font-weight:700;">PKR {{ number_format($b->total_price) }}</div>
                <div style="font-size:7px;color:#9ca3af;margin-top:1px;">(remaining balance at transfer)</div>
            @else
                @if($stmtPlotDisc > 0)
                    <div class="ig-label" style="color:#94a3b8;text-decoration:line-through;">Base Price</div>
                    <div class="ig-val" style="color:#94a3b8;text-decoration:line-through;font-size:8px;">PKR {{ number_format($b->total_price + $stmtPlotDisc) }}</div>
                    <div class="ig-label" style="margin-top:3px;">Agreed Price</div>
                    <div class="ig-val" style="color:#1d4ed8;font-weight:800;">PKR {{ number_format($b->total_price) }}</div>
                @else
                    <div class="ig-label">Total Price</div>
                    <div class="ig-val" style="color:#1d4ed8;font-weight:800;">PKR {{ number_format($b->total_price) }}</div>
                @endif
            @endif
        </div>
        <div class="info-grid-cell">
            <div class="ig-label">Down Payment</div>
            <div class="ig-val">PKR {{ number_format($b->down_payment ?? 0) }}</div>
        </div>
        <div class="info-grid-cell">
            <div class="ig-label">Monthly Installment</div>
            <div class="ig-val">
                @if($b->monthly_installment)
                PKR {{ number_format($b->monthly_installment) }} × {{ $b->total_installments ?? 0 }}
                @else
                —
                @endif
            </div>
        </div>
        <div class="info-grid-cell">
            <div class="ig-label">Quarterly Installment</div>
            <div class="ig-val">
                @if($b->quarterly_amount)
                PKR {{ number_format($b->quarterly_amount) }} × {{ $b->quarterly_installments ?? 0 }}
                @else
                —
                @endif
            </div>
        </div>
    </div>

    {{-- Discount highlight row (only shown when discounts exist) --}}
    @if($stmtTotalSavings > 0)
    <div style="background:#fffbeb;border-top:1px solid #fde68a;padding:5px 10px;display:table;width:100%;border-collapse:collapse;">
        <div style="display:table-row;">
            @if($stmtPlotDisc > 0)
            <div style="display:table-cell;width:50%;padding:3px 6px;vertical-align:top;border-right:1px solid #fde68a;">
                <div style="font-size:7.5px;font-weight:700;color:#92400e;text-transform:uppercase;">
                    ★ Plot Discount{{ $stmtPlotDiscReason ? ' ('.$stmtPlotDiscReason.')' : ' (at booking)' }}
                </div>
                <div style="font-size:9px;font-weight:800;color:#d97706;">PKR {{ number_format($stmtPlotDisc) }} saved</div>
            </div>
            @endif
            @if($stmtPayDisc > 0)
            <div style="display:table-cell;width:{{ $stmtPlotDisc > 0 ? '50%' : '100%' }};padding:3px 6px;vertical-align:top;">
                <div style="font-size:7.5px;font-weight:700;color:#166534;text-transform:uppercase;">
                    ★ Full-Payment Discount (Waived)
                </div>
                <div style="font-size:9px;font-weight:800;color:#16a34a;">PKR {{ number_format($stmtPayDisc) }} saved</div>
            </div>
            @endif
            @if($stmtPlotDisc > 0 && $stmtPayDisc > 0)
            <div style="display:table-cell;width:0;padding:3px 8px;vertical-align:middle;border-left:1px solid #fde68a;text-align:center;">
                <div style="font-size:7px;font-weight:700;color:#92400e;text-transform:uppercase;white-space:nowrap;">Total Savings</div>
                <div style="font-size:9px;font-weight:900;color:#d97706;white-space:nowrap;">PKR {{ number_format($stmtTotalSavings) }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Payment Progress --}}
    <div class="pay-bar-wrap">
        @if($row['is_transferred_out'])
        <div style="background:#f5f3ff;border:1px solid #e9d5ff;border-radius:3px;padding:5px 8px;text-align:center;">
            <span style="font-size:8px;font-weight:700;color:#7c3aed;">Plot Transferred — payments below are for historical reference only</span>
        </div>
        @else
        <div class="pay-bar-label">Payment Progress</div>
        <div class="pay-bar"><div class="pay-bar-fill" style="width:{{ $pct }}%;"></div></div>
        <table class="pay-bar-stats">
            <tr>
                <td class="pay-stat">
                    <div class="pay-stat-label">Paid</div>
                    <div class="pay-stat-val ps-green">PKR {{ number_format($row['paid']) }}</div>
                </td>
                <td class="pay-stat">
                    <div class="pay-stat-label">Remaining</div>
                    <div class="pay-stat-val ps-red">PKR {{ number_format($row['remaining']) }}</div>
                </td>
                <td class="pay-stat">
                    <div class="pay-stat-label">Progress</div>
                    <div class="pay-stat-val ps-blue">{{ $pct }}%</div>
                </td>
                <td class="pay-stat">
                    <div class="pay-stat-label">Payments Made</div>
                    <div class="pay-stat-val">{{ $b->payments->count() }}</div>
                </td>
            </tr>
        </table>
        @endif
    </div>

    {{-- Transfer Info Box --}}
    @if($row['outgoing_transfer'] || $row['incoming_transfer'])
    @php $xfer = $row['outgoing_transfer'] ?? $row['incoming_transfer']; @endphp
    <div style="background:#f5f3ff;border:1px solid #ddd6fe;border-left:3px solid #7c3aed;border-radius:3px;padding:6px 10px;margin:6px 0;display:table;width:100%;border-collapse:collapse;">
        <div style="display:table-row;">
            <div style="display:table-cell;width:25%;padding:2px 8px 2px 0;vertical-align:top;border-right:1px solid #ddd6fe;">
                <div style="font-size:7px;font-weight:800;color:#7c3aed;text-transform:uppercase;margin-bottom:1px;">
                    {{ $row['outgoing_transfer'] ? 'Transferred Out' : 'Transferred In' }}
                </div>
                <div style="font-size:8.5px;font-weight:800;color:#0f172a;font-family:monospace;">{{ $xfer->deed_no }}</div>
                <div style="font-size:7px;color:#64748b;">{{ \Carbon\Carbon::parse($xfer->transfer_date)->format('d M Y') }}</div>
                <div style="font-size:7px;color:#64748b;margin-top:1px;">{{ ucfirst($xfer->transfer_type) }} Transfer</div>
            </div>
            @if($row['outgoing_transfer'])
            <div style="display:table-cell;padding:2px 8px;vertical-align:top;">
                <div style="font-size:7px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:1px;">Transferred To</div>
                <div style="font-size:8.5px;font-weight:800;color:#0f172a;">{{ $xfer->toCustomer->name ?? '—' }}</div>
                @if($xfer->toCustomer?->cnic)<div style="font-size:7px;color:#64748b;font-family:monospace;">{{ $xfer->toCustomer->cnic }}</div>@endif
                @if($xfer->transfer_type === 'partial')<div style="font-size:7px;color:#ea580c;font-weight:700;">{{ $xfer->ownership_percentage }}% share transferred</div>@endif
            </div>
            @else
            <div style="display:table-cell;padding:2px 8px;vertical-align:top;">
                <div style="font-size:7px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:1px;">Transferred From</div>
                <div style="font-size:8.5px;font-weight:800;color:#0f172a;">
                    {{ $xfer->fromCustomer->name ?? $xfer->fromBooking?->customer?->name ?? '—' }}
                </div>
                @php $fromCnic = $xfer->fromCustomer?->cnic ?? $xfer->fromBooking?->customer?->cnic; @endphp
                @if($fromCnic)<div style="font-size:7px;color:#64748b;font-family:monospace;">{{ $fromCnic }}</div>@endif
                @if($xfer->transfer_type === 'partial')<div style="font-size:7px;color:#ea580c;font-weight:700;">{{ $xfer->ownership_percentage }}% share acquired</div>@endif
            </div>
            @endif
            <div style="display:table-cell;width:22%;padding:2px 0 2px 8px;vertical-align:top;border-left:1px solid #ddd6fe;text-align:center;">
                <div style="font-size:7px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:1px;">Balance at Transfer</div>
                <div style="font-size:9px;font-weight:900;color:#7c3aed;">PKR {{ number_format($xfer->remaining_balance_transferred ?? 0) }}</div>
                @if($xfer->transfer_fee > 0)
                <div style="font-size:7px;color:#64748b;margin-top:2px;">Transfer Fee</div>
                <div style="font-size:8px;font-weight:700;color:#0f172a;">PKR {{ number_format($xfer->transfer_fee) }}</div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Fees --}}
    @php
        $feeRows = [
            'registry_fee'    => 'Registry Fee',
            'development_fee' => 'Development Fee',
            'security_fee'    => 'Security Fee',
            'transfer_fee'    => 'Transfer Fee',
        ];
        $anyFee = collect($feeRows)->keys()->filter(fn($k) => !is_null($row[$k]))->isNotEmpty();
    @endphp
    <table class="fee-table">
        <thead>
            <tr>
                <th>Fee Type</th>
                <th>Billed Amount</th>
                <th>Paid Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($feeRows as $key => $label)
            @php $fee = $row[$key]; @endphp
            @if($fee)
            @php $settled = (bool)$fee->is_settled; @endphp
            <tr>
                <td>{{ $label }}</td>
                <td style="font-weight:700;">
                    {{ $fee->amount > 0 ? 'PKR '.number_format($fee->amount) : '—' }}
                </td>
                <td style="font-weight:700;color:#15803d;">
                    {{ $fee->paid_amount > 0 ? 'PKR '.number_format($fee->paid_amount) : '—' }}
                </td>
                <td>
                    @if($settled)
                    <span class="badge badge-paid">Paid</span>
                    @elseif($fee->paid_amount > 0)
                    <span class="badge badge-pending">Partial</span>
                    @else
                    <span class="badge badge-pending">Pending</span>
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
        @if(!$anyFee)
        <tr><td colspan="4" style="color:#94a3b8;font-style:italic;text-align:center;">No extra fees for this booking</td></tr>
        @endif
        </tbody>
    </table>

    {{-- Payment Ledger (mini) --}}
    @if($b->payments->count() || $row['fee_payments']->count())
    <div style="border-top:1px solid #e2e8f0;padding:0;">
        <table class="ledger-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th style="text-align:right;">Amount (PKR)</th>
                </tr>
            </thead>
            <tbody>
            @php $rowNum = 1; @endphp
            @foreach($b->payments as $pay)
            <tr>
                <td>{{ $rowNum++ }}</td>
                <td>{{ \Carbon\Carbon::parse($pay->paid_date)->format('d M Y') }}</td>
                <td class="cat">{{ ucfirst(str_replace('_',' ',$pay->payment_category)) }}</td>
                <td style="color:#475569;">{{ $pay->description ?? '—' }}</td>
                <td class="amt">{{ number_format($pay->amount_paid) }}</td>
            </tr>
            @endforeach
            @foreach($row['fee_payments'] as $fp)
            <tr style="background:#fefce8;">
                <td>{{ $rowNum++ }}</td>
                <td>{{ \Carbon\Carbon::parse($fp['date'])->format('d M Y') }}</td>
                <td class="cat" style="color:#b45309;">
                    {{ ucfirst($fp['fee_type']) }} Fee
                </td>
                <td style="color:#475569;">
                    {{ $fp['receipt'] ?? '—' }}
                    @if($fp['mode'])<span style="font-size:7px;color:#94a3b8;"> · {{ ucfirst(str_replace('_',' ',$fp['mode'])) }}</span>@endif
                </td>
                <td class="amt" style="color:#b45309;">{{ number_format($fp['amount']) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f0fdf4;">
                    <td colspan="4" style="font-weight:800;font-size:9px;color:#15803d;padding:5px 8px;">Total Plot Payments</td>
                    <td style="font-weight:900;color:#15803d;text-align:right;padding:5px 8px;">{{ number_format($row['paid']) }}</td>
                </tr>
                @if($row['fee_payments']->count())
                <tr style="background:#fefce8;">
                    <td colspan="4" style="font-weight:800;font-size:9px;color:#b45309;padding:5px 8px;">Total Fee Payments</td>
                    <td style="font-weight:900;color:#b45309;text-align:right;padding:5px 8px;">{{ number_format($row['fee_payments']->sum('amount')) }}</td>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>
    @endif

</div>
@endforeach

{{-- ── FOOTER ── --}}
<div class="page-footer">
    @php
        $stPhones = array_filter([$sc['phone'] ?? '', $sc['phone2'] ?? '', $sc['phone3'] ?? '']);
    @endphp
    @php
        $stFooterParts = [$sc['name'] ?? 'Zamar Valley'];
        if ($sc['address'] ?? '') $stFooterParts[] = $sc['address'];
        if ($stPhones)            $stFooterParts[] = implode(' · ', $stPhones);
        if ($sc['email'] ?? '')   $stFooterParts[] = $sc['email'];
    @endphp
    <div>{!! implode(' &bull; ', array_map('htmlspecialchars', $stFooterParts)) !!}</div>
    <div style="margin-top:3px;">This is a system-generated statement. For queries contact the office.</div>
</div>

</body>
</html>
