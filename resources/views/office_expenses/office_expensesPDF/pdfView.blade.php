<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ZV-Expenses-{{ $from ?? 'Report' }}</title>
    <style>
        @page { margin: 0.3in 0.4in; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #334155;
            line-height: 1.5;
            margin: 0; padding: 0; background: #fff;
        }

        /* ── Watermark ── */
        .watermark {
            position: fixed; top: 40%; left: 15%;
            font-size: 60px; color: rgba(226, 232, 240, 0.4);
            transform: rotate(-45deg); z-index: -1000;
            font-weight: bold; text-transform: uppercase;
        }

        /* ── Header Design ── */
        .header-wrap {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-table { width: 100%; }
        .brand-name { font-size: 24px; font-weight: bold; color: #1e3a8a; letter-spacing: -0.5px; }
        .brand-sub { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .doc-type-badge {
            background: #1e3a8a; color: #fff;
            padding: 5px 15px; border-radius: 4px;
            font-size: 11px; font-weight: bold;
            display: inline-block; text-transform: uppercase;
        }

        /* ── Info Bar ── */
        .info-grid {
            width: 100%; border-collapse: collapse; margin-bottom: 20px;
        }
        .info-card {
            background: #f8fafc; border: 1px solid #e2e8f0;
            padding: 10px; border-radius: 4px;
        }
        .label { font-size: 7px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; }
        .value { font-size: 12px; font-weight: bold; color: #0f172a; }

        /* ── Table Styling ── */
        .exp-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .exp-table th {
            background: #f1f5f9; color: #475569;
            text-align: left; padding: 8px 10px;
            font-size: 8px; text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }
        .exp-table td {
            padding: 8px 10px; border-bottom: 1px solid #f1f5f9;
            font-size: 9px; vertical-align: top;
        }
        .text-right { text-align: right; }

        /* ── Badges ── */
        .badge {
            padding: 2px 6px; border-radius: 10px; font-size: 7px; font-weight: bold; text-transform: uppercase;
        }
        .bg-success { background: #dcfce7; color: #15803d; }
        .bg-warning { background: #fef9c3; color: #854d0e; }

        /* ── Summary & Signatures ── */
        .summary-section { margin-top: 30px; width: 100%; }
        .summary-table { width: 250px; float: right; border-collapse: collapse; }
        .summary-table td { padding: 5px; font-size: 10px; }
        .total-row { background: #1e3a8a; color: white; font-weight: bold; }

        .signature-section { margin-top: 60px; width: 100%; clear: both; }
        .sig-box { text-align: center; border-top: 1px solid #cbd5e1; width: 150px; padding-top: 5px; }

        .clear { clear: both; }

        /* ── Fund Source Strip ── */
        .fund-strip { border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; border-left: 4px solid; }
        .fund-strip .fs-title { font-size: 7px; font-weight: bold; text-transform: uppercase; letter-spacing: .7px; margin-bottom: 3px; opacity: .7; }
        .fund-strip .fs-name  { font-size: 13px; font-weight: bold; }
        .fund-strip .fs-stats { display: table; width: 100%; margin-top: 8px; border-collapse: collapse; }
        .fund-strip .fs-stat  { display: table-cell; text-align: center; padding: 5px 8px; border-radius: 4px; }
        .fund-strip .fs-stat .sv { font-size: 11px; font-weight: bold; }
        .fund-strip .fs-stat .sl { font-size: 7px; text-transform: uppercase; letter-spacing: .5px; margin-top: 2px; opacity: .65; }
    </style>
</head>
<body>

<div class="watermark">OFFICIAL COPY</div>

<div class="header-wrap">
    <table class="header-table">
        <tr>
            <td>
                <div class="brand-name">ZAMAR VALLEY</div>
                <div class="brand-sub">Real Estate Development & ERP</div>
            </td>
            <td style="text-align: right;">
                <div class="doc-type-badge">Expense Report</div>
                <div style="margin-top: 5px; color: #64748b;">Ref: EXP/{{ date('Y/m') }}/{{ rand(100,999) }}</div>
            </td>
        </tr>
    </table>
</div>

<table class="info-grid" cellspacing="5">
    <tr>
        <td width="25%">
            <div class="info-card">
                <div class="label">Date Generated</div>
                <div class="value">{{ date('d M, Y') }}</div>
            </div>
        </td>
        <td width="25%">
            <div class="info-card">
                <div class="label">Report Period</div>
                <div class="value">{{ $from ?? 'Start' }} - {{ $to ?? 'End' }}</div>
            </div>
        </td>
        <td width="25%">
            <div class="info-card">
                <div class="label">Total Amount</div>
                <div class="value" style="color: #b91c1c;">PKR {{ number_format($total) }}</div>
            </div>
        </td>
        <td width="25%" style="text-align: center;">
            @if($qrCode)
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="50" height="50" style="border: 1px solid #e2e8f0; padding: 2px;">
                <div style="font-size: 6px; color: #94a3b8;">SECURE VERIFIED</div>
            @endif
        </td>
    </tr>
</table>

@php
    $fsMeta = [
        'plot_payments'   => ['label'=>'Plot Payments',   'icon'=>'Plot Pmts','color'=>'#1d4ed8','bg'=>'#eff6ff','border'=>'#1d4ed8'],
        'security_fee'    => ['label'=>'Security Fee',    'icon'=>'Security', 'color'=>'#7c3aed','bg'=>'#fdf4ff','border'=>'#7c3aed'],
        'registry_fee'    => ['label'=>'Registry Fee',    'icon'=>'Registry', 'color'=>'#0369a1','bg'=>'#e0f2fe','border'=>'#0369a1'],
        'development_fee' => ['label'=>'Development Fee', 'icon'=>'Dev. Fee', 'color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#16a34a'],
    ];
    // For single-expense PDF, grab fund source from first expense
    $singleExpense   = $expenses->first();
    $fundKey         = $singleExpense->fund_source ?? null;
    $fsData          = $fundKey ? ($fsMeta[$fundKey] ?? null) : null;
    if ($fsData) {
        $usedFromSource = \App\Models\OfficeExpense::where('type','expense')->where('status','approved')->where('fund_source',$fundKey)->sum('amount');
        $feeTypeMap     = ['plot_payments'=>null,'security_fee'=>'security','registry_fee'=>'registry','development_fee'=>'development'];
        $feeType        = $feeTypeMap[$fundKey] ?? null;
        if ($fundKey === 'plot_payments') {
            $validIds        = \App\Models\Booking::where('status','!=','cancelled')->pluck('id');
            $totalCollected  = \App\Models\PlotPayment::whereIn('booking_id',$validIds)->where('status','paid')->sum('amount_paid');
        } elseif($feeType) {
            $totalCollected  = \App\Models\BookingFee::where('fee_type',$feeType)->sum('paid_amount');
        } else {
            $totalCollected  = 0;
        }
        $fundRemaining = max(0, $totalCollected - $usedFromSource);
    }
@endphp

@if($fsData)
<div class="fund-strip" style="background:{{ $fsData['bg'] }};border-left-color:{{ $fsData['border'] }};color:{{ $fsData['color'] }};">
    <div class="fs-title">Fund Source</div>
    <div class="fs-name">{{ $fsData['label'] }}</div>
    <table class="fs-stats" style="margin-top:8px;">
        <tr>
            <td style="background:rgba(255,255,255,.7);border-radius:4px;padding:5px 8px;text-align:center;">
                <div class="sv" style="color:#15803d;">PKR {{ number_format($totalCollected) }}</div>
                <div class="sl">Collected</div>
            </td>
            <td width="8"></td>
            <td style="background:rgba(255,255,255,.7);border-radius:4px;padding:5px 8px;text-align:center;">
                <div class="sv" style="color:#dc2626;">PKR {{ number_format($usedFromSource) }}</div>
                <div class="sl">Used in Expenses</div>
            </td>
            <td width="8"></td>
            <td style="background:rgba(255,255,255,.7);border-radius:4px;padding:5px 8px;text-align:center;">
                <div class="sv" style="color:{{ $fsData['color'] }};">PKR {{ number_format($fundRemaining) }}</div>
                <div class="sl">Remaining</div>
            </td>
        </tr>
    </table>
</div>
@endif

<table class="exp-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="12%">Date</th>
            <th width="15%">Category</th>
            <th width="12%">Fund Source</th>
            <th>Paid To / Remarks</th>
            <th width="12%">Method</th>
            <th width="10%">Status</th>
            <th width="13%" class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $i => $expense)
        @php
            $rowFs = $expense->fund_source ? ($fsMeta[$expense->fund_source] ?? null) : null;
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y') }}</td>
            <td><strong>{{ strtoupper($expense->category) }}</strong></td>
            <td>
                @if($rowFs)
                    <span style="font-size:7px;font-weight:bold;padding:2px 6px;border-radius:10px;background:{{ $rowFs['bg'] }};color:{{ $rowFs['color'] }};">{{ $rowFs['label'] }}</span>
                @else
                    <span style="color:#94a3b8;font-size:8px;">—</span>
                @endif
            </td>
            <td>
                {{ $expense->paid_to }}
                @if($expense->remarks)
                    <div style="color: #64748b; font-size: 8px; margin-top: 2px;">{{ $expense->remarks }}</div>
                @endif
            </td>
            <td>{{ $expense->payment_method }}</td>
            <td>
                <span class="badge {{ $expense->status === 'approved' ? 'bg-success' : 'bg-warning' }}">
                    {{ $expense->status }}
                </span>
            </td>
            <td class="text-right"><strong>{{ number_format($expense->amount) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="summary-section">
    <table class="summary-table">
        <tr>
            <td>Approved Total:</td>
            <td class="text-right">PKR {{ number_format($expenses->where('status','approved')->sum('amount')) }}</td>
        </tr>
        <tr>
            <td>Pending Total:</td>
            <td class="text-right">PKR {{ number_format($expenses->where('status','pending')->sum('amount')) }}</td>
        </tr>
        <tr class="total-row">
            <td>GRAND TOTAL:</td>
            <td class="text-right">PKR {{ number_format($total) }}</td>
        </tr>
    </table>
    <div class="clear"></div>
</div>

<div class="signature-section">
    <table width="100%">
        <tr>
            <td width="33%">
                <div class="sig-box" style="margin: 0 auto;">Prepared By</div>
            </td>
            <td width="33%" style="text-align: center;">
                <div style="width: 70px; height: 70px; border: 2px dashed #cbd5e1; border-radius: 50%; margin: -20px auto 0; padding-top: 25px; color: #cbd5e1; font-weight: bold;">STAMP</div>
            </td>
            <td width="33%">
                <div class="sig-box" style="margin: 0 auto;">Authorized By</div>
            </td>
        </tr>
    </table>
</div>

<div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 7px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 5px;">
    This is a system-generated document and does not require a physical signature for digital verification. Printed on {{ date('d-M-Y H:i:s') }}
</div>

</body>
</html>
