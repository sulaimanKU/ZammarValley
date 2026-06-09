@extends('layouts.index')

@push('styles')
<style>
</style>
@endpush

@section('content')
<div class="settings-page-wrap pb-5">

    <div class="cfg-page-header">
        <h2>Society Configuration</h2>
        <div class="cfg-breadcrumb">
            <a href="{{ route('index.dashboard') }}"><i class="bi bi-house"></i> Dashboard</a>
            <span class="sep">›</span>
            <a href="{{ route('setting.view') }}">Settings</a>
            <span class="sep">›</span>
            <span>Finance Defaults</span>
        </div>
    </div>

    <a href="{{ route('setting.view') }}" class="sv-back-link">
        <i class="bi bi-arrow-left-circle-fill"></i> Back to Society Config
    </a>

    <div class="sv-page-header" style="--accent-light:#fdf4ff;--accent-mid:#d8b4fe;">
        <div class="sv-page-icon" style="background:linear-gradient(135deg,#7c3aed,#a855f7);">
            <i class="bi bi-cash-stack"></i>
        </div>
        <div>
            <h3 class="sv-page-title">Finance Defaults</h3>
            <p class="sv-page-sub">Auto-filled values when creating transfers, installments and late fines</p>
        </div>
        <div class="ms-auto">
            <span class="sv-badge sv-badge-purple">
                <i class="bi bi-currency-exchange"></i> {{ $settings['currency_symbol'] ?? 'PKR' }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 mb-4">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form action="{{ route('settings.society.finance') }}" method="POST">
        @csrf






        {{-- Late Payment Rules --}}
        <div class="sv-card">
            <div class="sv-card-head">
                <i class="bi bi-hourglass-split" style="color:#dc2626;"></i> Late Payment Rules
            </div>
            <div class="sv-card-body">
                <div class="sv-grid-2">
                    <div class="sv-field">
                        <label class="sv-label">Late Payment Fine % <span class="sv-req">*</span></label>
                        <div class="sv-input-suffix">
                            <input type="number" name="late_fine_percent" id="fineInput" class="sv-input sv-suffixed"
                                   value="{{ old('late_fine_percent', $settings['late_fine_percent'] ?? '2') }}"
                                   min="0" max="100" step="0.5">
                            <span class="sv-suf">%</span>
                        </div>
                        <span class="sv-hint">Charged per month on overdue installments</span>
                        @error('late_fine_percent')<div class="text-danger mt-1" style="font-size:11px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="sv-field">
                        <label class="sv-label">Installment Grace Period <span class="sv-req">*</span></label>
                        <div class="sv-input-suffix">
                            <input type="number" name="installment_grace_days" id="graceInput" class="sv-input sv-suffixed"
                                   value="{{ old('installment_grace_days', $settings['installment_grace_days'] ?? '10') }}"
                                   min="0" max="60">
                            <span class="sv-suf">days</span>
                        </div>
                        <span class="sv-hint">Days after due date before fine is applied</span>
                        @error('installment_grace_days')<div class="text-danger mt-1" style="font-size:11px;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Live calculator --}}
        <div class="sv-calc-strip">
            <div class="sv-calc-title"><i class="bi bi-calculator-fill"></i> Fine Preview — on PKR 100,000 overdue installment</div>
            <div class="sv-calc-row">
                <div class="sv-calc-box">
                    <span class="sv-calc-val" id="calcGrace">{{ $settings['installment_grace_days'] ?? '10' }} days</span>
                    <span class="sv-calc-key">Grace Period</span>
                </div>
                <span class="sv-calc-arrow">→</span>
                <div class="sv-calc-box">
                    <span class="sv-calc-val" id="calcRate">{{ $settings['late_fine_percent'] ?? '2' }}%</span>
                    <span class="sv-calc-key">Monthly Fine Rate</span>
                </div>
                <span class="sv-calc-arrow">→</span>
                <div class="sv-calc-box highlight">
                    <span class="sv-calc-val" id="calcResult">PKR 2,000</span>
                    <span class="sv-calc-key">Fine after 1 month overdue</span>
                </div>
            </div>
        </div>

        <div class="sv-save-bar">
            <a href="{{ route('setting.view') }}" class="sv-btn-cancel">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" class="sv-btn-save">
                <i class="bi bi-check-lg"></i> Save Finance Settings
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const fi = document.getElementById('fineInput');
    const gi = document.getElementById('graceInput');
    function update(){
        const rate  = parseFloat(fi?.value || 2);
        const grace = parseInt(gi?.value   || 10);
        const fine  = (100000 * rate / 100).toLocaleString('en-PK');
        if(document.getElementById('calcRate'))   document.getElementById('calcRate').textContent   = rate + '%';
        if(document.getElementById('calcGrace'))  document.getElementById('calcGrace').textContent  = grace + ' days';
        if(document.getElementById('calcResult')) document.getElementById('calcResult').textContent = 'PKR ' + fine;
    }
    fi?.addEventListener('input', update);
    gi?.addEventListener('input', update);
    update();
});
</script>
@endpush
