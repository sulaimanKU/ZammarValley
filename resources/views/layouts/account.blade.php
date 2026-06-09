@extends('layouts.index')

@section('content')

    <div class="ledger-wrap">

        {{-- PAGE HEADER --}}
        <div class="rpt-header no-print">
            <div>
                <p class="rpt-header-title">Account &amp; Finance</p>
                <p class="rpt-header-sub">Complete overview of all Payments — Zamar Valley &nbsp;·&nbsp; {{ now()->format('d M Y') }}</p>
            </div>
            <div class="rpt-header-actions">
                <a href="{{ route('finance.report') }}" class="btn-soft-header">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                    Finance Report
                </a>
                <a href="{{ route('booking.reports') }}" class="btn-soft-header">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    Booking Report
                </a>
            </div>
        </div>

        {{-- ════════════════════════════════════
             SEARCH CARD
        ════════════════════════════════════ --}}
        <div class="search-card">
            <p class="search-card-title">Find Client to Log Payment</p>
            <p class="search-card-sub">Search by CNIC, Name or Plot Number to open the client ledger and add a transaction.</p>

            <form action="{{ route('client.search') }}" method="POST">
                @csrf
                <div class="search-input-wrap">
                    <div class="search-input-box">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>
                        </svg>
                        <input type="text" name="search" value="{{ old('search', request('search')) }}"
                            placeholder="Search by Name, CNIC, Phone, Booking ID, or Plot Number...">
                    </div>
                    <button type="submit" class="btn-navy">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>
                        </svg>
                        Search Client
                    </button>
                </div>
                <div class="search-hints">
                    <span class="search-hint-tag">👤 Name</span>
                    <span class="search-hint-tag">📋 CNIC</span>
                    <span class="search-hint-tag">📞 Phone / Mobile</span>
                    <span class="search-hint-tag">🔖 Booking ID</span>
                    <span class="search-hint-tag">🏘️ Plot No. / Block</span>
                </div>
            </form>
        </div>

        {{-- ════════════════════════════════════
             SEARCH RESULTS
        ════════════════════════════════════ --}}
        @if(isset($clients) && $clients->isNotEmpty())
            <div class="results-section-title">
                Search Results &nbsp;
                <span style="font-size:11px;background:#eff6ff;color:#1d4ed8;padding:2px 9px;border-radius:20px;font-weight:700;">
                    {{ $clients->count() }} client{{ $clients->count() > 1 ? 's' : '' }} found
                </span>
            </div>

            <div class="row g-3">
                @foreach($clients as $client)
                    @foreach($client->booking as $book)
                        @php
                            $plotPriceCats = ['down_payment','quarterly_installment','installment','plot_balance','others'];
                            $paid = $book->payments
                                ->where('status','paid')
                                ->where('is_external', false)
                                ->whereIn('payment_category', $plotPriceCats)
                                ->sum('amount_paid');
                            $rem = max(0, (float)$book->total_price - $paid);

                            $bookTransfer = \App\Models\PlotTransfer::where('from_booking_id', $book->id)
                                ->where('status','completed')
                                ->with(['fromCustomer','toCustomer'])
                                ->latest('transfer_date')
                                ->first();
                        @endphp
                        <div class="col-md-4">
                            <div class="client-card">
                                <div class="client-card-top">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="client-card-avatar">
                                            {{ strtoupper(substr($client->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="client-card-name">{{ $client->name }}</p>
                                            <p class="client-card-cnic">CNIC: {{ $client->cnic }}</p>
                                        </div>
                                        <div class="ms-auto">
                                            @if($book->status === 'completed')
                                                <span class="spill spill-paid"><span class="spill-dot"></span>Completed</span>
                                            @elseif($book->status === 'transferred')
                                                <span class="spill" style="background:#eff6ff;color:#1d4ed8;"><span class="spill-dot" style="background:#1d4ed8;"></span>Transferred</span>
                                            @elseif($book->status === 'cancelled')
                                                <span class="spill" style="background:#fef2f2;color:#991b1b;"><span class="spill-dot" style="background:#991b1b;"></span>Cancelled</span>
                                            @elseif($book->status === 'pending')
                                                <span class="spill" style="background:#fffbeb;color:#92400e;"><span class="spill-dot" style="background:#92400e;"></span>Pending</span>
                                            @elseif($book->status === 'active')
                                                @if($rem <= 0)
                                                    <span class="spill spill-paid"><span class="spill-dot"></span>Fully Paid</span>
                                                @elseif($paid > 0)
                                                    <span class="spill spill-partial"><span class="spill-dot"></span>Partial</span>
                                                @else
                                                    <span class="spill spill-unpaid"><span class="spill-dot"></span>Unpaid</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    @if($bookTransfer)
                                        <div style="margin-top:10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:8px 12px;font-size:11px;color:#1d4ed8;display:flex;align-items:center;gap:8px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;flex-shrink:0;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                                            </svg>
                                            <span>Transferred from <strong>{{ $bookTransfer->fromCustomer->name ?? '—' }}</strong> on {{ \Carbon\Carbon::parse($bookTransfer->transfer_date)->format('d M Y') }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="client-card-body">
                                    <div class="client-card-row">
                                        <span class="cc-row-label">Booking Ref</span>
                                        <span class="cc-row-value" style="color:#1e3a8a;font-size:11px;font-family:monospace;">{{ $book->customer_booking_id }}</span>
                                    </div>
                                    <div class="client-card-row">
                                        <span class="cc-row-label">Plot No.</span>
                                        <span class="cc-row-value">#{{ $book->plot->plot_number ?? 'N/A' }} — {{ $book->plot->block ?? '' }}</span>
                                    </div>
                                    <div class="client-card-row">
                                        <span class="cc-row-label">Total Price</span>
                                        <span class="cc-row-value">PKR {{ number_format($book->total_price) }}</span>
                                    </div>
                                    <div class="client-card-row">
                                        <span class="cc-row-label">Paid So Far</span>
                                        <span class="cc-row-value green">PKR {{ number_format($paid) }}</span>
                                    </div>
                                    <div class="client-card-row">
                                        <span class="cc-row-label">Remaining</span>
                                        <span class="cc-row-value {{ $rem > 0 ? 'red' : 'green' }}">
                                            PKR {{ number_format($rem) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="client-card-foot">
                                    <a href="{{ route('ledger.view', $book->id) }}" class="btn-navy w-100" style="justify-content:center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75"/>
                                        </svg>
                                        Open Full Ledger
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

        @elseif(isset($clients) && $clients->isEmpty() && isset($oldOwnerBookings) && $oldOwnerBookings->isEmpty())
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="font-weight:700;font-size:14px;color:#64748b;margin-bottom:5px;">No client found</p>
                <p style="font-size:12px;">No records match your search. Try CNIC, Name or Plot Number.</p>
            </div>
        @else
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>
                </svg>
                <p style="font-weight:700;font-size:14px;color:#64748b;margin-bottom:5px;">Search for a client above</p>
                <p style="font-size:12px;">Enter a CNIC or Plot Number to look up their ledger.</p>
            </div>
        @endif

        {{-- OLD OWNER / TRANSFER HISTORY --}}
        @if(isset($oldOwnerBookings) && $oldOwnerBookings->isNotEmpty())
            <div class="results-section-title" style="margin-top:24px;color:#ca8a04;">
                Transfer History Found &nbsp;
                <span style="font-size:11px;background:#fffbeb;color:#854d0e;padding:2px 9px;border-radius:20px;font-weight:700;">
                    {{ $oldOwnerBookings->count() }} record{{ $oldOwnerBookings->count() > 1 ? 's' : '' }}
                </span>
                <span style="font-size:11px;color:#94a3b8;font-weight:400;margin-left:6px;">(Read-only — this person transferred their plot)</span>
            </div>

            <div class="row g-3">
                @foreach($oldOwnerBookings as $ob)
                    @php
                        $bk = $ob->booking;
                        $totalPaidByOldOwner = $bk->payments
                            ->where('status','paid')
                            ->where('is_external', false)
                            ->sum('amount_paid');
                    @endphp
                    <div class="col-md-6">
                        <div class="client-card" style="border-left:4px solid #ca8a04;opacity:.95;">
                            <div class="client-card-top" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="client-card-avatar" style="background:#fef9c3;color:#92400e;font-size:18px;font-weight:900;">
                                        {{ strtoupper(substr($ob->fromCustomer->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="client-card-name">{{ $ob->fromCustomer->name ?? '—' }}</p>
                                        <p class="client-card-cnic">CNIC: {{ $ob->fromCustomer->cnic ?? '—' }}</p>
                                    </div>
                                    <div class="ms-auto">
                                        <span style="font-size:9px;font-weight:800;background:#fef9c3;color:#854d0e;padding:4px 12px;border-radius:20px;text-transform:uppercase;border:1px solid #fde68a;">Transferred</span>
                                    </div>
                                </div>
                            </div>
                            <div class="client-card-body">
                                <div class="client-card-row">
                                    <span class="cc-row-label">Booking Ref</span>
                                    <span class="cc-row-value" style="color:#1e3a8a;font-size:11px;font-family:monospace;">{{ $bk->customer_booking_id ?? '—' }}</span>
                                </div>
                                <div class="client-card-row">
                                    <span class="cc-row-label">Plot</span>
                                    <span class="cc-row-value">#{{ $bk->plot->plot_number ?? '—' }} — {{ $bk->plot->block ?? '' }}</span>
                                </div>
                                <div class="client-card-row">
                                    <span class="cc-row-label">Total Paid by them</span>
                                    <span class="cc-row-value green">PKR {{ number_format($totalPaidByOldOwner) }}</span>
                                </div>
                                <div style="margin-top:12px;background:#f8fafc;border-radius:10px;padding:12px 14px;">
                                    <div style="font-size:9.5px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px;">Transfer Chain</div>
                                    @foreach($ob->allTransfers as $at)
                                        <div style="display:flex;align-items:center;gap:8px;{{ !$loop->last ? 'margin-bottom:8px;' : '' }}">
                                            <div style="width:7px;height:7px;border-radius:50%;background:{{ $at->status === 'completed' ? '#16a34a' : '#ca8a04' }};flex-shrink:0;"></div>
                                            <div style="flex:1;">
                                                <div style="font-size:11px;font-weight:700;color:#0f172a;">
                                                    {{ $at->fromCustomer->name ?? '—' }}
                                                    <span style="color:#94a3b8;font-weight:400;"> → </span>
                                                    {{ $at->toCustomer->name ?? '—' }}
                                                </div>
                                                <div style="font-size:10px;color:#94a3b8;">
                                                    {{ \Carbon\Carbon::parse($at->transfer_date)->format('d M Y') }}
                                                    &nbsp;·&nbsp;
                                                    <span style="font-family:monospace;color:#1e3a8a;">{{ $at->deed_no }}</span>
                                                    &nbsp;·&nbsp;
                                                    PKR {{ number_format($at->transfer_fee) }} fee
                                                </div>
                                            </div>
                                            <a href="{{ route('transfers.deed', $at->id) }}" target="_blank"
                                               style="font-size:10px;color:#1d4ed8;font-weight:700;text-decoration:none;white-space:nowrap;">Deed ↗</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="client-card-foot" style="background:#fffbeb;">
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <a href="{{ route('ledger.view', $bk->id) }}?readonly=1" class="btn-soft" style="flex:1;justify-content:center;font-size:12px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        View Full History
                                    </a>
                                    <span style="font-size:10px;color:#94a3b8;font-weight:600;display:flex;align-items:center;gap:4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                        </svg>
                                        Read Only
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- RECENT PAYMENTS TABLE --}}
        @if(isset($recent_payments) && $recent_payments->count() > 0)
            <div class="recent-card">
                <div class="recent-card-head">
                    <div>
                        <p class="recent-card-title">Recent Payments</p>
                        <p class="recent-card-sub">Latest {{ $recent_payments->count() }} Zamar receipts</p>
                    </div>
                    <a href="{{ route('finance.report') }}" class="btn-soft">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        Full Finance Report
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table rtable mb-0">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Receipt No.</th>
                                <th>Booking Ref</th>
                                <th>Plot</th>
                                <th>Amount Paid</th>
                                <th>Category</th>
                                <th>Installment</th>
                                <th>Mode</th>
                                <th>Paid Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_payments as $pay)
                                <tr>
                                    <td>
                                        <div style="font-weight:600;">{{ $pay->booking->customer->name ?? 'N/A' }}</div>
                                        <div style="font-size:11px;color:#94a3b8;">{{ $pay->booking->customer->cnic ?? '' }}</div>
                                    </td>
                                    <td>
                                        <span style="font-size:11px;font-weight:800;color:#1e3a8a;letter-spacing:.3px;font-family:monospace;">
                                            {{ $pay->receipt_no ?? '—' }}
                                        </span>
                                    </td>
                                    <td style="font-size:11px;font-weight:700;color:#475569;">
                                        {{ $pay->booking->customer_booking_id ?? '—' }}
                                    </td>
                                    <td>
                                        <div style="font-weight:600;">#{{ $pay->booking->plot->plot_number ?? 'N/A' }}</div>
                                        <div style="font-size:11px;color:#94a3b8;">{{ $pay->booking->plot->block ?? '' }}</div>
                                    </td>
                                    <td style="font-weight:800;color:#16a34a;">
                                        PKR {{ number_format($pay->amount_paid) }}
                                    </td>
                                    <td>
                                        @php
                                            $catMap = [
                                                'down_payment'           => ['bg'=>'#eff6ff','c'=>'#1d4ed8'],
                                                'quarterly_installment'  => ['bg'=>'#fff7ed','c'=>'#b45309'],
                                                'installment'            => ['bg'=>'#f0fdf4','c'=>'#16a34a'],
                                                'processing_fee'         => ['bg'=>'#fff7ed','c'=>'#ea580c'],
                                                'plot_balance'           => ['bg'=>'#f0f9ff','c'=>'#0369a1'],
                                                'fine'                   => ['bg'=>'#fef2f2','c'=>'#dc2626'],
                                                'security_fee'           => ['bg'=>'#fdf4ff','c'=>'#7c3aed'],
                                                'development_fee'        => ['bg'=>'#fefce8','c'=>'#a16207'],
                                                'registry_fee'           => ['bg'=>'#f8fafc','c'=>'#475569'],
                                                'others'                 => ['bg'=>'#f1f5f9','c'=>'#475569'],
                                            ];
                                            $cm = $catMap[strtolower($pay->payment_category ?? '')] ?? ['bg'=>'#f1f5f9','c'=>'#475569'];
                                        @endphp
                                        <span style="font-size:10px;font-weight:700;background:{{ $cm['bg'] }};color:{{ $cm['c'] }};padding:3px 10px;border-radius:20px;white-space:nowrap;">
                                            {{ ucwords(str_replace('_',' ',$pay->payment_category ?? '—')) }}
                                        </span>
                                    </td>
                                    <td style="text-align:center;">
                                        @if($pay->installment_no)
                                            <span style="font-size:11px;font-weight:700;background:#f8fafc;border:1px solid #e2e8f0;color:#475569;padding:3px 9px;border-radius:8px;">
                                                # {{ $pay->installment_no }}
                                            </span>
                                        @else
                                            <span style="color:#94a3b8;font-size:12px;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size:10px;font-weight:700;background:#f1f5f9;color:#475569;padding:3px 10px;border-radius:20px;">
                                            {{ ucfirst(str_replace('_',' ',$pay->payment_type ?? '—')) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($pay->paid_date)
                                            <div style="font-size:13px;">{{ \Carbon\Carbon::parse($pay->paid_date)->format('d M Y') }}</div>
                                            <div style="font-size:11px;color:#94a3b8;">{{ \Carbon\Carbon::parse($pay->paid_date)->diffForHumans() }}</div>
                                        @else
                                            <span style="color:#94a3b8;font-size:12px;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="spill spill-paid"><span class="spill-dot"></span> Paid</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('ledger.view', $pay->booking_id) }}" class="btn-soft" style="padding:5px 10px;font-size:11px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>{{-- /ledger-wrap --}}
@endsection
