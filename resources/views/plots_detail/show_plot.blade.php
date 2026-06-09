<!DOCTYPE html>
<html>
<head>
    <title>Plot Record - {{ $plot->plot_number }}</title>
    <style>
        @page { margin: 0cm; }
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0; color: #1a1a1a;
            background-color: #fff;
        }

        /* --- EXACT SVG HEADER FROM IMAGE --- */
        .header-svg {
            width: 100%;
            height: 185px;
            display: block;
        }

        .container { padding: 0 1.2cm; position: relative; }

        /* --- DARK GREEN BRANDING STRIP --- */
        .title-strip {
            background-color: #004d31;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 19px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: -22px; /* Pulls up to meet SVG curve */
        }
        .sub-title { text-align: center; font-size: 11px; margin-top: 3px; font-weight: bold; }

        /* --- MANAGEMENT BANNER (YELLOW) --- */
        .management-banner {
            background-color: #e6e94e;
            border: 1.5px solid #c0ca33;
            text-align: center;
            padding: 12px;
            margin: 20px 0;
            font-size: 17px;
            font-weight: bold;
            color: #000;
        }

        /* --- SUBJECT BOX (LIME) --- */
        .subject-box {
            background-color: #f1f8e9;
            border: 1px solid #dcedc8;
            padding: 15px;
            font-size: 14px;
            line-height: 2.2;
            margin-bottom: 25px;
        }

        .dotted-line {
            border-bottom: 1px dotted #000;
            padding: 0 8px;
            font-weight: bold;
            display: inline-block;
            color: #000;
        }

        /* --- MAIN FORM DATA SECTION --- */
        .form-content {
            font-size: 13px;
            line-height: 2.8;
            text-transform: uppercase;
        }

        /* --- CENTERED QR CODE --- */
        .qr-section {
            width: 100%;
            text-align: center;
            margin: 40px 0;
            border: 1px solid #eee;
            padding: 20px 0;
            background: #fafafa;
        }

        /* --- MULTI-COLOR FOOTER --- */
        .footer-strip {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 22px;
            display: flex;
        }
        .f-block { flex: 1; height: 100%; }
    </style>
</head>
<body>

    <svg class="header-svg" viewBox="0 0 800 185" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 0 H800 V160 C600 160 300 190 0 140 Z" fill="#f0f0f0" />
        <path d="M0 0 H800 V120 C550 120 350 170 0 110 Z" fill="#cbd32d" opacity="0.5" />
        <path d="M0 0 H800 V70 C600 70 450 140 0 90 Z" fill="#3a7d34" />
        <path d="M0 0 H320 V105 C200 105 100 95 0 105 Z" fill="white" />
    </svg>

    <div class="container" style="margin-top: -145px;">
        <div style="margin-left: 10px; margin-bottom: 35px;">
            <img src="{{ $config['logo'] }}" style="height: 80px;">
            <div style="font-size: 12px; font-weight: bold; color: #444;">(A Project of Bin Abbasi Associates)</div>
        </div>

        <div class="title-strip">Application For Plot Detail Record</div>
        <div class="sub-title">(Digital Inventory Verification)</div>

        <div class="management-banner">
            THE MANAGEMENT OF ZAMAR VALLEY<br>
            BIN ABBASI ASSOCIATES, ISLAMABAD.
        </div>

        <div class="subject-box">
            <strong>Subject:</strong> Plot No. <span class="dotted-line" style="min-width: 80px;">{{ $plot->plot_number }}</span>
            Street <span class="dotted-line" style="min-width: 150px;">{{ $plot->street_number ?? '---' }}</span>
            Block <span class="dotted-line" style="min-width: 120px;">{{ $plot->block ?? '---' }}</span>
            <br>
            Size <span class="dotted-line" style="min-width: 100px;">{{ $plot->size }} {{ $plot->unit }}</span>
            Sector <span class="dotted-line" style="min-width: 220px;">H-17, Islamabad</span>
        </div>

        <div class="form-content">
            This record verifies that Plot No. <span class="dotted-line" style="min-width: 150px;">{{ $plot->plot_number }}</span>
            Street No. <span class="dotted-line" style="min-width: 100px;">{{ $plot->street_number }}</span>
            Sector <span class="dotted-line" style="min-width: 100px;">H-17</span> Islamabad size
            <span class="dotted-line" style="min-width: 100px;">{{ $plot->size }} {{ $plot->unit }}</span> is currently
            assigned to the inventory of Zamar Valley.
            <br>
            Total Price: <span class="dotted-line" style="min-width: 150px;">PKR {{ number_format($plot->base_price) }}</span>
            Down Payment: <span class="dotted-line" style="min-width: 150px;">PKR {{ number_format($plot->down_payment) }}</span>
            <br>
            Installment Plan: <span class="dotted-line" style="min-width: 80px;">{{ $plot->total_installments }}</span> Months
            Monthly Installment: <span class="dotted-line" style="min-width: 150px;">PKR {{ number_format($plot->installment_amount) }}</span>
        </div>

        <div class="qr-section">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="110">
            <div style="font-size: 10px; font-weight: bold; margin-top: 8px; color: #3a7d34; letter-spacing: 2px;">
                OFFICIAL SYSTEM VERIFICATION
            </div>
        </div>
    </div>

    @php
        $spPhones = array_filter([$config['phone'] ?? '', $config['phone2'] ?? '', $config['phone3'] ?? '']);
    @endphp
    <div style="position:fixed;bottom:22px;width:100%;text-align:center;font-size:9px;color:#444;background:#fff;padding:4px 0;border-top:1px solid #e0e0e0;">
        {{ $config['name'] ?? 'Zamar Valley' }}
        @if($spPhones) &nbsp;·&nbsp; {{ implode(' · ', $spPhones) }} @endif
        @if($config['email'] ?? '') &nbsp;·&nbsp; {{ $config['email'] }} @endif
        @if($config['address'] ?? '') &nbsp;·&nbsp; {{ $config['address'] }} @endif
    </div>

    <div class="footer-strip">
        <div class="f-block" style="background:#3a7d34;"></div>
        <div class="f-block" style="background:#6b2e8d;"></div>
        <div class="f-block" style="background:#f9b31a;"></div>
        <div class="f-block" style="background:#333333;"></div>
        <div class="f-block" style="background:#3a7d34;"></div>
        <div class="f-block" style="background:#6b2e8d;"></div>
        <div class="f-block" style="background:#f9b31a;"></div>
        <div class="f-block" style="background:#333333;"></div>
    </div>

</body>
</html>
