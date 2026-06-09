@extends('layouts.index')

@section('content')
@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800;900&display=swap');
.te-wrap * { font-family:'DM Sans',sans-serif; }
.te-wrap { padding:1.75rem; background:#f4f6fb; min-height:100vh; }

/* ── Header ── */
.te-header { background:linear-gradient(135deg,#0f172a,#78350f 60%,#92400e); border-radius:18px; padding:24px 32px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; position:relative; overflow:hidden; }
.te-header::before { content:'EDIT'; position:absolute; right:-10px; top:-8px; font-size:120px; font-weight:900; color:rgba(255,255,255,.03); pointer-events:none; white-space:nowrap; }
.te-title { font-size:1.15rem; font-weight:800; color:#fff; margin:0; position:relative; z-index:1; }
.te-sub   { font-size:12px; color:rgba(255,255,255,.45); margin:5px 0 0; position:relative; z-index:1; }
.btn-back { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2); color:#fff; padding:9px 16px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:7px; position:relative; z-index:1; }
.btn-back:hover { background:rgba(255,255,255,.2); color:#fff; }

/* ── Warning banner ── */
.warn-banner { background:#fffbeb; border:1.5px solid #fde68a; border-radius:12px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:flex-start; gap:12px; font-size:12.5px; color:#92400e; font-weight:600; }
.warn-banner svg { width:18px; height:18px; flex-shrink:0; margin-top:1px; }

/* ── Info strip ── */
.info-strip { background:linear-gradient(135deg,#0f172a,#1e3a8a); border-radius:14px; padding:18px 24px; margin-bottom:20px; display:flex; gap:24px; flex-wrap:wrap; align-items:center; }
.is-item .is-label { font-size:9px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px; }
.is-item .is-value { font-size:13px; font-weight:800; color:#fff; }
.is-divider { width:1px; background:rgba(255,255,255,.1); align-self:stretch; }

/* ── Card ── */
.card { background:#fff; border-radius:14px; border:1px solid #e4e9f2; box-shadow:0 2px 8px rgba(15,23,42,.04); overflow:hidden; margin-bottom:20px; }
.card-head { padding:16px 22px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:10px; }
.card-head-num  { width:26px; height:26px; border-radius:8px; background:#ca8a04; color:#fff; font-size:12px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.card-title { font-size:13px; font-weight:800; color:#0f172a; }
.card-body  { padding:24px; }

/* ── Form controls ── */
.row-grid { display:grid; gap:16px; }
.row-grid.cols-2 { grid-template-columns:1fr 1fr; }
.row-grid.cols-3 { grid-template-columns:1fr 1fr 1fr; }
@media(max-width:640px){ .row-grid.cols-2,.row-grid.cols-3 { grid-template-columns:1fr; } }
.form-group { display:flex; flex-direction:column; }
.form-label { font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
.form-label span { color:#dc2626; }
.form-control,.form-select { border:1.5px solid #e2e8f0; border-radius:9px; font-size:13px; padding:10px 13px; color:#0f172a; font-family:'DM Sans',sans-serif; outline:none; transition:border-color .15s; width:100%; }
.form-control:focus,.form-select:focus { border-color:#ca8a04; box-shadow:0 0 0 3px rgba(202,138,4,.08); }
.form-control.is-invalid { border-color:#dc2626; }
.invalid-feedback { font-size:11px; color:#dc2626; margin-top:4px; }
.form-control[readonly] { background:#f8fafc; color:#64748b; cursor:not-allowed; }
.form-hint { font-size:11px; color:#94a3b8; margin-top:4px; }

/* ── Locked field ── */
.locked-field { background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:9px; padding:10px 13px; font-size:13px; color:#64748b; display:flex; align-items:center; gap:8px; }
.locked-field svg { width:13px; height:13px; color:#94a3b8; flex-shrink:0; }

/* ── Buttons ── */
.btn-save { background:linear-gradient(135deg,#ca8a04,#a16207); color:#fff; padding:12px 32px; border-radius:11px; font-size:14px; font-weight:800; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 14px rgba(202,138,4,.3); transition:all .15s; }
.btn-save:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(202,138,4,.4); }
.btn-cancel { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; padding:12px 24px; border-radius:11px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:7px; }
.btn-cancel:hover { background:#e2e8f0; }
</style>
@endpush

<div class="te-wrap">

{{-- ── HEADER ── --}}
<div class="te-header">
    <div>
        <p class="te-title">Edit Transfer — {{ $transfer->deed_no }}</p>
        <p class="te-sub">{{ $transfer->getTypeLabel() }} • Created {{ $transfer->created_at->format('d M Y') }}</p>
    </div>
    <a href="{{ route('index.transfer') }}" class="btn-back">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back
    </a>
</div>

{{-- ── WARNING ── --}}
<div class="warn-banner">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
    <span>You can only edit fee, date, reason, notes, and the new owner on pending transfers. The transfer type, original booking, and plot cannot be changed. Completed transfers are locked.</span>
</div>

{{-- ── LOCKED INFO STRIP ── --}}
<div class="info-strip">
    <div class="is-item">
        <div class="is-label">Deed No.</div>
        <div class="is-value" style="font-family:monospace;">{{ $transfer->deed_no }}</div>
    </div>
    <div class="is-divider"></div>
    <div class="is-item">
        <div class="is-label">Type</div>
        <div class="is-value">{{ $transfer->getTypeLabel() }}</div>
    </div>
    <div class="is-divider"></div>
    <div class="is-item">
        <div class="is-label">From Owner</div>
        <div class="is-value">{{ $transfer->fromCustomer->name ?? '—' }}</div>
    </div>
    <div class="is-divider"></div>
    <div class="is-item">
        <div class="is-label">Plot</div>
        <div class="is-value">Plot #{{ $transfer->plot->plot_number ?? '—' }}, Block {{ $transfer->plot->block ?? '—' }}</div>
    </div>
    <div class="is-divider"></div>
    <div class="is-item">
        <div class="is-label">Status</div>
        <div class="is-value" style="color:#fcd34d;">{{ ucfirst($transfer->status) }}</div>
    </div>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 18px;margin-bottom:18px;font-size:13px;color:#dc2626;font-weight:600;">
    <strong>Please fix:</strong>
    <ul style="margin:6px 0 0 16px;font-size:12px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('transfers.update', $transfer->id) }}">
@csrf
@method('PUT')

{{-- ── EDITABLE: Transfer details ── --}}
<div class="card">
    <div class="card-head"><div class="card-head-num">1</div><div class="card-title">Transfer Details</div></div>
    <div class="card-body">
        <div class="row-grid cols-3">
            <div class="form-group">
                <label class="form-label">Transfer Date <span>*</span></label>
                <input type="date" name="transfer_date" class="form-control {{ $errors->has('transfer_date')?'is-invalid':'' }}"
                       value="{{ old('transfer_date', $transfer->transfer_date->format('Y-m-d')) }}" required>
                @error('transfer_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>


        </div>
    </div>
</div>

{{-- ── EDITABLE: New owner (if ownership/partial) ── --}}
@if(in_array($transfer->transfer_type, ['ownership', 'partial']))
<div class="card">
    <div class="card-head"><div class="card-head-num">2</div><div class="card-title">New Owner</div></div>
    <div class="card-body">
        <div class="row-grid cols-2">
            <div class="form-group">
                <label class="form-label">New Owner (Customer)</label>
                <select name="to_customer_id" class="form-select">
                    <option value="">— Keep current —</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('to_customer_id', $transfer->to_customer_id) == $c->id ? 'selected':'' }}>
                        {{ $c->name }} — {{ $c->cnic }}
                    </option>
                    @endforeach
                </select>
            </div>
            @if($transfer->transfer_type === 'partial')
            <div class="form-group">
                <label class="form-label">Ownership Percentage</label>
                <input type="number" name="ownership_percentage" class="form-control"
                       value="{{ old('ownership_percentage', $transfer->ownership_percentage) }}"
                       min="1" max="99" step="0.01" placeholder="e.g. 50">
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ── EDITABLE: Block/plot number (if internal) ── --}}
@if($transfer->transfer_type === 'internal')
<div class="card">
    <div class="card-head"><div class="card-head-num">2</div><div class="card-title">New Plot Details</div></div>
    <div class="card-body">
        <div class="row-grid cols-2">
            <div class="form-group">
                <label class="form-label">New Block</label>
                <input type="text" name="new_block" class="form-control"
                       value="{{ old('new_block', $transfer->new_block) }}" placeholder="e.g. B">
            </div>
            <div class="form-group">
                <label class="form-label">New Plot Number</label>
                <input type="text" name="new_plot_number" class="form-control"
                       value="{{ old('new_plot_number', $transfer->new_plot_number) }}" placeholder="e.g. 145-A">
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── EDITABLE: Reason & notes ── --}}
<div class="card">
    <div class="card-head"><div class="card-head-num">3</div><div class="card-title">Reason & Notes</div></div>
    <div class="card-body">
        <div class="row-grid cols-2">
            <div class="form-group">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="Reason for transfer...">{{ old('reason', $transfer->reason) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes...">{{ old('notes', $transfer->notes) }}</textarea>
            </div>
        </div>
    </div>
</div>
{{-- ── EDITABLE: Witnesses & Consideration ── --}}
<div class="card">
    <div class="card-head"><div class="card-head-num">3</div><div class="card-title">Witnesses & Consideration</div></div>
    <div class="card-body">
        <div class="row-grid cols-3" style="margin-bottom: 20px;">
            <div class="form-group">
                <label class="form-label">Witness 1 Name</label>
                <input type="text" name="witness1_name" class="form-control"
                       value="{{ old('witness1_name', $transfer->witness1_name) }}" placeholder="Full Name">
            </div>
            <div class="form-group">
                <label class="form-label">Witness 1 CNIC</label>
                <input type="text" name="witness1_cnic" class="form-control"
                       value="{{ old('witness1_cnic', $transfer->witness1_cnic) }}" placeholder="00000-0000000-0">
            </div>
            <div class="form-group">
                <label class="form-label">Witness 1 Address</label>
                <input type="text" name="witness1_address" class="form-control"
                       value="{{ old('witness1_address', $transfer->witness1_address) }}" placeholder="Residential Address">
            </div>
        </div>

        <div class="row-grid cols-3" style="margin-bottom: 20px; border-top: 1px solid #f1f5f9; pt: 20px; padding-top: 20px;">
            <div class="form-group">
                <label class="form-label">Witness 2 Name</label>
                <input type="text" name="witness2_name" class="form-control"
                       value="{{ old('witness2_name', $transfer->witness2_name) }}" placeholder="Full Name">
            </div>
            <div class="form-group">
                <label class="form-label">Witness 2 CNIC</label>
                <input type="text" name="witness2_cnic" class="form-control"
                       value="{{ old('witness2_cnic', $transfer->witness2_cnic) }}" placeholder="00000-0000000-0">
            </div>
            <div class="form-group">
                <label class="form-label">Witness 2 Address</label>
                <input type="text" name="witness2_address" class="form-control"
                       value="{{ old('witness2_address', $transfer->witness2_address) }}" placeholder="Residential Address">
            </div>
        </div>


    </div>
</div>

{{-- ── SUBMIT ── --}}
<div style="display:flex;justify-content:flex-end;gap:12px;">
    <a href="{{ route('index.transfer') }}" class="btn-cancel">Cancel</a>
    <button type="submit" class="btn-save">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Save Changes
    </button>
</div>

</form>
</div>
@endsection
