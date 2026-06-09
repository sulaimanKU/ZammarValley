<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Possession Letter — {{ $booking->customer_booking_id }}</title>
<style>
@page { size: A4 portrait; margin: 0.3in 0.35in; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Helvetica, Arial, sans-serif; font-size: 9px; color: #1a1a2e; background: #fff; line-height: 1.4; }

.page-frame { border: 3px solid #1e3a8a; padding: 0; position: relative; }
.page-frame-inner { border: 1px solid #93c5fd; margin: 4px; padding: 0; }
.corner { position: absolute; width: 20px; height: 20px; }
.corner-tl { top: 8px; left: 8px; border-top: 2px solid #1e3a8a; border-left: 2px solid #1e3a8a; }
.corner-tr { top: 8px; right: 8px; border-top: 2px solid #1e3a8a; border-right: 2px solid #1e3a8a; }
.corner-bl { bottom: 8px; left: 8px; border-bottom: 2px solid #1e3a8a; border-left: 2px solid #1e3a8a; }
.corner-br { bottom: 8px; right: 8px; border-bottom: 2px solid #1e3a8a; border-right: 2px solid #1e3a8a; }

.watermark { position: fixed; top: 32%; left: 10%; font-size: 80px; font-weight: 900; color: rgba(30,58,138,0.025); transform: rotate(-35deg); letter-spacing: 6px; text-transform: uppercase; pointer-events: none; z-index: -1; white-space: nowrap; }

.doc-header { background: #1e3a8a; padding: 0; text-align: center; }
.header-top-strip { background: #fbbf24; height: 4px; width: 100%; }
.header-content { padding: 5px 16px 4px; }
.society-name { font-size: 20px; font-weight: 900; color: #ffffff; letter-spacing: 3px; text-transform: uppercase; line-height: 1; }
.society-tagline { font-size: 7.5px; color: #93c5fd; letter-spacing: 2.5px; text-transform: uppercase; margin-top: 2px; }
.society-address { font-size: 7.5px; color: rgba(255,255,255,0.55); margin-top: 3px; letter-spacing: 0.5px; }
.header-divider { height: 1px; background: rgba(255,255,255,0.15); margin: 3px 12px 0; }
.doc-type-banner { background: rgba(251,191,36,0.15); border: 1px solid rgba(251,191,36,0.4); margin: 3px 12px 0; padding: 3px 0; text-align: center; }
.doc-type-text { font-size: 13px; font-weight: 900; color: #fbbf24; letter-spacing: 6px; text-transform: uppercase; }
.header-bottom-strip { background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 50%, #fbbf24 100%); height: 3px; margin-top: 4px; }

.ref-bar { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 3px 14px; }
.ref-bar table { width: 100%; }
.ref-lbl { font-size: 7.5px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; }
.ref-val { font-size: 8px; font-weight: 800; color: #1e3a8a; }
.ref-val-sm { font-size: 9px; font-weight: 700; color: #334155; }

.doc-body { padding: 5px 12px; }

.cert-banner { text-align: center; padding: 3px 8px; margin-bottom: 5px; border-top: 2px solid #1e3a8a; border-bottom: 2px solid #1e3a8a; }
.cert-banner-bg { background: #eff6ff; border: 1px solid #bfdbfe; padding: 4px 8px; }
.cert-checkmark { font-size: 13px; color: #15803d; font-weight: 900; display: block; margin-bottom: 1px; }
.cert-title { font-size: 11px; font-weight: 900; color: #1e3a8a; text-transform: uppercase; letter-spacing: 3px; line-height: 1; }
.cert-subtitle { font-size: 7.5px; color: #3b82f6; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px; }

.sec-head { font-size: 7.5px; font-weight: 900; color: #fff; text-transform: uppercase; letter-spacing: 1.2px; background: #1e3a8a; padding: 2px 7px; margin-bottom: 0; display: block; }

.info-table { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; margin-bottom: 5px; }
.info-table td { padding: 3px 7px; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; vertical-align: top; }
.info-table tr:last-child td { border-bottom: none; }
.i-lbl { font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; width: 28%; background: #fafbfc; }
.i-val { font-size: 9px; font-weight: 700; color: #1e293b; width: 22%; }
.i-val-blue { font-size: 10px; font-weight: 900; color: #1e3a8a; }
.i-val-green { font-size: 10px; font-weight: 900; color: #15803d; }

.letter-box { border: 1px solid #e2e8f0; border-left: 3px solid #1e3a8a; padding: 5px 8px; margin-bottom: 4px; background: #fff; font-size: 9px; line-height: 1.3; color: #1e293b; }
.letter-box p { margin: 0 0 4px; }
.letter-box p:last-child { margin-bottom: 0; }

.conditions-table { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; margin-bottom: 5px; }
.conditions-table td { padding: 1.5px 7px; border-bottom: 1px solid #f1f5f9; vertical-align: top; font-size: 8px; color: #334155; line-height: 1.1; }
.conditions-table tr:last-child td { border-bottom: none; }
.cond-num { width: 20px; font-weight: 900; color: #1e3a8a; font-size: 9px; text-align: center; background: #eff6ff; border-right: 1px solid #dbeafe !important; }

.payment-confirmed { background: #f0fdf4; border: 1.5px solid #86efac; padding: 4px 10px; margin-bottom: 5px; text-align: center; }
.payment-confirmed-title { font-size: 9px; font-weight: 900; color: #15803d; text-transform: uppercase; letter-spacing: 2px; }
.payment-confirmed-sub { font-size: 7.5px; color: #166534; margin-top: 1px; }


.sig-area { margin-top: 1px; border-top: 2px solid #e2e8f0; padding-top: 1px; }
.sig-box { text-align: center; }
.sig-space { height: 16px; border-bottom: 1px solid #94a3b8; margin-bottom: 3px; }
.sig-name { font-size: 8px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; }
.sig-title { font-size: 8px; color: #64748b; }
.sig-cnic { font-size: 7px; color: #94a3b8; font-family: monospace; }

.seal-table { width: 72px; height: 28px; border: 2px solid #1e3a8a; border-collapse: collapse; margin: 0 auto 4px; }
.seal-table td { text-align: center; vertical-align: middle; font-size: 7px; color: #1e3a8a; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.3; border: none; }

.doc-footer { background: #f8fafc; border-top: 2px solid #e2e8f0; padding: 2px 14px; text-align: center; }
.footer-text { font-size: 7px; color: #94a3b8; line-height: 1; }
.footer-ref { font-size: 7px; font-weight: 800; color: #1e3a8a; }
</style>
</head>
<body>

@php
    // ══ Pull everything from $sc (passed by controller via societyConfig()) ══
    $socName    = $sc['name']           ?? 'Zamar Valley';
    $socTagline = $sc['tagline']        ?? 'Premium Residential Society';
    $socPhone   = $sc['phone']           ?? '';
    $socPhone2  = $sc['phone2']          ?? '';
    $socPhone3  = $sc['phone3']          ?? '';
    $socEmail   = $sc['email']           ?? '';
    $socAddress = $sc['address']        ?? '';
    $socLogo    = $sc['logo']           ?? null;
    $watermark  = $sc['watermark']      ?? strtoupper($socName);
    $showLogo   = $sc['show_logo']      ?? true;
    $footerNote = $sc['receipt_footer'] ?? '';

    // Logo absolute path — dompdf needs a real filesystem path, not a URL
    $logoPath    = ($showLogo && $socLogo)
                    ? storage_path('app/public/' . ltrim($socLogo, '/'))
                    : null;
    $showLogoImg = $logoPath && file_exists($logoPath);

    // Totals — only count real (non-external) paid records
    $totalPaid = $booking->payments->where('status','paid')->where('is_external',false)->sum('amount_paid');
    $issueDate = now()->format('d F Y');
    $letterNo  = 'ZV/PL/' . date('Y') . '/' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
@endphp

{{-- Watermark from SystemConfig --}}
<div class="watermark">{{ $watermark }}</div>

<div class="page-frame">
    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>

    <div class="page-frame-inner">

        {{-- ══ HEADER ══ --}}
        <div class="doc-header">
            <div class="header-top-strip"></div>
            <div class="header-content">

                @if($showLogoImg)
                {{-- Logo left, text centre, blank right to balance --}}
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:58px; vertical-align:middle; text-align:left; border:none; padding:0 10px 0 0;">
                            <img src="{{ $logoPath }}" width="50" height="40"
                                 style="object-fit:contain; border-radius:6px; border:1.5px solid rgba(255,255,255,0.25);"
                                 alt="{{ $socName }}">
                        </td>
                        <td style="vertical-align:middle; text-align:center; border:none; padding:0;">
                            <div class="society-name">{{ $socName }}</div>
                            <div class="society-tagline">{{ $socTagline }}</div>
                            <div class="society-address">
                                {{ $socAddress }}
                                @if($socPhone) &nbsp;·&nbsp; {{ $socPhone }} @endif
                                @if($socEmail) &nbsp;·&nbsp; {{ $socEmail }} @endif
                            </div>
                        </td>
                        <td style="width:58px; border:none;"></td>
                    </tr>
                </table>
                @else
                <div class="society-name">{{ $socName }}</div>
                <div class="society-tagline">{{ $socTagline }}</div>
                <div class="society-address">
                    {{ $socAddress }}
                    @if($socPhone) &nbsp;·&nbsp; {{ $socPhone }} @endif
                    @if($socEmail) &nbsp;·&nbsp; {{ $socEmail }} @endif
                </div>
                @endif

                <div class="header-divider"></div>
                <div class="doc-type-banner">
                    <div class="doc-type-text">Possession Letter</div>
                </div>
            </div>
            <div class="header-bottom-strip"></div>
        </div>

        {{-- ══ REF BAR ══ --}}
        <div class="ref-bar">
            <table>
                <tr>
                    <td width="25%">
                        <div class="ref-lbl">Letter No.</div>
                        <div class="ref-val">{{ $letterNo }}</div>
                    </td>
                    <td width="25%">
                        <div class="ref-lbl">Booking Ref</div>
                        <div class="ref-val">{{ $booking->customer_booking_id }}</div>
                    </td>
                    <td width="25%">
                        <div class="ref-lbl">Issue Date</div>
                        <div class="ref-val-sm">{{ now()->format('d M Y') }}</div>
                    </td>
                    <td width="25%" style="text-align:right;">
                        <span style="background:#dcfce7; color:#15803d; padding:3px 10px; border:1px solid #86efac; font-size:8px; font-weight:900; text-transform:uppercase; letter-spacing:0.5px;">
                            ✓ ALL DUES CLEARED
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ══ MAIN BODY ══ --}}
        <div class="doc-body">

            <div class="cert-banner">
                <div class="cert-banner-bg">
                    <span class="cert-checkmark">✓</span>
                    <div class="cert-title">Plot Possession Certificate</div>
                    <div class="cert-subtitle">Full payment received &nbsp;·&nbsp; Possession hereby granted &nbsp;·&nbsp; {{ $issueDate }}</div>
                </div>
            </div>

            {{-- Letter body + QR side-by-side --}}
            <table style="width:100%; margin-bottom:4px;">
                <tr>
                    <td width="{{ $qrCode ? '76%' : '100%' }}" valign="top" style="border:none; padding-right:{{ $qrCode ? '10px' : '0' }};">
                        <div class="letter-box">
                            <p><strong>To,</strong></p>
                            <p style="font-size:12px; font-weight:900; color:#1e3a8a; margin-bottom:2px;">{{ $booking->customer->name ?? '—' }}</p>
                            <p style="color:#475569; margin-bottom:2px;">{{ $booking->customer->address ?? 'Address not on record' }}</p>
                            <p style="color:#475569; margin-bottom:5px;">CNIC No. &nbsp;<strong style="font-family:monospace;">{{ $booking->customer->cnic ?? '—' }}</strong></p>
                            <p><strong>Subject:</strong> &nbsp;Possession Letter for Plot #{{ $booking->plot->plot_number ?? '—' }}, Block {{ $booking->plot->block ?? '—' }}, {{ $socName }}</p>
                            <p style="margin-top:5px;">Dear <strong>{{ $booking->customer->name ?? 'Valued Customer' }}</strong>,</p>
                            <p>With reference to Booking No. <strong>{{ $booking->customer_booking_id }}</strong> dated <strong>{{ date('d M Y', strtotime($booking->booking_date)) }}</strong>, we are pleased to inform you that you have <strong>successfully completed all financial obligations</strong> against the above-referenced plot. The Management of <strong>{{ $socName }}</strong> hereby grants you <strong>official physical possession</strong> of the property described below.</p>
                            <p>Please carry this letter when visiting the site for possession. The plot boundaries will be shown by our site staff upon presentation of this document.</p>
                        </div>
                    </td>
                    @if($qrCode)
                    <td width="24%" valign="top" style="border:none;">
                        <table style="width:100%; border:1px solid #dbeafe; border-collapse:collapse;">
                            <tr>
                                <td style="background:#eff6ff; padding:2px 0; text-align:center; border:none; font-size:7px; font-weight:800; color:#1e3a8a; text-transform:uppercase; letter-spacing:1px;">VERIFY ONLINE</td>
                            </tr>
                            <tr>
                                <td style="text-align:center; padding:8px; border:none;">
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="84" height="84" alt="QR">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:center; padding:0 6px 8px; border:none;">
                                    <div style="font-size:7px; color:#64748b; line-height:1.5;">Scan to verify<br>payment ledger</div>
                                    <div style="font-size:8px; font-weight:900; color:#1e3a8a; margin-top:3px; font-family:monospace;">{{ $booking->customer_booking_id }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    @endif
                </tr>
            </table>

            <span class="sec-head">I. &nbsp; Property Particulars</span>
            <table class="info-table">
                <tr>
                    <td class="i-lbl">Plot Number</td>
                    <td class="i-val i-val-blue">#{{ $booking->plot->plot_number ?? '—' }}</td>
                    <td class="i-lbl">Block</td>
                    <td class="i-val i-val-blue">{{ $booking->plot->block ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="i-lbl">Street No.</td>
                    <td class="i-val">{{ $booking->plot->street_number ?? '—' }}</td>
                    <td class="i-lbl">Street Width</td>
                    <td class="i-val">{{ $booking->plot->street_size ? $booking->plot->street_size.' ft' : '—' }}</td>
                </tr>
                <tr>
                    <td class="i-lbl">Measured Area</td>
                    <td class="i-val i-val-blue">{{ $booking->plot->size ?? '—' }} {{ $booking->plot->unit ?? '' }}</td>
                    <td class="i-lbl">Category</td>
                    <td class="i-val">{{ $booking->plot->category->name ?? 'Residential' }}</td>
                </tr>
                <tr>
                    <td class="i-lbl">Sector</td>
                    <td class="i-val">{{ $booking->plot->sector ?? '—' }}</td>
                    <td class="i-lbl">City</td>
                    <td class="i-val">{{ $booking->plot->city ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="i-lbl">Society / Project</td>
                    <td class="i-val">{{ $booking->plot->society ?? $socName }}</td>
                    <td class="i-lbl">Price Type</td>
                    <td class="i-val">{{ ucfirst($booking->plot->price_type ?? 'installment') }}</td>
                </tr>
            </table>

            <span class="sec-head">II. &nbsp; Allottee Information</span>
            {{-- Photo + info side by side --}}
            <table style="width:100%;border-collapse:collapse;border:1px solid #e2e8f0;margin-bottom:8px;">
                <tr>
                    <td style="border:none;padding:0;vertical-align:top;width:68px;">
                        <div style="width:60px;height:75px;border:1.5px solid #cbd5e1;margin:4px 6px;overflow:hidden;text-align:center;background:#f8fafc;display:flex;align-items:center;justify-content:center;">
                            @if(!empty($customerPicB64))
                                <img src="{{ $customerPicB64 }}" width="60" height="75" style="object-fit:cover;display:block;">
                            @else
                                <div style="font-size:7px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.4px;text-align:center;line-height:1.5;padding:4px;">PHOTO</div>
                            @endif
                        </div>
                    </td>
                    <td style="border:none;padding:0;vertical-align:top;">
                        <table class="info-table" style="margin-bottom:0;border:none;">
                            <tr>
                                <td class="i-lbl">Full Name</td>
                                <td class="i-val i-val-blue" colspan="3"><strong>{{ $booking->customer->name ?? '—' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="i-lbl">Father / Guardian</td>
                                <td class="i-val" colspan="3">{{ $booking->customer->guardian_name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="i-lbl">CNIC No.</td>
                                <td class="i-val" style="font-family:monospace;">{{ $booking->customer->cnic ?? '—' }}</td>
                                <td class="i-lbl">Mobile</td>
                                <td class="i-val" style="font-family:monospace;">{{ $booking->customer->mobile ?? $booking->customer->phone ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="i-lbl">Phone</td>
                                <td class="i-val" style="font-family:monospace;">{{ $booking->customer->phone ?? '—' }}</td>
                                <td class="i-lbl">Email</td>
                                <td class="i-val">{{ $booking->customer->email ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="i-lbl">Residential Address</td>
                                <td class="i-val" colspan="3">{{ $booking->customer->residential_address ?? $booking->customer->address ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="i-lbl">Booking Ref.</td>
                                <td class="i-val" style="font-family:monospace;">{{ $booking->customer_booking_id }}</td>
                                <td class="i-lbl">Booking Date</td>
                                <td class="i-val">{{ date('d M Y', strtotime($booking->booking_date)) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- ══ TRANSFER HISTORY — only shown if booking_type = Transfer ══ --}}
            @if(($booking->booking_type ?? '') === 'Transfer' && isset($transferChain) && $transferChain->isNotEmpty())
            <span class="sec-head">III. &nbsp; Transfer / Ownership History</span>
            <table class="info-table" style="margin-bottom:8px;">
                <tr>
                    <td class="i-lbl" style="background:#fffbeb;">Previous Owner</td>
                    <td class="i-val" colspan="3" style="font-weight:800;color:#92400e;">
                        {{ $transferChain->first()->fromCustomer->name ?? '—' }}
                        &nbsp;·&nbsp;
                        <span style="font-family:monospace;font-size:9px;">{{ $transferChain->first()->fromCustomer->cnic ?? '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="i-lbl" style="background:#fffbeb;">Current Owner</td>
                    <td class="i-val" colspan="3" style="font-weight:800;color:#15803d;">
                        {{ $booking->customer->name ?? '—' }}
                        &nbsp;·&nbsp;
                        <span style="font-family:monospace;font-size:9px;">{{ $booking->customer->cnic ?? '' }}</span>
                    </td>
                </tr>
            </table>
            {{-- Transfer chain table --}}
            <table class="conditions-table" style="margin-bottom:10px;">
                <tr style="background:#fafbfc;">
                    <td style="font-size:8px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.5px;width:20px;">#</td>
                    <td style="font-size:8px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">From</td>
                    <td style="font-size:8px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">To</td>
                    <td style="font-size:8px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Deed No.</td>
                    <td style="font-size:8px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Transfer Date</td>
                    <td style="font-size:8px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Fee</td>
                </tr>
                @foreach($transferChain as $i => $tr)
                <tr>
                    <td class="cond-num">{{ $i + 1 }}</td>
                    <td style="font-size:9.5px;font-weight:700;color:#92400e;">{{ $tr->fromCustomer->name ?? '—' }}</td>
                    <td style="font-size:9.5px;font-weight:700;color:#15803d;">{{ $tr->toCustomer->name ?? '—' }}</td>
                    <td style="font-size:9px;font-family:monospace;color:#1e3a8a;">{{ $tr->deed_no ?? '—' }}</td>
                    <td style="font-size:9px;color:#475569;">{{ $tr->transfer_date ? date('d M Y', strtotime($tr->transfer_date)) : '—' }}</td>
                    <td style="font-size:9px;font-weight:700;color:#334155;">PKR {{ number_format($tr->transfer_fee ?? 0) }}</td>
                </tr>
                @endforeach
            </table>
            @endif

            <span class="sec-head">{{ ($booking->booking_type ?? '') === 'Transfer' && isset($transferChain) && $transferChain->isNotEmpty() ? 'IV.' : 'III.' }} &nbsp; Financial Clearance Certificate</span>
            <div class="payment-confirmed">
                <div class="payment-confirmed-title">✓ &nbsp; Full Payment Received &nbsp; ✓</div>
                <div class="payment-confirmed-sub">All financial obligations have been cleared. Balance outstanding: PKR 0 (Nil)</div>
            </div>
            @php
                $plotPriceCats   = ['down_payment','installment','quarterly_installment','plot_balance','others'];
                $discSentinel_p  = 'Settlement discount — waived amount (not collected).';
                $isTransferIn    = !is_null($booking->parent_booking_id);
                $parentBook      = $parentBooking ?? null;

                // Payments made by THIS booking holder
                $totalPlotPaid   = $booking->payments->where('status','paid')
                    ->whereIn('payment_category',$plotPriceCats)
                    ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel_p)
                    ->sum('amount_paid');

                $downPaid        = $booking->payments->where('status','paid')->where('payment_category','down_payment')->sum('amount_paid');
                $installPaid     = $booking->payments->where('status','paid')->where('payment_category','installment')->sum('amount_paid');
                $qtrPaid         = $booking->payments->where('status','paid')->where('payment_category','quarterly_installment')->sum('amount_paid');

                // Payment-level discount credits (settlement waivers) for THIS booking
                $payDiscountP    = $booking->payments->where('status','paid')
                    ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel_p)
                    ->sum('discount_amount')
                    + $booking->payments->where('status','paid')
                    ->filter(fn($p) => ($p->remarks ?? '') === $discSentinel_p)
                    ->sum('amount_paid');

                if ($isTransferIn) {
                    // For transfer bookings: plot discount belongs to the original booking.
                    // total_price = remaining balance taken over — do NOT add plot discount to it.
                    $plotDiscountP   = 0;
                    $plotDiscReasonP = null;
                    $basePriceP      = (float)($booking->plot->base_price ?? $booking->total_price);
                    $fullPlotNetP    = $basePriceP - (float)($booking->plot->discount_amount ?? 0);
                    // Amount paid by previous owner(s) = full net price − this booking's obligation
                    $paidByPrevious  = max(0, $fullPlotNetP - (float)$booking->total_price);
                } else {
                    $plotDiscountP   = (float)($booking->plot->discount_amount ?? 0);
                    $plotDiscReasonP = $booking->plot->discount_reason ?? null;
                    $basePriceP      = $plotDiscountP > 0 ? $booking->total_price + $plotDiscountP : $booking->total_price;
                    $fullPlotNetP    = (float)$booking->total_price;
                    $paidByPrevious  = 0;
                }

                $totalCreditsP   = $totalPlotPaid + $payDiscountP;
                $outstanding     = max(0, (float)$booking->total_price - $totalCreditsP);
            @endphp

            {{-- Discount highlight banner (shown only when discounts exist) --}}
            {{-- Transfer-in: show full plot price context --}}
            @if($isTransferIn)
            <table style="width:100%;border-collapse:collapse;margin-bottom:6px;background:#f5f3ff;border:1.5px solid #ddd6fe;">
                <tr>
                    <td style="padding:4px 8px;font-size:8px;font-weight:900;color:#5b21b6;text-transform:uppercase;letter-spacing:.5px;border:none;" colspan="4">
                        ↓ &nbsp;Transfer Booking — Full Plot Price History
                    </td>
                </tr>
                <tr style="background:#ede9fe;">
                    <td class="i-lbl" style="background:#ede9fe;color:#5b21b6;">Full Plot Value (Base)</td>
                    <td class="i-val" style="color:#5b21b6;font-weight:900;">PKR {{ number_format($basePriceP) }}</td>
                    <td class="i-lbl" style="background:#ede9fe;color:#5b21b6;">Plot Discount (Original)</td>
                    <td class="i-val" style="color:#7c3aed;">{{ ($booking->plot->discount_amount ?? 0) > 0 ? 'PKR '.number_format($booking->plot->discount_amount) : '—' }}</td>
                </tr>
                <tr>
                    <td class="i-lbl">Net Plot Price</td>
                    <td class="i-val i-val-blue">PKR {{ number_format($fullPlotNetP) }}</td>
                    <td class="i-lbl">Paid by Previous Owner(s)</td>
                    <td class="i-val" style="color:#15803d;font-weight:800;">PKR {{ number_format($paidByPrevious) }}</td>
                </tr>
                <tr style="background:#fdf4ff;">
                    <td class="i-lbl" style="background:#fdf4ff;color:#7c3aed;font-weight:900;">Your Obligation (Transferred)</td>
                    <td class="i-val" style="color:#7c3aed;font-weight:900;">PKR {{ number_format($booking->total_price) }}</td>
                    <td class="i-lbl" style="background:#fdf4ff;">You Paid</td>
                    <td class="i-val" style="color:#15803d;font-weight:900;">PKR {{ number_format($totalPlotPaid) }}</td>
                </tr>
            </table>
            @endif

            {{-- Standard discount banner (non-transfer bookings only) --}}
            @if(!$isTransferIn && ($plotDiscountP > 0 || $payDiscountP > 0))
            <table style="width:100%;border-collapse:collapse;margin-bottom:6px;background:#fffbeb;border:1.5px solid #fde68a;">
                <tr>
                    <td style="padding:4px 8px;font-size:8px;font-weight:900;color:#92400e;text-transform:uppercase;letter-spacing:.5px;border:none;" colspan="4">
                        ★ &nbsp;Discount(s) Applied to This Booking
                    </td>
                </tr>
                @if($plotDiscountP > 0)
                <tr style="background:#fef9c3;">
                    <td class="i-lbl" style="background:#fef9c3;color:#78350f;">Plot Discount{{ $plotDiscReasonP ? ' — '.$plotDiscReasonP : ' (at booking)' }}</td>
                    <td class="i-val" style="color:#d97706;font-weight:900;">PKR {{ number_format($plotDiscountP) }}</td>
                    <td class="i-lbl" style="background:#fef9c3;color:#78350f;">Base Price (Before Discount)</td>
                    <td class="i-val" style="color:#92400e;text-decoration:line-through;">PKR {{ number_format($basePriceP) }}</td>
                </tr>
                @endif
                @if($payDiscountP > 0)
                <tr style="background:#f0fdf4;">
                    <td class="i-lbl" style="background:#f0fdf4;color:#166534;">Full-Payment Discount (Waived)</td>
                    <td class="i-val" style="color:#16a34a;font-weight:900;">PKR {{ number_format($payDiscountP) }}</td>
                    <td class="i-lbl" style="background:#f0fdf4;color:#166534;">Total Cash Paid</td>
                    <td class="i-val" style="color:#15803d;font-weight:900;">PKR {{ number_format($totalPlotPaid) }}</td>
                </tr>
                @endif
                <tr style="background:#fef3c7;">
                    <td class="i-lbl" style="background:#fef3c7;color:#92400e;font-weight:900;" colspan="2">Total Savings (All Discounts)</td>
                    <td class="i-val" style="color:#d97706;font-weight:900;" colspan="2">PKR {{ number_format($plotDiscountP + $payDiscountP) }}</td>
                </tr>
            </table>
            @endif

            <table class="info-table">
                @if(!$isTransferIn && $plotDiscountP > 0)
                <tr style="background:#fffbeb;">
                    <td class="i-lbl" style="background:#fffbeb;">Agreed Price (After Discount)</td>
                    <td class="i-val i-val-blue">PKR {{ number_format($booking->total_price) }}</td>
                    <td class="i-lbl">Total Paid (Cash)</td>
                    <td class="i-val i-val-green">PKR {{ number_format($totalPlotPaid) }}</td>
                </tr>
                @elseif(!$isTransferIn)
                <tr>
                    <td class="i-lbl">Total Plot Price</td>
                    <td class="i-val i-val-blue">PKR {{ number_format($booking->total_price) }}</td>
                    <td class="i-lbl">Total Paid</td>
                    <td class="i-val i-val-green">PKR {{ number_format($totalPlotPaid) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="i-lbl">Down Payment Paid</td>
                    <td class="i-val">PKR {{ number_format($downPaid) }}</td>
                    <td class="i-lbl">Installments Paid</td>
                    <td class="i-val">PKR {{ number_format($installPaid) }}</td>
                </tr>
                <tr>
                    <td class="i-lbl">Quarterly Paid</td>
                    <td class="i-val">PKR {{ number_format($qtrPaid) }}</td>
                    <td class="i-lbl">No. of Payments</td>
                    <td class="i-val">{{ $booking->payments->where('status','paid')->count() }} receipts</td>
                </tr>
                @if($payDiscountP > 0)
                <tr style="background:#f0fdf4;">
                    <td class="i-lbl" style="background:#f0fdf4;color:#166534;">Full-Payment Discount</td>
                    <td class="i-val" style="color:#16a34a;font-weight:900;">PKR {{ number_format($payDiscountP) }}</td>
                    <td class="i-lbl" style="background:#f0fdf4;color:#166534;">Total Credits</td>
                    <td class="i-val" style="color:#1d4ed8;font-weight:900;">PKR {{ number_format($totalCreditsP) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="i-lbl">Outstanding Balance</td>
                    <td class="i-val" style="color:#15803d; font-weight:900;">PKR {{ number_format($outstanding) }} {{ $outstanding == 0 ? '(Nil)' : '' }}</td>
                    <td class="i-lbl">Possession Date</td>
                    <td class="i-val">{{ $issueDate }}</td>
                </tr>
            </table>

            <span class="sec-head">@php echo (($booking->booking_type ?? '') === 'Transfer' && isset($transferChain) && $transferChain->isNotEmpty()) ? 'V.' : 'IV.'; @endphp &nbsp; Terms &amp; Conditions of Possession</span>
            <table class="conditions-table">
                <tr>
                    <td class="cond-num">1</td>
                    <td>The allottee is hereby authorized to take <strong>physical possession</strong> of the above-mentioned plot with effect from <strong>{{ $issueDate }}</strong>, the date of issuance of this letter.</td>
                </tr>
                <tr>
                    <td class="cond-num">2</td>
                    <td>All construction on the plot must be carried out strictly in accordance with the <strong>approved building plan</strong> and {{ $socName }} Society bye-laws. Unauthorized construction may result in penalties or demolition.</td>
                </tr>
                <tr>
                    <td class="cond-num">3</td>
                    <td>Any future <strong>development charges, maintenance fees, utility connections</strong>, or government taxes applicable to the property shall be the sole responsibility of the allottee.</td>
                </tr>
                <tr>
                    <td class="cond-num">4</td>
                    <td>This Possession Letter shall serve as the <strong>official handover document</strong>. Registry and mutation proceedings may be initiated upon presentation of this letter along with other required documents.</td>
                </tr>
                <tr>
                    <td class="cond-num">5</td>
                    <td>This is a <strong>legal document</strong>. Any dispute arising from this possession or the underlying agreement shall be subject to the jurisdiction of courts applicable to the location of {{ $socName }}.</td>
                </tr>
            </table>


            <div class="sig-area">
                <table style="width:100%;">
                    <tr>
                        <td width="30%" style="border:none; text-align:center; padding:0 8px;">
                            <div class="sig-box">
                                <div class="sig-space"></div>
                                <div class="sig-name">Allottee Signature</div>
                                <div class="sig-title">{{ $booking->customer->name ?? '' }}</div>
                                <div class="sig-cnic">CNIC: {{ $booking->customer->cnic ?? '—' }}</div>
                                <div class="sig-title" style="margin-top:2px;">Date: _______________</div>
                            </div>
                        </td>
                        <td width="40%" style="border:none; text-align:center; padding:0 8px;">
                            <div class="sig-box">
                                <table class="seal-table">
                                    <tr>
                                        <td style="border:none;">
                                            OFFICIAL<br>STAMP<br>&amp; SEAL<br>
                                            <span style="font-size:6px;">{{ strtoupper(substr($socName,0,14)) }}</span>
                                        </td>
                                    </tr>
                                </table>
                                <div class="sig-space"></div>
                                <div class="sig-name">Managing Director</div>
                                <div class="sig-title">{{ $socName }}</div>
                            </div>
                        </td>
                        <td width="30%" style="border:none; text-align:center; padding:0 8px;">
                            <div class="sig-box">
                                <div class="sig-space"></div>
                                <div class="sig-name">Possession Officer</div>
                                <div class="sig-title">Site In-Charge</div>
                                <div class="sig-title">{{ $socName }}</div>
                                <div class="sig-title" style="margin-top:2px;">Date: _______________</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

        </div>{{-- /doc-body --}}

        {{-- ══ FOOTER — all from SystemConfig ══ --}}
        <div class="doc-footer">
            <div class="footer-text">
                This is an official possession letter issued by {{ $socName }} &copy; {{ date('Y') }}.
                This document is legally binding. Duplicate issue requires written application to management.<br>
                @php $phones = array_filter([$socPhone, $socPhone2, $socPhone3]); @endphp
                @if($phones) {{ implode(' · ', $phones) }} @endif
                @if($phones && $socEmail) &nbsp;|&nbsp; @endif
                @if($socEmail) {{ $socEmail }} @endif
                @if($socAddress) <br>{{ $socAddress }} @endif
                @if($footerNote) <br>{{ $footerNote }} @endif
                <br>
                <span class="footer-ref">Ref: {{ $letterNo }}</span>
                &nbsp;·&nbsp; Booking: {{ $booking->customer_booking_id }}
                &nbsp;·&nbsp; Generated: {{ now()->format('d-M-Y h:i A') }}
            </div>
        </div>

    </div>{{-- /page-frame-inner --}}
</div>{{-- /page-frame --}}

</body>
</html>
