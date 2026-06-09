<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transfer Verification — {{ $transfer->deed_no }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f6fb; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .card { background: #fff; border-radius: 18px; max-width: 480px; width: 100%; box-shadow: 0 8px 32px rgba(15,23,42,.12); overflow: hidden; }
    .card-header { background: linear-gradient(135deg, #0a0f1e 0%, #0f2460 60%, #1a3a9c 100%); padding: 24px; text-align: center; }
    .verified-icon { font-size: 2.5rem; margin-bottom: 8px; }
    .card-title { color: #fff; font-size: 18px; font-weight: 800; }
    .card-sub { color: rgba(255,255,255,.5); font-size: 11px; margin-top: 4px; }
    .card-body { padding: 24px; }
    .status-banner { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; }
    .status-completed { background: #dcfce7; border: 1px solid #bbf7d0; }
    .status-pending { background: #fef9c3; border: 1px solid #fde68a; }
    .status-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .status-text { font-size: 13px; font-weight: 700; }
    .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #64748b; font-weight: 600; }
    .info-value { color: #0f172a; font-weight: 700; text-align: right; }
    .section-head { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .7px; margin: 16px 0 8px; }
    .plot-swap { display: grid; grid-template-columns: 1fr auto 1fr; gap: 10px; align-items: center; margin-bottom: 16px; }
    .plot-box { background: #f0f9ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 12px; text-align: center; }
    .plot-no { font-size: 16px; font-weight: 800; color: #1e3a8a; }
    .plot-info { font-size: 10px; color: #64748b; margin-top: 3px; }
    .plot-owner { font-size: 10px; font-weight: 700; color: #0f172a; margin-top: 6px; padding-top: 6px; border-top: 1px solid #bfdbfe; }
    .arrow { font-size: 22px; color: #1e40af; text-align: center; }
    .footer { text-align: center; padding: 16px; background: #f8fafc; border-top: 1px solid #e4e9f2; font-size: 10px; color: #94a3b8; }
</style>
</head>
<body>
<div class="card">

    {{-- Header --}}
    <div class="card-header">
        <div class="verified-icon">🔄</div>
        <div class="card-title">Transfer Verified</div>
        <div class="card-sub">Zamar Valley — Secure Digital Record · Deed #{{ $transfer->deed_no }}</div>
    </div>

    {{-- Status banner --}}
    @php $tStatus = $transfer->status ?? 'pending'; @endphp
    <div style="padding:14px 20px 0;">
        <div class="status-banner status-{{ $tStatus }}">
            <div class="status-dot"
                 style="background:{{ $tStatus === 'completed' ? '#16a34a' : '#d97706' }};"></div>
            <div>
                <div class="status-text"
                     style="color:{{ $tStatus === 'completed' ? '#15803d' : '#92400e' }};">
                    Transfer {{ ucfirst($tStatus) }}
                </div>
                <div style="font-size:11px;color:#64748b;margin-top:2px;">
                    {{ $transfer->transfer_date ? \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') : '—' }}
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Financial Status Section --}}
        <div class="section-head">Financial Verification</div>
      <div class="info-row">
    <span class="info-label">Transfer Fee Status</span>
    <span class="info-value" style="font-weight: bold;">
        @if($isFeePaid)
            <span style="color: #16a34a;">✅ Transfer Fee Paid</span>
        @else
            <span style="color: #dc2626;">⏳ Transfer Fee Pending</span>
        @endif
    </span>
</div>

        {{-- Plot Details (from your Subject image) --}}
        <div class="section-head">Property Details</div>
        <div class="info-row">
            <span class="info-label">Block / Plot</span>
            <span class="info-value">
                {{ $transfer->fromBooking->plot->block ?? 'B Block' }} /
                #{{ $transfer->fromBooking->plot->plot_number ?? '—' }}
            </span>
        </div>

        {{-- Parties Section --}}
        <div class="section-head">Parties</div>
        <div class="info-row">
            <span class="info-label">Party A (Transferor)</span>
            <span class="info-value">{{ $transfer->fromCustomer->name ?? ($transfer->fromBooking->customer->name ?? '—') }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Party B (Transferee)</span>
            <span class="info-value">{{ $transfer->toCustomer->name ?? '—' }}</span>
        </div>

        {{-- Administration --}}
        <div class="section-head">Official Record</div>
        <div class="info-row">
            <span class="info-label">Deed No.</span>
            <span class="info-value">{{ $transfer->deed_no }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Verified Date</span>
            <span class="info-value">{{ now()->format('d M Y') }}</span>
        </div>
    </div>

    <div class="footer">
        Zamar Valley Digital Verification &nbsp;·&nbsp; Secure Document
    </div>
</div>
</body>
</html>
