<aside id="mainSb">

    {{-- Brand --}}
    <div class="sb-brand">
        <button id="sbClose" aria-label="Close menu"><i class="bi bi-x-lg"></i></button>

        <div class="sb-logo-wrap">
            {{-- Swap icon for your real logo: --}}
            {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo"> --}}
            <i class="fa-solid fa-mountain-sun"></i>
        </div>

        <div class="sb-brand-text">
            <span class="sb-title">Zamar Valley</span>
            <span class="sb-subtitle">Property ERP</span>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="sb-nav">
        <ul style="list-style:none;padding:0;margin:0">

            {{-- Dashboard --}}
            <li class="sb-item" style="margin-top:4px">
                <a href="{{ route('index.dashboard') }}"
                   class="sb-link {{ request()->routeIs('index.dashboard') ? 'active' : '' }}">
                    <span class="sb-icon ic-indigo"><i class="bi bi-grid-fill"></i></span>
                    <span class="sb-lbl">Dashboard</span>
                    <span class="sb-tip">Dashboard</span>
                </a>
            </li>

            {{-- PROPERTY MANAGEMENT --}}
            <li class="sb-section">Property Management</li>

            {{-- Plot Inventory --}}
            <li class="sb-item">
                <button type="button"
                        class="sb-link {{ request()->routeIs('index.plots','plots.create','plots.categories','plots.blocks') ? 'active' : '' }}"
                        data-bs-toggle="collapse" data-bs-target="#colPlots"
                        aria-expanded="{{ request()->routeIs('index.plots','plots.create','plots.categories','plots.blocks') ? 'true' : 'false' }}">
                    <span class="sb-icon ic-blue"><i class="fa-solid fa-map-location-dot"></i></span>
                    <span class="sb-lbl">Plot Inventory</span>
                    <span class="sb-arr"><i class="bi bi-chevron-right"></i></span>
                    <span class="sb-tip">Plot Inventory</span>
                </button>
                <ul class="sb-sub collapse {{ request()->routeIs('index.plots','plots.create','plots.categories','plots.blocks') ? 'show' : '' }}"
                    id="colPlots">
                    <li>
                        <a href="{{ route('index.plots') }}"
                           class="sb-sub-link {{ request()->routeIs('index.plots') ? 'active' : '' }}">
                            <span class="sb-sub-icon ic-blue"><i class="bi bi-list-ul"></i></span>All Plots
                        </a>
                    </li>
                    <li>
                        <a href=""
                           class="sb-sub-link {{ request()->routeIs('plots.create') ? 'active' : '' }}">
                            <span class="sb-sub-icon ic-green"><i class="bi bi-plus-circle-fill"></i></span>Add Plot
                        </a>
                    </li>
                    <li>
                        <a href=""
                           class="sb-sub-link {{ request()->routeIs('plots.categories') ? 'active' : '' }}">
                            <span class="sb-sub-icon ic-amber"><i class="bi bi-tag-fill"></i></span>Categories
                        </a>
                    </li>
                    <li>
                        <a href=""
                           class="sb-sub-link {{ request()->routeIs('plots.blocks') ? 'active' : '' }}">
                            <span class="sb-sub-icon ic-orange"><i class="bi bi-geo-alt-fill"></i></span>Blocks / Sectors
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Digital Bookings --}}
            <li class="sb-item">
                <button type="button"
                        class="sb-link {{ request()->routeIs('index.booking','booking.create','booking.reports') ? 'active' : '' }}"
                        data-bs-toggle="collapse" data-bs-target="#colBook"
                        aria-expanded="{{ request()->routeIs('index.booking','booking.create','booking.reports') ? 'true' : 'false' }}">
                    <span class="sb-icon ic-teal"><i class="bi bi-clipboard2-check-fill"></i></span>
                    <span class="sb-lbl">Digital Bookings</span>
                    <span class="sb-arr"><i class="bi bi-chevron-right"></i></span>
                    <span class="sb-tip">Digital Bookings</span>
                </button>
                <ul class="sb-sub collapse {{ request()->routeIs('index.booking','booking.create','booking.reports') ? 'show' : '' }}"
                    id="colBook">
                    <li>
                        <a href="{{ route('index.booking') }}"
                           class="sb-sub-link {{ request()->routeIs('index.booking') ? 'active' : '' }}">
                            <span class="sb-sub-icon ic-teal"><i class="bi bi-list-check"></i></span>All Bookings
                        </a>
                    </li>
                    <li>
                        <a href=""
                           class="sb-sub-link {{ request()->routeIs('booking.create') ? 'active' : '' }}">
                            <span class="sb-sub-icon ic-green"><i class="bi bi-plus-circle-fill"></i></span>New Booking
                        </a>
                    </li>
                    <li>
                        <a href=""
                           class="sb-sub-link {{ request()->routeIs('booking.reports') ? 'active' : '' }}">
                            <span class="sb-sub-icon ic-indigo"><i class="bi bi-bar-chart-fill"></i></span>Booking Reports
                        </a>
                    </li>
                </ul>
            </li>

            @hasanyrole('admin|accountant')
            {{-- FINANCE --}}
            <li class="sb-section green">Finance</li>

            <li class="sb-item">
                <button type="button"
                        class="sb-link {{ request()->routeIs('index.account','office_expenses.view') ? 'active' : '' }}"
                        data-bs-toggle="collapse" data-bs-target="#colFin"
                        aria-expanded="{{ request()->routeIs('index.account','office_expenses.view') ? 'true' : 'false' }}">
                    <span class="sb-icon ic-green"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                    <span class="sb-lbl">Finance</span>
                    <span class="sb-arr"><i class="bi bi-chevron-right"></i></span>
                    <span class="sb-tip">Finance</span>
                </button>
                <ul class="sb-sub collapse {{ request()->routeIs('index.account','office_expenses.view') ? 'show' : '' }}"
                    id="colFin">
                    <li>
                        <a href="{{ route('index.account') }}"
                           class="sb-sub-link {{ request()->routeIs('index.account') ? 'active' : '' }}">
                            <span class="sb-sub-icon ic-lime"><i class="fa-solid fa-receipt"></i></span>Accounts & Recovery
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('office_expenses.view') }}"
                           class="sb-sub-link {{ request()->routeIs('office_expenses.view') ? 'active' : '' }}">
                            <span class="sb-sub-icon ic-amber"><i class="fa-solid fa-wallet"></i></span>Office Expenses
                        </a>
                    </li>
                </ul>
            </li>
            @endhasanyrole

            {{-- CRM --}}
            <li class="sb-section purple">CRM</li>

            <li class="sb-item">
                <a href="#" class="sb-link">
                    <span class="sb-icon ic-purple"><i class="bi bi-people-fill"></i></span>
                    <span class="sb-lbl">Client List</span>
                    <span class="sb-tip">Client List</span>
                </a>
            </li>

            @can('settings')
            {{-- SYSTEM --}}
            <li class="sb-section orange">System</li>

            <li class="sb-item">
                <a href="{{ route('setting.view') }}" class="sb-link">
                    <span class="sb-icon ic-orange"><i class="bi bi-gear-fill"></i></span>
                    <span class="sb-lbl">System Settings</span>
                    <span class="sb-tip">System Settings</span>
                </a>
            </li>
            @endcan

        </ul>
    </nav>

    {{-- Footer user card --}}
    <div class="sb-foot">
        <div class="sb-user">
            @if(auth()->user()->profile_image && file_exists(public_path('storage/user_image/'.auth()->user()->profile_image)))
                <img class="sb-ava" src="{{ asset('storage/user_image/'.auth()->user()->profile_image) }}" alt="">
            @else
                <div class="sb-ava">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
            @endif
            <div class="sb-uinfo">
                <span class="sb-uname">{{ auth()->user()->name ?? 'User' }}</span>
                <span class="sb-urole">{{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'Guest') }}</span>
            </div>
            <span class="sb-udots"><i class="bi bi-three-dots"></i></span>
        </div>
    </div>

</aside>
