<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

    /* ── Page layout ── */
    .page { padding: 15px;

         }

    /* ── Header ── */
    .header { text-align: center; border-bottom: 3px solid #0f2460; padding-bottom: 12px; margin-bottom: 10px; }
    .company-name { font-size: 22px; font-weight: 700; color: #0f2460; letter-spacing: 1px; }
    .doc-title { font-size: 15px; font-weight: 700; color: #1e40af; margin-top: 4px; text-transform: uppercase; letter-spacing: 2px; }
    .doc-sub { font-size: 10px; color: #64748b; margin-top: 3px; }

    /* ── Deed meta row ── */
    .meta-row { display: table; width: 100%; margin-bottom: 10px; }
    .meta-cell { display: table-cell; width: 33%; vertical-align: top; }
    .meta-label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
    .meta-value { font-size: 12px; font-weight: 700; color: #0f172a; margin-top: 2px; }

    /* ── Section title ── */
    .section-title {
        font-size: 11px; font-weight: 700; color: #fff;
        background: #0f2460; padding: 7px 14px;
        margin-bottom: 0; text-transform: uppercase; letter-spacing: .8px;
    }

    /* ── Party cards ── */
    .parties-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .party-cell { width: 50%; vertical-align: top; padding: 12px; border: 1px solid #e2e8f0; }
    .party-header { font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #f1f5f9; }
    .party-name { font-size: 13px; font-weight: 700; color: #0f172a; }
    .party-detail { font-size: 10px; color: #475569; margin-top: 3px; }

    /* ── Plot swap arrow ── */
    .swap-row { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .plot-card { width: 44%; vertical-align: middle; padding: 12px; border: 1.5px solid #bfdbfe; border-radius: 4px; background: #f0f9ff; text-align: center; }
    .arrow-cell { width: 12%; text-align: center; vertical-align: middle; font-size: 22px; color: #1e40af; font-weight: 700; }
    .plot-no { font-size: 18px; font-weight: 800; color: #1e3a8a; }
    .plot-detail { font-size: 10px; color: #475569; margin-top: 3px; }
    .plot-owner { font-size: 10px; font-weight: 700; color: #0f172a; margin-top: 5px; padding-top: 5px; border-top: 1px solid #bfdbfe; }

    /* ── Terms table ── */
    .terms-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .terms-table td { padding: 6px 10px; border: 1px solid #e2e8f0; font-size: 10.5px; vertical-align: top; }
    .terms-table .td-label { font-weight: 700; color: #475569; background: #f8fafc; width: 35%; }
    .terms-table .td-value { color: #0f172a; font-weight: 600; }

    /* ── Declaration ── */
    .declaration { background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; margin-bottom: 12px; font-size: 10px; color: #475569; line-height: 1.7; }
    .declaration strong { color: #0f172a; }

    /* ── Signatures ── */
    .sig-table { width: 100%; border-collapse: collapse; margin-top: 12px;}
    .sig-cell { width: 33%; text-align: center; padding: 6px 10px; vertical-align: bottom; }
    .sig-line { border-top: 1.5px solid #334155; padding-top: 6px; margin-top: 40px; }
    .sig-name { font-size: 10.5px; font-weight: 700; color: #0f172a; }
    .sig-role { font-size: 9px; color: #94a3b8; margin-top: 2px; }

    /* ── QR + footer ── */
    .footer-row { display: table; width: 100%; margin-top: 12px; border-top: 2px solid #0f2460; padding-top: 8px; }
    .footer-left { display: table-cell; vertical-align: middle; font-size: 9px; color: #94a3b8; width: 70%; }
    .footer-right { display: table-cell; vertical-align: middle; text-align: right; width: 30%; }
    .qr-label { font-size: 9px; color: #64748b; text-align: center; margin-top: 4px; }

    /* ── Status stamp ── */
    .stamp {
        position: absolute; top: 120px; right: 40px;
        border: 3px solid #16a34a; color: #16a34a;
        padding: 6px 14px; border-radius: 4px;
        font-size: 13px; font-weight: 800; letter-spacing: 2px;
        transform: rotate(-15deg); opacity: .35;
        text-transform: uppercase;
    }
</style>
</head>
<body>
<div class="page">

    {{-- ── Stamp ── --}}
    @if($transfer->status === 'completed')
    <div class="stamp">COMPLETED</div>
    @endif

    {{-- ── Header ── --}}
    <div class="header">
        <div class="company-name">ZAMAR VALLEY REAL ESTATE</div>
        <div class="doc-title">Plot Swap Transfer Deed</div>
        <div class="doc-sub">This document serves as an official record of plot swap between two parties</div>
    </div>

    {{-- ── Deed Meta ── --}}
    <div class="meta-row">
        <div class="meta-cell">
            <div class="meta-label">Deed No.</div>
            <div class="meta-value">{{ $transfer->deed_no }}</div>
        </div>
        <div class="meta-cell" style="text-align:center;">
            <div class="meta-label">Transfer Date</div>
            <div class="meta-value">{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</div>
        </div>
        <div class="meta-cell" style="text-align:right;">
            <div class="meta-label">Status</div>
            <div class="meta-value" style="color:{{ $transfer->status === 'completed' ? '#16a34a' : '#f59e0b' }};">
                {{ ucfirst($transfer->status) }}
            </div>
        </div>
    </div>

    {{-- ── Party A & Party B ── --}}
    <div class="section-title">Parties Involved</div>
    <table class="parties-table">
        <tr>
            {{-- Party A --}}
            <td class="party-cell">
                <div class="party-header">Party A — Transferring Party</div>
                <div class="party-name">{{ $transfer->fromBooking->customer->name ?? '—' }}</div>
                <div class="party-detail">CNIC: {{ $transfer->fromBooking->customer->cnic ?? '—' }}</div>
                <div class="party-detail">Phone: {{ $transfer->fromBooking->customer->phone ?? '—' }}</div>
                <div class="party-detail" style="margin-top:6px;">
                    Booking Ref: <strong>{{ $transfer->fromBooking->customer_booking_id ?? '—' }}</strong>
                </div>
            </td>
            {{-- Party B --}}
            <td class="party-cell" style="border-left:none;">
                <div class="party-header">Party B — Receiving Party</div>
                <div class="party-name">{{ $swapBooking->customer->name ?? '—' }}</div>
                <div class="party-detail">CNIC: {{ $swapBooking->customer->cnic ?? '—' }}</div>
                <div class="party-detail">Phone: {{ $swapBooking->customer->phone ?? '—' }}</div>
                <div class="party-detail" style="margin-top:6px;">
                    Booking Ref: <strong>{{ $swapBooking->customer_booking_id ?? '—' }}</strong>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Plot Swap Visual ── --}}
  <div class="section-title">Plot Exchange Details</div>
<table class="swap-row" style="margin-top:0;">
<tr>

{{-- Plot A (goes to Party B) --}}
<td class="plot-card">

<div style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:6px;">
Plot Given by Party A
</div>

<div class="plot-no">
Plot #{{ $transfer->fromBooking->plot->plot_number ?? '—' }}
</div>

<div class="plot-detail">
Block: {{ $transfer->fromBooking->plot->block ?? '—' }}
</div>

<div class="plot-detail">
{{ $transfer->fromBooking->plot->size ?? '' }} {{ $transfer->fromBooking->plot->unit ?? '' }}
</div>

<div class="plot-detail" style="margin-top:6px;">
Price: PKR {{ number_format($fromTotalPrice ?? 0) }}
</div>

<div class="plot-detail">
Paid: PKR {{ number_format($fromPaid ?? 0) }}
</div>

<div class="plot-detail">
Remaining: PKR {{ number_format($fromRemaining ?? 0) }}
</div>

<div class="plot-owner">
Now owned by: {{ $swapBooking->customer->name ?? '—' }}
</div>

</td>


{{-- Arrow --}}
<td class="arrow-cell">&#8644;</td>


{{-- Plot B (goes to Party A) --}}
<td class="plot-card">

<div style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:6px;">
Plot Given by Party B
</div>

<div class="plot-no">
Plot #{{ $swapBooking->plot->plot_number ?? '—' }}
</div>

<div class="plot-detail">
Block: {{ $swapBooking->plot->block ?? '—' }}
</div>

<div class="plot-detail">
{{ $swapBooking->plot->size ?? '' }} {{ $swapBooking->plot->unit ?? '' }}
</div>

<div class="plot-detail" style="margin-top:6px;">
Price: PKR {{ number_format($toTotalPrice ?? 0) }}
</div>

<div class="plot-detail">
Paid: PKR {{ number_format($toPaid ?? 0) }}
</div>

<div class="plot-detail">
Remaining: PKR {{ number_format($toRemaining ?? 0) }}
</div>

<div class="plot-owner">
Now owned by: {{ $transfer->fromBooking->customer->name ?? '—' }}
</div>

</td>

</tr>
</table>
    {{-- ── Transfer Terms ── --}}
    <div class="section-title">Transfer Terms</div>
    <table class="terms-table">
        <tr>
            <td class="td-label">Transfer Fee</td>
            <td class="td-value">PKR {{ number_format($transfer->transfer_fee) }}</td>
            <td class="td-label">Fee Status</td>
            <td class="td-value" style="color:{{ $transfer->transfer_fee_status === 'paid' ? '#16a34a' : '#dc2626' }};">
                {{ ucfirst($transfer->transfer_fee_status) }}
            </td>
        </tr>
        <tr>
            <td class="td-label">Fee Receipt No.</td>
            <td class="td-value">{{ $transfer->transfer_fee_receipt_no ?? '—' }}</td>
            <td class="td-label">Payment Date</td>
            <td class="td-value">{{ $transfer->fee_paid_date ? \Carbon\Carbon::parse($transfer->fee_paid_date)->format('d M Y') : '—' }}</td>
        </tr>
        <tr>
            <td class="td-label">Payment Method</td>
            <td class="td-value">{{ ucfirst(str_replace('_', ' ', $transfer->payment_method ?? '—')) }}</td>
            <td class="td-label">Processed By</td>
            <td class="td-value">{{ $transfer->approved_by ?? '—' }}</td>
        </tr>
        @if($transfer->reason)
        <tr>
            <td class="td-label">Reason / Notes</td>
            <td class="td-value" colspan="3">{{ $transfer->reason }}</td>
        </tr>
        @endif
    </table>

    {{-- ── Declaration ── --}}
    <div class="declaration">
        We, <strong>{{ $transfer->fromBooking->customer->name ?? '—' }}</strong> (Party A)
        and <strong>{{ $swapBooking->customer->name ?? '—' }}</strong> (Party B),
        hereby declare that we mutually agree to exchange the above-mentioned plots under the terms set
        by <strong>Zamar Valley Real Estate</strong>. Both parties acknowledge that this swap is voluntary,
        final, and binding. All future rights, liabilities, and responsibilities associated with the
        respective plots shall transfer accordingly upon completion of this deed.
        This document has been executed on
        <strong>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</strong>
        at the offices of Zamar Valley Real Estate.
    </div>

    {{-- ── Signatures ── --}}
    <table class="sig-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-line">
                    <div class="sig-name">{{ $transfer->fromBooking->customer->name ?? '—' }}</div>
                    <div class="sig-role">Party A — Signature & Stamp</div>
                </div>
            </td>
            <td class="sig-cell">
                <div class="sig-line">
                    <div class="sig-name">{{ $swapBooking->customer->name ?? '—' }}</div>
                    <div class="sig-role">Party B — Signature & Stamp</div>
                </div>
            </td>
            <td class="sig-cell">
                <div class="sig-line">
                    <div class="sig-name">Authorized Officer</div>
                    <div class="sig-role">Zamar Valley — Signature & Stamp</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Footer + QR ── --}}
    <div class="footer-row">
        <div class="footer-left">
            <strong style="color:#0f2460;">ZAMAR VALLEY REAL ESTATE</strong><br>
            Deed No: {{ $transfer->deed_no }} &nbsp;|&nbsp;
            Generated: {{ now()->format('d M Y, h:i A') }}<br>
            This document is computer-generated and valid without a physical signature if QR verified.
        </div>
        <div class="footer-right">
            <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" width="80" height="80">
        </div>
    </div>

</div>
</body>
</html>
