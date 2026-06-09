@extends('layouts.index')

@push('styles')
<style>
.edit-wrap { max-width: 820px; margin: 0 auto; padding: 0 16px 60px; }
.page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px; }
.page-hdr h1 { font-size:20px;font-weight:800;color:#0f172a;margin:0 0 3px; }
.page-hdr p  { font-size:13px;color:#64748b;margin:0; }
.edit-card { background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden; }
.edit-card-head { padding:20px 26px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px; }
.edit-card-body { padding:26px; }

/* Type pills (read-only display) */
.type-display { display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:800; }
.type-expense   { background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca; }
.type-income    { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.type-inventory { background:#ecfdf5;color:#059669;border:1.5px solid #a7f3d0; }

/* Fields */
.field-grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:18px; }
@media(max-width:560px){ .field-grid-2{ grid-template-columns:1fr; } }
.field-group  { margin-bottom:18px; }
.fl { font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;display:block; }
.fl span  { color:#dc2626; }
.fl small { color:#94a3b8;font-weight:400; }
.fi { width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:13px;color:#0f172a;font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s; }
.fi:focus { border-color:#1d4ed8;box-shadow:0 0 0 3px rgba(29,78,216,.08); }
textarea.fi { resize:vertical;min-height:80px; }
.amount-wrap { position:relative; }
.amount-prefix { position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:11px;font-weight:700;color:#64748b;pointer-events:none; }
.fi.has-prefix { padding-left:44px; }

/* Submit bar */
.submit-bar { display:flex;align-items:center;gap:12px;padding:18px 26px;background:#f8fafc;border-top:1px solid #f1f5f9;flex-wrap:wrap; }
.btn-submit { background:linear-gradient(135deg,#1e3a8a,#1d4ed8);color:#fff;border:none;border-radius:10px;padding:11px 28px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:7px;transition:opacity .15s; }
.btn-submit:hover { opacity:.9; }
.btn-cancel { background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 18px;font-size:13px;font-weight:700;text-decoration:none;transition:border-color .15s;display:inline-flex;align-items:center;gap:6px; }
.btn-cancel:hover { border-color:#94a3b8;color:#374151; }

/* Fund source */
.fund-source-wrap { background:#fff7ed;border:1.5px solid #fed7aa;border-radius:12px;padding:14px 16px;margin-bottom:18px; }
.fund-source-wrap .fs-label { font-size:11px;font-weight:800;color:#c2410c;margin-bottom:10px;display:flex;align-items:center;gap:6px; }
.fund-source-grid { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
@media(max-width:560px){ .fund-source-grid{ grid-template-columns:1fr; } }
.fs-opt input { display:none; }
.fs-opt-box { border:1.5px solid #e2e8f0;border-radius:10px;padding:12px 14px;cursor:pointer;transition:all .15s;background:#fff;display:flex;align-items:center;gap:10px;user-select:none; }
.fs-opt-box:hover { border-color:#94a3b8; }
.fs-opt input:checked + .fs-opt-box { border-color:#ea580c;background:#fff7ed; }
.fs-opt-icon { font-size:18px; }
.fs-opt-text { font-size:12px;font-weight:700;color:#0f172a; }
.fs-opt-sub  { font-size:10px;color:#94a3b8;margin-top:1px; }
.fs-opt input:checked + .fs-opt-box .fs-opt-text { color:#c2410c; }

/* Change impact strip */
.impact-strip { background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:13px 16px;margin-bottom:20px;font-size:12px;color:#92400e; }
.impact-strip strong { color:#78350f; }

/* Proof */
.proof-thumb { width:100%;max-height:180px;object-fit:contain;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:8px; }
</style>
@endpush

@section('content')
<div class="edit-wrap">

@if($errors->any())
<div class="alert alert-danger" style="border-radius:12px;font-size:13px;">
    <strong>Fix the following:</strong>
    <ul style="margin:6px 0 0 16px;padding:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

    <div class="page-hdr">
        <div>
            <h1>Edit Record</h1>
            <p>{{ $expense->voucher_no ?? 'Voucher #'.$expense->id }} &mdash; {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</p>
        </div>
        <a href="{{ route('expense.detail.view', $expense->id) }}" class="btn-cancel">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back
        </a>
    </div>

    {{-- Change impact notice for expenses with fund source --}}
    @if($expense->type === 'expense' && $expense->fund_source)
    @php
        $fsMeta = ['plot_payments'=>'Plot Payments','security_fee'=>'Security Fee','registry_fee'=>'Registry Fee','development_fee'=>'Development Fee'];
        $fsLabel = $fsMeta[$expense->fund_source] ?? $expense->fund_source;
    @endphp
    <div class="impact-strip">
        <strong>Fund Impact:</strong> This expense is charged to the <strong>{{ $fsLabel }}</strong> fund.
        Changing the amount or fund source will automatically adjust the fund balance shown on the expenses page.
    </div>
    @endif

    <div class="edit-card">
        <div class="edit-card-head">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;opacity:.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
            <div>
                <p style="font-size:14px;font-weight:800;color:#0f172a;margin:0 0 1px;">Edit Record</p>
                <p style="font-size:11px;color:#64748b;margin:0;">Update the fields below and save.</p>
            </div>
            {{-- Type badge (read-only) --}}
            <div style="margin-left:auto;">
                @if($expense->type === 'income')
                    <span class="type-display type-income">📥 Income</span>
                @elseif($expense->type === 'inventory')
                    <span class="type-display type-inventory">📦 Inventory</span>
                @else
                    <span class="type-display type-expense">📤 Expense</span>
                @endif
            </div>
        </div>

        <form action="{{ route('office_expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="edit-card-body">

                <div class="field-grid-2">
                    {{-- Date --}}
                    <div class="field-group">
                        <label class="fl">Date <span>*</span></label>
                        <input type="date" name="expense_date" class="fi"
                               value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                        @error('expense_date')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Category --}}
                    <div class="field-group">
                        <label class="fl">Category <span>*</span></label>
                        <select name="category" class="fi" required>
                            <option value="">— Select category —</option>
                            @php $currentCat = old('category', $expense->category); @endphp

                            @if($expense->type === 'expense')
                            <optgroup label="── Expense ──">
                                @foreach(['Salaries','Utilities','Marketing','Rent','Inventory','Others'] as $cat)
                                    <option value="{{ $cat }}" {{ $currentCat===$cat?'selected':'' }}>{{ $cat === 'Inventory' ? 'Inventory Purchase' : $cat }}</option>
                                @endforeach
                            </optgroup>
                            @elseif($expense->type === 'income')
                            <optgroup label="── Income ──">
                                @foreach(['Tube Well','Rent Received','Utility Recovery','Sale Proceeds','Misc'] as $cat)
                                    <option value="{{ $cat }}" {{ $currentCat===$cat?'selected':'' }}>{{ $cat }}</option>
                                @endforeach
                            </optgroup>
                            @else
                            <optgroup label="── Inventory ──">
                                @foreach(['Office Supplies','Construction Materials','Equipment & Tools','Furniture','IT & Electronics','Stationery','Cleaning Supplies','Inventory Others'] as $cat)
                                    <option value="{{ $cat }}" {{ $currentCat===$cat?'selected':'' }}>{{ $cat }}</option>
                                @endforeach
                            </optgroup>
                            @endif
                        </select>
                        @error('category')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Fund Source (expense only) --}}
                @if($expense->type === 'expense')
                <div class="fund-source-wrap">
                    <div class="fs-label">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                        Fund Source <span style="font-weight:400;color:#92400e;margin-left:4px;">— which payment pool covers this expense?</span>
                    </div>
                    @php $currentFs = old('fund_source', $expense->fund_source); @endphp
                    <input type="hidden" name="fund_source" id="fundSourceHidden" value="{{ $currentFs }}">
                    <div class="fund-source-grid">

                        <label class="fs-opt" onclick="setFundSource('plot_payments')">
                            <input type="radio" name="_fs_select" value="plot_payments" {{ $currentFs==='plot_payments'?'checked':'' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">🏘️</span>
                                <div>
                                    <div class="fs-opt-text">Plot Payments</div>
                                    <div class="fs-opt-sub">Down payment, installments, balance</div>
                                </div>
                            </div>
                        </label>

                        <label class="fs-opt" onclick="setFundSource('security_fee')">
                            <input type="radio" name="_fs_select" value="security_fee" {{ $currentFs==='security_fee'?'checked':'' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">🔒</span>
                                <div>
                                    <div class="fs-opt-text">Security Fee</div>
                                    <div class="fs-opt-sub">Collected security deposits</div>
                                </div>
                            </div>
                        </label>

                        <label class="fs-opt" onclick="setFundSource('registry_fee')">
                            <input type="radio" name="_fs_select" value="registry_fee" {{ $currentFs==='registry_fee'?'checked':'' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">📋</span>
                                <div>
                                    <div class="fs-opt-text">Registry Fee</div>
                                    <div class="fs-opt-sub">Collected registry charges</div>
                                </div>
                            </div>
                        </label>

                        <label class="fs-opt" onclick="setFundSource('development_fee')">
                            <input type="radio" name="_fs_select" value="development_fee" {{ $currentFs==='development_fee'?'checked':'' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">🏗️</span>
                                <div>
                                    <div class="fs-opt-text">Development Fee</div>
                                    <div class="fs-opt-sub">Collected development charges</div>
                                </div>
                            </div>
                        </label>

                    </div>
                    <div style="margin-top:10px;display:flex;align-items:center;gap:8px;">
                        <button type="button" onclick="clearFundSource()" style="font-size:11px;color:#94a3b8;background:none;border:none;cursor:pointer;padding:0;text-decoration:underline;">Clear fund source</button>
                    </div>
                </div>
                @else
                <input type="hidden" name="fund_source" value="">
                @endif

                {{-- Paid To --}}
                <div class="field-group">
                    <label class="fl">{{ $expense->type === 'income' ? 'Received From' : 'Paid To' }} <span>*</span></label>
                    <input type="text" name="paid_to" class="fi"
                           value="{{ old('paid_to', $expense->paid_to) }}" required>
                    @error('paid_to')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                </div>

                <div class="field-grid-2">
                    {{-- Amount --}}
                    <div class="field-group">
                        <label class="fl">Amount (PKR) <span>*</span></label>
                        <div class="amount-wrap">
                            <span class="amount-prefix">PKR</span>
                            <input type="number" name="amount" class="fi has-prefix"
                                   placeholder="0" min="1" step="1"
                                   value="{{ old('amount', $expense->amount) }}" required>
                        </div>
                        @error('amount')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Payment Method --}}
                    <div class="field-group">
                        <label class="fl">Payment Method <span>*</span></label>
                        <select name="payment_method" class="fi" required>
                            @foreach(['Cash','Bank Transfer','Cheque','JazzCash / EasyPaisa'] as $pm)
                                <option value="{{ $pm }}" {{ old('payment_method',$expense->payment_method)===$pm?'selected':'' }}>{{ $pm }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Reference --}}
                    <div class="field-group">
                        <label class="fl">Reference / Cheque No. <small>(optional)</small></label>
                        <input type="text" name="reference_no" class="fi"
                               value="{{ old('reference_no', $expense->reference_no) }}">
                    </div>

                    {{-- Status --}}
                    <div class="field-group">
                        <label class="fl">Status</label>
                        <select name="status" class="fi">
                            @php $currStatus = old('status', $expense->status); @endphp
                            <option value="approved" {{ $currStatus==='approved'?'selected':'' }}>Approved / Confirmed</option>
                            <option value="pending"  {{ $currStatus==='pending' ?'selected':'' }}>Pending</option>
                            <option value="paid"     {{ $currStatus==='paid'    ?'selected':'' }}>Paid</option>
                        </select>
                    </div>
                </div>

                {{-- Payment Proof --}}
                <div class="field-group">
                    <label class="fl">Payment Proof <small>(leave blank to keep existing)</small></label>
                    @if($expense->payment_proof)
                    <div style="margin-bottom:10px;">
                        <img src="{{ asset('storage/officeExpensesProof/'.$expense->payment_proof) }}" class="proof-thumb" alt="Current Proof">
                        <p style="font-size:10px;color:#94a3b8;margin:0;">Current receipt — uploading a new file will replace it</p>
                    </div>
                    @endif
                    <input type="file" name="payment_proof" class="fi" accept="image/*,.pdf" style="padding:8px 12px;cursor:pointer;">
                </div>

                {{-- Remarks --}}
                <div class="field-group" style="margin-bottom:0;">
                    <label class="fl">Remarks <small>(optional)</small></label>
                    <textarea name="remarks" class="fi" rows="2">{{ old('remarks', $expense->remarks) }}</textarea>
                </div>

            </div>

            <div class="submit-bar">
                <button type="submit" class="btn-submit">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Save Changes
                </button>
                <a href="{{ route('expense.detail.view', $expense->id) }}" class="btn-cancel">Cancel</a>

                {{-- Delete button --}}
                @can('expense_delete')
                <div style="margin-left:auto;">
                    <button type="button" onclick="confirmDelete()" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca;border-radius:10px;padding:10px 16px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-family:inherit;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        Delete Record
                    </button>
                </div>
                @endcan

                <span style="font-size:11px;color:#94a3b8;{{ auth()->user()->can('expense_delete') ? '' : 'margin-left:auto;' }}">Fields marked <span style="color:#dc2626;">*</span> are required</span>
            </div>
        </form>
    </div>

</div>

{{-- Delete Confirmation Modal --}}
@can('expense_delete')
<div id="deleteModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:18px;padding:28px;max-width:440px;width:calc(100% - 32px);box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:56px;height:56px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#dc2626" style="width:24px;height:24px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
            </div>
            <h3 style="font-size:17px;font-weight:800;color:#0f172a;margin:0 0 6px;">Delete this record?</h3>
            <p style="font-size:13px;color:#64748b;margin:0;">
                <strong style="color:#0f172a;">{{ $expense->voucher_no ?? 'Voucher #'.$expense->id }}</strong>
                &mdash; PKR {{ number_format($expense->amount) }}
            </p>
        </div>

        @if($expense->type === 'expense' && $expense->fund_source)
        @php
            $fsMeta2 = ['plot_payments'=>['label'=>'Plot Payments','color'=>'#1d4ed8','bg'=>'#eff6ff'],'security_fee'=>['label'=>'Security Fee','color'=>'#7c3aed','bg'=>'#fdf4ff'],'registry_fee'=>['label'=>'Registry Fee','color'=>'#0369a1','bg'=>'#e0f2fe'],'development_fee'=>['label'=>'Development Fee','color'=>'#16a34a','bg'=>'#f0fdf4']];
            $delFs = $fsMeta2[$expense->fund_source] ?? null;
        @endphp
        @if($delFs)
        <div style="background:{{ $delFs['bg'] }};border:1.5px solid;border-color:{{ $delFs['color'] }}22;border-radius:12px;padding:14px 16px;margin-bottom:18px;">
            <div style="font-size:10px;font-weight:700;color:{{ $delFs['color'] }};text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Fund Balance Impact</div>
            <div style="font-size:13px;color:#374151;line-height:1.6;">
                Deleting this expense will <strong style="color:#16a34a;">free up PKR {{ number_format($expense->amount) }}</strong>
                back into the <strong style="color:{{ $delFs['color'] }};">{{ $delFs['label'] }}</strong> fund balance automatically.
            </div>
        </div>
        @endif
        @endif

        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:11px 14px;margin-bottom:20px;font-size:12px;color:#dc2626;">
            This action is <strong>permanent</strong>. The record and any uploaded proof will be removed.
        </div>

        <div style="display:flex;gap:10px;">
            <button onclick="closeDeleteModal()" style="flex:1;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;padding:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                Cancel
            </button>
            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" style="flex:1;margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="width:100%;background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;border:none;border-radius:10px;padding:11px;font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
function setFundSource(val) {
    const hidden = document.getElementById('fundSourceHidden');
    if (hidden) hidden.value = val;
    document.querySelectorAll('input[name="_fs_select"]').forEach(function(r) {
        r.checked = r.value === val;
    });
}

function clearFundSource() {
    const hidden = document.getElementById('fundSourceHidden');
    if (hidden) hidden.value = '';
    document.querySelectorAll('input[name="_fs_select"]').forEach(function(r) { r.checked = false; });
}

function confirmDelete() {
    const modal = document.getElementById('deleteModal');
    if (modal) { modal.style.display = 'flex'; }
}
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) { modal.style.display = 'none'; }
}
// Close on backdrop click
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeDeleteModal();
        });
    }
});
</script>
@endpush

@endsection
