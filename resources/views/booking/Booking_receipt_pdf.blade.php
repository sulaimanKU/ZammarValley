<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ZV-Receipt-{{ $booking->customer_booking_id }}</title>
    <style>
        @page { margin: 0.3in 0.4in; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0; padding: 0; background: #fff;
        }

        .watermark {
            position: fixed; top: 38%; left: 15%;
            font-size: 72px; color: rgba(30,58,138,0.03);
            transform: rotate(-40deg); z-index: -1000;
            font-weight: 900; text-transform: uppercase; letter-spacing: 8px;
        }

        /* ── HEADER ── */
        .header-wrap {
            background: #1e3a8a;
            padding: 16px 20px; margin-bottom: 0;
        }
        .header-wrap table { width: 100%; }
        .brand-name { font-size: 26px; font-weight: 900; color: #fff; letter-spacing: 3px; text-transform: uppercase; margin: 0; }
        .brand-tagline { color: #93c5fd; font-size: 8px; text-transform: uppercase; letter-spacing: 2px; margin-top: 2px; }
        .receipt-label {
            background: rgba(255,255,255,0.15); color: #fff;
            padding: 4px 12px; border-radius: 3px; font-size: 9px;
            font-weight: bold; letter-spacing: 1px; text-transform: uppercase;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .receipt-num { font-size: 14px; font-weight: 900; color: #fbbf24; margin-top: 4px; letter-spacing: 1px; }

        /* ── META BAR ── */
        .meta-bar { background: #f8fafc; border-left: 4px solid #1e3a8a; padding: 8px 14px; margin-bottom: 12px; }
        .meta-bar table { width: 100%; }
        .meta-item-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-item-value { font-size: 11px; font-weight: bold; color: #1e293b; }
        .status-active { background: #dcfce7; color: #15803d; padding: 3px 10px; border-radius: 20px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .status-inactive { background: #fee2e2; color: #dc2626; padding: 3px 10px; border-radius: 20px; font-size: 9px; font-weight: bold; text-transform: uppercase; }

        /* ── SECTION TITLE ── */
        .sec-title { font-size: 9px; font-weight: 900; color: #1e3a8a; text-transform: uppercase; letter-spacing: 1.5px; }
        .sec-bar { width: 3px; height: 14px; background: #1e3a8a; border-radius: 2px; display: inline-block; margin-right: 6px; vertical-align: middle; }

        /* ── CARD BOX ── */
        .card-box { border: 1px solid #e2e8f0; border-radius: 4px; overflow: hidden; margin-bottom: 10px; }

        /* ── INFO GRID ── */
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .info-grid tr:last-child td { border-bottom: none; }
        .lbl { color: #94a3b8; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; width: 15%; font-weight: 600; }
        .val { color: #1e293b; font-size: 10px; font-weight: 700; width: 18%; }

        /* ── INSTALLMENT TABLE ── */
        .inst-table { width: 100%; border-collapse: collapse; }
        .inst-table th { background: #1e3a8a; color: #fff; padding: 5px 8px; font-size: 8px; text-align: center; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .inst-table td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; text-align: center; font-weight: bold; font-size: 10px; }

        /* ── SUMMARY ── */
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 5px 10px; border-bottom: 1px solid #f1f5f9; }
        .summary-table tr:last-child td { border-bottom: none; }
        .summary-lbl { font-size: 9px; color: #64748b; }
        .summary-val { text-align: right; font-weight: bold; font-size: 10px; }
        .summary-total { background: #1e3a8a; color: #fff; }

        /* ── QR BOX ── */
        .qr-wrap { border: 1px solid #dbeafe; border-radius: 4px; padding: 10px 8px; text-align: center; background: #eff6ff; }
        .qr-wrap img { width: 85px; height: 85px; display: block; margin: 0 auto; }
        .qr-label { font-size: 7px; color: #64748b; margin-top: 4px; line-height: 1.4; }
        .qr-ref { font-size: 8px; font-weight: 900; color: #1e3a8a; margin-top: 3px; }

        /* ── SIGNATURES ── */
        .sig-cell { text-align: center; padding: 0 20px; }
        .sig-name { font-size: 9px; font-weight: bold; color: #334155; text-transform: uppercase; }
        .sig-title { font-size: 8px; color: #94a3b8; }

        /* ── FOOTER ── */
        .doc-footer { margin-top: 16px; padding-top: 8px; border-top: 1px dashed #cbd5e1; text-align: center; font-size: 7.5px; color: #94a3b8; line-height: 1.6; }
    </style>
</head>
<body>

<div class="watermark">Zamar Valley</div>

{{-- HEADER --}}
<div class="header-wrap">
    <table>
        <tr>
            <td width="60%">
                <div class="brand-name">Zamar Valley</div>
                <div class="brand-tagline">Excellence in Real Estate Development</div>
                <div style="color:#bfdbfe; font-size:8px; margin-top:4px;">Main G.T. Road, Sector D-18, Islamabad &nbsp;|&nbsp; +92 3XX XXXXXXX</div>
            </td>
            <td width="40%" style="text-align:right;">
                <div class="receipt-label">Booking Receipt</div>
                <div class="receipt-num">{{ $booking->customer_booking_id }}</div>
                <div style="color:#bfdbfe; font-size:8px; margin-top:3px;">{{ date('d F Y', strtotime($booking->booking_date)) }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- META BAR --}}
<div class="meta-bar">
    <table>
        <tr>
            <td width="25%">
                <div class="meta-item-label">Booking Date</div>
                <div class="meta-item-value">{{ date('d-M-Y', strtotime($booking->booking_date)) }}</div>
            </td>
            <td width="25%">
                <div class="meta-item-label">Booking Type</div>
                <div class="meta-item-value">{{ $booking->booking_type ?? 'N/A' }}</div>
            </td>
            <td width="25%">
                <div class="meta-item-label">Payment Method</div>
                <div class="meta-item-value">{{ ucwords(str_replace('_', ' ', $booking->payment_method ?? '')) }}</div>
            </td>
            <td width="25%" style="text-align:right;">
                <span class="{{ $booking->status == 'active' ? 'status-active' : 'status-inactive' }}">
                    {{ strtoupper($booking->status) }}
                </span>
            </td>
        </tr>
    </table>
</div>

{{-- CUSTOMER + QR --}}
<table width="100%" style="margin-bottom:4px;">
    <tr><td colspan="3"><table style="width:100%;"><tr><td style="border-bottom:none; width:3px; padding:0;"><div style="width:3px; height:14px; background:#1e3a8a; border-radius:2px;"></div></td><td style="border-bottom:none; padding-bottom:0;"><span class="sec-title">Customer Information</span></td><td style="border-bottom:1px solid #e2e8f0;"></td></tr></table></td></tr>
    <tr>
        <td width="73%" valign="top" style="padding-top:6px;">
            <div class="card-box">
                <table class="info-grid">
                    <tr>
                        <td class="lbl">Full Name</td><td class="val">{{ $booking->customer->name ?? 'N/A' }}</td>
                        <td class="lbl">Guardian</td><td class="val">{{ $booking->customer->guardian_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">CNIC</td><td class="val">{{ $booking->customer->cnic ?? 'N/A' }}</td>
                        <td class="lbl">Phone</td><td class="val">{{ $booking->customer->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Email</td><td class="val">{{ $booking->customer->email ?? 'N/A' }}</td>
                        <td class="lbl">City</td><td class="val">{{ $booking->customer->city ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Address</td><td class="val" colspan="3">{{ $booking->customer->address ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </td>
        <td width="3%"></td>
        <td width="24%" valign="top" style="padding-top:6px;">
    @if($qrCode)
        <div class="qr-wrap">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="85" height="85">
            <div class="qr-label">Scan to view<br>full payment ledger</div>
            <div class="qr-ref text-uppercase">{{ $booking->customer_booking_id }}</div>
        </div>
    @else
        <div style="font-size: 10px; color: #64748b;">
            Ref: {{ $booking->customer_booking_id }}
        </div>
    @endif
</td>
    </tr>
</table>

{{-- PLOT DETAILS --}}
<table width="100%" style="margin-bottom:4px; margin-top:8px;"><tr><td style="border-bottom:none; width:3px; padding:0;"><div style="width:3px; height:14px; background:#1e3a8a; border-radius:2px;"></div></td><td style="border-bottom:none; padding-bottom:0;"><span class="sec-title">Plot &amp; Project Details</span></td><td style="border-bottom:1px solid #e2e8f0;"></td></tr></table>
<div class="card-box" style="margin-top:6px;">
    <table class="info-grid">
        <tr>
            <td class="lbl">Plot No.</td>
            <td class="val" style="font-size:13px; color:#1e3a8a;">#{{ $booking->plot->plot_number ?? 'N/A' }}</td>
            <td class="lbl">Size</td>
            <td class="val">{{ $booking->plot->size ?? 'N/A' }} {{ $booking->plot->unit ?? '' }}</td>
            <td class="lbl">Category</td>
            <td class="val">{{ $booking->plot->category->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Block</td><td class="val">{{ $booking->plot->block ?? 'N/A' }}</td>
            <td class="lbl">Sector</td><td class="val">{{ $booking->plot->sector ?? 'N/A' }}</td>
            <td class="lbl">Society</td><td class="val">{{ $booking->plot->society ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">City</td><td class="val">{{ $booking->plot->city ?? 'N/A' }}</td>
            <td class="lbl">Street</td><td class="val">{{ $booking->plot->street_number ?? 'N/A' }}</td>
            <td class="lbl">Price Type</td><td class="val">{{ ucfirst($booking->plot->price_type ?? 'N/A') }}</td>
        </tr>
    </table>
</div>

{{-- INSTALLMENT + SUMMARY --}}
<table width="100%" style="margin-top:10px;">
    <tr>
        <td width="62%" valign="top">
            @if($booking->total_installments > 0)
            <table width="100%" style="margin-bottom:4px;"><tr><td style="border-bottom:none; width:3px; padding:0;"><div style="width:3px; height:14px; background:#1e3a8a; border-radius:2px;"></div></td><td style="border-bottom:none; padding-bottom:0;"><span class="sec-title">Installment Plan</span></td><td style="border-bottom:1px solid #e2e8f0;"></td></tr></table>
            <table class="inst-table" style="margin-top:6px;">
                <thead>
                    <tr>
                        <th>Down Payment</th>
                        <th>Token</th>
                        <th>Installments</th>
                        <th>Monthly</th>
                        <th>Fee</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PKR {{ number_format($booking->down_payment) }}</td>
                        <td>PKR {{ number_format($booking->token_amount) }}</td>
                        <td>{{ $booking->total_installments }} Months</td>
                        <td style="color:#1e3a8a;">PKR {{ number_format($booking->monthly_installment) }}</td>
                        <td>PKR {{ number_format($booking->processing_fee ?? 0) }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            @if($booking->remarks)
            <div style="margin-top:8px; padding:6px 10px; background:#fffbeb; border-left:3px solid #f59e0b; border-radius:0 3px 3px 0;">
                <div style="font-size:8px; font-weight:bold; color:#92400e; text-transform:uppercase; margin-bottom:2px;">Remarks</div>
                <div style="font-size:9px; color:#78350f;">{{ $booking->remarks }}</div>
            </div>
            @endif
        </td>
        <td width="3%"></td>
        <td width="35%" valign="top">
            <table width="100%" style="margin-bottom:4px;"><tr><td style="border-bottom:none; width:3px; padding:0;"><div style="width:3px; height:14px; background:#1e3a8a; border-radius:2px;"></div></td><td style="border-bottom:none; padding-bottom:0;"><span class="sec-title">Payment Summary</span></td></tr></table>
            <div class="card-box" style="margin-top:6px;">
                <table class="summary-table">
                    <tr>
                        <td class="summary-lbl">Total Agreed Value</td>
                        <td class="summary-val">PKR {{ number_format($booking->total_price) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-lbl" style="color:#1d4ed8;">Down Payment</td>
                        <td class="summary-val" style="color:#1d4ed8;">- PKR {{ number_format($booking->down_payment) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-lbl" style="color:#15803d;">Token Paid</td>
                        <td class="summary-val" style="color:#15803d;">- PKR {{ number_format($booking->token_amount) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-lbl">Processing Fee</td>
                        <td class="summary-val">PKR {{ number_format($booking->processing_fee ?? 0) }}</td>
                    </tr>
                    <tr class="summary-total">
                        <td style="font-size:10px; font-weight:bold; padding:7px 10px;">Payable Balance</td>
                        <td style="text-align:right; font-size:12px; font-weight:900; padding:7px 10px; color:#fbbf24;">
                            PKR {{ number_format($booking->total_price - $booking->down_payment) }}
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

{{-- SIGNATURES --}}
<table style="margin-top:24px; width:100%;">
    <tr>
        <td width="33%" class="sig-cell">
            <div style="height:35px;"></div>
            <div style="border-top:1px solid #cbd5e1; padding-top:4px;">
                <div class="sig-name">Customer Signature</div>
                <div class="sig-title">{{ $booking->customer->name ?? '' }}</div>
            </div>
        </td>
        <td width="33%" style="text-align:center;">
            <div style="width:70px; height:70px; border:1px solid #cbd5e1; margin:0 auto 4px auto; line-height:35px; color:#cbd5e1; font-size:7px; text-align:center; border-radius:50%; padding-top:12px;">
                OFFICIAL<br>SEAL
            </div>
        </td>
        <td width="33%" class="sig-cell">
            <div style="height:35px;"></div>
            <div style="border-top:1px solid #cbd5e1; padding-top:4px;">
                <div class="sig-name">Authorized Officer</div>
                <div class="sig-title">Zamar Valley Management</div>
            </div>
        </td>
    </tr>
</table>

{{-- FOOTER --}}
<div class="doc-footer">
    This is an official computer-generated receipt of Zamar Valley Real Estate.<br>
    All installments must be paid by the 5th of every month. Late payment charges may apply as per policy.<br>
    <strong style="color:#1e3a8a;">Verification: {{ md5($booking->id . date('Y-m-d')) }}</strong>
    &nbsp;|&nbsp; Printed: {{ date('d-M-Y h:i A') }}
</div>

</body>
</html>
