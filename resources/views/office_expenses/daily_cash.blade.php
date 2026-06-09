@extends('layouts.index')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --navy: #0f172a; --blue: #1e3a8a; --green: #16a34a;
    --red: #dc2626; --slate: #64748b; --border: #e8edf3;
    --bg: #f1f5fb; --card: #ffffff; --radius: 14px;
    --shadow: 0 2px 12px rgba(15,23,42,.07);
}
* { box-sizing: border-box; }
body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; }

.page-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    border-radius: 18px; padding: 22px 28px; margin-bottom: 22px;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 14px; position: relative; overflow: hidden;
}
.page-header-title { font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0; }
.page-header-sub   { font-size: 11px; color: rgba(255,255,255,.5); margin: 4px 0 0; }

/* Date Picker Bar */
.date-bar {
    background: var(--card); border-radius: var(--radius);
    border: 1px solid var(--border); box-shadow: var(--shadow);
    padding: 16px 20px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
}

/* Summary Grid */
.summary-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 20px; }
@media(max-width:700px) { .summary-grid { grid-template-columns: 1fr 1fr; } }
.sum-card {
    background: var(--card); border-radius: var(--radius);
    border: 1px solid var(--border); box-shadow: var(--shadow);
    padding: 18px 20px; position: relative; overflow: hidden;
}
.sum-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: var(--accent, #3b82f6); }
.sum-label { font-size: 10px; font-weight: 700; color: var(--slate); text-transform: uppercase; letter-spacing: .6px; }
.sum-val   { font-size: 22px; font-weight: 800; margin: 6px 0 2px; }
.sum-sub   { font-size: 10px; color: var(--slate); }

/* NET balance card */
.net-card {
    border-radius: var(--radius); padding: 20px 24px; margin-bottom: 22px;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;
}
.net-card.positive { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1.5px solid #86efac; }
.net-card.negative { background: linear-gradient(135deg, #fef2f2, #ffe4e6); border: 1.5px solid #fecaca; }
.net-card.zero     { background: #f8fafc; border: 1.5px solid var(--border); }

/* Section Card */
.section-card { background: var(--card); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; margin-bottom: 18px; }
.sec-head { padding: 12px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; }
.sec-head-icon { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
.sec-head-title { font-size: 13px; font-weight: 800; color: var(--navy); }
.sec-head-sub   { font-size: 11px; color: var(--slate); }
.sec-head-amt   { margin-left: auto; font-size: 15px; font-weight: 800; }

.ledger-table { width: 100%; border-collapse: collapse; }
.ledger-table thead th { font-size: 10px; text-transform: uppercase; letter-spacing: .6px; color: var(--slate); font-weight: 700; background: #fafbfc; border-bottom: 1.5px solid var(--border); padding: 9px 14px; }
.ledger-table tbody td { padding: 10px 14px; border-bottom: 1px solid #f8fafc; font-size: 12px; vertical-align: middle; }
.ledger-table tbody tr:last-child td { border-bottom: none; }

.empty-row { text-align: center; padding: 28px; color: #cbd5e1; font-size: 12px; }

.badge-pill { font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 20px; }
.bp-green  { background: #dcfce7; color: #15803d; }
.bp-blue   { background: #eff6ff; color: #1d4ed8; }
.bp-amber  { background: #fef9c3; color: #92400e; }
.bp-purple { background: #fdf4ff; color: #7c3aed; }

/* Loading overlay */
#report-loading {
    display: none;
    position: absolute; inset: 0;
    background: rgba(241,245,251,.75);
    border-radius: var(--radius);
    align-items: center; justify-content: center;
    z-index: 10;
    backdrop-filter: blur(2px);
}
#report-wrapper { position: relative; }
#report-wrapper.is-loading #report-loading { display: flex; }
#report-wrapper.is-loading #report-content { opacity: .4; pointer-events: none; }

.nav-btn {
    background:#f1f5f9;border:1px solid var(--border);color:var(--slate);
    padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;
    cursor:pointer;transition:background .15s;
}
.nav-btn:hover { background:#e2e8f0; }
.nav-btn:disabled { opacity:.5; cursor:not-allowed; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">

{{-- Header --}}
<div class="page-header">
    <div>
        <p class="page-header-title"><i class="bi bi-calendar-day-fill" style="margin-right:8px;opacity:.7;"></i>Daily Cash Summary</p>
        <p class="page-header-sub">All cash in and out for a single day</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;position:relative;z-index:1;">
        <a href="{{ route('office_expenses.view') }}" style="background:rgba(255,255,255,.15);color:#fff;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="{{ route('reports.monthly_summary') }}" style="background:rgba(255,255,255,.15);color:#fff;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="bi bi-bar-chart"></i> Monthly Report
        </a>
    </div>
</div>

{{-- Date Selector --}}
<div class="date-bar">
    <i class="bi bi-calendar3" style="font-size:18px;color:var(--blue);"></i>
    <span style="font-size:13px;font-weight:700;color:var(--navy);">Viewing:</span>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <input type="date" id="reportDate" value="{{ $date->format('Y-m-d') }}"
               class="form-control" style="width:180px;border:1.5px solid var(--border);border-radius:9px;padding:7px 12px;font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;">
        <button type="button" onclick="loadDate(document.getElementById('reportDate').value)"
                style="background:var(--blue);color:#fff;border:none;border-radius:9px;padding:8px 18px;font-size:13px;font-weight:700;cursor:pointer;">
            <i class="bi bi-eye"></i> View
        </button>
        <button type="button" onclick="downloadPdf()"
                style="background:#dc2626;color:#fff;border:none;border-radius:9px;padding:8px 18px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;" target = "_blank">
            <i class="bi bi-file-earmark-pdf-fill"></i> Generate PDF
        </button>
    </div>
    <span id="dateLabel" style="font-size:14px;font-weight:800;color:var(--navy);margin-left:4px;">
        {{ $date->format('l, d F Y') }}
    </span>
    {{-- Prev / Next day --}}
    <div style="margin-left:auto;display:flex;gap:6px;">
        <button id="btnPrev" class="nav-btn" data-date="{{ $date->copy()->subDay()->format('Y-m-d') }}">
            ‹ Prev
        </button>
        <button id="btnNext" class="nav-btn" data-date="{{ $date->copy()->addDay()->format('Y-m-d') }}">
            Next ›
        </button>
    </div>
</div>

{{-- Report content (swapped on AJAX) --}}
<div id="report-wrapper">
    <div id="report-loading">
        <div style="text-align:center;">
            <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem;"></div>
            <div style="font-size:12px;font-weight:700;color:var(--slate);margin-top:10px;">Loading…</div>
        </div>
    </div>
    <div id="report-content">
        @include('office_expenses._daily_cash_data')
    </div>
</div>

</div>

@push('scripts')
<script>
const dailyCashUrl = '{{ route('reports.daily_cash') }}';
const pdfUrl       = '{{ route('reports.daily_cash_pdf') }}';

function downloadPdf() {
    const date = document.getElementById('reportDate').value;

    // Construct the complete URL containing the target date filter string parameter
    const fullPdfUrl = pdfUrl + '?date=' + date;

    // FORCES THE BROWSER TO POP OPEN A BLANK WINDOW TAB FOR THE PDF FILE
    window.open(fullPdfUrl, '_blank');
}

function loadDate(dateStr) {
    if (!dateStr) return;

    const wrapper = document.getElementById('report-wrapper');
    wrapper.classList.add('is-loading');
    document.getElementById('btnPrev').disabled = true;
    document.getElementById('btnNext').disabled = true;

    fetch(dailyCashUrl + '?date=' + dateStr, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        // Swap content
        document.getElementById('report-content').innerHTML = data.html;

        // Update date bar controls
        document.getElementById('reportDate').value      = data.date;
        document.getElementById('dateLabel').textContent  = data.dateLabel;
        document.getElementById('btnPrev').dataset.date   = data.prevDate;
        document.getElementById('btnNext').dataset.date   = data.nextDate;

        // Update browser URL without reload
        history.pushState({ date: data.date }, '', dailyCashUrl + '?date=' + data.date);
    })
    .catch(() => alert('Failed to load data. Please try again.'))
    .finally(() => {
        wrapper.classList.remove('is-loading');
        document.getElementById('btnPrev').disabled = false;
        document.getElementById('btnNext').disabled = false;
    });
}

// Prev / Next buttons
document.getElementById('btnPrev').addEventListener('click', function () {
    loadDate(this.dataset.date);
});
document.getElementById('btnNext').addEventListener('click', function () {
    loadDate(this.dataset.date);
});

// Date picker — load on Enter key
document.getElementById('reportDate').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') loadDate(this.value);
});

// Handle browser back/forward
window.addEventListener('popstate', function (e) {
    if (e.state && e.state.date) loadDate(e.state.date);
});
</script>
@endpush

@endsection
