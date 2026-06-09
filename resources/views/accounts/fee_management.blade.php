@extends('layouts.index')

@push('styles')
<style>
.fm-wrap { max-width: 960px; margin: 0 auto; padding: 0 16px 60px; }
.fm-search-box { background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;padding:28px;margin-bottom:28px; }
.fm-search-row { display:flex;gap:10px; }
.fm-search-input { flex:1;border:1.5px solid #e2e8f0;border-radius:10px;padding:11px 16px;font-size:13px;color:#0f172a;outline:none;font-family:inherit;transition:border-color .15s,box-shadow .15s; }
.fm-search-input:focus { border-color:#1d4ed8;box-shadow:0 0 0 3px rgba(29,78,216,.08); }
.fm-search-btn { background:#1d4ed8;color:#fff;border:none;border-radius:10px;padding:11px 22px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;font-family:inherit;white-space:nowrap; }

.bk-card { background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;margin-bottom:20px;overflow:hidden; }
.bk-card-top { display:flex;align-items:center;gap:14px;padding:16px 20px;border-bottom:1px solid #f1f5f9;flex-wrap:wrap; }
.bk-av { width:44px;height:44px;border-radius:12px;background:#eff6ff;color:#1d4ed8;font-size:18px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.bk-name { font-size:14px;font-weight:800;color:#0f172a;margin:0 0 2px; }
.bk-meta { font-size:11px;color:#94a3b8;display:flex;gap:8px;flex-wrap:wrap; }
.bk-meta span+span::before { content:'·';margin-right:5px;opacity:.5; }

.fee-row { display:grid;grid-template-columns:180px 1fr 140px 110px 120px;align-items:center;gap:10px;padding:13px 20px;border-bottom:1px solid #f8fafc; }
.fee-row:last-child { border-bottom:none; }
.fee-badge { display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap; }
.fee-progress { width:100%; }
.fee-progress-bar { height:5px;background:#e2e8f0;border-radius:6px;overflow:hidden;margin-bottom:3px; }
.fee-progress-fill { height:100%;border-radius:6px; }
.fee-progress-label { display:flex;justify-content:space-between;font-size:10px;color:#94a3b8; }
.fee-status { display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;white-space:nowrap; }
.fs-paid    { background:#dcfce7;color:#15803d; }
.fs-partial { background:#fef9c3;color:#854d0e; }
.fs-unpaid  { background:#fef2f2;color:#dc2626; }
.fs-open    { background:#eff6ff;color:#1d4ed8; }
.btn-pay { background:#1d4ed8;color:#fff;border:none;border-radius:8px;padding:7px 13px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;white-space:nowrap;display:inline-flex;align-items:center;gap:4px; }
.btn-pay.green { background:#16a34a; }
.btn-pay-sm { background:none;border:1.5px solid #7c3aed;color:#7c3aed;border-radius:8px;padding:4px 10px;font-size:10px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:3px;margin-top:2px;transition:all .15s; }
.btn-pay-sm:hover { background:#7c3aed;color:#fff; }
.btn-history { background:#f8fafc;color:#475569;border:1.5px solid #e2e8f0;border-radius:8px;padding:6px 12px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:4px; }

.modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,.45);display:none;align-items:center;justify-content:center;z-index:9999;padding:20px; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:16px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2); }
.modal-head { display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #f1f5f9; }
.modal-head h3 { font-size:15px;font-weight:800;color:#0f172a;margin:0; }
.modal-close { background:none;border:none;cursor:pointer;font-size:22px;color:#94a3b8;line-height:1; }
.modal-body { padding:22px; }
.modal-footer { padding:16px 22px;border-top:1px solid #f1f5f9;display:flex;gap:10px;justify-content:flex-end; }
.ml { font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;display:block; }
.ml small { color:#94a3b8;font-weight:400; }
.mi { width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 13px;font-size:13px;color:#0f172a;font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s; }
.mi:focus { border-color:#1d4ed8;box-shadow:0 0 0 3px rgba(29,78,216,.08); }
.fg { margin-bottom:14px; }
.fg-2 { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.btn-save { background:#1d4ed8;color:#fff;border:none;border-radius:9px;padding:10px 22px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit; }
.btn-mcancel { background:#f1f5f9;color:#475569;border:none;border-radius:9px;padding:10px 16px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit; }
.bill-info { background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:11px 14px;margin-bottom:16px;font-size:12px;color:#475569;line-height:1.6; }
.rem-text { font-size:11px;color:#dc2626;font-weight:700;margin-top:4px; }
</style>
@endpush

@section('content')

<div class="fm-wrap">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:21px;font-weight:800;color:#0f172a;margin:0 0 3px;">Fee Management</h1>
        <p style="font-size:13px;color:#64748b;margin:0;">Manage registry, development, security and transfer fee payments.</p>
    </div>

    @if(session('success'))
    <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:12px 18px;margin-bottom:18px;font-size:13px;font-weight:700;color:#15803d;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
        @if(session('last_payment_id'))
        <a href="{{ route('fee.receipt', session('last_payment_id')) }}" target="_blank"
           style="margin-left:auto;background:#1d4ed8;color:#fff;border-radius:8px;padding:6px 14px;font-size:11px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            🖨 Print Receipt
        </a>
        @endif
    </div>
    @endif

    @if($errors->any())
    <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:12px;padding:12px 18px;margin-bottom:18px;font-size:12px;color:#dc2626;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="fm-search-box">
        <p style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 4px;">Search Booking</p>
        <p style="font-size:12px;color:#94a3b8;margin:0 0 16px;">Enter customer name, CNIC, mobile, plot number, block, or booking ID.</p>
        <form method="GET" action="{{ route('fee.management') }}">
            <div class="fm-search-row">
                <input type="text" name="q" class="fm-search-input" value="{{ request('q') }}"
                       placeholder="e.g. Ali Khan / 35202-1234567-1 / Plot 101 / ZV-ABC..." autofocus>
                <button type="submit" class="fm-search-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/></svg>
                    Search
                </button>
            </div>
        </form>
    </div>

    @if(request()->filled('q'))
        @if($bookings->isEmpty())
            <div style="text-align:center;padding:48px;color:#94a3b8;">
                <p style="font-weight:700;font-size:14px;margin:0 0 4px;">No bookings found</p>
                <p style="font-size:12px;margin:0;">Try a different search term.</p>
            </div>
        @else

        <div style="font-size:12px;font-weight:700;color:#475569;margin-bottom:14px;">
            {{ $bookings->count() }} result{{ $bookings->count() > 1 ? 's' : '' }} for "<strong>{{ request('q') }}</strong>"
        </div>

        @foreach($bookings as $bk)
        @php
            /*
             * fee_type meta — all 4 types
             * recurring = true  → never marked "paid", always allow more payments
             * recurring = false → one-time, marks as "paid" when amount settled
             */
            $feeMeta = [
                'registry'    => ['label'=>'Registry Fee',    'icon'=>'📋','color'=>'#1d4ed8','bg'=>'#eff6ff','recurring'=>false],
                'development' => ['label'=>'Development Fee', 'icon'=>'🏗️','color'=>'#16a34a','bg'=>'#f0fdf4','recurring'=>true],
                'security'    => ['label'=>'Security Fee',    'icon'=>'🔒','color'=>'#7c3aed','bg'=>'#fdf4ff','recurring'=>true],
                'transfer'    => ['label'=>'Transfer Fee',    'icon'=>'🤝','color'=>'#ca8a04','bg'=>'#fefce8','recurring'=>false],
            ];

            // Bills keyed by fee_type
            $billsByType = $bk->bookingFees->keyBy('fee_type');

            // Transfer role
            $isSeller      = $bk->transfersFrom->isNotEmpty();
            $isBuyer       = $bk->transfersTo->isNotEmpty();
            $isTransferred = $bk->status === 'transferred'; // A handed over the plot

            // Transfer limit (from the plot, for buyer awareness)
            $transferCount = (int)($bk->plot->transfer_count ?? 0);
            $transferMaxed = $transferCount >= 5;

            /*
             * Standard fee rules:
             *
             * security    → plot owner only (NOT if transferred away)
             * development → plot owner only (NOT if transferred away)
             * registry    → if has_registry_fee=1 (shown in ALL booking statuses)
             * transfer    → BUYER only (to_booking_id = this booking)
             *               Seller (A) never pays transfer fee
             *
             * Once A transfers the plot away → only show whatever fees they
             * already have bills for (registry if was 1, development if was 1)
             * but NO security (they don't own the plot anymore) and NO transfer.
             */
            $isInactive = in_array($bk->status, ['transferred','cancelled','swapped','plot_relocated'])
                          || ($isSeller && in_array($bk->status, ['pending_transfer','partial_transferred']));
                          
            $readOnly   = $isInactive; // historical/inactive bookings are read-only for new fee types

            $shouldHave = $bk->bookingFees->pluck('fee_type')->unique()->values()->toArray();

            if (!$isInactive) {
                if ($bk->has_security_fee && !in_array('security', $shouldHave))       $shouldHave[] = 'security';
                if ($bk->has_registry_fee && !in_array('registry', $shouldHave))       $shouldHave[] = 'registry';
                if ($bk->has_development_fee && !in_array('development', $shouldHave)) $shouldHave[] = 'development';
            } else {
                // Registry is a government fee — always show if flag is set or a bill exists
                if ($bk->has_registry_fee && !in_array('registry', $shouldHave)) {
                    $shouldHave[] = 'registry';
                }
            }

            if ($isBuyer && !in_array('transfer', $shouldHave)) $shouldHave[] = 'transfer'; // buyer pays transfer fee

            $showFees    = array_values(array_filter($shouldHave, fn($t) => $billsByType->has($t)));
            $missingFees = !$readOnly
                ? array_values(array_filter($shouldHave, fn($t) => !$billsByType->has($t)))
                : [];

            // ── Security fee monthly calculation ──────────────────────
            // Per-booking rate: BookingFee.amount is the source of truth.
            // Falls back to plot.security_fee_amount if bill not yet created.
            $secBillForCalc  = $billsByType->get('security');
            $secMonthlyRate  = 0;
            if ($secBillForCalc && (float)$secBillForCalc->amount > 0) {
                $secMonthlyRate = (float)$secBillForCalc->amount;
            } elseif ($secBillForCalc) {
                $firstPmt = $secBillForCalc->payments->sortBy('paid_date')->first();
                $secMonthlyRate = $firstPmt ? (float)$firstPmt->amount : 0;
            }
            if ($secMonthlyRate <= 0) {
                $secMonthlyRate = (float)($bk->plot->security_fee_amount ?? 0);
            }
            $secMonthsPaid   = 0;
            $secMonthsTotal  = 0;
            $secOutstanding  = 0;
            $secUpToDate     = false;
            if (($bk->has_security_fee || $secBillForCalc) && $secMonthlyRate > 0 && $bk->booking_date) {
                $secStart   = \Carbon\Carbon::parse($bk->booking_date)->startOfMonth();
                $secNow     = \Carbon\Carbon::now()->startOfMonth();
                $terminalSt = ['transferred', 'cancelled', 'swapped', 'plot_relocated'];
                if (in_array($bk->status, $terminalSt) || ($isSeller && in_array($bk->status, ['pending_transfer','partial_transferred']))) {
                    $xfer = $bk->transfersFrom->whereIn('status',['completed','pending'])->sortByDesc('transfer_date')->first();
                    $capRaw = $xfer ? $xfer->transfer_date : $bk->updated_at;
                    $cap = \Carbon\Carbon::parse($capRaw)->startOfMonth();
                    if ($cap->lt($secNow)) $secNow = $cap;
                }
                $secMonthsTotal  = (int)$secStart->diffInMonths($secNow) + 1;
                $secTotalPaid    = $secBillForCalc ? (float)$secBillForCalc->paid_amount : 0;
                $secMonthsPaid   = (int)floor($secTotalPaid / $secMonthlyRate);
                $secOutstanding  = max(0, $secMonthsTotal * $secMonthlyRate - $secTotalPaid);
                $secUpToDate     = $secOutstanding <= 0;
            }
        @endphp

        <div class="bk-card">

            <div class="bk-card-top">
                <div class="bk-av">{{ strtoupper(substr($bk->customer->name,0,1)) }}</div>
                <div style="flex:1;">
                    <p class="bk-name">{{ $bk->customer->name }}</p>
                    <div class="bk-meta">
                        <span>{{ $bk->customer->cnic }}</span>
                        <span>{{ $bk->customer_booking_id }}</span>
                        <span>Plot #{{ $bk->plot->plot_number ?? '—' }} · {{ $bk->plot->block ?? '' }}</span>
                        <span>{{ $bk->plot->size ?? '' }} {{ $bk->plot->unit ?? '' }}</span>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                    <span style="font-size:10px;font-weight:800;padding:4px 11px;border-radius:20px;
                          background:{{ $isTransferred?'#f5f3ff':($bk->status==='completed'?'#f0fdf4':'#eff6ff') }};
                          color:{{ $isTransferred?'#7c3aed':($bk->status==='completed'?'#15803d':'#1d4ed8') }};">
                        {{ ucfirst(str_replace('_',' ',$bk->status)) }}
                    </span>
                    @if($isSeller && !$isTransferred)
                    <span style="font-size:10px;font-weight:800;padding:4px 10px;border-radius:20px;background:#fef9c3;color:#854d0e;">
                        ⏳ Transfer Pending · {{ $transferCount }}/5
                    </span>
                    @endif
                    @if($isTransferred)
                    <span style="font-size:10px;font-weight:800;padding:4px 10px;border-radius:20px;background:#f5f3ff;color:#6d28d9;">
                        📤 Transferred — Historical Records Only
                    </span>
                    @endif
                    @if($isBuyer)
                    <span style="font-size:10px;font-weight:800;padding:4px 10px;border-radius:20px;background:#ecfdf5;color:#15803d;">
                        📥 New Owner · Transfer Fee Due
                    </span>
                    @endif
                </div>
                  {{-- <a href="{{ route('fee.booking.receipt', $booking->id) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.35);color:#fff;padding:7px 14px;border-radius:9px;font-size:11px;font-weight:800;text-decoration:none;white-space:nowrap;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                Combined Receipt
            </a> --}}
                <a href="{{ route('fee.history', $bk->id) }}" class="btn-history">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    History
                </a>
            </div>

            {{-- ── Fees with existing bills ── --}}
            @foreach($showFees as $feeType)
            @php
                $meta        = $feeMeta[$feeType];
                $bill        = $billsByType->get($feeType);
                $paid        = (float)$bill->paid_amount;
                $total       = (float)$bill->amount;
                $remaining   = max(0, $total - $paid);
                $pct         = $total > 0 ? min(round(($paid / $total) * 100), 100) : 0;
                $isRecurring = $meta['recurring'];
                $isSettled   = !$isRecurring && $paid > 0;
                $isPartial   = $paid > 0 && !$isSettled;

                // For transfer: block if plot hit limit
                $isBlocked = ($feeType === 'transfer' && $transferMaxed && !$isSettled);
            @endphp
            <div class="fee-row">

                <div>
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <div class="fee-badge" style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }};">
                            {{ $meta['icon'] }} {{ $meta['label'] }}
                        </div>
                        @if(!$readOnly && in_array($feeType, ['security','registry','development']))
                        <button type="button"
                            onclick="openEditAmountModal('{{ $bill->id }}', '{{ $feeType }}', '{{ $meta['label'] }}', {{ $feeType === 'security' ? $secMonthlyRate : $total }}, '{{ request('q') }}')"
                            style="background:none;border:1px solid #cbd5e1;border-radius:6px;padding:2px 7px;cursor:pointer;font-size:10px;color:#64748b;font-weight:700;"
                            title="Edit {{ $feeType === 'security' ? 'monthly rate' : 'bill amount' }}">
                            ✏️ Edit {{ $feeType === 'security' ? 'Rate' : 'Amount' }}
                        </button>
                        @endif
                    </div>
                    @if($feeType === 'security' && $secMonthlyRate > 0)
                    <div style="font-size:10px;color:#7c3aed;margin-top:2px;font-weight:700;">
                        PKR {{ number_format($secMonthlyRate) }}/mo
                    </div>
                    @elseif($isRecurring && $feeType !== 'security')
                    <div style="font-size:10px;color:#94a3b8;margin-top:2px;font-style:italic;">Recurring</div>
                    @endif
                </div>

                {{-- Progress --}}
                @if($feeType === 'security' && $secMonthlyRate > 0 && $secMonthsTotal > 0)
                    <div class="fee-progress">
                        <div class="fee-progress-bar">
                            <div class="fee-progress-fill" style="width:{{ min(round(($secMonthsPaid/$secMonthsTotal)*100),100) }}%;background:{{ $secUpToDate?'#16a34a':'#7c3aed' }};"></div>
                        </div>
                        <div class="fee-progress-label">
                            <span>{{ $secMonthsPaid }}/{{ $secMonthsTotal }} months paid</span>
                            <span>{{ $secUpToDate ? '✓ Up to date' : ('PKR '.number_format($secOutstanding).' due') }}</span>
                        </div>
                    </div>
                @elseif($feeType === 'development')
                    {{-- Development: open-ended, preset amount is informational only --}}
                    <div style="font-size:11px;color:#94a3b8;">
                        @if($paid > 0)
                            PKR {{ number_format($paid) }} collected
                            @if($total > 0)<span style="opacity:.55;"> / PKR {{ number_format($total) }} suggested</span>@endif
                        @elseif($total > 0)
                            Suggested: PKR {{ number_format($total) }} — pay any amount
                        @else
                            Pay any amount
                        @endif
                    </div>
                @elseif($total == 0)
                    <div style="font-size:11px;color:#94a3b8;">
                        @if($isRecurring) PKR {{ number_format($paid) }} collected so far
                        @else No bill amount set — pay any amount
                        @endif
                    </div>
                @else
                    <div class="fee-progress">
                        <div class="fee-progress-bar">
                            <div class="fee-progress-fill" style="width:{{ $pct }}%;background:{{ $isSettled?'#16a34a':'#1d4ed8' }};"></div>
                        </div>
                        <div class="fee-progress-label">
                            <span>Paid PKR {{ number_format($paid) }}</span>
                            @if($total > 0)<span style="opacity:.6;">/ PKR {{ number_format($total) }} suggested</span>@endif
                        </div>
                    </div>
                @endif

                {{-- Remaining --}}
                <div style="font-size:12px;font-weight:700;color:{{ $isSettled||($feeType==='security'&&$secUpToDate)?'#16a34a':($feeType==='security'&&$secOutstanding>0?'#dc2626':'#64748b') }};">
                    @if($feeType === 'security' && $secMonthlyRate > 0)
                        @if($secUpToDate) ✓ Up to date
                        @else PKR {{ number_format($secOutstanding) }} due<div style="font-size:10px;font-weight:500;color:#b45309;">{{ $secMonthsTotal - $secMonthsPaid }} month(s) unpaid</div>
                        @endif
                    @elseif($isRecurring) Open
                    @elseif($total == 0) —
                    @elseif($isSettled) ✓ Settled
                    @else PKR {{ number_format($remaining) }} left
                    @endif
                </div>

                {{-- Status badge --}}
                <div class="fee-status {{ $feeType==='security'&&$secUpToDate?'fs-paid':($isRecurring?'fs-open':($isSettled?'fs-paid':($isPartial?'fs-partial':'fs-unpaid'))) }}">
                    @if($feeType === 'security' && $secUpToDate) ✓ Up to date
                    @elseif($feeType === 'security') ⏳ {{ $secMonthsTotal - $secMonthsPaid }} month(s) due
                    @elseif($isSettled) ✓ Paid
                    @elseif($isPartial) ~ Partial
                    @elseif($isRecurring) ● Open
                    @else ⏳ Unpaid
                    @endif
                </div>

                {{-- Pay button or Done + Receipt --}}
                @php
                    $lastPayment = $bill->payments->sortByDesc('created_at')->first();
                @endphp
                @if($readOnly)
                    {{-- Transferred booking: read-only, just show history link --}}
                    <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-start;">
                        <span style="font-size:10px;color:#6d28d9;font-weight:700;">Historical</span>
                        @if($lastPayment)
                        <a href="{{ route('fee.receipt', $lastPayment->id) }}" target="_blank"
                           style="font-size:10px;color:#1d4ed8;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:3px;">
                            🖨 Receipt
                        </a>
                        @endif
                    </div>
                @elseif($isSettled)
                    <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-start;">
                        <span style="font-size:11px;color:#16a34a;font-weight:800;">✓ Done</span>
                        @if($lastPayment)
                        <a href="{{ route('fee.receipt', $lastPayment->id) }}" target="_blank"
                           style="font-size:10px;color:#1d4ed8;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:3px;">
                            🖨 Receipt
                        </a>
                        @endif
                    </div>
                @elseif($isBlocked)
                    <span style="font-size:10px;color:#dc2626;font-weight:700;">Max 5 transfers reached</span>
                @else
                    <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-start;">
                        @if($feeType === 'security' && $secOutstanding > $secMonthlyRate)
                            {{-- Pay ALL Outstanding --}}
                            <button class="btn-pay" style="background:#7c3aed;"
                                    onclick="openPayModal(
                                        '{{ $bill->id }}',
                                        '{{ $bk->id }}',
                                        '{{ $feeType }}',
                                        '{{ $meta['label'] }} (All Due)',
                                        '{{ addslashes($bk->customer->name) }}',
                                        '{{ $bk->customer_booking_id }}',
                                        {{ (float)$secOutstanding }},
                                        {{ json_encode(request('q')) }},
                                        {{ (float)$secOutstanding }}
                                    )">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Pay All Due
                            </button>
                            {{-- Smaller Pay One Month button --}}
                            <button class="btn-pay-sm"
                                    onclick="openPayModal(
                                        '{{ $bill->id }}',
                                        '{{ $bk->id }}',
                                        '{{ $feeType }}',
                                        '{{ $meta['label'] }} (Single)',
                                        '{{ addslashes($bk->customer->name) }}',
                                        '{{ $bk->customer_booking_id }}',
                                        {{ (float)$secOutstanding }},
                                        {{ json_encode(request('q')) }},
                                        {{ (float)$secMonthlyRate }}
                                    )">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:10px;height:10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Pay Single Month
                            </button>
                        @else
                            <button class="btn-pay {{ ($isRecurring||$feeType==='security') ? 'green' : '' }}"
                                    onclick="openPayModal(
                                        '{{ $bill->id }}',
                                        '{{ $bk->id }}',
                                        '{{ $feeType }}',
                                        '{{ $meta['label'] }}',
                                        '{{ addslashes($bk->customer->name) }}',
                                        '{{ $bk->customer_booking_id }}',
                                        {{ (float)(($feeType==='security') ? $secOutstanding : $remaining) }},
                                        {{ json_encode(request('q')) }},
                                        {{ (float)(($feeType==='security') ? $secMonthlyRate : ($remaining > 0 ? $remaining : 0)) }}
                                    )">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                @if($feeType==='security') Pay Month @elseif($isRecurring) Pay @elseif($isPartial) Add More @else Pay Now @endif
                            </button>
                        @endif

                        @if($lastPayment)
                        <a href="{{ route('fee.receipt', $lastPayment->id) }}" target="_blank"
                           style="font-size:10px;color:#64748b;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:3px;">
                            🖨 Last Receipt
                        </a>
                        @endif
                    </div>
                @endif

            </div>
            @endforeach

            {{-- ── Missing bills (registry/development not yet created) ── --}}
            {{-- Note: security and transfer are auto-created by controller, so they rarely appear here --}}
            @foreach($missingFees as $feeType)
            @php $meta = $feeMeta[$feeType]; @endphp
            <div style="background:#fffbeb;padding:12px 20px;font-size:11px;color:#92400e;display:flex;align-items:center;gap:10px;flex-wrap:wrap;border-bottom:1px solid #fef3c7;">
                <span class="fee-badge" style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }};font-size:10px;padding:3px 9px;">
                    {{ $meta['icon'] }} {{ $meta['label'] }}
                </span>
                <span style="flex:1;">No bill found — click to create and record first payment.</span>
                <button class="btn-pay" style="background:#d97706;"
                        onclick="openPayModal(
                            '',
                            '{{ $bk->id }}',
                            '{{ $feeType }}',
                            '{{ $meta['label'] }}',
                            '{{ addslashes($bk->customer->name) }}',
                            '{{ $bk->customer_booking_id }}',
                            0,
                            '{{ request('q') }}'
                        )">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Create &amp; Pay
                </button>
            </div>
            @endforeach

            @if(empty($showFees) && empty($missingFees))
            <div style="padding:16px 20px;font-size:12px;color:#94a3b8;text-align:center;">No fees applicable.</div>
            @endif

        </div>
        @endforeach

        @endif
    @else
        <div style="text-align:center;padding:56px;color:#94a3b8;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:44px;height:44px;margin:0 auto 14px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/></svg>
            <p style="font-weight:700;font-size:15px;margin:0 0 5px;color:#64748b;">Search for a booking above</p>
            <p style="font-size:12px;margin:0;">Enter customer name, CNIC or plot number to see fee status.</p>
        </div>
    @endif
</div>

{{-- Payment Modal --}}
<div class="modal-overlay" id="payModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3 id="payModalTitle">Record Payment</h3>
            <button class="modal-close" onclick="closePayModal()">&times;</button>
        </div>
        <form action="{{ route('fee.payment.store') }}" method="POST">
            @csrf
            <input type="hidden" name="booking_fee_id" id="pBillId">
            <input type="hidden" name="booking_id"     id="pBookingId">
            <input type="hidden" name="fee_type"       id="pFeeType">
            <input type="hidden" name="_search_q"      id="pSearchQ">
            <div class="modal-body">
                <div class="bill-info" id="payBillInfo"></div>
                <div class="fg-2">
                    <div class="fg">
                        <label class="ml">Amount (PKR) <span style="color:#dc2626">*</span></label>
                        <input type="number" name="amount" id="pAmount" class="mi" placeholder="e.g. 50000" step="any" required>
                        <div class="rem-text" id="pRemText"></div>
                    </div>
                    <div class="fg">
                        <label class="ml">Paid Date <span style="color:#dc2626">*</span></label>
                        <input type="date" name="paid_date" class="mi" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="fg-2">
                    <div class="fg">
                        <label class="ml">Payment Mode <span style="color:#dc2626">*</span></label>
                        <select name="payment_mode" class="mi" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label class="ml">Receipt No. <small>(auto if blank)</small></label>
                        <input type="text" name="receipt_no" class="mi" placeholder="e.g. REC-0001">
                    </div>
                </div>
                <div class="fg">
                    <label class="ml">Notes <small>(optional)</small></label>
                    <input type="text" name="notes" class="mi" placeholder="Optional...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-mcancel" onclick="closePayModal()">Cancel</button>
                <button type="submit" class="btn-save">Save Payment</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Bill Amount Modal --}}
<div class="modal-overlay" id="editAmountModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3 id="editAmountTitle">Update Fee Amount</h3>
            <button class="modal-close" onclick="closeEditAmountModal()">&times;</button>
        </div>
        <form id="editAmountForm" method="POST">
            @csrf
            <input type="hidden" name="_search_q" id="eSearchQ">
            <div class="modal-body">
                <div class="bill-info" id="editAmountInfo"></div>
                <div class="fg">
                    <label class="ml" id="editAmountLabel">New Amount (PKR) <span style="color:#dc2626">*</span></label>
                    <input type="number" name="new_amount" id="eNewAmount" class="mi" placeholder="e.g. 25000" step="any" min="0" required>
                    <div class="rem-text" id="editAmountHint" style="color:#64748b;font-size:11px;margin-top:4px;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-mcancel" onclick="closeEditAmountModal()">Cancel</button>
                <button type="submit" class="btn-save">Update</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openPayModal(billId, bookingId, feeType, feeLabel, customerName, bookingRef, remaining, searchQ, fillAmount = '') {
    document.getElementById('pBillId').value    = billId;
    document.getElementById('pBookingId').value = bookingId;
    document.getElementById('pFeeType').value   = feeType;
    document.getElementById('pSearchQ').value   = searchQ;
    const isNew = !billId || billId === '0';
    document.getElementById('payModalTitle').textContent = (isNew ? 'Create & Pay — ' : 'Record ') + feeLabel;
    document.getElementById('payBillInfo').innerHTML =
        '<strong>' + customerName + '</strong> &nbsp;·&nbsp; ' + bookingRef +
        ' &nbsp;·&nbsp; <strong>' + feeLabel + '</strong>' +
        (isNew ? ' <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;margin-left:4px;">New Bill</span>' : '');
    document.getElementById('pRemText').textContent =
        remaining > 0 ? 'Remaining: PKR ' + remaining.toLocaleString() : '';
    document.getElementById('pAmount').value = fillAmount;
    document.getElementById('payModal').classList.add('open');
}
function closePayModal() { document.getElementById('payModal').classList.remove('open'); }
document.getElementById('payModal').addEventListener('click', function(e) { 
    if(e.target === this) closePayModal(); 
});

function openEditAmountModal(billId, feeType, feeLabel, currentAmount, searchQ) {
    const url = '{{ url("fee-management/bill") }}/' + billId + '/update-amount';
    document.getElementById('editAmountForm').action = url;
    document.getElementById('eSearchQ').value  = searchQ;
    document.getElementById('eNewAmount').value = currentAmount > 0 ? currentAmount : '';
    const isSecurity = feeType === 'security';
    document.getElementById('editAmountTitle').textContent = isSecurity ? 'Update Monthly Rate — ' + feeLabel : 'Update Bill Amount — ' + feeLabel;
    document.getElementById('editAmountLabel').innerHTML   = (isSecurity ? 'Monthly Rate (PKR)' : 'Total Bill Amount (PKR)') + ' <span style="color:#dc2626">*</span>';
    document.getElementById('editAmountInfo').innerHTML    = isSecurity
        ? '<span style="font-size:12px;color:#7c3aed;font-weight:700;">Changes the monthly rate for this booking only.</span>'
        : '<span style="font-size:12px;color:#1d4ed8;font-weight:700;">Changes the total billed amount. Payments already made are kept.</span>';
    document.getElementById('editAmountHint').textContent  = currentAmount > 0 ? 'Current: PKR ' + Number(currentAmount).toLocaleString() : 'No amount set yet.';
    document.getElementById('editAmountModal').classList.add('open');
}
function closeEditAmountModal() { document.getElementById('editAmountModal').classList.remove('open'); }
document.getElementById('editAmountModal').addEventListener('click', e => { if(e.target === document.getElementById('editAmountModal')) closeEditAmountModal(); });
</script>
@endpush

@endsection
