{{-- ══════════════════════════════════════════════════════════════
     resources/views/settings/society/panel_styles.blade.php
     @include this ONCE inside @push('styles') in settings/index.blade.php
══════════════════════════════════════════════════════════════ --}}
<style>
/* ── Panel wrapper ────────────────────────────────────────── */
.sv-panel-wrap {
    animation: svIn .22s ease;
}
@keyframes svIn {
    from { opacity:0; transform:translateX(14px); }
    to   { opacity:1; transform:translateX(0); }
}

/* ── Header ────────────────────────────────────────────────── */
.sv-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 22px;
    background: var(--accent-light, #f8fafc);
    border: 1.5px solid var(--accent-mid, #e2e8f0);
    border-radius: 16px;
    margin-bottom: 22px;
    flex-wrap: wrap;
}
.sv-header-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.4rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
}
.sv-header-text { flex: 1; min-width: 160px; }
.sv-header-text h4 {
    font-size: 16px; font-weight: 800;
    color: #0f172a; margin: 0 0 3px;
}
.sv-header-text p {
    font-size: 11px; color: #64748b; margin: 0;
}

/* ── Badges ────────────────────────────────────────────────── */
.sv-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700;
    padding: 5px 12px; border-radius: 20px;
}
.sv-badge-green  { background:#dcfce7; color:#15803d; }
.sv-badge-gray   { background:#f1f5f9; color:#475569; }
.sv-badge-purple { background:#ede9fe; color:#6d28d9; }
.sv-badge-amber  { background:#fef9c3; color:#a16207; }

/* ── Body ──────────────────────────────────────────────────── */
.sv-body {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ── Blocks (grouped sections inside body) ─────────────────── */
.sv-block {
    background: #fff;
    border: 1.5px solid #e8edf3;
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: 0 1px 4px rgba(15,23,42,.04);
}
.sv-block-label {
    display: flex; align-items: center; gap: 7px;
    font-size: 11px; font-weight: 700;
    color: #1e3a8a;
    text-transform: uppercase; letter-spacing: .6px;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1.5px solid #f1f5f9;
}

/* ── Grid layouts ──────────────────────────────────────────── */
.sv-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.sv-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
@media(max-width:640px) {
    .sv-grid-2, .sv-grid-3 { grid-template-columns: 1fr; }
}

/* ── Fields ────────────────────────────────────────────────── */
.sv-field { display: flex; flex-direction: column; gap: 5px; }
.sv-label {
    font-size: 11px; font-weight: 700;
    color: #475569; text-transform: uppercase; letter-spacing: .5px;
}
.sv-req { color: #dc2626; }
.sv-hint { font-size: 10px; color: #94a3b8; margin: 0; }
.sv-error {
    font-size: 11px; color: #dc2626; margin-top: 2px;
    display: flex; align-items: center; gap: 4px;
}

/* ── Inputs ────────────────────────────────────────────────── */
.sv-input {
    width: 100%; padding: 10px 13px;
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: 13px; font-family: inherit;
    color: #0f172a; background: #f8fafc;
    outline: none;
    transition: border-color .15s, background .15s, box-shadow .15s;
}
.sv-input:focus {
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(59,130,246,.09);
}
.sv-textarea { resize: vertical; min-height: 76px; }
.sv-select   { cursor: pointer; }
.sv-mono     { font-family: 'Courier New', monospace; letter-spacing: .5px; font-size: 13px; }

/* Input with left icon */
.sv-input-icon-wrap { position: relative; }
.sv-icon {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8; font-size: 13px; pointer-events: none;
}
.sv-has-icon { padding-left: 36px; }

/* Input with prefix */
.sv-input-prefix { display: flex; }
.sv-pre {
    background: #f1f5f9; border: 1.5px solid #e2e8f0;
    border-right: none; border-radius: 10px 0 0 10px;
    padding: 10px 11px; font-size: 11px; font-weight: 700;
    color: #64748b; white-space: nowrap; display: flex;
    align-items: center;
}
.sv-prefixed { border-radius: 0 10px 10px 0; }

/* Input with suffix */
.sv-input-suffix { display: flex; }
.sv-suf {
    background: #f1f5f9; border: 1.5px solid #e2e8f0;
    border-left: none; border-radius: 0 10px 10px 0;
    padding: 10px 11px; font-size: 11px; font-weight: 700;
    color: #64748b; white-space: nowrap; display: flex;
    align-items: center;
}
.sv-suffixed { border-radius: 10px 0 0 10px; flex: 1; }

/* ── Preview strip (contact/footer preview) ────────────────── */
.sv-preview-strip {
    display: flex; align-items: center; gap: 10px;
    flex-wrap: wrap;
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 11px 14px;
}
.sv-preview-label {
    font-size: 10px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .5px;
    display: flex; align-items: center; gap: 5px;
    white-space: nowrap;
}
.sv-preview-content {
    display: flex; align-items: center; gap: 7px;
    font-size: 12px; color: #0f172a; flex-wrap: wrap;
}
.sv-preview-sep { color: #cbd5e1; font-weight: 300; }

/* ── Prefix preview (doc IDs) ──────────────────────────────── */
.sv-prefix-preview {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: monospace; font-size: 11px;
    font-weight: 700; color: #1e3a8a;
    background: #eff6ff; padding: 3px 9px;
    border-radius: 6px; margin-top: 4px;
    width: fit-content;
}

/* ── Finance calculator strip ──────────────────────────────── */
.sv-calc-strip {
    background: #fff7ed; border: 1.5px solid #fed7aa;
    border-radius: 13px; padding: 14px 18px;
}
.sv-calc-label {
    font-size: 11px; font-weight: 700; color: #ea580c;
    text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: 12px; display: flex; align-items: center; gap: 6px;
}
.sv-calc-row {
    display: flex; align-items: center;
    gap: 12px; flex-wrap: wrap;
}
.sv-calc-item {
    display: flex; flex-direction: column;
    align-items: center; gap: 3px;
    background: #fff; border: 1px solid #fed7aa;
    border-radius: 10px; padding: 10px 16px; min-width: 90px;
}
.sv-calc-item.sv-calc-result {
    background: #fff7ed; border-color: #ea580c; min-width: 160px;
}
.sv-calc-val  { font-size: 16px; font-weight: 800; color: #0f172a; }
.sv-calc-key  { font-size: 10px; color: #94a3b8; white-space: nowrap; }
.sv-calc-arrow { font-size: 18px; color: #fed7aa; }

/* ── Logo drop zone ────────────────────────────────────────── */
.sv-logo-zone {
    display: flex; gap: 0;
    background: #f8fafc; border: 2px dashed #e2e8f0;
    border-radius: 16px; overflow: hidden;
    transition: border-color .15s, background .15s;
    flex-wrap: wrap;
}
.sv-logo-zone.sv-dz-active {
    border-color: #3b82f6; background: #eff6ff;
}
.sv-logo-current {
    width: 200px; flex-shrink: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 10px; padding: 28px 20px;
    background: #fff; border-right: 2px dashed #e2e8f0;
}
.sv-logo-label {
    font-size: 10px; font-weight: 700;
    color: #94a3b8; text-transform: uppercase; letter-spacing: .5px;
}
.sv-logo-img {
    width: 100px; height: 100px; border-radius: 14px;
    object-fit: contain; border: 2px solid #e8edf3;
    padding: 6px; background: #fff;
}
.sv-logo-empty {
    width: 100px; height: 100px; border-radius: 14px;
    background: #f1f5f9; border: 2px dashed #e2e8f0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 6px; font-size: 10px; color: #94a3b8;
}
.sv-logo-empty i { font-size: 2rem; color: #cbd5e1; }
.sv-logo-filename {
    font-size: 10px; color: #1e3a8a; font-weight: 700;
    text-align: center; max-width: 140px;
    word-break: break-all; margin: 0;
}
.sv-logo-divider { display: none; }
.sv-logo-upload {
    flex: 1; min-width: 200px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 32px 24px; text-align: center;
}
.sv-upload-icon {
    font-size: 2.4rem; color: #94a3b8;
    display: block; margin-bottom: 10px;
}
.sv-upload-title {
    font-size: 14px; font-weight: 700;
    color: #475569; margin: 0 0 5px;
}
.sv-upload-sub {
    font-size: 11px; color: #94a3b8;
    margin: 0 0 16px;
}
.sv-upload-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: #eff6ff; border: 1.5px solid #bfdbfe;
    color: #1d4ed8; padding: 9px 20px; border-radius: 10px;
    font-size: 12px; font-weight: 700; cursor: pointer;
    transition: background .15s;
}
.sv-upload-btn:hover { background: #dbeafe; }
.sv-upload-rules {
    display: flex; gap: 12px; margin-top: 14px;
    flex-wrap: wrap; justify-content: center;
}
.sv-upload-rules span {
    font-size: 10px; color: #64748b;
    display: flex; align-items: center; gap: 3px;
}
.sv-upload-rules i { color: #16a34a; }

/* ── Info strip (logo usage) ───────────────────────────────── */
.sv-info-strip {
    background: #eff6ff; border: 1.5px solid #bfdbfe;
    border-radius: 12px; padding: 14px 16px;
}
.sv-info-title {
    font-size: 11px; font-weight: 700; color: #1e3a8a;
    margin-bottom: 10px;
    display: flex; align-items: center; gap: 6px;
}
.sv-info-tags {
    display: flex; flex-wrap: wrap; gap: 8px;
}
.sv-tag {
    display: inline-flex; align-items: center; gap: 5px;
    background: #fff; border: 1px solid #bfdbfe;
    color: #1e3a8a; font-size: 11px; font-weight: 600;
    padding: 4px 10px; border-radius: 20px;
}

/* ── Toggle cards ──────────────────────────────────────────── */
.sv-toggles-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 14px;
}
@media(max-width:640px) { .sv-toggles-grid { grid-template-columns: 1fr; } }

.sv-toggle-card {
    background: #f8fafc; border: 1.5px solid #e2e8f0;
    border-radius: 14px; padding: 18px;
    transition: border-color .2s;
}
.sv-toggle-card:has(input:checked) {
    border-color: #1e3a8a;
    background: linear-gradient(135deg, #f8fbff, #eff6ff);
}
.sv-toggle-top {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.sv-toggle-ico {
    width: 48px; height: 48px; border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
}
.sv-toggle-title {
    font-size: 13px; font-weight: 800; color: #0f172a;
    margin-bottom: 4px;
}
.sv-toggle-desc {
    font-size: 11px; color: #64748b; line-height: 1.5;
    margin-bottom: 10px;
}
.sv-toggle-status { margin-top: 8px; }
.sv-status-on {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700; color: #16a34a;
    background: #dcfce7; padding: 3px 10px; border-radius: 20px;
}
.sv-status-off {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700; color: #64748b;
    background: #f1f5f9; padding: 3px 10px; border-radius: 20px;
}

/* ── Toggle switch ─────────────────────────────────────────── */
.sv-switch { position: relative; width: 48px; height: 28px; cursor: pointer; flex-shrink: 0; }
.sv-switch input { opacity:0; width:0; height:0; }
.sv-sw-track {
    position: absolute; inset: 0;
    background: #e2e8f0; border-radius: 99px;
    transition: background .2s;
}
.sv-sw-track::before {
    content: ''; position: absolute;
    width: 22px; height: 22px; border-radius: 50%;
    background: #fff; top: 3px; left: 3px;
    box-shadow: 0 1px 5px rgba(0,0,0,.2);
    transition: transform .2s;
}
.sv-switch input:checked + .sv-sw-track { background: #1e3a8a; }
.sv-switch input:checked + .sv-sw-track::before { transform: translateX(20px); }

/* ── Back button ───────────────────────────────────────────── */
.sc-back {
    background: none; border: none; cursor: pointer;
    font-size: 13px; font-weight: 700; color: #475569;
    padding: 0; margin-bottom: 20px;
    display: inline-flex; align-items: center; gap: 7px;
    font-family: inherit; transition: color .15s;
}
.sc-back:hover { color: #1e3a8a; }
.sc-back i { font-size: 17px; }

/* ── Footer (save/cancel bar) ──────────────────────────────── */
.sv-footer {
    display: flex; justify-content: flex-end; gap: 10px;
    margin-top: 24px; padding-top: 16px;
    border-top: 1.5px solid #f1f5f9;
}
.sv-btn-cancel {
    background: #f1f5f9; border: none; color: #475569;
    padding: 10px 20px; border-radius: 10px;
    font-size: 13px; font-weight: 700; cursor: pointer;
    font-family: inherit;
    display: inline-flex; align-items: center; gap: 6px;
    transition: background .15s;
}
.sv-btn-cancel:hover { background: #e2e8f0; }
.sv-btn-save {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    border: none; color: #fff; padding: 10px 28px;
    border-radius: 10px; font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    display: inline-flex; align-items: center; gap: 6px;
    box-shadow: 0 4px 14px rgba(30,58,138,.25);
    transition: opacity .15s;
}
.sv-btn-save:hover { opacity: .88; }

/* ── Hover lift on main cards ──────────────────────────────── */
.modern-cfg-card { transition: transform .2s, box-shadow .2s; }
.modern-cfg-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.1) !important;
}

/* ── Panel animation ───────────────────────────────────────── */
.sc-panel { animation: scFadeIn .22s ease; }
@keyframes scFadeIn {
    from { opacity:0; transform:translateX(12px); }
    to   { opacity:1; transform:translateX(0); }
}
</style>
