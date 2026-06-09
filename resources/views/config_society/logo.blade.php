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
            <span>Society Logo</span>
        </div>
    </div>

    <a href="{{ route('setting.view') }}" class="sv-back-link">
        <i class="bi bi-arrow-left-circle-fill"></i> Back to Society Config
    </a>

    <div class="sv-page-header" style="--accent-light:#eff6ff;--accent-mid:#bfdbfe;">
        <div class="sv-page-icon" style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);">
            <i class="bi bi-image-fill"></i>
        </div>
        <div>
            <h3 class="sv-page-title">Society Logo</h3>
            <p class="sv-page-sub">Appears on all receipts, possession letters, transfer deeds and PDF exports</p>
        </div>
        <div class="ms-auto">
            @if(!empty($settings['society_logo']))
                <span class="sv-badge sv-badge-green"><i class="bi bi-check-circle-fill"></i> Uploaded</span>
            @else
                <span class="sv-badge sv-badge-gray"><i class="bi bi-dash-circle"></i> Not Uploaded</span>
            @endif
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

    <form action="{{ route('settings.society.logo') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Drop Zone --}}
        <div class="sv-card">
            <div class="sv-card-head">
                <i class="bi bi-cloud-upload-fill" style="color:#1e3a8a;"></i> Upload New Logo
            </div>
            <div class="sv-card-body">
                <div class="sv-logo-zone" id="logoDropZone"
                     ondragover="event.preventDefault();this.classList.add('sv-dz-active')"
                     ondragleave="this.classList.remove('sv-dz-active')"
                     ondrop="svHandleDrop(event)">
                    <div class="sv-logo-current">
                        <div class="sv-logo-lbl">Current Logo</div>
                        @if(!empty($settings['society_logo']))
                            <img src="{{ asset('storage/'.$settings['society_logo']) }}"
                                 id="logoPreview" class="sv-logo-img" alt="Logo">
                        @else
                            <div id="logoPreview" class="sv-logo-empty">
                                <i class="bi bi-building"></i>
                                <span>No logo yet</span>
                            </div>
                        @endif
                        <p id="logoFileName" class="sv-logo-filename"></p>
                    </div>
                    <div class="sv-logo-upload">
                        <i class="bi bi-cloud-arrow-up-fill sv-upload-icon"></i>
                        <p class="sv-upload-title">Drag & drop your logo here</p>
                        <p class="sv-upload-sub">or click to browse files</p>
                        <label for="logoUpload" class="sv-upload-btn">
                            <i class="bi bi-folder2-open"></i> Browse File
                        </label>
                        <input type="file" id="logoUpload" name="society_logo"
                               accept="image/*" style="display:none;"
                               onchange="svPreviewLogo(this)">
                        <div class="sv-upload-rules">
                            <span><i class="bi bi-check2"></i> PNG, JPG, SVG, WebP</span>
                            <span><i class="bi bi-check2"></i> Max 2MB</span>
                            <span><i class="bi bi-check2"></i> Square preferred</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Where it's used --}}
        <div class="sv-card">
            <div class="sv-card-head">
                <i class="bi bi-info-circle-fill" style="color:#1e3a8a;"></i> Where this logo appears
            </div>
            <div class="sv-card-body">
                <div class="sv-info-tags">
                    <span class="sv-tag"><i class="bi bi-receipt"></i> Payment Receipts</span>
                    <span class="sv-tag"><i class="bi bi-file-earmark-text"></i> Booking Agreement</span>
                    <span class="sv-tag"><i class="bi bi-house-check"></i> Possession Letter</span>
                    <span class="sv-tag"><i class="bi bi-arrow-left-right"></i> Transfer Deed</span>
                    <span class="sv-tag"><i class="bi bi-file-pdf"></i> All PDF Exports</span>
                </div>
            </div>
        </div>

        <div class="sv-save-bar">
            <a href="{{ route('setting.view') }}" class="sv-btn-cancel">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" class="sv-btn-save">
                <i class="bi bi-cloud-upload-fill"></i> Upload & Save Logo
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function svPreviewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const el = document.getElementById('logoPreview');
        if (el.tagName === 'IMG') { el.src = e.target.result; }
        else {
            const img = document.createElement('img');
            img.id = 'logoPreview'; img.src = e.target.result; img.className = 'sv-logo-img';
            el.replaceWith(img);
        }
    };
    reader.readAsDataURL(input.files[0]);
    const fn = document.getElementById('logoFileName');
    if (fn) fn.textContent = '✓ ' + input.files[0].name;
}
function svHandleDrop(e) {
    e.preventDefault();
    document.getElementById('logoDropZone')?.classList.remove('sv-dz-active');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const input = document.getElementById('logoUpload');
        const dt = new DataTransfer(); dt.items.add(file);
        input.files = dt.files; svPreviewLogo(input);
    }
}
</script>
@endpush
