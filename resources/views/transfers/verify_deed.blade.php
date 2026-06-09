<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Transfer Deed - {{ $transfer->deed_no }}</title>
<style>
@page { size: A4; margin: 12mm 12mm 10mm 12mm; }
body {
    font-family: Arial, sans-serif;
    font-size: 9.5pt;
    margin: 0;
    padding: 0;
    background: white;
    color: #111;
}
.page { width: 100%; box-sizing: border-box; }

/* Header */
.header-bar {
    background: #0f172a;
    color: white;
    padding: 10pt 12pt;
    margin-bottom: 0;
}
.header-inner-table { width: 100%; border-collapse: collapse; }
.deed-title { font-size: 14pt; font-weight: bold; color: white; margin: 0; }
.deed-sub   { font-size: 9pt;  color: rgba(255,255,255,.65); margin: 3pt 0 0; }
.deed-no    { font-size: 11pt; font-weight: bold; color: #93c5fd; text-align: right; }

/* Green accent bar */
.accent-bar { background: #16a34a; height: 4pt; margin-bottom: 10pt; }

/* Section heading */
.sec-head {
    background: #1e3a8a;
    color: white;
    font-size: 9pt;
    font-weight: bold;
    padding: 4pt 8pt;
    text-transform: uppercase;
    letter-spacing: .5pt;
    margin-bottom: 0;
}

/* Info table */
.info-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8pt;
    border: 1pt solid #e2e8f0;
}
.info-table td {
    padding: 5pt 8pt;
    border-bottom: 1pt solid #f1f5f9;
    font-size: 9pt;
    vertical-align: top;
}
.info-table .lbl {
    color: #64748b;
    font-weight: bold;
    width: 90pt;
    white-space: nowrap;
    background: #f8fafc;
}
.info-table .val {
    font-weight: bold;
    color: #0f172a;
}

/* Chain of ownership */
.chain-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10pt;
}
.chain-table th {
    background: #1e3a8a;
    color: white;
    font-size: 8.5pt;
    font-weight: bold;
    padding: 5pt 7pt;
    text-align: left;
    border: 1pt solid #1e3a8a;
}
.chain-table td {
    padding: 5pt 7pt;
    font-size: 9pt;
    border: 1pt solid #e2e8f0;
    vertical-align: top;
}
.chain-table tr:nth-child(even) td { background: #f8fafc; }
.chain-table .current td { background: #f0fdf4 !important; }

/* Status pill (text-based, no border-radius for dompdf) */
.pill-green  { color: #15803d; font-weight: bold; }
.pill-amber  { color: #92400e; font-weight: bold; }
.pill-blue   { color: #1d4ed8; font-weight: bold; }
.pill-red    { color: #dc2626; font-weight: bold; }

/* Arrow between chain nodes */
.chain-arrow {
    text-align: center;
    font-size: 12pt;
    color: #1d4ed8;
    padding: 2pt 0;
}

/* Financial box */
.fin-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8pt;
    border: 1pt solid #e2e8f0;
}
.fin-table td {
    padding: 5pt 8pt;
    font-size: 9pt;
    border: 1pt solid #e2e8f0;
}
.fin-table .fin-lbl { background: #f8fafc; color: #64748b; font-weight: bold; }
.fin-table .fin-val { font-weight: bold; color: #0f172a; }
.fin-table .fin-total { background: #0f172a; color: white; font-weight: bold; font-size: 10pt; }

/* Signature section */
.sig-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12pt;
}
.sig-cell {
    width: 33%;
    text-align: center;
    padding: 0 8pt;
    vertical-align: top;
}
.sig-line {
    border-bottom: 1pt solid #333;
    height: 30pt;
    margin-bottom: 4pt;
}
.sig-label { font-size: 8.5pt; font-weight: bold; color: #475569; }

/* Footer */
.footer-bar {
    margin-top: 8pt;
    border-top: 1pt solid #e2e8f0;
    padding-top: 5pt;
    font-size: 8pt;
    color: #64748b;
    text-align: center;
}
.footer-stripe-table { width: 100%; border-collapse: collapse; margin-top: 6pt; }
.stripe-red    { background: #cc0000; height: 6pt; }
.stripe-orange { background: #ff8800; height: 6pt; }
.stripe-green  { background: #105e26; height: 6pt; }

/* QR placeholder */
.qr-box {
    width: 60pt;
    height: 60pt;
    border: 1pt solid #ccc;
    text-align: center;
    font-size: 7pt;
    color: #94a3b8;
    padding: 2pt;
}

@media print {
    body { margin: 0; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
}
</style>
</head>
<body>

@php
    $fromCustomer = $transfer->fromCustomer ?? $transfer->fromBooking?->customer;
    $toCustomer   = $transfer->toCustomer   ?? $transfer->toBooking?->customer;
    $plot         = $transfer->plot ?? $transfer->fromBooking?->plot;
    $booking      = $transfer->fromBooking;

    // ── Full chain of ownership for this plot ─────────────────
    // All transfers where this plot was involved, ordered by transfer_date
    $chain = \App\Models\PlotTransfer::with(['fromCustomer','toCustomer','fromBooking'])
        ->where('plot_id', $transfer->plot_id)
        ->whereIn('status', ['completed','pending'])
        ->orderBy('transfer_date', 'asc')
        ->get();

    // Original booking (parent_booking = null, oldest for this plot)
    $originalBooking = \App\Models\Booking::with('customer')
        ->where('plot_id', $transfer->plot_id)
        ->whereNull('parent_booking_id')
        ->oldest('booking_date')
        ->first();

    $discSentinel = 'Settlement discount — waived amount (not collected).';
    $plotCats = ['down_payment','installment','quarterly_installment','plot_balance','others'];

    // Use the balance stored at transfer time as the authoritative remaining figure
    $remaining = (float)($transfer->remaining_balance_transferred ?? 0);

    // Discount credits only
    $discPaid = $booking ? (
        $booking->payments->where('status','paid')->whereIn('payment_category', $plotCats)
            ->filter(fn($p) => ($p->remarks ?? '') === $discSentinel)->sum('amount_paid')
        + $booking->payments->where('status','paid')->whereIn('payment_category', $plotCats)
            ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)->sum('discount_amount')
    ) : 0;

    // Cash paid = total price − remaining balance (stored snapshot) − discount credits
    $cashPaid  = max(0, ($booking->total_price ?? 0) - $remaining - $discPaid);
    $totalPaid = $cashPaid;
    $equityPct = ($booking && $booking->total_price > 0)
        ? min(100, round((($cashPaid + $discPaid) / $booking->total_price) * 100))
        : 0;
@endphp

<div class="page">

<!-- ── HEADER ── -->
<div class="header-bar">
    <table class="header-inner-table">
        <tr>
            <td style="vertical-align:middle;">
                <div class="deed-title">ZAMAR VALLEY — TRANSFER DEED</div>
                <div class="deed-sub">A Project of Bin Abbasi Associates, Islamabad</div>
                <div class="deed-sub" style="margin-top:3pt;">
                    {{ ucfirst($transfer->transfer_type) }} Transfer
                    &nbsp;·&nbsp;
                    {{ $transfer->status === 'completed' ? '✓ Completed' : '⏳ Pending' }}
                </div>
            </td>
            <td style="text-align:right;vertical-align:top;">
                <div class="deed-no">{{ $transfer->deed_no }}</div>
                <div style="font-size:8pt;color:rgba(255,255,255,.55);margin-top:4pt;">
                    {{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}
                </div>
                @if(isset($qrCode))
                <div style="margin-top:6pt;">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width:50pt;height:50pt;" alt="QR">
                </div>
                @endif
            </td>
        </tr>
    </table>
</div>
<div class="accent-bar"></div>

<!-- ── PLOT DETAILS ── -->
<div class="sec-head">Property Details</div>
<table class="info-table">
    <tr>
        <td class="lbl">Plot No.</td>
        <td class="val">{{ $plot->plot_number ?? '—' }}</td>
        <td class="lbl">Block</td>
        <td class="val">{{ $plot->block ?? '—' }}</td>
        <td class="lbl">Sector</td>
        <td class="val">{{ $plot->sector ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Size</td>
        <td class="val">{{ ($plot->size ?? '—') . ' ' . ($plot->unit ?? '') }}</td>
        <td class="lbl">Street No.</td>
        <td class="val">{{ $plot->street_number ?? '—' }}</td>
        <td class="lbl">Transfer Count</td>
        <td class="val">{{ $plot->transfer_count ?? $chain->count() }} / 5</td>
    </tr>
    <tr>
        <td class="lbl">Category</td>
        <td class="val">{{ $plot->category->name ?? 'Residential' }}</td>
        <td class="lbl">Society</td>
        <td class="val">{{ $plot->society ?? 'Zamar Valley' }}</td>
        <td class="lbl">City</td>
        <td class="val">{{ $plot->city ?? 'Islamabad' }}</td>
    </tr>
</table>

<!-- ── CHAIN OF OWNERSHIP ── -->
<div class="sec-head">Complete Chain of Ownership</div>
<table class="chain-table">
    <tr>
        <th>#</th>
        <th>Owner</th>
        <th>CNIC</th>
        <th>Deed No.</th>
        <th>Date</th>
        <th>Type</th>
        <th>Consideration</th>
        <th>Status</th>
    </tr>

    @php $chainNo = 1; @endphp

    {{-- Original owner row --}}
    @if($originalBooking)
    <tr>
        <td style="text-align:center;font-weight:bold;">0</td>
        <td>
            <strong>{{ $originalBooking->customer->name ?? '—' }}</strong><br>
            <span style="font-size:8pt;color:#64748b;">Original Allottee</span>
        </td>
        <td style="font-family:monospace;font-size:8.5pt;">{{ $originalBooking->customer->cnic ?? '—' }}</td>
        <td style="font-family:monospace;font-size:8.5pt;">
            {{ $originalBooking->customer_booking_id ?? '—' }}<br>
            <span style="font-size:7.5pt;color:#64748b;">Booking Ref.</span>
        </td>
        <td>{{ \Carbon\Carbon::parse($originalBooking->booking_date)->format('d M Y') }}</td>
        <td><span class="pill-blue">Original</span></td>
        <td style="font-weight:bold;">PKR {{ number_format($originalBooking->total_price ?? 0) }}</td>
        <td><span class="pill-green">Active</span></td>
    </tr>
    @endif

    {{-- All transfer rows --}}
    @foreach($chain as $tr)
    @php $isCurrent = ($tr->id === $transfer->id); @endphp
    <tr class="{{ $isCurrent ? 'current' : '' }}">
        <td style="text-align:center;font-weight:bold;">{{ $chainNo++ }}</td>
        <td>
            <div style="font-size:8pt;color:#94a3b8;">FROM:</div>
            <strong>{{ $tr->fromCustomer->name ?? '—' }}</strong>
            @if($tr->toCustomer)
            <div style="margin-top:3pt;font-size:8pt;color:#94a3b8;">TO:</div>
            <strong style="color:#16a34a;">{{ $tr->toCustomer->name ?? '—' }}</strong>
            @endif
            @if($isCurrent)<div style="margin-top:2pt;"><span style="font-size:7.5pt;font-weight:bold;color:#1d4ed8;">&#9654; CURRENT DEED</span></div>@endif
        </td>
        <td style="font-size:8pt;">
            <span style="color:#64748b;">From:</span><br>
            <span style="font-family:monospace;">{{ $tr->fromCustomer->cnic ?? '—' }}</span>
            @if($tr->toCustomer)
            <br><span style="color:#64748b;">To:</span><br>
            <span style="font-family:monospace;">{{ $tr->toCustomer->cnic ?? '—' }}</span>
            @endif
        </td>
        <td style="font-family:monospace;font-size:8.5pt;font-weight:bold;">{{ $tr->deed_no }}</td>
        <td style="font-size:8.5pt;">{{ \Carbon\Carbon::parse($tr->transfer_date)->format('d M Y') }}</td>
        <td>
            <span class="{{ $tr->transfer_type === 'ownership' ? 'pill-blue' : 'pill-amber' }}">
                {{ ucfirst($tr->transfer_type) }}
            </span>
            @if($tr->ownership_percentage)
            <br><span style="font-size:7.5pt;">{{ $tr->ownership_percentage }}% share</span>
            @endif
        </td>
        <td style="font-weight:bold;">
            @if($tr->consideration_amount)
                PKR {{ number_format($tr->consideration_amount) }}
            @else
                <span style="color:#94a3b8;font-size:8pt;">Not recorded</span>
            @endif
            @if($tr->remaining_balance_transferred)
            <br><span style="font-size:7.5pt;color:#64748b;">Bal: PKR {{ number_format($tr->remaining_balance_transferred) }}</span>
            @endif
        </td>
        <td>
            @if($tr->status === 'completed')
                <span class="pill-green">&#10003; Done</span>
            @elseif($tr->status === 'pending')
                <span class="pill-amber">&#9203; Pending</span>
            @else
                <span class="pill-red">{{ ucfirst($tr->status) }}</span>
            @endif
        </td>
    </tr>
    @endforeach

    @if($chain->isEmpty())
    <tr>
        <td colspan="8" style="text-align:center;color:#94a3b8;padding:10pt;">No previous transfers — this is the first transfer for this plot.</td>
    </tr>
    @endif
</table>

<!-- ── THIS DEED DETAILS ── -->
<div class="sec-head">Current Transfer — {{ $transfer->deed_no }}</div>
<table style="width:100%;border-collapse:collapse;">
    <tr>
        <td style="width:50%;padding-right:6pt;vertical-align:top;">
            <table class="info-table">
                <tr>
                    <td class="lbl">Transferor</td>
                    <td class="val">{{ $fromCustomer->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">CNIC</td>
                    <td class="val" style="font-family:monospace;">{{ $fromCustomer->cnic ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Address</td>
                    <td class="val" style="font-size:8.5pt;">{{ $fromCustomer->residential_address ?? $fromCustomer->address ?? '—' }}</td>
                </tr>
            </table>
        </td>
        <td style="width:50%;padding-left:6pt;vertical-align:top;">
            <table class="info-table">
                <tr>
                    <td class="lbl">Transferee</td>
                    <td class="val">{{ $toCustomer->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">CNIC</td>
                    <td class="val" style="font-family:monospace;">{{ $toCustomer->cnic ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Address</td>
                    <td class="val" style="font-size:8.5pt;">{{ $toCustomer->residential_address ?? $toCustomer->address ?? '—' }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- ── FINANCIAL SUMMARY ── -->
<table style="width:100%;border-collapse:collapse;margin-top:4pt;">
    <tr>
        <td style="width:60%;padding-right:8pt;vertical-align:top;">
            <div class="sec-head">Financial Summary</div>
            <table class="fin-table">
                <tr>
                    <td class="fin-lbl">Total Plot Price</td>
                    <td class="fin-val">PKR {{ number_format($booking->total_price ?? 0) }}</td>
                </tr>
                <tr>
                    <td class="fin-lbl">Cash Paid</td>
                    <td class="fin-val" style="color:#16a34a;">PKR {{ number_format($cashPaid) }}</td>
                </tr>
                @if($discPaid > 0)
                <tr>
                    <td class="fin-lbl" style="color:#92400e;">Settlement Discount</td>
                    <td class="fin-val" style="color:#92400e;">PKR {{ number_format($discPaid) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="fin-lbl">Remaining Balance</td>
                    <td class="fin-val" style="color:#dc2626;">PKR {{ number_format($remaining) }}</td>
                </tr>
                <tr>
                    <td class="fin-lbl">Balance Transferred</td>
                    <td class="fin-val">PKR {{ number_format($transfer->remaining_balance_transferred ?? $remaining) }}</td>
                </tr>
                <tr>
                    <td class="fin-lbl">Consideration Paid</td>
                    <td class="fin-val" style="color:#1d4ed8;">
                        @if($transfer->consideration_amount)
                            PKR {{ number_format($transfer->consideration_amount) }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="fin-total">Payment Progress</td>
                    <td class="fin-total">{{ $equityPct }}% Collected</td>
                </tr>
            </table>
        </td>
        <td style="width:40%;vertical-align:top;padding-left:8pt;">
            <div class="sec-head">Transfer Fee</div>
            <table class="fin-table">
                <tr>
                    <td class="fin-lbl">Transfer Fee</td>
                    <td class="fin-val">PKR {{ number_format($transfer->transfer_fee ?? 0) }}</td>
                </tr>
                <tr>
                    <td class="fin-lbl">Fee Status</td>
                    <td class="fin-val">
                        @if(($transfer->transfer_fee_status ?? 'pending') === 'paid')
                            <span class="pill-green">&#10003; Paid</span>
                        @else
                            <span class="pill-amber">Pending</span>
                        @endif
                    </td>
                </tr>
                @if($transfer->reason)
                <tr>
                    <td class="fin-lbl">Reason</td>
                    <td style="font-size:8.5pt;">{{ $transfer->reason }}</td>
                </tr>
                @endif
                @if($transfer->notes)
                <tr>
                    <td class="fin-lbl">Notes</td>
                    <td style="font-size:8.5pt;">{{ $transfer->notes }}</td>
                </tr>
                @endif
            </table>
        </td>
    </tr>
</table>

<!-- ── WITNESS SECTION ── -->
<div class="sec-head">Witnesses</div>
<table style="width:100%;border-collapse:collapse;border:1pt solid #e2e8f0;">
    <tr>
        <td style="width:50%;padding:8pt 10pt;border-right:1pt solid #e2e8f0;vertical-align:top;">
            <strong style="font-size:9pt;">Witness No. 1</strong><br><br>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="color:#64748b;font-size:8.5pt;font-weight:bold;width:50pt;vertical-align:bottom;">Name:</td>
                    <td style="border-bottom:1pt dotted #333;height:13pt;font-size:9pt;">{{ $transfer->witness1_name ?? '' }}</td>
                </tr>
            </table>
            <div style="height:6pt;"></div>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="color:#64748b;font-size:8.5pt;font-weight:bold;width:50pt;vertical-align:bottom;">CNIC:</td>
                    <td style="border-bottom:1pt dotted #333;height:13pt;font-family:monospace;font-size:9pt;">{{ $transfer->witness1_cnic ?? '' }}</td>
                </tr>
            </table>
            <div style="height:20pt;border-bottom:1pt dotted #333;margin-top:8pt;"></div>
            <div style="font-size:7.5pt;color:#94a3b8;margin-top:2pt;text-align:center;">Signature &amp; Thumb</div>
        </td>
        <td style="width:50%;padding:8pt 10pt;vertical-align:top;">
            <strong style="font-size:9pt;">Witness No. 2</strong><br><br>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="color:#64748b;font-size:8.5pt;font-weight:bold;width:50pt;vertical-align:bottom;">Name:</td>
                    <td style="border-bottom:1pt dotted #333;height:13pt;font-size:9pt;">{{ $transfer->witness2_name ?? '' }}</td>
                </tr>
            </table>
            <div style="height:6pt;"></div>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="color:#64748b;font-size:8.5pt;font-weight:bold;width:50pt;vertical-align:bottom;">CNIC:</td>
                    <td style="border-bottom:1pt dotted #333;height:13pt;font-family:monospace;font-size:9pt;">{{ $transfer->witness2_cnic ?? '' }}</td>
                </tr>
            </table>
            <div style="height:20pt;border-bottom:1pt dotted #333;margin-top:8pt;"></div>
            <div style="font-size:7.5pt;color:#94a3b8;margin-top:2pt;text-align:center;">Signature &amp; Thumb</div>
        </td>
    </tr>
</table>

<!-- ── SIGNATURE ROW ── -->
<table class="sig-table" style="margin-top:14pt;">
    <tr>
        <td class="sig-cell">
            <div class="sig-line"></div>
            <div class="sig-label">TRANSFEROR SIGNATURE</div>
            <div style="font-size:8pt;color:#94a3b8;margin-top:2pt;">{{ $fromCustomer->name ?? '' }}</div>
        </td>
        <td class="sig-cell">
            <div class="sig-line"></div>
            <div class="sig-label">TRANSFEREE SIGNATURE</div>
            <div style="font-size:8pt;color:#94a3b8;margin-top:2pt;">{{ $toCustomer->name ?? '' }}</div>
        </td>
        <td class="sig-cell">
            <div class="sig-line"></div>
            <div class="sig-label">ZAMAR VALLEY — AUTHORISED OFFICER</div>
            <div style="font-size:8pt;color:#94a3b8;margin-top:2pt;">Stamp &amp; Seal</div>
        </td>
    </tr>
</table>

<!-- ── FOOTER ── -->
<div class="footer-bar">
    Zamar Valley Digital Registry &nbsp;|&nbsp;
    Generated: {{ now()->format('d M Y h:i A') }} &nbsp;|&nbsp;
    Ref: {{ strtoupper(substr(md5($transfer->id.$transfer->deed_no),0,10)) }}
</div>

<table class="footer-stripe-table">
    <tr>
        <td class="stripe-red"></td>
        <td class="stripe-orange"></td>
        <td class="stripe-green"></td>
    </tr>
</table>

</div>
</body>
</html>
