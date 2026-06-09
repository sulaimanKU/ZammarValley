@extends('layouts.index')

@push('styles')
<style>
.create-wrap { max-width: 820px; margin: 0 auto; padding: 0 16px 60px; }
.page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px; }
.page-hdr h1 { font-size:20px;font-weight:800;color:#0f172a;margin:0 0 3px; }
.page-hdr p  { font-size:13px;color:#64748b;margin:0; }
.create-card { background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden; }
.create-card-head { padding:20px 26px;border-bottom:1px solid #f1f5f9; }
.create-card-body { padding:26px; }

/* 3-column type row */
.type-row { display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:24px; }
@media(max-width:560px){ .type-row { grid-template-columns:1fr; } }
.type-opt input { display:none; }
.type-opt-box {
    border:2px solid #e2e8f0;border-radius:12px;padding:16px;text-align:center;
    cursor:pointer;transition:all .15s;background:#fafafa;user-select:none;
}
.type-opt-box:hover { border-color:#94a3b8;background:#f8fafc; }
.type-opt input:checked + .type-opt-box { transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.08); }
.type-opt.expense   input:checked + .type-opt-box { border-color:#dc2626;background:#fef2f2; }
.type-opt.income    input:checked + .type-opt-box { border-color:#16a34a;background:#f0fdf4; }
.type-opt.inventory input:checked + .type-opt-box { border-color:#7c3aed;background:#fdf4ff; }
.type-opt-icon  { font-size:24px;display:block;margin-bottom:7px; }
.type-opt-label { font-size:13px;font-weight:800;color:#0f172a; }
.type-opt-desc  { font-size:10px;color:#94a3b8;margin-top:3px; }
.type-opt.expense   input:checked + .type-opt-box .type-opt-label { color:#dc2626; }
.type-opt.income    input:checked + .type-opt-box .type-opt-label { color:#16a34a; }
.type-opt.inventory input:checked + .type-opt-box .type-opt-label { color:#7c3aed; }

/* Fields */
.field-grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:18px; }
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
.btn-submit.income-mode    { background:linear-gradient(135deg,#064e3b,#16a34a); }
.btn-submit.inventory-mode { background:linear-gradient(135deg,#4c1d95,#7c3aed); }
.btn-cancel { background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 18px;font-size:13px;font-weight:700;text-decoration:none;transition:border-color .15s;display:inline-flex;align-items:center;gap:6px; }
.btn-cancel:hover { border-color:#94a3b8;color:#374151; }

/* Inventory info strip */
.inv-info { background:#fdf4ff;border:1px solid #e9d5ff;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:12px;color:#6b21a8;display:flex;align-items:flex-start;gap:10px; }

/* Fund source field */
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
</style>
@endpush

@section('content')
<div class="create-wrap">
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

@endif
@if(session('success'))
<div class="flash flash-success">
    <i class="bi bi-check-circle-fill" style="flex-shrink:0;"></i>
    {{ session('success') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button>
</div>
@endif
@if(session('error'))
<div class="flash flash-error">
    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;"></i>
    {{ session('error') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button>
</div>
@endif
@if($errors->any())
<div class="flash flash-error">
    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;"></i>
    <div><strong>Fix:</strong><ul style="margin:4px 0 0 14px;padding:0;font-size:12px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button>
</div>
@endif
    <div class="page-hdr">
        <div>
            <h1 id="pageTitle">New Office Expense</h1>
            <p>Select the type, fill in the details and save.</p>
        </div>
        <a href="{{ route('office_expenses.view') }}" class="btn-cancel">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back
        </a>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:12px;color:#dc2626;">
        <strong>Please fix:</strong>
        <ul style="margin:6px 0 0 16px;padding:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="create-card">
        <div class="create-card-head">
            <p style="font-size:14px;font-weight:800;color:#0f172a;margin:0 0 2px;">Office Expense / Income / Inventory</p>
            <p style="font-size:12px;color:#64748b;margin:0;">Select one of the three types below, then fill in the details.</p>
        </div>

        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" id="typeHidden" value="{{ old('type','expense') }}">

            <div class="create-card-body">

                {{-- ── 3 Type Options ── --}}
                <div class="type-row">

                    <label class="type-opt expense">
                        <input type="radio" name="_type_select" value="expense"
                               {{ old('type','expense') === 'expense' ? 'checked' : '' }}
                               onchange="setType('expense')">
                        <div class="type-opt-box">
                            <span class="type-opt-icon">📤</span>
                            <span class="type-opt-label">Expense</span>
                            <span class="type-opt-desc">Money going out (salaries, rent, utilities…)</span>
                        </div>
                    </label>

                    <label class="type-opt income">
                        <input type="radio" name="_type_select" value="income"
                               {{ old('type') === 'income' ? 'checked' : '' }}
                               onchange="setType('income')">
                        <div class="type-opt-box">
                            <span class="type-opt-icon">📥</span>
                            <span class="type-opt-label">Income</span>
                            <span class="type-opt-desc">Money coming in (tube well, rent, sales…)</span>
                        </div>
                    </label>

                    <label class="type-opt inventory">
                        <input type="radio" name="_type_select" value="inventory"
                               {{ old('type') === 'inventory' ? 'checked' : '' }}
                               onchange="setType('inventory')">
                        <div class="type-opt-box">
                            <span class="type-opt-icon">📦</span>
                            <span class="type-opt-label">Inventory</span>
                            <span class="type-opt-desc">Stock, materials & supplies tracking</span>
                        </div>
                    </label>

                </div>

                {{-- Inventory info strip (shown only for inventory) --}}
                <div class="inv-info" id="invInfo" style="display:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    <span>Inventory records track materials and supplies purchased or used. Enter the item name in <strong>Description</strong>, quantity value in <strong>Amount</strong>, and select the sub-category below.</span>
                </div>

                <div class="field-grid-2">

                    {{-- Date --}}
                    <div class="field-group">
                        <label class="fl">Date <span>*</span></label>
                        <input type="date" name="expense_date" class="fi"
                               value="{{ old('expense_date', date('Y-m-d')) }}" required>
                        @error('expense_date')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Category --}}
                    <div class="field-group">
                        <label class="fl" id="catLabel">Category <span>*</span></label>
                        <input type="text" name="category" id="categorySelect" class="fi"
                               placeholder="Enter category" value="{{ old('category') }}" required>
                        @error('category')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                </div>

                {{-- Fund Source (expense only) --}}
                <div id="fundSourceWrap" class="fund-source-wrap" style="display:none;">
                    <div class="fs-label">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                        Fund Source <span style="font-weight:400;color:#92400e;margin-left:4px;">— which payment pool covers this expense?</span>
                    </div>
                    <input type="hidden" name="fund_source" id="fundSourceHidden" value="{{ old('fund_source') }}">
                    <div class="fund-source-grid">

                        <label class="fs-opt" onclick="setFundSource('plot_payments')">
                            <input type="radio" name="_fs_select" value="plot_payments" {{ old('fund_source')==='plot_payments' ? 'checked' : '' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">🏘️</span>
                                <div>
                                    <div class="fs-opt-text">Plot Payments</div>
                                    <div class="fs-opt-sub">Down payment, installments, balance</div>
                                </div>
                            </div>
                        </label>

                        <label class="fs-opt" onclick="setFundSource('security_fee')">
                            <input type="radio" name="_fs_select" value="security_fee" {{ old('fund_source')==='security_fee' ? 'checked' : '' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">🔒</span>
                                <div>
                                    <div class="fs-opt-text">Security Fee</div>
                                    <div class="fs-opt-sub">Collected security deposits</div>
                                </div>
                            </div>
                        </label>

                        <label class="fs-opt" onclick="setFundSource('registry_fee')">
                            <input type="radio" name="_fs_select" value="registry_fee" {{ old('fund_source')==='registry_fee' ? 'checked' : '' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">📋</span>
                                <div>
                                    <div class="fs-opt-text">Registry Fee</div>
                                    <div class="fs-opt-sub">Collected registry charges</div>
                                </div>
                            </div>
                        </label>

                        <label class="fs-opt" onclick="setFundSource('development_fee')">
                            <input type="radio" name="_fs_select" value="development_fee" {{ old('fund_source')==='development_fee' ? 'checked' : '' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">🏗️</span>
                                <div>
                                    <div class="fs-opt-text">Development Fee</div>
                                    <div class="fs-opt-sub">Collected development charges</div>
                                </div>
                            </div>
                        </label>

                        <label class="fs-opt" onclick="setFundSource('transfer_fee')">
                            <input type="radio" name="_fs_select" value="transfer_fee" {{ old('fund_source')==='transfer_fee' ? 'checked' : '' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">🔄</span>
                                <div>
                                    <div class="fs-opt-text">Transfer Fee</div>
                                    <div class="fs-opt-sub">Collected plot transfer charges</div>
                                </div>
                            </div>
                        </label>

                        <label class="fs-opt" onclick="setFundSource('misc_income')">
                            <input type="radio" name="_fs_select" value="misc_income" {{ old('fund_source')==='misc_income' ? 'checked' : '' }}>
                            <div class="fs-opt-box">
                                <span class="fs-opt-icon">💰</span>
                                <div>
                                    <div class="fs-opt-text">Misc. Income</div>
                                    <div class="fs-opt-sub">Office income (rent, tube well, etc.)</div>
                                </div>
                            </div>
                        </label>

                    </div>
                </div>

                {{-- Party / Description --}}
                <div class="field-group">
                    <label class="fl" id="partyLabel">Paid To <span>*</span></label>
                    <input type="text" name="paid_to" class="fi" id="partyInput"
                           placeholder="Recipient name or company"
                           value="{{ old('paid_to') }}" required>
                    @error('paid_to')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                </div>

                <div class="field-grid-2">

                    {{-- Amount --}}
                    <div class="field-group">
                        <label class="fl" id="amountLabel">Amount (PKR) <span>*</span></label>
                        <div class="amount-wrap">
                            <span class="amount-prefix">PKR</span>
                            <input type="number" name="amount" class="fi has-prefix"
                                   placeholder="0" min="1" step="1"
                                   value="{{ old('amount') }}" required>
                        </div>
                        @error('amount')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Payment Method --}}
                    <div class="field-group">
                        <label class="fl">Payment Method <span>*</span></label>
                        <select name="payment_method" class="fi" required>
                            <option value="Cash"               {{ old('payment_method')==='Cash'               ?'selected':'' }}>Cash</option>
                            <option value="Bank Transfer"      {{ old('payment_method')==='Bank Transfer'      ?'selected':'' }}>Bank Transfer</option>
                            <option value="Cheque"             {{ old('payment_method')==='Cheque'             ?'selected':'' }}>Cheque</option>
                            <option value="JazzCash / EasyPaisa" {{ old('payment_method')==='JazzCash / EasyPaisa' ?'selected':'' }}>JazzCash / EasyPaisa</option>
                        </select>
                    </div>

                    {{-- Reference --}}
                    <div class="field-group">
                        <label class="fl">Reference / Cheque No. <small>(optional)</small></label>
                        <input type="text" name="reference_no" class="fi"
                               placeholder="REF-0001" value="{{ old('reference_no') }}">
                    </div>

                    {{-- Status --}}
                    <div class="field-group">
                        <label class="fl">Status</label>
                        <select name="status" class="fi">
                            <option value="approved" {{ old('status','approved')==='approved' ?'selected':'' }}>Approved / Confirmed</option>
                            <option value="pending"  {{ old('status')==='pending'             ?'selected':'' }}>Pending</option>
                        </select>
                    </div>

                </div>

                {{-- Proof --}}
                <div class="field-group">
                    <label class="fl">Payment Proof / Receipt <small>(optional)</small></label>
                    <input type="file" name="payment_proof" class="fi" accept="image/*,.pdf"
                           style="padding:8px 12px;cursor:pointer;">
                    <p style="font-size:10px;color:#94a3b8;margin:4px 0 0;">JPG, PNG or PDF — max 5MB</p>
                </div>

                {{-- Remarks --}}
                <div class="field-group" style="margin-bottom:0;">
                    <label class="fl" id="remarksLabel">Remarks <small>(optional)</small></label>
                    <textarea name="remarks" class="fi" rows="2"
                              placeholder="Short note...">{{ old('remarks') }}</textarea>
                </div>

            </div>

            <div class="submit-bar">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    <span id="submitText">Save Expense</span>
                </button>
                <a href="{{ route('office_expenses.view') }}" class="btn-cancel">Cancel</a>
                <span style="font-size:11px;color:#94a3b8;margin-left:auto;">Fields marked <span style="color:#dc2626;">*</span> are required</span>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
const typeConfig = {
    expense:   { title:'New Office Expense',   party:'Paid To',       partyPH:'Recipient name or company',   btn:'',               save:'Save Expense',   amount:'Amount (PKR)', remarks:'Remarks' },
    income:    { title:'New Office Income',    party:'Received From', partyPH:'Person or source of income', btn:'income-mode',    save:'Save Income',    amount:'Amount (PKR)', remarks:'Remarks' },
    inventory: { title:'New Inventory Record', party:'Supplier / Description', partyPH:'Supplier name or item description', btn:'inventory-mode', save:'Save Inventory', amount:'Total Value (PKR)', remarks:'Notes / Qty Details' },
};

function setFundSource(val) {
    document.getElementById('fundSourceHidden').value = val;
    document.querySelectorAll('input[name="_fs_select"]').forEach(function(r) {
        r.checked = r.value === val;
    });
}

function setType(type) {
    document.getElementById('typeHidden').value = type;

    const cfg = typeConfig[type];

    // Title
    document.getElementById('pageTitle').textContent   = cfg.title;
    document.getElementById('submitText').textContent  = cfg.save;
    document.getElementById('submitBtn').className     = 'btn-submit ' + cfg.btn;

    // Party label & placeholder
    document.getElementById('partyLabel').childNodes[0].nodeValue = cfg.party + ' ';
    document.getElementById('partyInput').placeholder = cfg.partyPH;

    // Amount label
    document.getElementById('amountLabel').childNodes[0].nodeValue = cfg.amount + ' ';

    // Remarks label
    document.getElementById('remarksLabel').childNodes[0].nodeValue = cfg.remarks + ' ';

    // Inventory info strip
    document.getElementById('invInfo').style.display = type === 'inventory' ? 'flex' : 'none';

    // Fund source (for expense AND inventory)
    document.getElementById('fundSourceWrap').style.display = (type === 'expense' || type === 'inventory') ? 'block' : 'none';
    if (type !== 'expense' && type !== 'inventory') {
        document.getElementById('fundSourceHidden').value = '';
        document.querySelectorAll('input[name="_fs_select"]').forEach(function(r) { r.checked = false; });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const oldType = document.getElementById('typeHidden').value || 'expense';
    setType(oldType);

    // Re-select old category after setType resets it
    @if(old('category'))
    document.getElementById('categorySelect').value = '{{ old('category') }}';
    @endif

    // Re-select old fund source
    @if(old('fund_source'))
    setFundSource('{{ old('fund_source') }}');
    @endif
});
</script>
@endpush

@endsection
