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
            <span>Documents</span>
        </div>
    </div>

    <a href="{{ route('setting.view') }}" class="sv-back-link">
        <i class="bi bi-arrow-left-circle-fill"></i> Back to Society Config
    </a>

    <div class="sv-page-header" style="--accent-light:#fef9c3;--accent-mid:#fde68a;">
        <div class="sv-page-icon" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
            <i class="bi bi-file-earmark-text-fill"></i>
        </div>
        <div>
            <h3 class="sv-page-title">Documents</h3>
            <p class="sv-page-sub">ID prefixes, watermark text, receipt footer and PDF display options</p>
        </div>
        <div class="ms-auto">
            <span class="sv-badge sv-badge-amber">
                <i class="bi bi-file-earmark-check"></i>
                {{ $settings['receipt_prefix'] ?? 'REC' }} · {{ $settings['booking_id_prefix'] ?? 'ZV' }} · {{ $settings['deed_prefix'] ?? 'DEED' }}
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

    <form action="{{ route('settings.society.docs') }}" method="POST">
        @csrf

        {{-- ID Prefixes --}}
        <div class="sv-card">
            <div class="sv-card-head">
                <i class="bi bi-hash" style="color:#d97706;"></i> Document ID Prefixes
            </div>
            <div class="sv-card-body">
                <div class="sv-grid-3">

                    <div class="sv-field">
                        <label class="sv-label">Booking ID Prefix</label>
                        <input type="text" name="booking_id_prefix" id="pfxBooking"
                               class="sv-input sv-mono"
                               value="{{ old('booking_id_prefix', $settings['booking_id_prefix'] ?? 'ZV') }}"
                               placeholder="ZV" maxlength="10"
                               oninput="updatePreviews()">
                        <div class="sv-prefix-preview" id="prevBooking">
                            <i class="bi bi-arrow-right"></i> <span>ZV-ABC-123</span>
                        </div>
                    </div>
                    <div class="sv-field">
                        <label class="sv-label">Transfer Deed Prefix</label>
                        <input type="text" name="deed_prefix" id="pfxDeed"
                               class="sv-input sv-mono"
                               value="{{ old('deed_prefix', $settings['deed_prefix'] ?? 'DEED') }}"
                               placeholder="DEED" maxlength="10"
                               oninput="updatePreviews()">
                        <div class="sv-prefix-preview" id="prevDeed">
                            <i class="bi bi-arrow-right"></i> <span>DEED-2025-001</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Text Settings --}}
        <div class="sv-card">
            <div class="sv-card-head">
                <i class="bi bi-fonts" style="color:#475569;"></i> Document Text
            </div>
            <div class="sv-card-body">
                <div class="sv-grid-2">
                    <div class="sv-field">
                        <label class="sv-label">Watermark Text</label>
                        <input type="text" name="doc_watermark_text" class="sv-input"
                               value="{{ old('doc_watermark_text', $settings['doc_watermark_text'] ?? '') }}"
                               placeholder="Zamar Valley Official" maxlength="100">
                        <span class="sv-hint">Printed diagonally on all PDFs. Leave blank to disable.</span>
                    </div>
                    <div class="sv-field">
                        <label class="sv-label">Receipt Footer Note</label>
                        <input type="text" name="receipt_footer_note" class="sv-input"
                               value="{{ old('receipt_footer_note', $settings['receipt_footer_note'] ?? '') }}"
                               placeholder="Thank you for investing in Zamar Valley." maxlength="500">
                        <span class="sv-hint">Shown at the bottom of every receipt PDF.</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- PDF Toggles --}}
        <div class="sv-card">
            <div class="sv-card-head">
                <i class="bi bi-toggles" style="color:#dc2626;"></i> PDF Display Options
            </div>
            <div class="sv-card-body">
                <div class="sv-toggles-grid">



                    {{-- Logo Toggle --}}
                    <div class="sv-toggle-card {{ ($settings['show_logo_on_receipt'] ?? '1') === '1' ? 'is-on' : '' }}"
                         id="card-logo">
                        <div class="sv-toggle-top">
                            <div class="sv-toggle-ico" style="background:#fdf4ff;">
                                <i class="bi bi-image-fill" style="color:#7c3aed;font-size:1.5rem;"></i>
                            </div>
                            <label class="sv-switch">
                                <input type="checkbox" name="show_logo_on_receipt" id="tog-logo"
                                       {{ old('show_logo_on_receipt', $settings['show_logo_on_receipt'] ?? '1') === '1' ? 'checked' : '' }}>
                                <span class="sv-sw-track"></span>
                            </label>
                        </div>
                        <div class="sv-toggle-title">Logo on Receipts & PDFs</div>
                        <div class="sv-toggle-desc">
                            Print the society logo at the top of all generated PDFs, receipts and deed documents.
                        </div>
                        <div id="stat-logo">
                            @if(old('show_logo_on_receipt', $settings['show_logo_on_receipt'] ?? '1') === '1')
                                <span class="sv-status-on"><i class="bi bi-check-circle-fill"></i> Printed on all documents</span>
                            @else
                                <span class="sv-status-off"><i class="bi bi-x-circle-fill"></i> Not printed on documents</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="sv-save-bar">
            <a href="{{ route('setting.view') }}" class="sv-btn-cancel">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" class="sv-btn-save">
                <i class="bi bi-check-lg"></i> Save Document Settings
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Live prefix previews
    updatePreviews();

    // Toggle card states
    document.getElementById('tog-qr')?.addEventListener('change', function () {
        toggleCard('card-qr', 'stat-qr', this.checked, 'Active on all documents', 'Not shown on documents');
    });
    document.getElementById('tog-logo')?.addEventListener('change', function () {
        toggleCard('card-logo', 'stat-logo', this.checked, 'Printed on all documents', 'Not printed on documents');
    });
});

function updatePreviews() {
    const r = document.getElementById('pfxReceipt')?.value || 'REC';
    const b = document.getElementById('pfxBooking')?.value || 'ZV';
    const d = document.getElementById('pfxDeed')?.value    || 'DEED';
    document.getElementById('prevReceipt').innerHTML = '<i class="bi bi-arrow-right"></i> <span>' + r + '-0055</span>';
    document.getElementById('prevBooking').innerHTML = '<i class="bi bi-arrow-right"></i> <span>' + b + '-ABC-123</span>';
    document.getElementById('prevDeed').innerHTML    = '<i class="bi bi-arrow-right"></i> <span>' + d + '-2025-001</span>';
}

function toggleCard(cardId, statId, isOn, onText, offText) {
    const card = document.getElementById(cardId);
    const stat = document.getElementById(statId);
    if (card) { card.classList.toggle('is-on', isOn); }
    if (stat) {
        stat.innerHTML = isOn
            ? `<span class="sv-status-on"><i class="bi bi-check-circle-fill"></i> ${onText}</span>`
            : `<span class="sv-status-off"><i class="bi bi-x-circle-fill"></i> ${offText}</span>`;
    }
}
</script>
@endpush
