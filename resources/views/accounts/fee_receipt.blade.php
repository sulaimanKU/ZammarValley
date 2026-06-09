<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt — {{ $payment->receipt_no }}</title>
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

        /* Print / download bar — hidden on print */
        .print-bar {
            width:210mm; background:#1d4ed8; padding:10px 20px; margin:20px 0 0;
            display:flex; align-items:center; justify-content:space-between;
            border-radius:8px 8px 0 0; color:#fff;
        }
        .print-bar span { font-size:13px; font-weight:700; }
        .print-bar button {
            background:#fff; color:#1d4ed8; border:none; border-radius:6px;
            padding:7px 18px; font-size:13px; font-weight:800; cursor:pointer;
        }

        /* A4 page */
        .a4-page {
            width:210mm; height:297mm; background:white;
            box-shadow:0 0 20px rgba(0,0,0,.55);
            margin:0 0 30px; display:flex; flex-direction:column;
            overflow:hidden; border:1.5px solid var(--green-border);
        }

        /* Receipt halves */
        .receipt-half {
            flex:1; display:flex; flex-direction:column;
            padding:10mm 10mm 8mm 10mm; position:relative;
        }
        .receipt-half:first-child { border-bottom:2px dashed var(--green-border); }

        /* Header band — using table for dompdf compat */
        .receipt-header {
            width:100%; border:1.5px solid var(--green-border);
            background:var(--green-soft); border-radius:3px;
            margin-bottom:6mm; overflow:hidden;
        }
        .hdr-table { width:100%; border-collapse:collapse; }
        .hdr-logo-cell {
            width:26mm; background:white; border-right:1.5px solid var(--green-border);
            text-align:center; padding:3mm 2mm; vertical-align:middle;
        }
        .hdr-title-cell { text-align:center; padding:3mm 2mm; vertical-align:middle; }
        .hdr-fees-cell {
            width:36mm; border-left:1px solid var(--green-accent);
            border-right:1px solid var(--green-accent); padding:2mm 3mm; vertical-align:middle;
        }
        .hdr-logo-right-cell {
            width:26mm; background:white; border-left:1.5px solid var(--green-border);
            text-align:center; padding:3mm 2mm; vertical-align:middle;
        }
        .org-name { font-family:'Playfair Display',serif; font-size:20pt; font-weight:900; color:var(--green-dark); line-height:1; }
        .badge { background:var(--green-dark); color:white; font-size:7.5pt; font-weight:700; padding:2px 12px; letter-spacing:2px; display:inline-block; margin-top:4px; }

        /* Fee checkboxes */
        .fee-item { display:block; font-size:7.5pt; font-weight:700; color:var(--green-dark); margin-bottom:3px; }
        .fee-cb { display:inline-block; width:11px; height:11px; border:1.5px solid var(--green-dark); border-radius:50%; vertical-align:middle; margin-right:4px; background:white; text-align:center; line-height:9px; font-size:8pt; font-weight:900; color:var(--green-dark); }
        .fee-cb.filled { background:var(--green-dark); color:white; }
        .file-no { font-size:7pt; font-weight:700; color:var(--green-dark); margin-top:6px; display:block; }
        .file-line { display:inline-block; border-bottom:1px solid var(--green-dark); min-width:20mm; }

        .header-underline { height:2px; background:var(--green-dark); margin:1.5mm 0 2.5mm; border-radius:1px; }

        /* Form table — table layout instead of CSS Grid/Flex for dompdf compat */
        .form-table { width:100%; border-collapse:collapse; }
        .form-table td { vertical-align:top; padding:0; }
        .form-left-col { width:60%; padding-right:8mm; }
        .form-right-col { width:40%; }

        /* Field rows */
        .f-label { font-size:9pt; font-weight:700; color:var(--green-dark); white-space:nowrap; }
        .f-value { font-size:9pt; font-weight:700; color:#111; border-bottom:1.5px solid var(--line-col); display:inline-block; min-width:30mm; padding-bottom:1px; }
        .f-row { margin-top:4mm; display:block; }
        .f-row-inline { margin-top:4mm; }
        .f-row-inline td { vertical-align:bottom; padding-right:3mm; }

        /* Fee amount rows on the right */
        .fee-field { margin-top:4.5mm; display:block; }
        .fee-field .f-value { min-width:25mm; }

        /* Footer */
        .receipt-footer { margin-top:auto; padding-top:3mm; border-top:1px solid var(--green-accent); display:block; }
        .footer-table { width:100%; border-collapse:collapse; }
        .footer-table td { font-size:9pt; font-weight:700; color:var(--green-dark); vertical-align:bottom; }

        @media print {
            body { background:none; margin:0; padding:0; }
            .print-bar { display:none !important; }
            .a4-page { margin:0; box-shadow:none; width:210mm; height:297mm; border:none; }
            * { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            @page { size:A4; margin:0; }
        }
    </style>
</head>
<body>

@php
    $bk       = $payment->booking;
    $customer = $bk->customer;
    $plot     = $bk->plot;
    $feeType  = $payment->bookingFee->fee_type ?? '';

    // Circles filled based on overall booking fee payments (passed from controller)
    // $cbSecurity, $cbDevelopment, $cbRegistry, $cbTransfer are passed in
    $cbDev = $cbDevelopment ?? ($feeType === 'development' ? '●' : '');

    $feeLabels = [
        'security'    => 'Security Fee',
        'development' => 'Development Fee',
        'registry'    => 'Registry Fee',
        'transfer'    => 'Transfer Fee',
    ];
    $feeLabel      = $feeLabels[$feeType] ?? ucfirst($feeType);
    $dateFormatted = \Carbon\Carbon::parse($payment->paid_date)->format('d-m-Y');

    // Security fee month coverage label (computed in controller)
    $monthCoverageLabel = '';
    if ($feeType === 'security' && !empty($securityMonthRange)) {
        $smr = $securityMonthRange;
        if ($smr['completes']) {
            $monthCoverageLabel = $smr['same']
                ? $smr['from_str']
                : $smr['from_str'] . ' – ' . $smr['to_str'];
        } else {
            $monthCoverageLabel = 'Partial – ' . $smr['from_str'];
        }
    }
@endphp

{{-- Print bar --}}
<div class="print-bar">
    <span>{{ $payment->receipt_no }} &nbsp;·&nbsp; {{ $customer->name }} &nbsp;·&nbsp; {{ $feeLabel }}</span>
    <button onclick="window.print()">🖨 Print / Save PDF</button>
</div>

<div class="a4-page">

    {{-- ══════ HALF 1 — Cash 1 ══════ --}}
    <div class="receipt-half">
        <div class="receipt-header">
            <table class="hdr-table">
                <tr>
                    <td class="hdr-logo-cell">
                        <div style="font-size:16pt;color:var(--green-dark);font-weight:900;">ZV</div>
                        <span class="file-no">File #: <span class="file-line">{{ $bk->customer_booking_id }}</span></span>
                    </td>
                    <td class="hdr-title-cell">
                        <div class="org-name">Zamar Valley</div>
                        <span class="badge">Receipt Cash 1</span>
                    </td>
                    <td class="hdr-fees-cell">
                        <span class="fee-item"><span class="fee-cb {{ $cbSecurity    ? 'filled' : '' }}">{{ $cbSecurity    ? '●' : '' }}</span> Security Fee</span>
                        <span class="fee-item"><span class="fee-cb {{ $cbTransfer    ? 'filled' : '' }}">{{ $cbTransfer    ? '●' : '' }}</span> Transfer Fee</span>
                        <span class="fee-item"><span class="fee-cb {{ $cbRegistry    ? 'filled' : '' }}">{{ $cbRegistry    ? '●' : '' }}</span> Registry Fee</span>
                        <span class="fee-item"><span class="fee-cb {{ $cbDev         ? 'filled' : '' }}">{{ $cbDev         ? '●' : '' }}</span> Development Fee</span>
                    </td>
                    <td class="hdr-logo-right-cell">
                        <div style="font-size:16pt;color:var(--green-dark);font-weight:900;">ZV</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-underline"></div>

        <table class="form-table">
            <tr>
                {{-- LEFT column --}}
                <td class="form-left-col">

                    <div class="f-row">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="width:50%;padding-right:3mm;vertical-align:bottom;">
                                    <span class="f-label">Name: </span>
                                    <span class="f-value">{{ $customer->name }}</span>
                                </td>
                                <td style="vertical-align:bottom;">
                                    <span class="f-label">S/O, W/O, D/O: </span>
                                    <span class="f-value">{{ $customer->guardian_name ?? '' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="f-row">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="width:33%;padding-right:2mm;vertical-align:bottom;">
                                    <span class="f-label">Plot No: </span>
                                    <span class="f-value">{{ $plot->plot_number ?? '' }}</span>
                                </td>
                                <td style="width:33%;padding-right:2mm;vertical-align:bottom;">
                                    <span class="f-label">Street: </span>
                                    <span class="f-value">{{ $plot->street_number ?? '' }}</span>
                                </td>
                                <td style="vertical-align:bottom;">
                                    <span class="f-label">Block: </span>
                                    <span class="f-value">{{ $plot->block ?? '' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="f-row">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="width:33%;padding-right:2mm;vertical-align:bottom;">
                                    <span class="f-label">Plot Size: </span>
                                    <span class="f-value">{{ $plot->size ?? '' }} {{ $plot->unit ?? '' }}</span>
                                </td>
                                <td style="width:33%;padding-right:2mm;vertical-align:bottom;">
                                    <span class="f-label">Street Size: </span>
                                    <span class="f-value">{{ $plot->street_size ? $plot->street_size.' ft' : '' }}</span>
                                </td>
                                <td style="vertical-align:bottom;">
                                    <span class="f-label">Date: </span>
                                    <span class="f-value">{{ $dateFormatted }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="margin-top:auto;padding-top:12mm;">
                        <span class="f-label">Account Signature: </span>
                        <span class="f-value" style="min-width:45mm;">&nbsp;</span>
                    </div>

                </td>

                {{-- RIGHT column — fee amounts --}}
                <td class="form-right-col" style="padding-top:2mm;">
                    <span class="fee-field">
                        <span class="f-label">Security Fee: </span>
                        <span class="f-value">{{ $feeType === 'security' ? 'PKR '.number_format($payment->amount) : '' }}</span>
                    </span>
                    <span class="fee-field">
                        <span class="f-label">Transfer Fee: </span>
                        <span class="f-value">{{ $feeType === 'transfer' ? 'PKR '.number_format($payment->amount) : '' }}</span>
                    </span>
                    <span class="fee-field">
                        <span class="f-label">Registry Fee: </span>
                        <span class="f-value">{{ $feeType === 'registry' ? 'PKR '.number_format($payment->amount) : '' }}</span>
                    </span>
                    <span class="fee-field">
                        <span class="f-label">Development Fee: </span>
                        <span class="f-value">{{ $feeType === 'development' ? 'PKR '.number_format($payment->amount) : '' }}</span>
                    </span>
                    <span class="fee-field" style="margin-top:3mm;display:block;">
                        <span class="f-label" style="color:#2d7d32;font-style:italic;">Mode: </span>
                        <span class="f-value">{{ ucwords(str_replace('_',' ',$payment->payment_mode ?? 'cash')) }}</span>
                    </span>
                    <span class="fee-field" style="display:block;margin-top:3mm;">
                        <span class="f-label" style="color:#2d7d32;font-style:italic;">Receipt #: </span>
                        <span class="f-value">{{ $payment->receipt_no }}</span>
                    </span>
                    @if($monthCoverageLabel)
                    <span class="fee-field" style="display:block;margin-top:3mm;">
                        <span class="f-label" style="color:#2d7d32;font-style:italic;">Month Covered: </span>
                        <span class="f-value" style="font-size:8.5pt;">{{ $monthCoverageLabel }}</span>
                    </span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ══════ HALF 2 — Cash 2 ══════ --}}
    <div class="receipt-half">
        <div class="receipt-header">
            <table class="hdr-table">
                <tr>
                    <td class="hdr-logo-cell">
                        <div style="font-size:16pt;color:var(--green-dark);font-weight:900;">ZV</div>
                        <span class="file-no">File #: <span class="file-line">{{ $bk->customer_booking_id }}</span></span>
                    </td>
                    <td class="hdr-title-cell">
                        <div class="org-name">Zamar Valley</div>
                        <span class="badge">Receipt Cash 2</span>
                    </td>
                    <td class="hdr-fees-cell">
                        <span class="fee-item"><span class="fee-cb {{ $cbSecurity    ? 'filled' : '' }}">{{ $cbSecurity    ? '●' : '' }}</span> Security Fee</span>
                        <span class="fee-item"><span class="fee-cb {{ $cbTransfer    ? 'filled' : '' }}">{{ $cbTransfer    ? '●' : '' }}</span> Transfer Fee</span>
                        <span class="fee-item"><span class="fee-cb {{ $cbRegistry    ? 'filled' : '' }}">{{ $cbRegistry    ? '●' : '' }}</span> Registry Fee</span>
                        <span class="fee-item"><span class="fee-cb {{ $cbDev         ? 'filled' : '' }}">{{ $cbDev         ? '●' : '' }}</span> Development Fee</span>
                    </td>
                    <td class="hdr-logo-right-cell">
                        <div style="font-size:16pt;color:var(--green-dark);font-weight:900;">ZV</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-underline"></div>

        <table class="form-table">
            <tr>
                <td class="form-left-col">

                    <div class="f-row">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="width:50%;padding-right:3mm;vertical-align:bottom;">
                                    <span class="f-label">Name: </span>
                                    <span class="f-value">{{ $customer->name }}</span>
                                </td>
                                <td style="vertical-align:bottom;">
                                    <span class="f-label">S/O, W/O, D/O: </span>
                                    <span class="f-value">{{ $customer->guardian_name ?? '' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="f-row">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="width:33%;padding-right:2mm;vertical-align:bottom;">
                                    <span class="f-label">Plot No: </span>
                                    <span class="f-value">{{ $plot->plot_number ?? '' }}</span>
                                </td>
                                <td style="width:33%;padding-right:2mm;vertical-align:bottom;">
                                    <span class="f-label">Street: </span>
                                    <span class="f-value">{{ $plot->street_number ?? '' }}</span>
                                </td>
                                <td style="vertical-align:bottom;">
                                    <span class="f-label">Block: </span>
                                    <span class="f-value">{{ $plot->block ?? '' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="f-row">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="width:33%;padding-right:2mm;vertical-align:bottom;">
                                    <span class="f-label">Plot Size: </span>
                                    <span class="f-value">{{ $plot->size ?? '' }} {{ $plot->unit ?? '' }}</span>
                                </td>
                                <td style="width:33%;padding-right:2mm;vertical-align:bottom;">
                                    <span class="f-label">Street Size: </span>
                                    <span class="f-value">{{ $plot->street_size ? $plot->street_size.' ft' : '' }}</span>
                                </td>
                                <td style="vertical-align:bottom;">
                                    <span class="f-label">Date: </span>
                                    <span class="f-value">{{ $dateFormatted }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="margin-top:auto;padding-top:12mm;">
                        <span class="f-label">Account Signature: </span>
                        <span class="f-value" style="min-width:45mm;">&nbsp;</span>
                    </div>

                </td>

                <td class="form-right-col" style="padding-top:2mm;">
                    <span class="fee-field">
                        <span class="f-label">Security Fee: </span>
                        <span class="f-value">{{ $feeType === 'security' ? 'PKR '.number_format($payment->amount) : '' }}</span>
                    </span>
                    <span class="fee-field">
                        <span class="f-label">Transfer Fee: </span>
                        <span class="f-value">{{ $feeType === 'transfer' ? 'PKR '.number_format($payment->amount) : '' }}</span>
                    </span>
                    <span class="fee-field">
                        <span class="f-label">Registry Fee: </span>
                        <span class="f-value">{{ $feeType === 'registry' ? 'PKR '.number_format($payment->amount) : '' }}</span>
                    </span>
                    <span class="fee-field">
                        <span class="f-label">Development Fee: </span>
                        <span class="f-value">{{ $feeType === 'development' ? 'PKR '.number_format($payment->amount) : '' }}</span>
                    </span>
                    <span class="fee-field" style="margin-top:3mm;display:block;">
                        <span class="f-label" style="color:#2d7d32;font-style:italic;">Mode: </span>
                        <span class="f-value">{{ ucwords(str_replace('_',' ',$payment->payment_mode ?? 'cash')) }}</span>
                    </span>
                    <span class="fee-field" style="display:block;margin-top:3mm;">
                        <span class="f-label" style="color:#2d7d32;font-style:italic;">Receipt #: </span>
                        <span class="f-value">{{ $payment->receipt_no }}</span>
                    </span>
                    @if($monthCoverageLabel)
                    <span class="fee-field" style="display:block;margin-top:3mm;">
                        <span class="f-label" style="color:#2d7d32;font-style:italic;">Month Covered: </span>
                        <span class="f-value" style="font-size:8.5pt;">{{ $monthCoverageLabel }}</span>
                    </span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

</div>

<script>
// Auto-open print dialog after a short delay
window.addEventListener('load', function() {
    setTimeout(function() {
        // Don't auto-print — let user click the button
    }, 500);
});
</script>

</body>
</html>
