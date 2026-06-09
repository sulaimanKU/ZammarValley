<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Audit | Zamar Valley</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #334155; }
        .audit-card { background: white; max-width: 400px; margin: auto; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-top: 8px solid #0f172a; overflow: hidden; }
        .header { padding: 20px; text-align: center; border-bottom: 1px dashed #e2e8f0; }
        .amount-hero { font-size: 32px; font-weight: 800; color: #0f172a; margin: 10px 0; }
        .badge { background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .info-grid { padding: 20px; }
        .item { margin-bottom: 15px; }
        .label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
        .val { font-size: 15px; font-weight: 600; color: #1e293b; display: block; }
        .footer { background: #f8fafc; padding: 15px; text-align: center; font-size: 10px; color: #cbd5e1; }
    </style>
</head>
<body>
    <div class="audit-card">
        <div class="header">
            <span class="badge">System Verified Voucher</span>
            <div class="amount-hero">PKR {{ number_format($expense->amount) }}</div>
            <span class="label">Voucher #{{ $expense->voucher_no }}</span>
        </div>
        <div class="info-grid">
            <div class="item">
                <span class="label">Expense Category</span>
                <span class="val">{{ $expense->category }}</span>
            </div>
            <div class="item">
                <span class="label">Payment Date</span>
                <span class="val">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d F, Y') }}</span>
            </div>
            <div class="item">
                <span class="label">Payee / Description</span>
                <span class="val">{{ $expense->description ?? 'No detailed description' }}</span>
            </div>
        </div>
        <div class="footer">
            Digital Signature: {{ hash('sha256', $expense->id . $expense->created_at) }}
        </div>
    </div>
</body>
</html>
