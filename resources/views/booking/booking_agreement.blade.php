<!DOCTYPE html>
<html lang="ur" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اقرار نامہ زمر ویلی - بن عباسی ایسوسی ایٹس</title>

    <style>
        @import url('https://admin.urdufonts.com/wp-content/uploads/2024/02/Jameel-Noori-Nastaleeq-Font-Family-UrduFonts.com_.zip');

        :root {
            --green-dark: #1a5c20;
            --stripe-pink: #c2185b;
            --stripe-green: #388e3c;
            --stripe-teal: #00838f;
            --stripe-orange: #f57c00;
            --stripe-purple: #7b1fa2;
            --text-main: #111;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #555;
            font-family: 'Jameel Noori Nastaleeq', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            direction: rtl;
        }

        /* ══════════════ A4 PAGE ══════════════ */
        .page {
            width: 210mm;
            min-height: 297mm;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.6);
            margin: 10px 0 20px;
            display: flex;
            flex-direction: row;
            overflow: hidden;
            position: relative;
            direction: rtl;
        }

        /* ══════════════ LEFT DECORATIVE STRIPE ══════════════ */
        .left-stripe {
            width: 20px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            /* LTR order since it's a physical left column */
            order: 1;
        }

        .stripe-block {
            width: 20px;
            flex: 1;
        }

        /* ══════════════ MAIN CONTENT AREA ══════════════ */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 4mm 8mm 0 4mm;
        }

        /* ══════════════ HEADER ══════════════ */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            direction: ltr;
            /* keep header LTR for logo/ref layout */
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-container {
            width: 100px;
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo-container svg {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        #logo-placeholder {
            font-size: 8pt;
            color: #aaa;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .company-name-block {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .company-name-block svg {
            width: 180px;
            height: auto;
        }
        .logo-block2 {
            text-align: center;
        }
        .logo-text2 {
            font-size: 16px;
            font-family: Arial, Helvetica, sans-serif;
            letter-spacing: 3.5px;
        }

        /* Ref / Date block (top right of header, LTR) */
        .ref-date-block {
            text-align: left;
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 2;
            direction: ltr;
        }

        .ref-date-block .ref-line,
        .ref-date-block .date-line {
            display: flex;
            align-items: flex-end;
            gap: 4px;
        }

        .ref-date-block .ref-line span,
        .ref-date-block .date-line span {
            font-weight: bold;
        }

        .ref-date-block .field-line {
            border-bottom: 1px solid #333;
            min-width: 100px;
            height: 16px;
            display: inline-block;
            padding-bottom: 20px;
            padding-right: 10px;
        }

        /* ══════════════ TITLE SECTION ══════════════ */
        .title-section {
            text-align: center;
            margin: 6px 0 4px;
            direction: rtl;
        }

        .title-main {
            font-size: 15pt;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 700;
            color: #000;
            letter-spacing: 0.5px;
        }

        .title-party1 {
            font-size: 14pt;
            font-weight: 600;
            margin-top: 3px;
            line-height: 1.6;
        }

        .party2-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin: 3px 0;
            font-size: 9.5pt;
            font-weight: 600;
            direction: rtl;
        }

        .party2-left {
            white-space: nowrap;
        }

        .party2-right {
            display: flex;
            align-items: flex-end;
            gap: 5px;
            font-family: Arial, sans-serif;
            font-size: 9pt;
        }

        .inline-field {
            border-bottom: 1px solid #333;
            min-width: 120px;
            height: 16px;
            display: inline-block;
            vertical-align: bottom;
        }

        .inline-field-sm {
            min-width: 70px;
        }

        .inline-field-lg {
            min-width: 160px;
            padding-bottom: 20px;
            padding-right: 10px;

        }

        /* ══════════════ AGREEMENT BODY ══════════════ */
        .agreement-body {
            font-size: 12pt;
            line-height: 1.65;
            text-align: justify;
            direction: rtl;
            color: #111;
            flex: 1;
        }

        .clause {
            display: flex;
            gap: 4px;
            margin-bottom: 3px;
            align-items: flex-start;
            direction: rtl;
        }

        .clause-num {
            flex-shrink: 0;
            font-weight: 700;
            font-size: 10pt;
            direction: ltr;
            min-width: 20px;
            text-align: right;
        }

        .clause-text {
            flex: 1;
            text-align: justify;
        }

        /* dynamic plot info box inside clause 1 */
        .plot-info-inline {
            display: inline;
            font-weight: 700;
        }

        .qr-row {
            display: flex;
            justify-content: space-between;
        }
        /* ══════════════ CLOSING PARAGRAPH ══════════════ */
        .closing-para {
            font-size: 12pt;
            line-height: 1.65;
            text-align: justify;
            margin-top: 5px;
            direction: rtl;
        }
        .cnic-qr-container {
            display: flex;
            align-items: center;
            justify-content: space-between; /* Keeps them close together */
            /* margin: 6px 0; */
        }

        .qr-code-inline {
            width: 75px; /* Smaller, compact size */
            height: 75px;
            border: 1px solid #000;
            padding: 2px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0; /* Prevents the QR from squishing */
        }

        .qr-code-inline img {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* ══════════════ SIGNATURE SECTION ══════════════ */
        .sig-section {
            margin-top: 5px;
            direction: rtl;
            padding: 5px 10px;
        }

        .sig-top-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .sig-block {
            font-size: 12pt;
            font-weight: 700;
            min-width: 200px;
            /* Ensures both sides have equal 'gravity' */
        }

        .sig-underline {
            border-bottom: 1px solid #333;
            flex-grow: 1;
            max-width: 150px;
            height: 18px;
            display: inline-block;
        }

        .sig-bottom-row {
            display: flex;
            justify-content: space-between;
            margin-top: 6px;
            font-size: 12pt;
            font-weight: 700;
        }

        .sig-item-inner {
            display: flex;
            align-items: left;
            gap: 8px;
        }

        .witness-row {
            display: flex;
            justify-content: space-between;
            margin-top: 3px;
            font-size: 12pt;
            font-weight: 700;
        }

        .witness-item {
            display: flex;
            align-items: flex-end;
            gap: 4px;
        }

        .witness-line {
            border-bottom: 1px dotted #333;
            flex-grow: 1;
            max-width: 180px;
            height: 16px;
            display: inline-block;
        }

        .sig-block,
        .sig-item-inner,
        .witness-item {
            flex: 0 0 50%;
            /* Each column takes exactly 50% width */
            display: flex;
            align-items: flex-end;
            gap: 8px;
            font-size: 12pt;
            font-weight: 700;
        }

        /* Common row settings */
        .sig-top-row,
        .sig-bottom-row,
        .witness-row {
            display: flex;
            width: 100%;
            margin-bottom: 15px;
        }

        /* ══════════════ FOOTER ADDRESS BAR ══════════════ */
        /* ══════════════ FOOTER ADDRESS BAR ══════════════ */
        .footer-address {
            background: #4d4d4d;
            /* Dark Grey/Charcoal from image */
            color: #ffffff;
            text-align: right;

            /* Dimensions and Alignment */
            width: fit-content;
            /* Only as wide as the text */
            min-width: 60%;
            /* Ensures it has a substantial look */
            margin-left: auto;
            /* Pushes the container to the right */
            margin-right: 0;

            /* Shape and Spacing */
            padding: 12px 40px 12px 20px;
            border-radius: 10px 10px 0 0;
            /* Rounded on left side only, flat on right */

            /* Typography */
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.4;
            direction: ltr;

            /* Position at bottom of page */
            margin-top: 10px;
        }
        /* no-print for screen controls */
        .no-print-bar {
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* ══════════════ PRINT ══════════════ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                background: none;
                margin: 0;
                padding: 0;
                width: 210mm;
                height: 297mm;
            }

            .no-print {
                display: none !important;
            }

            .page {
                width: 210mm;
                height: 297mm;
                position: relative;
                display: flex;
                flex-direction: column;
                /* This is key for the layout */
                margin: 0;
                padding: 0;
                overflow: hidden;
                background-color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Side Stripe pinned to the right */
            .left-stripe {
                position: absolute;
                top: 0;
                left: 0;
                width: 12px;
                height: 100%;
                background-color: #2d7d32 !important;
                z-index: 5;
            }

            .main-content {
                flex: 1;
                /* Automatically takes up available space */
                padding: 10mm 15mm 5mm 10mm;
                /* Adjusted padding to prevent cutting */
                display: flex;
                flex-direction: column;
            }

            .agreement-body {
                font-size: 11pt;
                line-height: 1.5;
                text-align: justify;
            }
            .no-print-bar { display: none !important; }

            /* Footer styling - No absolute positioning here to prevent overlap */
            .footer-container {
                width: 100%;
                margin-top: 0;
            }

            .footer-address {
                padding: 3mm 12mm;
                font-size: 8.5pt;
                border-top: 1px solid #eee;
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .footer-stripe {
                height: 8mm;
                width: 100%;
                background: #2d7d32 !important;
                display: block !important;
            }

            .company-name-block {
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            /* Standard logo and text sizes */
            .logo-container {
                width: 82px;
                height: 92px;
                align-items: flex-start;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    @php
    $c = $booking->customer;
    $p = $booking->plot;
    $logoSrc  = ($sc['show_logo'] ?? true) && ($sc['logo'] ?? null) ? $sc['logo'] : null;
@endphp

    <!-- ════════ A4 PAGE ════════ -->
    <div class="page" id="form-page">

        <!-- ── LEFT DECORATIVE STRIPE ── -->
        <div class="left-stripe" id="left-stripe"></div>

        <!-- ── MAIN CONTENT ── -->
        <div class="main-content">
            {{-- HEADER --}}
            <div class="header-row">
                <div class="logo-area">
                    <div class="logo-container">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="3678 3385 5074 4459" fill-rule="evenodd">
      <!-- Dark: main tower body (left column + base arc) -->
      <path fill="#3a3a3a" d="M6747,7458 L6663,7453 6569,7446 6571,6729 C6377,6644 6007,6263 5863,6203 L5861,7449 5770,7452 5656,7457 4931,7529 C4417,7616 4133,7677 3678,7844
     4065,7831 4511,7764 4917,7737 5635,7689 6804,7696 7525,7739 7927,7763 8371,7834 8752,7844 8152,7642 7973,7572 7255,7503 L7254,5931 C7100,5844 7007,5728 6837,5637 L6840,7457 6747,7458Z" />
      <!-- Dark: tall right tower -->
      <path fill="#3a3a3a" d="M5656,7457 L5769,7452 5771,6009 C5853,6050 6510,6623 6663,6710 L6663,7453 6747,7458 6748,5128 C6737,5064 6688,5037 6621,4986 6415,4830 5633,4178 5504,4116
    L5504,5781 C5568,5718 5584,5691 5667,5649 L5656,7457Z" />
      <!-- Mid-gray: left shorter tower -->
      <path fill="#6e6f70" d="M4931,7529 L5657,7456 5570,7439 5572,5868 C5509,5891 4984,6327 4958,6385 4898,6521 4969,7292 4931,7529Z" />
      <!-- Light gray: glass facade panel (upper right) -->
      <path fill="#c4c6c8" d="M5765,4205 C5865,4260 6048,4421 6153,4503 6217,4553 6283,4605 6346,4655 6434,4725 6478,4782 6571,4817 L6570,4006 C6366,3903 6016,3536 5769,3385 L5765,4205Z"/>
      <!-- White: reflective window strip -->
      <path fill="#ffffff" d="M5770,7453 L5861,7449 5863,6202 C6007,6262 6377,6644 6571,6729 L6569,7446 6663,7453 6663,6709 C6509,6622 5852,6049 5771,6009 L5770,7453Z" />
                        </svg>
                    </div>
                    <div class="company-name-block">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="1315 6231 16695 2511" fill="rgb(27,25,24)"
                                fill-rule="evenodd">
                                <path
                                    d="M 11836,6544 L 12332,6582 C 12578,6325 13385,6294 13702,6688 13466,7287 12888,7505 12534,7530 13085,7624 13553,7974 13592,8137 13198,8442 12656,8595 12163,8580 11995,8645 11851,8702 11695,8669 11818,8550 11866,8510 12089,8437 11920,8280 12251,7716 11822,7546 12201,7450 11931,6972 12160,6782 L 11836,6544 Z M 11836,6544 L 11836,6544 Z M 12362,7335 C 12626,7368 12852,7321 13023,7167 13263,7048 13398,6936 13492,6768 13267,6591 13035,6568 12627,6593 12391,6777 12337,7039 12362,7335 Z M 12362,7335 L 12362,7335 Z M 12299,7776 C 12608,7737 13118,7925 13271,8112 12943,8324 12543,8396 12222,8433 12185,8256 12203,7967 12299,7776 Z" />
                                <path
                                    d="M 3905,6404 C 4070,6340 4034,6523 4159,6450 3934,7035 4103,7322 3942,7482 3859,7933 4004,8175 3914,8473 3789,8578 3737,8473 3650,8473 3534,7993 3827,7400 3639,7057 3807,6920 3735,6654 3905,6404 Z" />
                                <path
                                    d="M 4506,8553 C 4460,8599 4356,8532 4356,8476 4515,7953 4685,7288 4741,6538 4859,6483 4996,6495 5115,6574 5229,7212 5493,7724 5886,8185 L 6026,8071 C 6153,7571 6249,7138 6261,6571 6283,6486 6336,6414 6473,6390 6540,6993 6268,8060 6160,8359 6010,8440 5878,8465 5740,8431 L 5015,7197 C 4845,7256 4675,7929 4506,8553 Z" />
                                <path
                                    d="M 13610,8696 C 13887,8686 13843,7810 14233,7871 14536,7941 14793,7819 15040,7805 15150,8170 14938,8452 15013,8649 15103,8676 15257,8648 15285,8563 15381,8406 15434,7032 15250,6788 15009,6788 14966,6685 14855,6390 14630,6512 14505,6630 14394,6588 14353,7177 14009,7444 13891,7727 13775,7832 13701,7815 13732,7961 13740,8157 13575,8465 13610,8696 Z M 13610,8696 L 13610,8696 Z M 15032,7620 C 14733,7674 14420,7753 14199,7620 14266,7429 14425,6932 14737,6808 14926,6803 15000,6973 15074,7046 15102,7307 15123,7498 15032,7620 Z" />
                                <path
                                    d="M 15784,7114 C 15812,6801 16903,6271 17113,6414 17047,6983 15939,6877 16154,7102 15968,7372 16805,7417 17003,8223 16886,8421 16033,8680 15795,8599 15886,8215 16623,8341 16707,8084 16826,7919 15660,7441 15784,7114 Z" />
                                <path
                                    d="M 9794,6544 L 10290,6582 C 10536,6325 11343,6294 11660,6688 11424,7287 10846,7505 10492,7530 11043,7624 11511,7974 11550,8137 11156,8442 10614,8595 10121,8580 9953,8645 9809,8702 9653,8669 9776,8550 9824,8510 10047,8437 9878,8280 10209,7716 9780,7546 10159,7450 9889,6972 10118,6782 L 9794,6544 Z M 9794,6544 L 9794,6544 Z M 10320,7335 C 10584,7368 10810,7321 10981,7167 11221,7048 11356,6936 11450,6768 11225,6591 10993,6568 10585,6593 10349,6777 10295,7039 10320,7335 Z M 10320,7335 L 10320,7335 Z M 10257,7776 C 10566,7737 11076,7925 11229,8112 10901,8324 10501,8396 10180,8433 10143,8256 10161,7967 10257,7776 Z" />
                                <path
                                    d="M 1496,6544 L 1992,6582 C 2238,6325 3045,6294 3362,6688 3126,7287 2548,7505 2194,7530 2745,7624 3213,7974 3252,8137 2858,8442 2316,8595 1823,8580 1655,8645 1511,8702 1355,8669 1478,8550 1526,8510 1749,8437 1580,8280 1911,7716 1482,7546 1861,7450 1591,6972 1820,6782 L 1496,6544 Z M 1496,6544 L 1496,6544 Z M 2022,7335 C 2286,7368 2512,7321 2683,7167 2923,7048 3058,6936 3152,6768 2927,6591 2695,6568 2287,6593 2051,6777 1997,7039 2022,7335 Z M 2022,7335 L 2022,7335 Z M 1959,7776 C 2268,7737 2778,7925 2931,8112 2603,8324 2203,8396 1882,8433 1845,8256 1863,7967 1959,7776 Z" />
                                <path
                                    d="M 7598,8696 C 7875,8686 7831,7810 8221,7871 8524,7941 8781,7819 9028,7805 9138,8170 8926,8452 9001,8649 9091,8676 9245,8648 9273,8563 9369,8406 9422,7032 9238,6788 8997,6788 8954,6685 8843,6390 8618,6512 8493,6630 8382,6588 8341,7177 7997,7444 7879,7727 7763,7832 7689,7815 7720,7961 7728,8157 7563,8465 7598,8696 Z M 7598,8696 L 7598,8696 Z M 9020,7620 C 8721,7674 8408,7753 8187,7620 8254,7429 8413,6932 8725,6808 8914,6803 8988,6973 9062,7046 9090,7307 9111,7498 9020,7620 Z" />
                                <path
                                    d="M 17716,6404 C 17881,6340 17845,6523 17970,6450 17745,7035 17914,7322 17753,7482 17670,7933 17815,8175 17725,8473 17600,8578 17548,8473 17461,8473 17345,7993 17638,7400 17450,7057 17618,6920 17546,6654 17716,6404 Z" />
                            </svg>

                            <div class="logo-block2">
                                <span class="logo-text2">ASSOCIATES</span>
                            </div>
                    </div>
                </div>

                <div class="ref-date-block">
                <div class="ref-line">
                    <span>Ref</span>
                    <span class="field-line">{{ $booking->customer_booking_id }}</span>
                </div>
                <div class="date-line">
                    <span>Date</span>
                    <span class="field-line">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</span>
                </div>
            </div>
            </div>

            {{-- TITLE --}}
            <div class="title-section">
                <div class="title-main">اقرار نامہ &nbsp; زمر ویلی</div>
                <div class="title-party1">
                    فریق اول: بن عباسی ایسوسی ایٹس سروس روڈ زیرو موٹر وے چوک اسلام آباد
                </div>
            </div>

            {{-- فریق دوم --}}
            <div class="party2-row">
                <span class="party2-left">
                    فریق دوم:&nbsp;
                    <span class="inline-field inline-field-lg">{{ $c->name ?? '' }}</span>
                </span>
                <span class="party2-right">
                    &nbsp; S/O &nbsp;
                    <span class="inline-field inline-field-lg">{{ $c->guardian_name ?? '' }}</span>
                </span>
            </div>

            {{-- AGREEMENT BODY --}}
            <div class="agreement-body">

                <!-- Clause 1 - Dynamic Plot Info -->
                <div class="clause">
                    <span class="clause-num">(1)</span>
                    <span class="clause-text">
                    یہ کہ فریق اول نے پلاٹ نمبر&nbsp;<strong class="plot-info-inline">{{ $p->plot_number ?? '۔۔۔۔۔' }}</strong>&nbsp;
                    گلی نمبر&nbsp;<strong class="plot-info-inline">{{ $p->street_number ?? '۔۔۔۔۔' }}</strong>&nbsp;
                    سائز&nbsp;<strong class="plot-info-inline">{{ ($p->size ?? '') }} {{ ($p->unit ?? '') }}</strong>&nbsp;
                    بلاک&nbsp;<strong class="plot-info-inline">{{ $p->block ?? '۔۔۔۔۔' }}</strong>&nbsp;
                    فریق دوم کو فروخت کیا ہے۔
                </span>
                </div>

                <div class="clause">
                    <span class="clause-num">(2)</span>
                    <span class="clause-text">یہ کہ پلاٹ مذکورہ کی مکمل رقم مقررہ معیاد تک فریق اول کو وصول ہو جانے پر
                        فریق دوم فریق اول سے پلاٹ اپنے نام رجسٹر کروالے گا۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(3)</span>
                    <span class="clause-text">یہ کہ پلاٹ مذکورہ کے بیعانہ کی رقم وصول ہو جانے کے بعد فریق دوم کو پلاٹ کے
                        نقشہ کی نشاندہی کروا دی جائیگی۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(4)</span>
                    <span class="clause-text">یہ کہ زمر ویلی میں تمام گلیاں فریق دوم کی سہولت کے لئے رکھی گئی ہیں، جس کا
                        استعمال تمام صارفین کا مشترکہ حق ہو گا۔ دیگر کوئی بھی فریق ان میں تجاوزات بنا نہیں سکتا۔ فریق
                        دوم کو یہ حق حاصل نہیں ہو گا کہ وہ گلیوں میں بورنگ یا شیڈ نکالے، جبکہ فریق دوم اپنے پلاٹ کی حدود
                        کے اندر کنسٹرکشن کرنے کا حق رکھتا ہے۔ گلیوں میں کسی قسم کی تعمیرات کی اجازت فریق دوم کو ہر گز
                        حاصل نہ ہے۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(5)</span>
                    <span class="clause-text">مستقبل میں پانی کا نظام مکمل طور پر لگ جانے پر نصب کیا جائے گا جسکی
                        ادائیگی فریق دوم کو کرنی ہوگی۔ یعنی فریق دوم پانی کے حصول کے لئے بن عباسی ایسوسی ایٹ کے بنائے
                        گئے طریقہ کار کا پابند ہوگا۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(6)</span>
                    <span class="clause-text">تمام گلیوں کا لیول نقشہ اور پلاننگ کے مطابق طے شدہ ہے لہٰذا فریق دوم ان
                        تمام شرائط کو مانے کا پابند ہوگا۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(7)</span>
                    <span class="clause-text">متعلقہ زمین پر بجلی کی مین ہائی ٹینشن لائنز کی حد تک بن عباسی ایسوسی ایٹ
                        فریق دوم سے وصول کردہ رقوموں میں سے ادائیگی کی جائیگی، نیز (LTL) گھریلو یا کمرشل کنکشن کی مد میں
                        فریق دوم واپڈا کے موجودہ قوانین ضوابط کے مطابق ادائیگی کا پابند ہوگا۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(8)</span>
                    <span class="clause-text">مکمل رقم ادا کرنے پر فریق اول فریق دوم کو پلاٹ کی ٹرانسفر / رجسٹری انتقال
                        دینے کا پابند ہوگا تاہم یہ کہ کلائنٹ کو رجسٹری کی مد میں تمام ٹیکسز ادا کرنے ہوں گے، جس میں
                        ایڈوانس ٹیکس، گین ٹیکس اور رجسٹری سے متعلق جو بھی ٹیکس ہوگا، وہ کسٹمر (خریدار) ادا کرے
                        گا۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(9)</span>
                    <span class="clause-text">فریق دوم ماہانہ سیکورٹی کی مد میں طے شدہ سیکورٹی چارجز بھی ادا کرنے کا
                        پابند ہوگا۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(10)</span>
                    <span class="clause-text">فریق دوم پوری رقم کی ادائیگی کے بعد اگر اپنا پلاٹ فروخت کرنا چاہے تو فریق
                        اول کو کوئی اعتراض نہ ہوگا تاہم اپنے تمام تر ایگریمنٹ خریدار کے نام ٹرانسفر کروانے کے لئے بن
                        عباسی ایسوسی ایٹ سے رجوع کرے گا اور بقایا ادا شدہ رقم نہ ملنے کی صورت میں بن عباسی ایسوسی ایٹ
                        پلاٹ کو کینسل کرنے کا حق محفوظ رکھتا ہے اور جس پر فریق دوم کا کوئی بھی عذر ہر گز قابل قبول نہ
                        ہوگا۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(11)</span>
                    <span class="clause-text">لے آؤٹ پلان میں تبدیلی کی صورت میں بن عباسی ایسوسی ایٹ پلاٹ کو منتقل کرنے
                        کا حق رکھتا ہے۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(12)</span>
                    <span class="clause-text">اگر فریق دوم کسی بھی وجہ سے رقم ادا کرنے سے قاصر رہا تو اس صورت میں پلاٹ
                        کی کل رقم میں سے ۱۵٪ کٹوتی کے بعد بقیہ رقم واپس کر دی جائیگی اور پلاٹ کینسل کر دیا
                        جائیگا۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(13)</span>
                    <span class="clause-text">اگر فریق دوم اپنا پلاٹ دوران ادائیگی اقساط یا بیعانہ پر کسی دوسرے فریق کو
                        فروخت کرے گا تو اس پر بھی بن عباسی ایسوسی ایٹ کے تمام قوانین لاگو ہوں گے۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(14)</span>
                    <span class="clause-text">فریق دوم کے وارثوں بھی مذکورہ بالا طے کردہ تمام شرائط وضوابط کی ہر طرح سے
                        پابندی لازم کرنے کے پابند و مجاز ہوں گے۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(15)</span>
                    <span class="clause-text">رہائشی پلاٹ میں فریق دوم کسی بھی قسم کی کمرشل ایکٹویٹی کرنے کا حق نہیں
                        رکھتا۔ ایسی صورت میں بن عباسی ایسوسی ایٹ کو مکمل حق حاصل ہوگا کہ وہ پلاٹ کینسل کر کے فریق دوم کے
                        خلاف قانونی چارہ جوئی کرے۔ سوئی گیس کے موجودہ طریقہ کار کے مطابق پیسے ادا کرنے ہوں گے۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(16)</span>
                    <span class="clause-text">CDA کے تمام قوانین جن میں خصوصاً کمرشل پلاٹس، جن کی CDA کی طرف سے واضح
                        ہدایات ہیں کہ CDA بائی لاز کے مطابق نقشوں کے اندر Revised Plan کے ساتھ پارکنگ کی جگہ مختص کریں،
                        چار یا ساڑھے چار مرلے والے پلاٹس ۱۵ فٹ آگے پارکنگ چھوڑیں جو گراؤنڈ کے ساتھ ۱ یا ۲ منزلیں بنا رہے
                        ہیں انکو Basement بنانے کی ضرورت نہیں ہے۔ جو گراؤنڈ اور ۲ سے زیادہ منزلیں بنا رہے ہیں وہ ۱۵ فٹ
                        پارکنگ آگے بھی چھوڑیں اور ساتھ Basement بھی بنائیں۔</span>
                </div>

                <div class="clause">
                    <span class="clause-num">(17)</span>
                    <span class="clause-text">بن عباسی ایسوسی ایٹ اس بات کا اختیار رکھتی ہے کہ بڑھتی ہوئی مہنگائی کے سبب
                        وہ ڈیولپمنٹ چارجز جو کہ اس وقت پچاس ہزار روپے (Rs.50,000/-) فی مرلہ ہے اسے بڑھا سکتی ہے جس پر
                        فریق دوم کو کوئی اعتراض نہ ہوگا۔</span>
                </div>

            </div><!-- /agreement-body -->

            {{-- CLOSING --}}
            <div class="qr-row">
                <div class="closing-para">
                لہٰذامعاہدہ ہذا فریق دوم نے بقائمی حوش و حواس خمسہ بلا بر جبر و کراہ روبرو گواہ کے خود پڑھ، سن سمجھ کر
                تحریر وتکمیل کروا دیا ہے تاکہ سند رہے اور بوقت ضرورت کام آوے۔
            </div>
            <div class="qr-code-inline">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=ZamarValley-Plot-Booking" alt="QR">
                </div>
            </div>


            {{-- SIGNATURES --}}
            <div class="sig-section">
                <div class="sig-top-row">
                    <div class="sig-block">العبد (فریق اول)</div>
                    <div class="sig-block">العبد (فریق دوم)</div>
                </div>

                <div class="sig-bottom-row">
                    <div class="sig-item-inner">
                        <span>دستخط و مہر فریق اول</span>
                        <span class="sig-underline"></span>
                    </div>
                    <div class="sig-item-inner">
                        <span>دستخط و نشان انگوٹھا فریق دوم</span>
                        <span class="sig-underline"></span>
                    </div>
                </div>

                <div class="witness-row">
                    <div class="witness-item">
                        <span>گواہ شد: 1</span>
                        <span class="witness-line" id="field-witness1"></span>
                    </div>
                    <div class="witness-item">
                        <span>گواہ شد: 2</span>
                        <span class="witness-line" id="field-witness2"></span>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <footer class="footer-address">
                Ibrahim Khalil Avenue, Service Road, Adjacent Kashmir Highway,<br>
                Near Motorway Old Toll Plaza, Islamabad. &nbsp; | &nbsp; E-mail: binabbasi456@gmail.com
            </footer>

        </div><!-- /main-content -->

    </div><!-- /page -->

    {{-- Action bar (screen only, hidden on print) --}}
<div class="no-print-bar" style="background:#1e3a8a;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;margin-bottom:0;width:210mm;">
    <div style="color:#fff;font-size:11px;font-family:Arial,sans-serif;">
        <strong>{{ $booking->customer_booking_id }}</strong> —
        {{ $c->name ?? '' }} | Plot #{{ $p->plot_number ?? '' }}
    </div>
    <div style="display:flex;gap:10px;">
        <button onclick="window.print()"
            style="background:#1a5c20;color:#fff;border:none;border-radius:8px;padding:9px 22px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;">
            <span>⬇</span> Download / Print PDF
        </button>
        <button onclick="window.close()"
            style="background:#64748b;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;cursor:pointer;">
            ✕ Close
        </button>
    </div>
</div>

    <script>
    (function buildStripe() {
        const colors = [
            '#c2185b','#388e3c','#c2185b','#388e3c','#c2185b',
            '#388e3c','#c2185b','#388e3c','#c2185b','#388e3c',
            '#c2185b','#388e3c','#c2185b','#388e3c','#c2185b',
            '#388e3c','#c2185b','#388e3c','#c2185b','#388e3c',
            '#c2185b','#388e3c','#c2185b','#388e3c','#c2185b',
            '#388e3c','#c2185b','#388e3c','#c2185b','#388e3c',
            '#c2185b','#388e3c','#c2185b','#388e3c','#c2185b',
            '#388e3c','#c2185b','#388e3c','#c2185b','#388e3c',
        ];
        const stripe = document.getElementById('left-stripe');
        colors.forEach(c => {
            const s = document.createElement('div');
            s.className = 'stripe-block';
            s.style.background = c;
            stripe.appendChild(s);
        });
    })();
</script>
</body>

</html>
