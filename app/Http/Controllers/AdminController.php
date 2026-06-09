<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingFee;
use App\Models\Plot;
use App\Models\Customer;
use App\Models\OfficeExpense;
use App\Models\PlotPayment;
use App\Models\PlotTransfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
public function index(): \Illuminate\View\View
{
    $user = Auth::user();

    // ══════════════════════════════════════════════════════════
    // HELPER — convert size to marlas (1 kanal = 20 marlas)
    // ══════════════════════════════════════════════════════════
    $toMarlas = function ($size, $unit): float {
        $size = (float)($size ?? 0);
        $unit = strtolower($unit ?? 'marla');
        if (str_contains($unit, 'kanal')) return $size * 20;
        return $size;
    };

    $toDisplay = function (float $marlas): string {
        if ($marlas >= 20) {
            $kanals = floor($marlas / 20);
            $rem    = $marlas - ($kanals * 20);
            return $rem > 0
                ? "{$kanals} Kanal " . number_format($rem, 0) . " Marla"
                : "{$kanals} Kanal";
        }
        return number_format($marlas, 0) . " Marla";
    };

    // ══════════════════════════════════════════════════════════
    // PLOTS
    // ══════════════════════════════════════════════════════════
    $allPlots = Plot::all();

    $totalPlots     = $allPlots->count();
    $availablePlots = $allPlots->where('status', 'available')->count();
    $bookedPlots    = $allPlots->whereIn('status', ['booked', 'reserved'])->count();
    $soldPlots      = $allPlots->where('status', 'sold')->count();

    // Marlas per status
    $totalMarlas     = $allPlots->sum(fn($p) => $toMarlas($p->size, $p->unit));
    $availableMarlas = $allPlots->where('status', 'available')->sum(fn($p) => $toMarlas($p->size, $p->unit));
    $bookedMarlas    = $allPlots->whereIn('status', ['booked','reserved'])->sum(fn($p) => $toMarlas($p->size, $p->unit));
    $soldMarlas      = $allPlots->where('status', 'sold')->sum(fn($p) => $toMarlas($p->size, $p->unit));

    $totalDisplay     = $toDisplay($totalMarlas);
    $availableDisplay = $toDisplay($availableMarlas);
    $bookedDisplay    = $toDisplay($bookedMarlas);
    $soldDisplay      = $toDisplay($soldMarlas);

    // Plot status breakdown for chart
    $plotStatusBreakdown = [
        'Available' => $availablePlots,
        'Booked'    => $bookedPlots,
        'Sold'      => $soldPlots,
    ];
    $onHoldBookings = \App\Models\BookingHold::where('status','hold')->distinct('booking_id')->count('booking_id');

// "Remaining" = plots not yet sold (available + booked/reserved)
$remainingMarlas  = $availableMarlas + $bookedMarlas;
$remainingDisplay = $toDisplay($remainingMarlas);


    // ══════════════════════════════════════════════════════════
    // BOOKINGS
    // ══════════════════════════════════════════════════════════
    $totalBookings         = Booking::count();
    $activeBookings        = Booking::where('status', 'active')->count();
    $completedBookings     = Booking::where('status', 'completed')->count();
    $pendingBookings       = Booking::where('status', 'pending')->count();
    $transferredBookings   = Booking::where('status', 'transferred')->count();
    $pendingTransferCount  = Booking::where('status', 'pending_transfer')->count();
    $swappedBookings       = Booking::where('status', 'swapped')->count();
    $cancelledBookings     = Booking::where('status', 'cancelled')->count();
    $cancelledRefundTotal  = Booking::where('status', 'cancelled')->sum('cancellation_refund');
    $cancelledCollected    = PlotPayment::where('status', 'paid')
        ->whereHas('booking', fn($q) => $q->where('status', 'cancelled'))
        ->sum('amount_paid');

    // This month new bookings
    $newBookingsThisMonth = Booking::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count();

    // ══════════════════════════════════════════════════════════
    // FINANCIAL — Plot Payments
    // ══════════════════════════════════════════════════════════
    $plotPriceCats = ['down_payment','installment','quarterly_installment','plot_balance','others'];

    // ── Real cash collected — EXCLUDES settlement-discount (DISC-) records and cancelled bookings ──
    // Old DISC- records have remarks = 'Settlement discount — waived amount (not collected).'
    // New settlement records use discount_amount column instead of a separate record.
    $discSentinel = 'Settlement discount — waived amount (not collected).';

    $totalCollection = PlotPayment::where('status', 'paid')
                                   ->whereIn('payment_category', $plotPriceCats)
                                   ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
                                   ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
                                   ->sum('amount_paid');

    // ── Payment-time (settlement) discounts — two sources for backward compat: ──
    // 1. Old DISC- records: amount_paid stores the waived amount
    // 2. New records: discount_amount column (DISC- records no longer created)
    $payDiscOld = (float) PlotPayment::where('status', 'paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->where('remarks', '=', $discSentinel)
        ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
        ->sum('amount_paid');
    $payDiscNew = (float) PlotPayment::where('status', 'paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
        ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
        ->sum('discount_amount');
    $totalPaymentDiscounts = $payDiscOld + $payDiscNew;

    // Count of bookings that have had a payment-time (settlement) discount applied
    $settlementDiscountCount = (int) PlotPayment::where('status', 'paid')
        ->where(function ($q) use ($discSentinel) {
            $q->where('remarks', '=', $discSentinel)
              ->orWhere('discount_amount', '>', 0);
        })
        ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
        ->distinct('booking_id')
        ->count('booking_id');

    $thisMonthCollection = PlotPayment::where('status', 'paid')
                                       ->whereIn('payment_category', $plotPriceCats)
                                       ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
                                       ->whereMonth('paid_date', now()->month)
                                       ->whereYear('paid_date', now()->year)
                                       ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
                                       ->sum('amount_paid');

    // Total project value = sum of ORIGINAL bookings only (no parent).
    // Transfer bookings always have parent_booking_id set; original bookings don't.
    $totalPlotValue = Booking::whereNull('parent_booking_id')
        ->where('status', '!=', 'cancelled')
        ->sum('total_price');

    // Plot-level discount credits = sum of plots.discount_amount for original non-cancelled bookings.
    $dashDiscount = (float) DB::table('bookings')
        ->join('plots', 'bookings.plot_id', '=', 'plots.id')
        ->whereNull('bookings.parent_booking_id')
        ->where('bookings.status', '!=', 'cancelled')
        ->sum(DB::raw('COALESCE(plots.discount_amount, 0)'));

    // Remaining — computed per booking, ACTIVE/PENDING only.
    // Completed/transferred/cancelled bookings have remaining = 0 by definition.
    // Transfer child bookings (parent_booking_id != null) ARE included: after an ownership
    // transfer the original booking moves to 'transferred' (excluded by the status filter)
    // and the new owner's booking carries the remaining balance — we must count it here.
    $totalRemaining = (float) DB::selectOne("
        SELECT COALESCE(SUM(GREATEST(0,
            b.total_price
            - COALESCE(paid.cash_total, 0)
            - COALESCE(disc.disc_total, 0)
        )), 0) AS remaining
        FROM bookings b
        LEFT JOIN (
            SELECT booking_id, SUM(amount_paid) AS cash_total
            FROM plot_payments
            WHERE status = 'paid'
              AND payment_category IN ('down_payment','installment','quarterly_installment','plot_balance','others')
              AND (remarks IS NULL OR remarks != ?)
            GROUP BY booking_id
        ) paid ON paid.booking_id = b.id
        LEFT JOIN (
            SELECT booking_id,
                SUM(CASE WHEN remarks = ? THEN amount_paid ELSE discount_amount END) AS disc_total
            FROM plot_payments
            WHERE status = 'paid'
              AND payment_category IN ('down_payment','installment','quarterly_installment','plot_balance','others')
            GROUP BY booking_id
        ) disc ON disc.booking_id = b.id
        WHERE b.status IN ('active','pending','pending_transfer')
    ", [$discSentinel, $discSentinel])->remaining ?? 0;

    // Payment category breakdown — excludes cancelled bookings
    $categoryBreakdown = PlotPayment::where('status', 'paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
        ->select('payment_category', DB::raw('SUM(amount_paid) as total'))
        ->groupBy('payment_category')
        ->pluck('total', 'payment_category')
        ->toArray();

    // Total all payment categories — excludes cancelled bookings
    $totalAllPayments = PlotPayment::where('status', 'paid')
                                    ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
                                    ->sum('amount_paid');

    // Monthly chart — last 6 months
    $monthlyLabels     = [];
    $monthlyCollection = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $monthlyLabels[]     = $month->format('M Y');
        $monthlyCollection[] = (float) PlotPayment::where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->whereMonth('paid_date', $month->month)
            ->whereYear('paid_date',  $month->year)
            ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->sum('amount_paid');
    }

    // ══════════════════════════════════════════════════════════
    // FEES (booking_fees + fee_payments)
    // Exclude fees linked to cancelled bookings
    // ══════════════════════════════════════════════════════════
    $registryFeesTotal     = BookingFee::where('fee_type', 'registry')->whereHas('booking', fn($b) => $b->where('status', '!=', 'cancelled'))->sum('amount');
    $registryFeesPaid      = BookingFee::where('fee_type', 'registry')->whereHas('booking', fn($b) => $b->where('status', '!=', 'cancelled'))->sum('paid_amount');
    $registryFeesRemaining = max(0, $registryFeesTotal - $registryFeesPaid);

    $developmentFeesTotal     = BookingFee::where('fee_type', 'development')->whereHas('booking', fn($b) => $b->where('status', '!=', 'cancelled'))->sum('amount');
    $developmentFeesPaid      = BookingFee::where('fee_type', 'development')->whereHas('booking', fn($b) => $b->where('status', '!=', 'cancelled'))->sum('paid_amount');
    $developmentFeesRemaining = max(0, $developmentFeesTotal - $developmentFeesPaid);

    $securityFeesPaid = BookingFee::where('fee_type', 'security')->whereHas('booking', fn($b) => $b->where('status', '!=', 'cancelled'))->sum('paid_amount');

    $transferFeesTotal     = BookingFee::where('fee_type', 'transfer')->whereHas('booking', fn($b) => $b->where('status', '!=', 'cancelled'))->sum('amount');
    $transferFeesPaid      = BookingFee::where('fee_type', 'transfer')->whereHas('booking', fn($b) => $b->where('status', '!=', 'cancelled'))->sum('paid_amount');
    $transferFeesRemaining = max(0, $transferFeesTotal - $transferFeesPaid);

    $totalFeesPaid = BookingFee::whereHas('booking', fn($b) => $b->where('status', '!=', 'cancelled'))->sum('paid_amount');

    // ══════════════════════════════════════════════════════════
    // TRANSFERS
    // ══════════════════════════════════════════════════════════
    $totalTransfers    = PlotTransfer::count();
    $pendingTransfers  = PlotTransfer::where('status', 'pending')->count();
    $completedTransfers= PlotTransfer::where('status', 'completed')->count();
    $ownershipTransfers= PlotTransfer::where('transfer_type', 'ownership')->count();
    $swapTransfers     = PlotTransfer::where('transfer_type', 'swap')->count();

    // Transfer fees pending payment
    $pendingFeeCount = BookingFee::where('fee_type', 'transfer')
                                  ->where('status', '!=', 'paid')
                                  ->count();

    // ══════════════════════════════════════════════════════════
    // CUSTOMERS
    // ══════════════════════════════════════════════════════════
    $totalCustomers = Customer::count();
    $activeCustomers= Customer::where('status', 'active')->count();
    $newThisMonth   = Customer::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->count();

    // ══════════════════════════════════════════════════════════
    // OFFICE EXPENSES & INCOME
    // ══════════════════════════════════════════════════════════
    $totalExpenses = OfficeExpense::where('type', 'expense')
                                   ->where('status', 'approved')
                                   ->sum('amount');

    $totalIncome   = OfficeExpense::where('type', 'income')
                                   ->where('status', 'approved')
                                   ->sum('amount');

    $totalInventory = OfficeExpense::where('type', 'inventory')
                                    ->where('status', 'approved')
                                    ->sum('amount');

    $thisMonthExpenses = OfficeExpense::where('type', 'expense')
                                       ->where('status', 'approved')
                                       ->whereMonth('expense_date', now()->month)
                                       ->whereYear('expense_date', now()->year)
                                       ->sum('amount');

    $thisMonthIncome   = OfficeExpense::where('type', 'income')
                                       ->where('status', 'approved')
                                       ->whereMonth('expense_date', now()->month)
                                       ->whereYear('expense_date', now()->year)
                                       ->sum('amount');

    $netBalance = $totalIncome - $totalExpenses;

    // ══════════════════════════════════════════════════════════
    // RECENT DATA
    // ══════════════════════════════════════════════════════════
    $recentBookings = Booking::with(['customer', 'plot', 'payments'])
                              ->latest()->take(8)->get();

    $recentPayments = PlotPayment::with(['booking.customer', 'booking.plot'])
                                  ->where('status', 'paid')
                                  ->latest('paid_date')->take(6)->get();

    $topCustomers = PlotPayment::where('plot_payments.status', 'paid')
        ->join('bookings',  'plot_payments.booking_id',  '=', 'bookings.id')
        ->join('customers', 'bookings.customer_id',      '=', 'customers.id')
        ->select('customers.id', 'customers.name', DB::raw('SUM(plot_payments.amount_paid) as total_paid'))
        ->groupBy('customers.id', 'customers.name')
        ->orderByDesc('total_paid')
        ->take(5)->get();

    // ══════════════════════════════════════════════════════════
    // INSTALLMENT ALERTS
    // ══════════════════════════════════════════════════════════
    $graceDays = (int) \App\Models\SystemConfig::get('installment_grace_days', 10);

    $overdueInstallments  = collect();
    $upcomingInstallments = collect();

    $installmentBookings = Booking::with([
        'payments' => fn($q) => $q->where('payment_category', 'installment')->where('status', 'paid'),
        'plot', 'customer',
    ])
    ->whereIn('status', ['active', 'pending'])
    ->where('total_installments', '>', 0)
    ->whereNotNull('monthly_installment')
    ->get();

    $today = Carbon::today();

    foreach ($installmentBookings as $booking) {
        $paidCount = $booking->payments->count();
        if ($paidCount >= $booking->total_installments) continue;

        $nextNo       = $paidCount + 1;
        $dueDate      = Carbon::parse($booking->booking_date)->addMonths($nextNo);
        $graceDeadline= $dueDate->copy()->addDays($graceDays);
        $daysUntilDue = (int)$today->diffInDays($dueDate, false);

        $record = (object)[
            'booking'             => $booking,
            'next_installment'    => $nextNo,
            'due_date'            => $dueDate,
            'monthly_installment' => $booking->monthly_installment ?? 0,
            'days_overdue'        => 0,
            'days_until_due'      => max(0, $daysUntilDue),
        ];

        if ($today->gt($graceDeadline)) {
            $record->days_overdue = (int)$graceDeadline->diffInDays($today);
            $overdueInstallments->push($record);
        } elseif ($daysUntilDue >= 0 && $daysUntilDue <= 30) {
            $upcomingInstallments->push($record);
        } elseif ($daysUntilDue < 0) {
            $record->days_until_due = 0;
            $upcomingInstallments->push($record);
        }
    }

    $overdueInstallments  = $overdueInstallments->sortByDesc('days_overdue')->values();
    $upcomingInstallments = $upcomingInstallments->sortBy('days_until_due')->values();

    // ══════════════════════════════════════════════════════════
    // DISCOUNTS — two types
    // 1. Plot-level: set when adding/editing a plot
    // 2. Payment-time: lump-sum settlement discounts
    // ══════════════════════════════════════════════════════════
    $totalPlotDiscounts = $dashDiscount;   // alias for clarity in the view

    $discountedBookingsCount = (int) DB::table('bookings')
        ->join('plots', 'bookings.plot_id', '=', 'plots.id')
        ->whereNull('bookings.parent_booking_id')
        ->where('bookings.status', '!=', 'cancelled')
        ->where('plots.discount_amount', '>', 0)
        ->count();

    // Combined total of ALL discounts (plot-level + payment-time)
    $totalDiscount  = $totalPlotDiscounts + $totalPaymentDiscounts;

    // Gross value = contracted price + plot discounts = original value before concessions
    $grossPlotValue = $totalPlotValue + $totalPlotDiscounts;

    // ══════════════════════════════════════════════════════════
    // USERS (staff)
    // ══════════════════════════════════════════════════════════
    $totalStaff  = User::count();
    $activeStaff = User::where('is_active', 1)->count();

    return view('layouts.dashboard', compact(
        'user',
        // Plots
        'totalPlots', 'availablePlots', 'bookedPlots', 'soldPlots',
        'totalMarlas', 'availableMarlas', 'bookedMarlas', 'soldMarlas',
        'totalDisplay', 'availableDisplay', 'bookedDisplay', 'soldDisplay',
        'plotStatusBreakdown',
        // Bookings
        'totalBookings', 'activeBookings', 'completedBookings',
        'pendingBookings', 'transferredBookings', 'pendingTransferCount',
        'swappedBookings', 'cancelledBookings', 'newBookingsThisMonth',
        'cancelledRefundTotal', 'cancelledCollected', 'onHoldBookings',
        // Financial
        'totalCollection', 'thisMonthCollection', 'totalRemaining',
        'totalPlotValue', 'totalAllPayments',
        'categoryBreakdown', 'monthlyLabels', 'monthlyCollection',
        // Discounts
        'totalDiscount', 'totalPlotDiscounts', 'totalPaymentDiscounts',
        'discountedBookingsCount', 'settlementDiscountCount', 'grossPlotValue',
        // Fees
        'registryFeesTotal', 'registryFeesPaid', 'registryFeesRemaining',
        'developmentFeesTotal', 'developmentFeesPaid', 'developmentFeesRemaining',
        'securityFeesPaid', 'transferFeesTotal', 'transferFeesPaid', 'transferFeesRemaining',
        'totalFeesPaid',
        // Transfers
        'totalTransfers', 'pendingTransfers', 'completedTransfers',
        'ownershipTransfers', 'swapTransfers', 'pendingFeeCount',
        // Customers
        'totalCustomers', 'activeCustomers', 'newThisMonth',
        // Office
        'totalExpenses', 'totalIncome', 'totalInventory',
        'thisMonthExpenses', 'thisMonthIncome', 'netBalance',
        // Staff
        'totalStaff', 'activeStaff',
        // Recent
        'recentBookings', 'recentPayments', 'topCustomers',
        // Installments
        'overdueInstallments', 'upcomingInstallments',
        // Legacy aliases (so old blade references don't break)
        'pendingTransfers', 'pendingBookings',
    ));
}

}
