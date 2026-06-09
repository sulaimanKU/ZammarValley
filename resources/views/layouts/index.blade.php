<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title', 'Zamar Valley Dashboard')</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo3.png') }}">
    <link rel="stylesheet" href="{{ asset('styles/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('styles/external.css') }}" />

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    @stack('styles')
</head>

<body>

    <div class="sb-overlay" id="sbOverlay"></div>

    {{-- ════════════ SIDEBAR ════════════ --}}
    <aside class="sb" id="mainSb">

      <div class="sb-brand">
    <button id="sbClose" aria-label="Close"><i class="bi bi-x-lg"></i></button>
    <div class="sb-logo-wrap">
        @php $societyLogo = \App\Models\SystemConfig::get('society_logo'); @endphp
        <img src="{{ $societyLogo ? asset('storage/' . $societyLogo) : asset('images/logo3.png') }}"
             alt="Zamar Valley">
    </div>
    <div class="sb-brand-text">
        <span class="sb-title">{{ \App\Models\SystemConfig::get('society_name', 'Zamar Valley') }}</span>
        <span class="sb-subtitle">Property ERP</span>

    </div>
</div>

       <nav class="sb-nav">
   <ul style="list-style:none;padding:0;margin:0;">

    {{-- ══ DASHBOARD ══ --}}
    <li class="sb-item" style="margin-top:4px;">
        <a href="{{ route('index.dashboard') }}"
           class="sb-link {{ request()->routeIs('index.dashboard') ? 'active' : '' }}">
            <span class="sb-icon ic-indigo"><i class="fa-solid fa-house"></i></span>
            <span class="sb-lbl">Dashboard</span>
            <span class="sb-tip">Dashboard</span>
        </a>
    </li>

    {{-- ══════════════════════════════════════════
         PROPERTY MANAGEMENT
    ══════════════════════════════════════════ --}}
    @canany(['inventory_view','plot_create','plot_edit','plot_delete','plot_pricing_manage','plot_category_manage','block_manage'])
    <li class="sb-section">Property Management</li>

    <li class="sb-item">
        <button class="sb-link {{ request()->routeIs('index.plots','plot.add','categories.view','blocks.index','plots.edit','plot.pricing.view') ? 'active' : '' }}"
                data-bs-toggle="collapse" data-bs-target="#colPlots"
                aria-expanded="{{ request()->routeIs('index.plots','plot.add','categories.view','blocks.index','plots.edit','plot.pricing.view') ? 'true' : 'false' }}">
            <span class="sb-icon ic-blue"><i class="fa-solid fa-map-location-dot"></i></span>
            <span class="sb-lbl">Plot Inventory</span>
            <span class="sb-arr"><i class="bi bi-chevron-right"></i></span>
            <span class="sb-tip">Plot Inventory</span>
        </button>
        <ul class="sb-sub collapse {{ request()->routeIs('index.plots','plot.add','categories.view','blocks.index','plots.edit','plot.pricing.view') ? 'show' : '' }}"
            id="colPlots">

            @can('inventory_view')
            <li><a href="{{ route('index.plots') }}"
                   class="sb-sub-link {{ request()->routeIs('index.plots') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-blue"><i class="bi bi-list-ul"></i></span>All Plots
            </a></li>
            @endcan

            @can('plot_create')
            <li><a href="{{ route('plot.add') }}"
                   class="sb-sub-link {{ request()->routeIs('plot.add') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-green"><i class="bi bi-plus-circle-fill"></i></span>Add Plot
            </a></li>
            @endcan

            @can('block_manage')
            <li><a href="{{ route('blocks.index') }}"
                   class="sb-sub-link {{ request()->routeIs('blocks.index','blocks.edit') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-blue"><i class="fa-solid fa-table-cells-large"></i></span>Manage Blocks
            </a></li>
            @endcan

            @can('plot_category_manage')
            <li><a href="{{ route('categories.view') }}"
                   class="sb-sub-link {{ request()->routeIs('categories.view','categories.edit','categories.create') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-amber"><i class="bi bi-tag-fill"></i></span>Categories
            </a></li>
            @endcan



        </ul>
    </li>
    @endcanany

    {{-- ══════════════════════════════════════════
         DIGITAL BOOKINGS
    ══════════════════════════════════════════ --}}
    @canany(['booking_view_all','booking_create','booking_reports','booking_docs_view'])
    <li class="sb-item">
        <button class="sb-link {{ request()->routeIs('index.booking','booking.search','booking.reports','booking.create','booking.application.form','booking.agreement') ? 'active' : '' }}"
                data-bs-toggle="collapse" data-bs-target="#colBook"
                aria-expanded="{{ request()->routeIs('index.booking','booking.search','booking.reports','booking.create','booking.application.form','booking.agreement') ? 'true' : 'false' }}">
            <span class="sb-icon ic-teal"><i class="bi bi-clipboard2-check-fill"></i></span>
            <span class="sb-lbl">Digital Bookings</span>
            <span class="sb-arr"><i class="bi bi-chevron-right"></i></span>
            <span class="sb-tip">Digital Bookings</span>
        </button>
        <ul class="sb-sub collapse {{ request()->routeIs('index.booking','booking.search','booking.reports','booking.create') ? 'show' : '' }}"
            id="colBook">

            @can('booking_view_all')
            <li><a href="{{ route('index.booking') }}"
                   class="sb-sub-link {{ request()->routeIs('index.booking','booking.detail.view') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-teal"><i class="bi bi-list-check"></i></span>All Bookings
            </a></li>
            @endcan

            @can('booking_create')
            <li><a href="{{ route('booking.search') }}"
                   class="sb-sub-link {{ request()->routeIs('booking.search','booking.create') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-green"><i class="bi bi-plus-circle-fill"></i></span>New Booking
            </a></li>
            @endcan

            @can('booking_reports')
            <li><a href="{{ route('booking.reports') }}"
                   class="sb-sub-link {{ request()->routeIs('booking.reports') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-indigo"><i class="bi bi-bar-chart-fill"></i></span>Booking Reports
            </a></li>
            @endcan

        </ul>
    </li>
    @endcanany

    {{-- ══════════════════════════════════════════
         FINANCE
    ══════════════════════════════════════════ --}}
    @canany(['recovery_dashboard_view','ledger_view','expense_view','finance_reports_view','fee_management_view'])
    <li class="sb-section green">Finance</li>

    <li class="sb-item">
        <button class="sb-link {{ request()->routeIs('index.account','ledger.view','office_expenses.view','fee.management','fee.history','fee.receipt','finance.report','expenses.create') ? 'active' : '' }}"
                data-bs-toggle="collapse" data-bs-target="#colFin"
                aria-expanded="{{ request()->routeIs('index.account','ledger.view','office_expenses.view','fee.management','finance.report','expenses.create') ? 'true' : 'false' }}">
            <span class="sb-icon ic-green"><i class="fa-solid fa-file-invoice-dollar"></i></span>
            <span class="sb-lbl">Finance</span>
            <span class="sb-arr"><i class="bi bi-chevron-right"></i></span>
            <span class="sb-tip">Finance</span>
        </button>
        <ul class="sb-sub collapse {{ request()->routeIs('index.account','ledger.view','office_expenses.view','fee.management','fee.history','finance.report','expenses.create') ? 'show' : '' }}"
            id="colFin">

            @can('recovery_dashboard_view')
            <li><a href="{{ route('index.account') }}"
                   class="sb-sub-link {{ request()->routeIs('index.account','ledger.view') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-lime"><i class="fa-solid fa-receipt"></i></span>Accounts & Recovery
            </a></li>
            @endcan

            {{-- #44 fee_management_view --}}
            @can('fee_management_view')
            <li><a href="{{ route('fee.management') }}"
                   class="sb-sub-link {{ request()->routeIs('fee.management','fee.history','fee.receipt') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-indigo"><i class="fa-solid fa-hand-holding-dollar"></i></span>Fee Management
            </a></li>
            @endcan

            @can('expense_view')
            <li><a href="{{ route('office_expenses.view') }}"
                   class="sb-sub-link {{ request()->routeIs('office_expenses.view','expense.detail.view','expense.edit.view','expenses.create') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-amber"><i class="fa-solid fa-wallet"></i></span>Office Expenses
            </a></li>
            @endcan

            @can('finance_reports_view')
            <li><a href="{{ route('finance.report') }}"
                   class="sb-sub-link {{ request()->routeIs('finance.report','reports.daily_cash','reports.monthly_summary') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-amber"><i class="fa-solid fa-chart-line"></i></span>Finance Report
            </a></li>
            @endcan

        </ul>
    </li>
    @endcanany

    {{-- ══════════════════════════════════════════
         TRANSFER
    ══════════════════════════════════════════ --}}
    @canany(['transfer_history_view','transfer_create','transfer_approve'])
    <li class="sb-section green">Transfer</li>

    <li class="sb-item">
        <button class="sb-link {{ request()->routeIs('index.transfer','transfers.search','transfers.create','transfers.edit','transfers.pay-fee') ? 'active' : '' }}"
                data-bs-toggle="collapse" data-bs-target="#colTra"
                aria-expanded="{{ request()->routeIs('index.transfer','transfers.search','transfers.create','transfers.edit') ? 'true' : 'false' }}">
            <span class="sb-icon ic-green"><i class="fa-solid fa-arrow-up-from-bracket"></i></span>
            <span class="sb-lbl">Transfer</span>
            <span class="sb-arr"><i class="bi bi-chevron-right"></i></span>
            <span class="sb-tip">Transfer</span>
        </button>
        <ul class="sb-sub collapse {{ request()->routeIs('index.transfer','transfers.search','transfers.create','transfers.edit','transfers.pay-fee') ? 'show' : '' }}"
            id="colTra">

            @can('transfer_history_view')
            <li><a href="{{ route('index.transfer') }}"
                   class="sb-sub-link {{ request()->routeIs('index.transfer','transfers.edit','transfers.pay-fee','transfers.fee-receipt') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-lime"><i class="fa-solid fa-list"></i></span>All Transfers
            </a></li>
            @endcan

            @can('transfer_create')
            <li><a href="{{ route('transfers.search') }}"
                   class="sb-sub-link {{ request()->routeIs('transfers.search','transfers.create') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-amber"><i class="fa-solid fa-circle-plus"></i></span>New Transfer
            </a></li>
            @endcan

        </ul>
    </li>
    @endcanany

    {{-- ══════════════════════════════════════════
         USER MANAGEMENT
    ══════════════════════════════════════════ --}}
    @canany(['user_view','user_create','user_edit','user_delete'])
    <li class="sb-section green">User Management</li>

    <li class="sb-item">
        <button class="sb-link {{ request()->routeIs('index.user','add.user','users.edit') ? 'active' : '' }}"
                data-bs-toggle="collapse" data-bs-target="#colUser"
                aria-expanded="{{ request()->routeIs('index.user','add.user','users.edit') ? 'true' : 'false' }}">
            <span class="sb-icon ic-green"><i class="fa-solid fa-user"></i></span>
            <span class="sb-lbl">User Management</span>
            <span class="sb-arr"><i class="bi bi-chevron-right"></i></span>
            <span class="sb-tip">User Management</span>
        </button>
        <ul class="sb-sub collapse {{ request()->routeIs('index.user','add.user','users.edit') ? 'show' : '' }}"
            id="colUser">

            @can('user_view')
            <li><a href="{{ route('index.user') }}"
                   class="sb-sub-link {{ request()->routeIs('index.user','users.edit') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-lime"><i class="fa-solid fa-users"></i></span>All Users
            </a></li>
            @endcan

            @can('user_create')
            <li><a href="{{ route('add.user') }}"
                   class="sb-sub-link {{ request()->routeIs('add.user') ? 'active' : '' }}">
                <span class="sb-sub-icon ic-amber"><i class="fa-solid fa-circle-plus"></i></span>Add User
            </a></li>
            @endcan

        </ul>
    </li>
    @endcanany

    {{-- ══════════════════════════════════════════
         CLIENT MANAGEMENT
    ══════════════════════════════════════════ --}}
    @canany(['client_view','client_create','client_edit','client_delete'])
    <li class="sb-section purple">Client Management</li>

    <li class="sb-item">
        <a href="{{ route('index.customer') }}"
           class="sb-link {{ request()->routeIs('index.customer','customers.show','customers.edit') ? 'active' : '' }}">
            <span class="sb-icon ic-purple"><i class="bi bi-people-fill"></i></span>
            <span class="sb-lbl">Client List</span>
            <span class="sb-tip">Client List</span>
        </a>
    </li>
    @endcanany

    {{-- ══════════════════════════════════════════
         SYSTEM SETTINGS
    ══════════════════════════════════════════ --}}
    @canany(['settings_view','role_manage','location_manage','society_config_manage'])
    <li class="sb-section orange">System</li>

    <li class="sb-item">
        <a href="{{ route('setting.view') }}"
           class="sb-link {{ request()->routeIs('setting.view','role.create','RolePermission.edit','city.view','sector.view','society.view','blocks.index','settings.*') ? 'active' : '' }}">
            <span class="sb-icon ic-orange"><i class="bi bi-gear-fill"></i></span>
            <span class="sb-lbl">System Settings</span>
            <span class="sb-tip">System Settings</span>
        </a>
    </li>
    @endcanany

</ul>
</nav>

        <div class="sb-foot">
            <div class="sb-user">
                @if (auth()->user()->profile_image && file_exists(public_path('storage/user_image/' . auth()->user()->profile_image)))
                    <img class="sb-ava" src="{{ asset('storage/user_image/' . auth()->user()->profile_image) }}"
                        alt="">
                @else
                    <div class="sb-ava">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
                @endif
                <div class="sb-uinfo">
                    <span class="sb-uname">{{ auth()->user()->name ?? 'User' }}</span>
                    <span class="sb-urole">{{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'Guest') }}</span>
                </div>

            </div>
        </div>

    </aside>

    {{-- ════════════ MAIN CONTENT ════════════ --}}
    <div class="main-wrap" id="mainWrap">

        <header class="topbar">
            <div class="tb-left">
                <div class="tb-ham" id="sbToggle"><i class="bi bi-list"></i></div>
                <div class="tb-div"></div>
                <div class="tb-title">Dashboard</div>
            </div>
            <div class="tb-right">
               <a href="javascript:void(0)" class="tb-btn" id="theme-toggle" title="Toggle Dark/Light Mode">
    <i class="bi bi-sun-fill" id="theme-icon-light"></i>
    <i class="bi bi-moon-stars-fill d-none" id="theme-icon-dark"></i>
</a>
                <div class="tb-date">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ date('d M, Y') }}</span>
                    <input type="date" value="{{ date('Y-m-d') }}" />
                </div>
                <div class="pf-wrap">
                    <input type="checkbox" id="pfToggle" />
                    <label for="pfToggle">
                        <div class="pf-btn">
                            @if (auth()->user()->profile_image && file_exists(public_path('storage/user_image/' . auth()->user()->profile_image)))
                                <img class="pf-ava"
                                    src="{{ asset('storage/user_image/' . auth()->user()->profile_image) }}"
                                    alt="">
                            @else
                                <div class="pf-ava">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
                            @endif
                            {{-- Name text: sidebar.js wraps this in <span class="pf-name"> for mobile hiding --}}
                            {{ auth()->user()->name ?? 'User' }}
                            <i class="bi bi-chevron-down pf-chev"></i>
                        </div>
                    </label>
                    <ul class="pf-drop">
                        <li class="pf-head">
                            <div class="pf-hname">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="pf-hrole">{{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'Guest') }}
                            </div>
                        </li>
                        <li><a href="#" class="pf-item"><i class="bi bi-person-fill"></i> My Profile</a></li>
                        <li><a href="#" class="pf-item"><i class="bi bi-sliders2"></i> Preferences</a></li>
                        <li class="pf-sep"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="pf-item danger">
                                    <i class="bi bi-box-arrow-right"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>




        {{-- Page content --}}
        <div class="content-body">
            @yield('content')
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

{{-- <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script> --}}
    <script src="{{ asset('js/script.js') }}"></script>
 @stack('scripts')


    </script>
</body>

</html>
