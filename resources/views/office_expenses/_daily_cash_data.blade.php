{{-- Summary Cards --}}
<div class="summary-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
    <div class="sum-card" style="--accent:#16a34a;">
        <div class="sum-label">Plot Collections</div>
        <div class="sum-val" style="color:#16a34a;">PKR {{ number_format($totalPlotIncome) }}</div>
        <div class="sum-sub">{{ $plotPayments->count() }} payment(s)</div>
    </div>
    <div class="sum-card" style="--accent:#0369a1;">
        <div class="sum-label">Registry Collections</div>
        <div class="sum-val" style="color:#0369a1;">PKR {{ number_format($totalRegistryIncome) }}</div>
        <div class="sum-sub">{{ $registryPayments->count() }} entry(s)</div>
    </div>
    <div class="sum-card" style="--accent:#ca8a04;">
        <div class="sum-label">Fee Collections</div>
        <div class="sum-val" style="color:#ca8a04;">PKR {{ number_format($totalOtherFeeIncome + $totalMiscPayments) }}</div>
        <div class="sum-sub">{{ $feePayments->count() + $miscPayments->count() }} entry(s)</div>
    </div>
    <div class="sum-card" style="--accent:#0891b2;">
        <div class="sum-label">Transfers Period</div>
        <div class="sum-val" style="color:#0891b2;">{{ $transfers->count() }}</div>
        <div class="sum-sub">deed(s) — ownership changed</div>
    </div>
    <div class="sum-card" style="--accent:#dc2626;">
        <div class="sum-label">Office Expenses</div>
        <div class="sum-val" style="color:#dc2626;">PKR {{ number_format($totalExpenses) }}</div>
        <div class="sum-sub">{{ $expenses->count() }} entry(s)</div>
    </div>
    <div class="sum-card" style="--accent:#7c3aed;">
        <div class="sum-label">Inventory Out</div>
        <div class="sum-val" style="color:#7c3aed;">PKR {{ number_format($totalInventory) }}</div>
        <div class="sum-sub">{{ $inventories->count() }} item(s)</div>
    </div>
    <div class="sum-card" style="--accent:{{ $netBalance >= 0 ? '#16a34a' : '#dc2626' }};">
        <div class="sum-label">Total Cash In</div>
        <div class="sum-val" style="color:#1e3a8a;">PKR {{ number_format($totalIncome) }}</div>
        <div class="sum-sub">Plots + Registry + Fees</div>
    </div>
</div>

{{-- NET Balance Banner --}}
@php $netClass = $netBalance > 0 ? 'positive' : ($netBalance < 0 ? 'negative' : 'zero'); @endphp
<div class="net-card {{ $netClass }}">
    <div>
        <div style="font-size:11px;font-weight:700;color:var(--slate);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Net Balance for {{ $isSingleDay ? $startDateObj->format('d M Y') : $startDateObj->format('d M Y') . ' to ' . $endDateObj->format('d M Y') }}</div>
        <div style="font-size:28px;font-weight:900;color:{{ $netBalance >= 0 ? '#15803d' : '#dc2626' }};">
            {{ $netBalance >= 0 ? '+' : '' }}PKR {{ number_format($netBalance) }}
        </div>
        <div style="font-size:11px;color:var(--slate);margin-top:3px;">
            Cash In PKR {{ number_format($totalIncome) }}
            (Plots PKR {{ number_format($totalPlotIncome) }}
            + Registry PKR {{ number_format($totalRegistryIncome) }}
            + Fees PKR {{ number_format($totalOtherFeeIncome + $totalMiscPayments) }}) —
            Cash Out PKR {{ number_format($totalExpenses + $totalInventory) }}
        </div>
    </div>
    <div style="font-size:36px;">{{ $netBalance >= 0 ? '✅' : '⚠️' }}</div>
</div>

{{-- PLOT PAYMENTS --}}
<div class="section-card">
    <div class="sec-head">
        <div class="sec-head-icon" style="background:#f0fdf4;"><i class="bi bi-house-fill" style="color:#16a34a;"></i></div>
        <div>
            <div class="sec-head-title">Plot Payment Collections</div>
            <div class="sec-head-sub">Core payments: Down Payment, Installments, etc.</div>
        </div>
        <div class="sec-head-amt" style="color:#16a34a;">PKR {{ number_format($totalPlotIncome) }}</div>
    </div>
    <table class="ledger-table">
        <thead><tr>
            <th>#</th><th>Booking Ref</th><th>Customer</th><th>Plot</th>
            <th>Category</th><th>Method</th><th style="text-align:right;">Amount</th>
        </tr></thead>
        <tbody>
            @forelse($plotPayments as $p)
            <tr>
                <td style="color:var(--slate);font-size:11px;">{{ $loop->iteration }}</td>
                <td><strong style="color:var(--blue);font-size:11px;font-family:monospace;">{{ $p->booking->customer_booking_id ?? '—' }}</strong></td>
                <td style="font-weight:600;">{{ $p->booking->customer->name ?? '—' }}</td>
                <td style="font-size:11px;color:var(--slate);">Plot #{{ $p->booking->plot->plot_number ?? '—' }}</td>
                <td><span class="badge-pill bp-green">{{ ucwords(str_replace('_',' ',$p->payment_category)) }}</span></td>
                <td><span style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $p->payment_type }}</span></td>
                <td style="text-align:right;font-weight:800;color:#16a34a;">PKR {{ number_format($p->amount_paid) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="empty-row"><i class="bi bi-inbox" style="font-size:20px;display:block;margin-bottom:6px;"></i>No core plot payments received in this period</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- REGISTRY PAYMENTS --}}
<div class="section-card">
    <div class="sec-head">
        <div class="sec-head-icon" style="background:#eff6ff;"><i class="bi bi-file-earmark-text-fill" style="color:#0369a1;"></i></div>
        <div>
            <div class="sec-head-title">Registry Fee Collections</div>
            <div class="sec-head-sub">Government and legal registry fees</div>
        </div>
        <div class="sec-head-amt" style="color:#0369a1;">PKR {{ number_format($totalRegistryIncome) }}</div>
    </div>
    <table class="ledger-table">
        <thead><tr>
            <th>#</th><th>Booking Ref</th><th>Customer</th><th>Plot</th><th>Receipt No</th><th>Method</th><th style="text-align:right;">Amount</th>
        </tr></thead>
        <tbody>
            @forelse($registryPayments as $rp)
            <tr>
                <td style="color:var(--slate);font-size:11px;">{{ $loop->iteration }}</td>
                <td><strong style="color:var(--blue);font-size:11px;font-family:monospace;">{{ $rp->booking->customer_booking_id ?? '—' }}</strong></td>
                <td style="font-weight:600;">{{ $rp->booking->customer->name ?? '—' }}</td>
                <td style="font-size:11px;color:var(--slate);">Plot #{{ $rp->booking->plot->plot_number ?? '—' }}</td>
                <td style="font-size:11px;font-family:monospace;">{{ $rp->receipt_no ?? '—' }}</td>
                <td><span style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $rp->payment_mode ?? 'Cash' }}</span></td>
                <td style="text-align:right;font-weight:800;color:#0369a1;">PKR {{ number_format($rp->amount) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="empty-row"><i class="bi bi-inbox" style="font-size:20px;display:block;margin-bottom:6px;"></i>No registry fees received in this period</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- FEE COLLECTIONS (Development, Security, Transfer, Misc Payments) --}}
<div class="section-card">
    <div class="sec-head">
        <div class="sec-head-icon" style="background:#fefce8;"><i class="bi bi-receipt-cutoff" style="color:#ca8a04;"></i></div>
        <div>
            <div class="sec-head-title">Fee & Miscellaneous Collections</div>
            <div class="sec-head-sub">Development, Security, Transfer &amp; Other payments</div>
        </div>
        <div class="sec-head-amt" style="color:#ca8a04;">PKR {{ number_format($totalOtherFeeIncome + $totalMiscPayments) }}</div>
    </div>
    <table class="ledger-table">
        <thead><tr>
            <th>#</th><th>Type</th><th>Ref / Booking</th><th>Customer</th><th>Plot</th><th>Method</th><th>Notes</th><th style="text-align:right;">Amount</th>
        </tr></thead>
        <tbody>
            @php
                $feeTypeMeta = [
                    'development' => ['label'=>'Development Fee', 'color'=>'#16a34a','bg'=>'#f0fdf4'],
                    'security'    => ['label'=>'Security Fee',    'color'=>'#7c3aed','bg'=>'#fdf4ff'],
                    'transfer'    => ['label'=>'Transfer Fee',    'color'=>'#ca8a04','bg'=>'#fefce8'],
                ];
            @endphp
            
            {{-- 1. Standard Fees --}}
            @foreach($feePayments as $fp)
            @php
                $ft = $fp->bookingFee?->fee_type ?? 'other';
                $ftMeta = $feeTypeMeta[$ft] ?? ['label'=>ucfirst($ft),'color'=>'#475569','bg'=>'#f1f5f9'];
            @endphp
            <tr>
                <td style="color:var(--slate);font-size:11px;">{{ $loop->iteration }}</td>
                <td><span class="badge-pill" style="background:{{ $ftMeta['bg'] }};color:{{ $ftMeta['color'] }};">{{ $ftMeta['label'] }}</span></td>
                <td><strong style="color:var(--blue);font-size:11px;font-family:monospace;">{{ $fp->booking->customer_booking_id ?? '—' }}</strong></td>
                <td style="font-weight:600;">{{ $fp->booking->customer->name ?? '—' }}</td>
                <td style="font-size:11px;color:var(--slate);">Plot #{{ $fp->booking->plot->plot_number ?? '—' }}</td>
                <td><span style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $fp->payment_mode ?? 'Cash' }}</span></td>
                <td style="font-size:11px;color:var(--slate);">{{ $fp->notes ?? '—' }}</td>
                <td style="text-align:right;font-weight:800;color:#ca8a04;">PKR {{ number_format($fp->amount) }}</td>
            </tr>
            @endforeach

            {{-- 2. Miscellaneous Payments (from PlotPayment) --}}
            @foreach($miscPayments as $mp)
            <tr>
                <td style="color:var(--slate);font-size:11px;">{{ $loop->iteration + $feePayments->count() }}</td>
                <td><span class="badge-pill" style="background:#fef3c7;color:#92400e;">Misc Payment</span></td>
                <td><strong style="color:var(--blue);font-size:11px;font-family:monospace;">{{ $mp->booking->customer_booking_id ?? '—' }}</strong></td>
                <td style="font-weight:600;">{{ $mp->booking->customer->name ?? '—' }}</td>
                <td style="font-size:11px;color:var(--slate);">{{ ucwords(str_replace('_',' ',$mp->payment_category)) }}</td>
                <td><span style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $mp->payment_type }}</span></td>
                <td style="font-size:11px;color:var(--slate);">{{ $mp->remarks ?? '—' }}</td>
                <td style="text-align:right;font-weight:800;color:#ca8a04;">PKR {{ number_format($mp->amount_paid) }}</td>
            </tr>
            @endforeach

            {{-- 3. Direct Transfer Fees --}}
            @foreach($directTransferFees as $dtr)
            <tr>
                <td style="color:var(--slate);font-size:11px;">{{ $loop->iteration + $feePayments->count() + $miscPayments->count() }}</td>
                <td><span class="badge-pill" style="background:#fefce8;color:#ca8a04;">Transfer Fee</span></td>
                <td><strong style="color:var(--blue);font-size:11px;font-family:monospace;">{{ $dtr->deed_no }}</strong></td>
                <td style="font-weight:600;">{{ $dtr->fromCustomer->name ?? '—' }} → {{ $dtr->toCustomer->name ?? '—' }}</td>
                <td style="font-size:11px;color:var(--slate);">Plot #{{ $dtr->plot->plot_number ?? '—' }}</td>
                <td><span style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $dtr->payment_method ?? '—' }}</span></td>
                <td style="font-size:11px;color:var(--slate);">Direct transfer payment</td>
                <td style="text-align:right;font-weight:800;color:#ca8a04;">PKR {{ number_format($dtr->transfer_fee) }}</td>
            </tr>
            @endforeach

            @if($feePayments->isEmpty() && $miscPayments->isEmpty() && $directTransferFees->isEmpty())
            <tr><td colspan="8" class="empty-row"><i class="bi bi-inbox" style="font-size:20px;display:block;margin-bottom:6px;"></i>No fee or miscellaneous payments in this period</td></tr>
            @endif
        </tbody>
    </table>
</div>

{{-- PLOT TRANSFERS --}}
<div class="section-card">
    <div class="sec-head">
        <div class="sec-head-icon" style="background:#ecfeff;"><i class="bi bi-arrow-left-right" style="color:#0891b2;"></i></div>
        <div>
            <div class="sec-head-title">Plot Transfers</div>
            <div class="sec-head-sub">Ownership / deed transfers completed today</div>
        </div>
        <div class="sec-head-amt" style="color:#0891b2;">{{ $transfers->count() }} deed(s)</div>
    </div>
    <table class="ledger-table">
        <thead><tr>
            <th>#</th><th>Deed No</th><th>Type</th><th>From</th><th>To</th><th>Plot</th><th>Transfer Fee</th><th>Fee Status</th><th>Status</th>
        </tr></thead>
        <tbody>
            @forelse($transfers as $tr)
            @php
                $typeColors = [
                    'ownership' => ['bg'=>'#eff6ff','color'=>'#1d4ed8','label'=>'Ownership'],
                    'swap'      => ['bg'=>'#f0fdf4','color'=>'#16a34a','label'=>'Swap'],
                    'partial'   => ['bg'=>'#fdf4ff','color'=>'#7c3aed','label'=>'Partial'],
                    'internal'  => ['bg'=>'#fafafa','color'=>'#475569','label'=>'Internal'],
                ];
                $tc = $typeColors[$tr->transfer_type] ?? ['bg'=>'#f1f5f9','color'=>'#475569','label'=>ucfirst($tr->transfer_type)];
                $feeStatusColor = match($tr->transfer_fee_status) {
                    'paid'   => '#16a34a',
                    'waived' => '#7c3aed',
                    default  => '#ca8a04',
                };
                $statusColor = match($tr->status) {
                    'completed' => '#16a34a',
                    'approved'  => '#1d4ed8',
                    default     => '#ca8a04',
                };
            @endphp
            <tr>
                <td style="color:var(--slate);font-size:11px;">{{ $loop->iteration }}</td>
                <td style="font-family:monospace;font-size:11px;font-weight:700;color:#0891b2;">{{ $tr->deed_no }}</td>
                <td><span class="badge-pill" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }};">{{ $tc['label'] }}</span></td>
                <td style="font-weight:600;font-size:12px;">{{ $tr->fromCustomer->name ?? '—' }}</td>
                <td style="font-weight:600;font-size:12px;">{{ $tr->toCustomer->name ?? '—' }}</td>
                <td style="font-size:11px;color:var(--slate);">Plot #{{ $tr->plot->plot_number ?? '—' }}</td>
                <td style="font-weight:700;color:#0891b2;">{{ $tr->transfer_fee > 0 ? 'PKR '.number_format($tr->transfer_fee) : '—' }}</td>
                <td><span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:{{ $feeStatusColor }}22;color:{{ $feeStatusColor }};">{{ ucfirst($tr->transfer_fee_status) }}</span></td>
                <td><span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:{{ $statusColor }}22;color:{{ $statusColor }};">{{ ucfirst($tr->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="9" class="empty-row"><i class="bi bi-inbox" style="font-size:20px;display:block;margin-bottom:6px;"></i>No transfers processed in this period</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- OFFICE EXPENSES --}}
<div class="section-card">
    <div class="sec-head">
        <div class="sec-head-icon" style="background:#fef2f2;"><i class="bi bi-arrow-up-circle-fill" style="color:#dc2626;"></i></div>
        <div>
            <div class="sec-head-title">Office Expenses</div>
            <div class="sec-head-sub">All approved expenditure for this period</div>
        </div>
        <div class="sec-head-amt" style="color:#dc2626;">PKR {{ number_format($totalExpenses) }}</div>
    </div>
    <table class="ledger-table">
        <thead><tr>
            <th>#</th><th>Category</th><th>Fund Source</th><th>Paid To</th><th>Method</th><th>Remarks</th><th style="text-align:right;">Amount</th>
        </tr></thead>
        <tbody>
            @forelse($expenses as $exp)
            @php $fsi = $exp->fund_source && isset($fsMeta[$exp->fund_source]) ? $fsMeta[$exp->fund_source] : null; @endphp
            <tr>
                <td style="color:var(--slate);font-size:11px;">{{ $loop->iteration }}</td>
                <td><span class="badge-pill bp-amber">{{ $exp->category }}</span></td>
                <td>
                    @if($fsi)
                        <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;background:{{ $fsi['bg'] }};color:{{ $fsi['color'] }};white-space:nowrap;">{{ $fsi['icon'] }} {{ $fsi['label'] }}</span>
                    @else
                        <span style="color:#cbd5e1;font-size:11px;">—</span>
                    @endif
                </td>
                <td style="font-weight:600;">{{ $exp->paid_to }}</td>
                <td><span style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $exp->payment_method }}</span></td>
                <td style="font-size:11px;color:var(--slate);">{{ $exp->remarks ?? '—' }}</td>
                <td style="text-align:right;font-weight:800;color:#dc2626;">PKR {{ number_format($exp->amount) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="empty-row"><i class="bi bi-inbox" style="font-size:20px;display:block;margin-bottom:6px;"></i>No expenses recorded in this period</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($expenses->count() > 0)
    <div style="padding:16px 20px;background:#fafbfc;border-top:1px solid #f1f5f9;">
        <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">Expense by Fund Source</div>
        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
            @foreach($dailyFundUsage as $key => $fs)
            <div style="display:flex;align-items:center;gap:8px;background:{{ $fs['bg'] }};border:1px solid {{ $fs['border'] }};border-radius:10px;padding:8px 14px;">
                <span style="font-size:15px;">{{ $fs['icon'] }}</span>
                <div>
                    <div style="font-size:10px;color:{{ $fs['color'] }};font-weight:700;">{{ $fs['label'] }}</div>
                    <div style="font-size:13px;font-weight:800;color:{{ $fs['color'] }};">PKR {{ number_format($fs['used']) }}</div>
                </div>
            </div>
            @endforeach
            @if($noFundExpenses > 0)
            <div style="display:flex;align-items:center;gap:8px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:8px 14px;">
                <span style="font-size:15px;">📂</span>
                <div>
                    <div style="font-size:10px;color:#64748b;font-weight:700;">No Fund Assigned</div>
                    <div style="font-size:13px;font-weight:800;color:#64748b;">PKR {{ number_format($noFundExpenses) }}</div>
                </div>
            </div>
            @endif
            @if(empty($dailyFundUsage) && $noFundExpenses === 0.0)
            <span style="font-size:12px;color:#94a3b8;">No fund sources assigned to expenses in this period.</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- INVENTORY RECORDS --}}
<div class="section-card">
    <div class="sec-head">
        <div class="sec-head-icon" style="background:#fdf4ff;"><i class="bi bi-box-seam-fill" style="color:#7c3aed;"></i></div>
        <div>
            <div class="sec-head-title">Inventory & Supplies</div>
            <div class="sec-head-sub">Materials and stock purchased in this period</div>
        </div>
        <div class="sec-head-amt" style="color:#7c3aed;">PKR {{ number_format($totalInventory) }}</div>
    </div>
    <table class="ledger-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Supplier / Item</th>
                <th>Method</th>
                <th>Notes</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $inv)
            <tr>
                <td style="color:var(--slate);font-size:11px;">{{ $loop->iteration }}</td>
                <td><span class="badge-pill bp-purple">{{ $inv->category }}</span></td>
                <td style="font-weight:600;">{{ $inv->paid_to }}</td>
                <td><span style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $inv->payment_method }}</span></td>
                <td style="font-size:11px;color:var(--slate);">{{ $inv->remarks ?? '—' }}</td>
                <td style="text-align:right;font-weight:800;color:#7c3aed;">PKR {{ number_format($inv->amount) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="empty-row">
                    <i class="bi bi-inbox" style="font-size:20px;display:block;margin-bottom:6px;"></i>
                    No inventory records for this period
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
