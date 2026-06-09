@extends('layouts.index')

@section('content')
@push('styles')
<style>

</style>
@endpush

<div class="tr-wrap">

{{-- ── HEADER ── --}}
<div class="tr-header">
    <div>
        <p class="tr-header-title">Plot Transfers</p>
        <p class="tr-header-sub">Ownership, Swap, Partial & Internal transfers — Zamar Valley</p>
    </div>
    <div class="d-flex gap-2 flex-wrap" style="position:relative;z-index:1;">
        <a href="{{ route('transfers.search') }}" class="btn-navy">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Transfer
        </a>
    </div>
</div>

{{-- ── FLASH ── --}}
@if(session('success'))
<div class="alert-flash alert-flash-success">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:18px;">&times;</button>
</div>
@endif
@if(session('dues_warning'))
@php $dw = session('dues_warning'); @endphp
<div style="background:#fef2f2; border:1.5px solid #fecaca; border-radius:12px; padding:14px 18px; margin-bottom:18px; display:flex; gap:12px; align-items:flex-start;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#dc2626" style="width:20px;height:20px;flex-shrink:0;margin-top:1px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
    </svg>
    <div style="flex:1;">
        <div style="font-size:13px; font-weight:800; color:#dc2626; margin-bottom:3px;">Transfer Blocked — Outstanding Dues</div>
        <div style="font-size:12px; color:#991b1b; line-height:1.6;">
            Outstanding: <strong>PKR {{ number_format($dw['outstanding']) }}</strong>
            &nbsp;·&nbsp; Paid: PKR {{ number_format($dw['total_paid']) }} of PKR {{ number_format($dw['total_price']) }}
            @if(($dw['pending_inst'] ?? 0) > 0)
            &nbsp;·&nbsp; <strong>{{ $dw['pending_inst'] }} installment(s)</strong> pending
            @endif
            <br>Clear all dues before this transfer can be approved.
        </div>
    </div>
    <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-size:20px;color:#dc2626;line-height:1;padding:0;">×</button>
</div>
@endif

@if(session('error'))
<div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:12px 16px; margin-bottom:16px; font-size:13px; font-weight:600; color:#dc2626; display:flex; align-items:center; gap:10px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- ── STAT CARDS ── --}}
<div class="stat-grid">
    <div class="stat-card" style="--sc:#1e3a8a;--sb:#eff6ff;">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#1e3a8a"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg></div>
        <div><div class="stat-label">Total Transfers</div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-sub">all time</div></div>
    </div>
    <div class="stat-card" style="--sc:#ca8a04;--sb:#fef9c3;">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#ca8a04"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><div class="stat-label">Pending</div><div class="stat-value">{{ $stats['pending'] }}</div><div class="stat-sub">awaiting approval</div></div>
    </div>
    <div class="stat-card" style="--sc:#16a34a;--sb:#dcfce7;">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#16a34a"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><div class="stat-label">Completed</div><div class="stat-value">{{ $stats['completed'] }}</div><div class="stat-sub">successfully done</div></div>
    </div>
    <div class="stat-card" style="--sc:#7c3aed;--sb:#fdf4ff;">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#7c3aed"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg></div>
        <div><div class="stat-label">Transfer Fees</div><div class="stat-value" style="font-size:16px;">PKR {{ number_format($stats['fees_collected']) }}</div><div class="stat-sub">collected</div></div>
    </div>
    <div class="stat-card" style="--sc:#dc2626;--sb:#fef2f2;">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#dc2626"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><div class="stat-label">Chain Remaining</div><div class="stat-value" style="font-size:16px;">PKR {{ number_format($stats['chain_remaining']) }}</div><div class="stat-sub">owed by transfer holders</div></div>
    </div>
</div>

{{-- ── FILTER BAR ── --}}
<form method="GET" action="{{ route('index.transfer') }}">
    <div class="filter-bar">
        <input type="text" name="search" placeholder="Search deed no, customer, plot…" value="{{ request('search') }}">
        <select name="type">
            <option value="">All Types</option>
            <option value="ownership" {{ request('type')=='ownership'?'selected':'' }}>Ownership Transfer</option>
            <option value="swap"      {{ request('type')=='swap'     ?'selected':'' }}>Plot Swap</option>
            <option value="partial"   {{ request('type')=='partial'  ?'selected':'' }}>Partial Transfer</option>
            <option value="internal"  {{ request('type')=='internal' ?'selected':'' }}>Internal Transfer</option>
        </select>
        <select name="status">
            <option value="">All Status</option>
            <option value="pending"   {{ request('status')=='pending'  ?'selected':'' }}>Pending</option>
            <option value="approved"  {{ request('status')=='approved' ?'selected':'' }}>Approved</option>
            <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
            <option value="rejected"  {{ request('status')=='rejected' ?'selected':'' }}>Rejected</option>
        </select>
        <button type="submit" class="btn-filter">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            Search
        </button>
        <a href="{{ route('index.transfer') }}" class="btn-reset">Reset</a>
    </div>
</form>


{{-- ── TRANSFERS TABLE ── --}}
<div class="panel">
    <div class="panel-head">
        <div>
            <p class="panel-title">Transfer Records</p>
            <p class="panel-sub">{{ $transfers->total() }} records found</p>
        </div>
    </div>
    <div class="tr-table-wrap">
    <table class="tr-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Deed No.</th>
                    <th>Type</th>
                    <th>From Owner</th>
                    <th>To / Details</th>
                    <th>Plot</th>
                    <th>Balance Transferred</th>
                    <th>Transfer Fee</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Deed</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transfers as $t)
            <tr>
                <td style="color:#94a3b8;font-size:11px;">{{ $loop->iteration }}</td>

                <td><strong style="font-size:12px;color:#1e3a8a;font-family:'DM Mono',monospace;">{{ $t->deed_no }}</strong></td>

                <td>
                    @php
                        $typeBadge = match($t->transfer_type) {
                            'ownership' => ['class'=>'type-ownership','icon'=>'→','label'=>'Ownership'],
                            'swap'      => ['class'=>'type-swap',     'icon'=>'⇄','label'=>'Swap'],
                            'partial'   => ['class'=>'type-partial',  'icon'=>'%','label'=>'Partial'],
                            'internal'  => ['class'=>'type-internal', 'icon'=>'↺','label'=>'Internal'],
                            default     => ['class'=>'type-ownership','icon'=>'→','label'=>'Transfer'],
                        };
                    @endphp
                    <span class="type-badge {{ $typeBadge['class'] }}">
                        {{ $typeBadge['icon'] }} {{ $typeBadge['label'] }}
                    </span>
                </td>

                <td>
                    <div style="font-weight:700;font-size:12px;">{{ $t->fromCustomer->name ?? '—' }}</div>
                    <div style="font-size:10px;color:#94a3b8;">{{ $t->fromBooking->customer_booking_id ?? '' }}</div>
                </td>

                <td>
                    @if($t->transfer_type === 'ownership' || $t->transfer_type === 'partial')
                        <div style="font-weight:700;font-size:12px;color:#16a34a;">{{ $t->toCustomer->name ?? '—' }}</div>
                        @if($t->transfer_type === 'partial')
                        <div style="font-size:10px;color:#ea580c;">{{ $t->ownership_percentage }}% share</div>
                        @endif
                    @elseif($t->transfer_type === 'swap')
                        <div style="font-size:11px;color:#7c3aed;font-weight:700;">
                            Plot #{{ $t->plot->plot_number }} ⇄ Plot #{{ $t->swapPlot->plot_number ?? '?' }}
                        </div>
                        <div style="font-size:10px;color:#94a3b8;">{{ $t->swapFromBooking->customer->name ?? '' }}</div>
                    @elseif($t->transfer_type === 'internal')
                        <div style="font-size:11px;color:#16a34a;font-weight:700;">
                            → Block {{ $t->new_block }}, Plot #{{ $t->new_plot_number }}
                        </div>
                    @endif
                </td>

                <td>
                    <span style="font-weight:700;color:#0f172a;">Plot #{{ $t->plot->plot_number ?? '—' }}</span>
                    <span style="font-size:10px;color:#94a3b8;"> {{ $t->plot->block ?? '' }}</span>
                </td>

                <td>
                    @if(in_array($t->transfer_type, ['ownership','partial']) && $t->remaining_balance_transferred > 0)
                        <div style="font-weight:800;font-size:12px;color:#dc2626;">PKR {{ number_format($t->remaining_balance_transferred) }}</div>
                        <div style="font-size:10px;color:#94a3b8;">at transfer date</div>
                    @else
                        <span style="color:#94a3b8;font-size:11px;">—</span>
                    @endif
                </td>

                <td>
                    @if($t->transfer_fee > 0)
                        <div style="font-weight:800;font-size:13px;color:#0f172a;">PKR {{ number_format($t->transfer_fee) }}</div>
                        <span class="spill spill-{{ $t->transfer_fee_status === 'paid' ? 'completed' : ($t->transfer_fee_status === 'waived' ? 'approved' : 'pending') }}" style="margin-top:2px;">
                            <span class="spill-dot"></span>{{ ucfirst($t->transfer_fee_status) }}
                        </span>
                    @else
                        <span style="color:#94a3b8;font-size:11px;">Waived</span>
                    @endif
                </td>

                <td>
                    <div style="font-size:12px;font-weight:600;">{{ \Carbon\Carbon::parse($t->transfer_date)->format('d M Y') }}</div>
                    <div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($t->transfer_date)->diffForHumans() }}</div>
                </td>

                <td>
                    <span class="spill spill-{{ $t->status }}">
                        <span class="spill-dot"></span>{{ ucfirst($t->status) }}
                    </span>
                </td>

    {{--
    COMPLETE ACTION BUTTONS — replace the existing <td> in transfer_index.blade.php

    FIXES:
    1. App Form button now shows for ALL pending transfers (not inside fee check)
    2. Deed PDF available for both pending and completed
    3. No display:flex issues (that was blade, not dompdf — flex is fine here)
--}}

<td style="min-width:120px; vertical-align:top; padding: 8px 12px;">
<div style="display:flex; flex-direction:column; gap:5px;">

    {{-- ══════════════ PENDING ══════════════ --}}
    @if($t->status === 'pending')

        {{-- Application Form — always available for pending --}}
        <a href="{{ route('transfers.application.form', $t->id) }}" target="_blank"
           style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            App. Form
        </a>

        {{-- Deed PDF — available for pending too (preview) --}}
        <a href="{{ route('transfers.deed', $t->id) }}" target="_blank"
           style="background:#fff0f0;border:1px solid #fecaca;color:#dc2626;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            Deed PDF
        </a>

        {{-- Swap Deed — swap type only --}}
        @if($t->transfer_type === 'swap')
        <a href="{{ route('transfer.swap.deed', $t->id) }}" target="_blank"
           style="background:#f0f9ff;border:1px solid #bae6fd;color:#0369a1;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 3M21 7.5H7.5"/></svg>
            Swap Deed
        </a>
        @endif

        {{-- Fee Management link (goes to fee.management with search) --}}
        @if($t->toBooking)
        <a href="{{ route('fee.management', ['q' => $t->toBooking->customer_booking_id ?? '']) }}"
           style="background:#f0fdf4;border:1px solid #86efac;color:#15803d;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75"/></svg>
            Pay Transfer Fee
        </a>
        @endif

        {{-- Edit --}}
        <a href="{{ route('transfers.edit', $t->id) }}"
           style="background:#fffbeb;border:1px solid #fde68a;color:#ca8a04;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
            Edit
        </a>

        {{-- Reject --}}
        @php
            $rfb    = $t->fromBooking;
            $rfFees = $rfb ? $rfb->bookingFees->keyBy('fee_type') : collect();

            // Registry
            $rfReg     = $rfFees->get('registry');
            $rfRegAmt  = $rfReg ? (float)$rfReg->amount : 0;
            $rfRegPaid = $rfReg ? (float)$rfReg->paid_amount : 0;
            $rfRegOwed = max(0, $rfRegAmt - $rfRegPaid);

            // Development
            $rfDev     = $rfFees->get('development');
            $rfDevAmt  = $rfDev ? (float)$rfDev->amount : 0;
            $rfDevPaid = $rfDev ? (float)$rfDev->paid_amount : 0;
            $rfDevOwed = max(0, $rfDevAmt - $rfDevPaid);

            // Security — monthly
            $rfSec      = $rfFees->get('security');
            $rfSecRate  = $rfSec ? (float)$rfSec->amount : (float)($rfb?->plot?->security_fee_amount ?? 0);
            $rfSecPaid  = $rfSec ? (float)$rfSec->paid_amount : 0;
            $rfSecMonthsPaid = $rfSecRate > 0 ? (int)floor($rfSecPaid / $rfSecRate) : 0;

            // How many months A owed up to transfer date
            $rfSecMonthsTotal = 0;
            if ($rfSecRate > 0 && $rfb?->booking_date) {
                $rfStart  = \Carbon\Carbon::parse($rfb->booking_date)->startOfMonth();
                $rfTxDate = \Carbon\Carbon::parse($t->transfer_date)->startOfMonth();
                $rfSecMonthsTotal = (int)$rfStart->diffInMonths($rfTxDate) + 1;
            }
            $rfSecOutstanding = max(0, $rfSecMonthsTotal * $rfSecRate - $rfSecPaid);
        @endphp

        <button type="button"
            onclick="openRejectModal({{ $t->id }})"
            style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:5px;width:100%;font-family:inherit;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            Reject
        </button>

        {{-- Hidden form for actual submit --}}
        <form id="reject-form-{{ $t->id }}" method="POST" action="{{ route('transfers.reject', $t->id) }}" style="display:none;">
            @csrf
        </form>

        {{-- Reject modal --}}
        <div id="reject-modal-{{ $t->id }}"
             style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
            <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;margin:16px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">

                {{-- Header --}}
                <div style="background:linear-gradient(135deg,#7f1d1d,#dc2626);padding:18px 22px;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <p style="margin:0;font-size:14px;font-weight:800;color:#fff;">Reject Transfer</p>
                        <p style="margin:4px 0 0;font-size:11px;color:rgba(255,255,255,.65);">Deed: {{ $t->deed_no }} · {{ $t->fromCustomer->name ?? '—' }}</p>
                    </div>
                    <button onclick="closeRejectModal({{ $t->id }})"
                            style="background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:16px;line-height:1;display:flex;align-items:center;justify-content:center;">×</button>
                </div>

                <div style="padding:20px 22px;">

                    {{-- Warning --}}
                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:#7f1d1d;line-height:1.5;">
                        <strong>This will permanently reject the transfer</strong> and restore the original booking. All buyer records will be deleted.
                    </div>

                    {{-- Fee restoration summary --}}
                    <p style="font-size:11px;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin:0 0 10px;">Fees to be Restored (Original Owner)</p>

                    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:18px;">

                        {{-- Security --}}
                        @if($rfSecRate > 0)
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:12px;font-weight:700;color:#0f172a;">Security Fee <span style="font-weight:400;color:#94a3b8;">(monthly)</span></div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">
                                    {{ $rfSecMonthsPaid }}/{{ $rfSecMonthsTotal }} months paid
                                    @if($rfSecMonthsPaid > 0)
                                        · PKR {{ number_format($rfSecPaid) }} received
                                    @endif
                                </div>
                            </div>
                            @if($rfSecOutstanding <= 0)
                                <span style="background:#f0fdf4;color:#15803d;border:1px solid #86efac;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;">✓ Up to date</span>
                            @else
                                <span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;">{{ $rfSecMonthsTotal - $rfSecMonthsPaid }} month(s) due</span>
                            @endif
                        </div>
                        @endif

                        {{-- Registry --}}
                        @if($rfRegAmt > 0)
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:12px;font-weight:700;color:#0f172a;">Registry Fee</div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">PKR {{ number_format($rfRegPaid) }} paid of PKR {{ number_format($rfRegAmt) }}</div>
                            </div>
                            @if($rfRegOwed <= 0)
                                <span style="background:#f0fdf4;color:#15803d;border:1px solid #86efac;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;">✓ Settled</span>
                            @else
                                <span style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;">PKR {{ number_format($rfRegOwed) }} due</span>
                            @endif
                        </div>
                        @endif

                        {{-- Development --}}
                        @if($rfDevAmt > 0)
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:12px;font-weight:700;color:#0f172a;">Development Fee</div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">PKR {{ number_format($rfDevPaid) }} paid of PKR {{ number_format($rfDevAmt) }}</div>
                            </div>
                            @if($rfDevOwed <= 0)
                                <span style="background:#f0fdf4;color:#15803d;border:1px solid #86efac;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;">✓ Settled</span>
                            @else
                                <span style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;">PKR {{ number_format($rfDevOwed) }} due</span>
                            @endif
                        </div>
                        @endif

                        @if($rfSecRate <= 0 && $rfRegAmt <= 0 && $rfDevAmt <= 0)
                        <div style="text-align:center;font-size:12px;color:#94a3b8;padding:10px;">No fees on file for this booking.</div>
                        @endif

                    </div>

                    {{-- Action buttons --}}
                    <div style="display:flex;gap:10px;">
                        <button onclick="closeRejectModal({{ $t->id }})"
                                style="flex:1;background:#f1f5f9;border:1px solid #e2e8f0;color:#475569;padding:10px;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                            Cancel
                        </button>
                        <button onclick="document.getElementById('reject-form-{{ $t->id }}').submit()"
                                style="flex:1;background:#dc2626;border:none;color:#fff;padding:10px;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                            Confirm Reject &amp; Revert
                        </button>
                    </div>

                </div>
            </div>
        </div>

    {{-- ══════════════ COMPLETED ══════════════ --}}
    @elseif($t->status === 'completed')

        {{-- Application Form --}}
        <a href="{{ route('transfers.application.form', $t->id) }}" target="_blank"
           style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            App. Form
        </a>

        {{-- Deed PDF --}}
        <a href="{{ route('transfers.deed', $t->id) }}" target="_blank"
           style="background:#fff0f0;border:1px solid #fecaca;color:#dc2626;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            Deed PDF
        </a>

        {{-- Swap Deed -- swap only --}}
        @if($t->transfer_type === 'swap')
        <a href="{{ route('transfer.swap.deed', $t->id) }}" target="_blank"
           style="background:#f0f9ff;border:1px solid #bae6fd;color:#0369a1;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 3M21 7.5H7.5"/></svg>
            Swap Deed
        </a>
        @endif

        <div style="text-align:center;font-size:10px;font-weight:800;color:#15803d;padding:3px 0;background:#f0fdf4;border-radius:6px;">✓ Completed</div>

    {{-- ══════════════ REJECTED / OTHER ══════════════ --}}
    @else
        <div style="font-size:10px;font-weight:700;color:#dc2626;text-align:center;padding:4px 0;background:#fef2f2;border-radius:6px;">
            {{ ucfirst($t->status) }}
        </div>
    @endif

</div>
</td>
            </tr>
            @empty
                <tr><td colspan="10" style="text-align:center;padding:40px;color:#94a3b8;">No transfers found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{-- Pagination --}}
    @if($transfers->hasPages())
    <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
        {{ $transfers->links() }}
    </div>
    @endif
</div>

</div>{{-- /tr-wrap --}}

@push('scripts')
<script>
function openRejectModal(id) {
    var m = document.getElementById('reject-modal-' + id);
    if (m) { m.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
}
function closeRejectModal(id) {
    var m = document.getElementById('reject-modal-' + id);
    if (m) { m.style.display = 'none'; document.body.style.overflow = ''; }
}
// Close on backdrop click
document.addEventListener('click', function(e) {
    if (e.target.id && e.target.id.startsWith('reject-modal-')) {
        e.target.style.display = 'none';
        document.body.style.overflow = '';
    }
});
</script>
@endpush

@endsection
