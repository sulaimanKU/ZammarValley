@extends('layouts.index')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --navy: #0f172a; --blue: #1e3a8a; --green: #16a34a;
    --lime: #dcfce7; --amber: #d97706; --red: #dc2626;
    --slate: #64748b; --border: #e8edf3; --bg: #f1f5fb;
    --card: #ffffff; --radius: 14px; --shadow: 0 2px 12px rgba(15,23,42,.07);
}
* { box-sizing: border-box; }
body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; }

.page-header {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
    border-radius: 18px; padding: 24px 28px; margin-bottom: 22px;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 14px; position: relative; overflow: hidden;
}
.page-header::before {
    content: ''; position: absolute; top: -50px; right: -30px;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,.04);
}
.page-header-title { font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0; position: relative; z-index: 1; }
.page-header-sub   { font-size: 11px; color: rgba(255,255,255,.5); margin: 4px 0 0; position: relative; z-index: 1; }

.stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
@media(max-width:700px) { .stat-row { grid-template-columns: 1fr 1fr; } }
.stat-card {
    background: var(--card); border-radius: var(--radius);
    border: 1px solid var(--border); box-shadow: var(--shadow);
    padding: 16px 18px; display: flex; align-items: center; gap: 13px;
}
.stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.stat-label { font-size: 10px; font-weight: 700; color: var(--slate); text-transform: uppercase; letter-spacing: .6px; }
.stat-val   { font-size: 19px; font-weight: 800; color: var(--navy); margin-top: 2px; }
.stat-sub   { font-size: 10px; color: var(--slate); margin-top: 2px; }

.main-card { background: var(--card); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
.card-toolbar { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
.card-title { font-size: 13px; font-weight: 800; color: var(--navy); margin: 0; }

.inc-table { width: 100%; border-collapse: collapse; min-width: 750px; }
.inc-table thead th { font-size: 10px; text-transform: uppercase; letter-spacing: .6px; color: var(--slate); font-weight: 700; background: #fafbfc; border-bottom: 1.5px solid var(--border); padding: 11px 14px; white-space: nowrap; }
.inc-table tbody td { padding: 11px 14px; border-bottom: 1px solid #f8fafc; font-size: 12px; vertical-align: middle; }
.inc-table tbody tr:last-child td { border-bottom: none; }
.inc-table tbody tr:hover { background: #f0fdf4; }
.inc-table tfoot td { padding: 12px 14px; background: #f0fdf4; border-top: 2px solid #86efac; font-weight: 800; }

.cat-pill { font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; white-space: nowrap; }
.cat-tube_well        { background: #eff6ff; color: #1d4ed8; }
.cat-rent_received    { background: #fdf4ff; color: #7c3aed; }
.cat-utility_recovery { background: #fff7ed; color: #ea580c; }
.cat-sale_proceeds    { background: #f0fdf4; color: #15803d; }
.cat-misc             { background: #fef9c3; color: #92400e; }
.cat-other            { background: #f1f5f9; color: #475569; }

.status-pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; }
.s-confirmed { background: #dcfce7; color: #15803d; }
.s-pending   { background: #fef9c3; color: #92400e; }
.s-dot { width: 6px; height: 6px; border-radius: 50%; }
.s-confirmed .s-dot { background: #16a34a; }
.s-pending   .s-dot { background: #d97706; }

.btn-green { background: #16a34a !important; color: #fff !important; padding: 9px 20px !important; border-radius: 10px !important; font-size: 13px !important; font-weight: 700 !important; display: inline-flex !important; align-items: center !important; gap: 7px !important; border: none !important; cursor: pointer !important; text-decoration: none !important; }
.btn-green:hover { background: #15803d !important; color: #fff !important; }

.flash { display: flex; align-items: flex-start; gap: 12px; padding: 13px 18px; border-radius: 12px; margin-bottom: 16px; font-size: 13px; font-weight: 600; }
.flash-success { background: #f0fdf4; border: 1px solid #86efac; color: #15803d; }
.flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }

.modal-content { border: none; border-radius: 16px; overflow: hidden; }
.modal-header  { background: linear-gradient(135deg, #064e3b, #065f46); padding: 16px 22px; border: none; }
.modal-title   { font-size: 14px; font-weight: 800; color: #fff; }
.modal-body    { padding: 22px; }
.modal-footer  { background: #f8fafc; border-top: 1px solid var(--border); padding: 14px 22px; display: flex; justify-content: flex-end; gap: 10px; }
.form-label-c  { font-size: 11px; font-weight: 700; color: var(--slate); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; display: block; }
.form-control, .form-select { border: 1.5px solid var(--border); border-radius: 9px; font-size: 13px; padding: 8px 12px; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color .15s; }
.form-control:focus, .form-select:focus { border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.08); }

.empty-state { text-align: center; padding: 50px 20px; color: var(--slate); }
.empty-state i { font-size: 2.5rem; opacity: .2; display: block; margin-bottom: 12px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">

{{-- Header --}}
<div class="page-header">
    <div>
        <p class="page-header-title"><i class="bi bi-arrow-down-circle-fill" style="margin-right:8px;opacity:.7;"></i>Office Income</p>
        <p class="page-header-sub">Track Tube Well, rent, and other non-plot income</p>
    </div>
    <div style="display:flex;gap:10px;position:relative;z-index:1;flex-wrap:wrap;">
        <a href="{{ route('reports.daily_cash') }}" style="background:rgba(255,255,255,.15);color:#fff;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="bi bi-calendar-day"></i> Daily Cash
        </a>
        <a href="{{ route('reports.monthly_summary') }}" style="background:rgba(255,255,255,.15);color:#fff;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="bi bi-bar-chart"></i> Monthly Report
        </a>
        <button class="btn-green" data-bs-toggle="modal" data-bs-target="#addIncomeModal">
            <i class="bi bi-plus-lg"></i> New Income
        </button>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="flash flash-success"><i class="bi bi-check-circle-fill" style="flex-shrink:0;"></i>{{ session('success') }}<button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button></div>
@endif
@if(session('error'))
<div class="flash flash-error"><i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;"></i>{{ session('error') }}<button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button></div>
@endif

{{-- Stat Cards --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;"><i class="bi bi-arrow-down-circle-fill" style="color:#16a34a;"></i></div>
        <div>
            <div class="stat-label">This Month</div>
            <div class="stat-val" style="color:#16a34a;">PKR {{ number_format($total_month) }}</div>
            <div class="stat-sub">{{ date('F Y') }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;"><i class="bi bi-bank" style="color:#1d4ed8;"></i></div>
        <div>
            <div class="stat-label">All Time</div>
            <div class="stat-val">PKR {{ number_format($total_all) }}</div>
            <div class="stat-sub">Total confirmed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;"><i class="bi bi-droplet-fill" style="color:#0369a1;"></i></div>
        <div>
            <div class="stat-label">Tube Well</div>
            <div class="stat-val">PKR {{ number_format($by_category['tube_well']->total ?? 0) }}</div>
            <div class="stat-sub">All time</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fdf4ff;"><i class="bi bi-house-fill" style="color:#7c3aed;"></i></div>
        <div>
            <div class="stat-label">Rent Received</div>
            <div class="stat-val">PKR {{ number_format($by_category['rent_received']->total ?? 0) }}</div>
            <div class="stat-sub">All time</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="main-card">
    <div class="card-toolbar">
        <div>
            <p class="card-title">All Income Records</p>
            <p style="font-size:11px;color:var(--slate);margin:2px 0 0;">{{ $incomes->count() }} records</p>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="inc-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center;">#</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Received From</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Ref #</th>
                    <th>Remarks</th>
                    <th>Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $inc)
                @php
                    $catClass = 'cat-' . $inc->category;
                @endphp
                <tr>
                    <td style="text-align:center;color:var(--slate);font-size:11px;">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:var(--navy);">{{ \Carbon\Carbon::parse($inc->income_date)->format('d M Y') }}</div>
                        <div style="font-size:10px;color:var(--slate);">{{ \Carbon\Carbon::parse($inc->income_date)->diffForHumans() }}</div>
                    </td>
                    <td><span class="cat-pill {{ $catClass }}">{{ $inc->category_label }}</span></td>
                    <td>
                        <div style="font-weight:700;font-size:12px;color:var(--navy);">{{ $inc->received_from }}</div>
                    </td>
                    <td><strong style="color:#16a34a;font-size:14px;">PKR {{ number_format($inc->amount) }}</strong></td>
                    <td><span style="font-size:11px;font-weight:700;background:#f1f5f9;color:var(--slate);padding:3px 10px;border-radius:20px;">{{ $inc->payment_method }}</span></td>
                    <td><span style="font-family:monospace;font-size:11px;color:var(--slate);">{{ $inc->reference_no ?? '—' }}</span></td>
                    <td style="max-width:130px;"><span style="font-size:11px;color:var(--slate);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">{{ $inc->remarks ?? '—' }}</span></td>
                    <td>
                        <span class="status-pill {{ $inc->status === 'confirmed' ? 's-confirmed' : 's-pending' }}">
                            <span class="s-dot"></span>{{ ucfirst($inc->status) }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <form action="{{ route('office_income.destroy', $inc->id) }}" method="POST"
                              onsubmit="return confirm('Delete this income record?')" style="margin:0;display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" style="width:30px;height:30px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:13px;cursor:pointer;" title="Delete">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <i class="bi bi-arrow-down-circle"></i>
                            <p style="font-size:14px;font-weight:700;margin:0 0 6px;">No income records yet</p>
                            <p style="font-size:12px;margin:0;">Click "New Income" to add Tube Well or other income.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($incomes->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;font-size:10px;font-weight:700;color:var(--slate);letter-spacing:.5px;">TOTAL INCOME</td>
                    <td style="font-size:15px;color:#16a34a;">PKR {{ number_format($incomes->sum('amount')) }}</td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ADD INCOME MODAL --}}
<div class="modal fade" id="addIncomeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle-fill me-2"></i>New Income Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addIncomeForm" action="{{ route('office_income.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-c">Income Date <span style="color:#dc2626;">*</span></label>
                            <input type="date" name="income_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-c">Category <span style="color:#dc2626;">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">— Select —</option>
                                <option value="tube_well">Tube Well</option>
                                <option value="rent_received">Rent Received</option>
                                <option value="utility_recovery">Utility Recovery</option>
                                <option value="sale_proceeds">Sale Proceeds</option>
                                <option value="misc">Miscellaneous</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-c">Received From <span style="color:#dc2626;">*</span></label>
                            <input type="text" name="received_from" class="form-control" placeholder="Person or organization" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-c">Amount (PKR) <span style="color:#dc2626;">*</span></label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:11px;font-weight:700;color:var(--slate);pointer-events:none;">PKR</span>
                                <input type="number" name="amount" class="form-control" style="padding-left:42px;" placeholder="0" min="1" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-c">Payment Method <span style="color:#dc2626;">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-c">Reference No.</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-c">Status</label>
                            <select name="status" class="form-select">
                                <option value="confirmed">Confirmed / Received</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-c">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Optional note..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addIncomeForm" class="btn-green">
                    <i class="bi bi-check-lg"></i> Save Income
                </button>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
