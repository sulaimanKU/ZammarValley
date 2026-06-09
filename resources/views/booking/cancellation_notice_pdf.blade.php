<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; background: #fff; padding: 28px 32px; }
    .letterhead { text-align: center; border-bottom: 2px solid #dc2626; padding-bottom: 14px; margin-bottom: 18px; }
    .letterhead h1 { font-size: 18px; font-weight: 700; color: #0f172a; }
    .letterhead p  { font-size: 10px; color: #64748b; margin-top: 3px; }
    .doc-title { text-align: center; margin-bottom: 18px; }
    .doc-title h2 { font-size: 15px; font-weight: 700; color: #991b1b; letter-spacing: .5px; text-transform: uppercase; }
    .doc-title .ref { font-size: 10px; color: #64748b; margin-top: 4px; }
    .section { margin-bottom: 16px; }
    .section-head { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 10px; }
    table.info { width: 100%; border-collapse: collapse; }
    table.info td { padding: 5px 8px; font-size: 11px; vertical-align: top; }
    table.info td:first-child { font-weight: 600; color: #475569; width: 40%; }
    table.info td:last-child  { color: #0f172a; }
    .fin-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 14px 18px; margin-top: 10px; }
    .fin-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #fecaca; font-size: 11px; }
    .fin-row:last-child { border-bottom: none; font-weight: 700; font-size: 12px; padding-top: 8px; }
    .fin-row .lbl { color: #7f1d1d; }
    .fin-row .val { font-weight: 700; color: #991b1b; }
    .fin-row.refund .val { color: #15803d; }
    .cancel-reason { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; font-size: 11px; color: #78350f; line-height: 1.6; }
    .stamp-area { margin-top: 36px; display: flex; justify-content: space-between; }
    .stamp-box { text-align: center; width: 180px; }
    .stamp-line { border-top: 1px solid #94a3b8; padding-top: 5px; margin-top: 40px; font-size: 10px; color: #64748b; }
    .footer { text-align: center; margin-top: 28px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 9px; color: #94a3b8; }
</style>
</head>
<body>

{{-- Letterhead --}}
<div class="letterhead">
    @if(!empty($sc['logo_base64']))
        <img src="{{ $sc['logo_base64'] }}" style="height:48px;margin-bottom:6px;" alt="Logo">
    @endif
    <h1>{{ $sc['name'] ?? config('app.name') }}</h1>
    <p>{{ $sc['address'] ?? '' }}{{ !empty($sc['phone']) ? ' · Tel: '.$sc['phone'] : '' }}</p>
</div>

{{-- Document Title --}}
<div class="doc-title">
    <h2>Booking Cancellation Notice</h2>
    <div class="ref">Ref: {{ $booking->customer_booking_id }} &nbsp;·&nbsp; Date: {{ \Carbon\Carbon::parse($booking->cancelled_at ?? $booking->updated_at)->format('d M Y') }}</div>
</div>

{{-- Customer Info --}}
<div class="section">
    <div class="section-head">Customer Details</div>
    <table class="info">
        <tr><td>Full Name</td><td>{{ $booking->customer->name ?? '—' }}</td></tr>
        <tr><td>Father / Husband</td><td>{{ $booking->customer->guardian_name ?? '—' }}</td></tr>
        <tr><td>CNIC</td><td>{{ $booking->customer->cnic ?? '—' }}</td></tr>
        <tr><td>Mobile</td><td>{{ $booking->customer->mobile ?? $booking->customer->phone ?? '—' }}</td></tr>
    </table>
</div>

{{-- Plot Info --}}
<div class="section">
    <div class="section-head">Plot Details</div>
    <table class="info">
        <tr><td>Plot No.</td><td>#{{ $booking->plot->plot_number ?? '—' }} — {{ $booking->plot->block ?? '' }}</td></tr>
        <tr><td>Size</td><td>{{ $booking->plot->size ?? '' }} {{ $booking->plot->unit ?? '' }}</td></tr>
        <tr><td>Category</td><td>{{ $booking->plot->category->name ?? '—' }}</td></tr>
        <tr><td>Booking Date</td><td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td></tr>
        <tr><td>Cancelled On</td><td>{{ $booking->cancelled_at ? \Carbon\Carbon::parse($booking->cancelled_at)->format('d M Y, h:i A') : '—' }}</td></tr>
        @if($booking->cancelledBy)
        <tr><td>Cancelled By</td><td>{{ $booking->cancelledBy->name }}</td></tr>
        @endif
    </table>
</div>

{{-- Cancellation Reason --}}
@if($booking->cancellation_reason)
<div class="section">
    <div class="section-head">Reason for Cancellation</div>
    <div class="cancel-reason">{{ $booking->cancellation_reason }}</div>
</div>
@endif

{{-- Financial Summary --}}
<div class="section">
    <div class="section-head">Financial Summary</div>
    <div class="fin-box">
        <div class="fin-row">
            <span class="lbl">Agreed Plot Price</span>
            <span class="val">PKR {{ number_format($booking->total_price) }}</span>
        </div>
        <div class="fin-row">
            <span class="lbl">Total Amount Collected</span>
            <span class="val">PKR {{ number_format($totalPaid) }}</span>
        </div>
        <div class="fin-row refund">
            <span class="lbl">Agreed Refund to Customer</span>
            <span class="val" style="color:#15803d;">PKR {{ number_format($refundAmount) }}</span>
        </div>
        @if($totalPaid > 0)
        <div class="fin-row" style="background:#f8fafc;padding:6px 8px;border-radius:4px;margin-top:4px;">
            <span class="lbl" style="font-weight:700;color:#1e293b;">Net Amount Retained by Society</span>
            <span class="val" style="color:#1e293b;">PKR {{ number_format($netRetained) }}</span>
        </div>
        @endif
    </div>
</div>

{{-- Signature area --}}
<div class="stamp-area">
    <div class="stamp-box">
        <div class="stamp-line">Customer Signature &amp; CNIC</div>
    </div>
    <div class="stamp-box">
        <div class="stamp-line">Authorised Signatory</div>
    </div>
    <div class="stamp-box">
        <div class="stamp-line">Society Stamp &amp; Date</div>
    </div>
</div>

<div class="footer">
    This is an official cancellation notice issued by {{ $sc['name'] ?? config('app.name') }}.
    Generated: {{ now()->format('d M Y, h:i A') }}
</div>

</body>
</html>
