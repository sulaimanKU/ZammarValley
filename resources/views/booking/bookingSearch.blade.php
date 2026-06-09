@extends('layouts.index')
@push('styles')
<style>
/* ── Search section ── */
.search-section{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:24px;margin-bottom:20px;}
.search-section-title{font-size:11px;font-weight:800;color:var(--muted-text);text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;display:flex;align-items:center;gap:6px;}
.search-row{display:flex;gap:10px;margin-bottom:16px;}
.search-input-wrap{flex:1;position:relative;}
.search-input-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted-text);font-size:14px;pointer-events:none;}
.search-input-wrap input{width:100%;border:1.5px solid var(--border-color);border-radius:10px;padding:12px 14px 12px 42px;font-size:13px;background:var(--input-bg);color:var(--text-main);outline:none;font-family:'Poppins',sans-serif;transition:border-color .15s;}
.search-input-wrap input:focus{border-color:#1e3a8a;}
.search-btn{padding:0 28px;background:#1e3a8a;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Poppins',sans-serif;display:flex;align-items:center;gap:8px;white-space:nowrap;transition:background .15s;}
.search-btn:hover{background:#1e40af;}
/* ── Filter chips ── */
.filter-label{font-size:10px;font-weight:800;color:var(--muted-text);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;}
.filter-chips{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
.f-sel{border:1.5px solid var(--border-color);border-radius:20px;padding:6px 14px;font-size:12px;font-weight:600;background:var(--card-bg);color:var(--sub-text);outline:none;cursor:pointer;font-family:'Poppins',sans-serif;transition:all .15s;appearance:none;-webkit-appearance:none;padding-right:28px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;}
.f-sel:focus,.f-sel:hover{border-color:#1e3a8a;color:var(--text-main);}
.f-sel.active{border-color:#1e3a8a;background:#eff6ff;color:#1e3a8a;}
.reset-btn{border:1.5px solid var(--border-color);border-radius:20px;padding:6px 14px;font-size:12px;font-weight:600;background:var(--card-bg);color:var(--sub-text);cursor:pointer;display:flex;align-items:center;gap:5px;transition:all .15s;}
.reset-btn:hover{border-color:#dc2626;color:#dc2626;}
.count-badge{margin-left:auto;font-size:12px;font-weight:700;color:var(--muted-text);align-self:center;}
.plots-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;}
.pc{border:1.5px solid var(--border-color);border-radius:12px;padding:16px;background:var(--card-bg);transition:all .15s;cursor:pointer;text-decoration:none;display:block;color:inherit;}
.pc:hover{border-color:#1e3a8a;box-shadow:0 4px 18px rgba(30,58,138,.1);transform:translateY(-1px);}
.pc-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px;}
.pc-num{font-size:20px;font-weight:800;color:var(--text-main);}
.pc-size{font-size:12px;font-weight:600;color:var(--muted-text);}
.pc-location{font-size:11px;color:var(--muted-text);margin-bottom:10px;}
.pc-price{font-size:14px;font-weight:800;color:#1e3a8a;margin-bottom:10px;}
.pc-tags{display:flex;gap:6px;flex-wrap:wrap;}
.pct{padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;}
.pct-cash{background:#f0fdf4;color:#15803d;}
.pct-inst{background:#eff6ff;color:#1d4ed8;}
.pct-road{background:var(--hover-bg);color:var(--sub-text);}
.pct-feat{background:#fef9c3;color:#854d0e;}
.no-results{text-align:center;padding:60px 20px;color:var(--muted-text);grid-column:1/-1;}
.no-results i{font-size:3rem;opacity:.18;display:block;margin-bottom:12px;}
</style>
@endpush

@section('content')
<div class="ldg-wrap">

    <div class="rpt-header no-print">
        <div>
            <p class="rpt-header-title">New Booking — Select Plot</p>
            <p class="rpt-header-sub">
                {{ $config['name'] }} · Search and select an available plot to proceed
            </p>
        </div>
        <div class="rpt-header-actions">
            <a href="{{ route('index.booking') }}" class="btn-soft-header">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Search section --}}
    <div class="search-section">
        <div class="search-section-title">
            <i class="fas fa-search"></i> Find an Available Plot
        </div>

        {{-- Search input + button --}}
        <div class="search-row">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="plotSearch"
                    placeholder="Type plot number, block name, street, or size e.g. 'Block A' or '2001' or '5 Marla'">
            </div>
            <button type="button" class="search-btn" onclick="filterPlots()">
                <i class="fas fa-search"></i> Search Plots
            </button>
        </div>

        {{-- Filter chips --}}
        <div class="filter-label">Filter by</div>
        <div class="filter-chips">
            <select class="f-sel" id="fBlock" onchange="markActive(this); filterPlots()">
                <option value="">🏘 All Blocks</option>
                @foreach($blocks as $b)
                    <option value="{{ strtolower($b->name) }}">{{ $b->name }}</option>
                @endforeach
            </select>
            <select class="f-sel" id="fSize" onchange="markActive(this); filterPlots()">
                <option value="">📐 All Sizes</option>
                <option value="marla">Marla</option>
                <option value="kanal">Kanal</option>
                <option value="sqft">Sqft</option>
            </select>
            <select class="f-sel" id="fStreet" onchange="markActive(this); filterPlots()">
                <option value="">🛣 All Streets</option>
                @for($s=1;$s<=20;$s++)
                    <option value="street {{ $s }}">Street {{ $s }}</option>
                @endfor
            </select>
            <select class="f-sel" id="fType" onchange="markActive(this); filterPlots()">
                <option value="">💳 Cash & Instalment</option>
                <option value="cash">Cash Only</option>
                <option value="installment">Instalment Only</option>
            </select>
            <button class="reset-btn" onclick="resetFilters()">
                <i class="fas fa-times"></i> Clear Filters
            </button>
            <span class="count-badge" id="countBadge"></span>
        </div>
    </div>

    {{-- Plot Grid --}}
    <div class="plots-grid" id="plotsGrid">

        @forelse($availablePlots as $plot)
        <a href="{{ route('booking.create', $plot->id) }}"
            class="pc"
            data-search="{{ strtolower($plot->plot_number.' '.$plot->block.' '.$plot->street_number.' '.$plot->size.' '.$plot->unit.' '.$plot->sector.' '.$plot->property_features) }}"
            data-block="{{ strtolower($plot->block) }}"
            data-size="{{ strtolower($plot->unit) }}"
            data-street="{{ strtolower($plot->street_number) }}"
            data-type="{{ $plot->price_type }}">

            <div class="pc-top">
                <div>
                    <div class="pc-num">
                        #{{ $plot->plot_number }}
                        <span class="pc-size">{{ $plot->size }} {{ $plot->unit }}</span>
                    </div>
                </div>
                <span style="background:#dcfce7;color:#15803d;font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;">
                    Available
                </span>
            </div>

            <div class="pc-location">
                <i class="fas fa-map-marker-alt me-1"></i>
                {{ $plot->block }}
                @if($plot->street_number) · {{ $plot->street_number }} @endif
                @if($plot->street_size) · {{ $plot->street_size }}ft road @endif
            </div>

            @if($plot->base_price)
            @if($plot->discount_amount > 0)
            @php $effSearch = max(0, (float)$plot->base_price - (float)$plot->discount_amount); @endphp
            <div style="margin-bottom:6px;">
                <span style="font-size:11px;color:#94a3b8;text-decoration:line-through;">PKR {{ number_format($plot->base_price) }}</span>
                <span style="font-size:15px;font-weight:800;color:#15803d;margin-left:6px;">PKR {{ number_format($effSearch) }}</span>
                <span style="font-size:9px;font-weight:800;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;padding:1px 6px;border-radius:20px;margin-left:4px;">
                    -{{ round($plot->discount_amount / $plot->base_price * 100, 0) }}%
                </span>
                @if($plot->discount_reason)
                <div style="font-size:10px;color:#15803d;font-weight:600;margin-top:1px;"><i class="fas fa-tag me-1"></i>{{ $plot->discount_reason }}</div>
                @endif
            </div>
            @else
            <div class="pc-price">PKR {{ number_format($plot->base_price) }}</div>
            @endif
            @endif

            <div class="pc-tags">
                @if($plot->price_type === 'cash')
                    <span class="pct pct-cash"><i class="fas fa-money-bill me-1"></i>Cash</span>
                @else
                    <span class="pct pct-inst"><i class="fas fa-calendar me-1"></i>Instalment</span>
                    @if($plot->total_installments)
                        <span class="pct pct-road">{{ $plot->total_installments }} months</span>
                    @endif
                    @if($plot->quarterly_installments)
                        <span class="pct pct-road">{{ $plot->quarterly_installments }} quarterly</span>
                    @endif
                @endif
                @if($plot->property_features)
                    <span class="pct pct-feat">{{ $plot->property_features }}</span>
                @endif
            </div>

        </a>
        @empty
        <div class="no-results">
            <i class="fas fa-map-marked-alt"></i>
            <p style="font-weight:700;">No available plots</p>
            <p style="font-size:12px;">All plots are currently booked or sold.</p>
        </div>
        @endforelse

        {{-- No match message (shown by JS when search returns 0) --}}
        <div id="noMatch" class="no-results" style="display:none;grid-column:1/-1;">
            <i class="fas fa-map-marked-alt"></i>
            <p style="font-weight:700;">No plots match your search</p>
            <p style="font-size:12px;">Try different keywords or reset the filters.</p>
        </div>

    </div>

</div>
@endsection

<script>
/* Live search — fires as user types */
document.getElementById('plotSearch').addEventListener('input', filterPlots);

/* Enter key also triggers search */
document.getElementById('plotSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') filterPlots();
});

function filterPlots() {
    var q      = document.getElementById('plotSearch').value.toLowerCase().trim();
    var block  = document.getElementById('fBlock').value.toLowerCase();
    var size   = document.getElementById('fSize').value.toLowerCase();
    var street = document.getElementById('fStreet').value.toLowerCase();
    var type   = document.getElementById('fType').value.toLowerCase();
    var cards  = document.querySelectorAll('#plotsGrid .pc[data-search]');
    var shown  = 0;

    cards.forEach(function(c) {
        var ok = (!q      || c.dataset.search.includes(q))
              && (!block  || c.dataset.block  === block)
              && (!size   || c.dataset.size   === size)
              && (!street || c.dataset.street === street)
              && (!type   || c.dataset.type   === type);
        c.style.display = ok ? '' : 'none';
        if (ok) shown++;
    });

    /* show/hide no-match message */
    document.getElementById('noMatch').style.display = shown === 0 ? 'block' : 'none';

    /* update count badge */
    document.getElementById('countBadge').textContent =
        shown < cards.length
            ? shown + ' of ' + cards.length + ' plots'
            : cards.length + ' available plots';
}

function markActive(el) {
    el.classList.toggle('active', el.value !== '');
}

function resetFilters() {
    document.getElementById('plotSearch').value = '';
    ['fBlock','fSize','fStreet','fType'].forEach(function(id) {
        var el = document.getElementById(id);
        el.value = '';
        el.classList.remove('active');
    });
    filterPlots();
}

/* Show count on load */
filterPlots();
</script>
