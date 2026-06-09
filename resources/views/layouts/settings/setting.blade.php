@extends('layouts.index')

@push('styles')

<style>
/* ══════════════════════════════════════════════════════════════
   SOCIETY CONFIG CARDS
══════════════════════════════════════════════════════════════ */

/* ── Grid ───────────────────────────────────────────────────── */
.sc-back {
    background: none; border: none; cursor: pointer;
    font-size: 13px; font-weight: 700; color: #475569;
    padding: 0; margin-bottom: 18px;
    display: inline-flex; align-items: center; gap: 7px;
    font-family: inherit; transition: color .15s;
}
.sc-back:hover { color: #1e3a8a; }
.sc-back i { font-size: 16px; }

.sc-panel { animation: scFadeIn .2s ease; }
@keyframes scFadeIn {
    from { opacity:0; transform:translateX(10px); }
    to   { opacity:1; transform:translateX(0); }
}

.modern-cfg-card { transition: transform .2s, box-shadow .2s; }
.modern-cfg-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.1) !important;
}
@media(max-width: 768px) { .sc-grid { grid-template-columns: 1fr; } }
.sc-full { grid-column: 1 / -1; }

/* ── Card Shell ─────────────────────────────────────────────── */
.sc-card {
    background: #fff;
    border: 1px solid #e8edf3;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(15,23,42,.05);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: box-shadow .2s, transform .2s;
}
.sc-card:hover {
    box-shadow: 0 6px 20px rgba(15,23,42,.09);
    transform: translateY(-1px);
}

/* ── Card Header ────────────────────────────────────────────── */
.sc-card-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
}
.sc-card-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.sc-card-title { font-size: 13px; font-weight: 800; color: #0f172a; }
.sc-card-sub   { font-size: 11px; color: #94a3b8; margin-top: 1px; }

/* ── Card Body ──────────────────────────────────────────────── */
.sc-card-body { padding: 18px; flex: 1; display: flex; flex-direction: column; gap: 12px; }

/* ── Fields ─────────────────────────────────────────────────── */
.sc-field { display: flex; flex-direction: column; gap: 5px; }
.sc-label {
    font-size: 11px; font-weight: 700; color: #475569;
    text-transform: uppercase; letter-spacing: .5px;
}
.req { color: #dc2626; }
.sc-hint { font-size: 10px; color: #94a3b8; margin: 0; }

.sc-input {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    font-size: 13px;
    font-family: inherit;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    transition: border-color .15s, background .15s;
}
.sc-input:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,.08); }
.sc-textarea { resize: vertical; min-height: 80px; }
.sc-select { cursor: pointer; }
.sc-mono { font-family: monospace; letter-spacing: .5px; font-size: 13px; }

/* ── Row layouts inside card ────────────────────────────────── */
.sc-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.sc-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
@media(max-width:480px) {
    .sc-row-2, .sc-row-3 { grid-template-columns: 1fr; }
}

/* ── Input with icon ────────────────────────────────────────── */
.sc-input-icon-wrap { position: relative; }
.sc-input-icon {
    position: absolute; left: 11px; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8; font-size: 13px; pointer-events: none;
}
.sc-input-with-icon { padding-left: 34px; }

/* ── Input with prefix/suffix ───────────────────────────────── */
.sc-input-prefix-wrap, .sc-input-suffix-wrap { display: flex; align-items: center; }
.sc-prefix, .sc-suffix {
    background: #f1f5f9; border: 1.5px solid #e2e8f0;
    padding: 9px 10px; font-size: 11px; font-weight: 700; color: #64748b;
    white-space: nowrap; line-height: 1;
}
.sc-prefix { border-right: none; border-radius: 9px 0 0 9px; }
.sc-suffix { border-left: none;  border-radius: 0 9px 9px 0; }
.sc-input-prefixed { border-radius: 0 9px 9px 0; }
.sc-input-suffixed { border-radius: 9px 0 0 9px; flex: 1; }

/* ── Logo upload area ───────────────────────────────────────── */
.logo-upload-area {
    display: flex; align-items: center; gap: 20px;
    padding: 18px; background: #f8fafc;
    border: 2px dashed #e2e8f0; border-radius: 12px;
    flex-wrap: wrap;
}
.logo-preview-wrap { width: 80px; height: 80px; flex-shrink: 0; }
.logo-preview-img {
    width: 80px; height: 80px; border-radius: 12px;
    object-fit: contain; border: 2px solid #e8edf3;
    padding: 4px; background: #fff;
}
.logo-preview-placeholder {
    width: 80px; height: 80px; border-radius: 12px;
    background: #f1f5f9; border: 2px dashed #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #94a3b8;
}
.sc-upload-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: #eff6ff; border: 1.5px solid #bfdbfe;
    color: #1d4ed8; padding: 8px 16px; border-radius: 9px;
    font-size: 12px; font-weight: 700; cursor: pointer;
    transition: background .15s;
}
.sc-upload-btn:hover { background: #dbeafe; }

/* ── Contact preview box ────────────────────────────────────── */
.sc-preview-box {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 12px 14px; margin-top: 4px;
}
.sc-preview-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; }
.sc-preview-content { font-size: 11px; color: #0f172a; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.sc-preview-content i { color: #1e3a8a; }

/* ── Toggle switch ──────────────────────────────────────────── */
.sc-toggle-row {
    display: flex; align-items: center;
    justify-content: space-between; gap: 12px;
}
.sc-toggle-info { flex: 1; }
.sc-toggle-title { font-size: 13px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.sc-toggle-desc  { font-size: 11px; color: #64748b; margin-top: 3px; }
.sc-toggle-divider { height: 1px; background: #f1f5f9; margin: 4px 0; }

.sc-switch { position: relative; width: 46px; height: 26px; flex-shrink: 0; cursor: pointer; }
.sc-switch input { opacity: 0; width: 0; height: 0; }
.sc-switch-track {
    position: absolute; inset: 0;
    background: #e2e8f0; border-radius: 99px;
    transition: background .2s;
}
.sc-switch-track::before {
    content: ''; position: absolute;
    width: 20px; height: 20px; border-radius: 50%;
    background: #fff; top: 3px; left: 3px;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
    transition: transform .2s;
}
.sc-switch input:checked + .sc-switch-track { background: #1e3a8a; }
.sc-switch input:checked + .sc-switch-track::before { transform: translateX(20px); }

/* ── Flash ──────────────────────────────────────────────────── */
.sc-flash {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; border-radius: 12px;
    font-size: 13px; font-weight: 600; margin-bottom: 16px;
}
.sc-flash-success { background: #f0fdf4; border: 1px solid #86efac; color: #15803d; }
.sc-flash-close { margin-left: auto; background: none; border: none; cursor: pointer; font-size: 18px; line-height: 1; }

/* ── Action bar ─────────────────────────────────────────────── */
.sc-action-bar {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 16px 0 4px;
    border-top: 1px solid #f1f5f9;
    margin-top: 4px;
}
.sc-btn-discard {
    background: #f1f5f9; border: none; color: #475569;
    padding: 10px 20px; border-radius: 10px;
    font-size: 13px; font-weight: 700; cursor: pointer;
    font-family: inherit; display: inline-flex; align-items: center; gap: 6px;
    transition: background .15s;
}
.sc-btn-discard:hover { background: #e2e8f0; }
.sc-btn-save {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    border: none; color: #fff;
    padding: 10px 28px; border-radius: 10px;
    font-size: 13px; font-weight: 700; cursor: pointer;
    font-family: inherit; display: inline-flex; align-items: center; gap: 6px;
    box-shadow: 0 4px 14px rgba(30,58,138,.25);
    transition: opacity .15s;
}
.sc-btn-save:hover { opacity: .9; }
</style>
@endpush

@section('content')
@if (session('error'))
    <div class="alert-flash alert-flash-error mx-3 mt-3"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert-flash alert-flash-error mx-3 mt-3" style="flex-direction:column;align-items:flex-start;">
        <div style="display:flex;align-items:center;gap:8px;font-weight:700;margin-bottom:6px;"><i class="fas fa-exclamation-triangle"></i> Please fix the following:</div>
        <ul style="margin:0;padding-left:20px;">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="settings-page-wrap pb-5">

    {{-- ── Page Header ── --}}
    <div class="cfg-page-header">
        <h2>System Settings</h2>
        <div class="cfg-breadcrumb">
            <a href="{{ route('index.dashboard') }}"><i class="bi bi-house"></i> Dashboard</a>
            <span class="sep">›</span>
            <span>System Settings</span>
        </div>
    </div>

    {{-- ── Hero Banner + Tabs ── --}}
    <div class="cfg-hero">
        <div class="cfg-hero-top">
            <div>
                <div class="cfg-hero-title">
                    <div class="gear-icon"><i class="bi bi-gear-fill"></i></div>
                    <h1>Configuration Center</h1>
                </div>
                <p class="cfg-hero-subtitle">Manage all system settings from one place</p>
            </div>


        </div>

        <div class="cfg-tabs">
            @can('profile_edit')
            <button class="cfg-tab active" data-target="profile-section">
                <i class="bi bi-person-gear"></i> Profile
            </button>
            @endcan

            @can('role_manage')
            <button class="cfg-tab" data-target="roles-section">
                <i class="bi bi-shield-lock"></i> User Roles
            </button>
            @endcan

            @can('location_manage')
            <button class="cfg-tab" data-target="location">
                <i class="fa-solid fa-location-dot"></i> Location
            </button>
            @endcan

            @can('society_config_manage')
            <button class="cfg-tab" data-target="society-section">
                <i class="bi bi-building-gear"></i> Society Config
            </button>
            @endcan
        </div>
    </div>

    {{-- ── Body Content ── --}}
    <div class="cfg-body">

        {{-- ░░ Profile Section ░░ --}}
        @can('profile_edit')
        <div id="profile-section" class="cfg-section active">
            <div class="profile-hero-card">
                <form action="{{route('settings.profile.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="profile-avatar-wrap">
                        <div class="profile-avatar-ring">
                            <img id="profilePreview"
                                src="{{ filled(auth()->user()->profile_image)
                                    ? asset('storage/user_image/' . auth()->user()->profile_image)
                                    : asset('profile_img/person.png') }}"
                                alt="Profile" />
                            <div class="profile-cam">
                                <label for="uploadImage"><i class="bi bi-camera-fill"></i></label>
                                <input type="file" id="uploadImage" name="profile_image" accept="image/*" style="display:none;">
                            </div>
                        </div>
                        <div class="profile-meta">
                            <h3>{{ auth()->user()->name }}</h3>
                            <p>Zamar Valley</p>
                            <span class="profile-badge">{{ auth()->user()->getRoleNames()->first() }}</span>
                        </div>
                    </div>

                    <div class="form-section-label">
                        <div class="lbl-icon lbl-blue"><i class="bi bi-person-fill"></i></div>
                        <h5>Personal Information</h5>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label cfg-label">Full Name</label>
                            <input type="text" name="name" class="form-control custom-input"
                                value="{{ auth()->user()->name }}" placeholder="Enter your name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label cfg-label">Email Address</label>
                            <input type="email" name="email" class="form-control custom-input"
                                value="{{ auth()->user()->email }}" placeholder="Enter your email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label cfg-label">Phone Number</label>
                            <input type="tel" name="phone_number" class="form-control custom-input"
                                value="{{ auth()->user()->phone_number }}" placeholder="0300-0000000">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label cfg-label">Residential Address</label>
                            <textarea name="address" class="form-control custom-input" rows="2"
                                placeholder="Enter your full address">{{ auth()->user()->address }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4 opacity-15">

                    <div class="form-section-label">
                        <div class="lbl-icon lbl-red"><i class="bi bi-shield-lock-fill"></i></div>
                        <h5>Security &amp; Password</h5>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label cfg-label">New Password</label>
                            <input type="password" name="password" class="form-control custom-input" placeholder="••••••••">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label cfg-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control custom-input" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="cfg-save-btn">
                            <i class="bi bi-check-lg me-1"></i> Save All Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endcan

        {{-- ░░ Roles Section ░░ --}}
        @can('role_manage')
        <div id="roles-section" class="cfg-section">
            <div class="roles-header-bar">
                <h3><i class="bi bi-shield-lock-fill me-2 text-primary"></i>System Access Control</h3>
                <div class="roles-header-btns">
                    {{-- <button class="cfg-outline-btn" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                        <i class="bi bi-key-fill"></i> Create Permission
                    </button> --}}
                    <a href="{{ route('role.create') }}" class="cfg-outline-btn" style="text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                        <i class="bi bi-shield-plus"></i> Create Role
                    </a>
                </div>
            </div>

            <div class="cfg-table-wrap">
                <table class="cfg-table">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Permissions</th>
                            <th class="text-center">Users</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roleAndPermissions as $role)
                        <tr>
                            <td class="role-name-cell">
                                <div class="fw-bold text-dark">{{ $role->name }}</div>
                                <small class="text-muted">{{ $role->description ?? 'System Role' }}</small>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    @forelse($role->permissions->groupBy('module') as $module => $permissions)
                                        <div class="module-group">
                                            <small class="text-uppercase fw-bold text-primary" style="font-size:0.65rem;letter-spacing:0.5px;">
                                                {{ $module ?? 'General' }}
                                            </small>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($permissions as $perm)
                                                    <span class="perm-badge" title="{{ $perm->name }}">
                                                        {{ str_replace(['_','view','create','edit','delete'],['','View','Add','Edit','Del'], $perm->name) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-muted italic small">No permissions assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="user-count-badge">
                                    {{ str_pad($role->users_count ?? $role->users->count(), 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div style="display:inline-flex;gap:6px;">
                                    <a href="{{ route('RolePermission.edit', $role->id) }}" class="tbl-icon-btn edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('role.destroy', $role->id) }}" method="POST"
                                          onsubmit="return confirm('Delete this role?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="tbl-icon-btn del">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endcan

        {{-- ░░ Society Config Section ░░ --}}
        @can('society_config_manage')
      <div id="society-section" class="cfg-section">

    @if(session('success'))
    <div class="alert-flash alert-flash-success mb-3">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- ══════════════════════
         4 Cards — Home View
    ══════════════════════ --}}
    <div id="sc-cards-view">
        <div class="row g-3">

            {{-- Logo --}}
            <div class="col-md-4 col-sm-6">
                <div class="modern-cfg-card" onclick="scOpenPanel('sc-panel-logo')" style="cursor:pointer;">
                    <div class="card-inner">
                        <div class="card-head-flex">
                            <div class="icon-box" style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);">
                                <i class="bi bi-image-fill"></i>
                            </div>
                        </div>
                        <div class="card-body-content">
                            <h4>Society Logo</h4>
                            <p>Upload the logo shown on all receipts, PDFs and documents.</p>
                            <div class="card-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Status</span>
                                    <span class="stat-value" style="font-size:13px;">
                                        {{ !empty($settings['society_logo']) ? '✓ Uploaded' : 'Not set' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer-action">
                           <a href="{{route('settings.logo.view')}}"> <span class="view-link" style="color:#1e3a8a;">Configure Logo <i class="bi bi-chevron-right"></i></span></a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Identity --}}
            <div class="col-md-4 col-sm-6">
                <div class="modern-cfg-card" onclick="scOpenPanel('sc-panel-identity')" style="cursor:pointer;">
                    <div class="card-inner">
                        <div class="card-head-flex">
                            <div class="icon-box" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                                <i class="bi bi-building-fill"></i>
                            </div>
                        </div>
                        <div class="card-body-content">
                            <h4>Society Identity</h4>
                            <p>Set name, tagline, address and available plot sizes.</p>
                            <div class="card-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Name</span>
                                    <span class="stat-value" style="font-size:13px;">{{ $settings['society_name'] ?? 'Not set' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer-action">
                           <a href="{{ route('settings.identity.show') }}"> <span class="view-link text-success">Configure Identity <i class="bi bi-chevron-right"></i></span></a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Finance --}}
            <div class="col-md-4 col-sm-6">
                <div class="modern-cfg-card" onclick="scOpenPanel('sc-panel-finance')" style="cursor:pointer;">
                    <div class="card-inner">
                        <div class="card-head-flex">
                            <div class="icon-box" style="background:linear-gradient(135deg,#7c3aed,#a855f7);">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                        <div class="card-body-content">
                            <h4>Finance Defaults</h4>
                            <p>Set currency, transfer fee, late fine % and grace period.</p>
                            <div class="card-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Currency</span>
                                    <span class="stat-value" style="font-size:13px;">{{ $settings['currency_symbol'] ?? 'PKR' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer-action">
                           <a href="{{ route('settings.finance.show') }}"> <span class="view-link" style="color:#7c3aed;">Configure Finance <i class="bi bi-chevron-right"></i></span></a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Docs & QR --}}
            <div class="col-md-4 col-sm-6">
                <div class="modern-cfg-card" onclick="scOpenPanel('sc-panel-docs')" style="cursor:pointer;">
                    <div class="card-inner">
                        <div class="card-head-flex">
                            <div class="icon-box" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                        </div>
                        <div class="card-body-content">
                            <h4>Documents </h4>
                            <p>ID prefixes, watermark, footer note and QR code options.</p>
                            <div class="card-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Receipt Prefix</span>
                                    <span class="stat-value" style="font-size:13px;">{{ $settings['receipt_prefix'] ?? 'REC' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer-action">
                            <a href="{{ route('settings.docs.show') }}"><span class="view-link" style="color:#d97706;">Configure Docs <i class="bi bi-chevron-right"></i></span></a>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Config Email --}}
            <div class="col-md-4 col-sm-6">
                <div class="modern-cfg-card" onclick="scOpenPanel('sc-panel-docs')" style="cursor:pointer;">
                    <div class="card-inner">
                        <div class="card-head-flex">
                            <div class="icon-box" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
                               <i class="fa-regular fa-envelope"></i>
                            </div>
                        </div>
                        <div class="card-body-content">
                            <h4>Email Configure</h4>
                            <p>Here You will Configure your own Email.</p>
                            <div class="card-stats">

                            </div>
                        </div>
                        <div class="card-footer-action">
                            <a href="{{ route('settings.email.show') }}"><span class="view-link" style="color:#d97706;">Configure Email <i class="bi bi-chevron-right"></i></span></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    {{-- ════════════════════════════
         Panel: Logo
    ════════════════════════════ --}}
    <div id="sc-panel-logo" class="sc-panel" style="display:none;">
        <button class="sc-back" onclick="scClosePanel()">
            <i class="bi bi-arrow-left-circle-fill"></i> Back to Society Config
        </button>
        <div class="profile-hero-card">
            <form action="{{ route('settings.society.logo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-section-label">
                    <div class="lbl-icon" style="background:#eff6ff;">
                        <i class="bi bi-image-fill" style="color:#1e3a8a;"></i>
                    </div>
                    <h5>Society Logo</h5>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;
                                    padding:24px;background:#f8fafc;
                                    border:2px dashed #e2e8f0;border-radius:14px;">
                            @if(!empty($settings['society_logo']))
                                <img src="{{ asset('storage/'.$settings['society_logo']) }}"
                                     id="logoPreview"
                                     style="width:90px;height:90px;border-radius:14px;
                                            object-fit:contain;border:2px solid #e8edf3;
                                            padding:6px;background:#fff;">
                            @else
                                <div id="logoPreview"
                                     style="width:90px;height:90px;border-radius:14px;
                                            background:#f1f5f9;display:flex;
                                            align-items:center;justify-content:center;
                                            border:2px dashed #e2e8f0;">
                                    <i class="bi bi-building" style="font-size:2rem;color:#94a3b8;"></i>
                                </div>
                            @endif
                            <div>
                                <label for="logoUpload" class="btn btn-outline-primary btn-sm" style="cursor:pointer;">
                                    <i class="bi bi-upload me-1"></i> Choose Logo
                                </label>
                                <input type="file" id="logoUpload" name="society_logo"
                                       accept="image/*" style="display:none;"
                                       onchange="previewLogo(this)">
                                <p class="text-muted mt-2 mb-0" style="font-size:11px;">
                                    PNG, SVG or JPG — max 2MB
                                </p>
                                <p id="logoFileName" style="font-size:11px;color:#1e3a8a;font-weight:700;margin-top:3px;"></p>
                            </div>
                        </div>
                        @error('society_logo')
                            <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-light me-2" onclick="scClosePanel()">Cancel</button>
                    <button type="submit" class="cfg-save-btn">
                        <i class="bi bi-check-lg me-1"></i> Save Logo
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ════════════════════════════
         Panel: Identity
    ════════════════════════════ --}}
    <div id="sc-panel-identity" class="sc-panel" style="display:none;">
        <button class="sc-back" onclick="scClosePanel()">
            <i class="bi bi-arrow-left-circle-fill"></i> Back to Society Config
        </button>
        <div class="profile-hero-card">
            <form action="{{ route('settings.society.identity') }}" method="POST">
                @csrf
                <div class="form-section-label">
                    <div class="lbl-icon" style="background:#f0fdf4;">
                        <i class="bi bi-building-fill" style="color:#16a34a;"></i>
                    </div>
                    <h5>Society Identity</h5>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label cfg-label">Society Name <span class="text-danger">*</span></label>
                        <input type="text" name="society_name" class="form-control custom-input"
                               value="{{ old('society_name', $settings['society_name'] ?? 'Zamar Valley') }}" required>
                        @error('society_name')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label cfg-label">Tagline / Slogan</label>
                        <input type="text" name="society_tagline" class="form-control custom-input"
                               value="{{ old('society_tagline', $settings['society_tagline'] ?? '') }}"
                               placeholder="e.g. Premium Housing Project">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label cfg-label">Contact Phone</label>
                        <input type="text" name="society_phone" class="form-control custom-input"
                               value="{{ old('society_phone', $settings['society_phone'] ?? '') }}"
                               placeholder="+92-300-0000000">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label cfg-label">Contact Email</label>
                        <input type="email" name="society_email" class="form-control custom-input"
                               value="{{ old('society_email', $settings['society_email'] ?? '') }}"
                               placeholder="info@society.com">
                        @error('society_email')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label cfg-label">Office Address</label>
                        <textarea name="society_address" class="form-control custom-input" rows="2"
                                  placeholder="Full office address...">{{ old('society_address', $settings['society_address'] ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label cfg-label">Default Plot Unit</label>
                        <select name="default_plot_unit" class="form-select custom-input">
                            @foreach(['Marla','Kanal','Sq Ft','Sq Yard','Sq Meter'] as $unit)
                                <option value="{{ $unit }}"
                                    {{ old('default_plot_unit', $settings['default_plot_unit'] ?? 'Marla') === $unit ? 'selected' : '' }}>
                                    {{ $unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label cfg-label">Available Plot Sizes</label>
                        <input type="text" name="default_plot_sizes" class="form-control custom-input"
                               value="{{ old('default_plot_sizes', $settings['default_plot_sizes'] ?? '3,5,7,10,20') }}"
                               placeholder="3,5,7,10,20">
                        <div class="form-text">Comma separated values</div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-light me-2" onclick="scClosePanel()">Cancel</button>
                    <button type="submit" class="cfg-save-btn">
                        <i class="bi bi-check-lg me-1"></i> Save Identity
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ════════════════════════════
         Panel: Finance
    ════════════════════════════ --}}
    <div id="sc-panel-finance" class="sc-panel" style="display:none;">
        <button class="sc-back" onclick="scClosePanel()">
            <i class="bi bi-arrow-left-circle-fill"></i> Back to Society Config
        </button>
        <div class="profile-hero-card">
            <form action="{{ route('settings.society.finance') }}" method="POST">
                @csrf
                <div class="form-section-label">
                    <div class="lbl-icon" style="background:#fdf4ff;">
                        <i class="bi bi-cash-stack" style="color:#7c3aed;"></i>
                    </div>
                    <h5>Finance Defaults</h5>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label cfg-label">Currency <span class="text-danger">*</span></label>
                        <select name="currency_symbol" class="form-select custom-input">
                            @foreach(['PKR'=>'PKR — Pakistani Rupee','USD'=>'USD — US Dollar','AED'=>'AED — UAE Dirham','SAR'=>'SAR — Saudi Riyal'] as $v => $l)
                                <option value="{{ $v }}"
                                    {{ old('currency_symbol', $settings['currency_symbol'] ?? 'PKR') === $v ? 'selected' : '' }}>
                                    {{ $l }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label cfg-label">Transfer Fee <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:11px;font-weight:700;">PKR</span>
                            <input type="number" name="default_transfer_fee" class="form-control custom-input"
                                   value="{{ old('default_transfer_fee', $settings['default_transfer_fee'] ?? '50000') }}"
                                   min="0" step="1000">
                        </div>
                        <div class="form-text">Auto-filled on transfers</div>
                        @error('default_transfer_fee')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label cfg-label">Late Fine % <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="late_fine_percent" class="form-control custom-input"
                                   value="{{ old('late_fine_percent', $settings['late_fine_percent'] ?? '2') }}"
                                   min="0" max="100" step="0.5">
                            <span class="input-group-text" style="font-size:11px;font-weight:700;">%</span>
                        </div>
                        <div class="form-text">Per month on overdue</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label cfg-label">Grace Period <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="installment_grace_days" class="form-control custom-input"
                                   value="{{ old('installment_grace_days', $settings['installment_grace_days'] ?? '10') }}"
                                   min="0" max="60">
                            <span class="input-group-text" style="font-size:11px;font-weight:700;">days</span>
                        </div>
                        <div class="form-text">After due before fine applies</div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-light me-2" onclick="scClosePanel()">Cancel</button>
                    <button type="submit" class="cfg-save-btn">
                        <i class="bi bi-check-lg me-1"></i> Save Finance Settings
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ════════════════════════════
         Panel: Documents & QR
    ════════════════════════════ --}}
    <div id="sc-panel-docs" class="sc-panel" style="display:none;">
        <button class="sc-back" onclick="scClosePanel()">
            <i class="bi bi-arrow-left-circle-fill"></i> Back to Society Config
        </button>
        <div class="profile-hero-card">
            <form action="{{ route('settings.society.docs') }}" method="POST">
                @csrf

                {{-- ID Prefixes --}}
                <div class="form-section-label">
                    <div class="lbl-icon" style="background:#fef9c3;">
                        <i class="bi bi-file-earmark-text-fill" style="color:#d97706;"></i>
                    </div>
                    <h5>Document ID Prefixes</h5>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label cfg-label">Receipt Prefix</label>
                        <input type="text" name="receipt_prefix" class="form-control custom-input"
                               value="{{ old('receipt_prefix', $settings['receipt_prefix'] ?? 'REC') }}"
                               placeholder="REC" maxlength="10" style="font-family:monospace;">
                        <div class="form-text">e.g. REC → REC-0055</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label cfg-label">Booking Prefix</label>
                        <input type="text" name="booking_id_prefix" class="form-control custom-input"
                               value="{{ old('booking_id_prefix', $settings['booking_id_prefix'] ?? 'ZV') }}"
                               placeholder="ZV" maxlength="10" style="font-family:monospace;">
                        <div class="form-text">e.g. ZV → ZV-ABC-123</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label cfg-label">Deed Prefix</label>
                        <input type="text" name="deed_prefix" class="form-control custom-input"
                               value="{{ old('deed_prefix', $settings['deed_prefix'] ?? 'DEED') }}"
                               placeholder="DEED" maxlength="10" style="font-family:monospace;">
                        <div class="form-text">e.g. DEED → DEED-2025-001</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label cfg-label">Watermark Text</label>
                        <input type="text" name="doc_watermark_text" class="form-control custom-input"
                               value="{{ old('doc_watermark_text', $settings['doc_watermark_text'] ?? '') }}"
                               placeholder="Zamar Valley Official" maxlength="100">
                        <div class="form-text">Leave blank to disable watermark on PDFs</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label cfg-label">Receipt Footer Note</label>
                        <input type="text" name="receipt_footer_note" class="form-control custom-input"
                               value="{{ old('receipt_footer_note', $settings['receipt_footer_note'] ?? '') }}"
                               placeholder="Thank you for investing in Zamar Valley." maxlength="500">
                    </div>
                </div>

                <hr class="my-4 opacity-15">

                {{-- PDF Toggles --}}
                <div class="form-section-label">
                    <div class="lbl-icon" style="background:#fef2f2;">
                        <i class="bi bi-toggles" style="color:#dc2626;"></i>
                    </div>
                    <h5>PDF Options</h5>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;
                                    padding:14px 16px;display:flex;align-items:center;
                                    justify-content:space-between;gap:12px;">
                            <div>
                                <p style="margin:0;font-size:13px;font-weight:700;color:#0f172a;">
                                    QR Code on Documents
                                </p>
                                <p style="margin:3px 0 0;font-size:11px;color:#64748b;">
                                    Scan to verify — shown on receipts & deeds
                                </p>
                            </div>
                            <label style="position:relative;width:46px;height:26px;flex-shrink:0;">
                                <input type="checkbox" name="qr_on_documents"
                                       {{ old('qr_on_documents', $settings['qr_on_documents'] ?? '1') === '1' ? 'checked' : '' }}
                                       style="opacity:0;width:0;height:0;">
                                <span class="toggle-track"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;
                                    padding:14px 16px;display:flex;align-items:center;
                                    justify-content:space-between;gap:12px;">
                            <div>
                                <p style="margin:0;font-size:13px;font-weight:700;color:#0f172a;">
                                    Logo on Receipts
                                </p>
                                <p style="margin:3px 0 0;font-size:11px;color:#64748b;">
                                    Print society logo on all generated PDFs
                                </p>
                            </div>
                            <label style="position:relative;width:46px;height:26px;flex-shrink:0;">
                                <input type="checkbox" name="show_logo_on_receipt"
                                       {{ old('show_logo_on_receipt', $settings['show_logo_on_receipt'] ?? '1') === '1' ? 'checked' : '' }}
                                       style="opacity:0;width:0;height:0;">
                                <span class="toggle-track"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-light me-2" onclick="scClosePanel()">Cancel</button>
                    <button type="submit" class="cfg-save-btn">
                        <i class="bi bi-check-lg me-1"></i> Save Document Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
        @endcan

        {{-- ░░ Location Section ░░ --}}
        @can('location_manage')
        <div id="location" class="cfg-section">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="modern-cfg-card">
                        <div class="card-inner">
                            <div class="card-head-flex">
                                <div class="icon-box city-gradient"><i class="bi bi-geo-alt-fill"></i></div>
                                <button class="action-plus-btn" title="Add New City"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <div class="card-body-content">
                                <h4>Cities</h4>
                                <p>Configure operational cities in the country.</p>
                                <div class="card-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Active</span>
                                        <span class="stat-value">{{ $citiesCount }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer-action">
                                <a href="{{ route('city.view') }}" class="view-link">Explore Locations <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="modern-cfg-card">
                        <div class="card-inner">
                            <div class="card-head-flex">
                                <div class="icon-box property-gradient"><i class="bi bi-houses-fill"></i></div>
                                <button class="action-plus-btn" title="Add Property Type"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <div class="card-body-content">
                                <h4>Property Categories</h4>
                                <p>Manage Residential, Commercial, and Industrial plot classifications.</p>
                                <div class="card-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Defined</span>
                                        <span class="stat-value">{{ $propertyFeatures }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer-action">
                                <a href="{{ route('property.feature.view') }}" class="view-link text-success">Manage Types <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="modern-cfg-card">
                        <div class="card-inner">
                            <div class="card-head-flex">
                                <div class="icon-box property-gradient"><i class="bi bi-houses-fill"></i></div>
                                <button class="action-plus-btn" title="Add Block"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <div class="card-body-content">
                                <h4>Property Blocks</h4>
                                <p>Manage Different Blocks like Block A etc.</p>
                                <div class="card-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Defined</span>
                                        <span class="stat-value">{{ $blocks }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer-action">
                                <a href="{{ route('blocks.index') }}" class="view-link text-success">Manage Types <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="modern-cfg-card">
                        <div class="card-inner">
                            <div class="card-head-flex">
                                <div class="icon-box property-gradient"><i class="bi bi-houses-fill"></i></div>
                                <button class="action-plus-btn" title="Add Sector"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <div class="card-body-content">
                                <h4>Sector</h4>
                                <p>Manage Different Sectors.</p>
                                <div class="card-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Sectors</span>
                                        <span class="stat-value">{{ $sectors }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer-action">
                                <a href="{{ route('sector.view') }}" class="view-link text-success">Manage Types <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="modern-cfg-card">
                        <div class="card-inner">
                            <div class="card-head-flex">
                                <div class="icon-box property-gradient"><i class="bi bi-houses-fill"></i></div>
                                <button class="action-plus-btn" title="Add Society"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <div class="card-body-content">
                                <h4>Society</h4>
                                <p>Manage Different Society.</p>
                                <div class="card-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Society</span>
                                        <span class="stat-value">{{ $Society }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer-action">
                                <a href="{{ route('society.view') }}" class="view-link text-success">Manage Types <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

    </div>{{-- /cfg-body --}}
</div>


{{-- ══════════════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════════════ --}}


{{-- Add Permission Modal --}}
@can('role_manage')
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header pt-4 px-4">
                <h5 class="modal-title">Add System Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="e.g. edit_booking" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3">Create Permission</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script>
// ── Tab switching (activates first visible tab) ───────────────
document.addEventListener('DOMContentLoaded', function () {
    const tabs     = document.querySelectorAll('.cfg-tab');
    const sections = document.querySelectorAll('.cfg-section');

    // Clear defaults, activate first rendered tab
    tabs.forEach(t => t.classList.remove('active'));
    sections.forEach(s => s.classList.remove('active'));

    if (tabs.length > 0) {
        tabs[0].classList.add('active');
        const first = document.getElementById(tabs[0].dataset.target);
        if (first) first.classList.add('active');
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));
            tab.classList.add('active');
            const target = document.getElementById(tab.dataset.target);
            if (target) target.classList.add('active');
        });
    });
});

// ── Logo preview ──────────────────────────────────────────────
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.id        = 'logoPreview';
                img.src       = e.target.result;
                img.className = 'logo-preview-img';
                preview.replaceWith(img);
            }
        };
        reader.readAsDataURL(input.files[0]);
        const fn = document.getElementById('logoFileName');
        if (fn) fn.textContent = input.files[0].name;
    }
}

// ── Live contact preview ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const phoneInput  = document.querySelector('input[name="society_phone"]');
    const emailInput  = document.querySelector('input[name="society_email"]');
    const previewPhone = document.getElementById('previewPhone');
    const previewEmail = document.getElementById('previewEmail');

    if (phoneInput && previewPhone) {
        phoneInput.addEventListener('input', function () {
            previewPhone.textContent = this.value || '+92-XXX-XXXXXXX';
        });
    }
    if (emailInput && previewEmail) {
        emailInput.addEventListener('input', function () {
            previewEmail.textContent = this.value || 'info@society.com';
        });
    }
});
</script>
@endpush
