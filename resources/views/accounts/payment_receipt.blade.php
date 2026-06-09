<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bin Abbasi Associates — Payment Receipt #{{ $payment->id }}</title>
    <style>
        :root {
            --green-soft:   #e8f5e9;
            --accent-gold:  #f9a825;
            --text-dark:    #111;
            --line-col:     #333;
        }

        * {
    transform: none !important;
}

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #525659;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* ══════════════ RECEIPT PAGE — A5 Landscape ══════════════ */
        .receipt-page {
           width: 794px;   /* A5 landscape */
    height: 613px;
             background: linear-gradient(to right, #adadad, #868383);

            box-shadow: 0 0 20px rgba(0,0,0,0.55);
            margin: 24px 0 30px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }
        .receipt-header,
.form-body,
.receipt-footer {
    position: relative !important;
}

        /* ══════════════ HEADER SECTION ══════════════ */
        .receipt-header {
            position: relative;
            width: 100%;
            height: 45mm;
            background: #fff;
            overflow: hidden;
        }

        .header-svg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1;
        }

        .header-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 5mm 8mm;
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 6mm;
        }

        .logo-wrap {
            width: 28mm;
            height: 25mm;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .logo-wrap img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .company-block {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .company-line1 svg {
            color: #ffffff;
            width: 250px;
        }
        .company-line2 {
            font-size: 12pt;
            font-weight: 500;
            padding-left: 25px;
            color: #252525;
            letter-spacing: 7px;
        }

        .receipt-no-box {
            text-align: right;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }
        .rno-label {
            font-size: 7.5pt;
            font-weight: 700;
            color: rgba(255,255,255,0.75);
            letter-spacing: 1px;
        }
        .rno-val {
            font-size: 13pt;
            font-weight: 900;
            color: white;
            border-bottom: 1.5px solid rgba(255,255,255,0.6);
            min-width: 70px;
            text-align: center;
        }

        .banner-container {
            margin-top: auto;
            display: flex;
            justify-content: center;
            padding-bottom: 2mm;
        }
        .modern-banner {
            background: white;
            color: #333;
            padding: 6px 40px;
            font-size: 12pt;
            font-weight: 900;
            letter-spacing: 5px;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            border: 2px solid #333;
            text-transform: uppercase;
        }

        /* ══════════════ FORM BODY ══════════════ */
        .form-body {
            flex: 1;
            padding: 3mm 6mm 3mm 6mm;
            display: flex;
            flex-direction: column;
            gap: 0;
            background: white;
        }

        .date-row {
            display: flex;
            justify-content: flex-start;
            align-items: flex-end;
            padding: 2.5mm 0 2mm;
            font-size: 9pt;
            font-weight: 700;
            color: var(--text-dark);
            border-bottom: 1px solid #f0ebeb;
        }
        .date-field {
            display: flex;
            align-items: flex-end;
            gap: 4px;
        }

        .fval {
            border-bottom: 1.5px solid #333;
            height: 15px;
            display: inline-block;
            min-width: 90px;
            font-weight: 400;
            padding: 0 3px;
        }

        .f-row {
            display: flex;
            align-items: flex-end;
            padding: 3mm 0 1.5mm;
            font-size: 9pt;
            font-weight: 700;
            color: var(--text-dark);
            border-bottom: 1px solid #f0ebeb;
            gap: 6px;
            flex-wrap: nowrap;
        }
        .f-label { white-space: nowrap; flex-shrink: 0; }
        .f-grow {
            flex: 1;
            border-bottom: 1.5px solid var(--line-col);
            height: 15px;
            min-width: 40px;
            font-weight: 400;
            padding: 0 3px;
            font-size: 9pt;
        }

        .f-multi {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            padding: 3mm 0 1.5mm;
            font-size: 9pt;
            font-weight: 700;
            color: var(--text-dark);
            border-bottom: 1px solid #d8dad8;
        }
        .f-piece {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            white-space: nowrap;
        }
        .f-piece .f-grow { min-width: 50px; }

        .cb-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 13px; height: 13px;
            border: 1.5px solid #333;
            background: white;
            flex-shrink: 0;
            margin-bottom: 1px;
        }
        .cb-box.checked::after {
            content: '✓';
            font-size: 8pt;
            color: #1e6b24;
            font-weight: 900;
        }

        .qr-code-inline {
            width: 75px;
            height: 75px;
            border: 1px solid #000;
            padding: 2px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .qr-code-inline img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .f-row.qr {
            display: flex;
            justify-content: flex-end;
            border: none;
            padding-top: 2mm;
        }

        /* ══════════════ FOOTER ══════════════ */
       .receipt-footer {
    padding: 4mm 6mm 2mm; /* Added more top padding for signing space */
    background-color: #afafaf;
    border-top: 2px solid #333;
}
       .sig-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding: 2mm 0 4mm;
}
     .sig-item {
    display: flex;
    flex-direction: column; /* Stacks line on top of text */
    align-items: center;    /* Centers text under the line */
    flex: 1;
}
       .sig-line {
    width: 100%;
    border-bottom: 1.2px dotted #333; /* Dotted as per your original design */
    height: 10mm; /* This creates the physical space for the pen signature */
    margin-bottom: 4px;
}

.sig-label {
    font-size: 8pt;
    font-weight: 700;
    color: var(--text-dark);
    text-align: center;
    white-space: nowrap;
}

        .note-row {
            padding: 2mm 0 2.5mm;
            font-size: 7.5pt;
            color: #333;
            line-height: 1.5;
            border-top: 1px solid #9b9c9b;
        }
        .note-row strong { color: #1e6b24; }


        /* ══════════════ PRINT ══════════════ */
       @media print {
    body { background: white; }
    .no-print { display: none !important; }
    .receipt-page {
        margin: 0 !important;
        box-shadow: none !important;
        border: none !important;
    }
}
    </style>
</head>
<body>

     <div class="no-print" style="margin:20px; display: flex; gap: 10px;">
    <button onclick="downloadPDF()" style="padding:10px 20px; background:#16a34a; color:#fff; border:none; border-radius:5px; cursor:pointer;">
        Download PDF
    </button>

    <button onclick="window.print()" style="padding:10px 20px; background:#2563eb; color:#fff; border:none; border-radius:5px; cursor:pointer;">
        Print Receipt
    </button>
</div>

{{-- ════ Compute derived values ════ --}}
@php
    $booking      = $payment->booking;
    $customer     = $booking->customer;
    $plot         = $booking->plot;

    // 1. Calculate Total Received (all approved payments)
    $totalReceived = $booking->payments
                        ->whereIn('status', ['approved', 'paid', 'confirmed'])
                        ->sum('amount_paid');

    // 2. Calculate Balance
    $totalPlotPrice = $booking->total_price ?? $plot->base_price ?? 0;
    $balance = $totalPlotPrice - $totalReceived;

    // 3. Payment method flags
    $type          = strtolower($payment->payment_type ?? '');
    $category      = strtolower($payment->payment_category ?? '');
    $isCash        = ($type === 'cash');
    $isRefRequired = ($type !== 'cash');

    // 4. Installment label
    if (str_contains($category, 'down')) {
        $currentInstallment = "Down Payment";
    } elseif (str_contains($category, 'reg')) {
        $currentInstallment = "Registration Fee";
    } elseif (str_contains($payment->remarks ?? '', 'Settlement discount')) {
        $currentInstallment = "Settlement Discount (Waived)";
    } elseif (str_contains($payment->remarks ?? '', 'Lump Sum Settlement')) {
        $currentInstallment = "Lump Sum Settlement";
    } else {
        $currentInstallment = $booking->payments
            ->where('id', '<=', $payment->id)
            ->filter(fn($p) => str_contains(strtolower($p->payment_category), 'installment'))
            ->count();
    }

    $totalInstallments = $booking->total_installments ?? 0;

    // 5. Date formatting
    $paymentDate = \Carbon\Carbon::parse($payment->paid_date ?? $payment->created_at)->format('d-m-Y');

    // 6. Discount info — parse from remarks if present
    $isLumpSum       = str_contains($payment->remarks ?? '', 'Lump Sum Settlement');
    $isDiscountEntry = str_contains($payment->remarks ?? '', 'Settlement discount — waived');
    $discountAmt     = 0;
    if ($isLumpSum && preg_match('/Early-payment discount: PKR ([\d,]+(?:\.\d+)?)/', $payment->remarks ?? '', $m)) {
        $discountAmt = (float) str_replace(',', '', $m[1]);
    }
    $originalBalance = $isLumpSum && $discountAmt > 0
        ? ($payment->amount_paid + $discountAmt)
        : null;
@endphp
<!-- ════════ RECEIPT PAGE ════════ -->
<div class="receipt-page" id="receipt-page">

    <!-- ══════════════ HEADER ══════════════ -->
    <div class="receipt-header">
        <svg class="header-svg" viewBox="0 0 794 159" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="header-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#adadad;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#868383;stop-opacity:1" />
                </linearGradient>
            </defs>
            <path d="M0,0 L794,0 L794,110 Q400,160 0,110 Z" fill="url(#header-grad)"/>
            <path d="M0,0 L794,0 L794,30 Q400,50 0,30 Z" fill="#ffffff" opacity="0.05"/>
        </svg>

        <div class="header-content">
            <div class="header-top">
                <!-- Logo -->
                <div class="logo-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="3678 3385 5074 4459" fill-rule="evenodd">
                        <path fill="#3a3a3a" d="M6747,7458 L6663,7453 6569,7446 6571,6729 C6377,6644 6007,6263 5863,6203 L5861,7449 5770,7452 5656,7457 4931,7529 C4417,7616 4133,7677 3678,7844 4065,7831 4511,7764 4917,7737 5635,7689 6804,7696 7525,7739 7927,7763 8371,7834 8752,7844 8152,7642 7973,7572 7255,7503 L7254,5931 C7100,5844 7007,5728 6837,5637 L6840,7457 6747,7458Z" />
                        <path fill="#3a3a3a" d="M5656,7457 L5769,7452 5771,6009 C5853,6050 6510,6623 6663,6710 L6663,7453 6747,7458 6748,5128 C6737,5064 6688,5037 6621,4986 6415,4830 5633,4178 5504,4116 L5504,5781 C5568,5718 5584,5691 5667,5649 L5656,7457Z" />
                        <path fill="#6e6f70" d="M4931,7529 L5657,7456 5570,7439 5572,5868 C5509,5891 4984,6327 4958,6385 4898,6521 4969,7292 4931,7529Z" />
                        <path fill="#c4c6c8" d="M5765,4205 C5865,4260 6048,4421 6153,4503 6217,4553 6283,4605 6346,4655 6434,4725 6478,4782 6571,4817 L6570,4006 C6366,3903 6016,3536 5769,3385 L5765,4205Z"/>
                        <path fill="#ffffff" d="M5770,7453 L5861,7449 5863,6202 C6007,6262 6377,6644 6571,6729 L6569,7446 6663,7453 6663,6709 C6509,6622 5852,6049 5771,6009 L5770,7453Z" />
                    </svg>
                </div>

                <!-- Company name -->
                <div class="company-block">
                    <div class="company-line1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="1315 6231 16695 2511" fill="rgb(27,25,24)" fill-rule="evenodd">
                            <path d="M 11836,6544 L 12332,6582 C 12578,6325 13385,6294 13702,6688 13466,7287 12888,7505 12534,7530 13085,7624 13553,7974 13592,8137 13198,8442 12656,8595 12163,8580 11995,8645 11851,8702 11695,8669 11818,8550 11866,8510 12089,8437 11920,8280 12251,7716 11822,7546 12201,7450 11931,6972 12160,6782 L 11836,6544 Z M 12362,7335 C 12626,7368 12852,7321 13023,7167 13263,7048 13398,6936 13492,6768 13267,6591 13035,6568 12627,6593 12391,6777 12337,7039 12362,7335 Z M 12299,7776 C 12608,7737 13118,7925 13271,8112 12943,8324 12543,8396 12222,8433 12185,8256 12203,7967 12299,7776 Z"/>
                            <path d="M 3905,6404 C 4070,6340 4034,6523 4159,6450 3934,7035 4103,7322 3942,7482 3859,7933 4004,8175 3914,8473 3789,8578 3737,8473 3650,8473 3534,7993 3827,7400 3639,7057 3807,6920 3735,6654 3905,6404 Z"/>
                            <path d="M 4506,8553 C 4460,8599 4356,8532 4356,8476 4515,7953 4685,7288 4741,6538 4859,6483 4996,6495 5115,6574 5229,7212 5493,7724 5886,8185 L 6026,8071 C 6153,7571 6249,7138 6261,6571 6283,6486 6336,6414 6473,6390 6540,6993 6268,8060 6160,8359 6010,8440 5878,8465 5740,8431 L 5015,7197 C 4845,7256 4675,7929 4506,8553 Z"/>
                            <path d="M 13610,8696 C 13887,8686 13843,7810 14233,7871 14536,7941 14793,7819 15040,7805 15150,8170 14938,8452 15013,8649 15103,8676 15257,8648 15285,8563 15381,8406 15434,7032 15250,6788 15009,6788 14966,6685 14855,6390 14630,6512 14505,6630 14394,6588 14353,7177 14009,7444 13891,7727 13775,7832 13701,7815 13732,7961 13740,8157 13575,8465 13610,8696 Z M 15032,7620 C 14733,7674 14420,7753 14199,7620 14266,7429 14425,6932 14737,6808 14926,6803 15000,6973 15074,7046 15102,7307 15123,7498 15032,7620 Z"/>
                            <path d="M 15784,7114 C 15812,6801 16903,6271 17113,6414 17047,6983 15939,6877 16154,7102 15968,7372 16805,7417 17003,8223 16886,8421 16033,8680 15795,8599 15886,8215 16623,8341 16707,8084 16826,7919 15660,7441 15784,7114 Z"/>
                            <path d="M 9794,6544 L 10290,6582 C 10536,6325 11343,6294 11660,6688 11424,7287 10846,7505 10492,7530 11043,7624 11511,7974 11550,8137 11156,8442 10614,8595 10121,8580 9953,8645 9809,8702 9653,8669 9776,8550 9824,8510 10047,8437 9878,8280 10209,7716 9780,7546 10159,7450 9889,6972 10118,6782 L 9794,6544 Z M 10320,7335 C 10584,7368 10810,7321 10981,7167 11221,7048 11356,6936 11450,6768 11225,6591 10993,6568 10585,6593 10349,6777 10295,7039 10320,7335 Z M 10257,7776 C 10566,7737 11076,7925 11229,8112 10901,8324 10501,8396 10180,8433 10143,8256 10161,7967 10257,7776 Z"/>
                            <path d="M 1496,6544 L 1992,6582 C 2238,6325 3045,6294 3362,6688 3126,7287 2548,7505 2194,7530 2745,7624 3213,7974 3252,8137 2858,8442 2316,8595 1823,8580 1655,8645 1511,8702 1355,8669 1478,8550 1526,8510 1749,8437 1580,8280 1911,7716 1482,7546 1861,7450 1591,6972 1820,6782 L 1496,6544 Z M 2022,7335 C 2286,7368 2512,7321 2683,7167 2923,7048 3058,6936 3152,6768 2927,6591 2695,6568 2287,6593 2051,6777 1997,7039 2022,7335 Z M 1959,7776 C 2268,7737 2778,7925 2931,8112 2603,8324 2203,8396 1882,8433 1845,8256 1863,7967 1959,7776 Z"/>
                            <path d="M 7598,8696 C 7875,8686 7831,7810 8221,7871 8524,7941 8781,7819 9028,7805 9138,8170 8926,8452 9001,8649 9091,8676 9245,8648 9273,8563 9369,8406 9422,7032 9238,6788 8997,6788 8954,6685 8843,6390 8618,6512 8493,6630 8382,6588 8341,7177 7997,7444 7879,7727 7763,7832 7689,7815 7720,7961 7728,8157 7563,8465 7598,8696 Z M 9020,7620 C 8721,7674 8408,7753 8187,7620 8254,7429 8413,6932 8725,6808 8914,6803 8988,6973 9062,7046 9090,7307 9111,7498 9020,7620 Z"/>
                            <path d="M 17716,6404 C 17881,6340 17845,6523 17970,6450 17745,7035 17914,7322 17753,7482 17670,7933 17815,8175 17725,8473 17600,8578 17548,8473 17461,8473 17345,7993 17638,7400 17450,7057 17618,6920 17546,6654 17716,6404 Z"/>
                        </svg>
                    </div>
                    <span class="company-line2">ASSOCIATES</span>
                </div>

                <!-- Receipt number -->
                <div class="receipt-no-box">
                    <span class="rno-label">RECEIPT NO.</span>
                    <span class="rno-val">{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <div class="banner-container">
                <div class="modern-banner">RECEIPT</div>
            </div>
        </div>
    </div>

    <!-- ══════════════ FORM BODY ══════════════ -->
    <div class="form-body">

        <!-- Date -->
        <div class="date-row">
            <div class="date-field">
                <span class="f-label">Date:</span>
                <span class="fval">{{ $paymentDate }}</span>
            </div>
        </div>

        <!-- File No / Plot No / Street No / Plot Size / Block -->
        <div class="f-multi">
            <div class="f-piece" style="flex:1.1;">
                <span class="f-label">File No.</span>
                <span class="f-grow">{{ $booking->file_no ?? $booking->id }}</span>
            </div>
            <div class="f-piece" style="flex:1.1;">
                <span class="f-label">Plot No.</span>
                <span class="f-grow">{{ $plot->plot_no ?? $plot->plot_number ?? '—' }}</span>
            </div>
            <div class="f-piece" style="flex:1.2;">
                <span class="f-label">Street No.</span>
                <span class="f-grow">{{ $plot->street_number ?? $plot->street ?? '—' }}</span>
            </div>
            <div class="f-piece" style="flex:1.2;">
                <span class="f-label">Plot Size</span>
                <span class="f-grow">{{ $plot->plot_size ?? $plot->size ?? '—' }}</span>
            </div>
            <div class="f-piece" style="flex:0.8;">
                <span class="f-label">Block</span>
                <span class="f-grow">{{ $plot->block ?? '—' }}</span>
            </div>
        </div>

        <!-- Name / Relation -->
        <div class="f-row">
            <span class="f-label">Name:</span>
            <span class="f-grow" style="max-width:140px;">{{ $customer->name }}</span>
            <span class="f-label" style="margin-left:8px;">S/o, D/o, W/o</span>
            <span class="f-grow">{{ $customer->guardian_name ?? $customer->relation ?? '—' }}</span>
        </div>

        <!-- Total Amount / Cash / Cheque -->
<div class="f-row" style="display: flex; flex-wrap: nowrap; gap: 12px; align-items: baseline; margin-top: 10px; width: 100%;">

    <div style="white-space: nowrap;">
        <span class="f-label">Total Plot Rs.</span>
        <span style="border-bottom: 1px dotted #000; min-width: 110px; display: inline-block; text-align: center;">
            {{ number_format($payment->booking->total_price ?? 0) }}
        </span>
    </div>

    <div style="display: flex; align-items: center; gap: 4px; white-space: nowrap;">
        <span class="cb-box {{ $isCash ? 'checked' : '' }}"></span>
        <span class="f-label">Cash</span>
    </div>

    <div style="display: flex; align-items: center; gap: 4px; white-space: nowrap;">
        <span class="cb-box {{ $isRefRequired ? 'checked' : '' }}"></span>
        <span class="f-label">Cheque/Online No.</span>
    </div>

    <div style="flex-grow: 1; display: flex; align-items: baseline;">
        <span style="border-bottom: 1px dotted #000; flex-grow: 1; min-width: 200px; display: inline-block; text-align: center; padding: 0 5px;">
            {{ $payment->bank_ref ?: $payment->receipt_no ?: '' }}
        </span>
    </div>

</div>
    {{-- Category + Amount row --}}
    <div class="f-row">
        <span class="f-label">Payment Category:</span>
        <span class="f-grow">
            @if(is_numeric($currentInstallment))
                Installment #{{ $currentInstallment }} / {{ $totalInstallments }}
            @else
                {{ $currentInstallment }}
            @endif
        </span>
        <span class="f-label" style="margin-left:10px;">Amount Paid Now:</span>
        <span class="f-grow" style="max-width:120px; font-weight:700;">
            PKR {{ number_format($payment->amount_paid) }}
        </span>
    </div>

    {{-- Discount row (only for lump sum with discount) --}}
    @if($isLumpSum && $discountAmt > 0)
    <div class="f-row" style="border-bottom:1px solid #f0ebeb;">
        <span class="f-label">Original Balance:</span>
        <span class="f-grow" style="max-width:120px;">PKR {{ number_format($originalBalance) }}</span>
        <span class="f-label" style="margin-left:10px; color:#c8a000;">Settlement Discount:</span>
        <span class="f-grow" style="max-width:120px; color:#c8a000; font-weight:700;">PKR {{ number_format($discountAmt) }}</span>
        <span class="f-label" style="margin-left:10px;">Amount Settled:</span>
        <span class="f-grow" style="max-width:120px; font-weight:700;">PKR {{ number_format($payment->amount_paid) }}</span>
    </div>
    @endif

    @if($isDiscountEntry)
    <div class="f-row" style="border-bottom:1px solid #f0ebeb; color:#c8a000;">
        <span class="f-label">Note:</span>
        <span class="f-grow" style="font-style:italic;">Settlement discount — this amount was waived and not collected.</span>
    </div>
    @endif

    {{-- In Words — own row so long text never overflows --}}
    <div class="f-row" style="border-bottom:1px solid #e8e8e8;">
        <span class="f-label" style="white-space:nowrap;">Amount in Words:</span>
        <span style="flex:1; border-bottom:1.5px solid #333; padding:0 6px; font-size:8.5pt; font-weight:400; word-break:break-word; line-height:1.3; min-height:15px;">
            {{ $amountInWords }}
        </span>
    </div>

    {{-- Total Received / Balance --}}
    <div class="f-row">
            <span class="f-label">Total Amount Received Rs.</span>
            <span class="f-grow">{{ number_format($totalReceived) }}</span>
            <span class="f-label" style="margin-left:10px;">Total Balanced Amount Rs.</span>
            <span class="f-grow">{{ number_format(max(0, $balance)) }}</span>
        </div>

        <!-- QR Code -->
        <div class="f-row qr">
            <div class="qr-code-inline">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="Verification QR">
            </div>
        </div>

    </div><!-- /form-body -->

    <!-- ══════════════ FOOTER ══════════════ -->

       <div class="sig-row">
    <div class="sig-item">
        <span class="sig-line"></span>
        <span class="sig-label">N/o Receiver</span>
    </div>

    <div class="sig-item">
        <span class="sig-line"></span>
        <span class="sig-label">Receiver Signature</span>
    </div>

    <div class="sig-item" style="flex:0.6;">
        <span class="sig-line"></span>
        <span class="sig-label">Stamp</span>
    </div>

    <div class="sig-item">
        <span class="sig-line"></span>
        <span class="sig-label">Auditor Signature</span>
    </div>
</div>
   <div class="receipt-footer">
        <div class="note-row">
            <strong>NOTE:</strong> Kindly save the Receipt. For next installment, please bring the Receipt with you.
            For all correspondence mention plot and street Number.
        </div>
    </div>

</div><!-- /receipt-page -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>


function downloadPDF() {
    const element = document.getElementById('receipt-page');

    const opt = {
        margin: 0,
        filename: 'ZamarValley-Receipt-{{ $payment->id }}.pdf',

        image: { type: 'jpeg', quality: 1 },

        html2canvas: {
            scale: 2,
            useCORS: true,
            logging: false,
            dpi: 192,
            letterRendering: true
        },

        jsPDF: {
            unit: 'px',
            format: [794, 559],
            orientation: 'landscape'
        }
    };

    html2pdf().set(opt).from(element).save();
}
</script>
</body>
</html>
