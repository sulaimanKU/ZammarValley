<?php

namespace App\Http\Controllers;

use App\Models\OfficeExpense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\OfficeExpensesExport;
use App\Helpers\AppConfig;
use App\Models\Booking;
use App\Models\BookingFee;
use App\Models\FeePayment;
use App\Models\PlotPayment;
use App\Models\PlotTransfer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
class OfficeExpensesController extends Controller
{
    use \App\Traits\HasSocietyConfig;
public function index()
{
    // Expenses only
    $expenses = OfficeExpense::where('type', 'expense')
        ->orderBy('expense_date', 'desc')
        ->get();

    // Income only
    $incomes = OfficeExpense::where('type', 'income')
        ->orderBy('expense_date', 'desc')
        ->get();

    // ✅ New: Inventory only
    $inventories = OfficeExpense::where('type', 'inventory')
        ->orderBy('expense_date', 'desc')
        ->get();

    // Stats — all filtered to approved only for accuracy
    $total_expenses = OfficeExpense::where('type', 'expense')
        ->where('status', 'approved')
        ->whereMonth('expense_date', now()->month)
        ->whereYear('expense_date', now()->year)
        ->sum('amount');

    $total = OfficeExpense::where('type', 'expense')
        ->where('status', 'approved')
        ->sum('amount');

    $total_income = OfficeExpense::where('type', 'income')
        ->where('status', 'approved')
        ->sum('amount');

    $total_income_month = OfficeExpense::where('type', 'income')
        ->where('status', 'approved')
        ->whereMonth('expense_date', now()->month)
        ->whereYear('expense_date', now()->year)
        ->sum('amount');

    $total_inventory = OfficeExpense::where('type', 'inventory')
        ->where('status', 'approved')
        ->sum('amount');

    $total_inventory_month = OfficeExpense::where('type', 'inventory')
        ->where('status', 'approved')
        ->whereMonth('expense_date', now()->month)
        ->whereYear('expense_date', now()->year)
        ->sum('amount');

    // Net balance: approved income − approved expenses − approved inventory
    $net_balance = $total_income - $total - $total_inventory;

    // ── Cancellation refunds (stored on bookings) ──
    $cancelledRefundTotal = Booking::where('status', 'cancelled')
        ->whereNotNull('cancellation_refund')
        ->where('cancellation_refund', '>', 0)
        ->whereHas('payments')
        ->sum('cancellation_refund');

    // ── Fund Source Totals ──
    $fundSources = [
        'plot_payments'   => ['label'=>'Plot Payments',   'icon'=>'🏘️', 'color'=>'#1d4ed8','bg'=>'#eff6ff',
            'collected'       => max(0, PlotPayment::where('status','paid')->sum('amount_paid') - $cancelledRefundTotal),
            'gross_collected' => PlotPayment::where('status','paid')->sum('amount_paid'),
            'refunded'        => $cancelledRefundTotal],
        'security_fee'    => ['label'=>'Security Fee',    'icon'=>'🔒', 'color'=>'#7c3aed','bg'=>'#fdf4ff',
            'collected' => FeePayment::whereHas('bookingFee', fn($q) => $q->where('fee_type','security'))->sum('amount')],
        'registry_fee'    => ['label'=>'Registry Fee',    'icon'=>'📋', 'color'=>'#0369a1','bg'=>'#e0f2fe',
            'collected' => FeePayment::whereHas('bookingFee', fn($q) => $q->where('fee_type','registry'))->sum('amount')],
        'development_fee' => ['label'=>'Development Fee', 'icon'=>'🏗️', 'color'=>'#16a34a','bg'=>'#f0fdf4',
            'collected' => FeePayment::whereHas('bookingFee', fn($q) => $q->where('fee_type','development'))->sum('amount')],
        'transfer_fee'    => ['label'=>'Transfer Fee',    'icon'=>'🔄', 'color'=>'#0891b2','bg'=>'#ecfeff',
            // Path 1: via FeeManagementController (FeePayment record, fee_paid_date NOT set on transfer)
            // Path 2: via TransferController direct (fee_paid_date IS set — no FeePayment record created)
            'collected' => FeePayment::whereHas('bookingFee', fn($q) => $q->where('fee_type','transfer'))->sum('amount')
                         + PlotTransfer::where('transfer_fee_status','paid')->whereNotNull('fee_paid_date')->where('transfer_fee','>',0)->sum('transfer_fee')],
        'misc_income'     => ['label'=>'Misc. Income',    'icon'=>'💰', 'color'=>'#d97706','bg'=>'#fffbeb',
            'collected' => OfficeExpense::where('type','income')->where('status','approved')->sum('amount')],
    ];

    foreach ($fundSources as $key => &$fs) {
        $fs['used']      = OfficeExpense::whereIn('type',['expense','inventory'])->where('status','approved')->where('fund_source',$key)->sum('amount');
        $fs['remaining'] = max(0, $fs['collected'] - $fs['used']);
    }
    unset($fs);

    // ── Detailed transaction rows for each income source ──
    $plotPaymentRows = PlotPayment::with(['booking.customer','booking.plot'])
        ->where('status','paid')
        ->orderByDesc('paid_date')
        ->get();

    // Pre-compute per-booking refund info so the view doesn't repeat the refund for each payment row
    $cancelledBookingRefunds = $plotPaymentRows
        ->filter(fn($pp) => ($pp->booking->status ?? '') === 'cancelled')
        ->unique('booking_id')
        ->mapWithKeys(fn($pp) => [
            $pp->booking_id => [
                'refund'     => (float)($pp->booking->cancellation_refund ?? 0),
                'total_paid' => (float)$plotPaymentRows->where('booking_id', $pp->booking_id)->sum('amount_paid'),
            ]
        ]);

    $feePaymentRows = FeePayment::with(['booking.customer','booking.plot','bookingFee'])
        ->orderByDesc('paid_date')
        ->get()
        ->groupBy(fn($fp) => $fp->bookingFee->fee_type ?? 'unknown');

    return view('office_expenses.officeExpensesView', compact(
        'expenses', 'incomes', 'inventories',
        'total_expenses', 'total', 'total_income', 'total_income_month',
        'total_inventory', 'total_inventory_month', 'net_balance',
        'fundSources', 'plotPaymentRows', 'cancelledBookingRefunds', 'feePaymentRows'
    ));
}
public function create(): \Illuminate\View\View
{
    return view('office_expenses.expense_create');
}

    public function store(Request $request): \Illuminate\Http\RedirectResponse
{
    $request->validate([
        'category'       => 'required|string',

        'expense_date'   => 'required|date',
        'amount'         => 'required|numeric|min:1',

        'type' => 'required|in:expense,income,inventory',
    ]);

    try {
        $fileName = null;

        if ($request->hasFile('payment_proof')) {
            $file     = $request->file('payment_proof');
            $fileName = 'payment_proof_'.time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('officeExpensesProof', $fileName, 'public');
        }

        $finalDate = \Carbon\Carbon::parse($request->expense_date)->setTimeFrom(now());

        OfficeExpense::create([
            'voucher_no'     => OfficeExpense::generateVoucherNo($request->type),
            'category'       => $request->category,
            'amount'         => $request->amount,
            'expense_date'   => $finalDate,
            'paid_to'        => $request->paid_to,
            'payment_method' => $request->payment_method,
            'reference_no'   => $request->reference_no,
            'payment_proof'  => $fileName,
            'status'         => $request->status ?? 'approved',
            'remarks'        => $request->remarks,
            'type'           => $request->type,
            'fund_source'    => in_array($request->type, ['expense','inventory']) ? ($request->fund_source ?: null) : null,
        ]);

        $label = $request->type === 'income' ? 'Income' : 'Expense';

        return redirect()->route('office_expenses.view')
            ->with('success', $label.' of PKR '.number_format($request->amount).' recorded successfully.');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}

    public function expenseDetail($id) {
        $expense = OfficeExpense::findOrFail($id);
    return view('office_expenses.expenses_show', compact('expense'));
    }

    public function expenseEditView($id)
    {
        $expense = OfficeExpense::findOrFail($id);
    return view('office_expenses.expenses_edit', compact('expense'));
    }
   public function update(Request $request, $id)
    {
        $expense = OfficeExpense::findOrFail($id);

        $request->validate([
            'category'       => 'required',
            'amount'         => 'required|numeric',
            'expense_date'   => 'required|date',
            'paid_to'        => 'required',
            'status'         => 'required|in:pending,approved,paid',
            'payment_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        try {
            // ── KEY FIX: only update proof if a NEW file was uploaded ──
            $fileName = $expense->payment_proof; // keep existing by default

            if ($request->hasFile('payment_proof')) {
                // Delete old file if exists
                if ($expense->payment_proof &&
                    Storage::disk('public')->exists('officeExpensesProof/' . $expense->payment_proof)) {
                    Storage::disk('public')->delete('officeExpensesProof/' . $expense->payment_proof);
                }
                $file     = $request->file('payment_proof');
                $fileName = 'payment_proof_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('officeExpensesProof', $fileName, 'public');
            }

            $expense->update([
                'category'       => $request->category,
                'amount'         => $request->amount,
                'expense_date'   => $request->expense_date,
                'paid_to'        => $request->paid_to,
                'payment_method' => $request->payment_method,
                'reference_no'   => $request->reference_no,
                'payment_proof'  => $fileName,
                'status'         => $request->status,
                'remarks'        => $request->remarks,
                'fund_source'    => in_array($expense->type, ['expense','inventory']) ? ($request->fund_source ?: null) : null,
            ]);

            return redirect()->route('office_expenses.view')->with('success', 'Expense updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


public function expenseDestroy($id){
    try{
        $expense = OfficeExpense::findOrFail($id);

        $fundSourceLabels = [
            'plot_payments'   => 'Plot Payments',
            'security_fee'    => 'Security Fee',
            'registry_fee'    => 'Registry Fee',
            'development_fee' => 'Development Fee',
            'transfer_fee'    => 'Transfer Fee',
            'misc_income'     => 'Misc. Income',
        ];
        $amount      = (float) $expense->amount;
        $fundSource  = $expense->fund_source;
        $fundLabel   = $fundSource ? ($fundSourceLabels[$fundSource] ?? $fundSource) : null;
        $voucherNo   = $expense->voucher_no ?? ('Voucher #' . $expense->id);

        if($expense->payment_proof){
            $filePath = 'officeExpensesProof/' . $expense->payment_proof;
            if(Storage::disk('public')->exists($filePath)){
                Storage::disk('public')->delete($filePath);
            }
        }
        $expense->delete();

        if ($fundLabel && in_array($expense->type, ['expense', 'inventory'])) {
            $msg = $voucherNo . ' deleted. PKR ' . number_format($amount) . ' has been freed back into the ' . $fundLabel . ' fund balance.';
        } else {
            $msg = $voucherNo . ' deleted successfully.';
        }

        return redirect()->route('office_expenses.view')->with('success', $msg);

    }catch(\Exception $exe){
        return redirect()->back()->with('error', $exe->getMessage());
    }
}
public function officeExpensesSsearch(Request $request)
{
    // ── Build filtered query ───────────────────────────────────────────
    $query = OfficeExpense::query()
        ->when($request->filled('from_date'),      fn($q) => $q->whereDate('expense_date', '>=', $request->from_date))
        ->when($request->filled('to_date'),        fn($q) => $q->whereDate('expense_date', '<=', $request->to_date))
        ->when($request->filled('category'),       fn($q) => $q->where('category', $request->category))
        ->when($request->filled('filter_type'),    fn($q) => $q->where('type', $request->filter_type))
        ->when($request->filled('status'),         fn($q) => $q->where('status', $request->status))
        ->when($request->filled('fund_source'),    fn($q) => $q->where('fund_source', $request->fund_source))
        ->when($request->filled('payment_method'), fn($q) => $q->where('payment_method', $request->payment_method))
        ->when($request->filled('keyword'),        fn($q) => $q->where(function($q2) use ($request) {
            $q2->where('paid_to',      'like', '%'.$request->keyword.'%')
               ->orWhere('remarks',    'like', '%'.$request->keyword.'%')
               ->orWhere('voucher_no', 'like', '%'.$request->keyword.'%')
               ->orWhere('reference_no','like','%'.$request->keyword.'%');
        }))
        ->when($request->filled('amount_from'),    fn($q) => $q->where('amount', '>=', $request->amount_from))
        ->when($request->filled('amount_to'),      fn($q) => $q->where('amount', '<=', $request->amount_to));

    $allResults  = $query->orderBy('expense_date', 'desc')->get();
    $expenses    = $allResults->where('type', 'expense')->values();
    $incomes     = $allResults->where('type', 'income')->values();
    $inventories = $allResults->where('type', 'inventory')->values();

    $approvedExpenses    = $expenses->where('status', 'approved');
    $approvedIncomes     = $incomes->where('status', 'approved');
    $approvedInventories = $inventories->where('status', 'approved');

    $total                 = $approvedExpenses->sum('amount');
    $total_expenses        = $approvedExpenses->filter(fn($e) => Carbon::parse($e->expense_date)->isCurrentMonth())->sum('amount');
    $total_income          = $approvedIncomes->sum('amount');
    $total_income_month    = $approvedIncomes->filter(fn($e) => Carbon::parse($e->expense_date)->isCurrentMonth())->sum('amount');
    $total_inventory       = $approvedInventories->sum('amount');
    $total_inventory_month = $approvedInventories->filter(fn($e) => Carbon::parse($e->expense_date)->isCurrentMonth())->sum('amount');
    $net_balance           = $total_income - $total - $total_inventory;

    $exportType = $request->input('export_type', 'search');

    // ── PDF Export ────────────────────────────────────────────────────
    if ($exportType === 'pdf') {
        $from  = $request->filled('from_date') ? Carbon::parse($request->from_date)->format('d M Y') : 'All';
        $to    = $request->filled('to_date')   ? Carbon::parse($request->to_date)->format('d M Y')   : 'All';
        $total = $allResults->where('status','approved')->sum('amount');

        $renderer = new ImageRenderer(new RendererStyle(60, 1), new SvgImageBackEnd());
        $qrCode   = base64_encode((new Writer($renderer))->writeString(url('/office/expenses')));

        $filterSummary = array_filter([
            $request->filled('from_date')      ? 'From: '.$from                   : null,
            $request->filled('to_date')        ? 'To: '.$to                       : null,
            $request->filled('category')       ? 'Category: '.$request->category  : null,
            $request->filled('filter_type')    ? 'Type: '.ucfirst($request->filter_type) : null,
            $request->filled('status')         ? 'Status: '.ucfirst($request->status)    : null,
            $request->filled('fund_source')    ? 'Fund: '.str_replace('_',' ',ucwords($request->fund_source,'_')) : null,
            $request->filled('payment_method') ? 'Method: '.$request->payment_method : null,
            $request->filled('keyword')        ? 'Keyword: "'.$request->keyword.'"'  : null,
            $request->filled('amount_from')    ? 'Amt ≥ '.number_format($request->amount_from) : null,
            $request->filled('amount_to')      ? 'Amt ≤ '.number_format($request->amount_to)   : null,
        ]);

        $pdf = Pdf::loadView('office_expenses.office_expensesPDF.searchPdf', [
            'expenses'       => $allResults,
            'total'          => $total,
            'from'           => $from,
            'to'             => $to,
            'qrCode'         => $qrCode,
            'filterSummary'  => implode('  ·  ', $filterSummary),
            'expenseTotal'   => $expenses->where('status','approved')->sum('amount'),
            'incomeTotal'    => $incomes->where('status','approved')->sum('amount'),
            'inventoryTotal' => $inventories->where('status','approved')->sum('amount'),
        ])->setPaper('a4', 'portrait')
          ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->stream('Expenses_Report_'.now()->format('d-m-Y').'.pdf');
    }

    // ── Excel Export ──────────────────────────────────────────────────
    if ($exportType === 'excel') {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\OfficeExpensesExport($allResults),
            'Expenses_Report_'.now()->format('d-m-Y').'.xlsx'
        );
    }

    // ── Search (HTML) — return filtered view ──────────────────────────
    $cancelledRefundTotal = Booking::where('status', 'cancelled')
        ->whereNotNull('cancellation_refund')->where('cancellation_refund', '>', 0)
        ->whereHas('payments')->sum('cancellation_refund');

    $fundSources = [
        'plot_payments'   => ['label'=>'Plot Payments',   'icon'=>'🏘️','color'=>'#1d4ed8','bg'=>'#eff6ff',
            'collected'       => max(0, PlotPayment::where('status','paid')->sum('amount_paid') - $cancelledRefundTotal),
            'gross_collected' => PlotPayment::where('status','paid')->sum('amount_paid'),
            'refunded'        => $cancelledRefundTotal],
        'security_fee'    => ['label'=>'Security Fee',    'icon'=>'🔒','color'=>'#7c3aed','bg'=>'#fdf4ff','collected'=>FeePayment::whereHas('bookingFee',fn($q)=>$q->where('fee_type','security'))->sum('amount')],
        'registry_fee'    => ['label'=>'Registry Fee',    'icon'=>'📋','color'=>'#0369a1','bg'=>'#e0f2fe','collected'=>FeePayment::whereHas('bookingFee',fn($q)=>$q->where('fee_type','registry'))->sum('amount')],
        'development_fee' => ['label'=>'Development Fee', 'icon'=>'🏗️','color'=>'#16a34a','bg'=>'#f0fdf4','collected'=>FeePayment::whereHas('bookingFee',fn($q)=>$q->where('fee_type','development'))->sum('amount')],
        'transfer_fee'    => ['label'=>'Transfer Fee',    'icon'=>'🔄','color'=>'#0891b2','bg'=>'#ecfeff',
            'collected' => FeePayment::whereHas('bookingFee',fn($q)=>$q->where('fee_type','transfer'))->sum('amount')
                         + PlotTransfer::where('transfer_fee_status','paid')->whereNotNull('fee_paid_date')->where('transfer_fee','>',0)->sum('transfer_fee')],
        'misc_income'     => ['label'=>'Misc. Income',    'icon'=>'💰','color'=>'#d97706','bg'=>'#fffbeb','collected'=>OfficeExpense::where('type','income')->where('status','approved')->sum('amount')],
    ];
    foreach ($fundSources as $key => &$fs) {
        $fs['used']      = OfficeExpense::whereIn('type',['expense','inventory'])->where('status','approved')->where('fund_source',$key)->sum('amount');
        $fs['remaining'] = max(0, $fs['collected'] - $fs['used']);
    }
    unset($fs);

    $plotPaymentRows = PlotPayment::with(['booking.customer','booking.plot'])
        ->where('status','paid')
        ->orderByDesc('paid_date')->get();

    $cancelledBookingRefunds = $plotPaymentRows
        ->filter(fn($pp) => ($pp->booking->status ?? '') === 'cancelled')
        ->unique('booking_id')
        ->mapWithKeys(fn($pp) => [
            $pp->booking_id => [
                'refund'     => (float)($pp->booking->cancellation_refund ?? 0),
                'total_paid' => (float)$plotPaymentRows->where('booking_id', $pp->booking_id)->sum('amount_paid'),
            ]
        ]);

    $feePaymentRows = FeePayment::with(['booking.customer','booking.plot','bookingFee'])
        ->orderByDesc('paid_date')->get()
        ->groupBy(fn($fp) => $fp->bookingFee->fee_type ?? 'unknown');

    return view('office_expenses.officeExpensesView', compact(
        'expenses', 'incomes', 'inventories',
        'total', 'total_expenses', 'total_income', 'total_income_month',
        'total_inventory', 'total_inventory_month', 'net_balance',
        'fundSources', 'plotPaymentRows', 'cancelledBookingRefunds', 'feePaymentRows'
    ));
}

public function expenseDetailPdf($id)
{
    try {
        $expense = OfficeExpense::findOrFail($id);

        $verificationUrl = URL::signedRoute('expense.detail.pdf', ['id' => $id]);

        if (request()->hasValidSignature()) {
            return view('office_expenses.office_expensesPDF.verify_voucher', compact('expense'));
        }

        if (!auth()->check()) {
            abort(403, "Internal document. Access Denied.");
        }

        $renderer = new ImageRenderer(new RendererStyle(100, 1), new SvgImageBackEnd());
        $writer   = new Writer($renderer);
        $qrCode   = base64_encode($writer->writeString($verificationUrl));

        // Fund source stats
        $fundSourceLabels = [
            'plot_payments'   => 'Plot Payments',
            'security_fee'    => 'Security Fee',
            'registry_fee'    => 'Registry Fee',
            'development_fee' => 'Development Fee',
            'transfer_fee'    => 'Transfer Fee',
            'misc_income'     => 'Misc. Income',
        ];
        $fundKey   = $expense->fund_source;
        $fundLabel = $fundKey ? ($fundSourceLabels[$fundKey] ?? $fundKey) : null;

        $totalCollected = 0;
        $usedFromSource = 0;
        if ($fundKey) {
            $usedFromSource = OfficeExpense::whereIn('type', ['expense', 'inventory'])
                ->where('status', 'approved')
                ->where('fund_source', $fundKey)
                ->sum('amount');
            $feeTypeMap = [
                'plot_payments'   => null,
                'security_fee'    => 'security',
                'registry_fee'    => 'registry',
                'development_fee' => 'development',
                'transfer_fee'    => 'transfer',
            ];
            if ($fundKey === 'plot_payments') {
                $validIds       = Booking::where('status', '!=', 'cancelled')->pluck('id');
                $totalCollected = PlotPayment::whereIn('booking_id', $validIds)->where('status', 'paid')->sum('amount_paid');
            } elseif ($fundKey === 'misc_income') {
                $totalCollected = OfficeExpense::where('type', 'income')->where('status', 'approved')->sum('amount');
            } elseif (isset($feeTypeMap[$fundKey]) && $feeTypeMap[$fundKey]) {
                $totalCollected = FeePayment::whereHas('bookingFee', fn($q) => $q->where('fee_type', $feeTypeMap[$fundKey]))->sum('amount');
            }
        }

        $pdf = Pdf::loadView(
            'office_expenses.office_expensesPDF.voucherDetail',
            compact('expense', 'qrCode', 'fundLabel', 'totalCollected', 'usedFromSource')
        )->setPaper('a4', 'portrait')
         ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->stream("Voucher_{$expense->voucher_no}.pdf");

    } catch (\Exception $e) {
        return redirect()->back()->with('error', "Error: " . $e->getMessage());
    }
}

public function monthlySummary(Request $request)
{
    $year = $request->filled('year') ? (int)$request->year : now()->year;

    // 2. ───── PROJECT ALL-TIME STATS ─────
    $allTimePlotIncome = PlotPayment::where('status', 'paid')
        ->sum('amount_paid');

    $allTimeOfficeIncome = OfficeExpense::where('type', 'income')
        ->where('status', 'approved')
        ->sum('amount');
        // dd($allTimeOfficeIncome);

    $grandTotalProjectCollection = $allTimePlotIncome + $allTimeOfficeIncome;

    // 3. ───── YEARLY BREAKDOWN LOGIC ─────
    $months = [];
    for ($m = 1; $m <= 12; $m++) {
        $officeIncome = OfficeExpense::where('type', 'income')
            ->where('category', '!=', 'Misc')
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $m)
            ->where('status', 'approved')
            ->sum('amount');

        $miscIncome = OfficeExpense::where('type', 'income')
            ->where('category', 'Misc')
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $m)
            ->where('status', 'approved')
            ->sum('amount');

        $plotIncome = PlotPayment::whereYear('paid_date', $year)
            ->whereMonth('paid_date', $m)
            ->where('status', 'paid')
            ->sum('amount_paid');

        // ── FIXED: Switched to gather monthly sums from BookingFee ──
        $feeIncome = \App\Models\FeePayment::whereYear('paid_date', $year)
            ->whereMonth('paid_date', $m)
            ->sum('amount');

        $expenses = OfficeExpense::where('type', 'expense')
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $m)
            ->where('status', 'approved')
            ->sum('amount');

        $inventory = OfficeExpense::where('type', 'inventory')
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $m)
            ->where('status', 'approved')
            ->sum('amount');

        $transferCount = PlotTransfer::whereYear('transfer_date', $year)
            ->whereMonth('transfer_date', $m)
            ->whereIn('status', ['approved', 'completed'])
            ->count();

        $totalIncome = $officeIncome + $miscIncome + $plotIncome + $feeIncome;

        $months[$m] = [
            'name'            => Carbon::create($year, $m)->format('F'),
            'office_income'   => $officeIncome,
            'misc_income'     => $miscIncome,
            'fee_income'      => $feeIncome,
            'plot_income'     => $plotIncome,
            'total_income'    => $totalIncome,
            'expenses'        => $expenses,
            'inventory'       => $inventory,
            'transfer_count'  => $transferCount,
            'net'             => $totalIncome - ($expenses + $inventory),
        ];
    }

    // 4. Summaries for the selected year
    $yearTotalIncome     = collect($months)->sum('total_income');
    $yearTotalExpenses   = collect($months)->sum('expenses');
    $yearTotalInventory  = collect($months)->sum('inventory');
    $yearTotalFeeIncome  = collect($months)->sum('fee_income');
    $yearTotalMiscIncome = collect($months)->sum('misc_income');
    $yearTotalTransfers  = collect($months)->sum('transfer_count');
    $yearNet = $yearTotalIncome - ($yearTotalExpenses + $yearTotalInventory);

    // Year selection range
    $firstExpense = OfficeExpense::min('expense_date');
    $firstPayment = PlotPayment::min('paid_date');
    $startYear    = min(
        $firstExpense ? Carbon::parse($firstExpense)->year : now()->year,
        $firstPayment ? Carbon::parse($firstPayment)->year : now()->year
    );
    $availableYears = range($startYear, now()->year);

    // 5. ───── FUND SOURCE OVERVIEW (ALL-TIME) ─────
    // ── FIXED: Switched all queries below to use BookingFee:: paid_amount filtered by fee_type ──
    $fundSources = [
        'plot_payments'   => ['label'=>'Plot Payments',   'icon'=>'🏘️','color'=>'#1d4ed8','bg'=>'#eff6ff','border'=>'#bfdbfe',
            'collected'=> PlotPayment::where('status','paid')->sum('amount_paid')],
        'security_fee'    => ['label'=>'Security Fee',    'icon'=>'🔒','color'=>'#7c3aed','bg'=>'#fdf4ff','border'=>'#ddd6fe',
            'collected'=> \App\Models\BookingFee::where('fee_type','security')->sum('paid_amount')],
        'registry_fee'    => ['label'=>'Registry Fee',    'icon'=>'📋','color'=>'#0369a1','bg'=>'#e0f2fe','border'=>'#bae6fd',
            'collected'=> \App\Models\BookingFee::where('fee_type','registry')->sum('paid_amount')],
        'development_fee' => ['label'=>'Development Fee', 'icon'=>'🏗️','color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#bbf7d0',
            'collected'=> \App\Models\BookingFee::where('fee_type','development')->sum('paid_amount')],
        'transfer_fee'    => ['label'=>'Transfer Fee',    'icon'=>'🤝','color'=>'#ca8a04','bg'=>'#fefce8','border'=>'#fde68a',
            'collected'=> \App\Models\BookingFee::where('fee_type','transfer')->sum('paid_amount')
                        + PlotTransfer::where('transfer_fee_status','paid')->whereNotNull('fee_paid_date')->where('transfer_fee','>',0)->sum('transfer_fee')],
        'misc_income'     => ['label'=>'Misc. Income',    'icon'=>'💰','color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a',
            'collected'=> OfficeExpense::where('type','income')->where('status','approved')->sum('amount')],
    ];

    foreach ($fundSources as $key => &$fs) {
        $fs['used']           = OfficeExpense::where('type','expense')->where('status','approved')->where('fund_source',$key)->sum('amount');
        $fs['used_this_year'] = OfficeExpense::where('type','expense')->where('status','approved')->where('fund_source',$key)->whereYear('expense_date',$year)->sum('amount');
        $fs['remaining']      = max(0, $fs['collected'] - $fs['used']);
    }
    unset($fs);

    // Monthly fund breakdown: for each month, sum by fund source
    $monthlyFundBreakdown = [];
    foreach (array_keys($fundSources) as $key) {
        for ($m = 1; $m <= 12; $m++) {
            $monthlyFundBreakdown[$m][$key] = OfficeExpense::where('type','expense')
                ->where('status','approved')
                ->where('fund_source',$key)
                ->whereYear('expense_date',$year)
                ->whereMonth('expense_date',$m)
                ->sum('amount');
        }
    }

    return view('office_expenses.monthly_summary', compact(
        'months',
        'year',
        'availableYears',
        'grandTotalProjectCollection',
        'yearTotalIncome',
        'yearTotalExpenses',
        'yearTotalInventory',
        'yearTotalFeeIncome',
        'yearTotalMiscIncome',
        'yearTotalTransfers',
        'yearNet',
        'fundSources',
        'monthlyFundBreakdown'
    ));
}
public function monthlyPdf(Request $request)
{
    $year  = $request->filled('year')  ? (int)$request->year  : now()->year;
    $month = $request->filled('month') ? (int)$request->month : now()->month;

    $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
    $endDate   = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

    $plotPayments = PlotPayment::whereBetween('paid_date', [$startDate, $endDate])
        ->where('status', 'paid')
        ->with(['booking.customer', 'booking.plot'])
        ->orderBy('paid_date')
        ->get();

    // ── FIXED: Switched from FeePayment over to BookingFee Model range query ──
    $feePayments = \App\Models\FeePayment::whereBetween('paid_date', [$startDate, $endDate])
        ->with(['booking.customer', 'booking.plot'])
        ->orderBy('paid_date')
        ->get();

    $incomes = OfficeExpense::where('type', 'income')
        ->where('category', '!=', 'Misc')
        ->whereBetween('expense_date', [$startDate, $endDate])
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    $miscIncomes = OfficeExpense::where('type', 'income')
        ->where('category', 'Misc')
        ->whereBetween('expense_date', [$startDate, $endDate])
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    $expenses = OfficeExpense::where('type', 'expense')
        ->whereBetween('expense_date', [$startDate, $endDate])
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    $inventories = OfficeExpense::where('type', 'inventory')
        ->whereBetween('expense_date', [$startDate, $endDate])
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    $transfers = PlotTransfer::whereBetween('transfer_date', [$startDate, $endDate])
        ->whereIn('status', ['approved', 'completed'])
        ->with(['fromCustomer', 'toCustomer', 'plot'])
        ->orderBy('transfer_date')
        ->get();

    $totalPlotIncome   = $plotPayments->sum('amount_paid');

    // ── FIXED MATH LINE: Modified sum column constraint to map to paid_amount column natively ──
    $totalFeeIncome    = $feePayments->sum('paid_amount');

    $totalOfficeIncome = $incomes->sum('amount');
    $totalMiscIncome   = $miscIncomes->sum('amount');
    $totalExpenses     = $expenses->sum('amount');
    $totalInventory    = $inventories->sum('amount');
    $totalIncome       = $totalPlotIncome + $totalFeeIncome + $totalOfficeIncome + $totalMiscIncome;
    $netBalance        = $totalIncome - ($totalExpenses + $totalInventory);

    $feeTypeMeta = [
        'registry'    => ['label'=>'Registry Fee',    'color'=>'#1d4ed8'],
        'development' => ['label'=>'Development Fee', 'color'=>'#15803d'],
        'security'    => ['label'=>'Security Fee',    'color'=>'#7c3aed'],
        'transfer'    => ['label'=>'Transfer Fee',    'color'=>'#ca8a04'],
    ];

    $monthLabel = Carbon::create($year, $month)->format('F Y');
    $society    = $this->societyConfig();

    $pdf = Pdf::loadView('office_expenses.monthly_pdf', compact(
        'year', 'month', 'monthLabel',
        'plotPayments', 'feePayments', 'incomes', 'miscIncomes',
        'expenses', 'inventories', 'transfers',
        'totalPlotIncome', 'totalFeeIncome', 'totalOfficeIncome', 'totalMiscIncome',
        'totalExpenses', 'totalInventory', 'totalIncome', 'netBalance',
        'feeTypeMeta', 'society'
    ))->setPaper('a4', 'portrait');

    return $pdf->stream('monthly-report-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf');
}
public function yearlyPdf(Request $request)
{
    $year = $request->filled('year') ? (int)$request->year : now()->year;

    $months = [];
    for ($m = 1; $m <= 12; $m++) {
        $plotIncome = PlotPayment::whereYear('paid_date', $year)->whereMonth('paid_date', $m)
            ->where('status', 'paid')->sum('amount_paid');

        // ── FIXED: Switched from FeePayment over to BookingFee Model calculation ──
        $feeIncome = \App\Models\FeePayment::whereYear('paid_date', $year)
            ->whereMonth('paid_date', $m)
            ->sum('amount');

        $officeIncome = OfficeExpense::where('type', 'income')->where('category', '!=', 'Misc')
            ->whereYear('expense_date', $year)->whereMonth('expense_date', $m)
            ->where('status', 'approved')->sum('amount');

        $miscIncome = OfficeExpense::where('type', 'income')->where('category', 'Misc')
            ->whereYear('expense_date', $year)->whereMonth('expense_date', $m)
            ->where('status', 'approved')->sum('amount');

        $expenses = OfficeExpense::where('type', 'expense')
            ->whereYear('expense_date', $year)->whereMonth('expense_date', $m)
            ->where('status', 'approved')->sum('amount');

        $inventory = OfficeExpense::where('type', 'inventory')
            ->whereYear('expense_date', $year)->whereMonth('expense_date', $m)
            ->where('status', 'approved')->sum('amount');

        $transferCount = PlotTransfer::whereYear('transfer_date', $year)->whereMonth('transfer_date', $m)
            ->whereIn('status', ['approved', 'completed'])->count();

        $totalIncome = $plotIncome + $feeIncome + $officeIncome + $miscIncome;

        $months[$m] = [
            'name'           => Carbon::create($year, $m)->format('F'),
            'plot_income'    => $plotIncome,
            'fee_income'     => $feeIncome,
            'office_income'  => $officeIncome,
            'misc_income'    => $miscIncome,
            'total_income'   => $totalIncome,
            'expenses'       => $expenses,
            'inventory'      => $inventory,
            'transfer_count' => $transferCount,
            'net'            => $totalIncome - ($expenses + $inventory),
        ];
    }

    $yearTotals = [
        'plot_income'    => collect($months)->sum('plot_income'),
        'fee_income'     => collect($months)->sum('fee_income'),
        'office_income'  => collect($months)->sum('office_income'),
        'misc_income'    => collect($months)->sum('misc_income'),
        'total_income'   => collect($months)->sum('total_income'),
        'expenses'       => collect($months)->sum('expenses'),
        'inventory'      => collect($months)->sum('inventory'),
        'transfer_count' => collect($months)->sum('transfer_count'),
        'net'            => collect($months)->sum('net'),
    ];

    $society = $this->societyConfig();

    $pdf = Pdf::loadView('office_expenses.yearly_pdf', compact(
        'year', 'months', 'yearTotals', 'society'
    ))->setPaper('a4', 'landscape');

    return $pdf->stream('yearly-report-' . $year . '.pdf');
}

public function dailyCash(Request $request)
{
    // Ensure we handle the date input safely
    $date = $request->filled('date')
        ? Carbon::parse($request->date)->format('Y-m-d')
        : Carbon::today()->format('Y-m-d');

    // 1. Cash OUT (Approved Office Expenses)
    $expenses = OfficeExpense::where('type', 'expense')
        ->whereDate('expense_date', $date)
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    // ✅ 2. Cash OUT — Inventory (Purchases/Supplies)
    $inventories = OfficeExpense::where('type', 'inventory')
        ->whereDate('expense_date', $date)
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    // 3. Cash IN — Office Income (Rent, Tube Well, etc.) — excluding Misc
    $incomes = OfficeExpense::where('type', 'income')
        ->where('category', '!=', 'Misc')
        ->whereDate('expense_date', $date)
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    // 3b. Cash IN — Miscellaneous Income (category = 'Misc')
    $miscIncomes = OfficeExpense::where('type', 'income')
        ->where('category', 'Misc')
        ->whereDate('expense_date', $date)
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    // 4. Cash IN — Plot Payments (all: cash received on this date is cash, regardless of later booking status)
    $plotPayments = PlotPayment::whereDate('paid_date', $date)
        ->where('status', 'paid')
        ->with(['booking.customer', 'booking.plot'])
        ->orderBy('paid_date')
        ->get();

    // 5. Cash IN — Fee Payments (Safely querying via the ledger FeePayment model)
    $feePayments = \App\Models\FeePayment::whereDate('paid_date', $date)
        ->with(['bookingFee', 'booking.customer', 'booking.plot']) // 🔥 Added bookingFee here so your template can find fee_type
        ->orderBy('paid_date')
        ->get();

    // 6. Plot Transfers for the day (informational)
    $transfers = PlotTransfer::whereDate('transfer_date', $date)
        ->whereIn('status', ['approved', 'completed'])
        ->with(['fromCustomer', 'toCustomer', 'plot'])
        ->orderBy('transfer_date')
        ->get();

    // 6b. Transfer fees paid directly via TransferController
    $directTransferFees = PlotTransfer::whereDate('fee_paid_date', $date)
        ->where('transfer_fee_status', 'paid')
        ->where('transfer_fee', '>', 0)
        ->with(['fromCustomer', 'toCustomer', 'plot'])
        ->orderBy('fee_paid_date')
        ->get();

    $totalDirectTransferFees = $directTransferFees->sum('transfer_fee');

    // 7. Calculations
    $totalExpenses       = $expenses->sum('amount');
    $totalInventory      = $inventories->sum('amount');
    $totalOfficeIncome   = $incomes->sum('amount');
    $totalMiscIncome     = $miscIncomes->sum('amount');
    $totalPlotIncome     = $plotPayments->sum('amount_paid');

    // FIX: Summing 'amount' column directly from the fee_payments ledger table rows + direct transfer fees
    $totalFeeIncome      = $feePayments->sum('amount') + $totalDirectTransferFees;

    $totalIncome         = $totalOfficeIncome + $totalMiscIncome + $totalPlotIncome + $totalFeeIncome;
    $netBalance          = $totalIncome - ($totalExpenses + $totalInventory);

    // 8. Fund source usage for the day
    $fsMeta = [
        'plot_payments'   => ['label' => 'Plot Payments',   'icon' => '🏘️', 'color' => '#1d4ed8', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
        'security_fee'    => ['label' => 'Security Fee',    'icon' => '🔒', 'color' => '#7c3aed', 'bg' => '#fdf4ff', 'border' => '#ddd6fe'],
        'registry_fee'    => ['label' => 'Registry Fee',    'icon' => '📋', 'color' => '#0369a1', 'bg' => '#e0f2fe', 'border' => '#bae6fd'],
        'development_fee' => ['label' => 'Development Fee', 'icon' => '🏗️', 'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
    ];

    $dailyFundUsage = [];
    foreach ($fsMeta as $key => $meta) {
        $used = $expenses->where('fund_source', $key)->sum('amount');
        if ($used > 0) {
            $dailyFundUsage[$key] = array_merge($meta, ['used' => $used]);
        }
    }
    $noFundExpenses = $expenses->whereNull('fund_source')->sum('amount');

    // Convert back to Carbon object for the View
    $date = Carbon::parse($date);

    $viewData = compact(
        'date',
        'expenses',
        'incomes',
        'miscIncomes',
        'inventories',
        'plotPayments',
        'feePayments',
        'directTransferFees',
        'totalDirectTransferFees',
        'transfers',
        'totalExpenses',
        'totalInventory',
        'totalOfficeIncome',
        'totalMiscIncome',
        'totalPlotIncome',
        'totalFeeIncome',
        'totalIncome',
        'netBalance',
        'fsMeta',
        'dailyFundUsage',
        'noFundExpenses'
    );

    if ($request->ajax()) {
        return response()->json([
            'html'      => view('office_expenses._daily_cash_data', $viewData)->render(),
            'date'      => $date->format('Y-m-d'),
            'dateLabel' => $date->format('l, d F Y'),
            'prevDate'  => $date->copy()->subDay()->format('Y-m-d'),
            'nextDate'  => $date->copy()->addDay()->format('Y-m-d'),
        ]);
    }

    return view('office_expenses.daily_cash', $viewData);
}
public function dailyCashPdf(Request $request)
{
    $date = $request->filled('date')
        ? Carbon::parse($request->date)->format('Y-m-d')
        : Carbon::today()->format('Y-m-d');

    $expenses = OfficeExpense::where('type', 'expense')
        ->whereDate('expense_date', $date)
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    $inventories = OfficeExpense::where('type', 'inventory')
        ->whereDate('expense_date', $date)
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    $incomes = OfficeExpense::where('type', 'income')
        ->where('category', '!=', 'Misc')
        ->whereDate('expense_date', $date)
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    $miscIncomes = OfficeExpense::where('type', 'income')
        ->where('category', 'Misc')
        ->whereDate('expense_date', $date)
        ->where('status', 'approved')
        ->orderBy('expense_date')
        ->get();

    $plotPayments = PlotPayment::whereDate('paid_date', $date)
        ->where('status', 'paid')
        ->with(['booking.customer', 'booking.plot'])
        ->orderBy('paid_date')
        ->get();

    // ── FIXED SECTION 5: Switched to fetch from BookingFee Model while keeping variable name identical ──
    $feePayments = \App\Models\FeePayment::whereDate('paid_date', $date)
        ->with(['booking.customer', 'booking.plot'])
        ->orderBy('paid_date')
        ->get();

    $transfers = PlotTransfer::whereDate('transfer_date', $date)
        ->whereIn('status', ['approved', 'completed'])
        ->with(['fromCustomer', 'toCustomer', 'plot'])
        ->orderBy('transfer_date')
        ->get();

    $directTransferFees = PlotTransfer::whereDate('fee_paid_date', $date)
        ->where('transfer_fee_status', 'paid')
        ->where('transfer_fee', '>', 0)
        ->with(['fromCustomer', 'toCustomer', 'plot'])
        ->orderBy('fee_paid_date')
        ->get();

    $totalDirectTransferFees = $directTransferFees->sum('transfer_fee');

    $totalExpenses     = $expenses->sum('amount');
    $totalInventory    = $inventories->sum('amount');
    $totalOfficeIncome = $incomes->sum('amount');
    $totalMiscIncome   = $miscIncomes->sum('amount');
    $totalPlotIncome   = $plotPayments->sum('amount_paid');

    // ── FIXED MATH LINE: Changed from ->bookingFee->sum('amount') to directly sum the paid_amount column ──
    $totalFeeIncome    = $feePayments->sum('amount') ;

    $totalIncome       = $totalOfficeIncome + $totalMiscIncome + $totalPlotIncome + $totalFeeIncome;
    $netBalance        = $totalIncome - ($totalExpenses + $totalInventory);

    $date = Carbon::parse($date);
    $society = $this->societyConfig();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('office_expenses.daily_cash_pdf', compact(
        'date', 'expenses', 'incomes', 'miscIncomes', 'inventories',
        'plotPayments', 'feePayments', 'directTransferFees', 'totalDirectTransferFees', 'transfers',
        'totalExpenses', 'totalInventory', 'totalOfficeIncome',
        'totalMiscIncome', 'totalPlotIncome', 'totalFeeIncome', 'totalIncome', 'netBalance',
        'society'
    ))->setPaper('a4', 'portrait');

    return $pdf->stream('daily-cash-' . $date->format('Y-m-d') . '.pdf');
}
    public function incomeIndex()
    {
        $incomes = OfficeExpense::where('type', 'income')
            ->orderBy('expense_date', 'desc')
            ->get();

        $total_month = OfficeExpense::where('type', 'income')
            ->where('status', 'approved')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $total_all = OfficeExpense::where('type', 'income')
            ->where('status', 'approved')
            ->sum('amount');

        $by_category = OfficeExpense::where('type', 'income')
            ->where('status', 'approved')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        return view('office_income.index', compact('incomes', 'total_month', 'total_all', 'by_category'));
    }


}
