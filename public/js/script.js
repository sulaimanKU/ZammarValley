(function () {
  'use strict';

  var sidebar  = document.getElementById('mainSb');
  var toggle   = document.getElementById('sbToggle');
  var closeBtn = document.getElementById('sbClose');
  var overlay  = document.getElementById('sbOverlay');

  var isMobile = function () { return window.innerWidth <= 768; };

  /* ── Set up overlay styles ── */
  overlay.style.cssText = [
    'position:fixed', 'inset:0', 'z-index:1100',
    'background:rgba(0,0,0,.45)',
    'backdrop-filter:blur(2px)', '-webkit-backdrop-filter:blur(2px)',
    'display:none', 'opacity:0',
    'transition:opacity .28s ease',
    'cursor:pointer'
  ].join(';');

  /* ── Restore desktop collapsed state ── */
  if (!isMobile() && localStorage.getItem('sb-state') === 'collapsed') {
    sidebar.classList.add('collapsed');
  }

  /* ── Open mobile sidebar ── */
function openMobile() {
  sidebar.classList.add('mob-open');
  overlay.classList.add('show');
}

function closeMobile() {
  sidebar.classList.remove('mob-open');
  overlay.classList.remove('show');
}
  /* ── Hamburger toggle ── */
  toggle.addEventListener('click', function () {
    if (isMobile()) {
      openMobile();
    } else {
      var collapsed = sidebar.classList.toggle('collapsed');
      localStorage.setItem('sb-state', collapsed ? 'collapsed' : 'expanded');
    }
  });

  /* ── X close button ── */
  closeBtn.addEventListener('click', closeMobile);

  /* ── Overlay click closes sidebar ── */
  overlay.addEventListener('click', closeMobile);

  /* ── Collapsed desktop: expand first, then open sub-menu ── */
/* ── If sidebar is collapsed, expand it before toggling submenu ── */
sidebar.querySelectorAll('.sb-link[data-bs-toggle="collapse"]').forEach(function (link) {

  link.addEventListener('click', function () {

    if (!isMobile() && sidebar.classList.contains('collapsed')) {
      sidebar.classList.remove('collapsed');
      localStorage.setItem('sb-state', 'expanded');
    }

  });

});

  /* ── Active link on click (close mobile on nav) ── */
  sidebar.querySelectorAll('.sb-link, .sb-sub-link').forEach(function (el) {
    el.addEventListener('click', function () {
      if (this.tagName === 'A') {
        sidebar.querySelectorAll('.sb-link.active, .sb-sub-link.active')
          .forEach(function (a) { a.classList.remove('active'); });
        this.classList.add('active');
        if (isMobile()) closeMobile();
      }
    });
  });

  /* ── Profile dropdown: close on outside click ── */
  document.addEventListener('click', function (e) {
    var dd = document.getElementById('pfToggle');
    if (dd && !e.target.closest('.pf-wrap')) dd.checked = false;
  });

  /* ── Window resize: sync state ── */
  var resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      if (!isMobile()) {
        sidebar.classList.remove('open');
        overlay.style.display = 'none';
        overlay.style.opacity = '0';
        if (localStorage.getItem('sb-state') === 'collapsed') {
          sidebar.classList.add('collapsed');
        } else {
          sidebar.classList.remove('collapsed');
        }
      } else {
        sidebar.classList.remove('collapsed');
      }
    }, 150);
  });

})();
//------------------ sidebar-toggle-logic end-----------------------------------------//
// -------------------Pie Chart Logic--------------------------------------------------
      document.addEventListener('DOMContentLoaded', function () {
        const shared = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };

        new Chart(document.getElementById('mainChart'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                datasets: [{ data: [30, 50, 40, 70, 60], borderColor: '#6a82fb', tension: 0.4, fill: true, backgroundColor: 'rgba(106, 130, 251, 0.05)' }]
            },
            options: shared
        });

        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                datasets: [{ data: [70, 20, 10], backgroundColor: ['#6a82fb', '#f59e0b', '#10b981'], borderWidth: 0, cutout: '80%' }]
            },
            options: shared
        });
    });


    // This ensures the script waits for the page to be ready
    document.addEventListener('DOMContentLoaded', function() {

        // 1. Grab the elements
        const priceInput = document.getElementById('total_price');
        const tokenInput = document.getElementById('token_amount');
        const installInput = document.getElementById('total_installments');
        const monthlyOutput = document.getElementById('monthly_installment');


        function calculate() {

            let total = parseFloat(priceInput.value) || 0;
            let token = parseFloat(tokenInput.value) || 0;
            let months = parseInt(installInput.value) || 0;




            let balance = total - token;

            if (months > 0 && balance > 0) {
                let monthly = balance / months;
                monthlyOutput.value = Math.round(monthly);
            } else {
                monthlyOutput.value = 0;
            }
        }


        if(priceInput && tokenInput && installInput) {
            priceInput.addEventListener('input', calculate);
            tokenInput.addEventListener('input', calculate);
            installInput.addEventListener('input', calculate);
            console.log("Listeners attached successfully!");
        } else {
            console.error("One or more IDs (total_price, token_amount, total_installments) not found in HTML!");
        }
    });



document.addEventListener('DOMContentLoaded', function () {

    // ── Tab switching ──
    document.querySelectorAll('.cfg-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.cfg-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.cfg-section').forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(this.dataset.target).classList.add('active');
        });
    });

    // ── Profile image preview ──
    const upload = document.getElementById('uploadImage');
    if (upload) {
        upload.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => document.getElementById('profilePreview').src = e.target.result;
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // ── Edit Role modal ──
    document.querySelectorAll('.editRoleBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const roleId      = this.dataset.id;
            const roleName    = this.dataset.name;
            const permissions = JSON.parse(this.dataset.permissions);

            document.getElementById('role_name').value = roleName;

            let url = "{{ route('RolePermission.update', ':id') }}".replace(':id', roleId);
            document.getElementById('editRoleForm').action = url;

            document.querySelectorAll('.permissionCheckbox').forEach(cb => cb.checked = false);
            permissions.forEach(id => {
                const cb = document.getElementById('perm' + id);
                if (cb) cb.checked = true;
            });
        });
    });

});


function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const prev = document.getElementById('logoPreview');
            if (prev.tagName === 'IMG') {
                prev.src = e.target.result;
            } else {
                // Replace div with img
                const img = document.createElement('img');
                img.id = 'logoPreview';
                img.src = e.target.result;
                img.style.cssText = 'width:80px;height:80px;border-radius:14px;object-fit:contain;border:2px solid #e8edf3;padding:4px;background:#fff;';
                prev.replaceWith(img);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}





// finace and dashboard js



/* ─────────────────────────────────────────────
   DASHBOARD CHARTS
   Requires window.ZV_DASH to be set in blade:
   {
     mlJson, mcJson,
     psLabels, psVals,
     catLabels, catVals, catColors
   }
───────────────────────────────────────────── */
function initDashboardCharts() {
    const d = window.ZV_DASH;
    if (!d) return;

    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#64748b';

    // ── Monthly Bar Chart ──────────────────────
    const barEl = document.getElementById('barChart');
    if (barEl) {
        const barCtx  = barEl.getContext('2d');
        const barGrad = barCtx.createLinearGradient(0, 0, 0, 260);
        barGrad.addColorStop(0, 'rgba(30,58,138,.8)');
        barGrad.addColorStop(1, 'rgba(59,130,246,.15)');

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: d.mlJson,
                datasets: [{
                    label: 'PKR Collected',
                    data: d.mcJson,
                    backgroundColor: barGrad,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' PKR ' + (ctx.raw / 1000000).toFixed(2) + 'M'
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: {
                        grid: { color: '#f1f5f9' },
                        border: { display: false },
                        ticks: { callback: v => 'PKR ' + (v / 1000000).toFixed(1) + 'M' }
                    }
                }
            }
        });
    }

    // ── Plot Status Doughnut ───────────────────
    const plotEl = document.getElementById('plotDoughnut');
    if (plotEl) {
        new Chart(plotEl, {
            type: 'doughnut',
            data: {
                labels: d.psLabels,
                datasets: [{
                    data: d.psVals,
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                cutout: '68%',
                plugins: { legend: { display: false } },
                responsive: false
            }
        });
    }

    // ── Payment Category Doughnut ──────────────
    const catEl = document.getElementById('catDoughnut');
    if (catEl) {
        new Chart(catEl, {
            type: 'doughnut',
            data: {
                labels: d.catLabels,
                datasets: [{
                    data: d.catVals,
                    backgroundColor: d.catColors,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' PKR ' + (ctx.raw / 1000000).toFixed(2) + 'M'
                        }
                    }
                },
                responsive: false
            }
        });
    }
}

/* ─────────────────────────────────────────────
   FINANCE REPORT CHARTS + EXPORT
   Requires window.ZV_FINANCE to be set in blade:
   {
     monthLabels, monthData,
     catLabels, catData, catColors,
     summaryData, fileName
   }
───────────────────────────────────────────── */
function initFinanceCharts() {
    const f = window.ZV_FINANCE;
    if (!f) return;

    // ── Monthly Income Bar Chart ───────────────
    const monthEl = document.getElementById('monthlyChart');
    if (monthEl) {
        const maxVal = Math.max(...f.monthData);

        new Chart(monthEl, {
            type: 'bar',
            data: {
                labels: f.monthLabels,
                datasets: [{
                    label: 'Collections (PKR)',
                    data: f.monthData,
                    backgroundColor: f.monthData.map(v =>
                        v > 0 && v === maxVal ? '#1e3a8a' : 'rgba(30,58,138,.18)'
                    ),
                    borderRadius: 7,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' PKR ' + ctx.raw.toLocaleString()
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 10 },
                            color: '#94a3b8',
                            callback: v => v >= 1000000
                                ? (v / 1000000).toFixed(1) + 'M'
                                : v >= 1000
                                    ? (v / 1000).toFixed(0) + 'K'
                                    : v
                        }
                    }
                }
            }
        });
    }

    // ── Category Donut Chart ───────────────────
    const catEl = document.getElementById('catDonut');
    if (catEl && f.catData.length > 0) {
        new Chart(catEl, {
            type: 'doughnut',
            data: {
                labels: f.catLabels,
                datasets: [{
                    data: f.catData,
                    backgroundColor: f.catColors,
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' PKR ' + ctx.raw.toLocaleString()
                        }
                    }
                }
            }
        });
    }
}

/* ─────────────────────────────────────────────
   FINANCE REPORT — EXPORT TO EXCEL
   Called by onclick="exportToExcel()" in blade
───────────────────────────────────────────── */
function exportToExcel() {
    const f   = window.ZV_FINANCE;
    const btn = document.getElementById('exportExcelBtn');
    btn.innerHTML = '⏳ Generating...';
    btn.disabled  = true;

    setTimeout(() => {
        const table = document.getElementById('mainTable');
        const wb    = XLSX.utils.book_new();

        // Sheet 1 — Transactions
        const ws1   = XLSX.utils.table_to_sheet(table);
        const range = XLSX.utils.decode_range(ws1['!ref']);
        for (let C = range.s.c; C <= range.e.c; C++) {
            const cell = ws1[XLSX.utils.encode_cell({ r: 0, c: C })];
            if (cell) cell.s = { font: { bold: true }, fill: { fgColor: { rgb: '1E3A8A' } } };
        }
        XLSX.utils.book_append_sheet(wb, ws1, 'Transactions');

        // Sheet 2 — Summary
        const ws2 = XLSX.utils.aoa_to_sheet(f.summaryData);
        ws2['!cols'] = [{ wch: 32 }, { wch: 22 }];
        XLSX.utils.book_append_sheet(wb, ws2, 'Summary');

        XLSX.writeFile(wb, f.fileName);

        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" style="width:15px;height:15px;">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg> Export Excel`;
        btn.disabled = false;
    }, 100);
}

/* ─────────────────────────────────────────────
   AUTO-INIT on DOM ready
───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    initDashboardCharts();
    initFinanceCharts();
});





// end here finace and dashbaird


const toggleBtn = document.getElementById('theme-toggle');
    const sunIcon = document.getElementById('theme-icon-light');
    const moonIcon = document.getElementById('theme-icon-dark');
    const htmlElement = document.documentElement;

    // 1. Check for saved theme in LocalStorage on page load
    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    toggleBtn.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    });

    function applyTheme(theme) {
        // Set the Bootstrap 5.3+ attribute
        htmlElement.setAttribute('data-bs-theme', theme);

        // Toggle the visibility of the icons
        if (theme === 'dark') {
            sunIcon.classList.add('d-none');
            moonIcon.classList.remove('d-none');
        } else {
            sunIcon.classList.remove('d-none');
            moonIcon.classList.add('d-none');
        }
    }
