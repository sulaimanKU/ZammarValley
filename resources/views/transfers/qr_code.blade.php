@extends('layouts.index')
@section('content')
<style>
.qr-wrap { max-width: 520px; margin: 40px auto; padding: 0 16px; }
.qr-card {
    background: #fff; border-radius: 18px;
    border: 1px solid #e4e9f2;
    box-shadow: 0 4px 24px rgba(15,23,42,.08);
    overflow: hidden;
}
.qr-header {
    background: linear-gradient(135deg, #0a0f1e 0%, #0f2460 60%, #1a3a9c 100%);
    padding: 24px 28px; text-align: center;
}
.qr-header-title { color: #fff; font-size: 17px; font-weight: 800; margin: 0; }
.qr-header-sub { color: rgba(255,255,255,.5); font-size: 11px; margin-top: 4px; }
.qr-body { padding: 28px; text-align: center; }
.qr-deed { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .7px; margin-bottom: 6px; }
.qr-svg-wrap {
    display: inline-block;
    padding: 16px; background: #fff;
    border: 2px solid #e4e9f2; border-radius: 14px;
    box-shadow: 0 2px 12px rgba(15,23,42,.06);
    margin: 12px 0 18px;
}
.qr-hint { font-size: 11px; color: #94a3b8; margin-bottom: 20px; }
.qr-url-box {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 9px; padding: 10px 14px;
    font-size: 11px; color: #475569; word-break: break-all;
    margin-bottom: 20px; text-align: left;
}
.qr-url-label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }

/* ── Info tiles ── */
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; text-align: left; }
.info-tile { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; }
.info-tile-label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
.info-tile-value { font-size: 13px; font-weight: 800; color: #0f172a; margin-top: 3px; }

/* ── Actions ── */
.qr-actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; }
.btn-dl { background: #1e3a8a; color: #fff; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; }
.btn-dl:hover { background: #1e40af; color: #fff; }
.btn-print { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; }
.btn-print:hover { background: #e2e8f0; }
</style>

<div class="qr-wrap">
<div class="qr-card">

    {{-- Header ── --}}
    <div class="qr-header">
        <p class="qr-header-title">Transfer Verification QR</p>
        <p class="qr-header-sub">Zamar Valley Real Estate &nbsp;·&nbsp; {{ $transfer->deed_no }}</p>
    </div>

    <div class="qr-body">
        <div class="qr-deed">Deed No. {{ $transfer->deed_no }}</div>

        {{-- QR Code ── --}}
        <div class="qr-svg-wrap">
            {!! $qrCodeSvg !!}
        </div>

        <div class="qr-hint">
            <i class="bi bi-phone me-1"></i>
            Scan with any camera app to verify this transfer
        </div>

        {{-- Info tiles ── --}}
        <div class="info-grid">
            <div class="info-tile">
                <div class="info-tile-label">Transfer Type</div>
                <div class="info-tile-value">{{ ucfirst($transfer->transfer_type) }}</div>
            </div>
            <div class="info-tile">
                <div class="info-tile-label">Status</div>
                <div class="info-tile-value" style="color:{{ $transfer->status === 'completed' ? '#16a34a' : '#f59e0b' }};">
                    {{ ucfirst($transfer->status) }}
                </div>
            </div>
            <div class="info-tile">
                <div class="info-tile-label">Transfer Date</div>
                <div class="info-tile-value">{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</div>
            </div>
            <div class="info-tile">
                <div class="info-tile-label">Fee Status</div>
                <div class="info-tile-value" style="color:{{ $transfer->transfer_fee_status === 'paid' ? '#16a34a' : '#dc2626' }};">
                    {{ ucfirst($transfer->transfer_fee_status) }}
                </div>
            </div>

            @if($transfer->transfer_type === 'swap' && $swapBooking)
            <div class="info-tile">
                <div class="info-tile-label">Plot A</div>
                <div class="info-tile-value">Plot #{{ $transfer->fromBooking->plot->plot_number ?? '—' }}</div>
            </div>
            <div class="info-tile">
                <div class="info-tile-label">Plot B</div>
                <div class="info-tile-value">Plot #{{ $swapBooking->plot->plot_number ?? '—' }}</div>
            </div>
            @endif
        </div>

        {{-- Verification URL ── --}}
        <div class="qr-url-box">
            <div class="qr-url-label">Verification URL</div>
            {{ $qrUrl }}
        </div>

        {{-- Actions ── --}}
        <div class="qr-actions">
            @if($transfer->transfer_type === 'swap')
            <a href="{{ route('transfer.swap.deed', $transfer->id) }}" class="btn-dl" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Download Swap Deed PDF
            </a>
            @endif
            <button onclick="window.print()" class="btn-print">
                <i class="bi bi-printer"></i> Print QR
            </button>
        </div>
    </div>

</div>
</div>
@endsection
