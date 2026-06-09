@extends('layouts.index')

@push('styles')
<style>
.detail-wrap { max-width: 900px; margin: 0 auto; padding: 0 16px 60px; }

.page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px; }
.page-hdr h1 { font-size:19px;font-weight:800;color:#0f172a;margin:0 0 3px; }
.page-hdr p  { font-size:12px;color:#64748b;margin:0; }

.btn-back { background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:border-color .15s; }
.btn-back:hover { border-color:#94a3b8;color:#374151; }

.btn-pdf { background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;border:none;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:7px;transition:opacity .15s; }
.btn-pdf:hover { opacity:.88;color:#fff; }

.btn-edit { background:#fff;color:#374151;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:border-color .15s; }
.btn-edit:hover { border-color:#94a3b8; }

/* Hero strip */
.hero-strip {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    border-radius: 16px; padding: 22px 26px; margin-bottom: 20px;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;
    position: relative; overflow: hidden;
}
.hero-strip::before { content:'';position:absolute;top:-40px;right:-20px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.04); }
.hero-voucher { font-size:12px;color:rgba(255,255,255,.5);margin-bottom:4px;font-family:monospace;letter-spacing:.5px; }
.hero-amount  { font-size:32px;font-weight:800;color:#fff;position:relative;z-index:1; }
.hero-type    { display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px; }
.hero-type.expense   { background:rgba(239,68,68,.2);color:#fca5a5; }
.hero-type.income    { background:rgba(34,197,94,.2);color:#86efac; }
.hero-type.inventory { background:rgba(167,139,250,.2);color:#c4b5fd; }

/* Fund source highlight */
.fund-highlight {
    display: flex; align-items: center; gap: 12px;
    border-radius: 14px; padding: 16px 20px; margin-bottom: 20px;
    border: 2px solid;
}
.fund-highlight .fh-icon { font-size:28px;flex-shrink:0; }
.fund-highlight .fh-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;opacity:.7;margin-bottom:3px; }
.fund-highlight .fh-name  { font-size:17px;font-weight:800; }
.fund-highlight .fh-sub   { font-size:11px;opacity:.65;margin-top:2px; }

/* Cards */
.detail-grid { display:grid;grid-template-columns:1fr 320px;gap:18px; }
@media(max-width:768px){ .detail-grid{ grid-template-columns:1fr; } }

.detail-card { background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;overflow:hidden; }
.dc-head { padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:8px;font-size:13px;font-weight:800;color:#0f172a; }
.dc-head svg { flex-shrink:0;opacity:.5; }

.info-row { display:flex;align-items:baseline;padding:13px 20px;border-bottom:1px solid #f8fafc; }
.info-row:last-child { border-bottom:none; }
.ir-label { width:140px;flex-shrink:0;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.4px; }
.ir-val   { font-size:13px;color:#0f172a;font-weight:600;flex:1; }

/* Status badge */
.s-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700; }
.s-approved { background:#dcfce7;color:#15803d; }
.s-pending  { background:#fef9c3;color:#92400e; }
.s-paid     { background:#dbeafe;color:#1d4ed8; }
.s-dot { width:6px;height:6px;border-radius:50%; }
.s-approved .s-dot { background:#16a34a; }
.s-pending  .s-dot { background:#d97706; }
.s-paid     .s-dot { background:#2563eb; }

/* Cat pill */
.cat-pill { font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;background:#eff6ff;color:#1d4ed8; }

/* Proof card */
.proof-img { width:100%;max-height:260px;object-fit:contain;border-radius:10px;border:1px solid #e2e8f0;cursor:zoom-in;transition:opacity .15s; }
.proof-img:hover { opacity:.88; }

/* Empty proof */
.proof-empty { padding:36px 20px;text-align:center;color:#94a3b8; }
.proof-empty svg { width:40px;height:40px;margin-bottom:10px;opacity:.3; }
</style>
@endpush

@section('content')
<div class="detail-wrap">

    {{-- Header --}}
    <div class="page-hdr">
        <div>
            <h1>Expense Detail</h1>
            <p>{{ $expense->voucher_no ?? 'Voucher #'.$expense->id }} &mdash; {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            @can('expense_edit')
            <a href="{{ route('expense.edit.view', $expense->id) }}" class="btn-edit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                Edit
            </a>
            @endcan
            <a href="{{ route('expense.detail.pdf', $expense->id) }}" target="_blank" class="btn-pdf">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                PDF Voucher
            </a>
            <a href="{{ route('office_expenses.view') }}" class="btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Back
            </a>
        </div>
    </div>

    {{-- Hero Amount Strip --}}
    <div class="hero-strip">
        <div style="position:relative;z-index:1;">
            <div class="hero-voucher">{{ $expense->voucher_no ?? 'Voucher #'.$expense->id }}</div>
            <div class="hero-amount">PKR {{ number_format($expense->amount) }}</div>
            <div style="margin-top:8px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span class="hero-type {{ $expense->type ?? 'expense' }}">
                    {{ $expense->type === 'income' ? '📥' : ($expense->type === 'inventory' ? '📦' : '📤') }}
                    {{ ucfirst($expense->type ?? 'Expense') }}
                </span>
                <span style="font-size:12px;color:rgba(255,255,255,.55);">{{ $expense->category }}</span>
                <span style="font-size:12px;color:rgba(255,255,255,.3);">·</span>
                <span style="font-size:12px;color:rgba(255,255,255,.55);">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</span>
            </div>
        </div>
        <div style="position:relative;z-index:1;text-align:right;">
            @if($expense->status === 'approved')
                <span style="background:rgba(34,197,94,.15);color:#86efac;padding:6px 16px;border-radius:20px;font-size:12px;font-weight:800;">✓ Approved</span>
            @elseif($expense->status === 'paid')
                <span style="background:rgba(59,130,246,.15);color:#93c5fd;padding:6px 16px;border-radius:20px;font-size:12px;font-weight:800;">✓ Paid</span>
            @else
                <span style="background:rgba(234,179,8,.15);color:#fde68a;padding:6px 16px;border-radius:20px;font-size:12px;font-weight:800;">⏳ Pending</span>
            @endif
        </div>
    </div>

    {{-- Fund Source Highlight --}}
    @php
        $fsMeta = [
            'plot_payments'   => ['label'=>'Plot Payments',   'icon'=>'🏘️','color'=>'#1d4ed8','bg'=>'#eff6ff','border'=>'#bfdbfe','sub'=>'Drawn from collected plot payment funds'],
            'security_fee'    => ['label'=>'Security Fee',    'icon'=>'🔒','color'=>'#7c3aed','bg'=>'#fdf4ff','border'=>'#ddd6fe','sub'=>'Drawn from collected security deposits'],
            'registry_fee'    => ['label'=>'Registry Fee',    'icon'=>'📋','color'=>'#0369a1','bg'=>'#e0f2fe','border'=>'#bae6fd','sub'=>'Drawn from collected registry charges'],
            'development_fee' => ['label'=>'Development Fee', 'icon'=>'🏗️','color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#bbf7d0','sub'=>'Drawn from collected development charges'],
        ];
        $fsInfo = $expense->fund_source ? ($fsMeta[$expense->fund_source] ?? null) : null;
    @endphp

    @if($fsInfo)
    <div class="fund-highlight" style="background:{{ $fsInfo['bg'] }};border-color:{{ $fsInfo['border'] }};color:{{ $fsInfo['color'] }};">
        <span class="fh-icon">{{ $fsInfo['icon'] }}</span>
        <div style="flex:1;">
            <div class="fh-label">Fund Source</div>
            <div class="fh-name" style="color:{{ $fsInfo['color'] }};">{{ $fsInfo['label'] }}</div>
            <div class="fh-sub">{{ $fsInfo['sub'] }}</div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            @php
                $usedFromSource = \App\Models\OfficeExpense::where('type','expense')
                    ->where('status','approved')
                    ->where('fund_source', $expense->fund_source)
                    ->sum('amount');
                $fsLabels = ['plot_payments'=>'plot_payments','security_fee'=>'security','registry_fee'=>'registry','development_fee'=>'development'];
                $feeKey = $fsLabels[$expense->fund_source] ?? null;
                if ($expense->fund_source === 'plot_payments') {
                    $validIds = \App\Models\Booking::where('status','!=','cancelled')->pluck('id');
                    $totalCollected = \App\Models\PlotPayment::whereIn('booking_id',$validIds)->where('status','paid')->sum('amount_paid');
                } elseif($feeKey) {
                    $totalCollected = \App\Models\BookingFee::where('fee_type',$feeKey)->sum('paid_amount');
                } else {
                    $totalCollected = 0;
                }
                $remaining = max(0, $totalCollected - $usedFromSource);
            @endphp
            <div style="font-size:10px;opacity:.65;margin-bottom:4px;">Total used from this fund</div>
            <div style="font-size:20px;font-weight:800;color:{{ $fsInfo['color'] }};">PKR {{ number_format($usedFromSource) }}</div>
            <div style="font-size:10px;opacity:.6;margin-top:2px;">PKR {{ number_format($remaining) }} remaining in fund</div>
        </div>
    </div>
    @else
    <div style="background:#f8fafc;border:1.5px dashed #e2e8f0;border-radius:14px;padding:14px 20px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:12px;color:#94a3b8;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
        No fund source assigned to this record. Fund source is used to track which payment pool covers this expense.
    </div>
    @endif

    {{-- Main Grid --}}
    <div class="detail-grid">

        {{-- Left: Transaction Info --}}
        <div>
            <div class="detail-card">
                <div class="dc-head">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    Transaction Details
                </div>

                <div class="info-row">
                    <span class="ir-label">Voucher No</span>
                    <span class="ir-val" style="font-family:monospace;color:#1d4ed8;font-weight:800;font-size:14px;">{{ $expense->voucher_no ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Date</span>
                    <span class="ir-val">
                        {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                        <span style="font-size:11px;color:#94a3b8;margin-left:6px;">{{ \Carbon\Carbon::parse($expense->expense_date)->diffForHumans() }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Type</span>
                    <span class="ir-val">
                        @if($expense->type === 'expense')
                            <span style="background:#fef2f2;color:#dc2626;font-size:11px;font-weight:800;padding:3px 10px;border-radius:20px;">📤 Expense</span>
                        @elseif($expense->type === 'income')
                            <span style="background:#f0fdf4;color:#16a34a;font-size:11px;font-weight:800;padding:3px 10px;border-radius:20px;">📥 Income</span>
                        @else
                            <span style="background:#ecfdf5;color:#059669;font-size:11px;font-weight:800;padding:3px 10px;border-radius:20px;">📦 Inventory</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Category</span>
                    <span class="ir-val"><span class="cat-pill">{{ $expense->category }}</span></span>
                </div>
                <div class="info-row">
                    <span class="ir-label">{{ $expense->type === 'income' ? 'Received From' : 'Paid To' }}</span>
                    <span class="ir-val" style="font-weight:700;">{{ $expense->paid_to ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Amount</span>
                    <span class="ir-val" style="font-size:18px;font-weight:800;color:{{ $expense->type === 'income' ? '#16a34a' : '#dc2626' }};">
                        PKR {{ number_format($expense->amount) }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Payment Method</span>
                    <span class="ir-val">
                        <span style="background:#f1f5f9;color:#475569;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">{{ $expense->payment_method ?? '—' }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Reference No</span>
                    <span class="ir-val" style="font-family:monospace;color:#475569;">{{ $expense->reference_no ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Status</span>
                    <span class="ir-val">
                        @if($expense->status === 'approved')
                            <span class="s-badge s-approved"><span class="s-dot"></span>Approved</span>
                        @elseif($expense->status === 'paid')
                            <span class="s-badge s-paid"><span class="s-dot"></span>Paid</span>
                        @else
                            <span class="s-badge s-pending"><span class="s-dot"></span>Pending</span>
                        @endif
                    </span>
                </div>
                @if($expense->remarks)
                <div class="info-row" style="align-items:flex-start;">
                    <span class="ir-label" style="padding-top:2px;">Remarks</span>
                    <span class="ir-val" style="color:#64748b;line-height:1.6;">{{ $expense->remarks }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Fund Source mini card --}}
            @if($fsInfo)
            <div style="background:{{ $fsInfo['bg'] }};border:1.5px solid {{ $fsInfo['border'] }};border-radius:14px;padding:18px;">
                <div style="font-size:10px;font-weight:700;color:{{ $fsInfo['color'] }};text-transform:uppercase;letter-spacing:.6px;margin-bottom:12px;">Fund Source Breakdown</div>
                <div style="display:grid;grid-template-columns:1fr;gap:10px;">
                    <div style="background:#fff;border-radius:10px;padding:10px 14px;border:1px solid {{ $fsInfo['border'] }};">
                        <div style="font-size:9px;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:3px;">Total Collected</div>
                        <div style="font-size:15px;font-weight:800;color:#16a34a;">PKR {{ number_format($totalCollected) }}</div>
                    </div>
                    <div style="background:#fff;border-radius:10px;padding:10px 14px;border:1px solid {{ $fsInfo['border'] }};">
                        <div style="font-size:9px;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:3px;">Total Used in Expenses</div>
                        <div style="font-size:15px;font-weight:800;color:#dc2626;">PKR {{ number_format($usedFromSource) }}</div>
                    </div>
                    <div style="background:#fff;border-radius:10px;padding:10px 14px;border:1px solid {{ $fsInfo['border'] }};">
                        <div style="font-size:9px;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:3px;">Remaining in Fund</div>
                        <div style="font-size:15px;font-weight:800;color:{{ $fsInfo['color'] }};">PKR {{ number_format($remaining) }}</div>
                    </div>
                </div>
                @php $pct = $totalCollected > 0 ? min(100, round($usedFromSource / $totalCollected * 100)) : 0; @endphp
                <div style="margin-top:12px;">
                    <div style="display:flex;justify-content:space-between;font-size:10px;color:{{ $fsInfo['color'] }};font-weight:700;margin-bottom:4px;">
                        <span>Fund Usage</span><span>{{ $pct }}% used</span>
                    </div>
                    <div style="background:rgba(0,0,0,.06);border-radius:20px;height:6px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $fsInfo['color'] }};border-radius:20px;"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Payment Proof --}}
            <div class="detail-card">
                <div class="dc-head">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                    Payment Proof
                </div>
                @if($expense->payment_proof)
                    <div style="padding:14px;">
                        <a href="{{ asset('storage/officeExpensesProof/'.$expense->payment_proof) }}" target="_blank">
                            <img src="{{ asset('storage/officeExpensesProof/'.$expense->payment_proof) }}"
                                 class="proof-img" alt="Payment Proof">
                        </a>
                        <p style="font-size:11px;color:#94a3b8;text-align:center;margin:8px 0 0;">Click to open full size</p>
                    </div>
                @else
                    <div class="proof-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display:block;margin:0 auto 10px;opacity:.2;width:40px;height:40px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                        <p style="font-size:13px;font-weight:700;color:#94a3b8;margin:0 0 4px;">No proof uploaded</p>
                        <p style="font-size:11px;color:#cbd5e1;margin:0;">No receipt or proof was attached</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>
@endsection
