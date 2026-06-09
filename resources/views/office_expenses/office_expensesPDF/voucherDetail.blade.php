<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Voucher — {{ $expense->voucher_no ?? $expense->id }}</title>
<style>
@page { margin: 0.35in 0.45in; }
* { box-sizing: border-box; -webkit-print-color-adjust: exact; }
body { font-family: 'DejaVu Sans', 'Helvetica', sans-serif; font-size: 9.5px; color: #1e293b; margin: 0; padding: 0; background: #fff; line-height: 1.5; }

/* ── Watermark ── */
.watermark { position: fixed; top: 35%; left: 10%; font-size: 80px; color: rgba(226,232,240,0.30); transform: rotate(-40deg); z-index: -999; font-weight: 900; text-transform: uppercase; letter-spacing: 4px; }

/* ── Header ── */
.header { border-bottom: 3px solid #1e3a8a; padding-bottom: 12px; margin-bottom: 0; }
.soc-name  { font-size: 22px; font-weight: 900; color: #1e3a8a; letter-spacing: -0.3px; }
.soc-sub   { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 1.2px; margin-top: 1px; }

/* ── Type badge strip ── */
.type-strip { padding: 8px 14px; margin-bottom: 18px; display: table; width: 100%; border-bottom: 1px solid #e2e8f0; }
.type-badge { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px; padding: 4px 14px; border-radius: 5px; display: inline-block; }
.type-expense   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.type-income    { background: #f0fdf4; color: #15803d; border: 1px solid #86efac; }
.type-inventory { background: #ecfdf5; color: #065f46; border: 1px solid #6ee7b7; }

/* ── Voucher number block ── */
.voucher-block { float: right; text-align: right; }
.voucher-no    { font-size: 15px; font-weight: 900; color: #1e3a8a; font-family: monospace; }
.voucher-lbl   { font-size: 7.5px; color: #64748b; text-transform: uppercase; letter-spacing: .7px; }

/* ── Info grid (2-col detail cards) ── */
.detail-section { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.detail-section td { vertical-align: top; padding: 0 6px 0 0; }
.dcard { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 9px 12px; margin-bottom: 8px; }
.dcard-lbl  { font-size: 7.5px; color: #64748b; text-transform: uppercase; letter-spacing: .7px; font-weight: 700; margin-bottom: 3px; }
.dcard-val  { font-size: 12px; font-weight: 700; color: #0f172a; }
.dcard-sub  { font-size: 9px; color: #64748b; margin-top: 2px; }

/* ── Amount hero ── */
.amount-hero { text-align: center; border-radius: 8px; padding: 14px; margin-bottom: 18px; }
.amount-hero .lbl   { font-size: 8px; text-transform: uppercase; letter-spacing: .8px; font-weight: 700; opacity: .7; }
.amount-hero .value { font-size: 32px; font-weight: 900; }

/* ── Fund source bar ── */
.fund-bar { border-radius: 6px; padding: 10px 14px; margin-bottom: 18px; border-left: 4px solid; }
.fund-bar-title { font-size: 7.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .7px; opacity: .75; margin-bottom: 4px; }
.fund-bar-name  { font-size: 13px; font-weight: 900; margin-bottom: 8px; }
.fund-stats { width: 100%; border-collapse: collapse; }
.fund-stat  { text-align: center; padding: 6px 10px; border-radius: 4px; background: rgba(255,255,255,.7); }
.fund-stat .sv { font-size: 12px; font-weight: 800; }
.fund-stat .sl { font-size: 7px; text-transform: uppercase; letter-spacing: .4px; margin-top: 2px; opacity: .65; }

/* ── Remarks box ── */
.remarks-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 5px; padding: 9px 12px; margin-bottom: 18px; }
.remarks-box .lbl { font-size: 7.5px; color: #92400e; text-transform: uppercase; letter-spacing: .7px; font-weight: 700; margin-bottom: 4px; }
.remarks-box .val { font-size: 10px; color: #451a03; line-height: 1.6; }

/* ── Status pill ── */
.status-approved { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
.status-pending  { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
.spill { font-size: 8.5px; font-weight: 800; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: .5px; display: inline-block; }

/* ── Proof indicator ── */
.proof-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 5px; padding: 8px 12px; margin-bottom: 16px; }

/* ── Timeline / activity row ── */
.timeline { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
.timeline td { padding: 7px 10px; font-size: 9px; }
.timeline tr:nth-child(even) td { background: #f8fafc; }
.tl-icon { font-size: 14px; text-align: center; width: 30px; }
.tl-title { font-weight: 700; font-size: 10px; color: #0f172a; }
.tl-sub   { color: #64748b; font-size: 8.5px; }

/* ── Signature section ── */
.sig-section { margin-top: 40px; }
.sig-box { text-align: center; border-top: 1px solid #94a3b8; padding-top: 5px; font-size: 8px; color: #475569; width: 140px; }

/* ── Footer ── */
.doc-footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 7px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 4px; }

.clear { clear: both; }
hr.divider { border: none; border-top: 1px solid #e2e8f0; margin: 14px 0; }
</style>
</head>
<body>

<div class="watermark">OFFICIAL</div>

@php
use Carbon\Carbon;
use App\Models\SystemConfig;
$societyName = SystemConfig::get('society_name', 'ZAMAR VALLEY');
$societySub  = SystemConfig::get('society_tagline', 'Real Estate Development & Society Management');
$typeColors = [
    'expense'   => ['bg'=>'#fef2f2','color'=>'#b91c1c','amtColor'=>'#b91c1c','borderColor'=>'#fecaca'],
    'income'    => ['bg'=>'#f0fdf4','color'=>'#15803d','amtColor'=>'#15803d','borderColor'=>'#86efac'],
    'inventory' => ['bg'=>'#ecfdf5','color'=>'#065f46','amtColor'=>'#065f46','borderColor'=>'#6ee7b7'],
];
$tc       = $typeColors[$expense->type] ?? $typeColors['expense'];
$typeWord = ucfirst($expense->type);
$dateFormatted = Carbon::parse($expense->expense_date)->format('d F Y');
$dayName       = Carbon::parse($expense->expense_date)->format('l');
$nowFormatted  = now()->format('d M Y, h:i A');

$fundBarColors = [
    'plot_payments'   => ['bg'=>'#eff6ff','color'=>'#1d4ed8','border'=>'#1d4ed8'],
    'security_fee'    => ['bg'=>'#fdf4ff','color'=>'#7c3aed','border'=>'#7c3aed'],
    'registry_fee'    => ['bg'=>'#e0f2fe','color'=>'#0369a1','border'=>'#0369a1'],
    'development_fee' => ['bg'=>'#f0fdf4','color'=>'#16a34a','border'=>'#16a34a'],
    'transfer_fee'    => ['bg'=>'#ecfeff','color'=>'#0891b2','border'=>'#0891b2'],
    'misc_income'     => ['bg'=>'#fffbeb','color'=>'#b45309','border'=>'#b45309'],
];
$fbc        = $fundLabel ? ($fundBarColors[$expense->fund_source] ?? null) : null;
$fundBal    = max(0, ($totalCollected ?? 0) - ($usedFromSource ?? 0));
$thisExpAmt = ($expense->type !== 'income') ? $expense->amount : 0;
$fundBalAfter = max(0, $fundBal - 0); // already deducted since it's in DB
@endphp

{{-- ══ HEADER ══ --}}
<div class="header">
    <table width="100%">
        <tr>
            <td>
                <div class="soc-name">{{ strtoupper($societyName) }}</div>
                <div class="soc-sub">{{ $societySub }}</div>
            </td>
            <td style="text-align:right;vertical-align:top;">
                <div class="voucher-lbl">Document No.</div>
                <div class="voucher-no">{{ $expense->voucher_no ?? ('ZV-'.$expense->id) }}</div>
                <div style="font-size:7.5px;color:#64748b;margin-top:3px;">Printed: {{ $nowFormatted }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ TYPE STRIP ══ --}}
<table style="width:100%;margin:10px 0 16px;" cellspacing="0">
    <tr>
        <td>
            <span class="type-badge type-{{ $expense->type }}">
                @if($expense->type==='expense') &#9660; Expense Voucher
                @elseif($expense->type==='income') &#9650; Income Receipt
                @else &#9670; Inventory Record
                @endif
            </span>
            &nbsp;
            <span style="font-size:8px;color:#64748b;">Category: <strong style="color:#0f172a;">{{ $expense->category }}</strong></span>
        </td>
        <td style="text-align:right;">
            <span class="spill {{ $expense->status === 'approved' ? 'status-approved' : 'status-pending' }}">
                {{ $expense->status === 'approved' ? '&#10003; Approved' : '&#9711; Pending' }}
            </span>
        </td>
    </tr>
</table>

{{-- ══ AMOUNT HERO ══ --}}
<div class="amount-hero" style="background:{{ $tc['bg'] }};border:2px solid {{ $tc['borderColor'] }};">
    <div class="lbl" style="color:{{ $tc['color'] }};">{{ strtoupper($typeWord) }} AMOUNT</div>
    <div class="value" style="color:{{ $tc['amtColor'] }};">PKR {{ number_format($expense->amount) }}</div>
    <div style="font-size:8.5px;color:#64748b;margin-top:4px;">
        {{ $dateFormatted }} &mdash; {{ $dayName }}
    </div>
</div>

{{-- ══ DETAIL GRID ══ --}}
<table class="detail-section" cellspacing="0">
    <tr>
        {{-- Left column --}}
        <td width="48%">
            <div class="dcard">
                <div class="dcard-lbl">
                    @if($expense->type==='income') Received From / Party
                    @elseif($expense->type==='inventory') Supplier / Party
                    @else Paid To / Party
                    @endif
                </div>
                <div class="dcard-val">{{ $expense->paid_to ?? '—' }}</div>
            </div>
            <div class="dcard">
                <div class="dcard-lbl">Payment Method</div>
                <div class="dcard-val">{{ ucwords(str_replace('_',' ',$expense->payment_method ?? '—')) }}</div>
            </div>
            <div class="dcard">
                <div class="dcard-lbl">Transaction / Reference No.</div>
                <div class="dcard-val" style="font-family:monospace;font-size:11px;">{{ $expense->reference_no ?? '—' }}</div>
            </div>
        </td>
        <td width="4%"></td>
        {{-- Right column --}}
        <td width="48%">
            <div class="dcard">
                <div class="dcard-lbl">Transaction Date</div>
                <div class="dcard-val">{{ $dateFormatted }}</div>
                <div class="dcard-sub">{{ $dayName }}</div>
            </div>
            <div class="dcard">
                <div class="dcard-lbl">Document / Voucher No.</div>
                <div class="dcard-val" style="font-family:monospace;font-size:11px;">{{ $expense->voucher_no ?? ('ZV-'.$expense->id) }}</div>
            </div>
            <div class="dcard">
                <div class="dcard-lbl">Record Type &amp; Category</div>
                <div class="dcard-val">{{ $typeWord }} &mdash; {{ $expense->category }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- ══ REMARKS / DESCRIPTION ══ --}}
@if($expense->remarks)
<div class="remarks-box">
    <div class="lbl">Description / Remarks</div>
    <div class="val">{{ $expense->remarks }}</div>
</div>
@endif

{{-- ══ FUND SOURCE BAR ══ --}}
@if($fundLabel && $fbc)
<div class="fund-bar" style="background:{{ $fbc['bg'] }};border-left-color:{{ $fbc['border'] }};color:{{ $fbc['color'] }};">
    <div class="fund-bar-title">Fund Source — where this money came from</div>
    <div class="fund-bar-name">{{ $fundLabel }}</div>
    <table class="fund-stats" cellspacing="4">
        <tr>
            <td class="fund-stat">
                <div class="sv" style="color:#15803d;">PKR {{ number_format($totalCollected) }}</div>
                <div class="sl">Total Collected</div>
            </td>
            <td class="fund-stat">
                <div class="sv" style="color:#dc2626;">PKR {{ number_format($usedFromSource) }}</div>
                <div class="sl">Used in Expenses</div>
            </td>
            <td class="fund-stat">
                <div class="sv" style="color:{{ $fbc['color'] }};">PKR {{ number_format($fundBal) }}</div>
                <div class="sl">Remaining Balance</div>
            </td>
            <td class="fund-stat" style="background:rgba(255,255,255,.9);border:1px solid {{ $fbc['border'] }};">
                <div class="sv" style="color:#dc2626;">PKR {{ number_format($expense->amount) }}</div>
                <div class="sl">This Voucher</div>
            </td>
        </tr>
    </table>
</div>
@elseif($expense->type === 'income')
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:5px;padding:10px 14px;margin-bottom:18px;">
    <div style="font-size:7.5px;color:#15803d;text-transform:uppercase;letter-spacing:.7px;font-weight:800;margin-bottom:3px;">Income Source</div>
    <div style="font-size:12px;font-weight:800;color:#15803d;">Office / Misc. Income Record</div>
    <div style="font-size:9px;color:#166534;margin-top:4px;">This amount has been recorded as income. It contributes to the available fund balance for office expenses.</div>
</div>
@endif

{{-- ══ TRANSACTION TIMELINE ══ --}}
<table class="timeline" cellspacing="0">
    <tr>
        <td class="tl-icon" style="color:#1d4ed8;">&#9679;</td>
        <td>
            <div class="tl-title">Record Created</div>
            <div class="tl-sub">{{ Carbon::parse($expense->created_at)->format('d M Y, h:i A') }} &mdash; entered into the system</div>
        </td>
        <td style="text-align:right;font-size:8.5px;color:#64748b;">{{ Carbon::parse($expense->created_at)->diffForHumans() }}</td>
    </tr>
    @if($expense->updated_at && $expense->updated_at->ne($expense->created_at))
    <tr>
        <td class="tl-icon" style="color:#7c3aed;">&#9679;</td>
        <td>
            <div class="tl-title">Last Updated</div>
            <div class="tl-sub">{{ Carbon::parse($expense->updated_at)->format('d M Y, h:i A') }}</div>
        </td>
        <td style="text-align:right;font-size:8.5px;color:#64748b;">{{ Carbon::parse($expense->updated_at)->diffForHumans() }}</td>
    </tr>
    @endif
    <tr>
        <td class="tl-icon" style="color:{{ $expense->status==='approved' ? '#15803d' : '#92400e' }};">&#9679;</td>
        <td>
            <div class="tl-title">Status: {{ ucfirst($expense->status) }}</div>
            <div class="tl-sub">
                @if($expense->status==='approved') This record has been reviewed and approved.
                @else This record is awaiting approval.
                @endif
            </div>
        </td>
        <td style="text-align:right;">
            <span class="spill {{ $expense->status === 'approved' ? 'status-approved' : 'status-pending' }}">{{ ucfirst($expense->status) }}</span>
        </td>
    </tr>
    @if($expense->payment_proof)
    <tr>
        <td class="tl-icon" style="color:#059669;">&#9679;</td>
        <td>
            <div class="tl-title">Payment Proof Attached</div>
            <div class="tl-sub">A proof document / receipt image has been uploaded with this record.</div>
        </td>
        <td style="text-align:right;font-size:8.5px;color:#059669;font-weight:700;">&#10003; Attached</td>
    </tr>
    @endif
</table>

{{-- ══ SUMMARY BOX ══ --}}
<table width="100%" cellspacing="0" style="margin-bottom:24px;">
    <tr>
        <td width="55%">
            {{-- QR Code --}}
            @if($qrCode)
            <div style="display:inline-block;border:1px solid #e2e8f0;padding:5px;border-radius:5px;">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="70" height="70">
                <div style="font-size:6.5px;color:#94a3b8;text-align:center;margin-top:3px;">Scan to verify</div>
            </div>
            <div style="font-size:7.5px;color:#64748b;margin-top:6px;max-width:180px;">
                This QR code links to the official digital record. Scan to verify authenticity of this voucher.
            </div>
            @endif
        </td>
        <td width="45%" style="vertical-align:bottom;">
            <table width="100%" cellspacing="0" style="border-collapse:collapse;">
                <tr style="background:#f8fafc;">
                    <td style="padding:7px 10px;font-size:9px;">Record Type</td>
                    <td style="padding:7px 10px;text-align:right;font-weight:700;font-size:9px;">{{ $typeWord }}</td>
                </tr>
                <tr>
                    <td style="padding:7px 10px;font-size:9px;">Category</td>
                    <td style="padding:7px 10px;text-align:right;font-weight:700;font-size:9px;">{{ $expense->category }}</td>
                </tr>
                <tr style="background:#f8fafc;">
                    <td style="padding:7px 10px;font-size:9px;">Payment Method</td>
                    <td style="padding:7px 10px;text-align:right;font-weight:700;font-size:9px;">{{ ucwords(str_replace('_',' ',$expense->payment_method ?? '')) }}</td>
                </tr>
                @if($fundLabel)
                <tr>
                    <td style="padding:7px 10px;font-size:9px;">Fund Source</td>
                    <td style="padding:7px 10px;text-align:right;font-weight:700;font-size:9px;color:{{ $fbc['color'] ?? '#1d4ed8' }};">{{ $fundLabel }}</td>
                </tr>
                @endif
                <tr style="background:#1e3a8a;">
                    <td style="padding:9px 10px;font-size:11px;font-weight:800;color:#fff;">TOTAL AMOUNT</td>
                    <td style="padding:9px 10px;text-align:right;font-size:13px;font-weight:900;color:#fff;">PKR {{ number_format($expense->amount) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ══ SIGNATURES ══ --}}
<div class="sig-section">
    <table width="100%">
        <tr>
            <td width="33%" style="text-align:center;">
                <div class="sig-box" style="margin:0 auto;">Prepared By</div>
            </td>
            <td width="33%" style="text-align:center;">
                <div style="width:68px;height:68px;border:2px dashed #cbd5e1;border-radius:50%;margin:-14px auto 0;padding-top:22px;color:#cbd5e1;font-weight:900;font-size:8px;text-align:center;">STAMP</div>
            </td>
            <td width="33%" style="text-align:center;">
                <div class="sig-box" style="margin:0 auto;">Authorized By</div>
            </td>
        </tr>
    </table>
</div>

<div class="doc-footer">
    System-generated document &mdash; {{ $societyName }} ERP &mdash; Voucher {{ $expense->voucher_no ?? $expense->id }} &mdash; Printed {{ $nowFormatted }}
</div>

</body>
</html>
