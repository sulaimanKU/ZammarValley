
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Card — {{ $booking->customer->name }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #e8ecf1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px 60px;
        }

        /* ── Screen buttons ── */
        .screen-bar {
            width: 680px; display: flex; justify-content: flex-end;
            gap: 10px; margin-bottom: 20px;
        }
        .screen-bar button {
            background: #1e3a8a; color: #fff; border: none;
            padding: 9px 20px; border-radius: 9px; font-size: 13px;
            font-weight: 700; cursor: pointer; display: inline-flex;
            align-items: center; gap: 7px;
        }
        .screen-bar a {
            background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
            padding: 9px 16px; border-radius: 9px; font-size: 13px;
            font-weight: 600; text-decoration: none; display: inline-flex;
            align-items: center; gap: 6px;
        }

        /* ── Page hint ── */
        .hint {
            width: 680px; text-align: center; margin-bottom: 24px;
            font-size: 12px; color: #64748b;
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 10px; padding: 10px 16px;
        }

        /* ── Card itself ── */
        .card-wrap {
            width: 680px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        /* Standard CR80 card size: 85.6mm × 53.98mm → 323px × 204px at 96dpi */
        .customer-card {
            width: 340px; height: 210px;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,.18);
            position: relative;
            page-break-inside: avoid;
        }

        /* ── FRONT of card ── */
        .card-front {
            width: 100%; height: 100%;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #1d4ed8 100%);
            display: flex; flex-direction: column;
            position: relative; overflow: hidden;
        }
        .card-front::before {
            content: ''; position: absolute;
            top: -40px; right: -30px;
            width: 160px; height: 160px; border-radius: 50%;
            background: rgba(255,255,255,.05);
        }
        .card-front::after {
            content: ''; position: absolute;
            bottom: -50px; left: 20px;
            width: 120px; height: 120px; border-radius: 50%;
            background: rgba(255,255,255,.04);
        }

        /* Card header stripe */
        .card-header {
            padding: 12px 14px 8px;
            display: flex; justify-content: space-between; align-items: flex-start;
            position: relative; z-index: 1;
        }
        .card-brand-name { font-size: 13px; font-weight: 900; color: #fff; letter-spacing: .5px; }
        .card-brand-tag  { font-size: 7px; color: rgba(255,255,255,.45); margin-top: 1px; text-transform: uppercase; letter-spacing: 1px; }
        .card-chip {
            width: 28px; height: 20px; background: linear-gradient(135deg,#fbbf24,#f59e0b);
            border-radius: 4px; opacity: .85;
        }

        /* Card middle */
        .card-middle {
            flex: 1; padding: 4px 14px;
            display: flex; align-items: center; gap: 10px;
            position: relative; z-index: 1;
        }
        /* Avatar */
        .card-avatar {
            width: 46px; height: 46px; border-radius: 10px;
            background: rgba(255,255,255,.12);
            border: 2px solid rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 900; color: #fff;
            flex-shrink: 0; overflow: hidden;
        }
        .card-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .card-info { flex: 1; }
        .card-name { font-size: 13px; font-weight: 800; color: #fff; line-height: 1.2; }
        .card-cnic { font-size: 9px; color: rgba(255,255,255,.5); font-family: monospace; letter-spacing: .5px; margin-top: 3px; }
        .card-plot { font-size: 9px; color: #93c5fd; margin-top: 4px; font-weight: 700; }
        .card-booking-id { font-size: 8px; color: rgba(255,255,255,.4); margin-top: 2px; }

        /* QR code area */
        .card-qr {
            width: 54px; flex-shrink: 0; text-align: center;
        }
        .card-qr-box {
            background: #fff; border-radius: 6px; padding: 3px;
            display: inline-block;
        }
        .card-qr-label { font-size: 6px; color: rgba(255,255,255,.4); margin-top: 3px; text-align: center; line-height: 1.4; }

        /* Card footer */
        .card-footer {
            padding: 6px 14px 10px;
            display: flex; justify-content: space-between; align-items: flex-end;
            position: relative; z-index: 1;
        }
        .card-footer-left { }
        .card-type-badge {
            display: inline-block; background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.7);
            font-size: 7px; font-weight: 700; padding: 2px 8px;
            border-radius: 20px; text-transform: uppercase; letter-spacing: .8px;
        }
        .card-price-label { font-size: 7px; color: rgba(255,255,255,.4); margin-top: 3px; }
        .card-price-value { font-size: 11px; font-weight: 800; color: #4ade80; }
        .card-footer-right { text-align: right; }
        .card-status-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; display: inline-block; margin-right: 4px; }
        .card-status-text { font-size: 8px; color: rgba(255,255,255,.5); }
        .card-issued { font-size: 7px; color: rgba(255,255,255,.3); margin-top: 2px; }

        /* ── BACK of card ── */
        .card-back {
            width: 340px; height: 210px;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 8px 30px rgba(0,0,0,.18);
            display: flex; flex-direction: column;
        }
        .back-stripe { height: 36px; background: #0f172a; }
        .back-body { flex: 1; padding: 12px 16px; display: flex; gap: 14px; }
        .back-qr-zone { flex-shrink: 0; text-align: center; }
        .back-qr-label { font-size: 7.5px; color: #64748b; margin-top: 5px; line-height: 1.4; }
        .back-info { flex: 1; }
        .back-row { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #f1f5f9; font-size: 9px; }
        .back-row:last-child { border-bottom: none; }
        .back-lbl { color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
        .back-val { color: #0f172a; font-weight: 700; }
        .back-footer { padding: 8px 16px; background: #f8fafc; border-top: 1px solid #f1f5f9; font-size: 7.5px; color: #94a3b8; display: flex; justify-content: space-between; }

        /* ── Print styles ── */
        @media print {
            body { background: #fff; padding: 10px; }
            .screen-bar, .hint { display: none; }
            .card-wrap { gap: 20px; }
            .customer-card, .card-back {
                box-shadow: none;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .card-front {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
@php
    $customer = $booking->customer;
    $plot     = $booking->plot;
    $totalPaid = $booking->payments->sum('amount_paid');
    $remaining = $booking->total_price - $totalPaid;
    $prog = $booking->total_price > 0 ? min(round(($totalPaid / $booking->total_price) * 100), 100) : 0;
    $ledgerUrl = route('ledger.view', $booking->id);
@endphp

{{-- Screen buttons --}}
<div class="screen-bar">
    <a href="{{ route('ledger.view', $booking->id) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to Ledger
    </a>
    <button onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
        Print Card
    </button>
</div>

<div class="hint">
    💡 Print this page and cut along the card border — standard wallet size (85mm × 54mm). Give one to the customer to keep.
</div>

<div class="card-wrap">

    {{-- ════════════════════════════════ FRONT ════════════════════ --}}
    <div style="text-align:center; font-size:11px; font-weight:700; color:#64748b; letter-spacing:.5px; text-transform:uppercase;">Front Side</div>
    <div class="customer-card">
        <div class="card-front">

            {{-- Header --}}
            <div class="card-header">
                <div>
                    <div class="card-brand-name">Zamar Valley</div>
                    <div class="card-brand-tag">Real Estate & Development</div>
                </div>
                <div class="card-chip"></div>
            </div>

            {{-- Middle: avatar + info + QR --}}
            <div class="card-middle">
                <div class="card-avatar">
                    @if($customer->customer_pic)
                        <img src="{{ asset($customer->customer_pic) }}" alt="">
                    @else
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    @endif
                </div>
                <div class="card-info">
                    <div class="card-name">{{ $customer->name }}</div>
                    <div class="card-cnic">{{ $customer->cnic }}</div>
                    <div class="card-plot">
                        Plot #{{ $plot->plot_number }} — {{ $plot->block ?? '' }} | {{ $plot->size }} {{ $plot->unit }}
                    </div>
                    <div class="card-booking-id">ID: {{ $booking->customer_booking_id }}</div>
                </div>
                <div class="card-qr">
                    <div class="card-qr-box">
                        <div id="qrFront"></div>
                    </div>
                    <div class="card-qr-label">Scan for<br>ledger</div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="card-footer">
                <div class="card-footer-left">
                    <div class="card-type-badge">{{ $booking->booking_type ?? 'Installment' }}</div>
                    <div class="card-price-label">Total Price</div>
                    <div class="card-price-value">PKR {{ number_format($booking->total_price) }}</div>
                </div>
                <div class="card-footer-right">
                    <div><span class="card-status-dot"></span><span class="card-status-text">Active Member</span></div>
                    <div class="card-issued">Issued: {{ \Carbon\Carbon::parse($booking->booking_date)->format('M Y') }}</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ════════════════════════════════ BACK ═════════════════════ --}}
    <div style="text-align:center; font-size:11px; font-weight:700; color:#64748b; letter-spacing:.5px; text-transform:uppercase;">Back Side</div>
    <div class="card-back">
        <div class="back-stripe"></div>
        <div class="back-body">

            {{-- Big QR on back --}}
            <div class="back-qr-zone">
                <div style="background:#fff;border:2px solid #e8edf3;border-radius:8px;padding:6px;display:inline-block;">
                    <div id="qrBack"></div>
                </div>
                <div class="back-qr-label">Scan to view<br>full payment ledger</div>
                <div style="font-size:8px;font-weight:800;color:#1e3a8a;margin-top:4px;">{{ $booking->customer_booking_id }}</div>
            </div>

            {{-- Payment summary --}}
            <div class="back-info">
                <div style="font-size:9px;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:.7px;margin-bottom:8px;padding-bottom:5px;border-bottom:2px solid #f1f5f9;">Payment Summary</div>
                <div class="back-row"><span class="back-lbl">Total Price</span><span class="back-val">PKR {{ number_format($booking->total_price) }}</span></div>
                <div class="back-row"><span class="back-lbl">Total Paid</span><span class="back-val" style="color:#16a34a;">PKR {{ number_format($totalPaid) }}</span></div>
                <div class="back-row"><span class="back-lbl">Remaining</span><span class="back-val" style="color:{{ $remaining > 0 ? '#dc2626' : '#16a34a' }};">PKR {{ number_format(max($remaining,0)) }}</span></div>
                @if($booking->total_installments > 0)
                <div class="back-row"><span class="back-lbl">Installments</span><span class="back-val">{{ $booking->payments->where('payment_category','installment')->where('status','paid')->count() }}/{{ $booking->total_installments }} paid</span></div>
                @endif
                {{-- mini progress bar --}}
                <div style="margin-top:8px;">
                    <div style="height:4px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $prog }}%;background:linear-gradient(90deg,#1e3a8a,#4ade80);border-radius:4px;"></div>
                    </div>
                    <div style="font-size:8px;color:#94a3b8;text-align:right;margin-top:2px;">{{ $prog }}% collected</div>
                </div>
            </div>

        </div>
        <div class="back-footer">
            <span>{{ $customer->phone ?? '' }}</span>
            <span>zamarvalley.com</span>
        </div>
    </div>

</div>

{{-- Generate all 3 QR codes pointing to the same ledger URL --}}
<script>
    // Ensure the URL is as clean as possible
    const VERIFY_URL = '{!! $verificationUrl !!}';

    // Front QR - Make it black and increase the "Quiet Zone"
    var qrFront = new QRCode(document.getElementById('qrFront'), {
        text: VERIFY_URL,
        width: 60,         // Slightly larger if possible
        height: 60,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.L // 'L' uses the fewest dots possible
    });

    // Back QR - Increase the size for better optics
    var qrBack = new QRCode(document.getElementById('qrBack'), {
        text: VERIFY_URL,
        width: 100,        // Bigger is always better for scanning
        height: 100,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
    });
</script>
</body>
</html>
