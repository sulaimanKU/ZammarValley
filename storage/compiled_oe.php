<?php $__env->startPush('styles'); ?>
<style>
.page-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    border-radius: 18px; padding: 24px 28px; margin-bottom: 22px;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 14px; position: relative; overflow: hidden;
}
.page-header::before { content:'';position:absolute;top:-50px;right:-30px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04); }
.page-header-title { font-size:1.1rem;font-weight:800;color:#fff;margin:0;position:relative;z-index:1; }
.page-header-sub   { font-size:11px;color:rgba(255,255,255,.5);margin:4px 0 0;position:relative;z-index:1; }

.report-btn { background:rgba(255,255,255,.12)!important;border:1px solid rgba(255,255,255,.2)!important;color:#fff!important;padding:8px 14px!important;border-radius:9px!important;font-size:12px!important;font-weight:700!important;display:inline-flex!important;align-items:center!important;gap:6px!important;text-decoration:none!important;cursor:pointer!important;transition:background .15s!important;white-space:nowrap!important; }
.report-btn:hover { background:rgba(255,255,255,.22)!important;color:#fff!important; }

.stat-row { display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:14px;margin-bottom:20px; }
@media(max-width:600px){ .stat-row{ grid-template-columns:1fr 1fr; } }
.stat-card { background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:16px 18px;display:flex;align-items:center;gap:13px;position:relative;overflow:hidden;transition:transform .15s; }
.stat-card:hover { transform:translateY(-2px); }
.stat-card::after { content:'';position:absolute;bottom:0;left:0;right:0;height:3px;background:var(--accent,#3b82f6);opacity:0;transition:opacity .15s; }
.stat-card:hover::after { opacity:1; }
.stat-icon  { width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0; }
.stat-label { font-size:10px;font-weight:700;color:var(--slate);text-transform:uppercase;letter-spacing:.6px; }
.stat-val   { font-size:18px;font-weight:800;color:var(--navy);margin-top:3px; }
.stat-sub   { font-size:10px;color:var(--slate);margin-top:3px; }

.tab-bar { background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:6px;margin-bottom:18px;display:flex;flex-wrap:wrap;gap:4px;width:100%; }
.tab-btn { padding:9px 18px;border-radius:10px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:transparent;color:var(--slate);display:inline-flex;align-items:center;gap:7px;transition:all .15s;white-space:nowrap;flex:1;justify-content:center; }
.tab-btn.active-expense { background:#fef2f2;color:#dc2626; }
.tab-btn.active-income  { background:#f0fdf4;color:#16a34a; }
.tab-btn.active-inventory { background:#ecfdf5;color:#059669; }
.tab-btn:not(.active-expense):not(.active-income):not(.active-inventory):hover { background:#f8fafc; }
@media(max-width:600px){
    .tab-btn { padding:8px 10px;font-size:12px;gap:5px; }
    .tab-btn i { font-size:14px; }
}
@media(max-width:400px){
    .tab-btn span { display:none; }
    .tab-btn { flex:0 0 calc(50% - 4px); }
}

.main-card { background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden; }
.card-toolbar { padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px; }
.card-title { font-size:13px;font-weight:800;color:var(--navy);margin:0; }

.filter-card { background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:18px 20px;margin-bottom:18px; }
.filter-label { font-size:11px;font-weight:700;color:var(--slate);display:block;margin-bottom:5px; }

.btn-navy  { background:var(--blue)!important;color:#fff!important;padding:9px 20px!important;border-radius:10px!important;font-size:13px!important;font-weight:700!important;display:inline-flex!important;align-items:center!important;gap:7px!important;border:none!important;cursor:pointer!important;transition:background .15s,box-shadow .15s!important;text-decoration:none!important;white-space:nowrap!important;line-height:1.4!important;outline:none!important; }
.btn-navy:hover  { background:#1e40af!important;color:#fff!important; }
.btn-green { background:#16a34a!important;color:#fff!important;padding:9px 20px!important;border-radius:10px!important;font-size:13px!important;font-weight:700!important;display:inline-flex!important;align-items:center!important;gap:7px!important;border:none!important;cursor:pointer!important;transition:background .15s!important;text-decoration:none!important;white-space:nowrap!important;line-height:1.4!important;outline:none!important; }
.btn-green:hover { background:#15803d!important;color:#fff!important; }

.exp-table { width:100%;border-collapse:collapse;min-width:800px; }
.exp-table thead th { font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--slate);font-weight:700;background:#fafbfc;border-bottom:1.5px solid var(--border);padding:11px 14px;white-space:nowrap; }
.exp-table tbody td { padding:12px 14px;border-bottom:1px solid #f8fafc;font-size:12px;vertical-align:middle; }
.exp-table tbody tr:last-child td { border-bottom:none; }
.exp-table tbody tr:hover { background:#fafcff; }
.exp-table tfoot td { padding:12px 14px;background:#f8fafc;border-top:2px solid var(--border); }

.cat-pill { font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;white-space:nowrap; }
.cat-salaries        { background:#eff6ff;color:#1d4ed8; }
.cat-utilities       { background:#fff7ed;color:#ea580c; }
.cat-marketing       { background:#fdf4ff;color:#7c3aed; }
.cat-rent            { background:#fef9c3;color:#92400e; }
.cat-inventory       { background:#ecfdf5;color:#065f46; }
.cat-tube_well       { background:#e0f2fe;color:#0369a1; }
.cat-rent_received   { background:#fdf4ff;color:#7c3aed; }
.cat-utility_recovery{ background:#fff7ed;color:#ea580c; }
.cat-sale_proceeds   { background:#f0fdf4;color:#15803d; }
.cat-misc            { background:#fef9c3;color:#92400e; }
.cat-others          { background:#f1f5f9;color:#475569; }

.status-pill { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700; }
.status-approved { background:var(--lime);color:#15803d; }
.status-pending  { background:#fef9c3;color:#92400e; }
.status-dot { width:6px;height:6px;border-radius:50%; }
.status-approved .status-dot { background:var(--green); }
.status-pending  .status-dot { background:var(--amber); }

.type-badge { font-size:9px;font-weight:800;padding:2px 8px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px; }
.type-expense { background:#fef2f2;color:#dc2626; }
.type-income  { background:#f0fdf4;color:#16a34a; }

.flash { display:flex;align-items:flex-start;gap:12px;padding:13px 18px;border-radius:12px;margin-bottom:16px;font-size:13px;font-weight:600; }
.flash-success { background:#f0fdf4;border:1px solid #86efac;color:#15803d; }
.flash-error   { background:#fef2f2;border:1px solid #fecaca;color:#dc2626; }

.empty-state { text-align:center;padding:50px 20px;color:var(--slate); }
.empty-state i { font-size:2.5rem;opacity:.2;display:block;margin-bottom:12px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3 px-3 px-md-4">


<div class="page-header">
    <div>
        <p class="page-header-title"><i class="bi bi-wallet2" style="margin-right:8px;opacity:.7;"></i>Office Expenses & Income</p>
        <p class="page-header-sub">Track, manage and export all company expenditures and income</p>
    </div>
    <div style="display:flex;gap:8px;position:relative;z-index:1;flex-wrap:wrap;align-items:center;">
        <a href="<?php echo e(route('reports.daily_cash')); ?>" class="report-btn"><i class="bi bi-calendar-day-fill"></i> Daily Cash</a>
        <a href="<?php echo e(route('reports.monthly_summary')); ?>" class="report-btn"><i class="bi bi-bar-chart-fill"></i> Monthly Report</a>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_add')): ?>
        
        <a href="<?php echo e(route('expenses.create')); ?>" class="btn-green">
            <i class="bi bi-plus-circle-fill"></i> New Expense / Income
        </a>
        <?php endif; ?>
    </div>
</div>


<?php if(session('success')): ?>
<div class="flash flash-success">
    <i class="bi bi-check-circle-fill" style="flex-shrink:0;"></i>
    <?php echo e(session('success')); ?>

    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button>
</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="flash flash-error">
    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;"></i>
    <?php echo e(session('error')); ?>

    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button>
</div>
<?php endif; ?>
<?php if($errors->any()): ?>
<div class="flash flash-error">
    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;"></i>
    <div><strong>Fix:</strong><ul style="margin:4px 0 0 14px;padding:0;font-size:12px;"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;">&times;</button>
</div>
<?php endif; ?>


<div class="stat-row">
    <div class="stat-card" style="--accent:#dc2626;">
        <div class="stat-icon" style="background:#fef2f2;"><i class="bi bi-arrow-up-circle-fill" style="color:#dc2626;"></i></div>
        <div><div class="stat-label">All Expenses</div><div class="stat-val">PKR <?php echo e(number_format($total)); ?></div><div class="stat-sub">All time</div></div>
    </div>
    <div class="stat-card" style="--accent:#d97706;">
        <div class="stat-icon" style="background:#fffbeb;"><i class="bi bi-calendar-month-fill" style="color:#d97706;"></i></div>
        <div><div class="stat-label"><?php echo e(date('F')); ?> Expenses</div><div class="stat-val">PKR <?php echo e(number_format($total_expenses)); ?></div><div class="stat-sub">This month</div></div>
    </div>
    <div class="stat-card" style="--accent:#16a34a;">
        <div class="stat-icon" style="background:#f0fdf4;"><i class="bi bi-arrow-down-circle-fill" style="color:#16a34a;"></i></div>
        <div><div class="stat-label">All Income</div><div class="stat-val">PKR <?php echo e(number_format($total_income ?? 0)); ?></div><div class="stat-sub">All time</div></div>
    </div>
    <div class="stat-card" style="--accent:#0369a1;">
        <div class="stat-icon" style="background:#e0f2fe;"><i class="bi bi-droplet-fill" style="color:#0369a1;"></i></div>
        <div><div class="stat-label"><?php echo e(date('F')); ?> Income</div><div class="stat-val">PKR <?php echo e(number_format($total_income_month ?? 0)); ?></div><div class="stat-sub">This month</div></div>
    </div>
    <div class="stat-card" style="--accent:#059669;">
    <div class="stat-icon" style="background:#ecfdf5;"><i class="bi bi-box-seam-fill" style="color:#059669;"></i></div>
    <div>
        <div class="stat-label">Inventory</div>
        <div class="stat-val">PKR <?php echo e(number_format($total_inventory ?? 0)); ?></div>
        <div class="stat-sub">Approved Stock Value</div>
    </div>
</div>
    <div class="stat-card" style="--accent:#0f172a;">
        <div class="stat-icon" style="background:#f1f5f9;"><i class="bi bi-wallet2" style="color:#0f172a;"></i></div>
        <div><div class="stat-label">Net Balance</div><div class="stat-val" style="color:<?php echo e(($net_balance ?? 0) >= 0 ? '#16a34a' : '#dc2626'); ?>;">PKR <?php echo e(number_format(abs($net_balance ?? 0))); ?></div><div class="stat-sub"><?php echo e(($net_balance ?? 0) >= 0 ? 'Surplus' : 'Deficit'); ?> · Income − Exp − Inv</div></div>
    </div>
    <div class="stat-card" style="--accent:#6366f1;">
        <div class="stat-icon" style="background:#eef2ff;"><i class="bi bi-receipt" style="color:#6366f1;"></i></div>
        <div><div class="stat-label">Total Records</div><div class="stat-val"><?php echo e($expenses->count() + ($incomes->count() ?? 0) + ($inventories->count() ?? 0)); ?></div><div class="stat-sub">Exp + Income + Inventory</div></div>
    </div>
</div>


<?php if(isset($fundSources)): ?>
<div style="margin-bottom:20px;">
    <div style="font-size:12px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.7px;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
        Fund Sources — Collected vs. Used in Expenses
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
        <?php $__currentLoopData = $fundSources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $fs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $pct = $fs['collected'] > 0 ? min(100, round($fs['used'] / $fs['collected'] * 100)) : 0; ?>
        <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 18px;border-left:4px solid <?php echo e($fs['color']); ?>;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                <span style="font-size:18px;"><?php echo e($fs['icon']); ?></span>
                <span style="font-size:12px;font-weight:800;color:#0f172a;"><?php echo e($fs['label']); ?></span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:10px;">
                <div style="text-align:center;background:#f8fafc;border-radius:8px;padding:8px 4px;">
                    <div style="font-size:9px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px;">Collected</div>
                    <div style="font-size:12px;font-weight:800;color:#16a34a;"><?php echo e(number_format($fs['collected'])); ?></div>
                </div>
                <div style="text-align:center;background:#fef2f2;border-radius:8px;padding:8px 4px;">
                    <div style="font-size:9px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px;">Used</div>
                    <div style="font-size:12px;font-weight:800;color:#dc2626;"><?php echo e(number_format($fs['used'])); ?></div>
                </div>
                <div style="text-align:center;background:<?php echo e($fs['bg']); ?>;border-radius:8px;padding:8px 4px;">
                    <div style="font-size:9px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px;">Left</div>
                    <div style="font-size:12px;font-weight:800;color:<?php echo e($fs['color']); ?>;"><?php echo e(number_format($fs['remaining'])); ?></div>
                </div>
            </div>
            <div style="background:#f1f5f9;border-radius:20px;height:5px;overflow:hidden;">
                <div style="height:100%;width:<?php echo e($pct); ?>%;background:<?php echo e($fs['color']); ?>;border-radius:20px;transition:width .4s;"></div>
            </div>
            <div style="font-size:10px;color:#94a3b8;margin-top:4px;text-align:right;"><?php echo e($pct); ?>% used</div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>


<div class="filter-card" style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);padding:20px;margin-bottom:20px;">
    <div style="font-weight:800;color:#1e3a8a;font-size:15px;margin-bottom:15px;display:flex;align-items:center;gap:10px;">
        <div style="background:#eff6ff;padding:8px;border-radius:8px;"><i class="bi bi-funnel-fill" style="color:#2563eb;"></i></div>
        Filter & Generate Reports
    </div>
<form action="<?php echo e(route('office_expenses.search')); ?>" method="GET" target="_blank">

        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="filter-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="<?php echo e(request('from_date')); ?>" >
            </div>
            <div class="col-md-2">
                <label class="filter-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="<?php echo e(request('to_date')); ?>" >
            </div>
          <div class="col-md-2">
    <label class="filter-label">Category</label>
    <select name="category" class="form-select">
        <option value="">All Categories</option>

        <optgroup label="── Expenses ──">
            <?php $__currentLoopData = ['Salaries','Utilities','Marketing','Rent','Others']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cat); ?>" <?php echo e(request('category') == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </optgroup>

        <optgroup label="── Income ──">
            <?php $__currentLoopData = ['Tube Well','Rent Received','Utility Recovery','Sale Proceeds','Misc']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cat); ?>" <?php echo e(request('category') == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </optgroup>

        
        <optgroup label="── Inventory ──">
            <?php $__currentLoopData = ['Office Supplies','Construction Materials','Equipment & Tools','Furniture','IT & Electronics','Stationery','Cleaning Supplies','Inventory Others']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cat); ?>" <?php echo e(request('category') == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </optgroup>
    </select>
</div>

<div class="col-md-2">
    <label class="filter-label">Type</label>
    <select name="filter_type" class="form-select">
        <option value="">All Records</option>
        <option value="expense" <?php echo e(request('filter_type') == 'expense' ? 'selected' : ''); ?>>Expenses Only</option>
        <option value="income" <?php echo e(request('filter_type') == 'income' ? 'selected' : ''); ?>>Income Only</option>
        
        <option value="inventory" <?php echo e(request('filter_type') == 'inventory' ? 'selected' : ''); ?>>Inventory Only</option>
    </select>
</div>
            <div class="col-md-4">
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="submit" name="export_type" value="search" class="btn" style="background:#1e293b;color:#fff;border-radius:8px;padding:8px 18px;font-weight:600;"><i class="bi bi-search me-1"></i>Search</button>
                    <button type="submit" name="export_type" value="pdf"    class="btn" style="background:#dc2626;color:#fff;border-radius:8px;padding:8px 14px;font-weight:600;"><i class="bi bi-file-earmark-pdf-fill me-1"></i>PDF</button>
                    <button type="submit" name="export_type" value="excel"  class="btn" style="background:#16a34a;color:#fff;border-radius:8px;padding:8px 14px;font-weight:600;"><i class="bi bi-file-earmark-excel-fill me-1"></i>Excel</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div class="tab-bar">
    <button class="tab-btn active-expense" id="tab-expense" onclick="switchMainTab('expense')">
        <i class="bi bi-arrow-up-circle-fill"></i> Expenses
        <span style="background:#fecaca;color:#dc2626;font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;"><?php echo e($expenses->count()); ?></span>
    </button>
    <button class="tab-btn" id="tab-income" onclick="switchMainTab('income')">
        <i class="bi bi-arrow-down-circle-fill"></i> Income
        <span style="background:#dcfce7;color:#16a34a;font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;"><?php echo e($incomes->count() ?? 0); ?></span>
    </button>
    <button class="tab-btn" id="tab-inventory" onclick="switchMainTab('inventory')">
        <i class="bi bi-box-seam"></i> Inventory
        <span style="background:#d1fae5;color:#059669;font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;"><?php echo e($inventories->count() ?? 0); ?></span>
    </button>
    <button class="tab-btn" id="tab-all" onclick="switchMainTab('all')">
        <i class="bi bi-list-ul"></i> All Records
    </button>
</div>


<div id="panel-expense" class="main-card" style="margin-bottom:18px;">
    <div class="card-toolbar">
        <div>
            <p class="card-title"><i class="bi bi-arrow-up-circle-fill" style="color:#dc2626;margin-right:6px;"></i>Office Expenses</p>
            <p style="font-size:11px;color:var(--slate);margin:2px 0 0;"><?php echo e($expenses->count()); ?> record<?php echo e($expenses->count() !== 1 ? 's' : ''); ?></p>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="exp-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center;">#</th>
                    <th>Date</th><th>Category</th><th>Fund Source</th><th>Paid To</th><th>Amount</th>
                    <th>Method</th><th>Ref #</th><th>Status</th><th>Proof</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $catClass = 'cat-'.strtolower(str_replace(' ','_',$expense->category)); ?>
                <tr>
                    <td style="text-align:center;color:var(--slate);font-size:11px;"><?php echo e($loop->iteration); ?></td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:var(--navy);"><?php echo e(\Carbon\Carbon::parse($expense->expense_date)->format('d M Y')); ?></div>
                        <div style="font-size:10px;color:var(--slate);">
                            <?php $d = \Carbon\Carbon::parse($expense->expense_date); ?>
                            <?php if($d->isToday()): ?> Today <?php elseif($d->isYesterday()): ?> Yesterday <?php else: ?> <?php echo e($d->diffForHumans()); ?> <?php endif; ?>
                        </div>
                    </td>
                    <td><span class="cat-pill <?php echo e($catClass); ?>"><?php echo e($expense->category); ?></span></td>
                    <td>
                        <?php
                            $fsMeta = [
                                'plot_payments'   => ['label'=>'Plot Payments',  'color'=>'#1d4ed8','bg'=>'#eff6ff','icon'=>'🏘️'],
                                'security_fee'    => ['label'=>'Security Fee',   'color'=>'#7c3aed','bg'=>'#fdf4ff','icon'=>'🔒'],
                                'registry_fee'    => ['label'=>'Registry Fee',   'color'=>'#0369a1','bg'=>'#e0f2fe','icon'=>'📋'],
                                'development_fee' => ['label'=>'Dev. Fee',       'color'=>'#16a34a','bg'=>'#f0fdf4','icon'=>'🏗️'],
                                'transfer_fee'    => ['label'=>'Transfer Fee',   'color'=>'#0891b2','bg'=>'#ecfeff','icon'=>'🔄'],
                                'misc_income'     => ['label'=>'Misc. Income',   'color'=>'#d97706','bg'=>'#fffbeb','icon'=>'💰'],
                            ];
                            $fsInfo = $expense->fund_source ? ($fsMeta[$expense->fund_source] ?? null) : null;
                        ?>
                        <?php if($fsInfo): ?>
                            <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;background:<?php echo e($fsInfo['bg']); ?>;color:<?php echo e($fsInfo['color']); ?>;white-space:nowrap;"><?php echo e($fsInfo['icon']); ?> <?php echo e($fsInfo['label']); ?></span>
                        <?php else: ?>
                            <span style="color:#cbd5e1;font-size:11px;">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:var(--navy);"><?php echo e($expense->paid_to); ?></div>
                        <?php if($expense->remarks): ?><div style="font-size:10px;color:var(--slate);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;"><?php echo e($expense->remarks); ?></div><?php endif; ?>
                    </td>
                    <td><strong style="color:#dc2626;font-size:14px;">PKR <?php echo e(number_format($expense->amount)); ?></strong></td>
                    <td><span style="font-size:11px;font-weight:700;background:#f1f5f9;color:var(--slate);padding:3px 10px;border-radius:20px;"><?php echo e($expense->payment_method); ?></span></td>
                    <td><span style="font-family:monospace;font-size:11px;color:var(--slate);"><?php echo e($expense->reference_no ?? '—'); ?></span></td>
                    <td>
                        <span class="status-pill <?php echo e($expense->status === 'approved' ? 'status-approved' : 'status-pending'); ?>">
                            <span class="status-dot"></span><?php echo e(ucfirst($expense->status)); ?>

                        </span>
                    </td>
                    <td style="text-align:center;">
                        <?php if($expense->payment_proof): ?>
                            <a href="<?php echo e(asset('storage/officeExpensesProof/'.$expense->payment_proof)); ?>" target="_blank" style="font-size:10px;font-weight:700;color:#1d4ed8;text-decoration:none;"><i class="bi bi-paperclip"></i> View</a>
                        <?php else: ?>
                            <span style="color:#cbd5e1;font-size:11px;">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex;gap:6px;align-items:center;">
                            <a href="<?php echo e(route('expense.detail.view', $expense->id)); ?>" style="width:30px;height:30px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;" title="View"><i class="bi bi-eye-fill"></i></a>
                            <a href="<?php echo e(route('expense.detail.pdf', $expense->id)); ?>" target="_blank" style="width:30px;height:30px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;" title="Print"><i class="bi bi-printer-fill"></i></a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_edit')): ?>
                            <a href="<?php echo e(route('expense.edit.view', $expense->id)); ?>" style="width:30px;height:30px;border-radius:8px;background:#f8fafc;border:1px solid var(--border);color:var(--slate);display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_delete')): ?>
                            <form action="<?php echo e(route('expenses.destroy', $expense->id)); ?>" method="POST" onsubmit="return confirm('Delete this expense?')" style="margin:0;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="width:30px;height:30px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:13px;cursor:pointer;" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="11"><div class="empty-state"><i class="bi bi-wallet2"></i><p style="font-size:14px;font-weight:700;margin:0 0 6px;">No expense records</p><p style="font-size:12px;margin:0;">Click "New Expense / Income" to add the first record.</p></div></td></tr>
                <?php endif; ?>
            </tbody>
            <?php if($expenses->count() > 0): ?>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right;font-size:10px;font-weight:700;color:var(--slate);letter-spacing:.5px;">TOTAL EXPENSES</td>
                    <td style="font-size:15px;font-weight:800;color:#dc2626;">PKR <?php echo e(number_format($expenses->sum('amount'))); ?></td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>


<div id="panel-income" class="main-card" style="margin-bottom:18px;display:none;">
    <div class="card-toolbar">
        <div>
            <p class="card-title"><i class="bi bi-arrow-down-circle-fill" style="color:#16a34a;margin-right:6px;"></i>Office Income</p>
            <p style="font-size:11px;color:var(--slate);margin:2px 0 0;"><?php echo e($incomes->count() ?? 0); ?> record<?php echo e(($incomes->count() ?? 0) !== 1 ? 's' : ''); ?></p>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="exp-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center;">#</th>
                    <th>Date</th><th>Category</th><th>Received From</th><th>Amount</th>
                    <th>Method</th><th>Ref #</th><th>Status</th><th>Proof</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $incCatClass = 'cat-'.strtolower(str_replace(' ','_',$income->category)); ?>
                <tr>
                    <td style="text-align:center;color:var(--slate);font-size:11px;"><?php echo e($loop->iteration); ?></td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:var(--navy);"><?php echo e(\Carbon\Carbon::parse($income->expense_date)->format('d M Y')); ?></div>
                        <div style="font-size:10px;color:var(--slate);"><?php echo e(\Carbon\Carbon::parse($income->expense_date)->diffForHumans()); ?></div>
                    </td>
                    <td><span class="cat-pill <?php echo e($incCatClass); ?>"><?php echo e($income->category); ?></span></td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:var(--navy);"><?php echo e($income->paid_to); ?></div>
                        <?php if($income->remarks): ?><div style="font-size:10px;color:var(--slate);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;"><?php echo e($income->remarks); ?></div><?php endif; ?>
                    </td>
                    <td><strong style="color:#16a34a;font-size:14px;">PKR <?php echo e(number_format($income->amount)); ?></strong></td>
                    <td><span style="font-size:11px;font-weight:700;background:#f1f5f9;color:var(--slate);padding:3px 10px;border-radius:20px;"><?php echo e($income->payment_method); ?></span></td>
                    <td><span style="font-family:monospace;font-size:11px;color:var(--slate);"><?php echo e($income->reference_no ?? '—'); ?></span></td>
                    <td>
                        <span class="status-pill <?php echo e($income->status === 'approved' ? 'status-approved' : 'status-pending'); ?>">
                            <span class="status-dot"></span><?php echo e(ucfirst($income->status)); ?>

                        </span>
                    </td>
                    <td style="text-align:center;">
                        <?php if($income->payment_proof): ?>
                            <a href="<?php echo e(asset('storage/officeExpensesProof/'.$income->payment_proof)); ?>" target="_blank" style="font-size:10px;font-weight:700;color:#1d4ed8;text-decoration:none;"><i class="bi bi-paperclip"></i> View</a>
                        <?php else: ?>
                            <span style="color:#cbd5e1;font-size:11px;">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex;gap:6px;align-items:center;">
                            <a href="<?php echo e(route('expense.detail.view', $income->id)); ?>" style="width:30px;height:30px;border-radius:8px;background:#f0fdf4;border:1px solid #86efac;color:#16a34a;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;" title="View"><i class="bi bi-eye-fill"></i></a>
                            <a href="<?php echo e(route('expense.detail.pdf', $income->id)); ?>" target="_blank" style="width:30px;height:30px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;" title="Print PDF"><i class="bi bi-printer-fill"></i></a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_edit')): ?>
                            <a href="<?php echo e(route('expense.edit.view', $income->id)); ?>" style="width:30px;height:30px;border-radius:8px;background:#f8fafc;border:1px solid var(--border);color:var(--slate);display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_delete')): ?>
                            <form action="<?php echo e(route('expenses.destroy', $income->id)); ?>" method="POST" onsubmit="return confirm('Delete this income record?')" style="margin:0;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="width:30px;height:30px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:13px;cursor:pointer;" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="10"><div class="empty-state"><i class="bi bi-arrow-down-circle"></i><p style="font-size:14px;font-weight:700;margin:0 0 6px;">No income records</p><p style="font-size:12px;margin:0;">Click "New Expense / Income" to add income.</p></div></td></tr>
                <?php endif; ?>
            </tbody>
            <?php if(isset($incomes) && $incomes->count() > 0): ?>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;font-size:10px;font-weight:700;color:var(--slate);letter-spacing:.5px;">TOTAL INCOME</td>
                    <td style="font-size:15px;font-weight:800;color:#16a34a;">PKR <?php echo e(number_format($incomes->sum('amount'))); ?></td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<div id="panel-inventory" class="main-card" style="margin-bottom:18px;display:none;">
    <div class="card-toolbar">
        <div>
            <p class="card-title"><i class="bi bi-box-seam-fill" style="color:#059669;margin-right:6px;"></i>Office Inventory / Stock</p>
            <p style="font-size:11px;color:var(--slate);margin:2px 0 0;"><?php echo e($inventories->count() ?? 0); ?> item records</p>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="exp-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center;">#</th>
                    <th>Date</th><th>Category</th><th>Supplier/Party</th><th>Amount</th>
                    <th>Method</th><th>Status</th><th>Proof</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="text-align:center;color:var(--slate);font-size:11px;"><?php echo e($loop->iteration); ?></td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:var(--navy);"><?php echo e(\Carbon\Carbon::parse($inv->expense_date)->format('d M Y')); ?></div>
                    </td>
                    <td><span class="cat-pill cat-inventory"><?php echo e($inv->category); ?></span></td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:var(--navy);"><?php echo e($inv->paid_to); ?></div>
                        <?php if($inv->remarks): ?><div style="font-size:10px;color:var(--slate);"><?php echo e($inv->remarks); ?></div><?php endif; ?>
                    </td>
                    <td><strong style="color:#059669;font-size:14px;">PKR <?php echo e(number_format($inv->amount)); ?></strong></td>
                    <td><span style="font-size:11px;font-weight:700;background:#f1f5f9;color:var(--slate);padding:3px 10px;border-radius:20px;"><?php echo e($inv->payment_method); ?></span></td>
                    <td>
                        <span class="status-pill <?php echo e($inv->status === 'approved' ? 'status-approved' : 'status-pending'); ?>">
                            <span class="status-dot"></span><?php echo e(ucfirst($inv->status)); ?>

                        </span>
                    </td>
                    <td style="text-align:center;">
                        <?php if($inv->payment_proof): ?>
                            <a href="<?php echo e(asset('storage/officeExpensesProof/'.$inv->payment_proof)); ?>" target="_blank" style="font-size:10px;font-weight:700;color:#1d4ed8;text-decoration:none;"><i class="bi bi-paperclip"></i> View</a>
                        <?php else: ?> <span style="color:#cbd5e1;font-size:11px;">—</span> <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex;gap:6px;align-items:center;">
                            <a href="<?php echo e(route('expense.detail.view', $inv->id)); ?>" style="width:30px;height:30px;border-radius:8px;background:#ecfdf5;border:1px solid #6ee7b7;color:#059669;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;" title="View"><i class="bi bi-eye-fill"></i></a>
                            <a href="<?php echo e(route('expense.detail.pdf', $inv->id)); ?>" target="_blank" style="width:30px;height:30px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;" title="Print PDF"><i class="bi bi-printer-fill"></i></a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_edit')): ?>
                            <a href="<?php echo e(route('expense.edit.view', $inv->id)); ?>" style="width:30px;height:30px;border-radius:8px;background:#f8fafc;border:1px solid var(--border);color:var(--slate);display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_delete')): ?>
                            <form action="<?php echo e(route('expenses.destroy', $inv->id)); ?>" method="POST" onsubmit="return confirm('Delete this inventory record?')" style="margin:0;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="width:30px;height:30px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:13px;cursor:pointer;" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="9"><div class="empty-state"><i class="bi bi-box-seam"></i><p>No inventory records found.</p></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="panel-all" class="main-card" style="margin-bottom:18px;display:none;">
    <div class="card-toolbar">
        <div>
            <p class="card-title"><i class="bi bi-list-ul" style="margin-right:6px;"></i>All Records</p>
            <p style="font-size:11px;color:var(--slate);margin:2px 0 0;"><?php echo e($expenses->count() + ($incomes->count() ?? 0)); ?> total records</p>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="exp-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center;">#</th>
                    <th>Date</th><th>Type</th><th>Category</th><th>Party</th>
                    <th>Amount</th><th>Method</th><th>Status</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
               <?php
    // Merge all three collections: Expenses, Incomes, and Inventories
    $allRecords = $expenses
        ->concat($incomes ?? collect())
        ->concat($inventories ?? collect()) // ✅ Added this line
        ->sortByDesc('expense_date');

    $counter = 1;
?>
                <?php $__empty_1 = true; $__currentLoopData = $allRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
        <td style="text-align:center;color:var(--slate);font-size:11px;"><?php echo e($counter++); ?></td>
        <td>
            <div style="font-size:12px;font-weight:600;color:var(--navy);">
                <?php echo e(\Carbon\Carbon::parse($rec->expense_date)->format('d M Y')); ?>

            </div>
        </td>
        <td>
            
            <span class="type-badge
                <?php echo e($rec->type === 'income' ? 'type-income' : ($rec->type === 'inventory' ? 'type-inventory' : 'type-expense')); ?>">
                <?php echo e(ucfirst($rec->type)); ?>

            </span>
        </td>
        <td>
            <span class="cat-pill cat-<?php echo e(strtolower(str_replace(' ','_',$rec->category))); ?>">
                <?php echo e($rec->category); ?>

            </span>
        </td>
        <td style="font-weight:700;font-size:12px;"><?php echo e($rec->paid_to); ?></td>
        <td>
            
            <strong style="color:
                <?php if($rec->type === 'income'): ?> #16a34a
                <?php elseif($rec->type === 'inventory'): ?> #059669
                <?php else: ?> #dc2626 <?php endif; ?>;
                font-size:13px;">
                PKR <?php echo e(number_format($rec->amount)); ?>

            </strong>
        </td>
        <td>
            <span style="font-size:11px;background:#f1f5f9;color:var(--slate);padding:3px 8px;border-radius:20px;font-weight:600;">
                <?php echo e($rec->payment_method); ?>

            </span>
        </td>
        <td>
            <span class="status-pill <?php echo e($rec->status === 'approved' ? 'status-approved' : 'status-pending'); ?>">
                <span class="status-dot"></span><?php echo e(ucfirst($rec->status)); ?>

            </span>
        </td>
        <td style="text-align:center;">
            <div style="display:inline-flex;gap:5px;align-items:center;">
                <a href="<?php echo e(route('expense.detail.view', $rec->id)); ?>" style="width:28px;height:28px;border-radius:7px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;" title="View"><i class="bi bi-eye-fill"></i></a>
                <a href="<?php echo e(route('expense.detail.pdf', $rec->id)); ?>" target="_blank" style="width:28px;height:28px;border-radius:7px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;" title="Print PDF"><i class="bi bi-printer-fill"></i></a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_edit')): ?>
                <a href="<?php echo e(route('expense.edit.view', $rec->id)); ?>" style="width:28px;height:28px;border-radius:7px;background:#f8fafc;border:1px solid var(--border);color:var(--slate);display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_delete')): ?>
                <form action="<?php echo e(route('expenses.destroy', $rec->id)); ?>" method="POST" onsubmit="return confirm('Delete this record?')" style="margin:0;">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" style="width:28px;height:28px;border-radius:7px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                </form>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
        <td colspan="9">
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p style="font-size:14px;font-weight:700;margin:0;">No records found</p>
            </div>
        </td>
    </tr>
<?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div style="background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:6px;margin-bottom:18px;display:flex;flex-wrap:wrap;gap:4px;">
    <div style="width:100%;padding:4px 8px 6px;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.7px;border-bottom:1px solid var(--border);margin-bottom:4px;">
        <i class="bi bi-currency-dollar me-1"></i> Income Sources — collected payments by type
    </div>
    <button class="tab-btn active-income" id="src-tab-plot_payments" onclick="switchSourceTab('plot_payments')" style="flex:1;">
        🏘️ <span>Plot Payments</span>
        <span style="background:#dbeafe;color:#1d4ed8;font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;"><?php echo e($plotPaymentRows->count()); ?></span>
    </button>
    <button class="tab-btn" id="src-tab-security_fee" onclick="switchSourceTab('security_fee')" style="flex:1;">
        🔒 <span>Security Fee</span>
        <span style="background:#ede9fe;color:#7c3aed;font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;"><?php echo e(($feePaymentRows['security'] ?? collect())->count()); ?></span>
    </button>
    <button class="tab-btn" id="src-tab-development_fee" onclick="switchSourceTab('development_fee')" style="flex:1;">
        🏗️ <span>Development Fee</span>
        <span style="background:#dcfce7;color:#16a34a;font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;"><?php echo e(($feePaymentRows['development'] ?? collect())->count()); ?></span>
    </button>
    <button class="tab-btn" id="src-tab-registry_fee" onclick="switchSourceTab('registry_fee')" style="flex:1;">
        📋 <span>Registry Fee</span>
        <span style="background:#e0f2fe;color:#0369a1;font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;"><?php echo e(($feePaymentRows['registry'] ?? collect())->count()); ?></span>
    </button>
    <button class="tab-btn" id="src-tab-transfer_fee" onclick="switchSourceTab('transfer_fee')" style="flex:1;">
        🔄 <span>Transfer Fee</span>
        <span style="background:#cffafe;color:#0891b2;font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;"><?php echo e(($feePaymentRows['transfer'] ?? collect())->count()); ?></span>
    </button>
    <button class="tab-btn" id="src-tab-misc_income" onclick="switchSourceTab('misc_income')" style="flex:1;">
        💰 <span>Misc. Income</span>
        <span style="background:#fef9c3;color:#92400e;font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;"><?php echo e($incomes->count()); ?></span>
    </button>
</div>



<?php
$srcPanels = [
    ['id'=>'plot_payments',   'label'=>'Plot Payments',   'icon'=>'🏘️', 'color'=>'#1d4ed8', 'bg'=>'#eff6ff', 'border'=>'#bfdbfe'],
    ['id'=>'security_fee',    'label'=>'Security Fee',    'icon'=>'🔒', 'color'=>'#7c3aed', 'bg'=>'#fdf4ff', 'border'=>'#ddd6fe'],
    ['id'=>'development_fee', 'label'=>'Development Fee', 'icon'=>'🏗️', 'color'=>'#16a34a', 'bg'=>'#f0fdf4', 'border'=>'#bbf7d0'],
    ['id'=>'registry_fee',    'label'=>'Registry Fee',    'icon'=>'📋', 'color'=>'#0369a1', 'bg'=>'#e0f2fe', 'border'=>'#bae6fd'],
    ['id'=>'transfer_fee',    'label'=>'Transfer Fee',    'icon'=>'🔄', 'color'=>'#0891b2', 'bg'=>'#ecfeff', 'border'=>'#a5f3fc'],
];
?>


<div id="src-panel-plot_payments" class="main-card" style="margin-bottom:18px;display:none;">
    <?php
        $ppGross      = $plotPaymentRows->sum('amount_paid');
        $ppRefunded   = $cancelledBookingRefunds->sum('refund');
        $ppNet        = max(0, $ppGross - $ppRefunded);
        $ppCancelCount = $plotPaymentRows->filter(fn($r) => ($r->booking->status ?? '') === 'cancelled')->count();
    ?>
    <div class="card-toolbar" style="background:linear-gradient(135deg,#eff6ff,#fff);">
        <div>
            <p class="card-title" style="color:#1d4ed8;">🏘️ Plot Payments <span style="font-weight:400;font-size:11px;color:#64748b;">— all paid plot installments</span></p>
            <p style="font-size:11px;color:var(--slate);margin:2px 0 0;">
                <?php echo e($plotPaymentRows->count()); ?> records · Gross PKR <?php echo e(number_format($ppGross)); ?>

                <?php if($ppRefunded > 0): ?>
                 · <span style="color:#dc2626;">Refunded PKR <?php echo e(number_format($ppRefunded)); ?></span>
                 · <strong style="color:#1d4ed8;">Net PKR <?php echo e(number_format($ppNet)); ?></strong>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="exp-table">
            <thead>
                <tr>
                    <th style="width:36px;text-align:center;">#</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Plot</th>
                    <th>Booking Ref</th>
                    <th>Amount Paid</th>
                    <th>Method</th>
                    <th>Receipt</th>
                    <th style="width:80px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $plotPaymentRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $isCancelled = ($pp->booking->status ?? '') === 'cancelled'; ?>
                <tr style="<?php echo e($isCancelled ? 'background:#fffbeb;' : ''); ?>">
                    <td style="text-align:center;font-size:11px;color:#94a3b8;"><?php echo e($loop->iteration); ?></td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:#0f172a;"><?php echo e(\Carbon\Carbon::parse($pp->paid_date)->format('d M Y')); ?></div>
                        <div style="font-size:10px;color:#94a3b8;"><?php echo e(\Carbon\Carbon::parse($pp->paid_date)->diffForHumans()); ?></div>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:#0f172a;"><?php echo e($pp->booking->customer->name ?? '—'); ?></div>
                        <div style="font-size:10px;color:#94a3b8;"><?php echo e($pp->booking->customer->cnic ?? ''); ?></div>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:#0f172a;">Plot #<?php echo e($pp->booking->plot->plot_number ?? '—'); ?></div>
                        <div style="font-size:10px;color:#94a3b8;"><?php echo e($pp->booking->plot->block ?? ''); ?> <?php echo e($pp->booking->plot->street_number ? '· St '.$pp->booking->plot->street_number : ''); ?></div>
                    </td>
                    <td>
                        <span style="font-family:monospace;font-size:11px;color:<?php echo e($isCancelled ? '#92400e' : '#1d4ed8'); ?>;"><?php echo e($pp->booking->customer_booking_id ?? '—'); ?></span>
                        <?php if($isCancelled): ?>
                        <?php $refInfo = $cancelledBookingRefunds[$pp->booking_id] ?? null; ?>
                        <div style="margin-top:3px;">
                            <span style="font-size:9px;font-weight:800;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:2px 7px;border-radius:20px;display:inline-block;">⚠️ Cancelled</span>
                            <?php if($refInfo): ?>
                            <div style="font-size:9px;margin-top:2px;color:#92400e;font-weight:600;">
                                Total paid: PKR <?php echo e(number_format($refInfo['total_paid'])); ?>

                                <?php if($refInfo['refund'] > 0): ?>
                                · Refunded: PKR <?php echo e(number_format($refInfo['refund'])); ?>

                                · <span style="color:#15803d;">Retained: PKR <?php echo e(number_format(max(0, $refInfo['total_paid'] - $refInfo['refund']))); ?></span>
                                <?php else: ?>
                                · <span style="color:#64748b;">No refund given</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong style="color:<?php echo e($isCancelled ? '#92400e' : '#1d4ed8'); ?>;font-size:14px;">PKR <?php echo e(number_format($pp->amount_paid)); ?></strong>
                        <?php if($isCancelled): ?>
                        <div style="font-size:9px;color:#94a3b8;">cancelled booking</div>
                        <?php endif; ?>
                    </td>
                    <td><span style="font-size:11px;font-weight:600;background:#f1f5f9;color:#64748b;padding:3px 8px;border-radius:20px;"><?php echo e($pp->payment_type ?? '—'); ?></span></td>
                    <td style="font-size:11px;color:#64748b;font-family:monospace;"><?php echo e($pp->receipt_no ?? '—'); ?></td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex;gap:5px;align-items:center;">
                            <a href="<?php echo e(route('payment.receipt', $pp->id)); ?>" target="_blank" style="width:28px;height:28px;border-radius:7px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;" title="View Receipt"><i class="bi bi-printer-fill"></i></a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payment_add')): ?>
                            <form action="<?php echo e(route('plot.payment.destroy', $pp->id)); ?>" method="POST" onsubmit="return confirm('Delete this payment record? This cannot be undone.')" style="margin:0;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="width:28px;height:28px;border-radius:7px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="9"><div class="empty-state"><i class="bi bi-inbox"></i><p>No plot payments recorded yet.</p></div></td></tr>
                <?php endif; ?>
            </tbody>
            <?php if($plotPaymentRows->count() > 0): ?>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right;font-size:10px;font-weight:700;color:#64748b;letter-spacing:.5px;">GROSS COLLECTED</td>
                    <td style="font-size:13px;font-weight:700;color:#64748b;">PKR <?php echo e(number_format($ppGross)); ?></td>
                    <td colspan="3"></td>
                </tr>
                <?php if($ppRefunded > 0): ?>
                <tr>
                    <td colspan="5" style="text-align:right;font-size:10px;font-weight:700;color:#dc2626;letter-spacing:.5px;">REFUNDED (<?php echo e($cancelledBookingRefunds->count()); ?> booking<?php echo e($cancelledBookingRefunds->count() > 1 ? 's' : ''); ?>)</td>
                    <td style="font-size:13px;font-weight:700;color:#dc2626;">− PKR <?php echo e(number_format($ppRefunded)); ?></td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align:right;font-size:10px;font-weight:800;color:#1d4ed8;letter-spacing:.5px;">NET RETAINED</td>
                    <td style="font-size:15px;font-weight:800;color:#1d4ed8;">PKR <?php echo e(number_format($ppNet)); ?></td>
                    <td colspan="3"></td>
                </tr>
                <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:right;font-size:10px;font-weight:700;color:#1d4ed8;letter-spacing:.5px;">TOTAL COLLECTED</td>
                    <td style="font-size:15px;font-weight:800;color:#1d4ed8;">PKR <?php echo e(number_format($ppGross)); ?></td>
                    <td colspan="3"></td>
                </tr>
                <?php endif; ?>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>


<?php $__currentLoopData = [
    ['key'=>'security',    'label'=>'Security Fee',    'icon'=>'🔒', 'color'=>'#7c3aed', 'bg'=>'#fdf4ff'],
    ['key'=>'development', 'label'=>'Development Fee', 'icon'=>'🏗️', 'color'=>'#16a34a', 'bg'=>'#f0fdf4'],
    ['key'=>'registry',    'label'=>'Registry Fee',    'icon'=>'📋', 'color'=>'#0369a1', 'bg'=>'#e0f2fe'],
    ['key'=>'transfer',    'label'=>'Transfer Fee',    'icon'=>'🔄', 'color'=>'#0891b2', 'bg'=>'#ecfeff'],
]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $rows = $feePaymentRows[$src['key']] ?? collect();
?>
<div id="src-panel-<?php echo e($src['key']); ?>_fee" class="main-card" style="margin-bottom:18px;display:none;">
    <div class="card-toolbar" style="background:linear-gradient(135deg,<?php echo e($src['bg']); ?>,#fff);">
        <div>
            <p class="card-title" style="color:<?php echo e($src['color']); ?>;"><?php echo e($src['icon']); ?> <?php echo e($src['label']); ?> <span style="font-weight:400;font-size:11px;color:#64748b;">— all payment records</span></p>
            <p style="font-size:11px;color:var(--slate);margin:2px 0 0;"><?php echo e($rows->count()); ?> records · PKR <?php echo e(number_format($rows->sum('amount'))); ?> total</p>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="exp-table">
            <thead>
                <tr>
                    <th style="width:36px;text-align:center;">#</th>
                    <th>Date Paid</th>
                    <th>Customer</th>
                    <th>Plot</th>
                    <th>Booking Ref</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Receipt</th>
                    <th style="width:60px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="text-align:center;font-size:11px;color:#94a3b8;"><?php echo e($loop->iteration); ?></td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:#0f172a;"><?php echo e(\Carbon\Carbon::parse($fp->paid_date)->format('d M Y')); ?></div>
                        <div style="font-size:10px;color:#94a3b8;"><?php echo e(\Carbon\Carbon::parse($fp->paid_date)->diffForHumans()); ?></div>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:#0f172a;"><?php echo e($fp->booking->customer->name ?? '—'); ?></div>
                        <div style="font-size:10px;color:#94a3b8;"><?php echo e($fp->booking->customer->cnic ?? ''); ?></div>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:#0f172a;">Plot #<?php echo e($fp->booking->plot->plot_number ?? '—'); ?></div>
                        <div style="font-size:10px;color:#94a3b8;"><?php echo e($fp->booking->plot->block ?? ''); ?> <?php echo e($fp->booking->plot->street_number ? '· St '.$fp->booking->plot->street_number : ''); ?></div>
                    </td>
                    <td>
                        <span style="font-family:monospace;font-size:11px;color:<?php echo e($src['color']); ?>;"><?php echo e($fp->booking->customer_booking_id ?? '—'); ?></span>
                        <?php if(($fp->booking->status ?? '') === 'cancelled'): ?>
                        <div style="margin-top:3px;">
                            <span style="font-size:9px;font-weight:800;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:2px 7px;border-radius:20px;display:inline-block;">
                                ⚠️ Booking Cancelled
                            </span>
                            <div style="font-size:9px;color:#94a3b8;margin-top:1px;">Cash received — still on record</div>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td><strong style="color:<?php echo e($src['color']); ?>;font-size:14px;">PKR <?php echo e(number_format($fp->amount)); ?></strong></td>
                    <td><span style="font-size:11px;font-weight:600;background:#f1f5f9;color:#64748b;padding:3px 8px;border-radius:20px;"><?php echo e($fp->payment_mode ?? '—'); ?></span></td>
                    <td style="font-size:11px;color:#64748b;font-family:monospace;"><?php echo e($fp->receipt_no ?? '—'); ?></td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex;gap:5px;align-items:center;">
                            <a href="<?php echo e(route('fee.receipt', $fp->id)); ?>" target="_blank" style="width:28px;height:28px;border-radius:7px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;" title="Print Receipt"><i class="bi bi-printer-fill"></i></a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fee_management_pay')): ?>
                            <form action="<?php echo e(route('fee.payment.destroy', $fp->id)); ?>" method="POST" onsubmit="return confirm('Delete this fee payment record? This cannot be undone.')" style="margin:0;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="width:28px;height:28px;border-radius:7px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="9"><div class="empty-state"><i class="bi bi-inbox"></i><p>No <?php echo e(strtolower($src['label'])); ?> payments recorded yet.</p></div></td></tr>
                <?php endif; ?>
            </tbody>
            <?php if($rows->count() > 0): ?>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right;font-size:10px;font-weight:700;color:#64748b;letter-spacing:.5px;">TOTAL COLLECTED</td>
                    <td style="font-size:15px;font-weight:800;color:<?php echo e($src['color']); ?>;">PKR <?php echo e(number_format($rows->sum('amount'))); ?></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<div id="src-panel-misc_income" class="main-card" style="margin-bottom:18px;display:none;">
    <div class="card-toolbar" style="background:linear-gradient(135deg,#fffbeb,#fff);">
        <div>
            <p class="card-title" style="color:#d97706;">💰 Misc. Income <span style="font-weight:400;font-size:11px;color:#64748b;">— all office income records (rent, tube well, etc.)</span></p>
            <p style="font-size:11px;color:var(--slate);margin:2px 0 0;"><?php echo e($incomes->count()); ?> records · PKR <?php echo e(number_format($incomes->sum('amount'))); ?> total</p>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="exp-table">
            <thead>
                <tr>
                    <th style="width:36px;text-align:center;">#</th>
                    <th>Date</th><th>Category</th><th>Received From / Party</th>
                    <th>Amount</th><th>Method</th><th>Ref #</th><th>Status</th>
                    <th style="text-align:center;width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="text-align:center;font-size:11px;color:#94a3b8;"><?php echo e($loop->iteration); ?></td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:#0f172a;"><?php echo e(\Carbon\Carbon::parse($inc->expense_date)->format('d M Y')); ?></div>
                        <div style="font-size:10px;color:#94a3b8;"><?php echo e(\Carbon\Carbon::parse($inc->expense_date)->diffForHumans()); ?></div>
                    </td>
                    <td><span class="cat-pill cat-<?php echo e(strtolower(str_replace(' ','_',$inc->category))); ?>"><?php echo e($inc->category); ?></span></td>
                    <td>
                        <div style="font-size:12px;font-weight:700;color:#0f172a;"><?php echo e($inc->paid_to ?? '—'); ?></div>
                        <?php if($inc->remarks): ?><div style="font-size:10px;color:#94a3b8;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($inc->remarks); ?></div><?php endif; ?>
                    </td>
                    <td><strong style="color:#d97706;font-size:14px;">PKR <?php echo e(number_format($inc->amount)); ?></strong></td>
                    <td><span style="font-size:11px;font-weight:600;background:#f1f5f9;color:#64748b;padding:3px 8px;border-radius:20px;"><?php echo e($inc->payment_method ?? '—'); ?></span></td>
                    <td style="font-size:11px;color:#64748b;font-family:monospace;"><?php echo e($inc->reference_no ?? '—'); ?></td>
                    <td>
                        <span class="status-pill <?php echo e($inc->status === 'approved' ? 'status-approved' : 'status-pending'); ?>">
                            <span class="status-dot"></span><?php echo e(ucfirst($inc->status)); ?>

                        </span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex;gap:5px;align-items:center;">
                            <a href="<?php echo e(route('expense.detail.view', $inc->id)); ?>" style="width:28px;height:28px;border-radius:7px;background:#f0fdf4;border:1px solid #86efac;color:#16a34a;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;" title="View"><i class="bi bi-eye-fill"></i></a>
                            <a href="<?php echo e(route('expense.detail.pdf', $inc->id)); ?>" target="_blank" style="width:28px;height:28px;border-radius:7px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;" title="Print PDF"><i class="bi bi-printer-fill"></i></a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_edit')): ?>
                            <a href="<?php echo e(route('expense.edit.view', $inc->id)); ?>" style="width:28px;height:28px;border-radius:7px;background:#f8fafc;border:1px solid #e2e8f0;color:#64748b;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expense_delete')): ?>
                            <form action="<?php echo e(route('expenses.destroy', $inc->id)); ?>" method="POST" onsubmit="return confirm('Delete this income record?')" style="margin:0;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="width:28px;height:28px;border-radius:7px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="9"><div class="empty-state"><i class="bi bi-inbox"></i><p>No income records yet.</p></div></td></tr>
                <?php endif; ?>
            </tbody>
            <?php if($incomes->count() > 0): ?>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;font-size:10px;font-weight:700;color:#64748b;letter-spacing:.5px;">TOTAL COLLECTED</td>
                    <td style="font-size:15px;font-weight:800;color:#d97706;">PKR <?php echo e(number_format($incomes->sum('amount'))); ?></td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Section 1: Expenses / Income / Inventory / All
function switchMainTab(tab) {
    ['expense','income','inventory','all'].forEach(function(t) {
        var panel = document.getElementById('panel-' + t);
        if (panel) panel.style.display = (t === tab) ? 'block' : 'none';
        var btn = document.getElementById('tab-' + t);
        if (btn) {
            btn.className = 'tab-btn';
            if (t === tab) {
                if (t === 'income') btn.classList.add('active-income');
                else if (t === 'inventory') btn.classList.add('active-inventory');
                else btn.classList.add('active-expense');
            }
        }
    });
}

// Section 2: Income sources (plot, fees, misc)
var srcKeys = ['plot_payments','security_fee','development_fee','registry_fee','transfer_fee','misc_income'];
function switchSourceTab(tab) {
    srcKeys.forEach(function(t) {
        // panel id pattern: src-panel-<t>  (fee panels use <key>_fee except plot_payments & misc_income)
        var panelId = 'src-panel-' + t;
        var panel = document.getElementById(panelId);
        if (panel) panel.style.display = (t === tab) ? 'block' : 'none';
        var btn = document.getElementById('src-tab-' + t);
        if (btn) {
            btn.className = 'tab-btn';
            if (t === tab) btn.classList.add('active-income');
        }
    });
}

// Init source section: show Plot Payments by default
document.addEventListener('DOMContentLoaded', function() {
    switchSourceTab('plot_payments');
});

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.index', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>