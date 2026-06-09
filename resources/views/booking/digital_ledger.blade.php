<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Ledger | Zamar Valley</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f1f5f9; margin: 0; padding: 15px; color: #1e293b; }

        .status-active          { background: #16a34a; color: white; }
        .status-completed       { background: #1e40af; color: white; }
        .status-transferred     { background: #8b5cf6; color: white; }
        .status-pending_transfer{ background: #f59e0b; color: white; }
        .status-cancelled       { background: #ef4444; color: white; }
        .status-pending         { background: #64748b; color: white; }

        .header-card {
            background: white; border-radius: 20px; padding: 20px; text-align: center;
            margin-bottom: 15px; border: 1px solid #e2e8f0;
        }
        .status-pill {
            display: inline-block; padding: 4px 12px; border-radius: 50px;
            font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 10px;
        }
        .summary-card {
            background: #0f172a; border-radius: 24px; padding: 25px;
            margin-bottom: 20px; color: white;
        }
        .detail-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 1px;
            background: #e2e8f0; border: 1px solid #e2e8f0; border-radius: 16px;
            overflow: hidden; margin-bottom: 20px;
        }
        .detail-item { background: white; padding: 12px 15px; }
        .label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 800; display: block; }
        .value { font-size: 14px; font-weight: 700; color: #0f172a; }

        .t-list { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
        .t-item { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #f1f5f9; }
        .t-amount { font-weight: 800; color: #16a34a; text-align: right; }

        .progress-bar-container { background: rgba(255,255,255,0.1); height: 8px; border-radius: 10px; margin: 15px 0; }
        .progress-fill { background: #4ade80; height: 100%; border-radius: 10px; }

        /* ── Previous owner card ── */
        .prev-owner-card {
            background: #fffbeb; border-radius: 16px; padding: 18px;
            margin-bottom: 20px; border: 1px solid #fde68a;
        }
        .prev-owner-card .card-title {
            font-size: 11px; font-weight: 800; color: #92400e;
            text-transform: uppercase; letter-spacing: .5px; margin-bottom: 12px;
            display: flex; align-items: center; gap: 6px;
        }
        .prev-owner-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 1px;
            background: #fde68a; border-radius: 10px; overflow: hidden;
        }
        .prev-owner-item { background: #fffbeb; padding: 10px 12px; }

        /* ── Handled by strip ── */
        .handled-by {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px;
            padding: 12px 15px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        .handled-by .icon { background: #16a34a; color: white; width: 32px; height: 32px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0; }
        .handled-by .info { flex: 1; }
        .handled-by .info .hl  { font-size: 10px; color: #15803d; text-transform: uppercase; font-weight: 800; }
        .handled-by .info .hv  { font-size: 13px; font-weight: 700; color: #14532d; }
    </style>
</head>
<body>

@php
    // 1. Determine Total Price (Check Booking first, then Plot table)
    $totalPrice = 0;
    if ($booking->total_price > 0) {
        $totalPrice = $booking->total_price;
    } elseif ($booking->plot && $booking->plot->base_price > 0) {
        $totalPrice = $booking->plot->base_price;
    }

    // 2. Determine Monthly Installment
    $monthlyInstallment = 0;
    if ($booking->monthly_installment > 0) {
        $monthlyInstallment = $booking->monthly_installment;
    } elseif ($booking->plot && $booking->plot->installment_amount > 0) {
        $monthlyInstallment = $booking->plot->installment_amount;
    }

    // 3. Calculate Paid Amount & Percentage
    $totalPaid  = $booking->payments->where('status', 'paid')->sum('amount_paid');
    $percentage = ($totalPrice > 0) ? ($totalPaid / $totalPrice) * 100 : 0;
    $statusKey  = $booking->status;
@endphp

    {{-- ── Header ── --}}
    <div class="header-card">
        <div class="status-pill status-{{ $statusKey }}">
            {{ strtoupper(str_replace('_', ' ', $statusKey)) }}
        </div>
        <h3 style="margin:0; color: #1e3a8a;">Zamar Valley Ledger</h3>
        <p style="margin:5px 0 0; font-size: 12px; color: #64748b;">ID: {{ $booking->customer_booking_id }}</p>
    </div>

    {{-- ── Transfer notice ── --}}
    @if($booking->transferred_from_booking_id)
    <div style="background: #f3e8ff; color: #6b21a8; padding: 10px; border-radius: 12px; margin-bottom: 15px; font-size: 12px; text-align: center; border: 1px solid #d8b4fe;">
        ℹ️ This file was transferred from Booking ID: #{{ $booking->transferred_from_booking_id }}
    </div>
    @endif

    {{-- ── Handled By ── --}}
    <div class="handled-by">
        <div class="icon">👤</div>
        <div class="info">
            <div class="hl">Booking Handled By</div>
            <div class="hv">{{ $booking->createdBy->name ?? $booking->user->name ?? 'Zamar Valley Staff' }}</div>
        </div>
        <div style="font-size:10px; color:#64748b; text-align:right;">
            {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
        </div>
    </div>

 <div class="summary-card">
    <span class="label" style="color: #94a3b8;">Current Equity</span>
    <div style="font-size: 28px; font-weight: 800;">PKR {{ number_format($totalPaid) }}</div>

    <div class="progress-bar-container">
        <div class="progress-fill" style="width: {{ min($percentage, 100) }}%"></div>
    </div>

    <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 600;">
        <span>{{ number_format($percentage, 1) }}% Paid</span>
        {{-- Use the calculated $totalPrice here --}}
        <span style="opacity: 0.7;">Total Plot Value: {{ number_format($totalPrice) }}</span>
    </div>
</div>

<div class="detail-grid">
    <div class="detail-item">
        <span class="label">Plot</span>
        <span class="value">#{{ $booking->plot->plot_number ?? 'N/A' }} ({{ $booking->plot->block ?? 'N/A' }})</span>
    </div>
    <div class="detail-item">
        <span class="label">Size</span>
        <span class="value">{{ $booking->plot->size ?? '' }} {{ $booking->plot->unit ?? '' }}</span>
    </div>
    <div class="detail-item">
        <span class="label">Total Plan</span>
        {{-- Use Plot table for installments if booking table is 0 --}}
        <span class="value">{{ ($booking->total_installments > 0) ? $booking->total_installments : ($booking->plot->total_installments ?? 0) }} Months</span>
    </div>
    <div class="detail-item">
        <span class="label">Monthly Installment</span>
        {{-- Use the calculated $monthlyInstallment variable --}}
        <span class="value">PKR {{ number_format($monthlyInstallment) }}</span>
    </div>
</div>

    {{-- ── Previous Owner Card — only for Transfer bookings ── --}}
    @if($booking->booking_type === 'Transfer' && ($booking->previous_owner_name || $booking->previous_deed_no))
    <div class="prev-owner-card">
        <div class="card-title">
            🔄 Previous Owner Details
        </div>
        <div class="prev-owner-grid">
            <div class="prev-owner-item">
                <span class="label">Previous Owner</span>
                <span class="value" style="font-size:13px;">{{ $booking->previous_owner_name ?? '—' }}</span>
            </div>
            <div class="prev-owner-item">
                <span class="label">Previous CNIC</span>
                <span class="value" style="font-size:13px;">{{ $booking->previous_owner_cnic ?? '—' }}</span>
            </div>
            <div class="prev-owner-item">
                <span class="label">Deed / Ref No.</span>
                <span class="value" style="font-size:13px;">{{ $booking->previous_deed_no ?? '—' }}</span>
            </div>
            <div class="prev-owner-item">
                <span class="label">Transfer Date</span>
                <span class="value" style="font-size:13px;">
                    {{ $booking->previous_transfer_date
                        ? \Carbon\Carbon::parse($booking->previous_transfer_date)->format('d M Y')
                        : '—' }}
                </span>
            </div>
        </div>

        {{-- Document links --}}
        @if($booking->previous_owner_cnic_doc || $booking->previous_sale_deed)
        <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
            @if($booking->previous_owner_cnic_doc)
            <a href="{{ asset($booking->previous_owner_cnic_doc) }}" target="_blank"
                style="background:#92400e; color:white; font-size:11px; font-weight:700; padding:6px 14px; border-radius:20px; text-decoration:none;">
                📄 CNIC Copy
            </a>
            @endif
            @if($booking->previous_sale_deed)
            <a href="{{ asset($booking->previous_sale_deed) }}" target="_blank"
                style="background:#92400e; color:white; font-size:11px; font-weight:700; padding:6px 14px; border-radius:20px; text-decoration:none;">
                📜 Sale Deed
            </a>
            @endif
        </div>
        @endif
    </div>
    @endif

    {{-- ── Payment ledger ── --}}
    <h4 style="margin: 0 0 10px 5px; font-size: 14px; text-transform: uppercase;">Payment Ledger</h4>
    <div class="t-list">
       {{-- Filter out possession_fee from the ledger --}}
@forelse($booking->payments
    ->where('status', 'paid')
    ->where('payment_category', '!=', 'possession_fee')
    ->sortByDesc('paid_date') as $pay)

    <div class="t-item">
        <div>
            <span style="font-weight: 700; font-size: 14px;">
                {{ ucwords(str_replace('_', ' ', $pay->payment_category)) }}
            </span>
            <div style="font-size: 11px; color: #64748b;">
                {{ date('d M, Y', strtotime($pay->paid_date)) }}
            </div>
        </div>
        <div class="t-amount">
            +{{ number_format($pay->amount_paid) }}
            <div style="font-size: 10px; color: #94a3b8; font-weight: 400;">
                via {{ $pay->payment_type }}
            </div>
        </div>
    </div>
@empty
    <div style="padding: 20px; text-align: center; color: #94a3b8;">No verified payments found.</div>
@endforelse
    </div>

    {{-- ── Footer audit ── --}}
    <div style="text-align: center; margin-top: 25px; padding-bottom: 20px;">
        <p style="font-size: 10px; color: #94a3b8;">
            Audit Hash: {{ strtoupper(substr(md5($booking->id), 0, 16)) }}<br>
            Validated at: {{ now()->format('d M, Y h:i A') }}
        </p>
    </div>

</body>
</html>
