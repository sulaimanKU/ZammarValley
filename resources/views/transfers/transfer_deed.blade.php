<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Transfer Deed — {{ $transfer->deed_no }}</title>
<style>
@page { margin: 0.3in 0.35in; size: A4 portrait; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, sans-serif; font-size: 8.5px; color: #1e293b; background: #fff; line-height: 1.4; }

.wm { position: fixed; top: 38%; left: 5%; font-size: 90px; font-weight: 900;
      color: rgba(30,58,138,0.025); transform: rotate(-30deg); z-index: -1;
      letter-spacing: 10px; text-transform: uppercase; white-space: nowrap; }

.outer { border: 2.5px solid #1e3a8a; padding: 5px; }
.inner { border: 1px solid #c8a96e; padding: 12px 14px; }

/* ── HEADER ── */
.hdr { border-bottom: 2px solid #1e3a8a; padding-bottom: 8px; margin-bottom: 8px; }
.hdr table { width: 100%; border-collapse: collapse; }
.org-name { font-size: 18px; font-weight: 900; color: #0f172a; letter-spacing: 2px; text-transform: uppercase; }
.org-sub  { font-size: 6.5px; color: #94a3b8; letter-spacing: 3px; text-transform: uppercase; margin-top: 1px; }
.gold-div { width: 60px; border: none; border-top: 1.5px solid #c8a96e; margin: 5px auto; }
.doc-title { font-size: 12px; font-weight: 900; color: #1e3a8a; letter-spacing: 1.5px; text-transform: uppercase; }
.doc-cert  { font-size: 6.5px; color: #94a3b8; letter-spacing: 2px; text-transform: uppercase; margin-top: 1px; }
.logo-box { width: 48px; height: 48px; border-radius: 50%; border: 2px solid #1e3a8a; text-align: center; line-height: 44px; }
.logo-box img { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }

/* ── REF BAR ── */
.ref-bar { background: #0f172a; border-radius: 4px; padding: 6px 8px; margin-bottom: 10px; }
.ref-bar table { width: 100%; border-collapse: collapse; }
.ref-bar td { text-align: center; border-right: 1px solid rgba(255,255,255,0.1); padding: 0 6px; }
.ref-bar td:last-child { border-right: none; }

/* ── SECTION TITLE ── */
.sec { font-size: 7px; font-weight: 900; color: #1e3a8a; text-transform: uppercase;
       letter-spacing: 1.5px; padding-bottom: 3px; margin: 8px 0 5px;
       border-bottom: 1.5px solid #1e3a8a; }

/* ── PARTIES BANNER ── */
.pb-wrap { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 3px solid #1e3a8a;
           border-radius: 4px; margin-bottom: 10px; }
.pb-table { width: 100%; border-collapse: collapse; }
.pb-from  { width: 41%; vertical-align: top; padding: 10px 12px; }
.pb-arrow { width: 18%; vertical-align: middle; text-align: center; padding: 10px 4px; }
.pb-to    { width: 41%; vertical-align: top; padding: 10px 12px; text-align: right; }
.pb-badge-red   { font-size: 6px; font-weight: 800; color: #dc2626; background: #fee2e2; padding: 2px 6px; border-radius: 3px; display: inline-block; margin-bottom: 5px; }
.pb-badge-green { font-size: 6px; font-weight: 800; color: #15803d; background: #dcfce7; padding: 2px 6px; border-radius: 3px; display: inline-block; margin-bottom: 5px; }
.pb-name { font-size: 10.5px; font-weight: 900; color: #0f172a; margin-bottom: 2px; }
.pb-cnic { font-size: 7px; color: #64748b; }
.pb-bid  { font-size: 7px; background: #e0f2fe; color: #0369a1; border-radius: 3px; padding: 2px 6px; display: inline-block; }
.pb-bid-g{ background: #dcfce7; color: #15803d; }
.arrow-box { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #cbd5f5; background: #eef2ff; margin: 0 auto 3px; text-align: center; padding-top: 5px; }
.arrow-sym { font-size: 16px; font-weight: 900; color: #1e3a8a; }
.arrow-lbl { font-size: 6px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
.pct-big   { font-size: 12px; font-weight: 900; color: #f59e0b; }

/* ── PROPERTY BOX ── */
.prop-box { border: 1px solid #bfdbfe; border-radius: 4px; padding: 7px 10px; margin-bottom: 8px; background: #f8fafc; }
.prop-box table { width: 100%; border-collapse: collapse; }
.prop-box td { padding: 2px 4px; }
.pl { font-size: 7px; color: #64748b; font-weight: 700; width: 28%; }
.pv { font-size: 8px; color: #0f172a; font-weight: 800; }

/* ── FINANCIAL TABLE ── */
.fin { width: 100%; border-collapse: collapse; margin-bottom: 7px; }
.fin th { background: #1e3a8a; color: #fff; font-size: 6.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 7px; text-align: left; }
.fin td { padding: 3px 7px; font-size: 7.5px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.fin tr:last-child td { border-bottom: none; }
.fin tr:nth-child(even) td { background: #f8fafc; }
.fin .lbl { color: #475569; font-weight: 600; width: 60%; }
.fin .val { font-weight: 900; color: #0f172a; text-align: right; }

/* ── FEE STATUS TABLE ── */
.fee-tbl { width: 100%; border-collapse: collapse; margin-bottom: 7px; }
.fee-tbl th { background: #0f172a; color: #fff; font-size: 6px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 7px; text-align: left; }
.fee-tbl td { padding: 3.5px 7px; font-size: 7px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.fee-tbl tr:last-child td { border-bottom: none; }
.fee-tbl tr:nth-child(even) td { background: #fafbfc; }
.fee-lbl { color: #475569; font-weight: 600; width: 30%; }
.fee-amt { font-weight: 800; color: #0f172a; text-align: right; width: 22%; }
.fee-paid{ font-weight: 800; color: #16a34a; text-align: right; width: 22%; }
.fee-rem { font-weight: 800; color: #dc2626; text-align: right; width: 26%; }

/* ── OWNERSHIP CHAIN TABLE ── */
.chain-tbl { width: 100%; border-collapse: collapse; margin-bottom: 7px; }
.chain-tbl th { background: #1e3a8a; color: #fff; font-size: 6px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 7px; text-align: left; }
.chain-tbl td { padding: 3.5px 7px; font-size: 7px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.chain-tbl tr:last-child td { border-bottom: none; }
.chain-tbl .this-row td { background: #eff6ff; font-weight: 800; }

/* ── BADGE ── */
.badge { font-size: 6.5px; font-weight: 800; padding: 1px 6px; border-radius: 8px; display: inline-block; }
.b-paid    { background: #dcfce7; color: #15803d; }
.b-partial { background: #fef9c3; color: #854d0e; }
.b-pending { background: #fef9c3; color: #854d0e; }
.b-waived  { background: #eff6ff; color: #1d4ed8; }
.b-na      { background: #f1f5f9; color: #94a3b8; }

/* ── DECLARATION ── */
.decl { background: #fffbeb; border: 1px solid #fde68a; border-left: 3px solid #c8a96e; border-radius: 3px; padding: 6px 9px; }
.decl p { font-size: 7px; color: #78350f; line-height: 1.6; font-style: italic; }

/* ── INFO TABLE ── */
.info-tbl { width: 100%; border-collapse: collapse; }
.info-tbl td { padding: 2.5px 0; border-bottom: 1px dashed #f1f5f9; font-size: 7.5px; vertical-align: top; }
.info-tbl tr:last-child td { border-bottom: none; }
.il { color: #64748b; font-weight: 700; width: 48%; font-size: 7px; }
.iv { font-weight: 800; color: #0f172a; }

/* ── SIGNATURES ── */
.sig-tbl { width: 100%; border-collapse: collapse; margin-top: 8px; }
.sig-td  { text-align: center; padding: 0 4px; vertical-align: bottom; }
.sig-sp  { height: 24px; }
.sig-line { border-top: 1px solid #0f172a; padding-top: 3px; }
.sig-name { font-size: 7.5px; font-weight: 900; color: #0f172a; }
.sig-role { font-size: 6px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
.stamp { width: 52px; height: 52px; border-radius: 50%; border: 2px double #c8a96e; margin: 5px auto; text-align: center; padding-top: 7px; }
.stamp-t { font-size: 5.5px; font-weight: 800; color: #c8a96e; text-transform: uppercase; }
.stamp-m { font-size: 10px; font-weight: 900; color: #0f172a; }

/* ── FOOTER ── */
.footer { border-top: 1.5px solid #1e3a8a; margin-top: 8px; padding-top: 7px; }
.footer table { width: 100%; border-collapse: collapse; }
.foot-mid { font-size: 7px; color: #94a3b8; line-height: 1.7; text-align: center; }
.foot-mid strong { color: #0f172a; font-size: 8px; }
.doc-trail { text-align: center; font-size: 6px; color: #cbd5e1; margin-top: 5px; border-top: 1px dashed #e2e8f0; padding-top: 4px; }
</style>
</head>
<body>

@php
    $sc = $sc ?? [];
    $socName    = $sc['name']     ?? 'Zamar Valley';
    $socTagline = $sc['tagline']  ?? 'Premium Real Estate Development';
    $socPhone   = $sc['phone']    ?? '';
    $socPhone2  = $sc['phone2']   ?? '';
    $socPhone3  = $sc['phone3']   ?? '';
    $socEmail   = $sc['email']    ?? '';
    $socAddress = $sc['address']  ?? '';
    $socLogo    = $sc['logo']     ?? null;
    $showLogo   = $sc['show_logo'] ?? true;

    $fromC    = $transfer->fromCustomer ?? $transfer->fromBooking?->customer;
    $toC      = $transfer->toCustomer   ?? $transfer->toBooking?->customer;
    $plot     = $transfer->plot         ?? $transfer->fromBooking?->plot;
    $fromBook = $transfer->fromBooking;
    $toBook   = $transfer->toBooking;

    $totalPrice    = (float)($fromBook->total_price ?? 0);
    $payments      = $fromBook?->payments ?? collect();
    $plotCats      = ['down_payment','installment','quarterly_installment','plot_balance','others'];
    $discSentinel  = 'Settlement discount — waived amount (not collected).';

    // Use the balance stored at transfer time as the authoritative remaining figure.
    // This avoids any discrepancy from re-summing payment records.
    $balanceTransferred = (float)($transfer->remaining_balance_transferred ?? 0);

    // Discount/waiver credits only: old sentinel rows + new discount_amount column
    $discCredits = $payments->where('status','paid')->whereIn('payment_category', $plotCats)
        ->filter(fn($p) => ($p->remarks ?? '') === $discSentinel)->sum('amount_paid')
        + $payments->where('status','paid')->whereIn('payment_category', $plotCats)
        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)->sum('discount_amount');

    // Cash paid = total price − remaining balance (stored snapshot) − discount credits
    $cashPaid  = max(0, $totalPrice - $balanceTransferred - $discCredits);
    $totalPaid = $cashPaid;

    $typeLabels = [
        'ownership' => 'Ownership Transfer Deed',
        'partial'   => 'Partial Transfer Deed',
        'internal'  => 'Internal Transfer Deed',
    ];
    $typeLabel = $typeLabels[$transfer->transfer_type] ?? 'Transfer Deed';

    // ── Fee helpers ───────────────────────────────────────────────
    // booking_fees is keyed by fee_type
    $fromFees = $fromBookingFees ?? collect();
    $toFees   = $toBookingFees   ?? collect();

    // fee_type names map (fee_types table: 1=registration, 2=development, 3=security, 4=transfer)
    $feeNames = [
        'registry'    => 'Registry / Registration',
        'development' => 'Development',
        'security'    => 'Security',
        'transfer'    => 'Transfer',
    ];

    // Helper to get badge class from fee status
    $feeStatusBadge = function($status) {
        return match($status ?? 'pending') {
            'paid'    => 'b-paid',
            'partial' => 'b-partial',
            default   => 'b-pending',
        };
    };

    // Transfer fee bill for this transfer
    $tfBill     = $transferFeeBill ?? null;
    $tfPayments = $transferFeePayments ?? collect();
@endphp

<div class="wm">DEED</div>

<div class="outer"><div class="inner">

{{-- ════ HEADER ════ --}}
<div class="hdr">
    <table>
        <tr>
            <td style="width:55px;vertical-align:middle;">
                <div class="logo-box">
                    @if($showLogo && $socLogo)
                        <img src="{{ $socLogo }}" alt="">
                    @else
                        <span style="font-size:20px;font-weight:900;color:#1e3a8a;">{{ strtoupper(substr($socName,0,1)) }}</span>
                    @endif
                </div>
            </td>
            <td style="text-align:center;vertical-align:middle;">
                <div class="org-name">{{ $socName }}</div>
                <div class="org-sub">{{ $socTagline }}</div>
                <hr class="gold-div">
                <div class="doc-title">{{ $typeLabel }}</div>
                <div class="doc-cert">Certificate of Transfer — Official Document</div>
            </td>
            <td style="width:90px;vertical-align:middle;text-align:right;">
                <span style="font-size:8px;font-weight:900;color:#0f172a;">{{ $transfer->deed_no }}</span><br>
                <span style="font-size:7px;color:#94a3b8;">{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</span><br>
                <span style="font-size:6.5px;color:#94a3b8;text-transform:uppercase;">{{ $transfer->transfer_type }}</span><br>
                <span class="badge {{ $transfer->status==='completed'?'b-paid':'b-pending' }}">{{ strtoupper($transfer->status) }}</span>
            </td>
        </tr>
    </table>
</div>

{{-- ════ REF BAR ════ --}}
<div class="ref-bar">
    <table>
        <tr>
            <td><span style="font-size:6px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.7px;display:block;">Deed No.</span><span style="font-size:9px;font-weight:900;color:#f6c90e;">{{ $transfer->deed_no }}</span></td>
            <td><span style="font-size:6px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.7px;display:block;">Transfer Date</span><span style="font-size:9px;font-weight:900;color:#fff;">{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</span></td>
            <td><span style="font-size:6px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.7px;display:block;">Plot No.</span><span style="font-size:9px;font-weight:900;color:#fff;">{{ $plot->plot_number ?? '—' }}</span></td>
            <td><span style="font-size:6px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.7px;display:block;">Block</span><span style="font-size:9px;font-weight:900;color:#fff;">{{ $plot->block ?? '—' }}</span></td>
            <td><span style="font-size:6px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.7px;display:block;">Size</span><span style="font-size:9px;font-weight:900;color:#fff;">{{ $plot->size ?? '—' }} {{ $plot->unit ?? '' }}</span></td>
            <td><span style="font-size:6px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.7px;display:block;">Status</span><span style="font-size:9px;font-weight:900;color:#f6c90e;">{{ strtoupper($transfer->status) }}</span></td>
        </tr>
    </table>
</div>

{{-- ════ PARTIES ════ --}}
<div class="sec">Transfer Parties</div>
<div class="pb-wrap">
<table class="pb-table">
<tr>
    <td class="pb-from">
        <div class="pb-badge-red">{{ $transfer->transfer_type==='internal' ? 'Original Owner' : 'Transferor (Seller)' }}</div><br>
        <div class="pb-name">{{ $fromC->name ?? '—' }}</div>
        <div class="pb-cnic">CNIC: {{ $fromC->cnic ?? '—' }}</div>
        <div class="pb-cnic">Phone: {{ $fromC->phone ?? $fromC->mobile ?? '—' }}</div>
        @if(!empty($fromC->address))<div class="pb-cnic">{{ $fromC->address }}</div>@endif
        <br><span class="pb-bid">{{ $fromBook->customer_booking_id ?? '—' }}</span>
    </td>
    <td class="pb-arrow">
        <div class="arrow-box"><div class="arrow-sym">{{ $transfer->transfer_type==='internal'?'~':($transfer->transfer_type==='partial'?'%':'>') }}</div></div>
        <div class="arrow-lbl">{{ strtoupper($transfer->transfer_type) }}</div>
        @if($transfer->transfer_type==='partial')<div class="pct-big">{{ $transfer->ownership_percentage }}%</div>@endif
    </td>
    <td class="pb-to">
        @if($transfer->transfer_type === 'internal')
            <div class="pb-badge-green">After Relocation</div><br>
            <div class="pb-name">{{ $fromC->name ?? '—' }}</div>
            <div class="pb-cnic">Same Owner — Plot Relocated</div>
            <br><span class="pb-bid pb-bid-g">Plot #{{ $transfer->new_plot_number }} — Block {{ $transfer->new_block }}</span>
        @else
            <div class="pb-badge-green">Transferee (New Owner)</div><br>
            <div class="pb-name">{{ $toC->name ?? '—' }}</div>
            <div class="pb-cnic">CNIC: {{ $toC->cnic ?? '—' }}</div>
            <div class="pb-cnic">Phone: {{ $toC->phone ?? $toC->mobile ?? '—' }}</div>
            @if(!empty($toC->address))<div class="pb-cnic">{{ $toC->address }}</div>@endif
            <br>@if($toBook)<span class="pb-bid pb-bid-g">{{ $toBook->customer_booking_id }}</span>@endif
        @endif
    </td>
</tr>
</table>
</div>

{{-- ════ TWO COLUMN ════ --}}
<table style="width:100%;border-collapse:collapse;">
<tr>

{{-- ── LEFT ── --}}
<td style="width:56%;vertical-align:top;padding-right:11px;">

    {{-- PROPERTY --}}
    <div class="sec">Property Details</div>
    <div class="prop-box">
        <table>
            <tr>
                <td class="pl">Plot Number</td><td class="pv">Plot #{{ $plot->plot_number ?? '—' }}</td>
                <td class="pl">Block</td><td class="pv">Block {{ $plot->block ?? '—' }}</td>
            </tr>
            <tr>
                <td class="pl">Plot Size</td><td class="pv">{{ $plot->size ?? '—' }} {{ $plot->unit ?? '' }}</td>
                <td class="pl">Category</td><td class="pv">{{ $plot->category->name ?? 'Residential' }}</td>
            </tr>
            <tr>
                <td class="pl">Society</td><td class="pv" colspan="3">{{ $socName }}, {{ $plot->city ?? 'Pakistan' }}</td>
            </tr>
            @if($transfer->transfer_type === 'internal')
            <tr>
                <td class="pl">New Block</td><td class="pv">{{ $transfer->new_block }}</td>
                <td class="pl">New Plot No.</td><td class="pv">{{ $transfer->new_plot_number }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- FINANCIAL --}}
    <div class="sec">Financial Summary</div>
    <table class="fin">
        <tr><th style="width:60%;">Description</th><th style="text-align:right;">Amount (PKR)</th></tr>
        <tr><td class="lbl">Original Plot Price</td><td class="val">{{ number_format($totalPrice) }}</td></tr>
        <tr><td class="lbl">Cash Paid by Transferor</td><td class="val" style="color:#16a34a;">{{ number_format($cashPaid) }}</td></tr>
        @if($discCredits > 0)
        <tr><td class="lbl" style="color:#92400e;">Settlement Discount / Waiver</td><td class="val" style="color:#92400e;">{{ number_format($discCredits) }}</td></tr>
        @endif
        <tr><td class="lbl">Remaining Balance Transferred to Buyer</td><td class="val" style="color:#dc2626;">{{ number_format($balanceTransferred) }}</td></tr>
        <tr>
            <td class="lbl">Transfer Fee &nbsp;
                @if($tfBill)
                    <span class="badge {{ $feeStatusBadge($tfBill->status) }}">{{ ucfirst($tfBill->status) }}</span>
                @else
                    <span class="badge b-na">Not Set</span>
                @endif
            </td>
            <td class="val">
                @if($tfBill) PKR {{ number_format($tfBill->paid_amount) }} / {{ $tfBill->amount > 0 ? number_format($tfBill->amount) : 'Open' }}
                @else —
                @endif
            </td>
        </tr>
        @if($transfer->transfer_type === 'partial')
        <tr style="background:#0f172a !important;">
            <td class="lbl" style="background:#0f172a;color:#fff;">Ownership Share Transferred</td>
            <td class="val" style="background:#0f172a;color:#f6c90e;">{{ $transfer->ownership_percentage }}%</td>
        </tr>
        @endif
    </table>

    {{-- FEE STATUS — Seller (From Booking) --}}
    <div class="sec">Fee Status — Seller's Booking</div>
    <table class="fee-tbl">
        <tr><th>Fee Type</th><th style="text-align:right;">Billed</th><th style="text-align:right;">Paid</th><th style="text-align:right;">Remaining / Status</th></tr>
        @foreach(['registry'=>'Registry / Reg.','development'=>'Development','security'=>'Security','transfer'=>'Transfer'] as $fKey => $fName)
        @php
            $bill = $fromFees->get($fKey);
        @endphp
        @if($fromBook && ($fKey !== 'registry' || $fromBook->has_registry_fee) && ($fKey !== 'development' || $fromBook->has_development_fee))
        <tr>
            <td class="fee-lbl">{{ $fName }}</td>
            <td class="fee-amt">{{ $bill ? 'PKR '.number_format($bill->amount) : '—' }}</td>
            <td class="fee-paid">{{ $bill ? 'PKR '.number_format($bill->paid_amount) : 'PKR 0' }}</td>
            <td class="fee-rem">
                @if(!$bill)
                    <span class="badge b-na">N/A</span>
                @elseif($bill->status === 'paid')
                    <span class="badge b-paid">✓ Paid</span>
                @elseif($bill->status === 'partial')
                    <span class="badge b-partial">Partial</span>
                @else
                    <span class="badge b-pending">Pending</span>
                @endif
            </td>
        </tr>
        @endif
        @endforeach
        @if($fromFees->isEmpty())
        <tr><td colspan="4" style="text-align:center;color:#94a3b8;font-size:7px;">No fee records</td></tr>
        @endif
    </table>

    {{-- TRANSFER FEE PAYMENTS --}}
    @if($tfPayments->count() > 0)
    <div class="sec">Transfer Fee Payment History</div>
    <table class="fee-tbl">
        <tr><th>Receipt</th><th>Date</th><th style="text-align:right;">Amount</th><th>Mode</th></tr>
        @foreach($tfPayments as $tfp)
        <tr>
            <td style="font-family:monospace;font-size:6.5px;">{{ $tfp->receipt_no }}</td>
            <td>{{ \Carbon\Carbon::parse($tfp->paid_date)->format('d M Y') }}</td>
            <td style="text-align:right;font-weight:800;color:#16a34a;">PKR {{ number_format($tfp->amount) }}</td>
            <td>{{ ucfirst(str_replace('_',' ',$tfp->payment_mode)) }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    {{-- DECLARATION --}}
    <div class="sec">Declaration</div>
    <div class="decl">
        <p>
            This deed certifies that <strong>{{ $fromC->name ?? '—' }}</strong>
            @if($transfer->transfer_type !== 'internal')
                (CNIC: {{ $fromC->cnic ?? '—' }}) has voluntarily transferred
                @if($transfer->transfer_type === 'partial')
                    <strong>{{ $transfer->ownership_percentage }}%</strong> ownership share of
                @else full ownership rights of @endif
                Plot #{{ $plot->plot_number ?? '—' }}, Block {{ $plot->block ?? '—' }}, {{ $socName }}
                to <strong>{{ $toC->name ?? '—' }}</strong> (CNIC: {{ $toC->cnic ?? '—' }})
                effective <strong>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</strong>.
                The transferee assumes remaining financial obligations of <strong>PKR {{ number_format($balanceTransferred) }}</strong>.
            @else
                has been relocated from Plot #{{ $plot->plot_number ?? '—' }}, Block {{ $plot->block ?? '—' }}
                to Plot #{{ $transfer->new_plot_number }}, Block {{ $transfer->new_block }}
                effective <strong>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</strong>.
                All financial records remain unchanged.
            @endif
            This document is duly recorded in the {{ $socName }} official registry.
        </p>
    </div>

</td>

{{-- ── RIGHT ── --}}
<td style="width:44%;vertical-align:top;">

    {{-- TRANSFER INFO --}}
    <div class="sec">Transfer Information</div>
    <table class="info-tbl">
        <tr><td class="il">Deed Number</td><td class="iv" style="color:#1e3a8a;">{{ $transfer->deed_no }}</td></tr>
        <tr><td class="il">Transfer Type</td><td class="iv">{{ ucfirst($transfer->transfer_type) }}</td></tr>
        <tr><td class="il">Transfer Date</td><td class="iv">{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</td></tr>
        <tr>
            <td class="il">Status</td>
            <td class="iv"><span class="badge {{ $transfer->status==='completed'?'b-paid':'b-pending' }}">{{ ucfirst($transfer->status) }}</span></td>
        </tr>
        <tr><td class="il">Original Booking</td><td class="iv">{{ $fromBook->customer_booking_id ?? '—' }}</td></tr>
        @if($toBook)<tr><td class="il">New Booking</td><td class="iv">{{ $toBook->customer_booking_id }}</td></tr>@endif
        @if($transfer->reason)<tr><td class="il">Reason</td><td class="iv">{{ $transfer->reason }}</td></tr>@endif
        @if($transfer->witness1_name)
        <tr><td class="il">Witness 1</td><td class="iv">{{ $transfer->witness1_name }}@if($transfer->witness1_cnic)<br><span style="font-size:6.5px;color:#64748b;">{{ $transfer->witness1_cnic }}</span>@endif</td></tr>
        @endif
        @if($transfer->witness2_name)
        <tr><td class="il">Witness 2</td><td class="iv">{{ $transfer->witness2_name }}@if($transfer->witness2_cnic)<br><span style="font-size:6.5px;color:#64748b;">{{ $transfer->witness2_cnic }}</span>@endif</td></tr>
        @endif
    </table>

    {{-- ════ FULL OWNERSHIP CHAIN ════ --}}
    <div class="sec" style="margin-top:7px;">Complete Ownership Chain — Plot #{{ $plot->plot_number ?? '—' }} · {{ $plot->block ?? '' }}</div>

    @php
        $allT = $allTransfers ?? collect();

        // Build ordered node list: original owner first, then each transfer's "to" person
        $chainNodes = [];
        if (isset($originalBooking) && $originalBooking) {
            $chainNodes[] = [
                'name'    => $originalBooking->customer->name ?? '—',
                'cnic'    => $originalBooking->customer->cnic ?? '',
                'ref'     => $originalBooking->customer_booking_id,
                'date'    => $originalBooking->booking_date,
                'label'   => 'Original Owner',
                'bg'      => '#f0fdf4',
                'border'  => '#86efac',
                'color'   => '#15803d',
                'transfer'=> null,
            ];
        }
        foreach ($allT as $ct) {
            $ctTo = $ct->toCustomer ?? $ct->toBooking?->customer;
            $isThis = $ct->id === $transfer->id;
            $chainNodes[] = [
                'name'    => $ct->transfer_type === 'internal' ? ($chainNodes[count($chainNodes)-1]['name'] ?? '—') : ($ctTo->name ?? '—'),
                'cnic'    => $ct->transfer_type === 'internal' ? '' : ($ctTo->cnic ?? ''),
                'ref'     => $ct->toBooking->customer_booking_id ?? $ct->deed_no,
                'date'    => $ct->transfer_date,
                'label'   => $ct->transfer_type === 'internal' ? 'Relocated' : ($isThis ? 'Current Owner (This Deed)' : 'Owner'),
                'bg'      => $isThis ? '#fef9c3' : '#eff6ff',
                'border'  => $isThis ? '#fde047' : '#bfdbfe',
                'color'   => $isThis ? '#713f12' : '#1d4ed8',
                'transfer'=> $ct,
            ];
        }

        $completedCount = (int)($transfer->plot->transfer_count ?? $allT->where('status','completed')->count());
    @endphp

    @if(count($chainNodes) > 0)
    <div style="margin-bottom:6px;">
        @foreach($chainNodes as $ni => $node)

        {{-- Person node --}}
        <div style="display:inline-block;vertical-align:middle;background:{{ $node['bg'] }};border:1px solid {{ $node['border'] }};border-radius:5px;padding:4px 8px;max-width:115px;text-align:center;">
            <div style="font-size:5.5px;font-weight:800;color:{{ $node['color'] }};text-transform:uppercase;letter-spacing:.3px;margin-bottom:1px;">{{ $node['label'] }}</div>
            <div style="font-size:7.5px;font-weight:900;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $node['name'] }}</div>
            @if($node['cnic'])<div style="font-size:5.5px;color:#64748b;font-family:monospace;">{{ $node['cnic'] }}</div>@endif
            <div style="font-size:5.5px;color:#94a3b8;margin-top:1px;">{{ \Carbon\Carbon::parse($node['date'])->format('d M Y') }}</div>
            <div style="font-size:5px;color:#94a3b8;font-family:monospace;">{{ $node['ref'] }}</div>
        </div>

        {{-- Arrow between this node and the next, using the next node's transfer info --}}
        @if(!$loop->last)
        @php $arrow = $chainNodes[$ni + 1]['transfer'] ?? null; @endphp
        <div style="display:inline-block;vertical-align:middle;text-align:center;margin:0 3px;">
            @if($arrow)
            <div style="font-size:5.5px;color:#64748b;font-weight:700;white-space:nowrap;">{{ $arrow->deed_no }}</div>
            @endif
            <div style="font-size:11px;color:#1d4ed8;font-weight:900;line-height:1;">&#8594;</div>
            @if($arrow)
            <div style="font-size:5.5px;color:#64748b;white-space:nowrap;">{{ \Carbon\Carbon::parse($arrow->transfer_date)->format('d M Y') }}</div>
            <div style="font-size:5px;padding:1px 3px;border-radius:2px;font-weight:800;white-space:nowrap;
                background:{{ $arrow->transfer_type==='ownership'?'#eff6ff':($arrow->transfer_type==='partial'?'#fff7ed':'#fdf4ff') }};
                color:{{ $arrow->transfer_type==='ownership'?'#1d4ed8':($arrow->transfer_type==='partial'?'#ea580c':'#7c3aed') }};">
                {{ ucfirst($arrow->transfer_type) }}@if($arrow->transfer_type==='partial') · {{ $arrow->ownership_percentage }}%@endif
            </div>
            @endif
        </div>
        @endif

        @endforeach
    </div>
    <div style="font-size:6px;color:#94a3b8;margin-bottom:6px;text-align:right;">
        {{ $allT->count() }} transfer{{ $allT->count()!==1?'s':'' }} on record
        · {{ $completedCount }} completed · Max allowed: 5
        · <strong style="color:{{ $completedCount>=5?'#dc2626':'#15803d' }};">{{ max(0,5-$completedCount) }} remaining</strong>
    </div>
    @else
    <div style="font-size:7px;color:#94a3b8;padding:6px;border:1px dashed #e2e8f0;border-radius:4px;text-align:center;margin-bottom:6px;">
        No chain data available
    </div>
    @endif

    {{-- FEE STATUS — Buyer's Booking --}}
    @if($toBook && $toFees->count() > 0)
    <div class="sec">Fee Status — Buyer's Booking</div>
    <table class="fee-tbl">
        <tr><th>Fee Type</th><th style="text-align:right;">Paid</th><th style="text-align:right;">Status</th></tr>
        @foreach($toFees as $fKey => $bill)
        <tr>
            <td class="fee-lbl">{{ $feeNames[$fKey] ?? ucfirst($fKey) }}</td>
            <td class="fee-paid">PKR {{ number_format($bill->paid_amount) }}</td>
            <td>
                <span class="badge {{ $feeStatusBadge($bill->status) }}">{{ ucfirst($bill->status) }}</span>
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    {{-- SIGNATURES --}}
    <div class="sec" style="margin-top:7px;">Signatures</div>
    <table class="sig-tbl">
        <tr>
            <td class="sig-td">
                <div class="sig-sp"></div>
                <div class="sig-line">
                    <div class="sig-name">{{ $fromC->name ?? '—' }}</div>
                    <div class="sig-role">Transferor</div>
                </div>
            </td>
            <td class="sig-td">
                <div class="sig-sp"></div>
                <div class="sig-line">
                    @if($transfer->transfer_type === 'internal')
                        <div class="sig-name">{{ $fromC->name ?? '—' }}</div>
                        <div class="sig-role">Owner</div>
                    @else
                        <div class="sig-name">{{ $toC->name ?? '—' }}</div>
                        <div class="sig-role">Transferee</div>
                    @endif
                </div>
            </td>
        </tr>
        @if($transfer->witness1_name)
        <tr>
            <td class="sig-td">
                <div class="sig-sp"></div>
                <div class="sig-line">
                    <div class="sig-name">{{ $transfer->witness1_name }}</div>
                    <div class="sig-role">Witness 1</div>
                </div>
            </td>
            <td class="sig-td">
                <div class="sig-sp"></div>
                <div class="sig-line">
                    <div class="sig-name">{{ $transfer->witness2_name ?? '—' }}</div>
                    <div class="sig-role">Witness 2</div>
                </div>
            </td>
        </tr>
        @endif
        <tr>
            <td class="sig-td" colspan="2" style="text-align:center;padding-top:6px;">
                <div class="stamp">
                    <div class="stamp-t">{{ substr($socName,0,6) }}</div>
                    <div class="stamp-m">DEED</div>
                    <div class="stamp-t">{{ date('Y') }}</div>
                </div>
                <div class="sig-line" style="width:110px;margin:0 auto;">
                    <div class="sig-name">{{ $socName }}</div>
                    <div class="sig-role">Authorized Officer</div>
                </div>
            </td>
        </tr>
    </table>

</td>
</tr>
</table>

{{-- ════ FOOTER ════ --}}
<div class="footer">
    <table>
        <tr>
            @if($qrCode ?? false)
            <td style="width:70px;text-align:center;vertical-align:middle;">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="65">
                <div style="font-size:6px;color:#94a3b8;margin-top:2px;text-transform:uppercase;letter-spacing:0.7px;">Scan to Verify</div>
            </td>
            @endif
            <td style="vertical-align:middle;padding:0 10px;text-align:center;">
                <div class="foot-mid">
                    <strong>{{ $transfer->deed_no }}</strong> — {{ $typeLabel }}<br>
                    Plot #{{ $plot->plot_number ?? '—' }}, Block {{ $plot->block ?? '—' }}, {{ $plot->size ?? '—' }} {{ $plot->unit ?? '' }} — {{ $socName }}<br>
                    @php $dPhones = array_filter([$socPhone, $socPhone2, $socPhone3]); @endphp
                    {{ $socAddress }}@if($dPhones) &nbsp;|&nbsp; {{ implode(' · ', $dPhones) }} @endif @if($socEmail) &nbsp;|&nbsp; {{ $socEmail }} @endif<br>
                    Generated: {{ now()->format('d M Y, h:i A') }}
                </div>
            </td>
            <td style="width:88px;vertical-align:middle;text-align:right;">
                <span style="font-size:7px;color:#94a3b8;">Audit Hash</span><br>
                <span style="font-size:7px;font-weight:800;color:#0f172a;">{{ strtoupper(substr(md5($transfer->id.$transfer->deed_no), 0, 16)) }}</span>
            </td>
        </tr>
    </table>
</div>

<div class="doc-trail">
    {{ $socName }} &copy; {{ date('Y') }} &nbsp;|&nbsp; Deed: {{ $transfer->deed_no }} &nbsp;|&nbsp; CONFIDENTIAL — OFFICIAL DOCUMENT &nbsp;|&nbsp; {{ date('d-M-Y H:i:s') }}
</div>

</div></div>
</body>
</html>
