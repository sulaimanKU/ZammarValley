<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ZV-Statement-{{ $booking->customer_booking_id }}</title>
    <style>
        @page { margin: 0.3in 0.4in; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 9px; color: #1e293b;
            line-height: 1.3; margin: 0; padding: 0; background: #fff;
        }

        .watermark {
            position: fixed; top: 35%; left: 10%;
            font-size: 60px; color: rgba(30,58,138,0.03);
            transform: rotate(-40deg); z-index: -1000;
            font-weight: 900; text-transform: uppercase; letter-spacing: 10px;
        }

        /* ── HEADER ── */
        .header-wrap { background: #1e3a8a; padding: 15px 20px; color: #fff; }
        .header-wrap table { width: 100%; border-collapse: collapse; }
        .brand-name { font-size: 22px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; }
        .receipt-label { font-size: 10px; font-weight: bold; color: #fbbf24; text-transform: uppercase; }

        /* ── HANDLED BY STRIP ── */
        .handled-strip {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            padding: 6px 12px; margin-top: 8px; border-radius: 4px;
        }
        .handled-strip table { width: 100%; border-collapse: collapse; }

        /* ── SECTIONS ── */
        .sec-title {
            font-size: 9px; font-weight: 800; color: #1e3a8a;
            text-transform: uppercase; border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px; margin-bottom: 8px; margin-top: 15px; display: block;
        }
        .card-box { border: 1px solid #e2e8f0; border-radius: 4px; overflow: hidden; margin-bottom: 10px; }

        /* ── TABLES ── */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .data-table th { background: #f8fafc; color: #64748b; font-size: 7px; text-transform: uppercase; padding: 5px 8px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .data-table td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; font-size: 9px; }

        /* ── LEDGER TABLE ── */
        .ledger-table { width: 100%; border-collapse: collapse; }
        .ledger-table th { background: #1e3a8a; color: #fff; padding: 6px 8px; font-size: 8px; text-transform: uppercase; text-align: left; }
        .ledger-table td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; }
        .ledger-table tr:nth-child(even) { background: #fcfdfe; }

        /* ── PREVIOUS OWNER BOX ── */
        .prev-owner-box { border: 1px solid #fde68a; border-radius: 4px; overflow: hidden; margin-bottom: 10px; }
        .prev-owner-header { background: #92400e; color: #fff; padding: 5px 8px; font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; }
        .prev-owner-table { width: 100%; border-collapse: collapse; background: #fffbeb; }
        .prev-owner-table td { padding: 5px 8px; border-bottom: 1px solid #fef3c7; font-size: 9px; }
        .prev-owner-table .lbl { font-weight: bold; color: #92400e; width: 20%; }

        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .summary-box { background: #1e3a8a; color: #fff; padding: 10px; border-radius: 4px; margin-top: 5px; }
        .summary-row { display: table; width: 100%; margin-bottom: 3px; }
        .summary-cell { display: table-cell; font-size: 9px; }

        .qr-wrap { text-align: center; border: 1px solid #e2e8f0; padding: 5px; border-radius: 4px; }
        .footer { margin-top: 20px; border-top: 1px dashed #cbd5e1; padding-top: 10px; font-size: 7px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>

@php
    // ── Society config from controller (via HasSocietyConfig trait) ──────
    $socName     = $sc['name']           ?? 'Zamar Valley';
    $socTagline  = $sc['tagline']        ?? 'Premium Housing Project';
    $socPhone    = $sc['phone']          ?? '';
    $socEmail    = $sc['email']          ?? '';
    $socAddress  = $sc['address']        ?? '';
    $watermark   = $sc['watermark']      ?? strtoupper($socName);
    $footerNote  = $sc['receipt_footer'] ?? '';
    $showLogo    = $sc['show_logo']      ?? true;
    $logoSrc     = $sc['logo']           ?? null;   // base64 data URI from trait
    $showLogoImg = $showLogo && $logoSrc;
@endphp

{{-- ── WATERMARK from SystemConfig ── --}}
<div class="watermark">{{ $watermark ?: strtoupper($socName) }}</div>

{{-- ── HEADER ── --}}
<div class="header-wrap">
    <table>
        <tr>
            <td>
                @if($showLogoImg)
                <table style="border-collapse:collapse; width:auto;">
                    <tr>
                        <td style="width:46px; vertical-align:middle; border:none; padding-right:10px;">
                            <img src="{{ $logoSrc }}" width="40" height="40"
                                 style="object-fit:contain; border-radius:4px; border:1px solid rgba(255,255,255,0.2);"
                                 alt="{{ $socName }}">
                        </td>
                        <td style="vertical-align:middle; border:none;">
                            <div class="brand-name">{{ $socName }}</div>
                            <div style="font-size:8px; color:#bfdbfe;">{{ $socTagline }} — Account Statement</div>
                        </td>
                    </tr>
                </table>
                @else
                <div class="brand-name">{{ $socName }}</div>
                <div style="font-size:8px; color:#bfdbfe;">{{ $socTagline }} — Account Statement</div>
                @endif
            </td>
            <td style="text-align:right; vertical-align:top;">
                <div class="receipt-label">Account Ledger Receipt</div>
                <div style="font-size:12px; font-weight:bold; color:#fff;">{{ $booking->customer_booking_id }}</div>
                @if($socPhone)
                <div style="font-size:7px; color:#bfdbfe; margin-top:3px;">{{ $socPhone }}</div>
                @endif
                @if($socEmail)
                <div style="font-size:7px; color:#bfdbfe;">{{ $socEmail }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- ── HANDLED BY STRIP ── --}}
<div class="handled-strip">
    <table>
        <tr>
            <td>
                <span style="font-size:7px; font-weight:800; color:#15803d; text-transform:uppercase;">Booking Handled By:</span>
                <span style="font-size:9px; font-weight:700; color:#14532d; margin-left:5px;">
                    {{ $booking->createdBy->name ?? $booking->user->name ?? $socName . ' Staff' }}
                </span>
            </td>
            <td style="text-align:right;">
                <span style="font-size:7px; color:#64748b;">Booking Date: </span>
                <span style="font-size:8px; font-weight:700;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
            </td>
        </tr>
    </table>
</div>

{{-- ── TOP INFO BAR ── --}}
<table width="100%" style="margin-top:15px;">
    <tr>
        <td width="75%" valign="top">
            <span class="sec-title">Client &amp; Plot Information</span>
            <div class="card-box">
                <table class="data-table" style="margin-bottom:0;">
                    <tr>
                        <td class="font-bold" width="15%">Customer:</td>
                        <td width="35%">{{ $booking->customer->name }}</td>
                        <td class="font-bold" width="15%">CNIC:</td>
                        <td width="35%">{{ $booking->customer->cnic }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Phone:</td>
                        <td>{{ $booking->customer->phone ?? '—' }}</td>
                        <td class="font-bold">City:</td>
                        <td>{{ $booking->customer->city ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Plot Details:</td>
                        <td>#{{ $booking->plot->plot_number }} ({{ $booking->plot->size }} {{ $booking->plot->unit }})</td>
                        <td class="font-bold">Block/Sec:</td>
                        <td>{{ $booking->plot->block }} / {{ $booking->plot->sector ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Booking Type:</td>
                        <td>{{ $booking->booking_type }}</td>
                        <td class="font-bold">Status:</td>
                        <td><strong>{{ strtoupper(str_replace('_',' ', $booking->status)) }}</strong></td>
                    </tr>
                </table>
            </div>
        </td>
        <td width="2%"></td>
        @if(isset($qrCode) && $qrCode)
        <td width="23%" valign="top">
            <span class="sec-title">Verification</span>
            <div class="qr-wrap">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="75">
                <div style="font-size:7px; color:#1e3a8a; font-weight:bold; margin-top:5px;">OFFICIAL RECORD</div>
                <div style="font-size:6px; color:#94a3b8; margin-top:2px;">Scan for live balance</div>
            </div>
        </td>
        @endif
    </tr>
</table>

{{-- ── PREVIOUS OWNER SECTION — only for Transfer bookings ── --}}
@if($booking->booking_type === 'Transfer' && ($booking->previous_owner_name || $booking->previous_deed_no || $booking->previous_owner_cnic))
<span class="sec-title">Previous Owner Details (Transfer Booking)</span>
<div class="prev-owner-box">
    <div class="prev-owner-header">Previous Owner — Paper Deed / Private Sale Record</div>
    <table class="prev-owner-table">
        <tr>
            <td class="lbl">Previous Owner:</td>
            <td>{{ $booking->previous_owner_name ?? '—' }}</td>
            <td class="lbl">Previous CNIC:</td>
            <td>{{ $booking->previous_owner_cnic ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Deed / Ref No.:</td>
            <td>{{ $booking->previous_deed_no ?? '—' }}</td>
            <td class="lbl">Transfer Date:</td>
            <td>
                {{ $booking->previous_transfer_date
                    ? \Carbon\Carbon::parse($booking->previous_transfer_date)->format('d M Y')
                    : '—' }}
            </td>
        </tr>
        <tr>
            <td class="lbl">CNIC Doc:</td>
            <td>{{ $booking->previous_owner_cnic_doc ? 'Uploaded ✓' : 'Not uploaded' }}</td>
            <td class="lbl">Sale Deed:</td>
            <td>{{ $booking->previous_sale_deed ? 'Uploaded ✓' : 'Not uploaded' }}</td>
        </tr>
    </table>
</div>
@endif

{{-- ── FULL PAYMENT RECORD ── --}}
<span class="sec-title">Detailed Payment History (All Records)</span>
<div class="card-box">
    <table class="ledger-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt #</th>
                <th>Category</th>
                <th>Payment Type</th>
                <th class="text-right">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            @php $runningTotal = 0; @endphp
            @forelse($booking->payments->where('status', 'paid') as $pay)
                @php
                    // Only count real (non-external) payments in totals
                    if (!$pay->is_external) {
                        $runningTotal += $pay->amount_paid;
                    }
                @endphp
                <tr style="{{ $pay->is_external ? 'background:#f8f8f8; color:#aaa;' : '' }}">
                    <td>{{ date('d-M-Y', strtotime($pay->paid_date)) }}</td>
                    <td>
                        <span style="font-family:monospace; font-weight:bold;">{{ $pay->receipt_no }}</span>
                        @if($pay->is_external)
                            <span style="font-size:7px; color:#aaa;">(External)</span>
                        @endif
                    </td>
                    <td>
                        {{ ucwords(str_replace('_', ' ', $pay->payment_category)) }}
                        @if($pay->installment_no) (Month {{ $pay->installment_no }}) @endif
                    </td>
                    <td>{{ ucfirst($pay->payment_type) }}</td>
                    <td class="text-right font-bold" style="{{ $pay->is_external ? 'color:#aaa;' : '' }}">
                        {{ $pay->is_external ? '—' : number_format($pay->amount_paid) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px; color:#94a3b8;">No payments recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── SUMMARY ── --}}
<table width="100%" style="margin-top:10px;">
    <tr>
        <td width="60%" valign="top">
            <div style="padding:10px; border:1px solid #dcfce7; background:#f0fdf4; border-radius:4px;">
                <div style="font-weight:bold; color:#166534; font-size:8px; text-transform:uppercase;">Account Notes</div>
                <div style="font-size:8px; color:#15803d; margin-top:4px;">
                    This document reflects all payments received by {{ $socName }} including Down-payment and Installments.
                    External/historical records are shown for reference only and not counted in totals.
                    @if($booking->status == 'completed')
                        <br><strong>Status:</strong> All dues cleared. This property is fully paid.
                    @endif
                </div>
            </div>
        </td>
        <td width="5%"></td>
       <td width="35%" valign="top">
    <div class="summary-box">
        <div class="summary-row">
            <div class="summary-cell">Total Plot Price:</div>
            <div class="summary-cell text-right font-bold">
                PKR {{ number_format($booking->total_price + $booking->processing_fee) }}
            </div>
        </div>
        <div class="summary-row" style="color:#fbbf24; border-bottom:1px solid rgba(255,255,255,0.2); padding-bottom:5px; margin-bottom:5px;">
            <div class="summary-cell">Total Received:</div>
            <div class="summary-cell text-right font-bold">
                - PKR {{ number_format($runningTotal) }}
            </div>
        </div>
        <div class="summary-row">
            <div class="summary-cell" style="font-size:10px; font-weight:bold;">Remaining Payable:</div>
            <div class="summary-cell text-right" style="font-size:11px; font-weight:900; color:#fbbf24;">
                PKR {{ number_format(max(0, ($booking->total_price + $booking->processing_fee) - $runningTotal)) }}
            </div>
        </div>
    </div>
</td>
    </tr>
</table>

{{-- ── SIGNATURES ── --}}
<table style="margin-top:40px; width:100%;">
    <tr>
        <td width="33%" style="text-align:center;">
            <div style="border-top:1px solid #cbd5e1; width:120px; margin:0 auto; padding-top:5px;">
                <div class="font-bold">{{ $booking->createdBy->name ?? $booking->user->name ?? 'Staff' }}</div>
                <div style="font-size:7px; color:#94a3b8;">Booking Officer</div>
            </div>
        </td>
        <td width="33%" style="text-align:center;">
            <div style="border:1px solid #e2e8f0; width:60px; height:60px; margin:0 auto; border-radius:50%; padding-top:15px; color:#cbd5e1; font-size:7px; text-align:center;">STAMP</div>
        </td>
        <td width="33%" style="text-align:center;">
            <div style="border-top:1px solid #cbd5e1; width:120px; margin:0 auto; padding-top:5px;">
                <div class="font-bold">Customer</div>
                <div style="font-size:7px; color:#94a3b8;">{{ $booking->customer->name }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- ── FOOTER — all from SystemConfig ── --}}
<div class="footer">
    {{ $socName }} &copy; {{ date('Y') }} | System Generated Statement | Printed: {{ date('d-M-Y H:i') }}
    @if($socPhone || $socEmail)
        <br>
        @if($socPhone) {{ $socPhone }} @endif
        @if($socPhone && $socEmail) &nbsp;|&nbsp; @endif
        @if($socEmail) {{ $socEmail }} @endif
    @endif
    @if($socAddress) <br>{{ $socAddress }} @endif
    @if($footerNote) <br>{{ $footerNote }} @endif
</div>

</body>
</html>
