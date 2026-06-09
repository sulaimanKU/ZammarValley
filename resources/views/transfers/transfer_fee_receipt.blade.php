<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Fee Receipt — {{ $transfer->transfer_fee_receipt_no }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Arial,sans-serif; background:#e8ecf1; min-height:100vh; }

        /* ── Top bar ── */
        .print-bar { background:#1e3a8a; padding:12px 30px; display:flex; align-items:center; justify-content:space-between; gap:10px; }
        .print-bar-left { font-size:13px; font-weight:700; color:rgba(255,255,255,.7); letter-spacing:.3px; }
        .print-bar-left span { color:#93c5fd; }
        .print-bar-right { display:flex; gap:10px; align-items:center; }
        .print-bar button { background:#fff; color:#1e3a8a; border:none; padding:8px 18px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:7px; }
        .print-bar button:hover { background:#dbeafe; }
        .print-bar a { background:rgba(255,255,255,.12); color:#fff; border:1px solid rgba(255,255,255,.2); padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
        .print-bar a:hover { background:rgba(255,255,255,.2); color:#fff; }

        /* ── Receipt wrapper ── */
        .receipt-wrap { display:flex; justify-content:center; padding:30px 20px 50px; }
        .receipt { width:750px; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.12); }

        /* ── Receipt header ── */
        .receipt-header { background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 55%,#3730a3 100%); padding:28px 36px; position:relative; overflow:hidden; }
        .receipt-header::before { content:'TRANSFER FEE'; position:absolute; right:-20px; bottom:-20px; font-size:80px; font-weight:900; color:rgba(255,255,255,.04); letter-spacing:-2px; line-height:1; pointer-events:none; white-space:nowrap; }
        .rh-top { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; }
        .rh-org { font-size:20px; font-weight:900; color:#fff; letter-spacing:.5px; }
        .rh-org-sub { font-size:10px; color:rgba(255,255,255,.4); letter-spacing:2px; text-transform:uppercase; margin-top:3px; }
        .rh-badge { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2); border-radius:10px; padding:8px 16px; text-align:right; }
        .rh-badge-type { font-size:9px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1.5px; }
        .rh-badge-no   { font-size:16px; font-weight:900; color:#fff; font-family:'Courier New',monospace; margin-top:3px; }
        .rh-divider { border:none; border-top:1px solid rgba(255,255,255,.1); margin:20px 0; }
        .rh-bottom { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
        .rh-amount-box { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); border-radius:12px; padding:14px 22px; display:flex; align-items:center; gap:16px; }
        .rh-amount-label { font-size:9px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1.5px; }
        .rh-amount-value { font-size:28px; font-weight:900; color:#fcd34d; line-height:1; }
        .rh-amount-sub   { font-size:10px; color:rgba(255,255,255,.4); margin-top:3px; }
        .rh-status-badge { background:#dcfce7; color:#15803d; padding:8px 18px; border-radius:20px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; display:flex; align-items:center; gap:7px; }
        .rh-status-dot   { width:8px; height:8px; border-radius:50%; background:#16a34a; }

        /* ── Body ── */
        .receipt-body { padding:32px 36px; }

        /* ── Info grid ── */
        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:0; border:1px solid #e4e9f2; border-radius:12px; overflow:hidden; margin-bottom:24px; }
        .info-cell { padding:13px 18px; border-bottom:1px solid #f1f5f9; border-right:1px solid #f1f5f9; }
        .info-cell:nth-child(even) { border-right:none; }
        .info-cell:nth-last-child(-n+2) { border-bottom:none; }
        .info-cell-label { font-size:9.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.8px; margin-bottom:5px; }
        .info-cell-value { font-size:13px; font-weight:700; color:#0f172a; }
        .info-cell-sub   { font-size:10.5px; color:#64748b; margin-top:2px; }

        /* ── Transfer summary box ── */
        .transfer-summary { background:#f8fafc; border:1px solid #e4e9f2; border-radius:12px; padding:18px 22px; margin-bottom:24px; }
        .ts-title { font-size:9.5px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-bottom:14px; }
        .ts-row   { display:flex; justify-content:space-between; align-items:center; padding:7px 0; border-bottom:1px dashed #e4e9f2; font-size:12.5px; }
        .ts-row:last-child { border-bottom:none; }
        .ts-label { color:#64748b; font-weight:600; }
        .ts-value { font-weight:800; color:#0f172a; }
        .ts-row.highlight .ts-value { color:#1e3a8a; font-size:15px; }

        /* ── Completion box ── */
        .completion-box { background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1.5px solid #86efac; border-radius:12px; padding:16px 20px; margin-bottom:24px; }
        .cb-title { font-size:11px; font-weight:800; color:#15803d; text-transform:uppercase; letter-spacing:.8px; margin-bottom:12px; display:flex; align-items:center; gap:8px; }
        .cb-item { display:flex; align-items:center; gap:10px; font-size:12px; color:#166534; font-weight:600; padding:5px 0; }
        .cb-item::before { content:'✓'; font-weight:900; color:#16a34a; }

        /* ── Footer ── */
        .receipt-footer { background:#f8fafc; border-top:1px solid #e4e9f2; padding:20px 36px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; }
        .rf-qr { display:flex; flex-direction:column; align-items:center; gap:5px; }
        .rf-qr-label { font-size:8.5px; color:#94a3b8; text-transform:uppercase; letter-spacing:1.5px; text-align:center; }
        .rf-note { text-align:center; flex:1; max-width:300px; }
        .rf-note-main { font-size:11px; font-weight:700; color:#0f172a; }
        .rf-note-sub  { font-size:10px; color:#94a3b8; margin-top:4px; line-height:1.5; }
        .rf-stamp { text-align:center; }
        .rf-stamp-circle { width:80px; height:80px; border-radius:50%; border:2.5px double #1e3a8a; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:1px; margin:0 auto; }
        .rf-stamp-top { font-size:6.5px; font-weight:800; color:#1e3a8a; text-transform:uppercase; letter-spacing:.8px; text-align:center; }
        .rf-stamp-mid { font-size:11px; font-weight:900; color:#0f172a; }
        .rf-stamp-bot { font-size:7px; color:#64748b; text-transform:uppercase; }

        /* ── Print ── */
        @media print {
            body  { background:#fff !important; }
            .print-bar { display:none !important; }
            .receipt-wrap { padding:0 !important; }
            .receipt { box-shadow:none !important; border-radius:0 !important; width:100% !important; }
            * { -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; }
            @page { size:A4 portrait; margin:8mm; }
        }
    </style>
</head>
<body>

{{-- ── TOP BAR ── --}}
<div class="print-bar">
    <div class="print-bar-left">
        Transfer Fee Receipt &nbsp;•&nbsp; <span>{{ $transfer->transfer_fee_receipt_no }}</span>
    </div>
    <div class="print-bar-right">
        <button onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
            Print / Save PDF
        </button>
        <a href="{{ route('transfers.deed', $transfer->id) }}" target="_blank">View Deed</a>
        <a href="{{ route('index.transfer') }}">← Back</a>
    </div>
</div>

{{-- ── RECEIPT ── --}}
<div class="receipt-wrap">
<div class="receipt">

    {{-- Header --}}
    <div class="receipt-header">
        <div class="rh-top">
            <div>
                <div class="rh-org">Zamar Valley</div>
                <div class="rh-org-sub">Real Estate Development</div>
            </div>
            <div class="rh-badge">
                <div class="rh-badge-type">Transfer Fee Receipt</div>
                <div class="rh-badge-no">{{ $transfer->transfer_fee_receipt_no }}</div>
            </div>
        </div>
        <hr class="rh-divider">
        <div class="rh-bottom">
            <div class="rh-amount-box">
                <div>
                    <div class="rh-amount-label">Amount Paid</div>
                    <div class="rh-amount-value">PKR {{ number_format($transfer->transfer_fee) }}</div>
                    <div class="rh-amount-sub">Transfer Fee — {{ $transfer->deed_no }}</div>
                </div>
                <div id="headerQR"></div>
            </div>
            <div class="rh-status-badge">
                <div class="rh-status-dot"></div>
                Paid & Completed
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="receipt-body">

        {{-- Payment info grid --}}
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-cell-label">Receipt No.</div>
                <div class="info-cell-value" style="font-family:'Courier New',monospace;">{{ $transfer->transfer_fee_receipt_no }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">Payment Date</div>
                <div class="info-cell-value">{{ \Carbon\Carbon::parse($paymentInfo['date'])->format('d M Y') }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">Payment Method</div>
                <div class="info-cell-value">{{ ucfirst(str_replace('_',' ', $paymentInfo['method'])) }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">Paid By</div>
                <div class="info-cell-value">{{ $paymentInfo['paidBy'] }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">Deed No.</div>
                <div class="info-cell-value" style="color:#1e3a8a;font-family:'Courier New',monospace;">{{ $transfer->deed_no }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">Transfer Type</div>
                <div class="info-cell-value">{{ $transfer->getTypeLabel() }}</div>
            </div>
        </div>

        {{-- Transfer summary --}}
        <div class="transfer-summary">
            <div class="ts-title">Transfer Details</div>
            <div class="ts-row">
                <span class="ts-label">Plot</span>
                <span class="ts-value">Plot #{{ $transfer->plot->plot_number ?? '—' }}, Block {{ $transfer->plot->block ?? '—' }}</span>
            </div>
            <div class="ts-row">
                <span class="ts-label">Previous Owner</span>
                <span class="ts-value">{{ $transfer->fromCustomer->name ?? '—' }}</span>
            </div>
            @if($transfer->toCustomer)
            <div class="ts-row">
                <span class="ts-label">New Owner</span>
                <span class="ts-value" style="color:#16a34a;">{{ $transfer->toCustomer->name }}</span>
            </div>
            @endif
            @if($transfer->toBooking)
            <div class="ts-row">
                <span class="ts-label">New Booking Ref.</span>
                <span class="ts-value" style="color:#1e3a8a;font-family:'Courier New',monospace;">{{ $transfer->toBooking->customer_booking_id }}</span>
            </div>
            @endif
            <div class="ts-row">
                <span class="ts-label">Balance Transferred</span>
                <span class="ts-value">PKR {{ number_format($transfer->remaining_balance_transferred) }}</span>
            </div>
            <div class="ts-row highlight">
                <span class="ts-label">Transfer Fee Paid</span>
                <span class="ts-value">PKR {{ number_format($transfer->transfer_fee) }}</span>
            </div>
        </div>

        {{-- Payment proof image --}}
        @if($transfer->payment_proof)
        <div style="margin-bottom:24px;">
            <div style="font-size:9.5px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
                Payment Proof
                <span style="background:#dcfce7;color:#15803d;font-size:9px;padding:2px 9px;border-radius:10px;font-weight:800;">Verified</span>
            </div>
            <div style="border:1.5px solid #e4e9f2;border-radius:10px;overflow:hidden;text-align:center;background:#f8fafc;padding:12px;">
                <img src="{{ asset('storage/transferFeeRec/'.$transfer->payment_proof) }}"
                     alt="Payment Proof"
                     style="max-width:100%;max-height:240px;border-radius:8px;object-fit:contain;display:block;margin:0 auto;">
                <div style="font-size:10px;color:#94a3b8;margin-top:8px;">
                    Uploaded proof — {{ ucfirst(str_replace('_',' ',$paymentInfo['method'])) }}
                </div>
            </div>
        </div>
        @endif

        {{-- Completion confirmation --}}
        <div class="completion-box">
            <div class="cb-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Transfer Completed — All Actions Applied
            </div>
            <div class="cb-item">Transfer fee paid — Deed {{ $transfer->deed_no }} marked as Completed</div>
            <div class="cb-item">Old booking ({{ $transfer->fromBooking->customer_booking_id ?? '—' }}) closed — status: Transferred</div>
            @if($transfer->toBooking)
            <div class="cb-item">New booking ({{ $transfer->toBooking->customer_booking_id }}) activated for {{ $transfer->toCustomer->name ?? '—' }}</div>
            @endif
            <div class="cb-item">This receipt generated on {{ now()->format('d M Y, h:i A') }}</div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="receipt-footer">
        <div class="rf-qr">
            <div id="footerQR"></div>
            <div class="rf-qr-label">Scan → View Ledger</div>
        </div>
        <div class="rf-note">
            <div class="rf-note-main">Thank you for choosing Zamar Valley</div>
            <div class="rf-note-sub">
                This receipt is computer-generated and valid without a signature.<br>
                Printed: {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>
        <div class="rf-stamp">
            <div class="rf-stamp-circle">
                <div class="rf-stamp-top">Zamar<br>Valley</div>
                <div class="rf-stamp-mid">PAID</div>
                <div class="rf-stamp-bot">{{ now()->format('Y') }}</div>
            </div>
        </div>
    </div>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ledgerUrl = '{{ $transfer->toBooking
        ? route("ledger.view", $transfer->toBooking->id)
        : route("ledger.view", $transfer->fromBooking->id) }}';

    const opts = { width:64, height:64, colorDark:'#0f172a', colorLight:'#ffffff', correctLevel: QRCode.CorrectLevel.H };
    new QRCode(document.getElementById('headerQR'), { ...opts, width:56, height:56 });
    new QRCode(document.getElementById('footerQR'), opts);

    // Set correct URL after init
    setTimeout(() => {
        document.querySelectorAll('#headerQR img, #footerQR img').forEach(img => {});
        [document.getElementById('headerQR'), document.getElementById('footerQR')].forEach(el => {
            el.innerHTML = '';
            new QRCode(el, { text: ledgerUrl, ...opts });
        });
    }, 100);
});
</script>
</body>
</html>
