<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Booking — Zamar Valley</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    :root {
        --navy:   #1e3a8a;
        --navy2:  #1e40af;
        --gold:   #f59e0b;
        --gold2:  #fbbf24;
        --green:  #15803d;
        --green2: #dcfce7;
        --red:    #dc2626;
        --slate:  #64748b;
        --bg:     #f0f4f8;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'DM Sans', sans-serif;
        background: var(--bg);
        min-height: 100vh;
        color: #1e293b;
    }

    /* ── BACKGROUND PATTERN ── */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(ellipse 80% 60% at 50% -10%, rgba(30,58,138,0.12) 0%, transparent 70%),
            repeating-linear-gradient(45deg, transparent, transparent 40px, rgba(30,58,138,0.015) 40px, rgba(30,58,138,0.015) 41px);
        pointer-events: none;
        z-index: 0;
    }

    .page-wrap {
        position: relative;
        z-index: 1;
        max-width: 480px;
        margin: 0 auto;
        padding: 0 0 40px;
    }

    /* ── HERO HEADER ── */
    .hero {
        background: linear-gradient(160deg, #1e3a8a 0%, #1e40af 60%, #2563eb 100%);
        padding: 36px 24px 32px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .hero::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 28px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .hero-logo {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
    }
    .hero-logo-icon {
        width: 40px; height: 40px;
        background: var(--gold);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
    }
    .hero-logo-text {
        font-family: 'Playfair Display', serif;
        font-size: 20px;
        font-weight: 900;
        color: #fff;
        letter-spacing: 1px;
    }
    .hero-logo-sub {
        font-size: 9px;
        color: rgba(255,255,255,0.6);
        letter-spacing: 2px;
        text-transform: uppercase;
        text-align: left;
        margin-top: 2px;
    }
    .hero-title {
        font-size: 13px;
        color: rgba(255,255,255,0.65);
        text-transform: uppercase;
        letter-spacing: 3px;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .hero-ref {
        font-family: 'DM Mono', monospace;
        font-size: 22px;
        font-weight: 500;
        color: var(--gold2);
        letter-spacing: 2px;
    }

    /* ── STATUS CARD ── */
    .status-card {
        margin: 20px 16px 0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(30,58,138,0.13), 0 2px 8px rgba(0,0,0,0.06);
        background: #fff;
        animation: slideUp 0.5s ease both;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .status-banner {
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .status-banner.completed  { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-bottom: 2px solid #86efac; }
    .status-banner.active     { background: linear-gradient(135deg, #eff6ff, #dbeafe); border-bottom: 2px solid #93c5fd; }
    .status-banner.pending    { background: linear-gradient(135deg, #fffbeb, #fef9c3); border-bottom: 2px solid #fde68a; }
    .status-banner.transferred{ background: linear-gradient(135deg, #f5f3ff, #ede9fe); border-bottom: 2px solid #c4b5fd; }
    .status-banner.cancelled  { background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-bottom: 2px solid #fecdd3; }

    .status-icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .completed  .status-icon { background: #15803d; color: #fff; }
    .active     .status-icon { background: #1e3a8a; color: #fff; }
    .pending    .status-icon { background: #d97706; color: #fff; }
    .transferred.status-icon { background: #7c3aed; color: #fff; }
    .cancelled  .status-icon { background: #dc2626; color: #fff; }

    .status-text-wrap { flex: 1; }
    .status-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .completed  .status-label { color: var(--green); }
    .active     .status-label { color: var(--navy); }
    .pending    .status-label { color: #b45309; }
    .transferred .status-label{ color: #6d28d9; }
    .cancelled  .status-label { color: var(--red); }

    .status-value {
        font-family: 'Playfair Display', serif;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.1;
        color: #0f172a;
    }
    .status-sub {
        font-size: 11px;
        color: var(--slate);
        margin-top: 3px;
    }

    /* ── SECTIONS ── */
    .section {
        margin: 12px 16px 0;
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(30,58,138,0.07);
        animation: slideUp 0.5s ease both;
    }
    .section:nth-child(3) { animation-delay: 0.08s; }
    .section:nth-child(4) { animation-delay: 0.14s; }
    .section:nth-child(5) { animation-delay: 0.20s; }
    .section:nth-child(6) { animation-delay: 0.26s; }

    .sec-header {
        background: var(--navy);
        padding: 9px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sec-header-icon { font-size: 13px; }
    .sec-header-text {
        font-size: 10px;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .sec-body { padding: 4px 0; }

    .info-row {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        border-bottom: 1px solid #f1f5f9;
        gap: 12px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row-icon {
        width: 30px; height: 30px;
        background: #f0f4f8;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0;
    }
    .info-row-content { flex: 1; min-width: 0; }
    .info-lbl {
        font-size: 10px;
        color: var(--slate);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1px;
    }
    .info-val {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .info-val.mono { font-family: 'DM Mono', monospace; font-size: 12px; }
    .info-val.blue { color: var(--navy); }
    .info-val.green { color: var(--green); }

    /* ── PAYMENT PROGRESS ── */
    .progress-wrap { padding: 14px 16px; }
    .progress-meta {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 10px;
    }
    .progress-paid {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--green);
    }
    .progress-total {
        font-size: 12px;
        color: var(--slate);
        font-weight: 600;
    }
    .progress-bar-bg {
        height: 10px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
        margin-bottom: 8px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, #15803d, #22c55e);
        transition: width 1s ease;
    }
    .progress-pct {
        font-size: 12px;
        font-weight: 700;
        color: var(--green);
        text-align: right;
    }
    .progress-remaining {
        margin-top: 10px;
        padding: 8px 12px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 600;
    }
    .progress-remaining.zero { background: #f0fdf4; color: var(--green); border: 1px solid #86efac; }
    .progress-remaining.nonzero { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }

    /* ── INSTALLMENT LIST ── */
    .inst-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        border-bottom: 1px solid #f8fafc;
    }
    .inst-item:last-child { border-bottom: none; }
    .inst-dot {
        width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
    }
    .inst-dot.paid    { background: var(--green); }
    .inst-dot.pending { background: #d97706; }
    .inst-dot.overdue { background: var(--red); }
    .inst-num { font-size: 11px; font-weight: 700; color: var(--slate); width: 20px; }
    .inst-details { flex: 1; }
    .inst-name { font-size: 13px; font-weight: 600; color: #1e293b; }
    .inst-date { font-size: 10px; color: var(--slate); }
    .inst-amount { font-size: 13px; font-weight: 700; font-family: 'DM Mono', monospace; }
    .inst-amount.paid    { color: var(--green); }
    .inst-amount.pending { color: #d97706; }
    .inst-amount.overdue { color: var(--red); }
    .inst-badge {
        font-size: 9px; font-weight: 700; padding: 2px 7px; border-radius: 99px;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .inst-badge.paid    { background: #dcfce7; color: var(--green); }
    .inst-badge.pending { background: #fef9c3; color: #92400e; }
    .inst-badge.overdue { background: #ffe4e6; color: var(--red); }

    /* ── POSSESSION BADGE ── */
    .possession-banner {
        margin: 12px 16px 0;
        background: linear-gradient(135deg, #15803d, #16a34a);
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 16px rgba(21,128,61,0.25);
        animation: slideUp 0.5s 0.3s ease both;
    }
    .possession-icon { font-size: 30px; }
    .possession-text { flex: 1; }
    .possession-title { font-size: 14px; font-weight: 800; color: #fff; }
    .possession-sub { font-size: 11px; color: rgba(255,255,255,0.75); margin-top: 2px; }

    /* ── VERIFY FOOTER ── */
    .verify-footer {
        margin: 16px 16px 0;
        padding: 14px 16px;
        background: #fff;
        border-radius: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 2px 10px rgba(30,58,138,0.07);
        animation: slideUp 0.5s 0.35s ease both;
    }
    .verify-shield { font-size: 24px; }
    .verify-text { flex: 1; }
    .verify-title { font-size: 12px; font-weight: 700; color: #1e293b; }
    .verify-code {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: var(--slate);
        margin-top: 2px;
        word-break: break-all;
    }
    .verify-ok {
        background: #dcfce7;
        color: var(--green);
        font-size: 10px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 99px;
        border: 1px solid #86efac;
        white-space: nowrap;
    }

    /* ── NOT FOUND ── */
    .not-found {
        margin: 40px 24px;
        background: #fff;
        border-radius: 20px;
        padding: 40px 24px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        animation: slideUp 0.4s ease both;
    }
    .not-found-icon { font-size: 48px; margin-bottom: 12px; }
    .not-found-title { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .not-found-sub { font-size: 13px; color: var(--slate); line-height: 1.6; }

    /* ── RESPONSIVE TWEAKS ── */
    @media (min-width: 500px) {
        .page-wrap { padding-top: 24px; }
        .hero { border-radius: 20px 20px 0 0; margin: 0 auto; }
    }
</style>
</head>
<body>

@php
    $totalPaid  = $booking ? $booking->payments->where('status','paid')->sum('amount_paid') : 0;
    $totalPrice = $booking ? $booking->total_price : 0;
    $pct        = $totalPrice > 0 ? min(100, round(($totalPaid / $totalPrice) * 100)) : 0;
    $remaining  = max(0, $totalPrice - $totalPaid);
    $status     = $booking ? $booking->status : 'not_found';
    $verifyCode = $booking ? substr(md5($booking->id . $booking->customer_booking_id), 0, 16) : '';

    $statusConfig = [
        'completed'   => ['icon'=>'✓', 'label'=>'Booking Completed',    'sub'=>'All payments cleared'],
        'active'      => ['icon'=>'◎', 'label'=>'Booking Active',        'sub'=>'Payments in progress'],
        'pending'     => ['icon'=>'◷', 'label'=>'Booking Pending',       'sub'=>'Awaiting first payment'],
        'transferred' => ['icon'=>'⇄', 'label'=>'Plot Transferred',      'sub'=>'Ownership transferred'],
        'cancelled'   => ['icon'=>'✕', 'label'=>'Booking Cancelled',     'sub'=>'This booking is cancelled'],
    ];
    $sc = $statusConfig[$status] ?? ['icon'=>'?','label'=>'Unknown','sub'=>''];

    // Installments (up to 5 recent)
    $installments = $booking ? $booking->payments()
        ->whereIn('payment_category', ['installment','down_payment'])
        ->orderBy('created_at','asc')
        ->take(5)
        ->get() : collect();
@endphp

<div class="page-wrap">

    {{-- ── HERO ── --}}
    <div class="hero">
        <div class="hero-logo">
            <div class="hero-logo-icon">🏡</div>
            <div>
                <div class="hero-logo-text">Zamar Valley</div>
                <div class="hero-logo-sub">Payment Verification</div>
            </div>
        </div>
        @if($booking)
        <div class="hero-title">Booking Reference</div>
        <div class="hero-ref">{{ $booking->customer_booking_id }}</div>
        @else
        <div class="hero-title">Verification Portal</div>
        <div class="hero-ref">— — —</div>
        @endif
    </div>

    @if(!$booking)
    {{-- ── NOT FOUND ── --}}
    <div class="not-found">
        <div class="not-found-icon">🔍</div>
        <div class="not-found-title">Booking Not Found</div>
        <div class="not-found-sub">
            The booking reference you scanned could not be found in our system.<br><br>
            Please contact Zamar Valley office for assistance.<br><br>
            <strong>📞 +92 3XX XXXXXXX</strong>
        </div>
    </div>

    @else

    {{-- ── STATUS CARD ── --}}
    <div class="status-card">
        <div class="status-banner {{ $status }}">
            <div class="status-icon">{{ $sc['icon'] }}</div>
            <div class="status-text-wrap">
                <div class="status-label">Booking Status</div>
                <div class="status-value">{{ $sc['label'] }}</div>
                <div class="status-sub">{{ $sc['sub'] }}</div>
            </div>
        </div>
    </div>

    {{-- ── POSSESSION BANNER (completed only) ── --}}
    @if($status === 'completed')
    <div class="possession-banner">
        <div class="possession-icon">🏠</div>
        <div class="possession-text">
            <div class="possession-title">Possession Granted</div>
            <div class="possession-sub">All dues cleared. Physical possession authorized.</div>
        </div>
    </div>
    @endif

    {{-- ── CUSTOMER INFO ── --}}
    <div class="section">
        <div class="sec-header">
            <span class="sec-header-icon">👤</span>
            <span class="sec-header-text">Allottee Details</span>
        </div>
        <div class="sec-body">
            <div class="info-row">
                <div class="info-row-icon">🧑</div>
                <div class="info-row-content">
                    <div class="info-lbl">Full Name</div>
                    <div class="info-val blue">{{ $booking->customer->name ?? '—' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-row-icon">🪪</div>
                <div class="info-row-content">
                    <div class="info-lbl">CNIC Number</div>
                    <div class="info-val mono">{{ $booking->customer->cnic ?? '—' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-row-icon">📞</div>
                <div class="info-row-content">
                    <div class="info-lbl">Contact</div>
                    <div class="info-val mono">{{ $booking->customer->phone ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PLOT INFO ── --}}
    <div class="section">
        <div class="sec-header">
            <span class="sec-header-icon">📍</span>
            <span class="sec-header-text">Plot Details</span>
        </div>
        <div class="sec-body">
            <div class="info-row">
                <div class="info-row-icon">🔢</div>
                <div class="info-row-content">
                    <div class="info-lbl">Plot Number</div>
                    <div class="info-val blue">#{{ $booking->plot->plot_number ?? '—' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-row-icon">🏘️</div>
                <div class="info-row-content">
                    <div class="info-lbl">Block / Sector</div>
                    <div class="info-val">Block {{ $booking->plot->block ?? '—' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-row-icon">📐</div>
                <div class="info-row-content">
                    <div class="info-lbl">Measured Area</div>
                    <div class="info-val">{{ $booking->plot->size ?? '—' }} {{ $booking->plot->unit ?? '' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-row-icon">📅</div>
                <div class="info-row-content">
                    <div class="info-lbl">Booking Date</div>
                    <div class="info-val">{{ date('d M Y', strtotime($booking->booking_date)) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PAYMENT PROGRESS ── --}}
    <div class="section">
        <div class="sec-header">
            <span class="sec-header-icon">💰</span>
            <span class="sec-header-text">Payment Status</span>
        </div>
        <div class="sec-body">
            <div class="progress-wrap">
                <div class="progress-meta">
                    <div>
                        <div style="font-size:10px;color:#64748b;font-weight:600;margin-bottom:2px;">AMOUNT PAID</div>
                        <div class="progress-paid">PKR {{ number_format($totalPaid) }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:10px;color:#64748b;font-weight:600;margin-bottom:2px;">TOTAL PRICE</div>
                        <div class="progress-total">PKR {{ number_format($totalPrice) }}</div>
                    </div>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width:{{ $pct }}%;"></div>
                </div>
                <div class="progress-pct">{{ $pct }}% paid</div>
                <div class="progress-remaining {{ $remaining == 0 ? 'zero' : 'nonzero' }}">
                    @if($remaining == 0)
                        <span>✓ All dues cleared</span>
                        <span style="font-family:'DM Mono',monospace;">PKR 0</span>
                    @else
                        <span>Remaining balance</span>
                        <span style="font-family:'DM Mono',monospace;">PKR {{ number_format($remaining) }}</span>
                    @endif
                </div>
            </div>

            {{-- Recent installments --}}
            @if($installments->count())
            <div style="border-top:1px solid #f1f5f9; padding: 8px 16px 4px;">
                <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">Recent Payments</div>
            </div>
            @foreach($installments as $i => $inst)
            @php
                $iStatus = $inst->status ?? 'pending';
            @endphp
            <div class="inst-item">
                <div class="inst-dot {{ $iStatus }}"></div>
                <div class="inst-num">{{ $i+1 }}</div>
                <div class="inst-details">
                    <div class="inst-name">{{ ucwords(str_replace('_',' ', $inst->payment_category)) }}</div>
                    <div class="inst-date">{{ \Carbon\Carbon::parse($inst->payment_date ?? $inst->created_at)->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="inst-amount {{ $iStatus }}">PKR {{ number_format($inst->amount_paid) }}</div>
                    <div style="text-align:right;margin-top:2px;"><span class="inst-badge {{ $iStatus }}">{{ ucfirst($iStatus) }}</span></div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>

    {{-- ── VERIFICATION FOOTER ── --}}
    <div class="verify-footer">
        <div class="verify-shield">🔐</div>
        <div class="verify-text">
            <div class="verify-title">Document Verification</div>
            <div class="verify-code">{{ $verifyCode }}</div>
        </div>
        <div class="verify-ok">✓ VERIFIED</div>
    </div>

    {{-- ── CONTACT ── --}}
    <div style="margin:16px 16px 0; text-align:center; padding:12px; color:#94a3b8; font-size:11px; line-height:1.8;">
        Zamar Valley Real Estate &nbsp;·&nbsp; Main G.T. Road, Hazro<br>
        📞 +92 3XX XXXXXXX &nbsp;·&nbsp; info@zamarvalley.pk<br>
        <span style="font-size:10px;">Generated {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    @endif

</div>
</body>
</html>
