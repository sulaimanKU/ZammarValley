<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zamar Valley - Application for Booking of Plot</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap');

        :root {
            --green-dark: #1a5c20;
            --green-mid: #2e7d32;
            --green-light: #8bc34a;
            --green-fill: #8db84a;
            --green-banner: #c5e1a5;
            --inv-fill: #8fbc3b;
            --footer-stripe1: #e53935;
            --footer-stripe2: #43a047;
            --footer-stripe3: #fdd835;
            --footer-stripe4: #1e88e5;
            --footer-stripe5: #fb8c00;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #666;
            font-family: "Google Sans", sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
            /* Adds space for the screen view */
        }

        /* ── A4 PAGE ── */

        .page {
            width: 210mm;
            height: 297mm;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.6);
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            /* FIX: Ensure content stays within printable bounds */
            box-sizing: border-box;
        }

        .header-inner,
        .inventory-section,
        .form-body {
            /* FIX: Professional 15mm left/right padding to prevent side-cutting */
            padding-left: 10mm !important;
            padding-right: 10mm !important;
        }

        /* ═══════════════════════════════
           HEADER
        ═══════════════════════════════ */
        .header-section {
            height: 140px; /* Increased from 88px */
            position: relative;
            width: 100%;
            overflow: hidden;
            background: white;
            display: flex;
            align-items: center;
        }

        .header-curve-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            /* Covers the left half */
            height: 100%;
            z-index: 0;
        }

        /* Decorative diagonal green band top-right */
        .header-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 70%;
            /* Increased width to overlap slightly with the curve */
            height: 100%;
            background: linear-gradient(135deg,
                    transparent 20%,
                    /* #e8f5e0 20%, #e8f5e0 55%, */
                    #b8d96a 55%, #b8d96a 70%,
                    #6aaf28 70%, #6aaf28 82%,
                    #2d7d32 82%);
            z-index: 1;
        }

        .header-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-end; /* Changed from flex-end to center for vertical balance */
            height: 100%;
            padding: 10px 15px; /* Increased padding */
            margin: 65px;
        }

        .logo-container {
            width: 150px; /* Slightly wider for better scale */
            height: 100px; /* Increased from 76px */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        .logo-placeholder-text {
            font-size: 9pt;
            color: #aaa;
            text-align: center;
        }

        .header-text-block {
            margin-left: 8px;
            display: flex;
            flex-direction: column;
            justify-content: right;
        }

        .header-text-block .org-name {
            font-size: 13pt;
            font-weight: bold;
            color: #1a5c20;
            letter-spacing: 0.5px;
        }

        .header-text-block .org-sub {
            font-size: 14pt;
            color: #333;
            font-weight: 700;
        }

        /* ── APPLICATION FORM BANNER ── */
        .app-banner {
            background: var(--green-dark);
            color: white;
            text-align: center;
            font-size: 13.5pt;
            font-weight: bold;
            padding: 6px 0;
            letter-spacing: 1px;
            text-transform: uppercase;
            width: 100%;
            margin: 0;
        }

        /* ═══════════════════════════════
           PLOT INVENTORY GRID + PHOTO
        ═══════════════════════════════ */
        .inventory-section {
            display: flex;
            padding: 8px 12px 4px 12px;
            gap: 6px;
            align-items: flex-start;
        }

        .inv-grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: auto auto auto;
            gap: 4px;
        }

        /* Row 1: Plot No, Street No */
        /* Row 2: Block, Size */
        /* Row 3: Type (spanning 1 col, centered) */

        .inv-box {
            background: var(--inv-fill);
            border: 1px solid #555;
            padding: 3px 6px;
            font-size: 8.5pt;
            font-weight: bold;
            color: #111;
            display: flex;
            flex-direction: column;
        }

        .inv-box .inv-label {
            font-size: 9pt;
            font-weight: bold;
        }

        .inv-box .inv-value {
            font-size: 9pt;
            min-height: 14px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.3);
            margin-top: 2px;
        }

        .inv-type-row {
            grid-column: 1 / 2;
            /* type is in first column only based on image */
        }

        .photo-box-1 {
            width: 80px;
            height: 95px;
            border: 1.5px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            font-weight: bold;
            color: #555;
            background: #fafafa;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .photo-box-1 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
            position: absolute;
        }

        /* ═══════════════════════════════
           FORM BODY
        ═══════════════════════════════ */
        .form-body {
            padding: 4px 12px 6px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            flex: 1;
        }

        .cnic-qr-container {
            display: flex;
            align-items: center;
            justify-content: space-between; /* Keeps them close together */
            margin: 6px 0;
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

        /* generic field row */
        .f-row {
            display: flex;
            align-items: flex-end;
            margin: 5px 0;
            font-size: 9pt;
            font-weight: bold;
            gap: 0;
        }

        .f-label {
            white-space: nowrap;
            flex-shrink: 0;
        }

        .f-line {
            flex: 1;
            border-bottom: 1px solid #333;
            margin-left: 5px;
            height: 16px;
        }

        /* multi-column row */
        .f-cols {
            display: flex;
            gap: 10px;
            margin: 5px 0;
        }

        .f-col {
            display: flex;
            align-items: flex-end;
            font-size: 9pt;
            font-weight: bold;
            flex: 1;
        }

        .f-col .f-line {
            margin-left: 4px;
        }

        /* ── CNIC ── */
        .cnic-row {
            display: flex;
            align-items: center;
            margin: 6px 0;
            font-size: 9pt;
            font-weight: bold;
            gap: 6px;
        }

        .cnic-label {
            white-space: nowrap;
            flex-shrink: 0;
        }

        .cnic-boxes {
            display: flex;
            gap: 2px;
            align-items: center;
        }

        .c-box {
            width: 19px;
            height: 19px;
            border: 1px solid #333;
            text-align: center;
            line-height: 19px;
            font-size: 9pt;
            font-weight: normal;
            color: #333;
        }

        .c-dash {
            font-size: 10pt;
            font-weight: bold;
            padding: 0 1px;
        }

        /* ── NOMINEE SECTION (right-aligned photo) ── */
        .nominee-section {
            display: flex;
            gap: 8px;
            margin-top: 4px;
        }

        .nominee-fields {
            flex: 1;
        }

        .photo-box-2 {
            width: 80px;
            height: 90px;
            border: 1.5px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            font-weight: bold;
            color: #555;
            background: #fafafa;
            flex-shrink: 0;
            align-self: center;
            position: relative;
            overflow: hidden;
        }

        .photo-box-2 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
            position: absolute;
        }

        /* ── SECTION DIVIDER LABEL ── */
        .section-label {
            font-size: 9pt;
            font-weight: bold;
            color: #333;
            margin: 4px 0 2px;
            text-decoration: underline;
        }

        /* ── DECLARATION ── */
        .declaration-section {
            margin-top: 8px;
            font-size: 8.5pt;
            line-height: 1.45;
        }

        .declaration-section .decl-title {
            font-weight: bold;
            font-size: 9pt;
            text-decoration: underline;
            margin-bottom: 4px;
        }

        .declaration-section p {
            margin-bottom: 3px;
            text-align: justify;
        }

        .payment-line {
            margin-top: 6px;
            font-size: 8.5pt;
        }

        .pay-field {
            display: inline-block;
            min-width: 100px;
            border-bottom: 1px solid #333;
            margin: 0 3px;
            vertical-align: bottom;
        }

        /* ── FOOTER SIGNATURES ── */
        .footer-signs {
            display: flex;
            justify-content: space-between;
            margin-top: 22px;
            font-size: 9pt;
            font-weight: bold;
            padding: 0 4px;
        }

        .sign-field {
            display: inline-block;
            min-width: 140px;
            border-bottom: 1px solid #333;
            margin-left: 5px;
        }

        /* ── COLORFUL BOTTOM STRIPE ── */
        .footer-stripe {
            display: flex;
            height: 10px;
            margin-top: auto;
            flex-shrink: 0;
        }

        .stripe-seg {
            flex: 1;
        }

        /* ═══════════════════════════════
           PRINT STYLES
        ═══════════════════════════════ */
        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            body {
                background: none;
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .form-section {
                margin-bottom: 2mm !important;
                /* Reduce gap between sections */
            }

            .no-print {
                display: none !important;
            }

            .page {
                margin: 0 !important;
                box-shadow: none !important;
                width: 210mm;
                height: 297mm;
                /* Change min-height to fixed height */
                max-height: 297mm;
                /* Absolute cap */
                overflow: hidden;
                /* Prevents the 'ghost' second page */
                position: relative;
                display: flex;
                flex-direction: column;
                page-break-after: avoid;
                /* Don't force a new page after this */
                page-break-inside: avoid;
            }

            .footer {
                position: absolute;
                bottom: 10mm;
                /* Fixed distance from the bottom of A4 */
                left: 15mm;
                right: 15mm;
            }

            .footer-stripe {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
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
@endphp

    <!-- Action Buttons (fixed bar, hidden on print) -->
    <div class="no-print" style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#1e293b;padding:10px 20px;display:flex;gap:10px;align-items:center;box-shadow:0 2px 8px rgba(0,0,0,.3);">
        <span style="color:#94a3b8;font-size:12px;font-weight:600;margin-right:auto;">Booking Application — {{ $booking->customer_booking_id }}</span>
        <button type="button" onclick="window.print()"
                style="padding:7px 16px;background:#16a34a;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">
            🖨 Print
        </button>
        <button type="button" id="btn-download"
                style="padding:7px 16px;background:#2563eb;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">
            ⬇ Download
        </button>
    </div>
    <div class="no-print" style="height:48px;"></div>

    <!-- ════════ A4 PAGE ════════ -->
    <div class="page" id="form-page">
        <!-- ── HEADER ── -->
        <header class="header-section">
            <svg class="header-curve-left" viewBox="0 0 400 88" preserveAspectRatio="none">
                <path d="M 0,0 L 400,0 C 300,5 200,15 150,30 C 80,50 40,70 0,88 Z" fill="#2d7d32" />
                <path d="M 0,0 L 400,0 C 300,2 250,8 180,15 C 100,25 40,35 0,45 Z" fill="#52a852" opacity="0.4" />
            </svg>

            <div class="header-bg"></div>

            <div class="header-inner">
                <div class="logo-container">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="4550 4000 11400 8300" fill-rule="evenodd">
                        <path fill="rgb(36,163,79)" d="M 14172,9675 C 14465,9471 15023,8663 15247,8220 15716,7290 15810,6234 15665,5194 L 15559,4646 C 15474,4459 15532,4573 15457,4497 14189,6205 14738,5420 14857,6884 14985,8456 14265,9169 14172,9675 Z" />
                        <path fill="rgb(180,42,40)" d="M 12406,8821 C 12539,9343 12404,9328 12445,9772 12653,9681 12547,9861 12635,9494 12670,9346 12649,9307 12715,9169 12950,9277 13562,9946 13957,9983 13952,9709 13861,9692 13451,9379 13201,9188 13083,9005 12865,8822 13008,8716 14829,8145 13556,7536 12075,6830 12923,8461 12406,8821 Z M 12740,7781 C 12802,8052 12660,7806 12956,7962 L 12851,8594 C 13167,8525 13560,8282 13648,8081 13470,7673 12907,7639 12740,7781 Z" />
                        <path fill="rgb(180,42,40)" d="M 6497,9917 L 6608,9744 C 6792,9356 6596,8972 7616,8867 7612,9102 7532,9581 7694,9762 7962,9388 7910,7969 7821,7777 7695,7502 7744,7816 7588,7470 L 7514,7271 C 6985,7652 7037,7825 6806,8513 6670,8918 6493,8794 6502,9141 6508,9354 6544,9248 6489,9512 L 6497,9917 Z M 6977,8762 L 7608,8675 C 7619,8261 7648,8020 7309,7898 7213,8168 7072,8321 6977,8762 Z" />
                        <path fill="rgb(180,42,40)" d="M 10630,9927 C 11051,9237 10501,9036 11714,8868 L 11731,9767 C 11925,9658 11902,9717 11952,9163 11977,8879 12034,8002 11907,7756 11777,7504 11758,7759 11678,7446 11674,7431 11665,7377 11662,7362 11660,7350 11654,7326 11652,7316 11650,7306 11648,7284 11640,7270 10929,7580 11141,8376 10757,8814 10622,8966 10603,8748 10618,9133 10642,9776 10398,9685 10630,9927 Z M 11079,8762 L 11707,8688 C 11712,8310 11752,8047 11461,7907 11296,8102 11158,8464 11079,8762 Z" />
                        <path fill="rgb(19,21,21)" d="M 6504,11844 C 6967,11857 6735,11876 6977,11573 L 7831,11558 7964,11856 8317,11857 C 8159,11506 7768,10786 7551,10496 7103,10496 7116,10703 6939,11036 6798,11304 6610,11585 6504,11844 Z M 7116,11289 L 7666,11287 7416,10807 7116,11289 Z" />
                        <path fill="rgb(180,42,40)" d="M 8470,9787 C 8752,9595 8723,8888 8766,8548 L 8945,8746 C 9368,9210 9621,8466 9953,8269 9932,8518 9868,8763 9818,9012 L 9725,9578 C 9757,9786 9688,9642 9812,9775 10074,9342 10354,8281 10356,7614 9794,7783 9798,8497 9248,8598 8924,8307 9007,7915 8955,7602 L 8910,7437 C 8906,7423 8899,7402 8890,7386 8419,7463 8571,7716 8491,8448 8452,8803 8302,9544 8470,9787 Z" />
                        <path fill="rgb(241,229,28)" d="M 13170,5119 C 13365,5220 13292,5188 13437,5325 14084,5931 13581,5420 14120,6079 14274,6268 14354,6434 14448,6704 14550,6996 14585,7334 14693,7503 14770,6415 14538,5445 13852,4674 13668,4466 13505,4235 13170,4121 L 13170,5119 Z" />
                        <path fill="rgb(180,42,40)" d="M 4860,7795 C 5148,7754 5545,7696 5766,7848 5866,7950 5736,8126 5613,8334 L 4780,9548 C 4667,9882 4760,9805 4760,9806 L 4793,9913 C 5119,9999 6120,9720 6210,9266 5807,9361 5700,9522 5168,9556 5298,8949 6993,7633 5684,7400 4857,7253 4898,7497 4860,7795 Z" />
                        <path fill="rgb(218,52,49)" d="M 11352,4792 C 11729,5959 12679,5042 14075,6501 13776,5658 12040,4459 11352,4792 Z" />
                        <path fill="rgb(19,21,21)" d="M 4766,10504 L 5338,11569 C 5686,12156 5766,11792 6091,11201 6196,11008 6383,10729 6421,10494 5800,10500 6051,11078 5590,11434 5502,11243 5217,10695 5083,10510 L 4766,10504 Z" />
                        <path fill="rgb(19,21,21)" d="M 14731,11857 C 15048,11857 14994,11923 15058,11731 15064,11715 15060,11286 15061,11229 L 15704,10542 C 15275,10358 15170,10819 14922,10984 14860,10924 14823,10899 14751,10831 14465,10564 14564,10437 14069,10510 14105,10539 14348,10887 14502,11018 14827,11298 14734,11353 14731,11857 Z" />
                        <path fill="rgb(19,21,21)" d="M 8774,11844 L 10107,11860 C 10099,11519 10165,11582 9732,11587 9512,11590 9324,11606 9104,11574 9064,10997 9226,10288 8853,10506 8705,10594 8765,10551 8770,11130 8772,11368 8769,11606 8774,11844 Z" />
                        <path fill="rgb(19,21,21)" d="M 10565,11850 L 11649,11865 C 12071,11827 11820,11910 11906,11729 11864,11636 11955,11580 11462,11590 11266,11593 11088,11599 10891,11581 L 10874,10490 10598,10502 C 10511,10842 10557,11482 10565,11850 Z" />
                        <path fill="rgb(180,42,40)" d="M 10427,5989 L 10678,6122 C 11176,6309 11099,6129 11476,6009 11963,5855 12378,5972 12856,5995 12842,5986 12821,5955 12814,5967 L 12344,5758 C 11752,5596 10905,5615 10427,5989 Z" />
                        <path fill="rgb(27,25,24)" d="M 12675,11579 C 12709,11083 12750,11346 13131,11260 13310,11220 13250,11293 13303,11131 13292,10937 13128,11061 12715,11009 L 12675,10742 C 12717,10616 12689,10646 13134,10652 13313,10654 13495,10652 13674,10652 L 12475,10591 12475,11790 13673,11790 13674,11669 12889,11676 C 12545,11646 12763,11686 12675,11579 Z" />
                        <path fill="rgb(27,25,24)" d="M 12402,11858 L 13764,11845 13767,11626 12675,11579 C 12764,11685 12546,11646 12888,11675 L 13674,11668 13673,11790 12475,11790 12476,10590 13674,10652 C 13494,10652 13314,10654 13134,10651 12689,10645 12718,10615 12675,10742 L 13701,10708 13701,10536 12409,10532 12402,11858 Z" />
                        <path fill="rgb(141,41,42)" d="M 10455,7111 L 10946,7086 C 11082,6561 11318,6418 11737,6192 11427,6176 11102,6364 10952,6498 10792,6641 10490,6920 10455,7111 Z" />
                        <path fill="rgb(27,25,24)" d="M 12401,11858 L 12409,10532 13701,10536 13701,10708 C 13764,10622 13786,10821 13757,10507 L 12372,10474 C 12339,10722 12351,10952 12350,11190 12350,11391 12311,11682 12401,11858 Z" />
                    </svg>
                </div>
                <div class="header-text-block">
                    <span class="org-sub" id="org-sub">(A Project of Bin Abbasi Associates)</span>
                </div>
            </div>
        </header>


        <!-- ── APPLICATION FORM BANNER ── -->
        <div class="app-banner">APPLICATION FORM</div>

        <!-- ── INVENTORY + PHOTO 1 ── -->
        <div class="inventory-section">
            <div class="inv-grid">
                <!-- Row 1 -->
                <div class="inv-box">
                    <span class="inv-label">Plot No.</span>
                    <span class="inv-value" id="plot-no">#{{ $p->plot_number ?? '' }}</span>
                </div>
                <div class="inv-box">
                    <span class="inv-label">Street No.</span>
                    <span class="inv-value" id="street-no">{{ $p->street_number ?? '' }}</span>
                </div>
                <!-- Row 2 -->
                <div class="inv-box">
                    <span class="inv-label">Block</span>
                    <span class="inv-value" id="block">{{ $p->block ?? '' }}</span>
                </div>
                <div class="inv-box">
                    <span class="inv-label">Size</span>
                    <span class="inv-value" id="size">{{ $p->size ?? '' }} {{ $p->unit ?? '' }}</span>
                </div>
                <!-- Row 3 -->
                <div class="inv-box inv-type-row">
                    <span class="inv-label">Type</span>
                    <span class="inv-value" id="type">{{ ucfirst($p->price_type ?? '') }}</span>
                </div>
                @php $appPlotDisc = (float)($p->discount_amount ?? 0); @endphp
                @if($appPlotDisc > 0)
                <!-- Row 4: Discount (highlighted) -->
                <div class="inv-box" style="background:#fef9c3;border:1.5px solid #fde68a;grid-column:1/3;">
                    <span class="inv-label" style="color:#854d0e;">★ Discount{{ ($p->discount_reason ?? null) ? ' ('.$p->discount_reason.')' : ' (at booking)' }}</span>
                    <span class="inv-value" style="color:#d97706;font-weight:900;">− PKR {{ number_format($appPlotDisc) }}</span>
                </div>
                @endif
            </div>

            <!-- Applicant Photo Box -->
            <div class="photo-box-1">
                <img id="photo1-img" src="{{ $customerPicB64 ?? '' }}" alt="" style="{{ ($customerPicB64 ?? null) ? 'display:block;' : 'display:none;' }}">
                <span id="photo1-label" style="{{ ($customerPicB64 ?? null) ? 'display:none;' : '' }}">Photo</span>
            </div>
        </div>

        <!-- ── FORM BODY ── -->
        <div class="form-body">

            <!-- Applicant Info -->
            <div class="f-row">
                <span class="f-label">Name:</span>
                <span class="f-line" id="field-name">{{ $c->name ?? '' }}</span>
            </div>
            <div class="f-row">
                <span class="f-label">Father's / Husband's Name:</span>
                <span class="f-line" id="field-father-name">{{ $c->guardian_name ?? '' }}</span>
            </div>
            <div class="f-row">
                <span class="f-label">Postal Address:</span>
                <span class="f-line" id="field-postal-address">{{ $c->postal_address ?? '' }}</span>
            </div>
            <div class="f-row" style="margin-top:2px;">
                <span class="f-line" id="field-postal-address-2"></span>
            </div>
            <div class="f-row">
                <span class="f-label">Residential Address:</span>
                <span class="f-line" id="field-res-address">{{ $c->residential_address ?? '' }}</span>
            </div>

            <div class="f-cols" style="margin-top:6px;">
                <div class="f-col" style="flex:1.2;">
                    <span class="f-label">Phone Off #:</span>
                    <span class="f-line" id="field-phone-off">{{ $c->phone_off ?? '' }}</span>
                </div>
                <div class="f-col" style="flex:1;">
                    <span class="f-label">Res #:</span>
                    <span class="f-line" id="field-res-phone">{{ $c->phone_res ?? '' }}</span>
                </div>
                <div class="f-col" style="flex:1.2;">
                    <span class="f-label">Mobile #:</span>
                    <span class="f-line" id="field-mobile">{{ $c->mobile ?? '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <span class="f-label">Email:</span>
                <span class="f-line" id="field-email">{{ $c->email ?? '' }}</span>
            </div>

            <div class="f-cols">
                <div class="f-col" style="flex:1.2;">
                    <span class="f-label">Occupation:</span>
                    <span class="f-line" id="field-occupation">{{ $c->occupation ?? '' }}</span>
                </div>
                <div class="f-col" style="flex:0.6;">
                    <span class="f-label">Age:</span>
                    <span class="f-line" id="field-age">{{ $c->age ?? '' }}</span>
                </div>
                <div class="f-col" style="flex:1.2;">
                    <span class="f-label">Nationality:</span>
                    <span class="f-line" id="field-nationality">{{ $c->nationality ?? 'Pakistani' }}</span>
                </div>
            </div>

            <!-- CNIC 1 + QR -->
            <div class="cnic-qr-container">
                <div class="cnic-row" style="margin: 0;">
                    <span class="cnic-label">C.N.I.C. #</span>
                    <div class="cnic-boxes" id="cnic-1"></div>
                </div>

                <div class="qr-code-inline">
                    @if($qrCode ?? null)
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR">
                    @endif
                </div>
            </div>

            <!-- Nominee Section with Photo 2 -->
            <div class="nominee-section">
                <div class="nominee-fields">
                    <div class="f-row">
                        <span class="f-label">Name:</span>
                        <span class="f-line" id="field-nominee-name">{{ $c->nominee_name ?? '' }}</span>
                    </div>
                    <div class="f-row">
                        <span class="f-label">Relation:</span>
                        <span class="f-line" id="field-relation">{{ $c->nominee_relation ?? '' }}</span>
                    </div>
                    <div class="f-row">
                        <span class="f-label">Address of Nominee:</span>
                        <span class="f-line" id="field-nominee-address">{{ $c->nominee_address ?? '' }}</span>
                    </div>

                    <!-- CNIC 2 -->
                    <div class="cnic-row">
                        <span class="cnic-label">C.N.I.C. #</span>
                        <div class="cnic-boxes" id="cnic-2"></div>
                    </div>
                    <!-- CNIC 3 (additional row visible in image) -->
                    <div class="cnic-row">
                        <span class="cnic-label">C.N.I.C. #</span>
                        <div class="cnic-boxes" id="cnic-3"></div>
                    </div>
                </div>

                <!-- Nominee Photo Box -->
                <div class="photo-box-2">
                    <img id="photo2-img" src="{{ $nomineePicB64 ?? '' }}" alt="" style="{{ ($nomineePicB64 ?? null) ? 'display:block;' : 'display:none;' }}">
                    <span id="photo2-label" style="{{ ($nomineePicB64 ?? null) ? 'display:none;' : '' }}">Photo</span>
                </div>
            </div>

            <!-- ── DECLARATION ── -->
            <div class="declaration-section">
                <div class="decl-title">DECLARATION:</div>
                <p>(I) &nbsp; I, hereby declare that I have read and understood the terms and conditions of the
                    allotment of the plot in the project and accept the same.</p>
                <p>(II) &nbsp; I further agree to pay regularly the instalments and dues etc, and abide by all the
                    existing rules and regulations and those, which may be prescribed by BIN ABBASSI ASSOCIATES from
                    time to time.</p>

                <div class="payment-line">
                    I enclose here with sum of Rs.
                    <span class="pay-field" id="field-amount" style="min-width:120px;">{{ $booking->down_payment ? number_format($booking->down_payment) : '&nbsp;' }}</span>,
                    &nbsp;By Bank draft/Pay order No:
                    <span class="pay-field" id="field-payorder" style="min-width:110px;">&nbsp;</span>,
                    &nbsp;Dated:
                    <span class="pay-field" id="field-pay-date" style="min-width:80px;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</span>,
                    &nbsp;drawn on:
                    <span class="pay-field" id="field-drawn-on" style="min-width:100px;">&nbsp;</span>
                    &nbsp; On account of booking of the above plot.
                </div>
            </div>

            {{-- ── Payment Plan Summary ── --}}
            @php
                $appPlotDisc2  = (float)($p->discount_amount ?? 0);
                $appBasePrice  = $appPlotDisc2 > 0 ? $booking->total_price + $appPlotDisc2 : $booking->total_price;
            @endphp
            <div style="margin-top:6px;border:1.5px solid #1a5c20;border-radius:4px;overflow:hidden;">
                <div style="background:#1a5c20;color:#fff;font-size:8.5pt;font-weight:bold;padding:3px 8px;text-transform:uppercase;letter-spacing:0.5px;">
                    Payment Plan Summary
                </div>
                <table style="width:100%;border-collapse:collapse;font-size:8.5pt;">
                    @if($appPlotDisc2 > 0)
                    <tr style="background:#fffbeb;">
                        <td style="padding:3px 8px;font-weight:bold;color:#78350f;border-bottom:1px solid #fde68a;border-right:1px solid #e5e7eb;width:40%;">
                            ★ Plot Discount{{ ($p->discount_reason ?? null) ? ' ('.$p->discount_reason.')' : '' }}
                        </td>
                        <td style="padding:3px 8px;font-weight:900;color:#d97706;border-bottom:1px solid #fde68a;border-right:1px solid #e5e7eb;width:30%;text-align:right;">
                            − PKR {{ number_format($appPlotDisc2) }}
                        </td>
                        <td style="padding:3px 8px;color:#78350f;border-bottom:1px solid #fde68a;width:30%;font-size:8pt;">
                            (baked into agreed price)
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding:3px 8px;font-weight:bold;color:#1a5c20;border-bottom:1px solid #e5e7eb;border-right:1px solid #e5e7eb;">
                            {{ $appPlotDisc2 > 0 ? 'Agreed Price (After Discount)' : 'Total Plot Price' }}
                        </td>
                        <td style="padding:3px 8px;font-weight:900;color:#1a5c20;border-bottom:1px solid #e5e7eb;border-right:1px solid #e5e7eb;text-align:right;">
                            PKR {{ number_format($booking->total_price) }}
                        </td>
                        <td style="padding:3px 8px;border-bottom:1px solid #e5e7eb;"></td>
                    </tr>
                    <tr>
                        <td style="padding:3px 8px;font-weight:bold;border-bottom:1px solid #e5e7eb;border-right:1px solid #e5e7eb;">Down Payment</td>
                        <td style="padding:3px 8px;font-weight:bold;border-bottom:1px solid #e5e7eb;border-right:1px solid #e5e7eb;text-align:right;">PKR {{ number_format($booking->down_payment ?? 0) }}</td>
                        <td style="padding:3px 8px;border-bottom:1px solid #e5e7eb;color:#555;">On booking date</td>
                    </tr>
                    @if($booking->total_installments)
                    <tr>
                        <td style="padding:3px 8px;font-weight:bold;border-bottom:1px solid #e5e7eb;border-right:1px solid #e5e7eb;">Monthly Installments</td>
                        <td style="padding:3px 8px;font-weight:bold;border-bottom:1px solid #e5e7eb;border-right:1px solid #e5e7eb;text-align:right;">PKR {{ number_format($booking->monthly_installment ?? 0) }}</td>
                        <td style="padding:3px 8px;border-bottom:1px solid #e5e7eb;color:#555;">× {{ $booking->total_installments }} months</td>
                    </tr>
                    @endif
                    @if($booking->quarterly_installments)
                    <tr>
                        <td style="padding:3px 8px;font-weight:bold;border-bottom:1px solid #e5e7eb;border-right:1px solid #e5e7eb;">Quarterly Installments</td>
                        <td style="padding:3px 8px;font-weight:bold;border-bottom:1px solid #e5e7eb;border-right:1px solid #e5e7eb;text-align:right;">PKR {{ number_format($booking->quarterly_amount ?? 0) }}</td>
                        <td style="padding:3px 8px;border-bottom:1px solid #e5e7eb;color:#555;">× {{ $booking->quarterly_installments }} quarters</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- ── FOOTER SIGNATURES ── -->
            <div class="footer-signs">
                <div>
                    Date: <span class="sign-field" id="field-date">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</span>
                </div>
                <div>
                    Signature of Applicant: <span class="sign-field" id="field-signature"
                        style="min-width:120px;"></span>
                </div>
            </div>

        </div><!-- /form-body -->

        <!-- ── COLOURFUL BOTTOM STRIPE ── -->
        <div class="footer-stripe" id="footer-stripe"></div>

    </div><!-- /page -->

    <script>
        /* ══════════════════════════════
           BUILD CNIC BOXES
        ══════════════════════════════ */
        function buildCNIC(containerId, value) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            // Format: XXXXX-XXXXXXX-X  (5 + 7 + 1 = 13 digits, 2 dashes = 15 chars)
            const digits = (value || '').replace(/[^0-9]/g, '').split('');
            const positions = [0, 1, 2, 3, 4, 'dash', 5, 6, 7, 8, 9, 10, 11, 'dash', 12];
            positions.forEach((pos, i) => {
                if (pos === 'dash') {
                    const d = document.createElement('div');
                    d.className = 'c-dash';
                    d.textContent = '-';
                    container.appendChild(d);
                } else {
                    const box = document.createElement('div');
                    box.className = 'c-box';
                    box.textContent = digits[pos] || '';
                    container.appendChild(box);
                }
            });
        }

        buildCNIC('cnic-1', '{{ addslashes($c->cnic ?? '') }}');
        buildCNIC('cnic-2', '{{ addslashes($c->nominee_cnic ?? '') }}');
        buildCNIC('cnic-3', '');

        /* ══════════════════════════════
           BUILD FOOTER STRIPE
        ══════════════════════════════ */
        const stripeColors = [
            '#e53935', '#43a047', '#fdd835', '#1e88e5', '#fb8c00',
            '#8e24aa', '#00acc1', '#e53935', '#43a047', '#fdd835',
            '#1e88e5', '#fb8c00', '#8e24aa', '#00acc1', '#e53935',
            '#43a047', '#fdd835', '#1e88e5', '#fb8c00', '#8e24aa'
        ];
        const stripe = document.getElementById('footer-stripe');
        stripeColors.forEach(c => {
            const seg = document.createElement('div');
            seg.className = 'stripe-seg';
            seg.style.background = c;
            stripe.appendChild(seg);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
    document.getElementById('btn-download').addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

        const page = document.getElementById('form-page');
        const canvas = await html2canvas(page, { scale: 2, useCORS: true, backgroundColor: '#ffffff' });
        const link = document.createElement('a');
        link.href     = canvas.toDataURL('image/png');
        link.download = '{{ $booking->customer_booking_id }}-Application.png';
        link.click();

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-download"></i> Download';
    });
    </script>
</body>

</html>
