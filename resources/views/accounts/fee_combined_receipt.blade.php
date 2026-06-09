<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt — {{ $booking->customer_booking_id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-dark:   #1b5e20;
            --green-mid:    #2d7d32;
            --green-light:  #43a047;
            --green-soft:   #e8f5e9;
            --green-border: #388e3c;
            --green-accent: #81c784;
            --text-dark:    #111;
            --line-col:     #2d7d32;
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body { background:#525659; font-family:Arial,sans-serif; display:flex; flex-direction:column; align-items:center; min-height:100vh; }

        /* Print bar */
        .print-bar {
            width:210mm; background:#1d4ed8; padding:10px 20px; margin:20px 0 0;
            display:flex; align-items:center; justify-content:space-between;
            border-radius:8px 8px 0 0; color:#fff;
        }
        .print-bar span { font-size:13px; font-weight:700; }
        .print-bar button { background:#fff; color:#1d4ed8; border:none; border-radius:6px; padding:7px 18px; font-size:13px; font-weight:800; cursor:pointer; }

        /* A4 page */
        .a4-page { width:210mm; background:white; box-shadow:0 0 20px rgba(0,0,0,.55); margin:0 0 30px; border:1.5px solid var(--green-border); }

        /* Receipt half */
        .receipt-half { padding:9mm 10mm 8mm 10mm; position:relative; }
        .receipt-half.half-1 { border-bottom:2px dashed var(--green-border); }

        /* Header band */
        .receipt-header { width:100%; border:1.5px solid var(--green-border); background:var(--green-soft); border-radius:3px; margin-bottom:5mm; overflow:hidden; }
        .hdr-table { width:100%; border-collapse:collapse; }
        .hdr-logo-cell { width:26mm; background:white; border-right:1.5px solid var(--green-border); text-align:center; padding:3mm 2mm; vertical-align:middle; }
        .hdr-title-cell { text-align:center; padding:3mm 2mm; vertical-align:middle; }
        .hdr-fees-cell { width:38mm; border-left:1px solid var(--green-accent); border-right:1px solid var(--green-accent); padding:2mm 3mm; vertical-align:middle; }
        .hdr-logo-right-cell { width:26mm; background:white; border-left:1.5px solid var(--green-border); text-align:center; padding:3mm 2mm; vertical-align:middle; }
        .org-name { font-family:'Playfair Display',serif; font-size:19pt; font-weight:900; color:var(--green-dark); line-height:1; }
        .badge { background:var(--green-dark); color:white; font-size:7pt; font-weight:700; padding:2px 10px; letter-spacing:2px; display:inline-block; margin-top:4px; }

        /* Fee circles */
        .fee-item { display:block; font-size:7.5pt; font-weight:700; color:var(--green-dark); margin-bottom:3px; }
        .fee-cb { display:inline-block; width:11px; height:11px; border:1.5px solid var(--green-dark); border-radius:50%; vertical-align:middle; margin-right:4px; background:white; text-align:center; line-height:9px; font-size:8pt; font-weight:900; color:var(--green-dark); }
        .fee-cb.filled { background:var(--green-dark); color:white; }
        .file-no { font-size:7pt; font-weight:700; color:var(--green-dark); margin-top:6px; display:block; }
        .file-line { display:inline-block; border-bottom:1px solid var(--green-dark); min-width:18mm; }

        .header-underline { height:2px; background:var(--green-dark); margin:1.5mm 0 3mm; border-radius:1px; }

        /* Customer info */
        .info-table { width:100%; border-collapse:collapse; }
        .info-table td { vertical-align:bottom; padding:0; font-size:8.5pt; }
        .f-label { font-weight:700; color:var(--green-dark); white-space:nowrap; }
        .f-value { font-weight:700; color:#111; border-bottom:1.5px solid var(--line-col); display:inline-block; min-width:26mm; padding-bottom:1px; }
        .f-row { margin-top:3.5mm; display:block; }

        /* Fee summary table (right side) */
        .fee-sum-table { width:100%; border-collapse:collapse; }
        .fee-sum-table td { font-size:8.5pt; vertical-align:middle; padding:2px 0; }
        .fee-sum-lbl { font-weight:700; color:var(--green-dark); width:38mm; }
        .fee-sum-val { font-weight:800; color:#111; border-bottom:1px solid var(--green-accent); min-width:28mm; display:inline-block; padding-bottom:1px; }
        .fee-sum-val.paid { color:#111; }
        .fee-sum-val.empty { color:#bdbdbd; }
        .fee-sum-row { margin-top:3.5mm; display:block; }

        /* Payment history mini-table */
        .hist-section { margin-top:4mm; }
        .hist-title { font-size:7.5pt; font-weight:800; color:var(--green-dark); letter-spacing:.5px; text-transform:uppercase; border-bottom:1px solid var(--green-accent); padding-bottom:2px; margin-bottom:3px; }
        .hist-table { width:100%; border-collapse:collapse; }
        .hist-table th { font-size:6.5pt; font-weight:700; color:var(--green-mid); text-transform:uppercase; letter-spacing:.4px; padding:1.5px 3px; text-align:left; border-bottom:1px solid var(--green-accent); background:var(--green-soft); }
        .hist-table td { font-size:7.5pt; padding:2px 3px; border-bottom:1px solid #e8f5e9; color:#111; vertical-align:middle; }
        .hist-table tr:last-child td { border-bottom:none; }
        .hist-grand { font-size:8.5pt; font-weight:800; color:var(--green-dark); text-align:right; margin-top:2.5mm; }
        .hist-empty { font-size:7.5pt; color:#94a3b8; font-style:italic; padding:3px 0; }

        /* Grand total bar */
        .total-bar { margin-top:4mm; background:var(--green-soft); border:1.5px solid var(--green-border); border-radius:3px; padding:3mm 5mm; display:flex; justify-content:space-between; align-items:center; }
        .total-bar-lbl { font-size:9pt; font-weight:800; color:var(--green-dark); }
        .total-bar-val { font-size:11pt; font-weight:900; color:var(--green-dark); }

        /* Footer */
        .receipt-footer { margin-top:4mm; padding-top:2.5mm; border-top:1px solid var(--green-accent); }
        .footer-table { width:100%; border-collapse:collapse; }
        .footer-table td { font-size:8.5pt; font-weight:700; color:var(--green-dark); vertical-align:bottom; }

        @media print {
            body { background:none; margin:0; padding:0; }
            .print-bar { display:none !important; }
            .a4-page { margin:0; box-shadow:none; width:210mm; border:none; }
            * { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            @page { size:A4; margin:0; }
        }
    </style>
</head>
<body>

@php
    $customer = $booking->customer;
    $plot     = $booking->plot;
    $feeLabels = [
        'security'    => 'Security Fee',
        'development' => 'Development Fee',
        'registry'    => 'Registry Fee',
        'transfer'    => 'Transfer Fee',
    ];
    $feeColors = [
        'security'    => '#7c3aed',
        'development' => '#0369a1',
        'registry'    => '#b45309',
        'transfer'    => '#0f766e',
    ];

    // Build all payment rows across all fee types for the history table
    $allPaymentRows = [];
    foreach ($feeSummary as $type => $data) {
        foreach ($data['payments'] as $p) {
            $allPaymentRows[] = [
                'date'     => \Carbon\Carbon::parse($p->paid_date)->format('d-m-Y'),
                'type'     => $feeLabels[$type] ?? ucfirst($type),
                'amount'   => (float)$p->amount,
                'mode'     => ucwords(str_replace('_',' ',$p->payment_mode ?? 'cash')),
                'receipt'  => $p->receipt_no ?? '—',
                'coverage' => $data['secMonthInfo'][$p->id] ?? '',
            ];
        }
    }
    // Sort by date
    usort($allPaymentRows, fn($a,$b) => strcmp($a['date'],$b['date']));

    $feeOrderLabels = ['security','development','registry','transfer'];
@endphp

{{-- Print bar --}}
<div class="print-bar">
    <span>Combined Fee Receipt &nbsp;·&nbsp; {{ $customer->name }} &nbsp;·&nbsp; {{ $booking->customer_booking_id }}</span>
    <button onclick="window.print()">🖨 Print / Save PDF</button>
</div>

<div class="a4-page">

    {{-- ══════ HALF 1 — Cash 1 ══════ --}}
    <div class="receipt-half half-1">

        {{-- Header --}}
        <div class="receipt-header">
            <table class="hdr-table">
                <tr>
                    <td class="hdr-logo-cell">
                        <div style="font-size:16pt;color:var(--green-dark);font-weight:900;">ZV</div>
                        <span class="file-no">File #: <span class="file-line">{{ $booking->customer_booking_id }}</span></span>
                    </td>
                    <td class="hdr-title-cell">
                        <div class="org-name">Zamar Valley</div>
                        <span class="badge">Fee Receipt — Cash 1</span>
                    </td>
                    <td class="hdr-fees-cell">
                        <span class="fee-item">
                            <span class="fee-cb {{ $cbSecurity    ? 'filled' : '' }}">{{ $cbSecurity    ? '●' : '' }}</span> Security Fee
                        </span>
                        <span class="fee-item">
                            <span class="fee-cb {{ $cbTransfer    ? 'filled' : '' }}">{{ $cbTransfer    ? '●' : '' }}</span> Transfer Fee
                        </span>
                        <span class="fee-item">
                            <span class="fee-cb {{ $cbRegistry    ? 'filled' : '' }}">{{ $cbRegistry    ? '●' : '' }}</span> Registry Fee
                        </span>
                        <span class="fee-item">
                            <span class="fee-cb {{ $cbDevelopment ? 'filled' : '' }}">{{ $cbDevelopment ? '●' : '' }}</span> Development Fee
                        </span>
                    </td>
                    <td class="hdr-logo-right-cell">
                        <div style="font-size:16pt;color:var(--green-dark);font-weight:900;">ZV</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-underline"></div>

        {{-- Customer info + fee summary side-by-side --}}
        <table style="width:100%;border-collapse:collapse;">
            <tr>
                {{-- LEFT: Customer / Plot Info --}}
                <td style="width:57%;padding-right:6mm;vertical-align:top;">

                    <div class="f-row">
                        <table class="info-table"><tr>
                            <td style="width:50%;padding-right:2mm;">
                                <span class="f-label">Name: </span><span class="f-value">{{ $customer->name }}</span>
                            </td>
                            <td>
                                <span class="f-label">S/O, W/O, D/O: </span><span class="f-value">{{ $customer->guardian_name ?? '' }}</span>
                            </td>
                        </tr></table>
                    </div>

                    <div class="f-row">
                        <table class="info-table"><tr>
                            <td style="width:25%;padding-right:2mm;">
                                <span class="f-label">Plot No: </span><span class="f-value">{{ $plot->plot_number ?? '' }}</span>
                            </td>
                            <td style="width:25%;padding-right:2mm;">
                                <span class="f-label">Street: </span><span class="f-value">{{ $plot->street_number ?? '' }}</span>
                            </td>
                            <td style="width:25%;padding-right:2mm;">
                                <span class="f-label">Block: </span><span class="f-value">{{ $plot->block ?? '' }}</span>
                            </td>
                            <td>
                                <span class="f-label">Size: </span><span class="f-value">{{ $plot->size ?? '' }} {{ $plot->unit ?? '' }}{{ $plot->street_size ? ' / '.$plot->street_size.' ft' : '' }}</span>
                            </td>
                        </tr></table>
                    </div>

                    <div class="f-row">
                        <table class="info-table"><tr>
                            <td style="width:50%;padding-right:2mm;">
                                <span class="f-label">Booking Date: </span><span class="f-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</span>
                            </td>
                            <td>
                                <span class="f-label">Receipt Date: </span><span class="f-value">{{ $receiptDate }}</span>
                            </td>
                        </tr></table>
                    </div>

                    <div class="f-row">
                        <span class="f-label" style="font-size: 8pt;">CNIC: </span><span class="f-value" style="font-size: 8pt; min-width:38mm;">{{ $customer->cnic ?? '' }}</span>
                    </div>

                </td>

                {{-- RIGHT: Fee Amounts --}}
                <td style="width:43%;vertical-align:top;padding-top:1mm;">
                    @foreach($feeOrderLabels as $type)
                        @if(isset($feeSummary[$type]))
                        @php $data = $feeSummary[$type]; @endphp
                        <div class="fee-sum-row">
                            <span class="f-label" style="font-size:8pt;">{{ $feeLabels[$type] }}: </span>
                            @if($data['hasPaid'])
                                <span class="fee-sum-val paid" style="font-size:8.5pt; font-weight:700;">PKR {{ number_format($data['totalPaid']) }}</span>
                                @if($data['isSettled'])
                                    <span style="font-size:6.5pt;color:var(--green-mid);font-weight:800;"> ✓</span>
                                @endif
                            @else
                                <span class="fee-sum-val empty">—</span>
                            @endif
                        </div>
                        @endif
                    @endforeach
                    <div style="margin-top:4mm;border-top:1.5px solid var(--green-dark);padding-top:2mm;">
                        <span class="f-label" style="font-size:9pt;">Total Paid: </span>
                        <span style="font-size:9pt;font-weight:800;color:#111;">PKR {{ number_format($grandTotal) }}</span>
                    </div>
                    <div style="margin-top:3mm;">
                        <span class="f-label" style="font-size:9pt;">Receipt Ref: </span>
                        <span style="font-size:9pt;font-weight:600;color:#111;">{{ $receiptNo }}</span>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Payment History Table --}}
        <div class="hist-section">
            <div class="hist-title">Payment History — All Fees</div>
            @if(count($allPaymentRows) > 0)
            <table class="hist-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Fee Type</th>
                        <th>Amount (PKR)</th>
                        <th>Mode</th>
                        <th>Coverage / Receipt #</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allPaymentRows as $row)
                    <tr>
                        <td>{{ $row['date'] }}</td>
                        <td style="font-weight:700;">{{ $row['type'] }}</td>
                        <td style="font-weight:800;">{{ number_format($row['amount']) }}</td>
                        <td>{{ $row['mode'] }}</td>
                        <td style="font-size:7pt;">{{ $row['coverage'] ?: $row['receipt'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="hist-grand">Grand Total Paid: PKR {{ number_format($grandTotal) }}</div>
            @else
            <div class="hist-empty">No fee payments recorded yet.</div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="receipt-footer">
            <table class="footer-table">
                <tr>
                    <td style="width:33%;">
                        Receiver Signature:<br>
                        <span style="display:inline-block;width:50mm;border-bottom:1.5px solid var(--green-border);margin-top:8mm;">&nbsp;</span>
                    </td>
                    <td style="width:33%;text-align:left;">
                        Stamp<span style="display:inline-block;width:50mm;border-bottom:1.5px solid var(--green-border);margin-top:8mm;">&nbsp;</span>
                    </td>
                    <td style="width:34%;text-align:right;">
                        Applicant Signature:<br>
                        <span style="display:inline-block;text-align:right; width:40mm;border-bottom:1.5px solid var(--green-border);margin-top:8mm;">&nbsp;</span>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    {{-- ══════ HALF 2 — Cash 2 (Customer Copy) ══════ --}}
    <div class="receipt-half">

        <div class="receipt-header">
            <table class="hdr-table">
                <tr>
                    <td class="hdr-logo-cell">
                        <div style="font-size:16pt;color:var(--green-dark);font-weight:900;">ZV</div>
                        <span class="file-no">File #: <span class="file-line">{{ $booking->customer_booking_id }}</span></span>
                    </td>
                    <td class="hdr-title-cell">
                        <div class="org-name">Zamar Valley</div>
                        <span class="badge">Fee Receipt — Cash 2</span>
                    </td>
                    <td class="hdr-fees-cell">
                        <span class="fee-item">
                            <span class="fee-cb {{ $cbSecurity    ? 'filled' : '' }}">{{ $cbSecurity    ? '●' : '' }}</span> Security Fee
                        </span>
                        <span class="fee-item">
                            <span class="fee-cb {{ $cbTransfer    ? 'filled' : '' }}">{{ $cbTransfer    ? '●' : '' }}</span> Transfer Fee
                        </span>
                        <span class="fee-item">
                            <span class="fee-cb {{ $cbRegistry    ? 'filled' : '' }}">{{ $cbRegistry    ? '●' : '' }}</span> Registry Fee
                        </span>
                        <span class="fee-item">
                            <span class="fee-cb {{ $cbDevelopment ? 'filled' : '' }}">{{ $cbDevelopment ? '●' : '' }}</span> Development Fee
                        </span>
                    </td>
                    <td class="hdr-logo-right-cell">
                        <div style="font-size:16pt;color:var(--green-dark);font-weight:900;">ZV</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-underline"></div>

        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <td style="width:57%;padding-right:6mm;vertical-align:top;">

                    <div class="f-row">
                        <table class="info-table"><tr>
                            <td style="width:50%;padding-right:2mm;">
                                <span class="f-label">Name: </span><span class="f-value">{{ $customer->name }}</span>
                            </td>
                            <td>
                                <span class="f-label">S/O, W/O, D/O: </span><span class="f-value">{{ $customer->guardian_name ?? '' }}</span>
                            </td>
                        </tr></table>
                    </div>

                    <div class="f-row">
                        <table class="info-table"><tr>
                            <td style="width:25%;padding-right:2mm;">
                                <span class="f-label">Plot No: </span><span class="f-value">{{ $plot->plot_number ?? '' }}</span>
                            </td>
                            <td style="width:25%;padding-right:2mm;">
                                <span class="f-label">Street: </span><span class="f-value">{{ $plot->street_number ?? '' }}</span>
                            </td>
                            <td style="width:25%;padding-right:2mm;">
                                <span class="f-label">Block: </span><span class="f-value">{{ $plot->block ?? '' }}</span>
                            </td>
                            <td>
                                <span class="f-label">Size: </span><span class="f-value">{{ $plot->size ?? '' }} {{ $plot->unit ?? '' }}{{ $plot->street_size ? ' / '.$plot->street_size.' ft' : '' }}</span>
                            </td>
                        </tr></table>
                    </div>

                    <div class="f-row">
                        <table class="info-table"><tr>
                            <td style="width:50%;padding-right:2mm;">
                                <span class="f-label">Booking Date: </span><span class="f-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</span>
                            </td>
                            <td>
                                <span class="f-label">Receipt Date: </span><span class="f-value">{{ $receiptDate }}</span>
                            </td>
                        </tr></table>
                    </div>

                    <div class="f-row">
                        <span class="f-label" style ="font-size: 8pt;">CNIC: </span><span class="f-value" style="font-size: 8pt; min-width:38mm;">{{ $customer->cnic ?? '' }}</span>
                    </div>

                </td>

                <td style="width:43%;vertical-align:top;padding-top:1mm;">
                    @foreach($feeOrderLabels as $type)
                        @if(isset($feeSummary[$type]))
                        @php $data = $feeSummary[$type]; @endphp
                        <div class="fee-sum-row">
                            <span class="f-label" style="font-size:8pt;">{{ $feeLabels[$type] }}: </span>
                            @if($data['hasPaid'])
                                <span class="fee-sum-val paid" style="font-size:8.5pt; font-weight:700;">PKR {{ number_format($data['totalPaid']) }}</span>
                                @if($data['isSettled'])
                                    <span style="font-size:6.5pt;color:var(--green-mid);font-weight:800;"> ✓</span>
                                @endif
                            @else
                                <span class="fee-sum-val empty">—</span>
                            @endif
                        </div>
                        @endif
                    @endforeach
                    <div style="margin-top:4mm;border-top:1.5px solid var(--green-dark);padding-top:2mm;">
                        <span class="f-label" style="font-size:9pt;">Total Paid: </span>
                        <span style="font-size:9pt;font-weight:800;color:#111;">PKR {{ number_format($grandTotal) }}</span>
                    </div>
                    <div style="margin-top:3mm;">
                        <span class="f-label" style="font-size:9pt;">Receipt Ref: </span>
                        <span style="font-size:9pt;font-weight:600;color:#111;">{{ $receiptNo }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="hist-section">
            <div class="hist-title">Payment History — All Fees</div>
            @if(count($allPaymentRows) > 0)
            <table class="hist-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Fee Type</th>
                        <th>Amount (PKR)</th>
                        <th>Mode</th>
                        <th>Coverage / Receipt #</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allPaymentRows as $row)
                    <tr>
                        <td>{{ $row['date'] }}</td>
                        <td style="font-weight:700;">{{ $row['type'] }}</td>
                        <td style="font-weight:800;">{{ number_format($row['amount']) }}</td>
                        <td>{{ $row['mode'] }}</td>
                        <td style="font-size:7pt;">{{ $row['coverage'] ?: $row['receipt'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="hist-grand">Grand Total Paid: PKR {{ number_format($grandTotal) }}</div>
            @else
            <div class="hist-empty">No fee payments recorded yet.</div>
            @endif
        </div>

        <div class="receipt-footer">
            <table class="footer-table">
                <tr>
                    <td style="width:33%;">
                        Receiver Signature:
                        <span style="display:inline-block;width:50mm;border-bottom:1.5px solid var(--green-border);margin-top:8mm;">&nbsp;</span>
                    </td>
                    <td style="width:33%;text-align:center;">
                        Stamp<span style="display:inline-block;width:50mm;border-bottom:1.5px solid var(--green-border);margin-top:8mm;">&nbsp;</span>
                    </td>
                    <td style="width:34%;text-align:right;">
                        Applicant Signature:
                        <span style="display:inline-block;width:35mm;border-bottom:1.5px solid var(--green-border);margin-top:8mm;">&nbsp;</span>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</div>

</body>
</html>
