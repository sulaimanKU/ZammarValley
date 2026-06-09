<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zamar Valley - Application for Transfer of Plot</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap');

        :root {
            --green-dark: #1a5c20;
            --green-mid: #2e7d32;
            --yellow-bg: #d4e157;
            --yellow-dark: #c6cc28;
            --inv-fill: #8fbc3b;

            /* Footer Stripe */
            --zamar-red: #cc0000;
            --zamar-orange: #ff8800;
            --zamar-green: #105e26;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #525659;
            font-family: "Google Sans", sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* ══════════════ A4 PAGE ══════════════ */
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

        /* ══════════════ HEADER ══════════════ */

       .header-section {
            height: 130px; /* Increased from 88px */
            position: relative;
            width: 100%;
            overflow: visible;
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
            margin: 90px;
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
            justify-content: center;
        }

        .header-text-block .org-sub {
            font-size: 14pt;
            color: #333;
            font-weight: 700;
        }

        /* ══════════════ MAIN BANNER ══════════════ */
        .main-banner {
            background: var(--green-dark);
            color: white;
            text-align: center;
            font-size: 13.5pt;
            font-weight: bold;
            padding: 6px 0 5px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .sub-banner {
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            color: #111;
            padding: 3px 0 4px;
        }

        /* ══════════════ BODY WRAPPER ══════════════ */
        .body-wrap {
            column-gap: 10px;
            padding: 6px 14px 0;
        }

        /* Create the 2-column layout for the top part only */
        .top-flex-layout {
            display: grid;
            grid-template-columns: 1fr 95px;
            column-gap: 10px;
            margin-bottom: 10px;
        }

        .left-content {
            display: flex;
            flex-direction: column;
        }

        .to-block {
            font-size: 10pt;
            font-weight: bold;
            line-height: 1.9;
            display: flex;
            justify-content: space-between;
        }

        .to-block .date-line {
            display: flex;
            align-items: flex-end;
            gap: 5px;
            font-size: 9.5pt;
            padding-bottom: 10px;
        }

        .to-block .date-line .dotline {
            border-bottom: 1px dotted #333;
            min-width: 140px;
            height: 16px;
            display: inline-block;
            padding-bottom: 20px;
        }

        .photo-box {
            width: 85px;
            height: 100px;
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

        .photo-box img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .mgmt-box {
            background: var(--yellow-bg);
            border: 1.5px solid #b5b800;
            padding: 9px 10px;
            text-align: center;
            font-weight: bold;
            width: 100%;
            /* Changed from 80% to fill the column space */
            max-width: 550px;
            /* Optional: keep it from getting too wide */
            border-radius: 14px;
            font-size: 14pt;
            margin-bottom: 7px;
        }

        .subject-box {
            background: var(--yellow-bg);
            border: 1.5px solid #b5b800;
            padding: 10px;
            font-size: 11pt;
            font-weight: bold;
            border-radius: 14px;
            line-height: 1.9;
            width: 100%;
            /* Force full width */
        }

        .subj-line {
            display: inline-block;
            border-bottom: 1px solid #333;
            min-width: 100px;
            vertical-align: bottom;
        }

        .subj-line-long {
            min-width: 320px;
        }

        /* ── Body Text Container ── */
        .body-text {
            font-size: 10pt;
            /* Slightly reduced for better A4 fit */
            font-weight: bold;
            line-height: 1.9;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            /* Consistent spacing between rows */
        }

        .dear-line {
            margin-bottom: 5px;
            font-size: 11pt;
        }

        /* Row Styling */
        .f-row {
            display: flex;
            align-items: flex-end;
            margin: 2px 0;
        }

        .f-label {
            white-space: nowrap;
            flex-shrink: 0;
            font-size: 9.5pt;
        }

        .dotline {
            flex: 1;
            border-bottom: 1px dotted #333;
            margin-left: 6px;
        }

        /* Indented start for the first paragraph */
        .indented {
            padding-left: 30px;
        }

        .consideration-para {
            font-size: 10pt;
            font-weight: bold;
            line-height: 1.9;
            /* Increased line-height to give the border some 'breathing room' */
            text-align: justify;
        }

        .iline-money {
            display: inline-block;
            border-bottom: 1px solid #333;
            min-width: 180px;
            vertical-align: bottom;
        }

        .iline-money.dotted {
            border-bottom: 1px dotted #333;
        }
        .qr-container {
            display: flex;
            justify-content: space-between;
        }
        /* QR Code Container */
        .qr-block-with-space {
            width: 100%;
            text-align: right;
            padding-right: 14px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        /* The QR code box itself (no longer absolute) */
        .qr-code-verified {
            width: 65px; /* Adjust size as needed */
            height: 65px;
            border: 1px solid #ccc;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .qr-code-verified img {
            width: 90%;
            height: 90%;
            object-fit: contain;
        }

        /* ── Bottom Columns (Signatures) ── */
        .bottom-cols {
            display: flex;
            justify-content: space-between;
            padding: 5px 14px 20px;
        }

        /* Left: Specimen Box */
        .specimen-col {
            width: 45%;
        }

        .specimen-title {
            font-size: 9pt;
            font-weight: bold;
            line-height: 1.3;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .specimen-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 20px;
            row-gap: 15px;
        }

        .spec-item {
            display: flex;
            align-items: flex-end;
            gap: 5px;
        }

        .spec-line {
            flex: 1;
            border-bottom: 1px dotted #333;
            height: 30px;
            /* Space for physical signature */
        }

        /* Right: Allottee Details */
        .allottee-col {
            width: 45%;
            border-left: 1px solid #eee;
            /* Subtle visual separator */
            padding-left: 20px;
        }

        .allottee-title {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .body-wrap,
        .body-text,
        .bottom-cols,
        .witness-container {
            padding-left: 10mm !important;
            padding-right: 10mm !important;
            width: 100%;
        }

        /* ── Footer Branding ── */
        /* Force the stripe to the very bottom of the A4 boundary */
        .footer-stripe {
            position: absolute;
            /* Lock it to the page bottom */
            bottom: 0;
            /* Stick to the absolute bottom edge */
            left: 0;
            display: flex;
            width: 100%;
            height: 12px;
        }

        .stripe-seg {
            flex: 1;
            height: 100%;
        }

        .stripe-seg.red {
            background-color: var(--zamar-red);
        }

        .stripe-seg.orange {
            background-color: var(--zamar-orange);
        }

        .stripe-seg.green {
            background-color: var(--zamar-green);
        }

        /* ══════════════ PRINT ══════════════ */
        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .page {
                margin: 0 !important;
                box-shadow: none !important;
                width: 210mm;
                height: 297mm;
                position: relative;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                page-break-after: always;
                page-break-inside: avoid;
            }
            .page:last-of-type {
                page-break-after: avoid;
            }

            .footer-stripe {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 12px;
                display: flex !important;
                /* Ensure it stays visible */
            }


            /* 4. Ensure colors show up in PDF/Print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* ── Witness Layout (Page 2) ── */
        #back-page {
            padding: 20mm 15mm;

        }

        .witness-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 20mm;
            gap: 10mm;
            padding: 0 15mm;
        }

        .witness-column {
            width: 46%;
            display: flex;
            flex-direction: column;
            gap: 10mm;
        }

        .witness-header {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-decoration: underline;
            margin-bottom: 5mm;
            text-transform: uppercase;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .label {
            font-size: 9pt;
            font-weight: bold;
            color: #333;
        }

        .line {
            width: 100%;
            border-bottom: 1.5px solid #000;
        }
        line.name {
            height: 0;
        }

        .cnic-row {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            margin-top: 5mm;
        }

        .cnic-grid {
            display: flex;
            border: 1.5px solid #000;
            border-right: none;
            width: 100%; /* Ensure it fills the parent column */
            min-height: 7mm;
        }

       .c-box {
            flex: 1; /* Each box takes equal share of available width */
            height: 7mm;
            border-right: 1.5px solid #000;
            box-sizing: border-box;
        }

        .office-section {
            position: absolute;
            bottom: 30mm;
            left: 15mm;
            right: 15mm;
        }

        .divider-container {
            width: 100%;
            border-top: 1.5px solid #000;
            position: relative;
            margin-bottom: 25mm;
            display: flex;
            justify-content: center;
        }

        .office-badge {
            position: absolute;
            top: -12px;
            background: #444;
            color: white;
            padding: 2px 20px;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .footer-sig-row {
            display: flex;
            justify-content: space-around;
            width: 100%;
        }

        .footer-sig-box {
            width: 65mm;
            text-align: center;
        }

        /* ── Crucial Print Break ── */
        @media print {

    button { display: none !important; }

            .page {
                page-break-after: always !important;
                /* Forces Page 2 to a new sheet */
                margin-bottom: 0 !important;
            }

            #back-page {
                display: flex !important;
                width: 210mm;
                height: 297mm;
                position: relative;
            }

            .c-box {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
        }
    </style>
</head>

<body>


    <!-- Action Buttons (fixed bar, hidden on print) -->
    <div class="no-print" style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#1e293b;padding:10px 20px;display:flex;gap:10px;align-items:center;box-shadow:0 2px 8px rgba(0,0,0,.3);">
        <span style="color:#94a3b8;font-size:12px;font-weight:600;margin-right:auto;">Transfer Application — {{ $transfer->deed_no }}</span>
        <button type="button" onclick="window.print()"
                style="padding:7px 16px;background:#16a34a;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">
            🖨 Print (Duplex)
        </button>
        <button type="button" id="btn-download"
                style="padding:7px 16px;background:#2563eb;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">
            ⬇ Download Pages
        </button>
    </div>
    <!-- Spacer so content isn't hidden behind fixed bar -->
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
                        <path fill="rgb(36,163,79)"
                            d="M 14172,9675 C 14465,9471 15023,8663 15247,8220 15716,7290 15810,6234 15665,5194 L 15559,4646 C 15474,4459 15532,4573 15457,4497 14189,6205 14738,5420 14857,6884 14985,8456 14265,9169 14172,9675 Z" />

                        <path fill="rgb(180,42,40)"
                            d="M 12406,8821 C 12539,9343 12404,9328 12445,9772 12653,9681 12547,9861 12635,9494 12670,9346 12649,9307 12715,9169 12950,9277 13562,9946 13957,9983 13952,9709 13861,9692 13451,9379 13201,9188 13083,9005 12865,8822 13008,8716 14829,8145 13556,7536 12075,6830 12923,8461 12406,8821 Z M 12740,7781 C 12802,8052 12660,7806 12956,7962 L 12851,8594 C 13167,8525 13560,8282 13648,8081 13470,7673 12907,7639 12740,7781 Z" />

                        <path fill="rgb(180,42,40)"
                            d="M 6497,9917 L 6608,9744 C 6792,9356 6596,8972 7616,8867 7612,9102 7532,9581 7694,9762 7962,9388 7910,7969 7821,7777 7695,7502 7744,7816 7588,7470 L 7514,7271 C 6985,7652 7037,7825 6806,8513 6670,8918 6493,8794 6502,9141 6508,9354 6544,9248 6489,9512 L 6497,9917 Z M 6977,8762 L 7608,8675 C 7619,8261 7648,8020 7309,7898 7213,8168 7072,8321 6977,8762 Z" />

                        <path fill="rgb(180,42,40)"
                            d="M 10630,9927 C 11051,9237 10501,9036 11714,8868 L 11731,9767 C 11925,9658 11902,9717 11952,9163 11977,8879 12034,8002 11907,7756 11777,7504 11758,7759 11678,7446 11674,7431 11665,7377 11662,7362 11660,7350 11654,7326 11652,7316 11650,7306 11648,7284 11640,7270 10929,7580 11141,8376 10757,8814 10622,8966 10603,8748 10618,9133 10642,9776 10398,9685 10630,9927 Z M 11079,8762 L 11707,8688 C 11712,8310 11752,8047 11461,7907 11296,8102 11158,8464 11079,8762 Z" />

                        <path fill="rgb(19,21,21)"
                            d="M 6504,11844 C 6967,11857 6735,11876 6977,11573 L 7831,11558 7964,11856 8317,11857 C 8159,11506 7768,10786 7551,10496 7103,10496 7116,10703 6939,11036 6798,11304 6610,11585 6504,11844 Z M 7116,11289 L 7666,11287 7416,10807 7116,11289 Z" />

                        <path fill="rgb(180,42,40)"
                            d="M 8470,9787 C 8752,9595 8723,8888 8766,8548 L 8945,8746 C 9368,9210 9621,8466 9953,8269 9932,8518 9868,8763 9818,9012 L 9725,9578 C 9757,9786 9688,9642 9812,9775 10074,9342 10354,8281 10356,7614 9794,7783 9798,8497 9248,8598 8924,8307 9007,7915 8955,7602 L 8910,7437 C 8906,7423 8899,7402 8890,7386 8419,7463 8571,7716 8491,8448 8452,8803 8302,9544 8470,9787 Z" />
                        <path fill="rgb(241,229,28)"
                            d="M 13170,5119 C 13365,5220 13292,5188 13437,5325 14084,5931 13581,5420 14120,6079 14274,6268 14354,6434 14448,6704 14550,6996 14585,7334 14693,7503 14770,6415 14538,5445 13852,4674 13668,4466 13505,4235 13170,4121 L 13170,5119 Z" />
                        <path fill="rgb(180,42,40)"
                            d="M 4860,7795 C 5148,7754 5545,7696 5766,7848 5866,7950 5736,8126 5613,8334 L 4780,9548 C 4667,9882 4760,9805 4760,9806 L 4793,9913 C 5119,9999 6120,9720 6210,9266 5807,9361 5700,9522 5168,9556 5298,8949 6993,7633 5684,7400 4857,7253 4898,7497 4860,7795 Z" />
                        <path fill="rgb(218,52,49)"
                            d="M 11352,4792 C 11729,5959 12679,5042 14075,6501 13776,5658 12040,4459 11352,4792 Z" />
                        <path fill="rgb(19,21,21)"
                            d="M 4766,10504 L 5338,11569 C 5686,12156 5766,11792 6091,11201 6196,11008 6383,10729 6421,10494 5800,10500 6051,11078 5590,11434 5502,11243 5217,10695 5083,10510 L 4766,10504 Z" />
                        <path fill="rgb(19,21,21)"
                            d="M 14731,11857 C 15048,11857 14994,11923 15058,11731 15064,11715 15060,11286 15061,11229 L 15704,10542 C 15275,10358 15170,10819 14922,10984 14860,10924 14823,10899 14751,10831 14465,10564 14564,10437 14069,10510 14105,10539 14348,10887 14502,11018 14827,11298 14734,11353 14731,11857 Z" />
                        <path fill="rgb(19,21,21)"
                            d="M 8774,11844 L 10107,11860 C 10099,11519 10165,11582 9732,11587 9512,11590 9324,11606 9104,11574 9064,10997 9226,10288 8853,10506 8705,10594 8765,10551 8770,11130 8772,11368 8769,11606 8774,11844 Z" />
                        <path fill="rgb(19,21,21)"
                            d="M 10565,11850 L 11649,11865 C 12071,11827 11820,11910 11906,11729 11864,11636 11955,11580 11462,11590 11266,11593 11088,11599 10891,11581 L 10874,10490 10598,10502 C 10511,10842 10557,11482 10565,11850 Z" />
                        <path fill="rgb(180,42,40)"
                            d="M 10427,5989 L 10678,6122 C 11176,6309 11099,6129 11476,6009 11963,5855 12378,5972 12856,5995 12842,5986 12821,5955 12814,5967 L 12344,5758 C 11752,5596 10905,5615 10427,5989 Z" />
                        <path fill="rgb(27,25,24)"
                            d="M 12675,11579 C 12709,11083 12750,11346 13131,11260 13310,11220 13250,11293 13303,11131 13292,10937 13128,11061 12715,11009 L 12675,10742 C 12717,10616 12689,10646 13134,10652 13313,10654 13495,10652 13674,10652 L 12475,10591 12475,11790 13673,11790 13674,11669 12889,11676 C 12545,11646 12763,11686 12675,11579 Z" />
                        <path fill="rgb(27,25,24)"
                            d="M 12402,11858 L 13764,11845 13767,11626 12675,11579 C 12764,11685 12546,11646 12888,11675 L 13674,11668 13673,11790 12475,11790 12476,10590 13674,10652 C 13494,10652 13314,10654 13134,10651 12689,10645 12718,10615 12675,10742 L 13701,10708 13701,10536 12409,10532 12402,11858 Z" />
                        <path fill="rgb(141,41,42)"
                            d="M 10455,7111 L 10946,7086 C 11082,6561 11318,6418 11737,6192 11427,6176 11102,6364 10952,6498 10792,6641 10490,6920 10455,7111 Z" />
                        <path fill="rgb(27,25,24)"
                            d="M 12401,11858 L 12409,10532 13701,10536 13701,10708 C 13764,10622 13786,10821 13757,10507 L 12372,10474 C 12339,10722 12351,10952 12350,11190 12350,11391 12311,11682 12401,11858 Z" />
                    </svg>
                </div>
                <div class="header-text-block">
                    <span class="org-sub" id="org-sub">(A Project of Bin Abbasi Associates)</span>
                </div>
            </div>
        </header>

        <!-- ── MAIN BANNER ── -->
        <div class="main-banner">APPLICATION FOR THE TRANSFER OF PLOT</div>
        <div class="sub-banner">(For Residential / Commercial Property)</div>

        <!-- ── BODY ── -->
        <div class="body-wrap">
            <div class="top-flex-layout">
                <div class="left-content">
                    <div class="to-block">
                        <div>To,</div>
                        <div class="date-line">
                            Date: <span class="dotline">{{ $transfer->transfer_date?->format('d M Y') }}</span>
                        </div>
                    </div>

                    <div class="mgmt-box">
                        THE MANAGEMENT OF ZAMAR VALLEY<br>
                        BIN ABBASI ASSOCIATES, ISLAMABAD.
                    </div>
                </div>

                <div class="photo-box">
                    @php $toCustomer = $transfer->toCustomer ?? $transfer->toBooking?->customer; @endphp
                    @if($toCustomer?->customer_pic)
                        <img src="{{ asset($toCustomer->customer_pic) }}" style="display:block;">
                    @else
                        <span>Photo</span>
                    @endif
                </div>
            </div>

            @php
                $plot = $transfer->plot ?? $transfer->fromBooking?->plot;
                $fromCustomer = $transfer->fromCustomer ?? $transfer->fromBooking?->customer;
                $toCustomer   = $transfer->toCustomer   ?? $transfer->toBooking?->customer;
            @endphp
            <div class="subject-box">
                <div>
                    Subject: Transfer of Plot No.
                    <span class="subj-line">{{ $plot?->plot_number ?? '—' }}</span>
                    Street <span class="subj-line iline-sm">{{ $plot?->street_number ?? '—' }}</span>
                    Block No. <span class="subj-line">{{ $plot?->block ?? '—' }}</span>
                </div>
                <div>
                    Size <span class="subj-line">{{ ($plot?->size ?? '—') . ' ' . ($plot?->unit ?? '') }}</span>
                    Road <span class="subj-line subj-line-long">{{ ($plot?->street_size ? $plot->street_size.' ft' : '—') }}</span>
                </div>
            </div>
        </div>

        <!-- Body Text -->
        <div class="body-text">

            <div class="dear-line">Dear Sir/Madam</div>

            <!-- I/WE ALLOTTEE row (indented) -->
            <div class="f-row indented">
                <span class="f-label">I/WE ALLOTTEE/ATTORNEY OF ZAMAR VALLEY PLOT NO.</span>
                <span class="dotline">{{ $plot?->plot_number ?? '—' }}</span>
            </div>

            <div class="f-row">
                <span class="f-label">STREET NO.</span>
                <span class="dotline-fixed iline iline-sm"
                    style="margin-left:4px; min-width:70px; border-bottom:1px dotted #333;display:inline-block;">{{ $plot?->street_number ?? '—' }}</span>
                &nbsp;
                <span class="f-label" style="margin-left:4px;">SECTOR.</span>
                <span class="dotline-fixed iline iline-sm"
                    style="margin-left:4px;min-width:70px;border-bottom:1px dotted #333;display:inline-block;">{{ $plot?->sector ?? '—' }}</span>
                &nbsp;
                <span class="f-label" style="margin-left:4px;">ISLAMABAD SIZE.</span>
                <span class="dotline" style="margin-left:4px;">{{ ($plot?->size ?? '—') . ' ' . ($plot?->unit ?? '') }}</span>
            </div>

            <div class="f-row">
                <span class="f-label">TRANSFER LETTER NO.</span>
                <span class="dotline" style="margin-left:4px;">{{ $transfer->deed_no }}</span>
                &nbsp;
                <span class="f-label" style="margin-left:6px; flex-shrink:0;">DATED:</span>
                <span class="dotline" style="margin-left:4px; max-width:100px;">{{ $transfer->transfer_date?->format('d M Y') }}</span>
            </div>

            <div style="margin: 5px 0 4px; font-size:9pt; font-weight:bold;">
                I HAVE NOW DECIDED TO TRANSFER THIS SAID PLOT
            </div>

            <div class="f-row">
                <span class="f-label">MR./MRS./MISS:</span>
                <span class="dotline">{{ $toCustomer?->name ?? '—' }}</span>
            </div>
            <div class="f-row">
                <span class="f-label">S/O, D/O, W/O:</span>
                <span class="dotline">{{ $toCustomer?->guardian_name ?? '—' }}</span>
            </div>
            <div class="f-row">
                <span class="f-label">RESIDENT OF:</span>
                <span class="dotline">{{ $toCustomer?->residential_address ?? $toCustomer?->address ?? '—' }}</span>
            </div>

            <!-- Horizontal divider -->
            <div style="border-top:1px dotted #555; margin:8px 0;"></div>

            <!-- Consideration paragraph -->
            <div class="consideration-para">
                FOR CONSIDERATION OF RS.&nbsp; <span class="iline iline-money">{{ number_format($transfer->remaining_balance_transferred ?? 0) }}</span>&nbsp;FROM WHOM I HAVE RECEIVED THE ENTIRE AMOUNT THE SAID PLOT
                MAY BE TRANSFERRED IN THE NAME OF ABOVE TRANSFEREE WITH ALL RIGHTS, LIABILITIES AND DEPOSITS.
            </div>
            <div class="qr-container">
                <div class="box"></div>
                 @if($qrCode ?? false)
            <td style="width:50px;text-align:right;vertical-align:right; background-color: pink;">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="65">
            </td>
            @endif
            </div>

        </div><!-- /body-text -->

        <!-- ── BOTTOM TWO COLUMNS ── -->
        <div class="bottom-cols">

            <!-- LEFT: Three Specimen Signatures -->
            <div class="specimen-col">
                <div class="specimen-title">
                    THREE SPECIMEN SIGNATURE &amp; THUMBS<br>
                    IMPRESSION ALLOTTEE
                </div>
                <div class="specimen-grid">
                    <div class="spec-item"><span>1.</span>
                        <div class="spec-line" id="spec-1a"></div>
                    </div>
                    <div class="spec-item"><span>1.</span>
                        <div class="spec-line" id="spec-1b"></div>
                    </div>
                    <div class="spec-item"><span>2.</span>
                        <div class="spec-line" id="spec-2a"></div>
                    </div>
                    <div class="spec-item"><span>2.</span>
                        <div class="spec-line" id="spec-2b"></div>
                    </div>
                    <div class="spec-item"><span>3.</span>
                        <div class="spec-line" id="spec-3a"></div>
                    </div>
                    <div class="spec-item"><span>3.</span>
                        <div class="spec-line" id="spec-3b"></div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Allottee Signature block -->
            <div class="allottee-col">
                <div class="allottee-title">SIGNATURE OF<br>ALLOTTEE / ATTORNEY</div>
                <div class="f-row" style="margin:5px 0;">
                    <span class="f-label">NAME:</span>
                    <span class="dotline">{{ $fromCustomer?->name ?? '—' }}</span>
                </div>
                <div class="f-row" style="margin:15px 0;">
                    <span class="f-label">S/O, D/O, W/O:</span>
                    <span class="dotline">{{ $fromCustomer?->guardian_name ?? '—' }}</span>
                </div>
                <div class="f-row" style="margin:15px 0;">
                    <span class="f-label">CNIC NO:</span>
                    <span class="dotline">{{ $fromCustomer?->cnic ?? '—' }}</span>
                </div>
                <div class="f-row" style="margin:15px 0;">
                    <span class="f-label">ADDRESS</span>
                    <span class="dotline">{{ $fromCustomer?->residential_address ?? $fromCustomer?->address ?? '—' }}</span>
                </div>
            </div>

        </div><!-- /bottom-cols -->

        <!-- ── COLORFUL FOOTER STRIPE ── -->
        <div class="footer-stripe" id="footer-stripe">
            <div class="stripe-seg red"></div>
            <div class="stripe-seg orange"></div>
            <div class="stripe-seg green"></div>
        </div>

    </div><!-- /body-wrap -->

    <div class="page" id="back-page">
       <div class="witness-container">
    <!-- Witness 1 -->
    <div class="witness-column">
        <div class="witness-header">Witness No 1</div>

        <div class="input-group">
            <span class="label">SIGNATURE</span>
            <div class="line"></div>
        </div>

        <div class="input-group">
            <span class="label">THUMB</span>
            <div class="line"></div>
        </div>

        <div class="input-group">
            <span class="label">NAME</span>
            <div class="line name">{{ $transfer->witness1_name ?? '' }}</div>
        </div>

        <div class="cnic-row">
            <span class="label">CNIC</span>
            @php $w1digits = str_split(preg_replace('/\D/', '', $transfer->witness1_cnic ?? '')); @endphp
            <div class="cnic-grid">
                @for($i = 0; $i < 13; $i++)
                    <div class="c-box">{{ $w1digits[$i] ?? '' }}</div>
                @endfor
            </div>
        </div>

        @if($transfer->witness1_address)
        <div class="input-group" style="margin-top:6mm;">
            <span class="label">ADDRESS</span>
            <div class="line" style="font-size:8pt;">{{ $transfer->witness1_address }}</div>
        </div>
        @endif
    </div>

    <!-- Witness 2 -->
    <div class="witness-column">
        <div class="witness-header">Witness No 2</div>

        <div class="input-group">
            <span class="label">SIGNATURE</span>
            <div class="line"></div>
        </div>

        <div class="input-group">
            <span class="label">THUMB</span>
            <div class="line"></div>
        </div>

        <div class="input-group">
            <span class="label">NAME</span>
            <div class="line name">{{ $transfer->witness2_name ?? '' }}</div>
        </div>

        <div class="cnic-row">
            <span class="label">CNIC</span>
            @php $w2digits = str_split(preg_replace('/\D/', '', $transfer->witness2_cnic ?? '')); @endphp
            <div class="cnic-grid">
                @for($i = 0; $i < 13; $i++)
                    <div class="c-box">{{ $w2digits[$i] ?? '' }}</div>
                @endfor
            </div>
        </div>

        @if($transfer->witness2_address)
        <div class="input-group" style="margin-top:6mm;">
            <span class="label">ADDRESS</span>
            <div class="line" style="font-size:8pt;">{{ $transfer->witness2_address }}</div>
        </div>
        @endif
    </div>
</div>

        <div class="office-section">
            <div class="divider-container">
                <div class="office-badge">Office Use</div>
            </div>
            <div class="footer-sig-row">
                <div class="footer-sig-box">
                    <div class="line"></div>
                    <div class="label">SIGNATURE</div>
                </div>
                <div class="footer-sig-box">
                    <div class="line"></div>
                    <div class="label">STAMP</div>
                </div>
            </div>
        </div>

        <div class="footer-stripe"></div>
    </div>

    </div><!-- /page -->

<!-- Footer Stripe -->
<div id="footer-stripe" style="display:flex; height:8px;"></div>


<!-- Include html2canvas -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
/* ══════════════════════════════
   FOOTER STRIPE
══════════════════════════════ */
(function buildStripe() {
    const colors = [
        '#9c27b0', '#ff9800', '#f44336', '#4caf50',
        '#9c27b0', '#ff9800', '#f44336', '#4caf50',
        '#9c27b0', '#ff9800', '#f44336', '#4caf50',
        '#9c27b0', '#ff9800', '#f44336', '#4caf50',
        '#9c27b0', '#ff9800', '#f44336', '#4caf50'
    ];
    const stripe = document.getElementById('footer-stripe');
    colors.forEach(c => {
        const s = document.createElement('div');
        s.className = 'stripe-seg';
        s.style.background = c;
        s.style.flex = '1';
        stripe.appendChild(s);
    });
})();

/* ══════════════════════════════
   IMAGE LOADER
══════════════════════════════ */
function loadImage(event, imgId, placeholderId) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = document.getElementById(imgId);
        img.src = e.target.result;
        img.style.display = 'block';
        if (placeholderId) {
            const ph = document.getElementById(placeholderId);
            if (ph) ph.style.display = 'none';
        }
        if (imgId === 'photo-img') document.getElementById('photo-label').style.display = 'none';
    };
    reader.readAsDataURL(file);
}

/* All fields are server-rendered — no JS population needed */

/* ══════════════════════════════
   DOWNLOAD BUTTON
══════════════════════════════ */
document.getElementById('btn-download').addEventListener('click', async function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

    const deed = '{{ $transfer->deed_no }}';
    const pages = document.querySelectorAll('.page');
    const labels = ['Front', 'Back'];

    for (let i = 0; i < pages.length; i++) {
        const canvas = await html2canvas(pages[i], { scale: 2, useCORS: true, backgroundColor: '#ffffff' });
        const link = document.createElement('a');
        link.href     = canvas.toDataURL('image/png');
        link.download = deed + '-' + (labels[i] || ('Page' + (i + 1))) + '.png';
        link.click();
        // Small delay between downloads so browser doesn't block the second one
        await new Promise(r => setTimeout(r, 400));
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-download"></i> Download Pages';
});
</script>
</body>


</html>
