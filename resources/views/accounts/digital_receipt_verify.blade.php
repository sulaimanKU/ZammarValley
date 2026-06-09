<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verified | Zamar Valley</title>
    <style>
        body { font-family: sans-serif; background-color: #f1f5f9; margin: 0; padding: 20px; color: #334155; }
        .receipt-card { background: white; max-width: 400px; margin: auto; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .status-header { background: #059669; color: white; padding: 30px 20px; text-align: center; }
        .amount-box { padding: 25px; text-align: center; border-bottom: 1px dashed #e2e8f0; }
        .amount { font-size: 32px; font-weight: bold; color: #059669; margin: 5px 0; }
        .details { padding: 20px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
        .label { color: #64748b; }
        .value { font-weight: 600; text-align: right; }
        .footer { padding: 15px; background: #f8fafc; text-align: center; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="status-header">
            <h2 style="margin:0">Payment Verified</h2>
            <p style="font-size: 13px; opacity: 0.9;">Official Digital Receipt</p>
        </div>

        <div class="amount-box">
            <div class="label">Amount Paid</div>
            <div class="amount">PKR {{ number_format($payment->amount_paid) }}</div>
            <div style="font-size: 12px; color: #64748b;">via {{ ucfirst($payment->payment_type) }}</div>
        </div>

        <div class="details">
            <div class="row">
                <span class="label">Receipt #</span>
                <span class="value">{{ $payment->id }}</span>
            </div>
            <div class="row">
                <span class="label">Customer</span>
                <span class="value">{{ $payment->booking->customer->name }}</span>
            </div>
            <div class="row">
                <span class="label">Plot</span>
                <span class="value">{{ $payment->booking->plot->plot_number }} ({{ $payment->booking->plot->block }})</span>
            </div>
          <div class="row">
    <span class="label">Payment Description</span>
    <span class="value" style="color: #059669; font-weight: bold;">
        {{ $paymentLabel }}
    </span>
</div>
            <div class="row">
                <span class="label">Payment Date</span>
                <span class="value">{{ \Carbon\Carbon::parse($payment->paid_date)->format('d M, Y') }}</span>
            </div>
            <hr style="border: none; border-top: 1px solid #f1f5f9;">
          <div class="row" style="margin-top: 10px;">
    <span class="label">Remaining Balance</span>
    <span class="value" style="color: #ef4444;">
        {{-- Use the variable we passed from the controller --}}
        PKR {{ number_format($remainingBalance) }}
    </span>
</div>
        </div>

        <div class="footer">
            Digital Signature: {{ strtoupper(substr(md5($payment->id), 0, 10)) }}<br>
            Verified on: {{ now()->format('d-M-Y H:i A') }}
        </div>
    </div>
</body>
</html>
