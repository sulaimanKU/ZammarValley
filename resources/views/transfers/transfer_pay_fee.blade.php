@extends('layouts.index')

@section('content')
@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800;900&display=swap');
.pf-wrap * { font-family:'DM Sans',sans-serif; }
.pf-wrap { padding:1.75rem;  box-sizing:border-box; max-width:100%; }

/* ── Header ── */
.pf-header { background:linear-gradient(135deg,#0f172a,#1e3a8a 60%,#3730a3); border-radius:18px; padding:24px 32px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; position:relative; overflow:hidden; }
.pf-header::before { content:'PAY FEE'; position:absolute; right:-10px; top:-8px; font-size:90px; font-weight:900; color:rgba(255,255,255,.03); pointer-events:none; white-space:nowrap; }
.pf-title { font-size:1.15rem; font-weight:800; color:#fff; margin:0; position:relative; z-index:1; }
.pf-sub   { font-size:12px; color:rgba(255,255,255,.45); margin:5px 0 0; position:relative; z-index:1; }
.btn-back { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2); color:#fff; padding:9px 16px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:7px; position:relative; z-index:1; }
.btn-back:hover { background:rgba(255,255,255,.2); color:#fff; }

/* ── Summary card ── */
.summary-card { background:linear-gradient(135deg,#0f172a,#1e3a8a); border-radius:14px; padding:24px; margin-bottom:22px; display:grid; grid-template-columns:1fr 1fr 1fr minmax(0,160px); gap:16px; align-items:center; width:100%; box-sizing:border-box; overflow:hidden; }
@media(max-width:1000px){ .summary-card { grid-template-columns:1fr 1fr; } }
@media(max-width:480px) { .summary-card { grid-template-columns:1fr; } }
.sc-item {}
.sc-label { font-size:9px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px; }
.sc-value { font-size:14px; font-weight:800; color:#fff; }
.sc-sub   { font-size:10.5px; color:rgba(255,255,255,.5); margin-top:3px; }
.fee-highlight { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); border-radius:12px; padding:14px 20px; text-align:center; min-width:140px; }
.fee-amount   { font-size:24px; font-weight:900; color:#fcd34d; line-height:1; }
.fee-label    { font-size:9px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1px; margin-top:5px; }

/* ── Auto-actions preview ── */
.actions-preview { background:#fff; border-radius:14px; border:1px solid #e4e9f2; padding:18px 22px; margin-bottom:22px; overflow:hidden; box-sizing:border-box; width:100%; }
.actions-title { font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.8px; margin-bottom:14px; }
.action-row { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f1f5f9; min-width:0; width:100%; }
.action-row:last-child { border-bottom:none; }
.action-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.action-icon svg { width:16px; height:16px; }
.action-text { flex:1; min-width:0; overflow:hidden; }
.action-main { font-size:12.5px; font-weight:700; color:#0f172a; }
.action-desc { font-size:11px; color:#94a3b8; margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.action-result { font-size:11px; font-weight:800; padding:3px 10px; border-radius:20px; white-space:nowrap; flex-shrink:0; }

/* ── Form card ── */
.form-card { background:#fff; border-radius:14px; border:1px solid #e4e9f2; box-shadow:0 2px 8px rgba(15,23,42,.04); overflow:hidden; margin-bottom:20px; }
.form-card-head { padding:16px 22px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:10px; }
.form-card-num  { width:26px; height:26px; border-radius:8px; background:#1e3a8a; color:#fff; font-size:12px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.form-card-title { font-size:13px; font-weight:800; color:#0f172a; }
.form-card-body  { padding:24px; }

/* ── Payment method selector ── */
.method-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:4px; }
@media(max-width:700px){ .method-grid { grid-template-columns:repeat(2,1fr); } }
.method-card { border:2px solid #e4e9f2; border-radius:12px; padding:14px 12px; text-align:center; cursor:pointer; transition:all .2s; position:relative; }
.method-card:hover { border-color:#1e3a8a; background:#f8fafc; }
.method-card.selected { border-color:var(--mc); background:var(--mb); }
.method-card input { position:absolute; opacity:0; width:0; height:0; }
.method-icon { font-size:22px; margin-bottom:8px; display:block; }
.method-label { font-size:12px; font-weight:800; color:#0f172a; }
.method-check { position:absolute; top:8px; right:8px; width:18px; height:18px; border-radius:50%; background:var(--mc); display:none; align-items:center; justify-content:center; }
.method-card.selected .method-check { display:flex; }
.method-check svg { width:10px; height:10px; color:#fff; }

/* ── Form controls ── */
.row-grid { display:grid; gap:16px; }
.row-grid.cols-2 { grid-template-columns:1fr 1fr; }
.row-grid.cols-3 { grid-template-columns:1fr 1fr 1fr; }
@media(max-width:640px){ .row-grid.cols-2,.row-grid.cols-3 { grid-template-columns:1fr; } }
.form-group { display:flex; flex-direction:column; }
.form-label { font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
.form-label span { color:#dc2626; }
.form-control,.form-select { border:1.5px solid #e2e8f0; border-radius:9px; font-size:13px; padding:10px 13px; color:#0f172a; font-family:'DM Sans',sans-serif; outline:none; transition:border-color .15s; width:100%; }
.form-control:focus,.form-select:focus { border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.08); }
.form-control.is-invalid { border-color:#dc2626; }
.invalid-feedback { font-size:11px; color:#dc2626; margin-top:4px; }

/* ── Proof upload ── */
.proof-upload-wrap { border:2px dashed #e2e8f0; border-radius:12px; padding:22px; text-align:center; cursor:pointer; transition:all .2s; position:relative; background:#fafbfc; }
.proof-upload-wrap:hover,.proof-upload-wrap.drag-over { border-color:#1e3a8a; background:#eff6ff; }
.proof-upload-wrap.has-file { border-color:#16a34a; border-style:solid; background:#f0fdf4; }
.proof-upload-wrap input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
.proof-upload-icon { width:44px; height:44px; border-radius:12px; background:#eff6ff; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; }
.proof-upload-icon svg { width:22px; height:22px; color:#1e3a8a; }
.proof-upload-title { font-size:13px; font-weight:700; color:#0f172a; margin-bottom:4px; }
.proof-upload-sub   { font-size:11px; color:#94a3b8; }
.proof-required-badge { display:inline-flex; align-items:center; gap:5px; background:#fef2f2; border:1px solid #fecaca; color:#dc2626; font-size:10px; font-weight:800; padding:3px 10px; border-radius:20px; margin-bottom:10px; }
.proof-optional-badge { display:inline-flex; align-items:center; gap:5px; background:#f0fdf4; border:1px solid #86efac; color:#16a34a; font-size:10px; font-weight:800; padding:3px 10px; border-radius:20px; margin-bottom:10px; }
.proof-preview { display:none; flex-direction:column; align-items:center; gap:10px; }
.proof-preview.show { display:flex; }
.proof-preview img { max-width:100%; max-height:200px; border-radius:10px; border:2px solid #86efac; object-fit:contain; }
.proof-preview-name { font-size:12px; font-weight:700; color:#16a34a; }
.proof-preview-change { font-size:11px; color:#94a3b8; text-decoration:underline; cursor:pointer; background:none; border:none; }

.btn-pay { background:linear-gradient(135deg,#16a34a,#15803d); color:#fff; padding:13px 36px; border-radius:12px; font-size:15px; font-weight:800; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:9px; box-shadow:0 4px 14px rgba(22,163,74,.3); transition:all .15s; }
.btn-pay:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(22,163,74,.4); }
.btn-cancel { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; padding:13px 24px; border-radius:12px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:7px; }
.btn-cancel:hover { background:#e2e8f0; }
</style>
@endpush

<div class="pf-wrap">

{{-- ── HEADER ── --}}
<div class="pf-header">
    <div>
        <p class="pf-title">Pay Transfer Fee</p>
        <p class="pf-sub">Deed {{ $transfer->deed_no }} — {{ $transfer->getTypeLabel() }}</p>
    </div>
    <a href="{{ route('index.transfer') }}" class="btn-back">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back
    </a>
</div>

{{-- ── TRANSFER SUMMARY ── --}}
<div class="summary-card">
    <div class="sc-item">
        <div class="sc-label">Transferor (Old Owner)</div>
        <div class="sc-value">{{ $transfer->fromCustomer->name ?? '—' }}</div>
        <div class="sc-sub">{{ $transfer->fromCustomer->cnic ?? '' }}</div>
    </div>
    <div class="sc-item">
        <div class="sc-label">
            @if($transfer->transfer_type === 'internal') Plot Updated To
            @elseif($transfer->transfer_type === 'swap')  Swapped With
            @else New Owner
            @endif
        </div>
        <div class="sc-value">{{ $transfer->toCustomer->name ?? ($transfer->transfer_type === 'internal' ? 'Same Owner' : '—') }}</div>
        <div class="sc-sub">{{ $transfer->toCustomer->cnic ?? '' }}</div>
    </div>
    <div class="sc-item">
        <div class="sc-label">Plot</div>
        <div class="sc-value">Plot #{{ $transfer->plot->plot_number ?? '—' }}</div>
        <div class="sc-sub">Block {{ $transfer->plot->block ?? '—' }} • {{ $transfer->transfer_date->format('d M Y') }}</div>
    </div>
    <div class="fee-highlight">
        <div class="fee-amount">PKR {{ number_format($transfer->transfer_fee) }}</div>
        <div class="fee-label">Fee Due</div>
    </div>
</div>


{{-- ── PAYMENT FORM ── --}}
@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 18px;margin-bottom:18px;font-size:13px;color:#dc2626;font-weight:600;">
    <strong>Please fix:</strong>
    <ul style="margin:6px 0 0 16px;font-size:12px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('transfers.process-payment', $transfer->id) }}" enctype="multipart/form-data">
@csrf

{{-- Payment method --}}
<div class="form-card">
    <div class="form-card-head"><div class="form-card-num">1</div><div class="form-card-title">Payment Method</div></div>
    <div class="form-card-body">
        <div class="method-grid">
            <label class="method-card {{ old('payment_method','cash')==='cash'?'selected':'' }}" style="--mc:#16a34a;--mb:#f0fdf4;" onclick="selectMethod('cash')">
                <input type="radio" name="payment_method" value="cash" {{ old('payment_method','cash')==='cash'?'checked':'' }}>
                <div class="method-check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></div>
                <span class="method-icon">💵</span>
                <div class="method-label">Cash</div>
            </label>
            <label class="method-card {{ old('payment_method')==='bank_transfer'?'selected':'' }}" style="--mc:#1d4ed8;--mb:#eff6ff;" onclick="selectMethod('bank_transfer')">
                <input type="radio" name="payment_method" value="bank_transfer" {{ old('payment_method')==='bank_transfer'?'checked':'' }}>
                <div class="method-check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></div>
                <span class="method-icon">🏦</span>
                <div class="method-label">Bank Transfer</div>
            </label>
            <label class="method-card {{ old('payment_method')==='cheque'?'selected':'' }}" style="--mc:#7c3aed;--mb:#fdf4ff;" onclick="selectMethod('cheque')">
                <input type="radio" name="payment_method" value="cheque" {{ old('payment_method')==='cheque'?'checked':'' }}>
                <div class="method-check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></div>
                <span class="method-icon">📋</span>
                <div class="method-label">Cheque</div>
            </label>
            <label class="method-card {{ old('payment_method')==='online'?'selected':'' }}" style="--mc:#ea580c;--mb:#fff7ed;" onclick="selectMethod('online')">
                <input type="radio" name="payment_method" value="online" {{ old('payment_method')==='online'?'checked':'' }}>
                <div class="method-check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></div>
                <span class="method-icon">📱</span>
                <div class="method-label">Online</div>
            </label>
        </div>
    </div>
</div>

{{-- Payment details --}}
<div class="form-card">
    <div class="form-card-head"><div class="form-card-num">2</div><div class="form-card-title">Payment Details</div></div>
    <div class="form-card-body">
        <div class="row-grid cols-3" style="margin-bottom:16px;">
            <div class="form-group">
                <label class="form-label">Receipt / Ref. No. <small>(optional)</small></label>
                <input type="text" name="receipt_no" class="form-control {{ $errors->has('receipt_no')?'is-invalid':'' }}"
                       placeholder="e.g. TRF-FEE-001" value="{{ $transfer->deed_no}}">
                @error('receipt_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Payment Date <span>*</span></label>
                <input type="date" name="payment_date" class="form-control {{ $errors->has('payment_date')?'is-invalid':'' }}"
                       value="{{ old('payment_date', date('Y-m-d')) }}" required>
                @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Amount (PKR)</label>
                <input type="text" class="form-control" value="{{ number_format($transfer->transfer_fee) }}" readonly style="background:#f8fafc;font-weight:800;color:#1e3a8a;">
            </div>
        </div>
        <div class="row-grid cols-2">
            <div class="form-group">
                <label class="form-label">Paid By <span>*</span></label>
                <input type="text" name="paid_by" class="form-control {{ $errors->has('paid_by')?'is-invalid':'' }}"
                       placeholder="Name of person paying" value="{{ old('paid_by', $transfer->toCustomer->name ?? $transfer->fromCustomer->name ?? '') }}" required>
                @error('paid_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <input type="text" name="notes" class="form-control" placeholder="Optional remarks" value="{{ old('notes') }}">
            </div>
        </div>
    </div>
</div>

{{-- Payment Proof Upload --}}
<div class="form-card" id="proofCard">
    <div class="form-card-head">
        <div class="form-card-num">3</div>
        <div class="form-card-title">Payment Proof</div>
        <span id="proofBadge" class="proof-required-badge" style="margin-left:auto;">
            ● Required
        </span>
    </div>
    <div class="form-card-body">

        <div class="proof-upload-wrap" id="uploadZone">
            <input type="file" name="payment_proof" id="proofInput"
                   accept="image/jpeg,image/png,image/webp,application/pdf"
                   onchange="previewProof(this)">

            {{-- Default state --}}
            <div id="uploadDefault">
                <div class="proof-upload-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                </div>
                <div class="proof-upload-title">Click or drag to upload proof</div>
                <div class="proof-upload-sub">JPG, PNG, WEBP or PDF • Max 5MB</div>
            </div>

            {{-- Preview state --}}
            <div class="proof-preview" id="uploadPreview">
                <img id="previewImg" src="" alt="Preview">
                <div class="proof-preview-name" id="previewName"></div>
                <button type="button" class="proof-preview-change" onclick="document.getElementById('proofInput').click()">Change file</button>
            </div>
        </div>

        @error('payment_proof')
            <div class="invalid-feedback" style="display:block;margin-top:8px;">{{ $message }}</div>
        @enderror

        <div id="proofHint" style="font-size:11px;color:#94a3b8;margin-top:10px;display:flex;align-items:center;gap:6px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            <span id="proofHintText">Upload a screenshot or photo of your payment transaction.</span>
        </div>
    </div>
</div>

{{-- Submit --}}
<div style="display:flex;justify-content:flex-end;gap:12px;">
    <a href="{{ route('index.transfer') }}" class="btn-cancel">Cancel</a>
    <button type="submit" class="btn-pay" onclick="return confirm('Confirm payment of PKR {{ number_format($transfer->transfer_fee) }}? This will complete the transfer and cannot be undone.')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Confirm Payment & Complete Transfer
    </button>
</div>

</form>
</div>

@endsection

@push('scripts')
<script>
// ── Payment method selection ──────────────────────────────────
function selectMethod(val) {
    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
    const card = document.querySelector(`input[value="${val}"]`).parentElement;
    card.classList.add('selected');
    card.querySelector('input').checked = true;
    updateProofRequirement(val);
}

// ── Proof required/optional based on payment method ──────────
function updateProofRequirement(method) {
    const input  = document.getElementById('proofInput');
    const badge  = document.getElementById('proofBadge');
    const hint   = document.getElementById('proofHintText');

    if (method === 'cash') {
        // Optional for cash
        input.removeAttribute('required');
        badge.className = 'proof-optional-badge';
        badge.innerHTML = '● Optional';
        hint.textContent = 'Proof is optional for cash payments.';
    } else {
        // Required for bank_transfer, cheque, online
        input.setAttribute('required', 'required');
        badge.className = 'proof-required-badge';
        badge.innerHTML = '● Required';
        const labels = {
            'bank_transfer': 'Upload a screenshot of your bank transfer confirmation.',
            'cheque':        'Upload a photo of the cheque front.',
            'online':        'Upload a screenshot of your online payment confirmation.',
        };
        hint.textContent = labels[method] || 'Upload payment proof.';
    }
}

// ── File preview ──────────────────────────────────────────────
function previewProof(input) {
    const file    = input.files[0];
    const zone    = document.getElementById('uploadZone');
    const def     = document.getElementById('uploadDefault');
    const preview = document.getElementById('uploadPreview');
    const img     = document.getElementById('previewImg');
    const name    = document.getElementById('previewName');

    if (!file) return;

    // Validate size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('File too large. Maximum size is 5MB.');
        input.value = '';
        return;
    }

    name.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        // PDF — show icon instead
        img.style.display = 'none';
    }

    def.style.display = 'none';
    preview.classList.add('show');
    zone.classList.add('has-file');
}

// ── Drag and drop ─────────────────────────────────────────────
const zone = document.getElementById('uploadZone');
zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    const input = document.getElementById('proofInput');
    input.files = e.dataTransfer.files;
    previewProof(input);
});

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) {
        checked.parentElement.classList.add('selected');
        updateProofRequirement(checked.value);
    }
});
</script>
@endpush
