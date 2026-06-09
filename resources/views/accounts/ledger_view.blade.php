@extends('layouts.index')

@section('content')

@php
    use App\Helpers\AppConfig;

    $isInstallmentBooking = strtolower($mainBooking->booking_type ?? '') === 'installment';
    $isTransferBooking    = $mainBooking->booking_type === 'Transfer';
    $downLeft             = max(0, ($downDue ?? 0) - ($downPaid ?? 0));

    $statusMap = [
        'active'      => ['bg'=>'#eff6ff','border'=>'#bfdbfe','color'=>'#1d4ed8','dot'=>'#3b82f6','label'=>'Active'],
        'completed'   => ['bg'=>'#f0fdf4','border'=>'#86efac','color'=>'#15803d','dot'=>'#16a34a','label'=>'Completed'],
        'transferred' => ['bg'=>'#fdf4ff','border'=>'#e9d5ff','color'=>'#7c3aed','dot'=>'#9333ea','label'=>'Transferred'],
        'cancelled'   => ['bg'=>'#fef2f2','border'=>'#fecaca','color'=>'#dc2626','dot'=>'#ef4444','label'=>'Cancelled'],
        'pending'     => ['bg'=>'#fffbeb','border'=>'#fde68a','color'=>'#92400e','dot'=>'#d97706','label'=>'Pending'],
        'on_hold'     => ['bg'=>'#f8fafc','border'=>'#e2e8f0','color'=>'#475569','dot'=>'#94a3b8','label'=>'On Hold'],
    ];
    $s = $statusMap[$mainBooking->status] ?? $statusMap['active'];

    $tfootTotal = $mainBooking->payments->where('status','paid')
                    ->whereIn('payment_category',['down_payment','quarterly_installment','installment','plot_balance','others'])
                    ->sum('amount_paid');
@endphp

<div class="ldg-wrap">

{{-- ═══ PAGE HEADER ═══════════════════════════════════════════ --}}
<div class="ldg-header">
    <div>
        <p class="ldg-header-title">
            Financial Ledger &nbsp;·&nbsp;
            <span style="color:#93c5fd;">{{ $mainBooking->customer->name }}</span>
        </p>
        <p class="ldg-header-sub">
            Booking: {{ $mainBooking->customer_booking_id }}
            &nbsp;·&nbsp; Plot #{{ $mainBooking->plot->plot_number }}
            — {{ $mainBooking->plot->block ?? '' }}
            &nbsp;·&nbsp; {{ $mainBooking->plot->size }} {{ $mainBooking->plot->unit }}
            &nbsp;·&nbsp;
            <span style="background:rgba(255,255,255,.12);padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;letter-spacing:.4px;">
                {{ ucfirst($mainBooking->booking_type ?? 'N/A') }} Booking
            </span>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap" style="position:relative;z-index:1;">
    <a href="{{ route('index.account') }}" class="btn-soft">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back
    </a>

    {{-- HOLD / UNHOLD button in header --}}
    @if(in_array($mainBooking->status, ['active','pending','pending_transfer']))
        @if($isOnHold)
        {{-- UNHOLD --}}
        <form method="POST" action="{{ route('booking.unhold', $mainBooking->id) }}"
              onsubmit="return confirm('Release hold? Payments will be accepted again.')"
              style="display:inline;">
            @csrf
            <button type="submit"
                style="display:inline-flex;align-items:center;gap:6px;background:#dcfce7;border:1.5px solid #86efac;color:#15803d;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:800;cursor:pointer;font-family:inherit;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z"/></svg>
                Release Hold
            </button>
        </form>
        @else
        {{-- HOLD --}}
        <button type="button"
            onclick="openLedgerHoldModal()"
            style="display:inline-flex;align-items:center;gap:6px;background:#fef3c7;border:1.5px solid #fbbf24;color:#92400e;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:800;cursor:pointer;font-family:inherit;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
            Hold Booking
        </button>
        @endif

        {{-- CANCEL --}}
        @can('booking_cancel')
        <button type="button"
            onclick="openCancelModal()"
            style="display:inline-flex;align-items:center;gap:6px;background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:800;cursor:pointer;font-family:inherit;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel Booking
        </button>
        @endcan
    @endif

    {{-- Cancellation Notice PDF — only for cancelled bookings --}}
    @if($mainBooking->status === 'cancelled')
    <a href="{{ route('booking.cancellation.notice', $mainBooking->id) }}"
       target="_blank"
       style="display:inline-flex;align-items:center;gap:6px;background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:800;text-decoration:none;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        Cancellation Notice PDF
    </a>
    @endif

    {{-- Change Installment Plan — only for active/pending installment bookings --}}
    @if($hasInstallmentPlan && in_array($mainBooking->status, ['active','pending']) && !$readOnly && !$isOnHold && !$pendingTransfer)
    @can('booking_plan_change')
    <button type="button"
        onclick="openLedgerPlanModal()"
        style="display:inline-flex;align-items:center;gap:6px;background:#fefce8;border:1.5px solid #fde68a;color:#92400e;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:800;cursor:pointer;font-family:inherit;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        Change Plan
    </button>
    @endcan
    @endif

    {{-- Lump Sum Settlement --}}
    @if($remaining > 0 && in_array($mainBooking->status, ['active','pending']) && !$readOnly && !$isOnHold && !$pendingTransfer)
    <button type="button"
        onclick="openLumpSumModal()"
        style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;border:1.5px solid #86efac;color:#15803d;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:800;cursor:pointer;font-family:inherit;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
        Pay Lump Sum
    </button>
    @endif

    {{-- Possession Letter — only when ready --}}
    @if($possessionReady)
    <a href="{{ route('booking.possession.letter', $mainBooking->id) }}"
       target="_blank"
       style="display:inline-flex;align-items:center;gap:6px;background:#1e3a8a;color:#fff;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:800;text-decoration:none;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
        Possession Letter
    </a>
    @elseif($possessionBlocked)
    <button type="button" disabled
        title="{{ implode(' · ', $possessionBlockedReasons) }}"
        style="display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;border:1.5px solid #e2e8f0;color:#94a3b8;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:700;cursor:not-allowed;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
        Possession Locked
    </button>
    @endif

    @if($readOnly && !$isOnHold)
        <div style="display:inline-flex;align-items:center;gap:8px;background:#f8fafc;border:1.5px solid #e2e8f0;padding:9px 16px;border-radius:10px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#94a3b8" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            <span style="font-size:12px;font-weight:700;color:#94a3b8;">Read Only</span>
        </div>
    @elseif($pendingTransfer && !$isOnHold)
        <button class="btn-navy" disabled style="opacity:.5;cursor:not-allowed;">Payments Locked</button>
    @elseif(!$isOnHold)
        <button class="btn-navy" data-bs-toggle="modal" data-bs-target="#paymentModal">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Log Payment
        </button>
    @endif
</div>
</div>

{{-- ═══ FLASH MESSAGES ═════════════════════════════════════════ --}}
@if(session('success'))
<div class="alert-flash alert-flash-success">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:18px;">&times;</button>
</div>
@endif
@if(session('error'))
<div class="alert-flash alert-flash-error">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
    {{ session('error') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:18px;">&times;</button>
</div>
@endif
@if($errors->any())
<div class="alert-flash alert-flash-error">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
    <div><strong>Fix the following:</strong><ul style="margin:5px 0 0 14px;padding:0;font-size:12px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:18px;">&times;</button>
</div>
<script>document.addEventListener('DOMContentLoaded',function(){new bootstrap.Modal(document.getElementById('paymentModal')).show();});</script>
@endif
@if($isOnHold)
<div style="background:#fef3c7;border:2px solid #fbbf24;border-radius:14px;padding:16px 22px;margin-bottom:18px;display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
    <div style="width:40px;height:40px;background:#f59e0b;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:14px;font-weight:800;color:#92400e;">Booking ON HOLD — Payments Blocked</div>
        @if($activeHold?->remarks)
        <div style="font-size:12px;color:#a16207;margin-top:4px;">
            <strong>Reason:</strong> {{ $activeHold->remarks }}
        </div>
        @endif
        @if($activeHold?->createdBy)
        <div style="font-size:11px;color:#a16207;margin-top:2px;">
            Placed by <strong>{{ $activeHold->createdBy->name ?? 'Staff' }}</strong>
            on {{ \Carbon\Carbon::parse($activeHold->created_at)->format('d M Y H:i') }}
        </div>
        @endif
    </div>
    @if(in_array($mainBooking->status, ['active','pending','pending_transfer']))
    <form method="POST" action="{{ route('booking.unhold', $mainBooking->id) }}"
          onsubmit="return confirm('Release hold? Payments will be accepted again.')">
        @csrf
        <button type="submit"
            style="background:#15803d;color:#fff;padding:10px 20px;border-radius:10px;font-size:12px;font-weight:800;border:none;cursor:pointer;font-family:inherit;white-space:nowrap;">
            ▶ Release Hold
        </button>
    </form>
    @endif
</div>
@endif
{{-- ═══ CANCELLED BANNER + FINANCIAL SUMMARY ════════════════════ --}}
@if($mainBooking->status === 'cancelled')
@php
    $cancelPriceCats = ['down_payment','installment','quarterly_installment','plot_balance','others'];
    $cancelTotalPaid = $mainBooking->payments
        ->where('status','paid')
        ->whereIn('payment_category', $cancelPriceCats)
        ->sum('amount_paid');
    $cancelRefund    = (float)($mainBooking->cancellation_refund ?? 0);
    $cancelRetained  = max(0, $cancelTotalPaid - $cancelRefund);
@endphp

{{-- Red top banner --}}
<div style="background:#fef2f2;border:2px solid #fecaca;border-radius:14px;padding:16px 22px;margin-bottom:12px;display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
    <div style="width:40px;height:40px;background:#dc2626;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:14px;font-weight:800;color:#991b1b;">Booking CANCELLED — Read Only</div>
        @if($mainBooking->cancellation_reason)
        <div style="font-size:12px;color:#b91c1c;margin-top:4px;">
            <strong>Reason:</strong> {{ $mainBooking->cancellation_reason }}
        </div>
        @endif
        @if($mainBooking->cancelled_at)
        <div style="font-size:11px;color:#b91c1c;margin-top:2px;">
            Cancelled on <strong>{{ \Carbon\Carbon::parse($mainBooking->cancelled_at)->format('d M Y H:i') }}</strong>
            @if($mainBooking->cancelledBy)
                by <strong>{{ $mainBooking->cancelledBy->name ?? 'Staff' }}</strong>
            @endif
        </div>
        @endif
    </div>
    <a href="{{ route('booking.cancellation.notice', $mainBooking->id) }}" target="_blank"
       style="align-self:center;display:inline-flex;align-items:center;gap:6px;background:#dc2626;color:#fff;padding:8px 14px;border-radius:9px;font-size:11px;font-weight:800;text-decoration:none;white-space:nowrap;flex-shrink:0;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        Cancellation PDF
    </a>
</div>

{{-- Financial summary for cancellation --}}
<div style="background:#fff;border:1.5px solid #fecaca;border-radius:14px;padding:18px 22px;margin-bottom:18px;">
    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#dc2626;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
        Cancellation Financial Summary
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;">
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px 16px;">
            <div style="font-size:10px;font-weight:700;color:#9a3412;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">Agreed Plot Price</div>
            <div style="font-size:16px;font-weight:800;color:#7c2d12;">PKR {{ number_format($mainBooking->total_price) }}</div>
        </div>
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 16px;">
            <div style="font-size:10px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">Total Collected</div>
            <div style="font-size:16px;font-weight:800;color:#dc2626;">PKR {{ number_format($cancelTotalPaid) }}</div>
            @if($cancelTotalPaid == 0)
            <div style="font-size:10px;color:#94a3b8;margin-top:3px;">No payments recorded</div>
            @endif
        </div>
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 16px;">
            <div style="font-size:10px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">Agreed Refund</div>
            <div style="font-size:16px;font-weight:800;color:#15803d;">PKR {{ number_format($cancelRefund) }}</div>
            @if($cancelRefund == 0)
            <div style="font-size:10px;color:#94a3b8;margin-top:3px;">No refund agreed</div>
            @endif
        </div>
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;">
            <div style="font-size:10px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">Net Retained</div>
            <div style="font-size:16px;font-weight:800;color:#0f172a;">PKR {{ number_format($cancelRetained) }}</div>
            <div style="font-size:10px;color:#94a3b8;margin-top:3px;">Collected minus refund</div>
        </div>
    </div>
</div>
@endif
{{-- ═══ STATUS BAR ═════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:18px;">
    <div style="display:inline-flex;align-items:center;gap:7px;background:{{ $s['bg'] }};border:1.5px solid {{ $s['border'] }};padding:7px 14px;border-radius:30px;">
        <span style="width:8px;height:8px;border-radius:50%;background:{{ $s['dot'] }};display:inline-block;"></span>
        <span style="font-size:11px;font-weight:800;color:{{ $s['color'] }};text-transform:uppercase;letter-spacing:.5px;">Booking: {{ $s['label'] }}</span>
    </div>
    @if($hasCompletedTransfer)
    <div style="display:inline-flex;align-items:center;gap:7px;background:#fdf4ff;border:1.5px solid #e9d5ff;padding:7px 14px;border-radius:30px;">
        <span style="font-size:11px;font-weight:800;color:#7c3aed;text-transform:uppercase;letter-spacing:.5px;">Transfer: Completed</span>
    </div>
    @elseif($pendingTransfer)
    <div style="display:inline-flex;align-items:center;gap:7px;background:#fffbeb;border:1.5px solid #fde68a;padding:7px 14px;border-radius:30px;">
        <span style="font-size:11px;font-weight:800;color:#92400e;text-transform:uppercase;letter-spacing:.5px;">Transfer: Pending Fee</span>
    </div>
    @endif
    @if($remaining <= 0)
    <div style="display:inline-flex;align-items:center;gap:7px;background:#f0fdf4;border:1.5px solid #86efac;padding:7px 14px;border-radius:30px;">
        <span style="font-size:11px;font-weight:800;color:#15803d;text-transform:uppercase;letter-spacing:.5px;">Fully Paid ✓</span>
    </div>
    @else
    <div style="display:inline-flex;align-items:center;gap:7px;background:#fef2f2;border:1.5px solid #fecaca;padding:7px 14px;border-radius:30px;">
        <span style="font-size:11px;font-weight:800;color:#dc2626;">Remaining: PKR {{ number_format($remaining) }}</span>
    </div>
    @endif
    @php
        $ldgTotalSavings = ($plotDiscount ?? 0) + ($paymentDiscountCredits ?? 0);
    @endphp
    @if($ldgTotalSavings > 0)
    <div style="display:inline-flex;align-items:center;gap:7px;background:#fffbeb;border:1.5px solid #fde68a;padding:7px 14px;border-radius:30px;">
        <span style="font-size:11px;font-weight:800;color:#92400e;">★ Savings: PKR {{ number_format($ldgTotalSavings) }}</span>
    </div>
    @endif
</div>

{{-- ═══ PENDING TRANSFER BANNER ════════════════════════════════ --}}
@if($pendingTransfer)
<div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:14px;padding:16px 22px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
    <div style="flex:1;">
        <div style="font-size:13px;font-weight:800;color:#92400e;">Payments Locked — Transfer Fee Pending</div>
        <div style="font-size:12px;color:#a16207;margin-top:3px;">
            Deed <strong style="font-family:monospace;">{{ $pendingTransfer->deed_no }}</strong> has an unpaid fee of <strong>PKR {{ number_format($pendingTransfer->transfer_fee) }}</strong>.
        </div>
    </div>
    <a href="{{ route('transfers.pay-fee', $pendingTransfer->id) }}"
       style="background:#ca8a04;color:#fff;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:800;text-decoration:none;white-space:nowrap;">
        Pay Transfer Fee
    </a>
</div>
@endif


@if($registryFeeRequired || $developmentFeeRequired || $securityFeeRequired)
@php $anyFeePending = !$registryFeeCleared || !$developmentFeeCleared || !$securityFeeCleared; @endphp
<div style="background:{{ $anyFeePending ? '#fef2f2' : '#f0fdf4' }};border:1.5px solid {{ $anyFeePending ? '#fecaca' : '#86efac' }};border-radius:14px;padding:14px 20px;margin-bottom:18px;">
    <div style="font-size:13px;font-weight:800;color:{{ $anyFeePending ? '#dc2626' : '#15803d' }};margin-bottom:12px;display:flex;align-items:center;gap:8px;">
        @if($anyFeePending)
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
        Fee Status — Pending Fees Outstanding
        @else
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Fee Status — All Fees Cleared
        @endif
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">

        {{-- Registry Fee --}}
        @if($registryFeeRequired)
        @php $regAmt = $registryFeeBill ? (float)$registryFeeBill->amount : 0; $regPaid = $registryFeeBill ? (float)$registryFeeBill->paid_amount : 0; $regRem = max(0, $regAmt - $regPaid); @endphp
        <div style="background:{{ $registryFeeCleared ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $registryFeeCleared ? '#86efac' : '#fca5a5' }};border-radius:9px;padding:10px 14px;min-width:190px;">
            <div style="font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;display:flex;align-items:center;gap:5px;">
                <i class="fas fa-stamp" style="color:#b45309;"></i> Registry Fee
            </div>
            <div style="font-size:13px;font-weight:800;color:{{ $registryFeeCleared ? '#15803d' : '#dc2626' }};margin-bottom:4px;">
                @if($registryFeeCleared) ✓ Cleared @else ✗ Pending @endif
            </div>
            @if($regAmt > 0)
            <div style="font-size:11px;color:#475569;">
                Bill: <strong>PKR {{ number_format($regAmt) }}</strong>
            </div>
            <div style="font-size:11px;color:#475569;">
                Paid: <strong style="color:{{ $regPaid > 0 ? '#15803d' : '#94a3b8' }};">PKR {{ number_format($regPaid) }}</strong>
            </div>
            @if($regRem > 0)
            <div style="font-size:11px;color:#dc2626;font-weight:700;margin-top:2px;">
                Due: PKR {{ number_format($regRem) }}
            </div>
            @endif
            @elseif(!$registryFeeCleared)
            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">Amount not set yet</div>
            @endif
            @if(!$registryFeeCleared)
            <a href="{{ route('fee.management') }}" style="font-size:10px;font-weight:700;color:#1d4ed8;text-decoration:none;display:inline-block;margin-top:5px;">Pay Now →</a>
            @endif
        </div>
        @endif

        {{-- Development Fee --}}
        @if($developmentFeeRequired)
        @php $devAmt = $developmentFeeBill ? (float)$developmentFeeBill->amount : 0; $devPaid = $developmentFeeBill ? (float)$developmentFeeBill->paid_amount : 0; $devRem = max(0, $devAmt - $devPaid); @endphp
        <div style="background:{{ $developmentFeeCleared ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $developmentFeeCleared ? '#86efac' : '#fca5a5' }};border-radius:9px;padding:10px 14px;min-width:190px;">
            <div style="font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;display:flex;align-items:center;gap:5px;">
                <i class="fas fa-hard-hat" style="color:#1d4ed8;"></i> Development Fee
            </div>
            <div style="font-size:13px;font-weight:800;color:{{ $developmentFeeCleared ? '#15803d' : '#dc2626' }};margin-bottom:4px;">
                @if($developmentFeeCleared) ✓ Cleared @else ✗ Pending @endif
            </div>
            @if($devAmt > 0)
            <div style="font-size:11px;color:#475569;">
                Bill: <strong>PKR {{ number_format($devAmt) }}</strong>
            </div>
            <div style="font-size:11px;color:#475569;">
                Paid: <strong style="color:{{ $devPaid > 0 ? '#15803d' : '#94a3b8' }};">PKR {{ number_format($devPaid) }}</strong>
            </div>
            @if($devRem > 0)
            <div style="font-size:11px;color:#dc2626;font-weight:700;margin-top:2px;">
                Due: PKR {{ number_format($devRem) }}
            </div>
            @endif
            @elseif(!$developmentFeeCleared)
            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">Amount not set yet</div>
            @endif
            @if(!$developmentFeeCleared)
            <a href="{{ route('fee.management') }}" style="font-size:10px;font-weight:700;color:#1d4ed8;text-decoration:none;display:inline-block;margin-top:5px;">Pay Now →</a>
            @endif
        </div>
        @endif

        {{-- Security Fee --}}
        @if($securityFeeRequired)
        <div style="background:{{ $securityFeeCleared ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $securityFeeCleared ? '#86efac' : '#fca5a5' }};border-radius:9px;padding:10px 14px;min-width:190px;">
            <div style="font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;display:flex;align-items:center;gap:5px;">
                <i class="fas fa-shield-alt" style="color:#7c3aed;"></i> Security Fee
            </div>
            @if($secMonthsTotal !== null)
            <div style="font-size:13px;font-weight:800;color:{{ $securityFeeCleared ? '#15803d' : '#dc2626' }};margin-bottom:4px;">
                @if($securityFeeCleared) ✓ Up to Date @else ✗ Overdue @endif
            </div>
            <div style="font-size:11px;color:#475569;">
                Rate: <strong>PKR {{ number_format($secMonthlyRate) }}/mo</strong>
            </div>
            <div style="font-size:11px;color:#475569;">
                Months: <strong style="color:{{ $secMonthsPaid >= $secMonthsTotal ? '#15803d' : '#dc2626' }};">{{ $secMonthsPaid }}/{{ $secMonthsTotal }}</strong> paid
            </div>
            @if($secMonthsUnpaid > 0)
            <div style="font-size:11px;color:#dc2626;font-weight:700;margin-top:2px;">
                {{ $secMonthsUnpaid }} month{{ $secMonthsUnpaid > 1 ? 's' : '' }} due — PKR {{ number_format($secOutstanding) }}
            </div>
            @endif
            <div style="font-size:11px;color:#475569;margin-top:2px;">
                Total paid: <strong>PKR {{ number_format($secTotalPaid) }}</strong>
            </div>
            @else
            <div style="font-size:13px;font-weight:800;color:{{ $securityFeeCleared ? '#15803d' : '#dc2626' }};margin-bottom:4px;">
                @if($securityFeeCleared) ✓ Cleared @else ✗ Pending @endif
            </div>
            <div style="font-size:11px;color:#94a3b8;">Monthly rate not set on plot</div>
            @endif
            @if(!$securityFeeCleared)
            <a href="{{ route('fee.management') }}" style="font-size:10px;font-weight:700;color:#1d4ed8;text-decoration:none;display:inline-block;margin-top:5px;">Pay Now →</a>
            @endif
        </div>
        @endif

    </div>
</div>
@elseif($possessionReady)
<div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:11px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#15803d" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span style="font-size:12px;font-weight:800;color:#15803d;">All clear — Plot paid, all fees settled. Possession letter available.</span>
</div>
@endif


{{-- ═══ OWNERSHIP HISTORY ═══════════════════════════════════════ --}}
@if($transferHistory->isNotEmpty())
<div style="background:#fff;border:1.5px solid #e4e9f2;border-radius:14px;margin-bottom:20px;overflow:hidden;">
    <div style="padding:13px 20px;background:#fafbfc;border-bottom:1px solid #f1f5f9;">
        <span style="font-size:12px;font-weight:800;color:#0f172a;">Ownership History</span>
        <span style="font-size:10px;color:#94a3b8;margin-left:8px;">{{ $transferHistory->count() }} transfer{{ $transferHistory->count()>1?'s':'' }}</span>
    </div>
    <div style="padding:16px 20px;display:flex;align-items:center;flex-wrap:wrap;">
        @foreach($transferHistory as $th)
        <div style="text-align:center;padding:0 10px;">
            <div style="width:38px;height:38px;border-radius:10px;background:#fef2f2;color:#dc2626;font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 5px;">
                {{ strtoupper(substr($th->fromCustomer->name??'?',0,1)) }}
            </div>
            <div style="font-size:11px;font-weight:700;color:#dc2626;">{{ $th->fromCustomer->name??'—' }}</div>
            <div style="font-size:9px;color:#94a3b8;margin-top:2px;">{{ \Carbon\Carbon::parse($th->transfer_date)->format('d M Y') }}</div>
        </div>
        <div style="text-align:center;padding:0 6px;">
            <div style="font-size:9px;color:#1e3a8a;font-family:monospace;font-weight:700;">{{ $th->deed_no }}</div>
            <div style="font-size:20px;color:#94a3b8;line-height:1;">→</div>
        </div>
        @if($loop->last)
        <div style="text-align:center;padding:0 10px;">
            <div style="width:38px;height:38px;border-radius:10px;background:#f0fdf4;color:#16a34a;font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 5px;">
                {{ strtoupper(substr($th->toCustomer->name??'?',0,1)) }}
            </div>
            <div style="font-size:11px;font-weight:700;color:#16a34a;">{{ $th->toCustomer->name??'—' }}</div>
            <div style="font-size:9px;color:#94a3b8;margin-top:2px;">Current Owner</div>
        </div>
        @endif
        @endforeach
        <div style="margin-left:auto;display:flex;flex-direction:column;gap:5px;">
            @foreach($transferHistory as $th)
            <a href="{{ route('transfers.deed', $th->id) }}" target="_blank"
               style="font-size:10px;font-weight:700;color:#0369a1;background:#e0f2fe;padding:3px 10px;border-radius:8px;border:1px solid #7dd3fc;text-decoration:none;">
                {{ $th->deed_no }} ↗
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ═══ STAT CARDS ROW ═════════════════════════════════════════ --}}
@php
    $statCards = [
        ['label'=>'Total Transactions','val'=>$mainBooking->payments->count(),'fmt'=>false,'color'=>'#1e3a8a','bg'=>'#eff6ff','icon'=>'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z'],
        ['label'=>'Total Collected','val'=>$totalCollectedReal,'fmt'=>true,'color'=>'#16a34a','bg'=>'#f0fdf4','icon'=>'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z'],
        ['label'=>'Remaining Balance','val'=>$remaining,'fmt'=>true,'color'=>$remaining>0?'#dc2626':'#16a34a','bg'=>$remaining>0?'#fef2f2':'#f0fdf4','icon'=>'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'Collection %','val'=>$prog.'%','fmt'=>false,'color'=>'#7c3aed','bg'=>'#fdf4ff','icon'=>'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
    ];
@endphp
<div class="row g-3" style="margin-bottom:20px;">
    @foreach($statCards as $sc)
    <div class="col-sm-6 col-lg-3">
        <div class="ldg-card mb-0" style="padding:20px 22px;display:flex;align-items:center;gap:16px;border-radius:12px;">
            <div style="width:48px;height:48px;border-radius:12px;background:{{ $sc['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="{{ $sc['color'] }}" style="width:24px;height:24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sc['icon'] }}"/>
                </svg>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;">{{ $sc['label'] }}</div>
                <div style="font-size:19px;font-weight:800;color:{{ $sc['color'] }};margin-top:3px;">
                    {{ $sc['fmt'] ? 'PKR '.number_format($sc['val']) : $sc['val'] }}
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══ MAIN GRID ═══════════════════════════════════════════════ --}}
<div class="row g-4">
    <div class="col-lg-5">

        {{-- Customer Profile --}}
        <div class="ldg-card">
            <div class="ldg-card-head"><p class="ldg-card-title">Customer Profile</p></div>
            <div class="ldg-card-body">
                @if($mainBooking->customer->customer_pic)
                    <img src="{{ asset($mainBooking->customer->customer_pic) }}" class="profile-avatar" alt="Photo">
                @else
                    <div class="profile-avatar d-flex align-items-center justify-content-center"
                         style="background:#eff6ff;font-size:26px;font-weight:800;color:#1d4ed8;margin:0 auto 16px;width:80px;height:80px;border-radius:50%;">
                        {{ strtoupper(substr($mainBooking->customer->name,0,1)) }}
                    </div>
                @endif
                <div class="profile-row"><div class="profile-label">Full Name</div><div class="profile-value">{{ $mainBooking->customer->name }}</div></div>
                <div class="profile-row"><div class="profile-label">CNIC</div><div class="profile-value" style="font-family:monospace;">{{ $mainBooking->customer->cnic }}</div></div>
                <div class="profile-row"><div class="profile-label">Phone</div><div class="profile-value">{{ $mainBooking->customer->phone??'—' }}</div></div>
                <div class="profile-row">
                    <div class="profile-label">Booking Type</div>
                    <div class="profile-value">
                        <span style="background:{{ $isInstallmentBooking?'#f0fdf4':'#eff6ff' }};color:{{ $isInstallmentBooking?'#16a34a':'#1d4ed8' }};padding:2px 10px;border-radius:20px;font-size:11px;">
                            {{ ucfirst($mainBooking->booking_type??'—') }}
                        </span>
                    </div>
                </div>
                <div class="profile-row"><div class="profile-label">Booking Date</div><div class="profile-value">{{ \Carbon\Carbon::parse($mainBooking->booking_date)->format('d M Y') }}</div></div>
            </div>
        </div>

    </div>

    {{-- ══ RIGHT COLUMN — Payment Summary ══════════════════════ --}}
    <div class="col-lg-7">
        {{-- Payment Summary --}}
        <div class="ldg-card">
            <div class="ldg-card-head"><p class="ldg-card-title">Payment Summary</p></div>
            <div class="ldg-card-body">
                @if($downDue > 0)
                <div class="bk-row">
                    <span class="bk-label"><span class="bk-dot" style="background:#1d4ed8;"></span>Down Payment</span>
                    <span class="bk-due">PKR {{ number_format($downDue) }}</span>
                    <span class="bk-status {{ $downPaid>=$downDue?'paid':($downPaid>0?'partial':'unpaid') }}">
                        {{ $downPaid>=$downDue?'✓ Cleared':($downPaid>0?'PKR '.number_format($downPaid):'Unpaid') }}
                    </span>
                </div>
                @endif
                @if($hasQuarterlyPlan)
                @php $qtrTotal = $totalQuarterlyCount * $quarterlyAmount; @endphp
                <div class="bk-row">
                    <span class="bk-label"><span class="bk-dot" style="background:#b45309;"></span>Quarterly ({{ $paidQuarterlyCount }}/{{ $totalQuarterlyCount }})</span>
                    <span class="bk-due">PKR {{ number_format($qtrTotal) }}</span>
                    <span class="bk-status {{ $paidQuarterlyCount>=$totalQuarterlyCount?'paid':($paidQuarterlyCount>0?'partial':'unpaid') }}">
                        {{ $paidQuarterlyCount>=$totalQuarterlyCount ? '✓ Done' : 'PKR '.number_format($qtrPaid).' paid' }}
                    </span>
                </div>
                @endif
                @if($hasInstallmentPlan)
                @php $mthTotal = $totalInstallmentCount * $monthlyInstallment; @endphp
                <div class="bk-row">
                    <span class="bk-label"><span class="bk-dot" style="background:#16a34a;"></span>Monthly ({{ $paidInstallmentCount }}/{{ $totalInstallmentCount }})</span>
                    <span class="bk-due">PKR {{ number_format($mthTotal) }}</span>
                    <span class="bk-status {{ $paidInstallmentCount>=$totalInstallmentCount?'paid':($paidInstallmentCount>0?'partial':'unpaid') }}">
                        {{ $paidInstallmentCount>=$totalInstallmentCount ? '✓ Done' : 'PKR '.number_format($installPaid).' paid' }}
                    </span>
                </div>
                @endif
                <div class="plan-box">
                    {{-- ── Price Breakdown ───────────────────────────────── --}}
                    @if(($plotDiscount ?? 0) > 0)
                    <div class="plan-row" style="opacity:.7;">
                        <span class="plan-key" style="font-size:10px;">Base Price</span>
                        <span class="plan-val" style="font-size:11px;text-decoration:line-through;opacity:.6;">PKR {{ number_format($trueTotal + $plotDiscount) }}</span>
                    </div>
                    <div class="plan-row">
                        <span class="plan-key" style="color:#4ade80;font-size:10px;">Offer Discount</span>
                        <span class="plan-val" style="color:#4ade80;font-size:11px;">- PKR {{ number_format($plotDiscount) }}</span>
                    </div>
                    @endif
                    <div class="plan-row" style="{{ ($plotDiscount??0)>0 ? 'border-top:1px solid rgba(255,255,255,.1);padding-top:6px;margin-top:2px;' : '' }}">
                        <span class="plan-key">{{ ($plotDiscount??0)>0 ? 'Your Price' : 'Total Price' }}</span>
                        <span class="plan-val">PKR {{ number_format($trueTotal) }}</span>
                    </div>
                    @if($downDue > 0)
                    <div class="plan-row"><span class="plan-key" style="font-size:10px;color:rgba(255,255,255,.5);">↳ Down Payment</span><span class="plan-val" style="font-size:11px;color:#93c5fd;">PKR {{ number_format($downDue) }}</span></div>
                    @endif
                    @if($hasQuarterlyPlan)
                    <div class="plan-row"><span class="plan-key" style="font-size:10px;color:rgba(255,255,255,.5);">↳ Quarterly Total</span><span class="plan-val" style="font-size:11px;color:#fcd34d;">PKR {{ number_format($totalQuarterlyCount * $quarterlyAmount) }}</span></div>
                    @endif
                    @if($hasInstallmentPlan)
                    <div class="plan-row"><span class="plan-key" style="font-size:10px;color:rgba(255,255,255,.5);">↳ Monthly Total</span><span class="plan-val" style="font-size:11px;color:#86efac;">PKR {{ number_format($totalInstallmentCount * $monthlyInstallment) }}</span></div>
                    @endif
                    {{-- ── Payments Summary ──────────────────────────────── --}}
                    <div class="plan-row" style="border-top:1px solid rgba(255,255,255,.1);padding-top:8px;margin-top:4px;">
                        <span class="plan-key">Cash Paid</span><span class="plan-val green">PKR {{ number_format($totalPaidReal) }}</span>
                    </div>
                    @if(($paymentDiscountCredits ?? 0) > 0)
                    <div class="plan-row" style="background:rgba(251,146,60,.1);border-radius:6px;padding:4px 6px;margin:2px 0;">
                        <span class="plan-key" style="color:#fb923c;font-size:10px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:10px;height:10px;margin-right:3px;vertical-align:middle;"><path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.75 11.25a.75.75 0 00-.75.75v.75a.75.75 0 001.5 0v-.75a.75.75 0 00-.75-.75zM12.25 11.25a.75.75 0 00-.75.75v.75a.75.75 0 001.5 0v-.75a.75.75 0 00-.75-.75zM8 11.25a.75.75 0 00-.75.75v.75a.75.75 0 001.5 0v-.75A.75.75 0 008 11.25z"/></svg>
                            Full-Payment Discount (Waived)
                        </span>
                        <span class="plan-val" style="color:#fb923c;">− PKR {{ number_format($paymentDiscountCredits) }}</span>
                    </div>
                    <div class="plan-row" style="opacity:.7;">
                        <span class="plan-key" style="font-size:10px;color:rgba(255,255,255,.5);">Total Credits (Cash + Disc.)</span>
                        <span class="plan-val" style="font-size:11px;color:#93c5fd;">PKR {{ number_format($totalPaidReal + $paymentDiscountCredits) }}</span>
                    </div>
                    @endif
                    <div class="plan-row" style="margin-top:4px;">
                        <span class="plan-key">Remaining</span>
                        <span class="plan-val {{ $remaining>0?'yellow':'green' }}">PKR {{ number_format($remaining) }}{{ $remaining<=0?' ✓':'' }}</span>
                    </div>
                    <div style="margin-top:12px;">
                        <div style="display:flex;justify-content:space-between;font-size:10px;color:rgba(255,255,255,.4);margin-bottom:5px;"><span>Collection Progress</span><span>{{ $prog }}%</span></div>
                        <div style="height:6px;background:rgba(255,255,255,.1);border-radius:10px;overflow:hidden;">
                            <div style="height:100%;width:{{ $prog }}%;background:linear-gradient(90deg,#3b82f6,#4ade80);border-radius:10px;"></div>
                        </div>
                    </div>
                    {{-- ── Discount Badge ─────────────────────────────────── --}}
                    @php $ldgTotalSav = ($plotDiscount ?? 0) + ($paymentDiscountCredits ?? 0); @endphp
                    @if($ldgTotalSav > 0)
                    <div style="margin-top:10px;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.3);border-radius:8px;padding:8px 10px;">
                        <div style="font-size:9px;font-weight:800;color:#fbbf24;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">★ Total Savings on This Booking</div>
                        @if(($plotDiscount ?? 0) > 0)
                        <div style="display:flex;justify-content:space-between;font-size:10px;margin-bottom:3px;">
                            <span style="color:rgba(255,255,255,.6);">Plot Discount (at booking)</span>
                            <span style="color:#fbbf24;font-weight:700;">PKR {{ number_format($plotDiscount) }}</span>
                        </div>
                        @endif
                        @if(($paymentDiscountCredits ?? 0) > 0)
                        <div style="display:flex;justify-content:space-between;font-size:10px;margin-bottom:3px;">
                            <span style="color:rgba(255,255,255,.6);">Full-Payment Discount</span>
                            <span style="color:#fb923c;font-weight:700;">PKR {{ number_format($paymentDiscountCredits) }}</span>
                        </div>
                        @endif
                        <div style="border-top:1px solid rgba(251,191,36,.3);margin-top:4px;padding-top:4px;display:flex;justify-content:space-between;font-size:11px;font-weight:800;">
                            <span style="color:#fbbf24;">Total Saved</span>
                            <span style="color:#fbbf24;">PKR {{ number_format($ldgTotalSav) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
                @if($hasQuarterlyPlan)
                @php
                    $qtrProgress = $totalQuarterlyCount > 0 ? round(($paidQuarterlyCount/$totalQuarterlyCount)*100) : 0;
                    $qtrColor    = $qtrProgress >= 100 ? '#10b981' : '#b45309';
                @endphp
                <div class="tracker-box" style="margin-top:12px;">
                    <div class="tracker-title" style="color:#b45309;">Quarterly Plan Tracker</div>
                    <div class="tracker-row"><span class="tracker-key">Total Quarters</span><span class="tracker-val">{{ $totalQuarterlyCount }} payments</span></div>
                    <div class="tracker-row"><span class="tracker-key">Amount / Quarter</span><span class="tracker-val" style="color:#b45309;">PKR {{ number_format($quarterlyAmount) }}</span></div>
                    <div class="tracker-row"><span class="tracker-key">Paid</span>
                        <span class="tracker-val {{ $paidQuarterlyCount>0?'green':'' }}" style="{{ $paidQuarterlyCount===0?'color:#94a3b8;':'' }}">
                            {{ $paidQuarterlyCount>0 ? $paidQuarterlyCount.' quarters' : 'None yet' }}
                        </span>
                    </div>
                    @if($remainingQuarterlyCount > 0)
                    <div class="tracker-row" style="margin-bottom:0;"><span class="tracker-key">Remaining</span><span class="tracker-val red">{{ $remainingQuarterlyCount }} quarters</span></div>
                    @endif
                    <div class="prog-bar" style="height:8px;margin-top:10px;background:rgba(180,83,9,.15);border-radius:10px;overflow:hidden;">
                        <div class="prog-fill" style="height:100%;width:{{ $qtrProgress }}%;background:{{ $qtrColor }};border-radius:10px;"></div>
                    </div>
                    <div style="text-align:right;font-size:10px;color:#94a3b8;margin-top:5px;">
                        @if($paidQuarterlyCount >= $totalQuarterlyCount) <span style="color:#10b981;font-weight:bold;">✓ Quarterly Done</span>
                        @elseif($paidQuarterlyCount===0) <span style="color:#f59e0b;font-weight:700;">No quarterly payments yet</span>
                        @else {{ $qtrProgress }}% completed
                        @endif
                    </div>
                </div>
                @endif
                @if($hasInstallmentPlan)
                @php
                    $ipDisplay     = $totalInstallmentCount > 0 ? round(($paidInstallmentCount / $totalInstallmentCount) * 100) : 0;
                    $progressColor = $paidInstallmentCount >= $totalInstallmentCount ? '#10b981' : '#1d4ed8';
                @endphp
                <div class="tracker-box" style="margin-top:12px;">
                    <div class="tracker-title">Monthly Plan Tracker</div>
                    <div class="tracker-row"><span class="tracker-key">Total Plan</span><span class="tracker-val">{{ $totalInstallmentCount }} months</span></div>
                    <div class="tracker-row"><span class="tracker-key">Monthly Amount</span><span class="tracker-val" style="color:#1d4ed8;">PKR {{ number_format($monthlyInstallment) }}</span></div>
                    <div class="tracker-row"><span class="tracker-key">Paid</span>
                        <span class="tracker-val {{ $paidInstallmentCount>0?'green':'' }}" style="{{ $paidInstallmentCount===0?'color:#94a3b8;':'' }}">
                            {{ $paidInstallmentCount>0 ? $paidInstallmentCount.' months' : 'None yet' }}
                        </span>
                    </div>
                    @if($remainingInstallmentCount > 0)
                    <div class="tracker-row" style="margin-bottom:0;"><span class="tracker-key">Remaining</span><span class="tracker-val red">{{ $remainingInstallmentCount }} months</span></div>
                    @endif
                    <div class="prog-bar" style="height:8px;margin-top:10px;">
                        <div class="prog-fill" style="width:{{ $ipDisplay }}%;background:{{ $progressColor }};"></div>
                    </div>
                    <div style="text-align:right;font-size:10px;color:#94a3b8;margin-top:5px;">
                        @if($paidInstallmentCount >= $totalInstallmentCount) <span style="color:#10b981;font-weight:bold;">✓ Monthly Done</span>
                        @elseif($paidInstallmentCount===0) <span style="color:#f59e0b;font-weight:700;">No installments paid yet</span>
                        @else {{ $ipDisplay }}% completed
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══ SETTLEMENT BREAKDOWN ════════════════════════════════════ --}}
@php
    // Detect lump-sum / mixed payments: any plot_balance payment, or any payment with discount_amount > 0
    $hasLumpSum     = $mainBooking->payments->where('payment_category','plot_balance')->where('status','paid')->isNotEmpty();
    $hasPayDiscount = $paymentDiscountCredits > 0;
    $showBreakdown  = $hasLumpSum || $hasPayDiscount || (($plotDiscount ?? 0) > 0);

    // Per-category subtotals (paid only, real cash)
    $discSent    = 'Settlement discount — waived amount (not collected).';
    $bdDown      = $mainBooking->payments->where('status','paid')->where('payment_category','down_payment')
                    ->filter(fn($p) => ($p->remarks ?? '') !== $discSent)->sum('amount_paid');
    $bdMonthly   = $mainBooking->payments->where('status','paid')->where('payment_category','installment')
                    ->filter(fn($p) => ($p->remarks ?? '') !== $discSent)->sum('amount_paid');
    $bdQtr       = $mainBooking->payments->where('status','paid')->where('payment_category','quarterly_installment')
                    ->filter(fn($p) => ($p->remarks ?? '') !== $discSent)->sum('amount_paid');
    $bdBalance   = $mainBooking->payments->where('status','paid')->where('payment_category','plot_balance')
                    ->filter(fn($p) => ($p->remarks ?? '') !== $discSent)->sum('amount_paid');
    $bdOthers    = $mainBooking->payments->where('status','paid')->where('payment_category','others')
                    ->filter(fn($p) => ($p->remarks ?? '') !== $discSent)->sum('amount_paid');
    $bdFine      = $mainBooking->payments->where('status','paid')->where('payment_category','fine')
                    ->filter(fn($p) => ($p->remarks ?? '') !== $discSent)->sum('amount_paid');

    // Per-payment discount subtotals
    $bdDiscDown  = $mainBooking->payments->where('status','paid')->where('payment_category','down_payment')->sum('discount_amount');
    $bdDiscMonth = $mainBooking->payments->where('status','paid')->where('payment_category','installment')->sum('discount_amount');
    $bdDiscQtr   = $mainBooking->payments->where('status','paid')->where('payment_category','quarterly_installment')->sum('discount_amount');
    $bdDiscBal   = $mainBooking->payments->where('status','paid')->where('payment_category','plot_balance')->sum('discount_amount')
                   + $mainBooking->payments->where('status','paid')->filter(fn($p) => ($p->remarks ?? '') === $discSent)->sum('amount_paid');
    $bdDiscOther = $mainBooking->payments->where('status','paid')->where('payment_category','others')->sum('discount_amount');

    $bdCashTotal  = $bdDown + $bdMonthly + $bdQtr + $bdBalance + $bdOthers + $bdFine;
    $bdDiscTotal  = ($plotDiscount ?? 0) + $paymentDiscountCredits;
@endphp
@if($showBreakdown)
<div class="ldg-card" style="margin-top:24px;">
    <div class="ldg-card-head">
        <div>
            <p class="ldg-card-title">Payment & Discount Breakdown</p>
            <p class="ldg-card-sub">How the full plot price is settled — installments, lump sum, and discounts</p>
        </div>
    </div>
    <div style="padding:20px 24px;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                    <th style="padding:9px 14px;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;text-align:left;">Category</th>
                    <th style="padding:9px 14px;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;text-align:center;">Count</th>
                    <th style="padding:9px 14px;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;text-align:right;">Cash Paid</th>
                    <th style="padding:9px 14px;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;text-align:right;">Discount</th>
                    <th style="padding:9px 14px;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;text-align:right;">Credit Total</th>
                </tr>
            </thead>
            <tbody>

                {{-- Agreed price row --}}
                @if(($plotDiscount ?? 0) > 0)
                <tr style="background:#fffbeb;border-bottom:1px solid #fde68a;">
                    <td style="padding:8px 14px;" colspan="2">
                        <div style="font-size:12px;font-weight:700;color:#92400e;">
                            <span style="margin-right:6px;">★</span>Plot Discount
                            @if(!empty($mainBooking->plot->discount_reason))
                            <span style="font-size:10px;font-weight:600;color:#b45309;margin-left:4px;">({{ $mainBooking->plot->discount_reason }})</span>
                            @endif
                        </div>
                        <div style="font-size:10px;color:#a16207;margin-top:2px;">Base price PKR {{ number_format($trueTotal + $plotDiscount) }} → agreed PKR {{ number_format($trueTotal) }}</div>
                    </td>
                    <td style="padding:8px 14px;text-align:right;color:#94a3b8;font-size:11px;">—</td>
                    <td style="padding:8px 14px;text-align:right;font-weight:800;color:#d97706;">− PKR {{ number_format($plotDiscount) }}</td>
                    <td style="padding:8px 14px;text-align:right;font-size:10px;color:#94a3b8;font-style:italic;">baked in</td>
                </tr>
                @endif

                {{-- Down payment --}}
                @if($bdDown > 0 || ($mainBooking->down_payment ?? 0) > 0)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:8px 14px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#1d4ed8;display:inline-block;flex-shrink:0;"></span>
                            <span style="font-size:13px;font-weight:700;color:#1e293b;">Down Payment</span>
                        </div>
                    </td>
                    <td style="padding:8px 14px;text-align:center;font-size:11px;color:#64748b;">
                        {{ $mainBooking->payments->where('status','paid')->where('payment_category','down_payment')->count() }} receipt(s)
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:700;color:#16a34a;font-size:13px;">PKR {{ number_format($bdDown) }}</td>
                    <td style="padding:8px 14px;text-align:right;color:{{ $bdDiscDown > 0 ? '#d97706' : '#cbd5e1' }};font-weight:{{ $bdDiscDown > 0 ? '700' : '400' }};">
                        {{ $bdDiscDown > 0 ? '− PKR '.number_format($bdDiscDown) : '—' }}
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:800;color:#1d4ed8;">PKR {{ number_format($bdDown + $bdDiscDown) }}</td>
                </tr>
                @endif

                {{-- Monthly installments --}}
                @if($hasInstallmentPlan && ($bdMonthly > 0 || $paidInstallmentCount > 0))
                <tr style="border-bottom:1px solid #f1f5f9;background:#fafffe;">
                    <td style="padding:8px 14px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;flex-shrink:0;"></span>
                            <div>
                                <span style="font-size:13px;font-weight:700;color:#1e293b;">Monthly Installments</span>
                                <div style="font-size:10px;color:#64748b;margin-top:1px;">{{ $paidInstallmentCount }} of {{ $totalInstallmentCount }} paid × PKR {{ number_format($monthlyInstallment) }}/mo</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:8px 14px;text-align:center;">
                        <span style="font-size:12px;font-weight:700;background:#f0fdf4;color:#16a34a;padding:2px 10px;border-radius:20px;border:1px solid #86efac;">
                            {{ $paidInstallmentCount }}/{{ $totalInstallmentCount }}
                        </span>
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:700;color:#16a34a;font-size:13px;">PKR {{ number_format($bdMonthly) }}</td>
                    <td style="padding:8px 14px;text-align:right;color:{{ $bdDiscMonth > 0 ? '#d97706' : '#cbd5e1' }};font-weight:{{ $bdDiscMonth > 0 ? '700' : '400' }};">
                        {{ $bdDiscMonth > 0 ? '− PKR '.number_format($bdDiscMonth) : '—' }}
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:800;color:#15803d;">PKR {{ number_format($bdMonthly + $bdDiscMonth) }}</td>
                </tr>
                @endif

                {{-- Quarterly installments --}}
                @if($hasQuarterlyPlan && ($bdQtr > 0 || $paidQuarterlyCount > 0))
                <tr style="border-bottom:1px solid #f1f5f9;background:#fffdf5;">
                    <td style="padding:8px 14px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#b45309;display:inline-block;flex-shrink:0;"></span>
                            <div>
                                <span style="font-size:13px;font-weight:700;color:#1e293b;">Quarterly Installments</span>
                                <div style="font-size:10px;color:#64748b;margin-top:1px;">{{ $paidQuarterlyCount }} of {{ $totalQuarterlyCount }} paid × PKR {{ number_format($quarterlyAmount) }}/qtr</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:8px 14px;text-align:center;">
                        <span style="font-size:12px;font-weight:700;background:#fffbeb;color:#b45309;padding:2px 10px;border-radius:20px;border:1px solid #fde68a;">
                            {{ $paidQuarterlyCount }}/{{ $totalQuarterlyCount }}
                        </span>
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:700;color:#16a34a;font-size:13px;">PKR {{ number_format($bdQtr) }}</td>
                    <td style="padding:8px 14px;text-align:right;color:{{ $bdDiscQtr > 0 ? '#d97706' : '#cbd5e1' }};font-weight:{{ $bdDiscQtr > 0 ? '700' : '400' }};">
                        {{ $bdDiscQtr > 0 ? '− PKR '.number_format($bdDiscQtr) : '—' }}
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:800;color:#92400e;">PKR {{ number_format($bdQtr + $bdDiscQtr) }}</td>
                </tr>
                @endif

                {{-- Lump sum / Plot balance --}}
                @if($bdBalance > 0 || $bdDiscBal > 0)
                <tr style="border-bottom:1px solid #f1f5f9;background:#f0fff4;">
                    <td style="padding:8px 14px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#0369a1;display:inline-block;flex-shrink:0;"></span>
                            <div>
                                <span style="font-size:13px;font-weight:700;color:#1e293b;">Lump Sum / Plot Balance</span>
                                @if($bdDiscBal > 0)
                                <div style="font-size:10px;color:#16a34a;margin-top:1px;font-weight:700;">
                                    ★ Full-payment discount applied: PKR {{ number_format($bdDiscBal) }} waived
                                </div>
                                @else
                                <div style="font-size:10px;color:#64748b;margin-top:1px;">Remaining balance paid in one go</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:8px 14px;text-align:center;font-size:11px;color:#64748b;">
                        {{ $mainBooking->payments->where('status','paid')->where('payment_category','plot_balance')->count() }} receipt(s)
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:700;color:#16a34a;font-size:13px;">PKR {{ number_format($bdBalance) }}</td>
                    <td style="padding:8px 14px;text-align:right;color:{{ $bdDiscBal > 0 ? '#d97706' : '#cbd5e1' }};font-weight:{{ $bdDiscBal > 0 ? '800' : '400' }};font-size:{{ $bdDiscBal > 0 ? '13' : '11' }}px;">
                        {{ $bdDiscBal > 0 ? '− PKR '.number_format($bdDiscBal) : '—' }}
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:800;color:#0369a1;font-size:13px;">PKR {{ number_format($bdBalance + $bdDiscBal) }}</td>
                </tr>
                @endif

                {{-- Others --}}
                @if($bdOthers > 0)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:8px 14px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#64748b;display:inline-block;flex-shrink:0;"></span>
                            <span style="font-size:13px;font-weight:700;color:#1e293b;">Others</span>
                        </div>
                    </td>
                    <td style="padding:8px 14px;text-align:center;font-size:11px;color:#64748b;">
                        {{ $mainBooking->payments->where('status','paid')->where('payment_category','others')->count() }} receipt(s)
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:700;color:#16a34a;font-size:13px;">PKR {{ number_format($bdOthers) }}</td>
                    <td style="padding:8px 14px;text-align:right;color:{{ $bdDiscOther > 0 ? '#d97706' : '#cbd5e1' }};font-weight:{{ $bdDiscOther > 0 ? '700' : '400' }};">
                        {{ $bdDiscOther > 0 ? '− PKR '.number_format($bdDiscOther) : '—' }}
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:800;color:#475569;">PKR {{ number_format($bdOthers + $bdDiscOther) }}</td>
                </tr>
                @endif

                {{-- Fine --}}
                @if($bdFine > 0)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:8px 14px;" colspan="2">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#dc2626;display:inline-block;flex-shrink:0;"></span>
                            <span style="font-size:13px;font-weight:700;color:#1e293b;">Fine / Penalty</span>
                        </div>
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-weight:700;color:#dc2626;font-size:13px;">PKR {{ number_format($bdFine) }}</td>
                    <td style="padding:8px 14px;text-align:right;color:#cbd5e1;">—</td>
                    <td style="padding:8px 14px;text-align:right;font-weight:800;color:#dc2626;">PKR {{ number_format($bdFine) }}</td>
                </tr>
                @endif

            </tbody>
            <tfoot>
                {{-- Totals row --}}
                <tr style="background:#1e3a8a;color:#fff;">
                    <td style="padding:11px 14px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;" colspan="2">Grand Total</td>
                    <td style="padding:11px 14px;text-align:right;font-size:14px;font-weight:800;color:#86efac;">PKR {{ number_format($bdCashTotal) }}</td>
                    <td style="padding:11px 14px;text-align:right;font-size:13px;font-weight:800;color:#fbbf24;">
                        {{ $bdDiscTotal > 0 ? '− PKR '.number_format($bdDiscTotal) : '—' }}
                    </td>
                    <td style="padding:11px 14px;text-align:right;font-size:15px;font-weight:900;color:#fff;">PKR {{ number_format($bdCashTotal + $paymentDiscountCredits) }}</td>
                </tr>
                {{-- Remaining row --}}
                <tr style="background:{{ $remaining <= 0 ? '#f0fdf4' : '#fef2f2' }};border-top:2px solid {{ $remaining <= 0 ? '#86efac' : '#fecaca' }};">
                    <td colspan="4" style="padding:8px 14px;font-size:12px;font-weight:700;color:{{ $remaining <= 0 ? '#15803d' : '#dc2626' }};">
                        {{ $remaining <= 0 ? '✓ All dues cleared — full price settled' : 'Outstanding balance' }}
                    </td>
                    <td style="padding:8px 14px;text-align:right;font-size:14px;font-weight:900;color:{{ $remaining <= 0 ? '#15803d' : '#dc2626' }};">
                        PKR {{ number_format($remaining) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- ═══ FINANCIAL STATEMENT TABLE ══════════════════════════════ --}}
<div class="ldg-card" style="margin-top:24px;">
    <div class="ldg-card-head">
        <div>
            <p class="ldg-card-title">Financial Statement</p>
            <p class="ldg-card-sub">{{ $mainBooking->payments->count() }} transaction{{ $mainBooking->payments->count()!==1?'s':'' }} recorded</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if(!$readOnly && !$pendingTransfer)
            <button class="btn-navy" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Log Payment
            </button>
            @endif
        </div>
    </div>
    <div class="stmt-wrap">
        <table class="stmt-table">
            <thead>
                <tr>
                    <th style="width:38px;text-align:center;">#</th>
                    <th>Receipt No.</th>
                    <th>Category</th>
                    <th style="text-align:center;">Inst.</th>
                    <th>Amount Paid</th>
                    <th style="color:#d97706;">Discount</th>
                    <th>Mode</th>
                    <th>Paid Date</th>
                    <th>Status</th>
                    <th style="text-align:center;">Proof</th>
                    <th style="text-align:center;">Receipt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transferHistory as $th)
                <tr style="background:#f0f9ff;border-left:3px solid #0ea5e9;">
                    <td style="text-align:center;color:#0ea5e9;">⇄</td>
                    <td><strong style="font-size:11px;color:#0369a1;font-family:monospace;">{{ $th->deed_no }}</strong></td>
                    <td><span style="font-size:10px;font-weight:700;background:#e0f2fe;color:#0369a1;padding:3px 10px;border-radius:20px;">Transfer Fee</span></td>
                    <td style="text-align:center;color:#94a3b8;">—</td>
                    <td>
                        @if($th->transfer_fee > 0) <strong style="color:#0369a1;font-size:13px;">+ PKR {{ number_format($th->transfer_fee) }}</strong>
                        @else <span style="color:#94a3b8;font-size:11px;font-style:italic;">Waived</span> @endif
                    </td>
                    <td style="color:#cbd5e1;font-size:11px;">—</td>
                    <td><span style="font-size:10px;background:#f1f5f9;color:#475569;padding:3px 10px;border-radius:20px;font-weight:700;">{{ ucfirst(str_replace('_',' ',$th->payment_method??'N/A')) }}</span></td>
                    <td>
                        <div style="font-size:12px;font-weight:600;">{{ \Carbon\Carbon::parse($th->transfer_date)->format('d M Y') }}</div>
                        <div style="font-size:10px;color:#94a3b8;">{{ $th->fromCustomer->name??'—' }} → {{ $th->toCustomer->name??'—' }}</div>
                    </td>
                    <td><span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $th->transfer_fee_status==='paid'?'#dcfce7':'#fef9c3' }};color:{{ $th->transfer_fee_status==='paid'?'#15803d':'#854d0e' }};">{{ ucfirst($th->transfer_fee_status) }}</span></td>
                    <td style="text-align:center;">
                        @if($th->payment_proof) <a href="{{ asset('storage/transferFeeRec/'.$th->payment_proof) }}" target="_blank" style="font-size:10px;font-weight:700;color:#0369a1;">View</a>
                        @else <span style="color:#cbd5e1;">—</span> @endif
                    </td>
                    <td style="text-align:center;"><a href="{{ route('transfers.deed',$th->id) }}" target="_blank" style="background:#e0f2fe;border:1px solid #7dd3fc;color:#0369a1;padding:4px 10px;border-radius:7px;font-size:10px;font-weight:700;text-decoration:none;">Deed</a></td>
                </tr>
                @endforeach

                @forelse($mainBooking->payments->sortByDesc('paid_date') as $p)
                <tr>
                    <td style="text-align:center;color:#94a3b8;font-size:11px;">{{ $loop->iteration }}</td>
                    <td><strong style="font-size:12px;color:#1e3a8a;letter-spacing:.3px;">{{ $p->receipt_no??'—' }}</strong></td>
                    <td>
                        @php
                            $cc = match(strtolower($p->payment_category??'')) {
                                'down_payment'           => 'cat-down',
                                'quarterly_installment'  => 'cat-other',
                                'installment'            => 'cat-install',
                                'processing_fee'         => 'cat-proc',
                                'plot_balance'           => 'cat-balance',
                                'fine'                   => 'cat-fine',
                                default                  => 'cat-other',
                            };
                            $catLabel = ucwords(str_replace('_',' ', $p->payment_category ?? 'Other'));
                        @endphp
                        <span class="cat-pill {{ $cc }}">{{ $catLabel }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($p->installment_no)
                            <span style="font-size:11px;font-weight:700;background:#f1f5f9;border:1px solid #e2e8f0;color:#475569;padding:2px 8px;border-radius:8px;">#{{ $p->installment_no }}</span>
                        @else <span style="color:#cbd5e1;">—</span> @endif
                    </td>
                    <td>
                        <strong style="color:#16a34a;font-size:14px;">+ PKR {{ number_format($p->amount_paid) }}</strong>
                        @if(($p->discount_amount ?? 0) > 0)
                        <div style="font-size:10px;font-weight:700;color:#d97706;margin-top:2px;">+PKR {{ number_format($p->discount_amount) }} disc.</div>
                        @endif
                    </td>
                    <td>
                        @php
                            $discSentRow = 'Settlement discount — waived amount (not collected).';
                            $isDiscRow   = ($p->remarks ?? '') === $discSentRow;
                            $rowDiscount = $isDiscRow ? $p->amount_paid : ($p->discount_amount ?? 0);
                        @endphp
                        @if($rowDiscount > 0)
                            <span style="font-size:11px;font-weight:800;background:#fffbeb;color:#d97706;padding:3px 10px;border-radius:20px;border:1px solid #fde68a;white-space:nowrap;">
                                − PKR {{ number_format($rowDiscount) }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:11px;">—</span>
                        @endif
                    </td>
                    <td><span style="font-size:10px;font-weight:700;background:#f1f5f9;color:#475569;padding:3px 10px;border-radius:20px;">{{ ucfirst(str_replace('_',' ',$p->payment_type??'—')) }}</span></td>
                    <td>
                        @if($p->paid_date)
                            <div style="font-size:12px;font-weight:600;">{{ \Carbon\Carbon::parse($p->paid_date)->format('d M Y') }}</div>
                            <div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($p->paid_date)->diffForHumans() }}</div>
                        @else <span style="color:#94a3b8;">—</span> @endif
                    </td>
                    <td>
                        <span class="spill spill-{{ strtolower($p->status??'pending') }}"><span class="spill-dot"></span>{{ ucfirst($p->status??'Pending') }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($p->payment_proof) <a href="{{ asset('storage/'.$p->payment_proof) }}" target="_blank" style="font-size:10px;font-weight:700;color:#1d4ed8;text-decoration:none;">View</a>
                        @else <span style="color:#cbd5e1;font-size:11px;">—</span> @endif
                    </td>
                    <td style="text-align:center;white-space:nowrap;">
                        <a href="{{ route('payment.receipt',$p->id) }}" target="_blank" class="btn-pdf">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6"/></svg>
                            PDF
                        </a>
                        @if(!$readOnly)
                        <button type="button"
                            class="btn-edit-payment"
                            onclick="openEditPayment({{ json_encode([
                                'id'               => $p->id,
                                'receipt_no'       => $p->receipt_no ?? '',
                                'paid_date'        => $p->paid_date ? \Carbon\Carbon::parse($p->paid_date)->format('Y-m-d') : '',
                                'payment_category' => $p->payment_category ?? '',
                                'payment_type'     => $p->payment_type ?? '',
                                'amount_paid'      => $p->amount_paid,
                                'installment_no'   => $p->installment_no ?? '',
                                'quarterly_no'     => $p->quarterly_no ?? '',
                                'bank_ref'         => $p->bank_ref ?? '',
                                'remarks'          => $p->remarks ?? '',
                                'status'           => $p->status ?? 'paid',
                            ]) }})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                            Edit
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align:center;padding:50px 20px;color:#94a3b8;">
                        <p style="font-weight:700;font-size:13px;margin:0 0 4px;">No transactions yet</p>
                        <p style="font-size:12px;margin:0;">Click "Log Payment" to record the first payment.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($mainBooking->payments->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;color:#94a3b8;font-size:10px;letter-spacing:.7px;font-weight:700;">TOTAL COLLECTED (CASH)</td>
                    <td style="color:#16a34a;font-size:15px;font-weight:800;">+ PKR {{ number_format($tfootTotal) }}</td>
                    <td style="color:#d97706;font-size:12px;font-weight:800;">
                        @if($paymentDiscountCredits > 0) − PKR {{ number_format($paymentDiscountCredits) }} @else — @endif
                    </td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ═══ FEES & CHARGES ══════════════════════════════════════════ --}}
@if($bookingFees->isNotEmpty())
<div class="ldg-card" style="margin-top:24px;">
    <div class="ldg-card-head">
        <div>
            <p class="ldg-card-title">Fees & Charges</p>
            <p class="ldg-card-sub">Registry · Development · Security · Transfer fees</p>
        </div>
        <a href="{{ route('fee.management') }}" class="btn-soft" style="font-size:11px;padding:7px 14px;">
            Manage Fees →
        </a>
    </div>
    <div class="stmt-wrap">
        {{--
            Columns (6):
            1. Fee / # entry
            2. Date
            3. Amount  ← BOTH individual payments AND fee totals share this column
            4. Mode / Info
            5. Status
            6. Receipt
        --}}
        <table class="stmt-table">
            <thead>
                <tr>
                    <th>Fee Type / Entry</th>
                    <th>Date</th>
                    <th style="text-align:right;">Amount (PKR)</th>
                    <th>Mode / Notes</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Receipt</th>
                </tr>
            </thead>
            <tbody>
            @foreach($bookingFees as $bf)
            @php
                $meta           = $bf->meta;
                $settled        = $bf->is_settled;
                $sortedPayments = $bf->payments->sortBy(function($p){ return [$p->paid_date, $p->id]; })->values();
                $runningTotal   = 0;
            @endphp

            {{-- ── Fee type header row ── --}}
            <tr style="background:#f8fafc;border-top:2px solid #e2e8f0;">
                <td style="padding-top:10px;padding-bottom:10px;">
                    <span style="font-size:11px;font-weight:700;background:{{ $meta['bg'] }};color:{{ $meta['color'] }};padding:3px 10px;border-radius:20px;">
                        {{ $meta['label'] }}
                    </span>
                    @if($bf->fee_type === 'security' && isset($secMonthlyRate) && $secMonthlyRate > 0)
                    <div style="font-size:10px;color:#7c3aed;margin-top:3px;font-weight:700;">
                        PKR {{ number_format($secMonthlyRate) }}/month
                        @if(isset($secMonthsPaid)) · {{ $secMonthsPaid }} month(s) paid @endif
                        @if(isset($secMonthsUnpaid) && $secMonthsUnpaid > 0) · <span style="color:#dc2626;">{{ $secMonthsUnpaid }} unpaid</span> @endif
                    </div>
                    @elseif($bf->amount > 0)
                    <div style="font-size:10px;color:#94a3b8;margin-top:3px;">
                        Suggested: PKR {{ number_format($bf->amount) }}
                    </div>
                    @endif
                </td>
                <td style="color:#94a3b8;font-size:11px;padding-top:10px;">
                    {{ $sortedPayments->count() }} payment{{ $sortedPayments->count() !== 1 ? 's' : '' }}
                </td>
                <td style="text-align:right;padding-top:10px;">
                    <strong style="font-size:14px;color:#16a34a;">
                        {{ $bf->paid_amount > 0 ? 'PKR '.number_format($bf->paid_amount) : '—' }}
                    </strong>
                    <div style="font-size:10px;color:#94a3b8;font-weight:500;">total collected</div>
                </td>
                <td style="padding-top:10px;">
                    @if($bf->fee_type === 'security')
                        @if(isset($secOutstanding) && $secOutstanding > 0)
                            <span style="color:#dc2626;font-size:12px;font-weight:700;">PKR {{ number_format($secOutstanding) }} due</span>
                        @else
                            <span style="color:#15803d;font-size:12px;font-weight:700;">✓ Up to date</span>
                        @endif
                    @elseif($settled)
                        <span style="color:#15803d;font-size:12px;font-weight:700;">✓ Settled</span>
                    @elseif($bf->paid_amount > 0)
                        <span style="color:#b45309;font-size:12px;font-weight:700;">Open</span>
                    @else
                        <span style="color:#dc2626;font-size:12px;font-weight:700;">Unpaid</span>
                    @endif
                </td>
                <td style="text-align:center;padding-top:10px;">
                    <span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;
                        background:{{ $settled ? '#dcfce7' : ($bf->paid_amount > 0 ? '#fef9c3' : '#fef2f2') }};
                        color:{{ $settled ? '#15803d' : ($bf->paid_amount > 0 ? '#854d0e' : '#dc2626') }};">
                        {{ $settled ? '✓ Cleared' : ($bf->paid_amount > 0 ? 'Partial' : 'Unpaid') }}
                    </span>
                </td>
                <td></td>
            </tr>

            {{-- ── Individual payment rows (amount in same column as total above) ── --}}
            @if($sortedPayments->isEmpty())
            <tr style="background:#fffbeb;">
                <td colspan="6" style="padding-left:32px;color:#94a3b8;font-size:11px;font-style:italic;border-bottom:1px solid #fef3c7;">
                    No payments recorded yet.
                </td>
            </tr>
            @else
            @foreach($sortedPayments as $i => $pay)
            @php $runningTotal += (float)$pay->amount; @endphp
            <tr style="background:#fff;">
                <td style="padding-left:28px;">
                    <span style="font-size:11px;color:#64748b;font-weight:700;">↳ Payment #{{ $i + 1 }}</span>
                    @if($pay->receipt_no)
                    <div style="font-size:10px;color:#94a3b8;">{{ $pay->receipt_no }}</div>
                    @endif
                </td>
                <td style="font-size:12px;color:#475569;">
                    {{ \Carbon\Carbon::parse($pay->paid_date)->format('d M Y') }}
                </td>
                <td style="text-align:right;">
                    {{-- ← same column as "total collected" in header row --}}
                    <strong style="font-size:13px;color:#16a34a;">PKR {{ number_format($pay->amount) }}</strong>
                    <div style="font-size:10px;color:#94a3b8;">running: PKR {{ number_format($runningTotal) }}</div>
                </td>
                <td>
                    <span style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;background:#f1f5f9;color:#475569;">
                        {{ ucfirst(str_replace('_',' ', $pay->payment_mode ?? 'cash')) }}
                    </span>
                    @if($pay->notes)
                    <div style="font-size:10px;color:#94a3b8;margin-top:2px;">{{ $pay->notes }}</div>
                    @endif
                </td>
                <td></td>
                <td style="text-align:center;">
                    <a href="{{ route('fee.receipt', $pay->id) }}" target="_blank"
                       style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:4px 10px;border-radius:7px;font-size:10px;font-weight:700;text-decoration:none;">
                        PDF
                    </a>
                </td>
            </tr>
            @endforeach
            @endif

            @endforeach
            </tbody>
            @php $totalFeesPaid = $bookingFees->sum('paid_amount'); @endphp
            <tfoot>
                <tr style="border-top:2px solid #e2e8f0;">
                    <td colspan="2" style="text-align:right;color:#94a3b8;font-size:10px;letter-spacing:.7px;font-weight:700;padding-top:10px;">
                        TOTAL FEES COLLECTED
                    </td>
                    <td style="text-align:right;padding-top:10px;">
                        <strong style="color:#16a34a;font-size:15px;font-weight:800;">PKR {{ number_format($totalFeesPaid) }}</strong>
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- ═══ PAYMENT MODAL ═══════════════════════════════════════════ --}}
@if(!$readOnly && !$pendingTransfer)
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm" action="{{ route('plot.payment.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="booking_id" value="{{ $mainBooking->id }}">
                <input type="hidden" name="plot_id"    value="{{ $mainBooking->plot_id }}">
                <input type="hidden" name="status"     value="paid">

                {{-- ╔══════════════════════════════════════════════════╗
                     ║  THE FIX: single authoritative installment_no   ║
                     ║  field — JS writes here before submit           ║
                     ╚══════════════════════════════════════════════════╝ --}}
                <input type="hidden" name="installment_no" id="finalInstallmentNo" value="{{ old('installment_no') }}">
                <input type="hidden" name="quarterly_no" id="finalQuarterlyNo" value="{{ old('quarterly_no') }}">

                <div class="modal-body">
                    {{-- Info bar --}}
                    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
                        <div class="alert-info-custom" style="flex:1;margin-bottom:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                            <div>
                                <strong>{{ $mainBooking->customer->name }}</strong>
                                &nbsp;·&nbsp; Plot #{{ $mainBooking->plot->plot_number }}
                                @if($mainBooking->plot->block) — {{ $mainBooking->plot->block }}@endif
                                &nbsp;·&nbsp; <strong>{{ $mainBooking->customer_booking_id }}</strong>
                                @if($hasInstallmentPlan && $paidInstallmentCount < $totalInstallmentCount)
                                <div style="margin-top:5px;font-size:11px;color:#1d4ed8;font-weight:700;">
                                    Month {{ $paidInstallmentCount + 1 }} of {{ $totalInstallmentCount }} due
                                    &nbsp;·&nbsp; PKR {{ number_format($nextInstallmentAmount) }}
                                    @if($nextInstallmentDueDate)
                                        &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($nextInstallmentDueDate)->format('d M Y') }}
                                    @endif
                                </div>
                                @elseif($hasQuarterlyPlan && $paidQuarterlyCount < $totalQuarterlyCount)
                                <div style="margin-top:5px;font-size:11px;color:#b45309;font-weight:700;">
                                    Quarter {{ $paidQuarterlyCount + 1 }} of {{ $totalQuarterlyCount }} due
                                    &nbsp;·&nbsp; PKR {{ number_format($nextQuarterlyAmount) }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @if($remaining > 0)
                        <div style="display:flex;flex-direction:column;justify-content:center;align-items:flex-end;background:#fef2f2;border:1.5px solid #fecaca;padding:8px 14px;border-radius:10px;white-space:nowrap;">
                            <div style="font-size:10px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:.4px;">Remaining</div>
                            <div style="font-size:16px;font-weight:900;color:#dc2626;">PKR {{ number_format($remaining) }}</div>
                        </div>
                        @else
                        <div style="display:flex;align-items:center;gap:6px;background:#f0fdf4;border:1.5px solid #86efac;padding:8px 14px;border-radius:10px;white-space:nowrap;">
                            <span style="font-size:11px;font-weight:800;color:#15803d;">✓ Fully Paid</span>
                        </div>
                        @endif
                    </div>

                    <div class="row g-3">

                        {{-- Receipt No --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Receipt / Voucher No. <span class="text-danger">*</span></label>
                            @php
                                $receiptPrefix = \App\Models\SystemConfig::get('receipt_prefix','REC');
                                // Derive next sequence from the highest existing receipt with this prefix
                                $lastReceipt = \App\Models\PlotPayment::where('receipt_no', 'like', $receiptPrefix.'-%')
                                    ->orderByRaw('CAST(SUBSTRING(receipt_no, ' . (strlen($receiptPrefix) + 2) . ') AS UNSIGNED) DESC')
                                    ->value('receipt_no');
                                $lastSeq = $lastReceipt
                                    ? (int) substr($lastReceipt, strlen($receiptPrefix) + 1)
                                    : 0;
                                $autoReceiptNo = $receiptPrefix.'-'.str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
                            @endphp
                            <input type="text" name="receipt_no" class="form-control {{ $errors->has('receipt_no')?'is-invalid':'' }}"
                                   value="{{ old('receipt_no',$autoReceiptNo) }}" required>
                            <div style="font-size:10.5px;color:#94a3b8;margin-top:4px;">Auto-generated — you can edit this</div>
                            @error('receipt_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Paid Date --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="paid_date" class="form-control {{ $errors->has('paid_date')?'is-invalid':'' }}"
                                   value="{{ old('paid_date',date('Y-m-d')) }}" required>
                            @error('paid_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Category --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Payment Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="payment_category" id="paymentCategory" required onchange="onCategoryChange()">
                                <option value="">— Select Category —</option>
                                @if(!$isFullyPaid)
                                    @php
                                        $downLeft = max(0, ($downDue ?? 0) - ($downPaid ?? 0));
                                    @endphp

                                    {{-- Down payment: show if unpaid balance remains --}}
                                    @if($downLeft > 0)
                                    <option value="down_payment" data-amount="{{ $downLeft }}">Down Payment — PKR {{ number_format($downLeft) }} due</option>
                                    @endif

                                    {{-- Monthly: hide when booking completed or all paid --}}
                                    @if($hasInstallmentPlan && $paidInstallmentCount < $totalInstallmentCount && $mainBooking->status !== 'completed')
                                    <option value="installment" data-amount="{{ $nextInstallmentAmount }}" data-type="monthly">
                                        Monthly Instalment — PKR {{ number_format($nextInstallmentAmount) }} ({{ $paidInstallmentCount }}/{{ $totalInstallmentCount }} paid)
                                    </option>
                                    @endif

                                    {{-- Quarterly: hide when booking completed or all paid --}}
                                    @if($hasQuarterlyPlan && $paidQuarterlyCount < $totalQuarterlyCount && $mainBooking->status !== 'completed')
                                    <option value="quarterly_installment" data-amount="{{ $nextQuarterlyAmount }}" data-type="quarterly">
                                        Quarterly Instalment — PKR {{ number_format($nextQuarterlyAmount) }} ({{ $paidQuarterlyCount }}/{{ $totalQuarterlyCount }} paid)
                                    </option>
                                    @endif

                                    {{-- Generic balance: cash / no-plan bookings only (installment bookings use Lump Sum button) --}}
                                    @if($remaining > 0 && !$hasInstallmentPlan && !$hasQuarterlyPlan)
                                    <option value="plot_balance" data-amount="{{ $remaining }}" data-is-full="1" id="fullBalanceOption">Plot Balance — Pay Full (PKR {{ number_format($remaining) }})</option>
                                    <option value="plot_balance" data-amount="{{ $remaining }}" data-is-full="0" id="partialBalanceOption">Plot Balance — Pay Partial (adjust below)</option>
                                    @endif
                                @else
                                    <option disabled style="color:#15803d;font-weight:bold;">✓ Plot Fully Paid — Balance: PKR 0</option>
                                @endif

                                <option value="fine">Fine / Penalty</option>
                                <option value="others">Others</option>
                            </select>
                            <input type="hidden" name="is_full_payment" id="isFullPayment" value="0">
                            <div id="categoryHint" style="font-size:10.5px;color:#94a3b8;margin-top:5px;">
                                {{ $isFullyPaid ? 'All dues cleared — only fees can be charged.' : 'Select a category to auto-fill the amount.' }}
                            </div>
                            @error('payment_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Amount Paid (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="amount_paid" id="amountPaid"
                                   class="form-control {{ $errors->has('amount_paid')?'is-invalid':'' }}"
                                   placeholder="0" step="any" value="{{ old('amount_paid') }}" required>
                            <div id="amountHint" style="font-size:10.5px;color:#94a3b8;margin-top:4px;">Remaining: PKR {{ number_format($remaining) }}</div>
                            @error('amount_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Monthly Installment Picker --}}
                        @if($hasInstallmentPlan)
                        @php
                            $paidInstNos = $mainBooking->payments->where('payment_category','installment')->where('status','paid')->pluck('installment_no')->toArray();
                            $nextDue = $paidInstallmentCount + 1;
                        @endphp
                        <div class="col-12" id="installmentSection" style="display:none;">
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px 18px;">
                                <div style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                                    Monthly Plan — {{ $totalInstallmentCount }} months total
                                </div>
                                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(82px,1fr));gap:8px;margin-bottom:14px;">
                                    @for($i=1;$i<=$totalInstallmentCount;$i++)
                                    @php $isPaid = in_array($i,$paidInstNos); @endphp
                                    <div class="inst-month-btn {{ $isPaid?'inst-paid':($i===$nextDue?'inst-next':'inst-future') }}"
                                         data-month="{{ $i }}"
                                         onclick="{{ $isPaid?'void(0)':'selectInstallmentMonth('.$i.')' }}"
                                         style="{{ $isPaid?'cursor:default;':'cursor:pointer;' }}">
                                        <div style="font-size:9px;font-weight:700;">Month</div>
                                        <div style="font-size:15px;font-weight:800;">{{ $i }}</div>
                                        <div style="font-size:9px;margin-top:1px;">
                                            @if($isPaid) ✓ Paid
                                            @elseif($i===$nextDue) {{ $remainingInstallmentCount===1?'Last Due':'Next Due' }}
                                            @else Upcoming @endif
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                                {{-- display-only, no name attribute --}}
                                <div style="display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:#475569;">
                                    <span>Selected: <strong id="selectedMonthLabel" style="color:#1e3a8a;">Month {{ $nextDue }}</strong></span>
                                    <span>Paid: <strong style="color:#16a34a;">{{ $paidInstallmentCount }}/{{ $totalInstallmentCount }}</strong></span>
                                    <span>Left: <strong style="color:#dc2626;">{{ $remainingInstallmentCount }} months</strong></span>
                                </div>
                            </div>
                        </div>
                        @else
                            <div class="col-12" id="installmentSection" style="display:none;"></div>
                        @endif

                        {{-- Quarterly Picker --}}
                        @if($hasQuarterlyPlan)
                        @php $nextQtrDue = $paidQuarterlyCount + 1; @endphp
                        <div class="col-12" id="quarterlySection" style="display:none;">
                            <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:16px 18px;">
                                <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                                    Quarterly Plan — {{ $totalQuarterlyCount }} quarters total
                                </div>
                                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px;margin-bottom:14px;">
                                    @for($q=1;$q<=$totalQuarterlyCount;$q++)
                                    @php $qPaid = in_array($q, $paidQuarterNos); @endphp
                                    <div class="qtr-btn {{ $qPaid?'inst-paid':($q===$nextQtrDue?'inst-next':'inst-future') }}"
                                         data-quarter="{{ $q }}"
                                         onclick="{{ $qPaid?'void(0)':'selectQuarter('.$q.')' }}"
                                         style="{{ $qPaid?'cursor:default;':'cursor:pointer;' }}">
                                        <div style="font-size:9px;font-weight:700;">Quarter</div>
                                        <div style="font-size:15px;font-weight:800;">{{ $q }}</div>
                                        <div style="font-size:9px;margin-top:1px;">
                                            @if($qPaid) ✓ Paid
                                            @elseif($q===$nextQtrDue) {{ $remainingQuarterlyCount===1?'Last Due':'Next Due' }}
                                            @else Upcoming @endif
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                                {{-- display-only, no name attribute --}}
                                <div style="display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:#475569;">
                                    <span>Selected: <strong id="selectedQuarterLabel" style="color:#b45309;">Quarter {{ $nextQtrDue }}</strong></span>
                                    <span>Paid: <strong style="color:#16a34a;">{{ $paidQuarterlyCount }}/{{ $totalQuarterlyCount }}</strong></span>
                                    <span>Left: <strong style="color:#dc2626;">{{ $remainingQuarterlyCount }} quarters</strong></span>
                                </div>
                            </div>
                        </div>
                        @else
                            <div class="col-12" id="quarterlySection" style="display:none;"></div>
                        @endif

                        {{-- Due Date --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Due Date</label>
                            <input type="date" name="due_date" id="dueDate" class="form-control" value="{{ old('due_date') }}">
                            <div style="font-size:10.5px;color:#94a3b8;margin-top:4px;" id="dueDateHint">Auto-filled for installments</div>
                        </div>

                        {{-- Payment Mode --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Payment Mode <span class="text-danger">*</span></label>
                            <select class="form-select {{ $errors->has('payment_type')?'is-invalid':'' }}" name="payment_type" id="paymentType" required
                                    onchange="document.getElementById('bankRefSection').style.display=['bank_transfer','cheque'].includes(this.value)?'block':'none'">
                                <option value="">— Select Mode —</option>
                                <option value="cash"          {{ old('payment_type')==='cash'         ?'selected':'' }}>Cash</option>
                                <option value="bank_transfer" {{ old('payment_type')==='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                <option value="cheque"        {{ old('payment_type')==='cheque'       ?'selected':'' }}>Cheque</option>
                                <option value="online"        {{ old('payment_type')==='online'       ?'selected':'' }}>Online</option>
                            </select>
                            @error('payment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Bank Ref --}}
                        <div class="col-md-6" id="bankRefSection" style="display:{{ in_array(old('payment_type'),['bank_transfer','cheque'])?'block':'none' }};">
                            <label class="form-label-custom">Bank / Cheque Ref No.</label>
                            <input type="text" name="bank_ref" class="form-control" placeholder="e.g. CHQ-12345" value="{{ old('bank_ref') }}">
                        </div>

                        {{-- Proof Upload --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Upload Receipt / Proof</label>
                            <input type="file" name="payment_proof" class="form-control" accept="image/*,.pdf">
                            <div style="font-size:10.5px;color:#94a3b8;margin-top:4px;">JPG, PNG or PDF — optional</div>
                        </div>

                        {{-- Remarks --}}
                        <div class="col-12">
                            <label class="form-label-custom">Remarks / Notes</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Optional note...">{{ old('remarks') }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-soft" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="savePaymentBtn" class="btn-navy" onclick="return validatePayment()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ═══ MONTHLY SCHEDULE ════════════════════════════════════════ --}}
@if($hasInstallmentPlan)
@php
    $paidInstNos2 = $mainBooking->payments->where('payment_category','installment')->where('status','paid')->pluck('installment_no')->toArray();
    $bookingBase  = \Carbon\Carbon::parse($mainBooking->booking_date);
    $monthlyAmt   = $mainBooking->monthly_installment ?? 0;
    $scheduleRows = [];
    for ($m = 1; $m <= $totalInstallmentCount; $m++) {
        $dueDate   = (clone $bookingBase)->addMonths($m);
        $isPaid    = in_array($m, $paidInstNos2);
        $isOverdue = !$isPaid && $dueDate->isPast();
        $daysOver  = $isOverdue ? now()->diffInDays($dueDate) : 0;
        $daysUntil = (!$isPaid && !$isOverdue) ? (int) now()->diffInDays($dueDate, false) : null;
        $isDueSoon = $daysUntil !== null && $daysUntil <= 7;
        $scheduleRows[] = compact('m','dueDate','isPaid','isOverdue','daysOver','daysUntil','isDueSoon');
    }
    $overdueCount = collect($scheduleRows)->where('isOverdue',true)->count();
    $dueSoonCount = collect($scheduleRows)->where('isDueSoon',true)->count();
@endphp
<div class="ldg-card" style="margin-top:20px;">
    <div class="ldg-card-head">
        <div>
            <p class="ldg-card-title">Monthly Installment Schedule</p>
            <p class="ldg-card-sub">{{ $totalInstallmentCount }}-month plan · PKR {{ number_format($monthlyAmt) }}/month</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @if($overdueCount > 0)<span style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;">⚠ {{ $overdueCount }} Overdue</span>@endif
            @if($dueSoonCount > 0)<span style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;">⏰ {{ $dueSoonCount }} Due Soon</span>@endif
            <span style="background:#f0fdf4;border:1px solid #86efac;color:#15803d;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;">✓ {{ $paidInstallmentCount }}/{{ $totalInstallmentCount }} Paid</span>
        </div>
    </div>
    <div class="ldg-card-body" style="padding:16px 20px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:8px;">
            @foreach($scheduleRows as $row)
            @php
                if ($row['isPaid'])         { [$mbg,$mborder,$mtxt,$mlbl] = ['#dcfce7','#86efac','#15803d','✓ Paid']; }
                elseif ($row['isOverdue'])  { [$mbg,$mborder,$mtxt,$mlbl] = ['#fef2f2','#fca5a5','#dc2626',$row['daysOver'].'d over']; }
                elseif ($row['isDueSoon'])  { [$mbg,$mborder,$mtxt,$mlbl] = ['#fffbeb','#fde68a','#92400e','In '.$row['daysUntil'].'d']; }
                else                        { [$mbg,$mborder,$mtxt,$mlbl] = ['#f8fafc','#e2e8f0','#94a3b8',$row['dueDate']->format('M y')]; }
            @endphp
            <div style="background:{{ $mbg }};border:2px solid {{ $mborder }};border-radius:10px;padding:8px 4px;text-align:center;">
                <div style="font-size:9px;font-weight:700;color:{{ $mtxt }};text-transform:uppercase;">Month</div>
                <div style="font-size:15px;font-weight:800;color:{{ $mtxt }};line-height:1.2;">{{ $row['m'] }}</div>
                <div style="font-size:9px;color:{{ $mtxt }};margin-top:2px;">{{ $mlbl }}</div>
            </div>
            @endforeach
        </div>
        @php $nextRow = collect($scheduleRows)->first(fn($r) => !$r['isPaid']); @endphp
        @if($nextRow && $remaining > 0)
        <div style="margin-top:16px;background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:12px;padding:12px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <div>
                <div style="font-size:10px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.5px;">{{ $nextRow['isOverdue']?'⚠ Overdue':($nextRow['isDueSoon']?'⏰ Due Very Soon':'Next Payment Due') }}</div>
                <div style="font-size:15px;font-weight:800;color:#1e3a8a;margin-top:2px;">Month {{ $nextRow['m'] }} &nbsp;·&nbsp; PKR {{ number_format($nextInstallmentAmount) }}</div>
                <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $nextRow['dueDate']->format('d M Y') }}</div>
            </div>
            @if(!$readOnly && !$pendingTransfer)
            <button class="btn-navy" data-bs-toggle="modal" data-bs-target="#paymentModal" style="padding:8px 16px;font-size:12px;">Log Installment →</button>
            @endif
        </div>
        @endif
    </div>
</div>
@endif

{{-- ═══ QUARTERLY SCHEDULE ════════════════════════════════════ --}}
@if($hasQuarterlyPlan)
@php
    $bookingBase2 = \Carbon\Carbon::parse($mainBooking->booking_date);
    $scheduleQtr  = [];
    for ($q = 1; $q <= $totalQuarterlyCount; $q++) {
        $dueDate   = (clone $bookingBase2)->addMonths($q * 3);
        $isPaid    = in_array($q, $paidQuarterNos);
        $isOverdue = !$isPaid && $dueDate->isPast();
        $daysOver  = $isOverdue ? now()->diffInDays($dueDate) : 0;
        $daysUntil = (!$isPaid && !$isOverdue) ? (int) now()->diffInDays($dueDate, false) : null;
        $isDueSoon = $daysUntil !== null && $daysUntil <= 7;
        $scheduleQtr[] = compact('q','dueDate','isPaid','isOverdue','daysOver','daysUntil','isDueSoon');
    }
    $overdueQ = collect($scheduleQtr)->where('isOverdue',true)->count();
    $dueSoonQ = collect($scheduleQtr)->where('isDueSoon',true)->count();
@endphp
<div class="ldg-card" style="margin-top:20px;">
    <div class="ldg-card-head">
        <div>
            <p class="ldg-card-title" style="color:#b45309;">Quarterly Instalment Schedule</p>
            <p class="ldg-card-sub">{{ $totalQuarterlyCount }} quarters · PKR {{ number_format($quarterlyAmount) }}/quarter · Every 3 months</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @if($overdueQ > 0)<span style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;">⚠ {{ $overdueQ }} Overdue</span>@endif
            @if($dueSoonQ > 0)<span style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;">⏰ {{ $dueSoonQ }} Due Soon</span>@endif
            <span style="background:#fff7ed;border:1px solid #fed7aa;color:#b45309;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;">✓ {{ $paidQuarterlyCount }}/{{ $totalQuarterlyCount }} Paid</span>
        </div>
    </div>
    <div class="ldg-card-body" style="padding:16px 20px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:10px;">
            @foreach($scheduleQtr as $row)
            @php
                if ($row['isPaid'])         { [$mbg,$mborder,$mtxt,$mlbl] = ['#fff7ed','#fed7aa','#b45309','✓ Paid']; }
                elseif ($row['isOverdue'])  { [$mbg,$mborder,$mtxt,$mlbl] = ['#fef2f2','#fca5a5','#dc2626',$row['daysOver'].'d over']; }
                elseif ($row['isDueSoon'])  { [$mbg,$mborder,$mtxt,$mlbl] = ['#fffbeb','#fde68a','#92400e','In '.$row['daysUntil'].'d']; }
                else                        { [$mbg,$mborder,$mtxt,$mlbl] = ['#f8fafc','#e2e8f0','#94a3b8',$row['dueDate']->format('M y')]; }
            @endphp
            <div style="background:{{ $mbg }};border:2px solid {{ $mborder }};border-radius:10px;padding:10px 6px;text-align:center;">
                <div style="font-size:9px;font-weight:700;color:{{ $mtxt }};text-transform:uppercase;">Quarter</div>
                <div style="font-size:16px;font-weight:800;color:{{ $mtxt }};line-height:1.2;">{{ $row['q'] }}</div>
                <div style="font-size:9px;color:{{ $mtxt }};margin-top:2px;">{{ $mlbl }}</div>
                <div style="font-size:9px;color:#94a3b8;margin-top:1px;">{{ $row['dueDate']->format('d M Y') }}</div>
            </div>
            @endforeach
        </div>
        @php $nextQtr = collect($scheduleQtr)->first(fn($r) => !$r['isPaid']); @endphp
        @if($nextQtr && $remaining > 0)
        <div style="margin-top:16px;background:#fff7ed;border:1.5px solid #fed7aa;border-radius:12px;padding:12px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <div>
                <div style="font-size:10px;font-weight:700;color:#b45309;text-transform:uppercase;letter-spacing:.5px;">{{ $nextQtr['isOverdue']?'⚠ Overdue':($nextQtr['isDueSoon']?'⏰ Due Very Soon':'Next Quarterly Due') }}</div>
                <div style="font-size:15px;font-weight:800;color:#92400e;margin-top:2px;">Quarter {{ $nextQtr['q'] }} &nbsp;·&nbsp; PKR {{ number_format($nextQuarterlyAmount) }}</div>
                <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $nextQtr['dueDate']->format('d M Y') }}</div>
            </div>
            @if(!$readOnly && !$pendingTransfer)
            <button class="btn-navy" data-bs-toggle="modal" data-bs-target="#paymentModal" style="padding:8px 16px;font-size:12px;background:#b45309;border-color:#b45309;">Log Quarterly →</button>
            @endif
        </div>
        @endif
    </div>
</div>
@endif

</div>{{-- /ldg-wrap --}}

@if(!$readOnly)
{{-- ═══ EDIT PAYMENT MODAL ══════════════════════════════════════ --}}
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);border:none;padding:18px 24px;">
                <h5 class="modal-title" style="color:#fff;font-size:14px;font-weight:800;display:flex;align-items:center;gap:8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                    Edit Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPaymentForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding:24px;">
                    <div class="row g-3">

                        {{-- Receipt No --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Receipt / Voucher No.</label>
                            <input type="text" name="receipt_no" id="ep_receipt_no" class="form-control" placeholder="e.g. REC-0001">
                        </div>

                        {{-- Payment Date --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="paid_date" id="ep_paid_date" class="form-control" required>
                        </div>

                        {{-- Category --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Payment Category <span class="text-danger">*</span></label>
                            <select name="payment_category" id="ep_payment_category" class="form-select" required>
                                <option value="down_payment">Down Payment</option>
                                <option value="installment">Monthly Installment</option>
                                <option value="quarterly_installment">Quarterly Installment</option>
                                <option value="plot_balance">Plot Balance (Lump Sum)</option>
                                <option value="processing_fee">Processing Fee</option>
                                <option value="fine">Fine / Penalty</option>
                                <option value="security_fee">Security Fee</option>
                                <option value="maintenance_fee">Maintenance Fee</option>
                                <option value="bifurcation_fee">Bifurcation Fee</option>
                                <option value="others">Others</option>
                            </select>
                        </div>

                        {{-- Payment Type --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_type" id="ep_payment_type" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="online">Online</option>
                            </select>
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Amount Paid (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="amount_paid" id="ep_amount_paid" class="form-control" step="any" required>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label-custom">Status <span class="text-danger">*</span></label>
                            <select name="status" id="ep_status" class="form-select" required>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>

                        {{-- Installment No --}}
                        <div class="col-md-4">
                            <label class="form-label-custom">Installment # <small style="color:#94a3b8;">if monthly</small></label>
                            <input type="number" name="installment_no" id="ep_installment_no" class="form-control" min="1" placeholder="e.g. 3">
                        </div>

                        {{-- Quarterly No --}}
                        <div class="col-md-4">
                            <label class="form-label-custom">Quarter # <small style="color:#94a3b8;">if quarterly</small></label>
                            <input type="number" name="quarterly_no" id="ep_quarterly_no" class="form-control" min="1" placeholder="e.g. 2">
                        </div>

                        {{-- Bank Ref --}}
                        <div class="col-md-4">
                            <label class="form-label-custom">Bank Ref / Cheque No.</label>
                            <input type="text" name="bank_ref" id="ep_bank_ref" class="form-control" placeholder="optional">
                        </div>

                        {{-- Remarks --}}
                        <div class="col-12">
                            <label class="form-label-custom">Remarks</label>
                            <textarea name="remarks" id="ep_remarks" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:14px 24px;gap:10px;">
                    <button type="button" class="btn-soft" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-navy">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-edit-payment {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    color: #1d4ed8;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    padding: 3px 8px;
    cursor: pointer;
    margin-left: 4px;
    transition: background .12s;
}
.btn-edit-payment:hover { background: #dbeafe; }
</style>

<script>
function openEditPayment(data) {
    document.getElementById('ep_receipt_no').value       = data.receipt_no       || '';
    document.getElementById('ep_paid_date').value        = data.paid_date        || '';
    document.getElementById('ep_payment_category').value = data.payment_category || '';
    document.getElementById('ep_payment_type').value     = data.payment_type     || '';
    document.getElementById('ep_amount_paid').value      = data.amount_paid      || '';
    document.getElementById('ep_installment_no').value   = data.installment_no   || '';
    document.getElementById('ep_quarterly_no').value     = data.quarterly_no     || '';
    document.getElementById('ep_bank_ref').value         = data.bank_ref         || '';
    document.getElementById('ep_remarks').value          = data.remarks          || '';
    document.getElementById('ep_status').value           = data.status           || 'paid';

    document.getElementById('editPaymentForm').action =
        '{{ url("plot/payment") }}/' + data.id + '/update';

    new bootstrap.Modal(document.getElementById('editPaymentModal')).show();
}
</script>
@endif

@endsection
<div class="hold-modal-backdrop" id="ledgerHoldBackdrop"
     style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9998;display:none;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="font-size:15px;font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:8px;">
            <span style="background:#f59e0b;width:28px;height:28px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
            </span>
            Hold Booking
        </div>
        <p style="font-size:12px;color:#64748b;margin-bottom:16px;">
            {{ $mainBooking->customer_booking_id }} — {{ $mainBooking->customer->name }}<br>
            Payments will be blocked until the hold is released.
        </p>
        <form method="POST" action="{{ route('booking.hold', $mainBooking->id) }}" id="ledgerHoldForm">
            @csrf
            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">
                Reason for hold <span style="color:#dc2626;">*</span>
            </label>
            <textarea name="remarks" id="ledgerHoldRemarks"
                style="width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 13px;font-size:13px;font-family:inherit;resize:vertical;min-height:80px;outline:none;"
                placeholder="e.g. Dispute between parties, cheque bounced…"
                required></textarea>
            <div style="display:flex;gap:8px;margin-top:14px;">
                <button type="submit"
                    style="background:#f59e0b;color:#fff;border:none;border-radius:9px;padding:10px 22px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;">
                    Confirm Hold
                </button>
                <button type="button" onclick="closeLedgerHoldModal()"
                    style="background:#f1f5f9;color:#64748b;border:none;border-radius:9px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
{{-- ═══ CANCEL MODAL ═══════════════════════════════════════════ --}}
@can('booking_cancel')
<div id="cancelModalBackdrop"
     style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9998;display:none;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="font-size:15px;font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:8px;">
            <span style="background:#dc2626;width:28px;height:28px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </span>
            Cancel Booking
        </div>
        <p style="font-size:12px;color:#64748b;margin-bottom:16px;">
            {{ $mainBooking->customer_booking_id }} — {{ $mainBooking->customer->name }}<br>
            <span style="color:#dc2626;font-weight:700;">This action is permanent. The plot will be returned to available inventory.</span>
        </p>
        <form method="POST" action="{{ route('booking.cancel', $mainBooking->id) }}" id="cancelBookingForm">
            @csrf
            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">
                Reason for cancellation <span style="color:#dc2626;">*</span>
            </label>
            <textarea name="cancellation_reason" id="cancelReasonText"
                style="width:100%;border:1.5px solid #fecaca;border-radius:9px;padding:10px 13px;font-size:13px;font-family:inherit;resize:vertical;min-height:80px;outline:none;"
                placeholder="e.g. Customer request, payment default, agreement dispute…"
                required></textarea>
            <div style="margin-top:12px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">
                    Refund amount <span style="color:#94a3b8;font-weight:400;">(optional)</span>
                </label>
                <input type="number" name="cancellation_refund" id="cancelRefundAmt"
                    min="0" step="0.01"
                    style="width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 13px;font-size:13px;font-family:inherit;outline:none;"
                    placeholder="0.00">
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                    Total collected: PKR {{ number_format($mainBooking->payments->where('status','paid')->sum('amount')) }}
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:16px;">
                <button type="submit"
                    onclick="return confirm('Are you sure you want to cancel this booking? This cannot be undone.')"
                    style="background:#dc2626;color:#fff;border:none;border-radius:9px;padding:10px 22px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;">
                    Confirm Cancellation
                </button>
                <button type="button" onclick="closeCancelModal()"
                    style="background:#f1f5f9;color:#64748b;border:none;border-radius:9px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ═══ CHANGE INSTALLMENT PLAN MODAL ══════════════════════════ --}}
@if($hasInstallmentPlan && in_array($mainBooking->status, ['active','pending']))
@can('booking_plan_change')
<div id="ldgPlanBackdrop"
     style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9998;display:none;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto;">
        <div style="font-size:15px;font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:8px;">
            <span style="background:#f59e0b;width:28px;height:28px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
            </span>
            Change Installment Plan
        </div>
        <p style="font-size:12px;color:#64748b;margin-bottom:16px;">
            {{ $mainBooking->customer_booking_id }} — {{ $mainBooking->customer->name }}<br>
            Adjust the repayment schedule — increase or decrease the total installments.
        </p>

        {{-- Current plan summary --}}
        <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:12px;padding:14px 16px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Current Total</div>
                <div style="font-size:16px;font-weight:800;color:#1e3a8a;">{{ $totalInstallmentCount }} months</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Installments Paid</div>
                <div style="font-size:16px;font-weight:800;color:#16a34a;">{{ $paidInstallmentCount }}</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Current Monthly</div>
                <div style="font-size:15px;font-weight:800;color:#1e3a8a;">PKR {{ number_format($monthlyInstallment) }}</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">
                    {{ $remainingQuarterlyCount > 0 ? 'Monthly Portion' : 'Outstanding Balance' }}
                </div>
                @php $ldgMonthlyPortion = max(0, $remaining - $remainingQuarterlyCount * $quarterlyAmount); @endphp
                <div style="font-size:15px;font-weight:800;color:#dc2626;">PKR {{ number_format($ldgMonthlyPortion) }}</div>
                @if($remainingQuarterlyCount > 0)
                <div style="font-size:10px;color:#b45309;margin-top:2px;">
                    Total remaining: PKR {{ number_format($remaining) }}<br>
                    (Quarterly covers {{ $remainingQuarterlyCount }} × PKR {{ number_format($quarterlyAmount) }} = PKR {{ number_format($remainingQuarterlyCount * $quarterlyAmount) }})
                </div>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('booking.change.plan', $mainBooking->id) }}" id="ldgPlanForm">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                    New Total Installments <span style="color:#dc2626;">*</span>
                    <span style="font-weight:400;color:#94a3b8;">(min: {{ $paidInstallmentCount }} already paid)</span>
                </label>
                <input type="number" name="new_total_installments" id="ldgNewTotal"
                    min="{{ max(1, $paidInstallmentCount) }}" step="1"
                    style="width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 13px;font-size:14px;font-weight:700;font-family:inherit;outline:none;"
                    placeholder="{{ $totalInstallmentCount }}"
                    value="{{ $totalInstallmentCount }}"
                    oninput="ldgPreviewNewAmount()" required>
            </div>

            {{-- Live preview --}}
            <div id="ldgPlanPreview" style="display:none;background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:10px;padding:12px 16px;margin-bottom:14px;">
                <div style="font-size:10px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">New Plan Preview</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <div style="font-size:10px;color:#64748b;">Remaining Balance</div>
                        <div style="font-size:14px;font-weight:800;color:#1e3a8a;">PKR {{ number_format($remaining) }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:#64748b;">Remaining Months</div>
                        <div id="ldgPreviewMonths" style="font-size:14px;font-weight:800;color:#1e3a8a;">—</div>
                    </div>
                    <div style="grid-column:1/-1;">
                        <div style="font-size:10px;color:#64748b;">New Monthly Amount</div>
                        <div id="ldgPreviewAmt" style="font-size:18px;font-weight:800;color:#16a34a;">—</div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                    Reason for Change <span style="color:#dc2626;">*</span>
                </label>
                <textarea name="reason" id="ldgPlanReason"
                    style="width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 13px;font-size:13px;font-family:inherit;resize:vertical;min-height:70px;outline:none;"
                    placeholder="e.g. Customer requested extended timeline due to financial constraints…"
                    required></textarea>
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit"
                    style="background:#f59e0b;color:#fff;border:none;border-radius:9px;padding:10px 22px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;">
                    Confirm Change
                </button>
                <button type="button" onclick="closeLedgerPlanModal()"
                    style="background:#f1f5f9;color:#64748b;border:none;border-radius:9px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
@endif

{{-- ═══ LUMP SUM SETTLEMENT MODAL ════════════════════════════ --}}
@if($remaining > 0 && in_array($mainBooking->status, ['active','pending']))
<div id="lumpSumBackdrop"
     style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9998;display:none;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="font-size:15px;font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:8px;">
            <span style="background:#15803d;width:28px;height:28px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
            </span>
            Pay Full Balance — Lump Sum
        </div>
        <p style="font-size:12px;color:#64748b;margin-bottom:16px;">
            {{ $mainBooking->customer_booking_id }} — {{ $mainBooking->customer->name }}<br>
            Settle the entire remaining balance in one payment. Booking will be marked as completed.
        </p>

        {{-- Balance breakdown --}}
        <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:12px;padding:14px 16px;margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                <span style="font-size:12px;color:#64748b;">Full Remaining Balance</span>
                <span style="font-size:15px;font-weight:800;color:#dc2626;">PKR {{ number_format($remaining) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;" id="lsDiscountRow" style="display:none;">
                <span style="font-size:12px;color:#64748b;">Settlement Discount</span>
                <span id="lsDiscountDisplay" style="font-size:13px;font-weight:700;color:#16a34a;">— PKR 0</span>
            </div>
            <div style="border-top:1px solid #e2e8f0;padding-top:8px;margin-top:8px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:#0f172a;">Amount to Pay</span>
                <span id="lsFinalAmount" style="font-size:17px;font-weight:800;color:#15803d;">PKR {{ number_format($remaining) }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('booking.lump.sum', $mainBooking->id) }}" id="lumpSumForm">
            @csrf

            {{-- Settlement discount --}}
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                    Settlement Discount
                    <span style="font-weight:400;color:#94a3b8;">(optional — leave blank for no discount)</span>
                </label>
                <div style="display:flex;gap:8px;">
                    <div style="flex:1;position:relative;">
                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;color:#94a3b8;font-weight:700;">PKR</span>
                        <input type="number" name="discount_amount" id="lsDiscountAmt"
                            min="0" step="0.01" max="{{ $remaining - 1 }}"
                            style="width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 13px 10px 46px;font-size:13px;font-family:inherit;outline:none;"
                            placeholder="0"
                            oninput="lsCalc()">
                    </div>
                    <div style="position:relative;width:90px;">
                        <input type="number" id="lsDiscountPct"
                            min="0" max="99" step="0.1"
                            style="width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 30px 10px 13px;font-size:13px;font-family:inherit;outline:none;"
                            placeholder="0"
                            oninput="lsCalcFromPct()">
                        <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:12px;color:#94a3b8;font-weight:700;">%</span>
                    </div>
                </div>
                <div style="font-size:10.5px;color:#94a3b8;margin-top:4px;">Enter amount or percentage — both fields stay in sync.</div>
            </div>

            {{-- Payment mode --}}
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                    Payment Mode <span style="color:#dc2626;">*</span>
                </label>
                <select name="payment_type" required
                    style="width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 13px;font-size:13px;font-family:inherit;outline:none;background:#fff;">
                    <option value="">— Select Mode —</option>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cheque">Cheque</option>
                    <option value="online">Online</option>
                </select>
            </div>

            {{-- Date --}}
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                    Payment Date <span style="color:#dc2626;">*</span>
                </label>
                <input type="date" name="paid_date" required value="{{ date('Y-m-d') }}"
                    style="width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 13px;font-size:13px;font-family:inherit;outline:none;">
            </div>

            {{-- Remarks --}}
            <div style="margin-bottom:16px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Remarks / Notes</label>
                <textarea name="remarks"
                    style="width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 13px;font-size:13px;font-family:inherit;resize:vertical;min-height:60px;outline:none;"
                    placeholder="Optional…"></textarea>
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit"
                    onclick="return confirmLumpSum()"
                    style="background:#15803d;color:#fff;border:none;border-radius:9px;padding:10px 22px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;">
                    Confirm Payment &amp; Complete
                </button>
                <button type="button" onclick="closeLumpSumModal()"
                    style="background:#f1f5f9;color:#64748b;border:none;border-radius:9px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')

@if(AppConfig::qrEnabled())
<script>
const qrEl = document.getElementById('ledgerQR');
if (qrEl) {
    new QRCode(qrEl, {
        text: '{{ route("ledger.view",$mainBooking->id) }}',
        width: 110, height: 110,
        colorDark: '#0f172a', colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
}
</script>
@endif
<script>
const MONTHLY_INSTALLMENT    = {{ $nextInstallmentAmount ?? 0 }};
const REMAINING_BALANCE      = {{ $remaining }};
const IS_FULLY_PAID          = {{ $isFullyPaid ? 'true' : 'false' }};
const BOOKING_DATE           = '{{ $mainBooking->booking_date }}';
const PAID_INSTALLMENT_COUNT = {{ $paidInstallmentCount }};
const TOTAL_INSTALLMENTS     = {{ $totalInstallmentCount }};
const HAS_INSTALLMENT_PLAN   = {{ $hasInstallmentPlan ? 'true' : 'false' }};
const NEXT_QUARTERLY_AMOUNT  = {{ $nextQuarterlyAmount ?? 0 }};
const PAID_QUARTERLY_COUNT   = {{ $paidQuarterlyCount ?? 0 }};
const TOTAL_QUARTERLY        = {{ $totalQuarterlyCount ?? 0 }};
const HAS_QUARTERLY_PLAN     = {{ ($hasQuarterlyPlan ?? false) ? 'true' : 'false' }};

// ── Authoritative hidden field writers ────────────────────────────
// Monthly installment → installment_no
function setFinalInstallmentNo(val) {
    const f = document.getElementById('finalInstallmentNo');
    if (f) f.value = (val !== null && val !== undefined) ? val : '';
}
// Quarterly → quarterly_no (different column!)
function setFinalQuarterlyNo(val) {
    const f = document.getElementById('finalQuarterlyNo');
    if (f) f.value = (val !== null && val !== undefined) ? val : '';
}

function onCategoryChange() {
    const sel       = document.getElementById('paymentCategory');
    const opt       = sel.options[sel.selectedIndex];
    const cat       = sel.value;
    const mSection  = document.getElementById('installmentSection');
    const qSection  = document.getElementById('quarterlySection');
    const amtFld    = document.getElementById('amountPaid');
    const hint      = document.getElementById('amountHint');
    const catHint   = document.getElementById('categoryHint');
    const isFullFld = document.getElementById('isFullPayment');
    const dueDate   = document.getElementById('dueDate');
    const dueHint   = document.getElementById('dueDateHint');

    if (mSection) mSection.style.display = 'none';
    if (qSection) qSection.style.display = 'none';

    amtFld.readOnly = false;
    amtFld.style.background = amtFld.style.borderColor = '';
    if (isFullFld) isFullFld.value = '0';

    // Clear both hidden fields
    setFinalInstallmentNo('');
    setFinalQuarterlyNo('');

    if (dueDate) dueDate.value = '';
    if (dueHint) { dueHint.textContent = 'Auto-filled for installments'; dueHint.style.color = '#94a3b8'; }

    // ── Monthly installment ──────────────────────────────────────
    if (cat === 'installment') {
        if (!HAS_INSTALLMENT_PLAN || PAID_INSTALLMENT_COUNT >= TOTAL_INSTALLMENTS) {
            alert('All monthly installments have already been paid.');
            sel.value = '';
            return;
        }
        if (mSection) mSection.style.display = 'block';
        selectInstallmentMonth(PAID_INSTALLMENT_COUNT + 1);
        return;
    }

    // ── Quarterly installment ────────────────────────────────────
    if (cat === 'quarterly_installment') {
        if (!HAS_QUARTERLY_PLAN || PAID_QUARTERLY_COUNT >= TOTAL_QUARTERLY) {
            alert('All quarterly installments have already been paid.');
            sel.value = '';
            return;
        }
        if (qSection) qSection.style.display = 'block';
        selectQuarter(PAID_QUARTERLY_COUNT + 1);
        return;
    }

    // ── Plot balance ─────────────────────────────────────────────
    if (cat === 'plot_balance') {
        const isFull = opt ? (opt.dataset.isFull === '1') : false;
        if (isFullFld) isFullFld.value = isFull ? '1' : '0';
        amtFld.value    = REMAINING_BALANCE;
        amtFld.readOnly = isFull;
        amtFld.style.background  = isFull ? '#f0fdf4' : '';
        amtFld.style.borderColor = isFull ? '#86efac' : '';
        hint.innerHTML = isFull
            ? `<span style="color:#16a34a;font-weight:700;">✓ Locked to full balance: PKR ${Number(REMAINING_BALANCE).toLocaleString()}</span>`
            : `<span style="color:#1d4ed8;font-weight:700;">Pre-filled — adjust to partial amount</span>`;
        return;
    }

    // ── Other categories ─────────────────────────────────────────
    if (opt && opt.dataset.amount) {
        const autoAmt = parseFloat(opt.dataset.amount);
        amtFld.value = autoAmt;
        hint.innerHTML = `<span style="color:#1d4ed8;font-weight:700;">Auto-filled: PKR ${autoAmt.toLocaleString()}</span> | Remaining: PKR ${Number(REMAINING_BALANCE).toLocaleString()}`;
    } else {
        amtFld.value   = '';
        hint.innerHTML = `Remaining: PKR ${Number(REMAINING_BALANCE).toLocaleString()}`;
    }
    if (catHint) catHint.textContent = '';
}

// ── Monthly month selector ────────────────────────────────────────
function selectInstallmentMonth(month) {
    setFinalInstallmentNo(month);    // → installment_no in DB
    setFinalQuarterlyNo('');         // clear quarterly

    const labelEl = document.getElementById('selectedMonthLabel');
    const amtFld  = document.getElementById('amountPaid');
    if (labelEl) labelEl.textContent = 'Month ' + month;
    if (amtFld)  amtFld.value = MONTHLY_INSTALLMENT;

    document.querySelectorAll('.inst-month-btn').forEach(btn => {
        if (btn.classList.contains('inst-paid')) return;
        btn.classList.remove('inst-selected','inst-next','inst-future');
        btn.classList.add(parseInt(btn.dataset.month) === month ? 'inst-selected' : 'inst-future');
    });

    // Calculate due date: booking_date + month number (in months)
    try {
        const base = new Date(BOOKING_DATE);
        base.setMonth(base.getMonth() + month);
        const dd = document.getElementById('dueDate');
        if (dd) dd.value = `${base.getFullYear()}-${String(base.getMonth()+1).padStart(2,'0')}-${String(base.getDate()).padStart(2,'0')}`;
        const dh = document.getElementById('dueDateHint');
        if (dh) { dh.textContent = `Due date for month ${month}`; dh.style.color = '#1d4ed8'; }
    } catch(e) {}
}

// ── Quarterly selector ────────────────────────────────────────────
function selectQuarter(quarter) {
    setFinalQuarterlyNo(quarter);    // → quarterly_no in DB  ← KEY FIX
    setFinalInstallmentNo('');       // clear monthly

    const labelEl = document.getElementById('selectedQuarterLabel');
    const amtFld  = document.getElementById('amountPaid');
    if (labelEl) labelEl.textContent = 'Quarter ' + quarter;
    if (amtFld)  amtFld.value = NEXT_QUARTERLY_AMOUNT;

    document.querySelectorAll('.qtr-btn').forEach(btn => {
        if (btn.classList.contains('inst-paid')) return;
        btn.classList.remove('inst-selected','inst-next','inst-future');
        btn.classList.add(parseInt(btn.dataset.quarter) === quarter ? 'inst-selected' : 'inst-future');
    });

    // Calculate due date: booking_date + (quarter × 3 months)
    try {
        const base = new Date(BOOKING_DATE);
        base.setMonth(base.getMonth() + (quarter * 3));
        const dd = document.getElementById('dueDate');
        if (dd) dd.value = `${base.getFullYear()}-${String(base.getMonth()+1).padStart(2,'0')}-${String(base.getDate()).padStart(2,'0')}`;
        const dh = document.getElementById('dueDateHint');
        if (dh) { dh.textContent = `Due date for quarter ${quarter} (${quarter * 3} months from booking date)`; dh.style.color = '#b45309'; }
    } catch(e) {}
}

// ── Validation before submit ──────────────────────────────────────
function validatePayment() {
    const sel    = document.getElementById('paymentCategory');
    const cat    = sel ? sel.value : '';
    const amount = parseFloat(document.getElementById('amountPaid')?.value || '0');
    const instNo = document.getElementById('finalInstallmentNo')?.value;
    const qtrNo  = document.getElementById('finalQuarterlyNo')?.value;

    if (!cat)                   { alert('Please select a payment category.'); return false; }
    if (!amount || amount <= 0) { alert('Please enter a valid amount.'); return false; }

    if (cat === 'installment' && (!instNo || instNo === '')) {
        alert('Please select an installment month from the grid.');
        return false;
    }
    if (cat === 'quarterly_installment' && (!qtrNo || qtrNo === '')) {
        alert('Please select a quarter from the grid.');
        return false;
    }

    const feeCats = ['fine','plot_balance','development_fee','registry_fee','security_fee','maintenance_fee','bifurcation_fee','others'];
    if (amount > REMAINING_BALANCE && !feeCats.includes(cat)) {
        if (!confirm(`⚠️ Amount PKR ${amount.toLocaleString()} exceeds remaining balance PKR ${Number(REMAINING_BALANCE).toLocaleString()}.\n\nThis will mark the booking as overpaid. Continue?`)) {
            return false;
        }
    }

    // Prevent double-submission — defer disable so it fires AFTER form submission is dispatched
    const btn = document.getElementById('savePaymentBtn');
    if (btn) {
        setTimeout(function () {
            btn.disabled = true;
            btn.textContent = 'Saving…';
        }, 0);
    }
    return true;
}

// ── Modal lifecycle ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('paymentModal');
    if (!modal) return;

    // Auto-select the most relevant category on open
    modal.addEventListener('show.bs.modal', function () {
        const sel = document.getElementById('paymentCategory');
        if (!sel || sel.value) return;

        const downLeft = {{ max(0, ($downDue ?? 0) - ($downPaid ?? 0)) }};

        // Installment-only booking: jump straight to the month grid
        if (HAS_INSTALLMENT_PLAN
            && PAID_INSTALLMENT_COUNT < TOTAL_INSTALLMENTS
            && downLeft === 0
            && !HAS_QUARTERLY_PLAN) {
            const instOpt = sel.querySelector('option[value="installment"]');
            if (instOpt) { sel.value = 'installment'; onCategoryChange(); }
            return;
        }

        // Quarterly-only booking: auto-select quarterly
        if (HAS_QUARTERLY_PLAN
            && PAID_QUARTERLY_COUNT < TOTAL_QUARTERLY
            && downLeft === 0
            && !HAS_INSTALLMENT_PLAN) {
            const qOpt = sel.querySelector('option[value="quarterly_installment"]');
            if (qOpt) { sel.value = 'quarterly_installment'; onCategoryChange(); }
            return;
        }

        // Cash / no-plan booking: auto-select full balance
        if (!HAS_INSTALLMENT_PLAN && !HAS_QUARTERLY_PLAN && REMAINING_BALANCE > 0) {
            const full = document.getElementById('fullBalanceOption');
            if (full) { sel.value = full.value; onCategoryChange(); }
        }
    });


    // Reset on close
    modal.addEventListener('hidden.bs.modal', function () {
        const amtFld = document.getElementById('amountPaid');
        if (amtFld) { amtFld.readOnly = false; amtFld.style.background = ''; amtFld.style.borderColor = ''; }
        const isFullFld = document.getElementById('isFullPayment');
        if (isFullFld) isFullFld.value = '0';
        setFinalInstallmentNo('');
        setFinalQuarterlyNo('');
    });
});
function openLedgerHoldModal() {
    document.getElementById('ledgerHoldRemarks').value = '';
    const bd = document.getElementById('ledgerHoldBackdrop');
    bd.style.display = 'flex';
}
function closeLedgerHoldModal() {
    document.getElementById('ledgerHoldBackdrop').style.display = 'none';
}
document.getElementById('ledgerHoldBackdrop').addEventListener('click', function(e) {
    if (e.target === this) closeLedgerHoldModal();
});

function openCancelModal() {
    const el = document.getElementById('cancelReasonText');
    if (el) el.value = '';
    const rf = document.getElementById('cancelRefundAmt');
    if (rf) rf.value = '';
    const bd = document.getElementById('cancelModalBackdrop');
    if (bd) { bd.style.display = 'flex'; }
}
function closeCancelModal() {
    const bd = document.getElementById('cancelModalBackdrop');
    if (bd) bd.style.display = 'none';
}
const cancelBd = document.getElementById('cancelModalBackdrop');
if (cancelBd) {
    cancelBd.addEventListener('click', function(e) {
        if (e.target === this) closeCancelModal();
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeLedgerHoldModal(); closeCancelModal(); closeLedgerPlanModal(); closeLumpSumModal(); }
});

// ─── Change Plan modal (ledger) ───────────────────────────────────────────
const _LDG_REMAINING          = {{ $remaining }};
const _LDG_INST_PAID          = {{ $paidInstallmentCount }};
const _LDG_INST_TOTAL         = {{ $totalInstallmentCount }};
const _LDG_FUTURE_QUARTERLY   = {{ max(0, ($totalQuarterlyCount - $paidQuarterlyCount)) * $quarterlyAmount }};

function openLedgerPlanModal() {
    // Pre-fill with current plan so user can see and adjust up or down
    document.getElementById('ldgNewTotal').value   = _LDG_INST_TOTAL;
    document.getElementById('ldgPlanReason').value = '';
    ldgPreviewNewAmount();
    const bd = document.getElementById('ldgPlanBackdrop');
    if (bd) bd.style.display = 'flex';
}
function closeLedgerPlanModal() {
    const bd = document.getElementById('ldgPlanBackdrop');
    if (bd) bd.style.display = 'none';
}
function ldgPreviewNewAmount() {
    const newTotal = parseInt(document.getElementById('ldgNewTotal').value || '0');
    const preview  = document.getElementById('ldgPlanPreview');
    if (!newTotal || newTotal < _LDG_INST_PAID) { if (preview) preview.style.display = 'none'; return; }
    const remainingMonths = newTotal - _LDG_INST_PAID;
    // Monthly portion excludes future quarterly payments
    const monthlyPortion  = Math.max(0, _LDG_REMAINING - _LDG_FUTURE_QUARTERLY);
    const newMonthly      = remainingMonths > 0 ? Math.round((monthlyPortion / remainingMonths) * 100) / 100 : 0;
    const mEl = document.getElementById('ldgPreviewMonths');
    const aEl = document.getElementById('ldgPreviewAmt');
    if (mEl) mEl.textContent = remainingMonths + ' months';
    if (aEl) aEl.textContent = 'PKR ' + newMonthly.toLocaleString(undefined, {minimumFractionDigits:0,maximumFractionDigits:2});
    if (preview) preview.style.display = 'block';
}
const ldgPlanBd = document.getElementById('ldgPlanBackdrop');
if (ldgPlanBd) ldgPlanBd.addEventListener('click', function(e) { if (e.target === this) closeLedgerPlanModal(); });

// ─── Lump Sum Settlement modal ─────────────────────────────────────────────
const _LS_REMAINING = {{ $remaining }};

function openLumpSumModal() {
    document.getElementById('lsDiscountAmt').value = '';
    document.getElementById('lsDiscountPct').value = '';
    lsCalc();
    const bd = document.getElementById('lumpSumBackdrop');
    if (bd) bd.style.display = 'flex';
}
function closeLumpSumModal() {
    const bd = document.getElementById('lumpSumBackdrop');
    if (bd) bd.style.display = 'none';
}
function lsCalc() {
    const disc    = Math.max(0, parseFloat(document.getElementById('lsDiscountAmt').value || '0'));
    const pctEl   = document.getElementById('lsDiscountPct');
    if (pctEl && document.activeElement !== pctEl) {
        pctEl.value = _LS_REMAINING > 0 ? (disc / _LS_REMAINING * 100).toFixed(1) : '0';
    }
    const final   = Math.max(0, _LS_REMAINING - disc);
    const discRow = document.getElementById('lsDiscountRow');
    const discDis = document.getElementById('lsDiscountDisplay');
    const finEl   = document.getElementById('lsFinalAmount');
    if (discRow) discRow.style.display = disc > 0 ? 'flex' : 'none';
    if (discDis) discDis.textContent = '— PKR ' + disc.toLocaleString(undefined, {minimumFractionDigits:0,maximumFractionDigits:2});
    if (finEl)   finEl.textContent   = 'PKR ' + final.toLocaleString(undefined, {minimumFractionDigits:0,maximumFractionDigits:2});
}
function lsCalcFromPct() {
    const pct   = Math.max(0, Math.min(99, parseFloat(document.getElementById('lsDiscountPct').value || '0')));
    const disc  = Math.round((_LS_REMAINING * pct / 100) * 100) / 100;
    const amtEl = document.getElementById('lsDiscountAmt');
    if (amtEl) amtEl.value = disc > 0 ? disc : '';
    lsCalc();
}
function confirmLumpSum() {
    const disc  = parseFloat(document.getElementById('lsDiscountAmt').value || '0');
    const final = Math.max(0, _LS_REMAINING - disc);
    return confirm(
        'Confirm lump sum payment?\n\n' +
        'Remaining balance:  PKR ' + _LS_REMAINING.toLocaleString() + '\n' +
        (disc > 0 ? 'Settlement discount: PKR ' + disc.toLocaleString() + '\n' : '') +
        'Amount to pay:      PKR ' + final.toLocaleString() + '\n\n' +
        'This will CLOSE the booking as COMPLETED.'
    );
}
const lumpSumBd = document.getElementById('lumpSumBackdrop');
if (lumpSumBd) lumpSumBd.addEventListener('click', function(e) { if (e.target === this) closeLumpSumModal(); });

</script>
@endpush
