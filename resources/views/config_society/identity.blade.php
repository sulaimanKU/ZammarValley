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
            <span>Society Identity</span>
        </div>
    </div>

    <a href="{{ route('setting.view') }}" class="sv-back-link">
        <i class="bi bi-arrow-left-circle-fill"></i> Back to Society Config
    </a>

    <div class="sv-page-header" style="--accent-light:#f0fdf4;--accent-mid:#86efac;">
        <div class="sv-page-icon" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
            <i class="bi bi-building-fill"></i>
        </div>
        <div>
            <h3 class="sv-page-title">Society Identity</h3>
            <p class="sv-page-sub">Name, tagline, contact details, address and plot configuration</p>
        </div>
        <div class="ms-auto">
            <span class="sv-badge sv-badge-green">
                <i class="bi bi-building-check"></i> {{ $settings['society_name'] ?? 'Zamar Valley' }}
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

    <form action="{{ route('settings.society.identity') }}" method="POST">
        @csrf

        {{-- Basic Info --}}
        <div class="sv-card">
            <div class="sv-card-head">
                <i class="bi bi-card-text" style="color:#16a34a;"></i> Basic Information
            </div>
            <div class="sv-card-body">
                <div class="sv-grid-2">
                    <div class="sv-field">
                        <label class="sv-label">Society Name <span class="sv-req">*</span></label>
                        <input type="text" name="society_name" class="sv-input"
                               value="{{ old('society_name', $settings['society_name'] ?? 'Zamar Valley') }}" required>
                        @error('society_name')<div class="text-danger mt-1" style="font-size:11px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="sv-field">
                        <label class="sv-label">Tagline / Slogan</label>
                        <input type="text" name="society_tagline" class="sv-input"
                               value="{{ old('society_tagline', $settings['society_tagline'] ?? '') }}"
                               placeholder="e.g. Premium Housing Project">
                    </div>
                </div>
                <div class="sv-field">
                    <label class="sv-label">Office Address</label>
                    <textarea name="society_address" class="sv-input sv-textarea"
                              rows="2" placeholder="Full office address...">{{ old('society_address', $settings['society_address'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="sv-card">
            <div class="sv-card-head">
                <i class="bi bi-telephone-fill" style="color:#ea580c;"></i> Contact Details
            </div>
            <div class="sv-card-body">
                <div class="sv-grid-2">
                    <div class="sv-field">
                        <label class="sv-label">Contact Number 1 <span class="sv-req">*</span></label>
                        <div class="sv-input-icon-wrap">
                            <i class="bi bi-telephone sv-icon"></i>
                            <input type="text" name="society_phone" class="sv-input sv-has-icon"
                                   value="{{ old('society_phone', $settings['society_phone'] ?? '') }}"
                                   placeholder="+92-300-0000000">
                        </div>
                    </div>
                    <div class="sv-field">
                        <label class="sv-label">Contact Number 2</label>
                        <div class="sv-input-icon-wrap">
                            <i class="bi bi-telephone sv-icon"></i>
                            <input type="text" name="society_phone2" class="sv-input sv-has-icon"
                                   value="{{ old('society_phone2', $settings['society_phone2'] ?? '') }}"
                                   placeholder="+92-301-0000000">
                        </div>
                    </div>
                    <div class="sv-field">
                        <label class="sv-label">Contact Number 3</label>
                        <div class="sv-input-icon-wrap">
                            <i class="bi bi-telephone sv-icon"></i>
                            <input type="text" name="society_phone3" class="sv-input sv-has-icon"
                                   value="{{ old('society_phone3', $settings['society_phone3'] ?? '') }}"
                                   placeholder="+92-302-0000000">
                        </div>
                    </div>
                    <div class="sv-field">
                        <label class="sv-label">Contact Email</label>
                        <div class="sv-input-icon-wrap">
                            <i class="bi bi-envelope sv-icon"></i>
                            <input type="email" name="society_email" class="sv-input sv-has-icon"
                                   value="{{ old('society_email', $settings['society_email'] ?? '') }}"
                                   placeholder="info@zammarvalley.com">
                        </div>
                        @error('society_email')<div class="text-danger mt-1" style="font-size:11px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="sv-preview-strip">
                    <span class="sv-preview-label"><i class="bi bi-eye-fill"></i> Document footer preview</span>
                    <div class="sv-preview-content">
                        <i class="bi bi-telephone-fill" style="color:#1e3a8a;"></i>
                        <span id="prevPhone">{{ $settings['society_phone'] ?? '+92-XXX-XXXXXXX' }}</span>
                        @if(!empty($settings['society_phone2']))
                        <span style="color:#cbd5e1;">·</span>
                        <span id="prevPhone2">{{ $settings['society_phone2'] }}</span>
                        @endif
                        @if(!empty($settings['society_phone3']))
                        <span style="color:#cbd5e1;">·</span>
                        <span id="prevPhone3">{{ $settings['society_phone3'] }}</span>
                        @endif
                        <span style="color:#cbd5e1;">|</span>
                        <i class="bi bi-envelope-fill" style="color:#1e3a8a;"></i>
                        <span id="prevEmail">{{ $settings['society_email'] ?? 'info@society.com' }}</span>
                    </div>
                </div>
            </div>
        </div>



        <div class="sv-save-bar">
            <a href="{{ route('setting.view') }}" class="sv-btn-cancel">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" class="sv-btn-save">
                <i class="bi bi-check-lg"></i> Save Identity
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const ph = document.querySelector('input[name="society_phone"]');
    const em = document.querySelector('input[name="society_email"]');
    if(ph) ph.addEventListener('input', () => { document.getElementById('prevPhone').textContent = ph.value || '+92-XXX-XXXXXXX'; });
    if(em) em.addEventListener('input', () => { document.getElementById('prevEmail').textContent = em.value || 'info@society.com'; });
});
</script>
@endpush
