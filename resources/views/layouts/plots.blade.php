@extends('layouts.index')
@push('styles')
<style>
    div.dataTables_wrapper div.dataTables_info {
    padding: 14px 20px;
    font-size: 12px;
    color: var(--muted-text);
}
div.dataTables_wrapper div.dataTables_paginate {
    padding: 10px 20px 14px;
    text-align: right;
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    border-radius: 8px !important;
    padding: 0 10px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    color: var(--sub-text) !important;
    border: 1px solid transparent !important;
    margin: 0 2px !important;
    cursor: pointer;
    background: transparent !important;
    box-shadow: none !important;
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover {
    background: var(--hover-bg) !important;
    border-color: var(--border-color) !important;
    color: var(--text-main) !important;
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button.current,
div.dataTables_wrapper div.dataTables_paginate .paginate_button.current:hover {
    background: #1e3a8a !important;
    color: #fff !important;
    border-color: #1e3a8a !important;
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button.disabled,
div.dataTables_wrapper div.dataTables_paginate .paginate_button.disabled:hover {
    opacity: .35;
    cursor: not-allowed;
}
div.dataTables_wrapper div.dataTables_length { display: none; }
div.dataTables_wrapper div.dataTables_filter { display: none; }
/* ── Stat cards ── */
.plot-stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(148px,1fr));gap:10px;margin-bottom:22px;}
.plot-stat{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:12px 14px;display:flex;align-items:center;gap:10px;}
.plot-stat-icon{width:36px;height:36px;border-radius:9px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:14px;}
.plot-stat-label{font-size:9px;font-weight:700;color:var(--muted-text);text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;}
.plot-stat-val{font-size:17px;font-weight:800;color:var(--text-main);line-height:1.2;}

/* ── Tabs ── */
.page-tabs{display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border-color);}
.page-tab{padding:9px 18px;border-radius:8px 8px 0 0;font-size:13px;font-weight:700;cursor:pointer;border:none;background:transparent;color:var(--muted-text);border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s;display:flex;align-items:center;gap:6px;}
.page-tab:hover{color:var(--text-main);background:var(--hover-bg);}
.page-tab.active{color:#1e3a8a;border-bottom-color:#1e3a8a;background:var(--card-bg);}

/* ── DataTable overrides ── */
.dt-wrapper{background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;overflow:hidden;}
.dt-wrapper .dataTables_wrapper{overflow-x:auto;}
.dt-toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:16px 20px;border-bottom:1px solid var(--border-color);}
.dt-toolbar-left{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.dt-search-wrap{display:flex;align-items:center;border:1.5px solid var(--border-color);border-radius:9px;background:var(--input-bg);overflow:hidden;transition:border-color .15s;}
.dt-search-wrap:focus-within{border-color:#1e3a8a;}
.dt-search-wrap i{padding:0 11px;color:var(--muted-text);font-size:13px;}
.dt-search-wrap input{border:none;background:transparent;outline:none;font-size:13px;padding:8px 10px 8px 0;width:220px;color:var(--text-main);font-family:'Poppins',sans-serif;}
.dt-filter{border:1.5px solid var(--border-color);border-radius:9px;padding:8px 12px;font-size:12px;background:var(--input-bg);color:var(--text-main);outline:none;cursor:pointer;font-family:'Poppins',sans-serif;}
.dt-filter:focus{border-color:#1e3a8a;}

/* Override DataTables default styles */
table.dataTable{width:100% !important;border-collapse:collapse !important;}
table.dataTable thead th{font-size:9.5px !important;text-transform:uppercase !important;letter-spacing:.5px !important;color:var(--muted-text) !important;font-weight:700 !important;background:var(--table-head-bg) !important;border-bottom:2px solid var(--border-color) !important;padding:10px 10px !important;white-space:nowrap !important;cursor:pointer !important;user-select:none;}
table.dataTable thead th:hover{background:var(--hover-bg) !important;color:var(--text-main) !important;}
table.dataTable tbody td{padding:9px 10px !important;border-bottom:1px solid var(--table-border) !important;font-size:12px !important;vertical-align:middle !important;color:var(--text-main) !important;}
table.dataTable tbody tr:hover>td{background:var(--table-row-hover) !important;}
table.dataTable tbody tr:last-child td{border-bottom:none !important;}

/* DataTables sort icons */
table.dataTable thead .sorting::after,
table.dataTable thead .sorting_asc::after,
table.dataTable thead .sorting_desc::after{opacity:.5;font-size:10px;}

/* DT pagination */
.dataTables_wrapper .dataTables_paginate{padding:14px 20px;}
.dataTables_wrapper .dataTables_paginate .paginate_button{border-radius:8px !important;padding:5px 11px !important;font-size:12px !important;font-weight:700 !important;color:var(--sub-text) !important;border:1px solid transparent !important;margin:0 2px !important;}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover{background:var(--hover-bg) !important;border-color:var(--border-color) !important;color:var(--text-main) !important;}
.dataTables_wrapper .dataTables_paginate .paginate_button.current{background:#1e3a8a !important;color:#fff !important;border-color:#1e3a8a !important;}
.dataTables_wrapper .dataTables_info{padding:14px 20px;font-size:12px;color:var(--muted-text);}
.dataTables_wrapper .dataTables_length{display:none;}
.dataTables_wrapper .dataTables_filter{display:none;}/* we use our own search */
.dataTables_wrapper .dt-layout-row{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;}

/* ── Status badges ── */
.bs{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;white-space:nowrap;}
.bs-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0;}
.bs-available{background:#dcfce7;color:#15803d;}.bs-available .bs-dot{background:#16a34a;}
.bs-booked{background:#eff6ff;color:#1d4ed8;}.bs-booked .bs-dot{background:#3b82f6;}
.bs-sold{background:#fdf4ff;color:#7c3aed;}.bs-sold .bs-dot{background:#7c3aed;}
.bs-reserved{background:#fef9c3;color:#854d0e;}.bs-reserved .bs-dot{background:#ca8a04;}
.bs-on_hold{background:#fee2e2;color:#dc2626;}.bs-on_hold .bs-dot{background:#dc2626;}

/* ── Block cards ── */
.btype{display:inline-block;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;}
.btype-residential{background:#eff6ff;color:#1d4ed8;}
.btype-overseas{background:#fdf4ff;color:#7c3aed;}
.btype-commercial{background:#fff7ed;color:#ea580c;}
.btype-civic{background:#f0fdf4;color:#15803d;}
.btype-extension{background:#fef9c3;color:#854d0e;}
.blocks-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;padding:4px 0;}
.block-card{background:var(--card-bg);border:1.5px solid var(--border-color);border-radius:12px;padding:16px;display:flex;flex-direction:column;gap:10px;transition:box-shadow .15s,border-color .15s;}
.block-card:hover{box-shadow:0 4px 20px rgba(30,58,138,.1);border-color:#bfdbfe;}

/* ── Action buttons ── */
.act-btn{width:30px;height:30px;border-radius:8px;border:1px solid var(--border-color);background:var(--card-bg);display:inline-flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;text-decoration:none;color:var(--sub-text);transition:all .15s;}
.act-btn:hover{background:var(--hover-bg);}
.act-btn.edit:hover{background:#eff6ff;border-color:#3b82f6;color:#1d4ed8;}
.act-btn.del:hover{background:#fef2f2;border-color:#fecaca;color:#dc2626;}

/* ── Modals ── */
.modal-content{border:none;border-radius:16px;overflow:hidden;}
.modal-header{background:linear-gradient(135deg,#0f172a,#1e3a8a);padding:18px 24px;border:none;}
.modal-title{font-size:15px;font-weight:800;color:#fff;}
.modal-body{padding:24px;background:var(--card-bg);}
.modal-footer{background:var(--hover-bg);border-top:1px solid var(--border-color);padding:14px 24px;}
.plan-prev{display:none;flex-wrap:wrap;gap:8px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 14px;margin-top:8px;}
.plan-prev.show{display:flex;}
.pv-item{text-align:center;min-width:90px;}
.pv-lbl{font-size:9px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;display:block;}
.pv-val{font-size:13px;font-weight:800;color:#1e3a8a;display:block;margin-top:2px;}
.modal-sec{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;padding:6px 0 10px;display:flex;align-items:center;gap:7px;}
.no-data{text-align:center;padding:60px 20px;color:var(--muted-text);}
.no-data i{font-size:3rem;opacity:.18;display:block;margin-bottom:12px;}
</style>
@endpush

@section('content')
<div class="ldg-wrap">

{{-- HEADER --}}
<div class="rpt-header no-print">
    <div>
        <p class="rpt-header-title">Plots &amp; Blocks</p>
        <p class="rpt-header-sub">All plots · manage blocks · pricing plans — Zamar Valley &nbsp;·&nbsp; {{ now()->format('d M Y') }}</p>
    </div>
    <div class="rpt-header-actions">

        <a href="{{ route('plot.add') }}" class="btn-navy">
            <i class="fas fa-plus"></i> Add Plot
        </a>
    </div>
</div>

{{-- FLASH --}}
@if(session('success'))
<div class="alert-flash alert-flash-success mb-4"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert-flash alert-flash-error mb-4">
    <i class="fas fa-exclamation-circle"></i>
    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
</div>
@endif

{{-- STAT CARDS --}}
<div class="plot-stat-grid">
    <div class="plot-stat">
        <div class="plot-stat-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-th-large"></i></div>
        <div><div class="plot-stat-label">Total Plots</div><div class="plot-stat-val">{{ $stats['total'] }}</div></div>
    </div>
    <div class="plot-stat">
        <div class="plot-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check-circle"></i></div>
        <div><div class="plot-stat-label">Available</div><div class="plot-stat-val">{{ $stats['available'] }}</div></div>
    </div>
    <div class="plot-stat">
        <div class="plot-stat-icon" style="background:#fdf4ff;color:#7c3aed;"><i class="fas fa-handshake"></i></div>
        <div><div class="plot-stat-label">Sold</div><div class="plot-stat-val">{{ $stats['sold'] }}</div></div>
    </div>
    <div class="plot-stat">
        <div class="plot-stat-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fas fa-bookmark"></i></div>
        <div><div class="plot-stat-label">Booked</div><div class="plot-stat-val">{{ $stats['booked'] }}</div></div>
    </div>
    <div class="plot-stat">
        <div class="plot-stat-icon" style="background:#fef9c3;color:#854d0e;"><i class="fas fa-clock"></i></div>
        <div><div class="plot-stat-label">Reserved</div><div class="plot-stat-val">{{ $stats['reserved'] }}</div></div>
    </div>
    <div class="plot-stat">
        <div class="plot-stat-icon" style="background:#f0fdf4;color:#15803d;"><i class="fas fa-ruler-combined"></i></div>
        <div>
            <div class="plot-stat-label">Total Marla</div>
            <div class="plot-stat-val">{{ number_format($stats['total_marla'], 0) }}<span style="font-size:10px;font-weight:600;color:var(--muted-text);margin-left:2px;">M</span></div>
        </div>
    </div>
    <div class="plot-stat">
        <div class="plot-stat-icon" style="background:#eff6ff;color:#0369a1;"><i class="fas fa-map-marked-alt"></i></div>
        <div>
            <div class="plot-stat-label">Avail. Marla</div>
            <div class="plot-stat-val">{{ number_format($stats['remaining_marla'], 0) }}<span style="font-size:10px;font-weight:600;color:var(--muted-text);margin-left:2px;">M</span></div>
        </div>
    </div>
</div>

{{-- TABS --}}
<div class="page-tabs">
    <button class="page-tab active" onclick="switchTab('plots',this)">
        <i class="fas fa-map"></i> Plot Inventory
    </button>
    <button class="page-tab" onclick="switchTab('blocks',this)">
        <i class="fas fa-th"></i> Blocks
        <span style="background:#eff6ff;color:#1d4ed8;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;">{{ $blocks->count() }}</span>
    </button>
</div>

{{-- ═══ TAB: PLOTS ═══ --}}
<div id="tab-plots">
<div class="dt-wrapper">

    {{-- Custom toolbar --}}
    <div class="dt-toolbar">
        <div class="dt-toolbar-left">
            <div class="dt-search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="dtSearch" placeholder="Search plot no., block, city…">
            </div>
            <select class="dt-filter" id="fStatus">
                <option value="">All Status</option>
                <option value="available">Available</option>
                <option value="booked">Booked</option>
                <option value="sold">Sold</option>
                <option value="reserved">Reserved</option>
                <option value="on_hold">On Hold</option>
            </select>
            <select class="dt-filter" id="fBlock">
                <option value="">All Blocks</option>
                @foreach($blocks as $b)<option value="{{ $b->name }}">{{ $b->name }}</option>@endforeach
            </select>
            <select class="dt-filter" id="fUnit">
                <option value="">All Units</option>
                <option value="Marla">Marla</option>
                <option value="Kanal">Kanal</option>
                <option value="Sqft">Sqft</option>
            </select>
            <select class="dt-filter" id="fFee">
                <option value="">All Fees</option>
                <optgroup label="Registry">
                    <option value="reg-pending">Registry — Pending</option>
                    <option value="reg-paid">Registry — Paid</option>
                </optgroup>
                <optgroup label="Development">
                    <option value="dev-pending">Development — Pending</option>
                    <option value="dev-paid">Development — Paid</option>
                </optgroup>
                <optgroup label="Security">
                    <option value="sec-pending">Security — Pending</option>
                    <option value="sec-paid">Security — Paid</option>
                </optgroup>
            </select>
        </div>
        <button class="btn-soft" onclick="resetDtFilters()" style="font-size:12px;padding:7px 14px;">
            <i class="fas fa-undo me-1"></i> Reset
        </button>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;">
    {{-- no-auto-dt prevents the layout from auto-initializing this table --}}
    <table id="plotsTable" class="no-auto-dt" style="width:100%;">
        <thead>
            <tr>
                <th style="width:36px;">#</th>
                <th>Plot No.</th>
                <th>Block</th>
                <th style="width:60px;">Street</th>
                <th style="width:70px;">Size</th>
                <th>City / Society</th>
                <th>Category</th>
                <th style="width:80px;">Price Type</th>
                <th style="width:90px;">Fees</th>
                <th style="width:90px;">Status</th>
                <th style="width:80px;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($plots as $i => $plot)
        @php
            $pf = $plotFeeMap[$plot->id] ?? null;
            $feeTokens = '';
            if ($pf) {
                if ($pf['registry_required'])    $feeTokens .= ($pf['registry_ok']    ? 'reg-paid '    : 'reg-pending ');
                if ($pf['development_required']) $feeTokens .= ($pf['development_ok'] ? 'dev-paid '    : 'dev-pending ');
                if ($pf['security_required'])    $feeTokens .= ($pf['security_ok']    ? 'sec-paid '    : 'sec-pending ');
            }
        @endphp
        <tr data-fee="{{ trim($feeTokens) }}">
            <td style="color:var(--muted-text);font-size:11px;">{{ $loop->iteration }}</td>
            <td>
                <span style="font-weight:800;">#{{ $plot->plot_number }}</span>
                @if($plot->street_number)
                    <div style="font-size:10px;color:var(--muted-text);">{{ $plot->street_number }}</div>
                @endif
            </td>
            <td>
                <span style="font-size:11px;font-weight:700;background:var(--hover-bg);border:1px solid var(--border-color);padding:2px 9px;border-radius:6px;color:var(--sub-text);white-space:nowrap;">
                    {{ $plot->block ?? '—' }}
                </span>
            </td>
            <td style="font-size:11px;color:var(--sub-text);">
                {{ $plot->street_size ? $plot->street_size.' ft' : '—' }}
            </td>
            <td>
                <span style="font-weight:700;">{{ $plot->size }}</span>
                <span style="font-size:10px;color:var(--muted-text);"> {{ $plot->unit }}</span>
            </td>
            <td>
                <div style="font-weight:600;font-size:12px;">{{ $plot->city ?? '—' }}</div>
                <div style="font-size:10px;color:var(--muted-text);">{{ $plot->society }}{{ $plot->sector ? ' · '.$plot->sector : '' }}</div>
            </td>
            <td style="font-size:11px;color:var(--sub-text);">{{ $plot->category->name ?? '—' }}</td>
            <td>
                @php
                    $hasDisc   = $plot->discount_amount > 0;
                    $effListing = $hasDisc ? max(0, (float)$plot->base_price - (float)$plot->discount_amount) : (float)($plot->base_price ?? 0);
                    $discPctL  = ($hasDisc && $plot->base_price > 0) ? round($plot->discount_amount / $plot->base_price * 100, 0) : 0;
                @endphp

                @if($plot->base_price)
                @if($hasDisc)
                    <div style="font-size:10px;color:#94a3b8;text-decoration:line-through;line-height:1.2;">PKR {{ number_format($plot->base_price) }}</div>
                    <div style="font-size:13px;font-weight:800;color:#15803d;line-height:1.3;">PKR {{ number_format($effListing) }}</div>
                @else
                    <div style="font-size:12px;font-weight:700;color:var(--text-main);">PKR {{ number_format($plot->base_price) }}</div>
                @endif
                @endif

                @if($plot->price_type === 'cash')
                    <span style="font-size:9px;font-weight:700;background:#f0fdf4;color:#15803d;padding:1px 7px;border-radius:20px;display:inline-block;margin-top:2px;">Cash</span>
                @else
                    <span style="font-size:9px;font-weight:700;background:#eff6ff;color:#1d4ed8;padding:1px 7px;border-radius:20px;display:inline-block;margin-top:2px;">Instalment</span>
                @endif

                @if($hasDisc)
                <div style="margin-top:3px;">
                    <span style="font-size:9px;font-weight:800;background:#fef9c3;color:#92400e;padding:2px 7px;border-radius:20px;border:1px solid #fde68a;display:inline-flex;align-items:center;gap:3px;">
                        <i class="fas fa-tag" style="font-size:7px;"></i>
                        {{ $discPctL }}% off
                        @if($plot->discount_reason) · {{ $plot->discount_reason }} @endif
                    </span>
                </div>
                @endif
            </td>
            <td>
                @if($pf)
                    @php $anyFee = $pf['registry_required'] || $pf['development_required'] || $pf['security_required']; @endphp
                    @if($anyFee)
                        @if($pf['registry_required'])
                        <span style="display:inline-flex;align-items:center;gap:3px;border-radius:5px;padding:2px 6px;font-size:9px;font-weight:700;margin:1px;white-space:nowrap;
                            {{ $pf['registry_ok'] ? 'background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;' : 'background:#fef2f2;border:1px solid #fecaca;color:#dc2626;' }}">
                            <i class="fas fa-stamp" style="font-size:8px;"></i>
                            Reg {{ $pf['registry_ok'] ? '✓' : '!' }}
                        </span><br>
                        @endif
                        @if($pf['development_required'])
                        <span style="display:inline-flex;align-items:center;gap:3px;border-radius:5px;padding:2px 6px;font-size:9px;font-weight:700;margin:1px;white-space:nowrap;
                            {{ $pf['development_ok'] ? 'background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;' : 'background:#fef2f2;border:1px solid #fecaca;color:#dc2626;' }}">
                            <i class="fas fa-hard-hat" style="font-size:8px;"></i>
                            Dev {{ $pf['development_ok'] ? '✓' : '!' }}
                        </span><br>
                        @endif
                        @if($pf['security_required'])
                        <span style="display:inline-flex;align-items:center;gap:3px;border-radius:5px;padding:2px 6px;font-size:9px;font-weight:700;margin:1px;white-space:nowrap;
                            {{ $pf['security_ok'] ? 'background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;' : 'background:#fef2f2;border:1px solid #fecaca;color:#dc2626;' }}">
                            <i class="fas fa-shield-alt" style="font-size:8px;"></i>
                            Sec {{ $pf['security_ok'] ? '✓' : '!' }}
                        </span>
                        @endif
                    @else
                        <span style="font-size:11px;color:var(--muted-text);">No fees</span>
                    @endif
                @else
                    <span style="font-size:11px;color:var(--muted-text);">—</span>
                @endif
            </td>
            <td>
                <span class="bs bs-{{ $plot->status }}">
                    <span class="bs-dot"></span>
                    {{ ucfirst(str_replace('_',' ',$plot->status)) }}
                </span>
            </td>
            <td>
                <div style="display:flex;gap:5px;">
                    <a href="{{ route('plots.show', Hashids::encode($plot->id)) }}" class="act-btn" title="View" target="_blank">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('plots.edit', $plot->id) }}" class="act-btn edit" title="Edit">
                        <i class="fas fa-pen"></i>
                    </a>
                    <form action="{{ route('plots.destroy', $plot->id) }}" method="POST"
                          onsubmit="return confirm('Delete plot #{{ $plot->plot_number }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="act-btn del" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        @endforelse
        </tbody>
    </table>
    </div>

</div>
</div>

{{-- ═══ TAB: BLOCKS ═══ --}}
<div id="tab-blocks" style="display:none;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <span style="font-size:11px;font-weight:700;color:var(--muted-text);">Types:</span>
            <span class="btype btype-residential">Residential</span>
            <span class="btype btype-overseas">Overseas</span>
            <span class="btype btype-commercial">Commercial</span>
            <span class="btype btype-civic">Civic Center</span>
            <span class="btype btype-extension">Extension</span>
        </div>
        <button class="btn-navy" style="padding:8px 16px;font-size:12px;" data-bs-toggle="modal" data-bs-target="#addBlockModal">
            <i class="fas fa-plus"></i> Add Block
        </button>
    </div>

    <div class="blocks-grid">
    @forelse($blocks as $block)
    <div class="block-card">
        <div>
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px;">
                <span style="font-size:14px;font-weight:800;color:var(--text-main);">{{ $block->name }}</span>
                <span class="btype btype-{{ $block->type }}">{{ ucfirst($block->type) }}</span>
            </div>
            @if($block->description)
            <div style="font-size:11px;color:var(--muted-text);line-height:1.5;">{{ $block->description }}</div>
            @endif
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding-top:8px;border-top:1px solid var(--border-color);">
            <span style="font-size:11px;color:var(--muted-text);">
                <i class="fas fa-map-marker-alt me-1"></i>
                {{ \App\Models\Plot::where('block', $block->name)->count() }} plots
            </span>
            <div style="display:flex;gap:5px;">
               <a href="{{ route('blocks.edit', $block->id) }}" class="act-btn edit" title="Edit">
    <i class="fas fa-pen"></i>
</a>
                <form action="{{ route('blocks.destroy', $block->id) }}" method="POST"
                      onsubmit="return confirm('Remove {{ $block->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="act-btn del"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="no-data" style="grid-column:1/-1;">
        <i class="fas fa-th"></i>
        <p style="font-weight:700;">No blocks yet — run the seeder or click Add Block</p>
    </div>
    @endforelse
    </div>
</div>

</div><!-- /ldg-wrap -->






@endsection

@push('scripts')
<script>
$(document).ready(function () {

    /* ══════════════════════════════════════════════════════════
       1. Destroy any auto-initialized DataTable first
    ══════════════════════════════════════════════════════════ */
    if ($.fn.DataTable.isDataTable('#plotsTable')) {
        $('#plotsTable').DataTable().destroy();
    }

    /* ══════════════════════════════════════════════════════════
       2. Initialize DataTable cleanly
    ══════════════════════════════════════════════════════════ */
    var plotsTable = $('#plotsTable').DataTable({
        destroy:    true,
        paging:     true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        ordering:   true,
        info:       true,
        searching:  true,
        autoWidth:  false,
        columnDefs: [
            { targets: 0,  orderable: false, searchable: false },  // #
            { targets: 8,  orderable: false, searchable: false },  // Fees
            { targets: 10, orderable: false, searchable: false },  // Actions
        ],
        language: {
            info:         'Showing _START_–_END_ of _TOTAL_ plots',
            infoEmpty:    'No plots found',
            infoFiltered: '(filtered from _MAX_ total)',
            emptyTable:   'No plots available',
            zeroRecords:  'No plots match your search',
            paginate: {
                previous: '&lsaquo;',
                next:     '&rsaquo;'
            }
        },
        dom: 'tip',  // table + info + pagination only (no built-in search/length)
        drawCallback: function () {
            /* Re-style DT pagination to match our design */
            $('.dataTables_paginate .paginate_button').css({
                'border-radius': '8px',
                'font-size': '12px',
                'font-weight': '700',
                'padding': '5px 12px',
                'margin': '0 2px',
            });
        }
    });

    /* ══════════════════════════════════════════════════════════
       3. Wire up our custom search box
    ══════════════════════════════════════════════════════════ */
    $('#dtSearch').on('input', function () {
        plotsTable.search($(this).val()).draw();
    });

    /* ══════════════════════════════════════════════════════════
       3b. Custom fee filter — matches data-fee attribute on <tr>
    ══════════════════════════════════════════════════════════ */
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'plotsTable') return true;
        var token = $('#fFee').val();
        if (!token) return true;
        var feeData = $(plotsTable.row(dataIndex).node()).data('fee') || '';
        return feeData.indexOf(token) !== -1;
    });

    $('#fFee').on('change', function () {
        plotsTable.draw();
    });

    /* ══════════════════════════════════════════════════════════
       4. Wire up column filter dropdowns
          col 9 = Status, col 2 = Block, col 4 = Size (unit)
    ══════════════════════════════════════════════════════════ */
    $('#fStatus').on('change', function () {
        plotsTable.column(9).search($(this).val()).draw();
    });

    $('#fBlock').on('change', function () {
        plotsTable.column(2).search($(this).val()).draw();
    });

    $('#fUnit').on('change', function () {
        plotsTable.column(4).search($(this).val()).draw();
    });

    /* ══════════════════════════════════════════════════════════
       5. Reset all filters
    ══════════════════════════════════════════════════════════ */
    window.resetDtFilters = function () {
        $('#dtSearch').val('');
        $('#fStatus').val('');
        $('#fBlock').val('');
        $('#fUnit').val('');
        $('#fFee').val('');
        plotsTable.search('').columns().search('').draw();
    };

    /* ══════════════════════════════════════════════════════════
       6. Tab switching — adjust columns when plots tab shown
    ══════════════════════════════════════════════════════════ */
    window.switchTab = function (name, btn) {
        $('#tab-plots').toggle(name === 'plots');
        $('#tab-blocks').toggle(name === 'blocks');
        $('.page-tab').removeClass('active');
        $(btn).addClass('active');
        if (name === 'plots') {
            // Must adjust after tab becomes visible
            setTimeout(function () {
                plotsTable.columns.adjust().draw(false);
            }, 50);
        }
    };

    /* ══════════════════════════════════════════════════════════
       7. Add Block modal reset on open
    ══════════════════════════════════════════════════════════ */
    $('#addBlockModal').on('show.bs.modal', function (e) {
        if (e.relatedTarget) {
            // Triggered by "Add Block" button, not editBlock()
            $('#blockName').val('');
            $('#blockType').val('residential');
            $('#blockDesc').val('');
            $('#blockMethod').val('POST');
            $('#blockForm').attr('action', '{{ route("blocks.store") }}');
            $('#blockModalTitle').html('<i class="fas fa-th me-2"></i>Add Block');
        }
    });

}); // end ready

/* ══════════════════════════════════════════════════════════
   8. Edit block — called from blade onclick, outside ready()
══════════════════════════════════════════════════════════ */
function editBlock(id, name, type, desc) {
    $('#blockName').val(name);
    $('#blockType').val(type);
    $('#blockDesc').val(desc || '');
    $('#blockMethod').val('PUT');
    $('#blockForm').attr('action', '/blocks/' + id);
    $('#blockModalTitle').html('<i class="fas fa-pen me-2"></i>Edit Block');
    new bootstrap.Modal(document.getElementById('addBlockModal')).show();
}
</script>
@endpush
