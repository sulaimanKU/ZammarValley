@extends('layouts.index')

@section('content')

<style>
.fh-wrap { max-width: 960px; margin: 0 auto; padding: 0 16px 60px; }
.fh-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px; }
.fh-title { font-size:21px;font-weight:800;color:#0f172a;margin:0 0 3px; }
.fh-sub   { font-size:13px;color:#64748b;margin:0; }
.bk-banner { background:linear-gradient(135deg,#1e3a8a,#1d4ed8);border-radius:16px;padding:18px 22px;margin-bottom:24px;display:flex;align-items:center;gap:16px;flex-wrap:wrap; }
.bk-banner-av { width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,.15);color:#fff;font-size:20px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.bk-banner-name { font-size:15px;font-weight:800;color:#fff;margin:0 0 3px; }
.bk-banner-meta { font-size:11px;color:rgba(255,255,255,.7);display:flex;gap:8px;flex-wrap:wrap; }
.bk-banner-meta span+span::before { content:'·';margin-right:5px;opacity:.5; }

/* ── Section card ── */
.fh-card { background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;margin-bottom:22px;overflow:hidden; }
.fh-card-head { display:flex;align-items:center;gap:10px;padding:14px 20px;border-bottom:1px solid #f1f5f9;flex-wrap:wrap; }
.fee-badge-hd { display:inline-flex;align-items:center;gap:7px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700; }

/* ── Month grid ── */
.month-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(148px,1fr));gap:10px;padding:18px 20px; }
.month-cell { border-radius:10px;padding:11px 13px;border:1.5px solid #e2e8f0;position:relative; }
.mc-paid    { background:#f0fdf4;border-color:#bbf7d0; }
.mc-partial { background:#fffbeb;border-color:#fcd34d; }
.mc-unpaid  { background:#fef2f2;border-color:#fecaca; }
.mc-no   { font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px; }
.mc-name { font-size:13px;font-weight:800;color:#0f172a;margin-bottom:5px; }
.mc-paid .mc-name { color:#15803d; }
.mc-partial .mc-name { color:#854d0e; }
.mc-unpaid .mc-name { color:#dc2626; }
.mc-amt  { font-size:11px;font-weight:700; }
.mc-status { display:inline-block;margin-top:6px;font-size:10px;font-weight:800;padding:2px 9px;border-radius:20px; }
.mc-paid    .mc-status { background:#dcfce7;color:#15803d; }
.mc-partial .mc-status { background:#fef9c3;color:#854d0e; }
.mc-unpaid  .mc-status { background:#fef2f2;color:#dc2626; }

/* ── Payment table ── */
.fee-section { margin-bottom:22px; }
.fee-section-head { display:flex;align-items:center;gap:10px;padding:14px 20px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px 14px 0 0;border-bottom:none;flex-wrap:wrap; }
.fee-section-badge { display:inline-flex;align-items:center;gap:7px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700; }
.fee-section-total { margin-left:auto;font-size:13px;font-weight:800;color:#0f172a; }
.fee-section-total span { color:#16a34a; }
.pmt-table-wrap { border:1.5px solid #e2e8f0;border-top:none;border-radius:0 0 14px 14px;overflow:hidden; }
.pmt-table { width:100%;border-collapse:collapse; }
.pmt-table th { background:#fff;padding:10px 16px;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;text-align:left;border-bottom:1px solid #f1f5f9; }
.pmt-table td { padding:11px 16px;font-size:13px;color:#0f172a;border-bottom:1px solid #f8fafc;vertical-align:middle; }
.pmt-table tr:last-child td { border-bottom:none; }
.pmt-table tr:hover td { background:#fafbfc; }
.mode-pill { display:inline-block;padding:2px 9px;border-radius:20px;font-size:10px;font-weight:700;background:#f1f5f9;color:#475569; }
.btn-back { background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px; }
.btn-receipt { background:#1d4ed8;color:#fff;border:none;border-radius:8px;padding:5px 11px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:4px;cursor:pointer; }
</style>

<div class="fh-wrap">

    <div class="fh-header">
        <div>
            <h1 class="fh-title">Fee Payment History</h1>
            <p class="fh-sub">All recorded fee payments for this booking.</p>
        </div>
        <a href="{{ url()->previous() }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back
        </a>
    </div>

    {{-- ── Transferred notice ── --}}
    @if($booking->status === 'transferred')
    <div style="background:#f5f3ff;border:1.5px solid #c4b5fd;border-radius:12px;padding:12px 18px;margin-bottom:18px;font-size:12px;font-weight:700;color:#6d28d9;display:flex;align-items:center;gap:10px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
        This booking has been transferred. Records shown are historical and read-only.
    </div>
    @endif

    {{-- ── Booking Banner ── --}}
    <div class="bk-banner">
        <div class="bk-banner-av">{{ strtoupper(substr($booking->customer->name,0,1)) }}</div>
        <div style="flex:1;">
            <p class="bk-banner-name">{{ $booking->customer->name }}</p>
            <div class="bk-banner-meta">
                <span>{{ $booking->customer->cnic }}</span>
                <span>{{ $booking->customer_booking_id }}</span>
                <span>Plot #{{ $booking->plot->plot_number ?? '—' }} · {{ $booking->plot->block ?? '' }}</span>
                <span>{{ $booking->plot->size ?? '' }} {{ $booking->plot->unit ?? '' }}</span>
                <span>{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                <span>Booked: {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
            </div>
        </div>
        <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
            <div>
                <div style="font-size:10px;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.5px;">Total Fees Paid</div>
                <div style="font-size:18px;font-weight:800;color:#86efac;">
                    PKR {{ number_format($booking->bookingFees->sum('paid_amount')) }}
                </div>
            </div>
            <a href="{{ route('fee.booking.receipt', $booking->id) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.35);color:#fff;padding:7px 14px;border-radius:9px;font-size:11px;font-weight:800;text-decoration:none;white-space:nowrap;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                Combined Receipt
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SECURITY FEE — MONTHLY LEDGER
    ══════════════════════════════════════════════════════════════ --}}
    @if($securityLedger)
    <div class="fh-card">

        {{-- Card header --}}
        <div class="fh-card-head">
            <div class="fee-badge-hd" style="background:#fdf4ff;color:#7c3aed;">🔒 Security Fee — Monthly Ledger</div>
            <div style="font-size:11px;color:#94a3b8;">PKR {{ number_format($securityLedger['monthly_rate']) }} / month &nbsp;·&nbsp; from {{ \Carbon\Carbon::parse($booking->booking_date)->format('M Y') }}</div>
            <div style="margin-left:auto;display:flex;gap:14px;flex-wrap:wrap;">
                <div style="text-align:right;">
                    <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Months Paid</div>
                    <div style="font-size:15px;font-weight:800;color:#15803d;">{{ $securityLedger['months_paid'] }} / {{ $securityLedger['months_elapsed'] }}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Outstanding</div>
                    <div style="font-size:15px;font-weight:800;color:{{ $securityLedger['outstanding'] > 0 ? '#dc2626' : '#15803d' }};">
                        {{ $securityLedger['outstanding'] > 0 ? 'PKR '.number_format($securityLedger['outstanding']) : '✓ Up to date' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary bar --}}
        @php
            $paidPct = $securityLedger['months_elapsed'] > 0
                ? round(($securityLedger['months_paid'] / $securityLedger['months_elapsed']) * 100) : 0;
        @endphp
        <div style="padding:10px 20px;background:#f8fafc;border-bottom:1px solid #f1f5f9;">
            <div style="height:6px;background:#e2e8f0;border-radius:6px;overflow:hidden;">
                <div style="height:100%;width:{{ $paidPct }}%;background:{{ $paidPct >= 100 ? '#16a34a' : '#7c3aed' }};border-radius:6px;transition:width .4s;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;color:#94a3b8;margin-top:4px;">
                <span>Total paid: PKR {{ number_format($securityLedger['total_paid']) }}</span>
                <span>Total due so far: PKR {{ number_format($securityLedger['total_owed']) }}</span>
            </div>
        </div>

        {{-- Month grid --}}
        <div class="month-grid">
            @foreach($securityLedger['months'] as $m)
            @php $mc = $m['status'] === 'paid' ? 'mc-paid' : ($m['status'] === 'partial' ? 'mc-partial' : 'mc-unpaid'); @endphp
            <div class="month-cell {{ $mc }}">
                <div class="mc-no">Month #{{ $m['no'] }}</div>
                <div class="mc-name">{{ $m['month']->format('M Y') }}</div>
                @if($m['status'] === 'paid')
                    <div class="mc-amt" style="color:#15803d;">PKR {{ number_format($m['due']) }}</div>
                    <span class="mc-status">✓ Paid</span>
                @elseif($m['status'] === 'partial')
                    <div class="mc-amt" style="color:#854d0e;">PKR {{ number_format($m['paid']) }} / {{ number_format($m['due']) }}</div>
                    <span class="mc-status">~ Partial</span>
                @else
                    <div class="mc-amt" style="color:#dc2626;">PKR {{ number_format($m['due']) }} due</div>
                    <span class="mc-status">⏳ Unpaid</span>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Payment records for security fee --}}
        @if($securityLedger['all_payments']->isNotEmpty())
        <div style="border-top:1px solid #f1f5f9;">
            <div style="padding:12px 20px 0;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">
                Payment Records ({{ $securityLedger['all_payments']->count() }} entries)
            </div>
            <table class="pmt-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Receipt No.</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Paid Date</th>
                        <th>Notes</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningBefore = 0; @endphp
                    @foreach($securityLedger['all_payments'] as $pay)
                    @php
                        $runningAfter  = $runningBefore + (float)$pay->amount;
                        $fromM = (int)floor($runningBefore / $securityLedger['monthly_rate']) + 1;
                        $toM   = (int)floor($runningAfter  / $securityLedger['monthly_rate']);
                        $start = \Carbon\Carbon::parse($booking->booking_date)->startOfMonth();
                        $fromStr = $start->copy()->addMonths($fromM - 1)->format('M Y');
                        $toStr   = $toM > 0 ? $start->copy()->addMonths($toM - 1)->format('M Y') : null;
                        $runningBefore = $runningAfter;
                    @endphp
                    <tr>
                        <td style="color:#94a3b8;font-size:11px;">{{ $loop->iteration }}</td>
                        <td style="font-family:monospace;font-size:12px;font-weight:700;color:#1e3a8a;">{{ $pay->receipt_no ?? '—' }}</td>
                        <td style="font-weight:800;color:#16a34a;">PKR {{ number_format($pay->amount) }}</td>
                        <td><span class="mode-pill">{{ ucwords(str_replace('_',' ',$pay->payment_mode ?? 'cash')) }}</span></td>
                        <td style="font-size:12px;">
                            {{ \Carbon\Carbon::parse($pay->paid_date)->format('d M Y') }}
                            <div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($pay->paid_date)->diffForHumans() }}</div>
                        </td>
                        <td style="font-size:11px;color:#64748b;">
                            @if($toStr && $toM >= $fromM)
                                <span style="background:#f5f3ff;color:#6d28d9;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;">
                                    Covers: {{ $fromStr }}{{ $fromStr !== $toStr ? ' – '.$toStr : '' }}
                                </span>
                            @else
                                <span style="background:#fffbeb;color:#854d0e;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;">
                                    Partial: {{ $fromStr }}
                                </span>
                            @endif
                            @if($pay->notes) <div style="margin-top:3px;color:#64748b;">{{ $pay->notes }}</div> @endif
                        </td>
                        <td>
                            <a href="{{ route('fee.receipt', $pay->id) }}" target="_blank" class="btn-receipt">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659"/></svg>
                                Print
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f8fafc;">
                        <td colspan="2" style="text-align:right;font-size:10px;font-weight:700;color:#94a3b8;letter-spacing:.5px;">TOTAL COLLECTED</td>
                        <td style="font-weight:800;font-size:14px;color:#16a34a;">PKR {{ number_format($securityLedger['total_paid']) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div style="text-align:center;padding:20px;font-size:12px;color:#94a3b8;">No payments recorded yet for security fee.</div>
        @endif

    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         OTHER FEE BILLS (registry, development, transfer)
    ══════════════════════════════════════════════════════════════ --}}
    @php
        $feeMeta = [
            'registry'    => ['label'=>'Registry Fee',    'icon'=>'📋','color'=>'#1d4ed8','bg'=>'#eff6ff'],
            'development' => ['label'=>'Development Fee', 'icon'=>'🏗️','color'=>'#16a34a','bg'=>'#f0fdf4'],
            'security'    => ['label'=>'Security Fee',    'icon'=>'🔒','color'=>'#7c3aed','bg'=>'#fdf4ff'],
            'transfer'    => ['label'=>'Transfer Fee',    'icon'=>'🤝','color'=>'#ca8a04','bg'=>'#fefce8'],
        ];
        // If security ledger is shown above, skip the raw security bill in the loop below
        $otherBills = $securityLedger
            ? $booking->bookingFees->where('fee_type', '!=', 'security')
            : $booking->bookingFees;
    @endphp

    @forelse($otherBills as $bill)
    @php
        $meta     = $feeMeta[$bill->fee_type] ?? ['label'=>ucfirst($bill->fee_type),'icon'=>'💳','color'=>'#475569','bg'=>'#f1f5f9'];
        $payments = $bill->payments->sortByDesc('paid_date');
        $total    = $payments->sum('amount');
    @endphp

    <div class="fee-section">
        <div class="fee-section-head">
            <div class="fee-section-badge" style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }};">
                {{ $meta['icon'] }} {{ $meta['label'] }}
            </div>
            <div style="font-size:11px;color:#94a3b8;">
                {{ $payments->count() }} payment{{ $payments->count() !== 1 ? 's' : '' }}
            </div>
            @if((float)$bill->amount > 0)
            <div style="font-size:11px;color:#64748b;">
                Bill: PKR {{ number_format($bill->amount) }}
            </div>
            @endif
            <div class="fee-section-total">
                Paid: <span>PKR {{ number_format($total) }}</span>
            </div>
            @php $bs = $bill->status; @endphp
            <span style="font-size:10px;font-weight:800;padding:3px 10px;border-radius:20px;
                  background:{{ $bs==='paid'?'#dcfce7':($bs==='partial'?'#fef9c3':'#fef2f2') }};
                  color:{{ $bs==='paid'?'#15803d':($bs==='partial'?'#854d0e':'#dc2626') }};">
                {{ ucfirst($bs) }}
            </span>
        </div>

        <div class="pmt-table-wrap">
            @if($payments->isEmpty())
                <div style="text-align:center;padding:24px;font-size:12px;color:#94a3b8;background:#fff;">
                    No payments recorded yet.
                </div>
            @else
            <table class="pmt-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Receipt No.</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Paid Date</th>
                        <th>Notes</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $pay)
                    <tr>
                        <td style="color:#94a3b8;font-size:11px;">{{ $loop->iteration }}</td>
                        <td style="font-family:monospace;font-size:12px;font-weight:700;color:#1e3a8a;">{{ $pay->receipt_no ?? '—' }}</td>
                        <td style="font-weight:800;color:#16a34a;">PKR {{ number_format($pay->amount) }}</td>
                        <td><span class="mode-pill">{{ ucwords(str_replace('_',' ',$pay->payment_mode ?? 'cash')) }}</span></td>
                        <td style="font-size:12px;">
                            {{ \Carbon\Carbon::parse($pay->paid_date)->format('d M Y') }}
                            <div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($pay->paid_date)->diffForHumans() }}</div>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $pay->notes ?? '—' }}</td>
                        <td>
                            <a href="{{ route('fee.receipt', $pay->id) }}" target="_blank" class="btn-receipt">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659"/></svg>
                                Print
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f8fafc;">
                        <td colspan="2" style="text-align:right;font-size:10px;font-weight:700;color:#94a3b8;letter-spacing:.5px;">SUBTOTAL</td>
                        <td style="font-weight:800;font-size:14px;color:#16a34a;">PKR {{ number_format($total) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </div>
    </div>
    @empty
        @if(!$securityLedger)
        <div style="text-align:center;padding:48px;color:#94a3b8;">
            <p style="font-weight:700;font-size:14px;margin:0;">No fee bills found for this booking.</p>
        </div>
        @endif
    @endforelse

</div>
@endsection
