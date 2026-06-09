@extends('layouts.index')

@section('content')

<div class="tr-wrap">

    {{-- Header --}}
    <div class="tr-header">
        <div>
            <p class="tr-header-title">New Transfer</p>
            <p class="tr-header-sub">Search for a booking to transfer — by customer name, CNIC, plot number, or booking ID</p>
        </div>
        <a href="{{ route('index.transfer') }}" class="btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to Transfers
        </a>
    </div>

    @if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:13px;font-weight:600;color:#dc2626;display:flex;align-items:center;gap:10px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @if(session('info'))
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:13px;font-weight:600;color:#1d4ed8;display:flex;align-items:center;gap:10px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
        {{ session('info') }}
    </div>
    @endif

    {{-- Search Box --}}
    <div class="search-card" style="margin-bottom:24px;">
        <p class="search-card-title">Find Booking to Transfer</p>
        <p class="search-card-sub">Search by customer name, CNIC, mobile, plot number, block, or booking reference.</p>

        <form action="{{ route('transfers.search') }}" method="GET">
            <div class="search-input-wrap">
                <div class="search-input-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="e.g. Ali Khan, 35202-1234567-1, Plot 101, A Block, ZV-ABC..."
                           autofocus>
                </div>
                <button type="submit" class="btn-navy">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>
                    </svg>
                    Search
                </button>
            </div>
            <div class="search-hints">
                <span class="search-hint-tag">👤 Customer Name</span>
                <span class="search-hint-tag">📋 CNIC</span>
                <span class="search-hint-tag">📱 Mobile / Phone</span>
                <span class="search-hint-tag">🏘️ Plot No. / Block</span>
                <span class="search-hint-tag">🔖 Booking ID</span>
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if(request()->filled('q'))
        @if($bookings->isEmpty())
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="font-weight:700;font-size:14px;color:#64748b;margin-bottom:5px;">No bookings found</p>
                <p style="font-size:12px;">Only active and completed bookings can be transferred. Try a different search.</p>
            </div>
        @else
            <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px;">
                {{ $bookings->count() }} booking{{ $bookings->count() > 1 ? 's' : '' }} found
                <span style="font-size:11px;font-weight:400;color:#94a3b8;margin-left:6px;">Click a booking to proceed with the transfer</span>
            </div>

            <div class="row g-3">
                @foreach($bookings as $book)
                @php
                    $plotCats   = ['down_payment','quarterly_installment','installment','plot_balance','others'];
                    $paid       = $book->payments->where('status','paid')->whereIn('payment_category',$plotCats)->sum('amount_paid');
                    $discAmt    = $book->payments->where('status','paid')->whereIn('payment_category',$plotCats)->sum('discount_amount');
                    $remaining  = max(0, $book->total_price - $paid - $discAmt);
                    $pct        = $book->total_price > 0 ? min(round((($paid + $discAmt) / $book->total_price) * 100), 100) : 0;
                    $statusColors = [
                        'active'    => ['bg'=>'#eff6ff','color'=>'#1d4ed8'],
                        'completed' => ['bg'=>'#f0fdf4','color'=>'#15803d'],
                    ];
                    $sc = $statusColors[$book->status] ?? ['bg'=>'#f1f5f9','color'=>'#475569'];

                    // ── On-hold check ───────────────────────────────────
                    $isOnHold = $book->activeHold !== null;

                    // ── Fee checks ─────────────────────────────────────────────
                    // Security: block if never paid. Registry/Development: info notice only (not a block).
                    $feeBlocks   = []; // hard blocks
                    $feeWarnings = []; // informational only

                    // Registry fee — info notice only; settled once any payment made
                    $regFeeRec = $book->bookingFees->where('fee_type','registry')->first();
                    if (($book->has_registry_fee || $regFeeRec) && $regFeeRec && !$regFeeRec->is_settled) {
                        $regOwed       = max(0, ($regFeeRec->amount ?? 0) - ($regFeeRec->paid_amount ?? 0));
                        $feeWarnings[] = 'Registry Fee' . ($regOwed > 0 ? ' (PKR '.number_format($regOwed).' due)' : ' (unsettled)');
                    }

                    // Development fee — mandatory; blocks transfer if outstanding balance > 0 or never paid
                    $devFeeRec = $book->bookingFees->where('fee_type','development')->first();
                    if (($book->has_development_fee || $devFeeRec) && $devFeeRec) {
                        $devPaid = (float)($devFeeRec->paid_amount ?? 0);
                        $devAmt  = (float)($devFeeRec->amount ?? 0);
                        if ($devPaid == 0) {
                            $feeBlocks[] = 'Development Fee — never paid' . ($devAmt > 0 ? ' (PKR '.number_format($devAmt).' due)' : '');
                        } elseif ($devAmt > 0 && $devPaid < $devAmt) {
                            $feeBlocks[] = 'Development Fee — PKR '.number_format($devAmt - $devPaid).' outstanding';
                        }
                        // devPaid >= devAmt OR (devAmt == 0 && devPaid > 0) → fully paid / open-ended with payment — no block
                    }

                    // Security fee — block only if NEVER paid (any payment = customer is active)
                    $secFeeRec = $book->bookingFees->where('fee_type','security')->first();
                    if ($book->has_security_fee && $secFeeRec && (float)($secFeeRec->paid_amount ?? 0) == 0) {
                        $feeBlocks[] = 'Security Fee — never paid (at least one month required)';
                    } elseif ($book->has_security_fee && $secFeeRec && (float)($secFeeRec->paid_amount ?? 0) > 0) {
                        // Has payments — show outstanding as info only
                        $monthlyAmt = (float)($secFeeRec->amount > 0 ? $secFeeRec->amount : ($book->plot->security_fee_amount ?? 0));
                        
                        $effectiveStart = $book->security_fee_start_date ?: $book->booking_date;
                        
                        if ($monthlyAmt > 0 && $effectiveStart) {
                            $secStart = \Carbon\Carbon::parse($effectiveStart)->startOfMonth();
                            
                            if ($book->security_fee_end_date) {
                                $secNow = \Carbon\Carbon::parse($book->security_fee_end_date)->startOfMonth();
                            } else {
                                $secNow = \Carbon\Carbon::now()->startOfMonth();
                                $terminalSt = ['transferred','partial_transferred','cancelled','swapped','plot_relocated'];
                                if (in_array($book->status, $terminalSt)) {
                                    $cap = \Carbon\Carbon::parse($book->updated_at)->startOfMonth();
                                    if ($cap->lt($secNow)) $secNow = $cap;
                                }
                            }

                            if ($secStart->lte($secNow)) {
                                $monthsElapsed = (int)$secStart->diffInMonths($secNow) + 1;
                                $totalOwed     = $monthsElapsed * $monthlyAmt;
                                $totalSecPaid  = (float)$secFeeRec->paid_amount;
                                if ($totalSecPaid < $totalOwed) {
                                    $shortfall     = number_format($totalOwed - $totalSecPaid);
                                    $feeWarnings[] = 'Security Fee — PKR '.$shortfall.' in arrears (transfer still allowed)';
                                }
                            }
                        }
                    }

                    $isBlocked = $isOnHold || count($feeBlocks) > 0;
                @endphp
                <div class="col-md-6 col-lg-4">
                    @if($isBlocked)
                    {{-- Blocked card — not a link --}}
                    <div>
                    @else
                    <a href="{{ route('transfers.create', $book->id) }}" style="text-decoration:none;">
                    @endif
                        <div class="client-card" style="{{ $isBlocked ? 'opacity:.88;' : 'cursor:pointer;' }}transition:box-shadow .15s,transform .15s;"
                            @if(!$isBlocked)
                            onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,.12)';this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.boxShadow='';this.style.transform=''"
                            @endif>

                            <div class="client-card-top">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="client-card-avatar" style="{{ $isBlocked ? 'background:#fee2e2;color:#dc2626;' : '' }}">
                                        {{ strtoupper(substr($book->customer->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="client-card-name">{{ $book->customer->name ?? '—' }}</p>
                                        <p class="client-card-cnic">{{ $book->customer->cnic ?? '' }}</p>
                                    </div>
                                    <div class="ms-auto">
                                        <span style="font-size:10px;font-weight:800;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:3px 10px;border-radius:20px;text-transform:uppercase;">
                                            {{ ucfirst($book->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="client-card-body">
                                <div class="client-card-row">
                                    <span class="cc-row-label">Booking Ref</span>
                                    <span class="cc-row-value" style="font-family:monospace;color:#1e3a8a;font-size:11px;">{{ $book->customer_booking_id }}</span>
                                </div>
                                <div class="client-card-row">
                                    <span class="cc-row-label">Plot</span>
                                    <span class="cc-row-value">#{{ $book->plot->plot_number ?? '—' }} — {{ $book->plot->block ?? '' }} ({{ $book->plot->size ?? '' }} {{ $book->plot->unit ?? '' }})</span>
                                </div>
                                <div class="client-card-row">
                                    <span class="cc-row-label">Total Price</span>
                                    <span class="cc-row-value">PKR {{ number_format($book->total_price) }}</span>
                                </div>
                                <div class="client-card-row">
                                    <span class="cc-row-label">Paid</span>
                                    <span class="cc-row-value green">PKR {{ number_format($paid) }}</span>
                                </div>
                                <div class="client-card-row">
                                    <span class="cc-row-label">Remaining</span>
                                    <span class="cc-row-value {{ $remaining > 0 ? 'red' : 'green' }}">PKR {{ number_format($remaining) }}</span>
                                </div>

                                {{-- Progress bar --}}
                                <div style="margin-top:10px;">
                                    <div style="display:flex;justify-content:space-between;font-size:10px;color:#94a3b8;margin-bottom:4px;">
                                        <span>Payment Progress</span><span>{{ $pct }}%</span>
                                    </div>
                                    <div style="height:5px;background:#e2e8f0;border-radius:10px;overflow:hidden;">
                                        <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 100 ? '#16a34a' : '#1d4ed8' }};border-radius:10px;"></div>
                                    </div>
                                </div>

                                {{-- On-hold / fee blocking warnings --}}
                                @if($isOnHold)
                                <div style="margin-top:10px;background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:8px 10px;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="#d97706" style="width:13px;height:13px;flex-shrink:0;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/>
                                        </svg>
                                        <span style="font-size:10px;font-weight:800;color:#92400e;text-transform:uppercase;letter-spacing:.4px;">Booking On Hold — Release hold to transfer</span>
                                    </div>
                                </div>
                                @elseif($isBlocked)
                                <div style="margin-top:10px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:8px 10px;">
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#dc2626" style="width:13px;height:13px;flex-shrink:0;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                        </svg>
                                        <span style="font-size:10px;font-weight:800;color:#dc2626;text-transform:uppercase;letter-spacing:.4px;">Transfer Blocked</span>
                                    </div>
                                    @foreach($feeBlocks as $fb)
                                    <div style="font-size:11px;color:#b91c1c;font-weight:600;padding-left:19px;line-height:1.6;">• {{ $fb }}</div>
                                    @endforeach
                                </div>
                                @endif
                                @if(count($feeWarnings) > 0)
                                <div style="margin-top:10px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:8px 10px;">
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#d97706" style="width:13px;height:13px;flex-shrink:0;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                        </svg>
                                        <span style="font-size:10px;font-weight:800;color:#92400e;text-transform:uppercase;letter-spacing:.4px;">Fee Notice</span>
                                    </div>
                                    @foreach($feeWarnings as $fw)
                                    <div style="font-size:11px;color:#92400e;font-weight:600;padding-left:19px;line-height:1.6;">• {{ $fw }}</div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            @if(in_array($book->status, ['transferred','partial_transferred']))
                            <div class="client-card-foot" style="background:#fdf4ff;">
                                <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                                    <span style="font-size:11px;font-weight:700;color:#7c3aed;">Previously transferred — will redirect to current booking →</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#7c3aed" style="width:15px;height:15px;flex-shrink:0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </div>
                            </div>
                            @elseif($isOnHold)
                            <div class="client-card-foot" style="background:#fefce8;border-top:1px solid #fde68a;">
                                <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                                    <span style="font-size:11px;font-weight:700;color:#92400e;">Release hold to enable transfer</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#d97706" style="width:14px;height:14px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
                                </div>
                            </div>
                            @elseif($isBlocked)
                            <div class="client-card-foot" style="background:#fef2f2;border-top:1px solid #fecaca;">
                                <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                                    <span style="font-size:11px;font-weight:700;color:#dc2626;">Clear outstanding fees to enable transfer</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#dc2626" style="width:14px;height:14px;flex-shrink:0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </div>
                            </div>
                            @else
                            <div class="client-card-foot" style="background:#f8fafc;">
                                <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                                    <span style="font-size:12px;font-weight:700;color:#475569;">Click to Transfer →</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#7c3aed" style="width:16px;height:16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                                    </svg>
                                </div>
                            </div>
                            @endif

                        </div>
                    @if($isBlocked)
                    </div>
                    @else
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>
            </svg>
            <p style="font-weight:700;font-size:14px;color:#64748b;margin-bottom:5px;">Search for a booking above</p>
            <p style="font-size:12px;">Enter customer name, CNIC, or plot number to find the booking you want to transfer.</p>
        </div>
    @endif

</div>

@endsection
