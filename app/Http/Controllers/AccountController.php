<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Plot;
use App\Models\PlotPayment;
use App\Models\PlotTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Vinkla\Hashids\Facades\Hashids;
use Barryvdh\DomPDF\Facade\Pdf;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;
use App\Helpers\AppConfig;

class AccountController extends Controller
{
private function _globalStats(): array
{
    // Plot-price payment categories only — no fees, no processing_fee
    $plotPriceCats  = ['down_payment', 'installment', 'quarterly_installment', 'plot_balance', 'others'];
    $discSentinel   = 'Settlement discount — waived amount (not collected).';

    // Project value = original bookings only (parent_booking_id IS NULL = no transfer children)
    $total_booking = Booking::whereNull('parent_booking_id')
        ->where('status', '!=', 'cancelled')
        ->sum('total_price');

    // Real cash collected — excludes old DISC- records
    $total_received = PlotPayment::where('status', 'paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
        ->sum('amount_paid');

    // Plot-level discount credits
    $total_plot_discount = (float) \Illuminate\Support\Facades\DB::table('bookings')
        ->join('plots', 'bookings.plot_id', '=', 'plots.id')
        ->whereNull('bookings.parent_booking_id')
        ->where('bookings.status', '!=', 'cancelled')
        ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(plots.discount_amount, 0)'));

    // Payment-time (settlement) discount credits — old DISC- records + new discount_amount column
    $total_payment_discount = (float) PlotPayment::where('status', 'paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->where('remarks', '=', $discSentinel)
        ->sum('amount_paid')
        + (float) PlotPayment::where('status', 'paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
        ->sum('discount_amount');

    $total_discount  = $total_plot_discount + $total_payment_discount;

    // Remaining = project value minus all credits
    $total_remaining = max(0, $total_booking - $total_received - $total_discount);

    $recent_payments = PlotPayment::with(['booking.customer', 'booking.plot'])
        ->where('status', 'paid')
        ->latest('paid_date')
        ->take(10)
        ->get();

    return compact('total_booking', 'total_received', 'total_remaining', 'recent_payments');
}
// ─────────────────────────────────────────────────────────────────

public function index()
{
    $clients          = collect();
    $oldOwnerBookings = collect();

    return view('layouts.account', array_merge(
        $this->_globalStats(),
        compact('clients', 'oldOwnerBookings')
    ));
}


public function clientSearch(Request $request)
{
    $stats = $this->_globalStats();
    $clients = collect();
    $oldOwnerBookings = collect();

    if ($request->filled('search')) {
        $originalS = trim($request->search);
        // Strip spaces and dashes for CNIC / booking-ID / plot-number matching
        $cleanS = str_replace([' ', '-'], '', $originalS);

        $statuses = ['active', 'completed', 'transferred', 'pending', 'swapped', 'pending_transfer', 'partial_transferred', 'plot_relocated'];

        // ── Single combined query: match ANY field simultaneously ──────────────────
        $clients = Customer::with(['booking' => function ($q) use ($statuses) {
            $q->with('plot', 'payments')->whereIn('status', $statuses);
        }])
        ->where(function ($q) use ($originalS, $cleanS) {
            // Customer fields
            $q->where('name',   'like', "%$originalS%")
              ->orWhereRaw("REPLACE(REPLACE(cnic,   '-', ''), ' ', '') LIKE ?", ["%$cleanS%"])
              ->orWhereRaw("REPLACE(mobile, ' ', '') LIKE ?", ["%$cleanS%"])
              ->orWhereRaw("REPLACE(phone,  ' ', '') LIKE ?", ["%$cleanS%"])
              // Booking ID — match both with-dashes and without-dashes
              ->orWhereHas('booking', fn($bq) =>
                    $bq->where('customer_booking_id', 'like', "%$originalS%")
                       ->orWhereRaw("REPLACE(REPLACE(customer_booking_id, '-', ''), ' ', '') LIKE ?", ["%$cleanS%"])
              )
              // Plot number or block — match both with-dashes and without
              ->orWhereHas('booking.plot', fn($pq) =>
                    $pq->where('plot_number', 'like', "%$originalS%")
                       ->orWhereRaw("REPLACE(REPLACE(plot_number, '-', ''), ' ', '') LIKE ?", ["%$cleanS%"])
                       ->orWhere('block', 'like', "%$originalS%")
              );
        })
        ->get()
        ->filter(fn($c) => $c->booking->isNotEmpty());

        // ── Old owners / transfer history ──────────────────────────────────────────
        $oldTransfers = PlotTransfer::with([
            'fromCustomer',
            'toCustomer',
            'fromBooking' => function ($q) {
                $q->with(['plot', 'payments' => fn($p) => $p->orderBy('paid_date', 'asc')]);
            },
        ])
        ->where(function ($q) use ($originalS, $cleanS) {
            $q->whereHas('fromCustomer', fn($cq) =>
                $cq->where('name', 'like', "%$originalS%")
                   ->orWhereRaw("REPLACE(REPLACE(cnic, '-', ''), ' ', '') LIKE ?", ["%$cleanS%"])
                   ->orWhereRaw("REPLACE(mobile, ' ', '') LIKE ?", ["%$cleanS%"])
                   ->orWhereRaw("REPLACE(phone,  ' ', '') LIKE ?", ["%$cleanS%"])
            )
            ->orWhereHas('fromBooking', fn($bq) =>
                $bq->where('customer_booking_id', 'like', "%$originalS%")
                   ->orWhereRaw("REPLACE(REPLACE(customer_booking_id, '-', ''), ' ', '') LIKE ?", ["%$cleanS%"])
            )
            ->orWhereHas('fromBooking.plot', fn($pq) =>
                $pq->where('plot_number', 'like', "%$originalS%")
                   ->orWhereRaw("REPLACE(REPLACE(plot_number, '-', ''), ' ', '') LIKE ?", ["%$cleanS%"])
                   ->orWhere('block', 'like', "%$originalS%")
            );
        })
        ->whereIn('status', ['completed', 'pending'])
        ->latest('transfer_date')
        ->get();

        $oldOwnerBookings = $oldTransfers
            ->unique('from_booking_id')
            ->map(function ($t) {
                return (object) [
                    'transfer'     => $t,
                    'booking'      => $t->fromBooking,
                    'fromCustomer' => $t->fromCustomer,
                    'toCustomer'   => $t->toCustomer,
                    'allTransfers' => PlotTransfer::where('from_booking_id', $t->from_booking_id)
                                        ->with(['fromCustomer', 'toCustomer'])
                                        ->orderBy('transfer_date', 'asc')
                                        ->get(),
                ];
            })
            ->values();
    }

    return view('layouts.account', array_merge(
        $stats,
        compact('clients', 'oldOwnerBookings')
    ));
}




public function ledgerView(string $id): \Illuminate\View\View
{
    $mainBooking = Booking::with([
        'customer',
        'plot',
        'payments' => fn($q) => $q->orderBy('paid_date','asc'),
        'bookingFees.payments',
    ])->findOrFail($id);

    $payments = $mainBooking->payments;

    $totalPrice    = (float) $mainBooking->total_price;
    $discSentinel  = 'Settlement discount — waived amount (not collected).';

    $plotPriceCats = ['down_payment','quarterly_installment','installment','plot_balance','others'];

    // Real cash paid (exclude old DISC- records)
    $totalPaidReal = $payments
        ->where('status','paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)
        ->sum('amount_paid');

    // Payment-time discount credits: old DISC- records + new discount_amount column
    $paymentDiscountCredits = $payments
        ->where('status','paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->filter(fn($p) => ($p->remarks ?? '') === $discSentinel)
        ->sum('amount_paid')
        + $payments
        ->where('status','paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)
        ->sum('discount_amount');

    $totalCollectedReal = $payments->where('status','paid')
        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)
        ->sum('amount_paid');

    $downPaid    = $payments->where('payment_category','down_payment')->where('status','paid')
        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)->sum('amount_paid');
    $installPaid = $payments->where('payment_category','installment')->where('status','paid')
        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)->sum('amount_paid');
    $qtrPaid     = $payments->where('payment_category','quarterly_installment')->where('status','paid')
        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)->sum('amount_paid');
    $procPaid    = $payments->where('payment_category','processing_fee')->where('status','paid')
        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)->sum('amount_paid');
    $othersPaid  = $payments->where('payment_category','others')->where('status','paid')
        ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)->sum('amount_paid');

    $downDue = (float)($mainBooking->down_payment ?? 0);
    $procDue = 0;

    // ── Monthly installment ────────────────────────────────────────
    $totalInstallmentCount   = (int)($mainBooking->total_installments ?? 0);
    $monthlyInstallment      = (float)($mainBooking->monthly_installment ?? 0);
    $paidInstallmentCount    = $payments->where('payment_category','installment')->where('status','paid')->count();
    $hasInstallmentPlan      = $totalInstallmentCount > 0;

    // ── Quarterly ──────────────────────────────────────────────────
    $totalQuarterlyCount   = (int)($mainBooking->quarterly_installments ?? 0);
    $quarterlyAmount       = (float)($mainBooking->quarterly_amount ?? 0);
    $paidQuarterlyCount    = $payments->where('payment_category','quarterly_installment')->where('status','paid')->count();
    $hasQuarterlyPlan      = $totalQuarterlyCount > 0;

    // ── True total: use the larger of total_price or schedule-based total ─
    // Handles cases where quarterly installments were NOT factored into total_price
    $scheduleBasedTotal = $downDue
        + ($totalInstallmentCount * $monthlyInstallment)
        + ($totalQuarterlyCount   * $quarterlyAmount);
    $trueTotal = max($totalPrice, $scheduleBasedTotal);

    // Offer/plot discount — stored for DISPLAY in the price breakdown only.
    // It is already baked into total_price (booking form saves base - discount), so
    // we must NOT subtract it again from remaining.
    $plotDiscount = (float)($mainBooking->plot->discount_amount ?? 0);

    $remaining = max(0, $trueTotal - $totalPaidReal - $paymentDiscountCredits);

    // ── Is fully paid? ────────────────────────────────────────────
    // Tolerance of PKR 10 handles rounding (e.g. 18 × 75,556 = 1,360,008 vs total 1,360,000).
    // ALL three conditions must hold:
    //   1. Amount: remaining within tolerance
    //   2. Monthly count done  (if plan exists)
    //   3. Quarterly count done (if plan exists)
    $tolerance          = 10;
    $monthlyCountDone   = ($totalInstallmentCount === 0 || $paidInstallmentCount  >= $totalInstallmentCount);
    $quarterlyCountDone = ($totalQuarterlyCount   === 0 || $paidQuarterlyCount    >= $totalQuarterlyCount);
    $isFullyPaid        = ($remaining <= $tolerance) && $monthlyCountDone && $quarterlyCountDone;

    // A completed booking (e.g. settled via lump sum with discount) is always fully paid
    // regardless of individual installment counts — the status is the authoritative source.
    $closedStatuses = ['completed', 'cancelled', 'transferred', 'swapped', 'plot_relocated'];
    if (in_array($mainBooking->status, $closedStatuses)) {
        $isFullyPaid = true;
    }

    // Snap remaining to 0 so all downstream $remaining checks are clean
    if ($isFullyPaid) {
        $remaining = 0;
    }

    // Progress: cash paid + settlement discounts vs contracted price
    $totalCreditsLedger = $totalPaidReal + $paymentDiscountCredits;
    $prog = $trueTotal > 0
        ? min(round(($totalCreditsLedger / $trueTotal) * 100), 100)
        : 0;

    // ── Remaining installment counts (use per-schedule logic, not just $remaining) ──
    $remainingInstallmentCount = max(0, $totalInstallmentCount - $paidInstallmentCount);
    if ($remainingInstallmentCount === 0) {
        $nextInstallmentAmount = 0;
    } elseif ($remainingInstallmentCount === 1) {
        $nextInstallmentAmount = max(0, ($totalInstallmentCount * $monthlyInstallment) - $installPaid);
    } else {
        $nextInstallmentAmount = $monthlyInstallment;
    }

    $remainingQuarterlyCount = max(0, $totalQuarterlyCount - $paidQuarterlyCount);
    if ($remainingQuarterlyCount === 0) {
        $nextQuarterlyAmount = 0;
    } elseif ($remainingQuarterlyCount === 1) {
        $nextQuarterlyAmount = max(0, ($totalQuarterlyCount * $quarterlyAmount) - $qtrPaid);
    } else {
        $nextQuarterlyAmount = $quarterlyAmount;
    }

    $paidQuarterNos = $payments
        ->where('payment_category','quarterly_installment')
        ->where('status','paid')
        ->pluck('quarterly_no')->filter()->toArray();

    $nextQuarterDueDate = null;
    if ($hasQuarterlyPlan && $paidQuarterlyCount < $totalQuarterlyCount) {
        $nextQtrNo = $paidQuarterlyCount + 1;
        $nextQuarterDueDate = \Carbon\Carbon::parse($mainBooking->booking_date)->addMonths($nextQtrNo * 3)->format('Y-m-d');
    }
    $nextInstallmentDueDate = null;
    if ($hasInstallmentPlan && $paidInstallmentCount < $totalInstallmentCount) {
        $nextInstNo = $paidInstallmentCount + 1;
        $nextInstallmentDueDate = \Carbon\Carbon::parse($mainBooking->booking_date)->addMonths($nextInstNo)->format('Y-m-d');
    }

    // ── Transfer history ───────────────────────────────────────────
    $transferHistory = PlotTransfer::where('from_booking_id', $id)
        ->orWhere('to_booking_id', $id)
        ->with(['fromCustomer','toCustomer'])
        ->orderBy('transfer_date','asc')
        ->get();

    $pendingTransfer      = PlotTransfer::where('from_booking_id',$id)->where('status','pending')->first();
    $hasCompletedTransfer = PlotTransfer::where('from_booking_id',$id)->where('status','completed')->exists();

    $readOnly = request()->boolean('readonly')
        || $hasCompletedTransfer
        || $mainBooking->status === 'transferred'
        || $mainBooking->status === 'cancelled';

    // ══════════════════════════════════════════════════════════════
    // HOLD STATUS — new addition
    // ══════════════════════════════════════════════════════════════
    $activeHold  = \App\Models\BookingHold::where('booking_id', $mainBooking->id)
                        ->where('status', 'hold')
                        ->latest()
                        ->first();
    $isOnHold    = (bool) $activeHold;

    // If on hold, override readOnly to block payment modal too
    if ($isOnHold) {
        $readOnly = true; // payments blocked
    }

    // ══════════════════════════════════════════════════════════════
    // FEE STATUS — registry & development (from booking_fees table)
    // Used to gate possession letter + legal docs
    // ══════════════════════════════════════════════════════════════
    $registryFeeBill     = \App\Models\BookingFee::where('booking_id', $mainBooking->id)->where('fee_type','registry')->first();
    $developmentFeeBill  = \App\Models\BookingFee::where('booking_id', $mainBooking->id)->where('fee_type','development')->first();
    $securityFeeBill     = \App\Models\BookingFee::where('booking_id', $mainBooking->id)->where('fee_type','security')->first();

    $registryFeeRequired    = $mainBooking->has_registry_fee    || (bool) $registryFeeBill;
    $registryFeeCleared     = !$mainBooking->has_registry_fee   || ($registryFeeBill && $registryFeeBill->is_settled);

    $developmentFeeRequired = $mainBooking->has_development_fee || (bool) $developmentFeeBill;
    $developmentFeeCleared  = !$mainBooking->has_development_fee || ($developmentFeeBill && $developmentFeeBill->is_settled);

    $securityFeeRequired    = (bool) $mainBooking->has_security_fee || $securityFeeBill;

    // Security fee monthly calculation
    $secMonthlyRate   = (float)($mainBooking->plot->security_fee_amount ?? 0);
    $secMonthsTotal   = null;
    $secMonthsPaid    = null;
    $secMonthsUnpaid  = null;
    $secTotalOwed     = null;
    $secTotalPaid     = null;
    $secOutstanding   = null;
    $securityFeeCleared = true;

    if ($securityFeeRequired && $secMonthlyRate > 0 && ($mainBooking->booking_date || $mainBooking->security_fee_start_date)) {
        $secStart = \Carbon\Carbon::parse($mainBooking->security_fee_start_date ?: $mainBooking->booking_date)->startOfMonth();
        $secNow   = \Carbon\Carbon::now()->startOfMonth();
        $terminalSt = ['transferred','partial_transferred','cancelled','swapped','plot_relocated'];
        
        if ($mainBooking->security_fee_end_date) {
            $cap = \Carbon\Carbon::parse($mainBooking->security_fee_end_date)->startOfMonth();
            if ($cap->lt($secNow)) $secNow = $cap;
        } elseif (in_array($mainBooking->status, $terminalSt)) {
            $latestXfer = \App\Models\PlotTransfer::where('from_booking_id', $mainBooking->id)
                ->whereNotNull('transfer_date')->latest('transfer_date')->first();
            $capRaw = $latestXfer ? $latestXfer->transfer_date : $mainBooking->updated_at;
            $cap = \Carbon\Carbon::parse($capRaw)->startOfMonth();
            if ($cap->lt($secNow)) $secNow = $cap;
        }
        $secMonthsTotal  = (int)$secStart->diffInMonths($secNow) + 1;
        $secTotalPaid    = $securityFeeBill ? (float)$securityFeeBill->paid_amount : 0;
        $secMonthsPaid   = (int)floor($secTotalPaid / $secMonthlyRate);
        $secMonthsUnpaid = max(0, $secMonthsTotal - $secMonthsPaid);
        $secTotalOwed    = $secMonthsTotal * $secMonthlyRate;
        $secOutstanding  = max(0, $secTotalOwed - $secTotalPaid);
        $securityFeeCleared = $secOutstanding <= 0;
    } elseif ($securityFeeRequired) {
        $securityFeeCleared = $securityFeeBill ? $securityFeeBill->is_settled : false;
    }

    // Possession ready = plot fully paid AND all mandatory fees paid AND still current owner
    $plotFullyPaid  = $isFullyPaid;
    $allFeesCleared = $registryFeeCleared && $developmentFeeCleared && $securityFeeCleared;

    // Booking must be active or completed — transferred/swapped/etc. means this person
    // is no longer the owner; possession goes to the new owner (B's booking).
    $isCurrentOwner = in_array($mainBooking->status, ['active', 'completed']);

    $possessionReady   = $plotFullyPaid && $allFeesCleared && $isCurrentOwner;
    $legalDocsBlocked  = false; // application form & agreement always available
    $possessionBlocked = !$possessionReady;

    // Reason string for UI tooltip/banner
    $possessionBlockedReasons = [];
    if (!$plotFullyPaid)         $possessionBlockedReasons[] = 'Plot balance outstanding: PKR ' . number_format($remaining);
    if (!$registryFeeCleared)    $possessionBlockedReasons[] = 'Registry fee not cleared';
    if (!$developmentFeeCleared) $possessionBlockedReasons[] = 'Development fee not cleared';
    if (!$securityFeeCleared)    $possessionBlockedReasons[] = 'Security fee overdue';
    if (!$isCurrentOwner) {
        $noOwnerStatuses = ['transferred', 'partial_transferred', 'swapped', 'plot_relocated'];
        if (in_array($mainBooking->status, $noOwnerStatuses)) {
            $possessionBlockedReasons[] = 'Plot has been transferred — possession is issued to the new owner only';
        } elseif ($mainBooking->status === 'pending_transfer') {
            $possessionBlockedReasons[] = 'Transfer in progress — possession locked until transfer fee is paid';
        } else {
            $possessionBlockedReasons[] = 'Booking not yet active';
        }
    }

    // ── Booking fees (for ledger display) ─────────────────────────
    $bookingFees = $mainBooking->bookingFees;

    // installment alerts (keep existing code)
    $overdueInstallments  = collect();
    $upcomingInstallments = collect();

    return view('accounts.ledger_view', compact(
        'mainBooking',
        'totalPrice',
        'trueTotal',
        'totalPaidReal',
        'totalCollectedReal',
        'remaining',
        'plotDiscount',
        'paymentDiscountCredits',
        'prog',
        'downDue','procDue','downPaid','procPaid','installPaid','qtrPaid','othersPaid',
        // Monthly
        'totalInstallmentCount','monthlyInstallment','paidInstallmentCount',
        'remainingInstallmentCount','nextInstallmentAmount','hasInstallmentPlan',
        // Quarterly
        'totalQuarterlyCount','quarterlyAmount','paidQuarterlyCount',
        'remainingQuarterlyCount','nextQuarterlyAmount','hasQuarterlyPlan','paidQuarterNos',
        // Transfer
        'transferHistory','pendingTransfer','hasCompletedTransfer','readOnly',
        // Dates
        'nextQuarterDueDate','nextInstallmentDueDate',
        // Installment alerts
        'overdueInstallments','upcomingInstallments',
        // ── NEW ──
        'isOnHold',
        'activeHold',
        'registryFeeRequired','registryFeeCleared','registryFeeBill',
        'developmentFeeRequired','developmentFeeCleared','developmentFeeBill',
        'securityFeeRequired','securityFeeCleared','securityFeeBill',
        'secMonthlyRate','secMonthsTotal','secMonthsPaid','secMonthsUnpaid',
        'secTotalOwed','secTotalPaid','secOutstanding',
        'possessionReady','possessionBlocked','possessionBlockedReasons',
        'plotFullyPaid','allFeesCleared','isFullyPaid',
        'bookingFees',
    ));
}
// ─────────────────────────────────────────────────────────────

public function plotPaymentStore(Request $request): \Illuminate\Http\RedirectResponse
{
    $request->validate([
        'booking_id'       => 'required|exists:bookings,id',
        'amount_paid'      => 'required|numeric|min:0.01',
        'payment_type'     => 'required|in:cash,bank_transfer,cheque,online',
        'payment_category' => 'required|in:down_payment,installment,quarterly_installment,plot_balance,processing_fee,fine,security_fee,maintenance_fee,bifurcation_fee,others',
        'paid_date'        => 'required|date',
        'receipt_no'       => 'nullable|string|max:100|unique:plot_payments,receipt_no',
        'remarks'          => 'nullable|string|max:500',
        'installment_no'   => 'nullable|integer|min:1',
        'quarterly_no'     => 'nullable|integer|min:1',
        'payment_proof'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
    ]);

    return DB::transaction(function () use ($request) {

        $booking = Booking::lockForUpdate()->findOrFail($request->booking_id);

        // ── Guard 1: Booking must be in a payable state ────────────────
        $payableStatuses = ['active', 'pending', 'completed'];
        if (!in_array($booking->status, $payableStatuses)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Booking {$booking->customer_booking_id} is in '{$booking->status}' status — payments cannot be recorded.");
        }

        // ── Guard 2: Hold check ────────────────────────────────────────
        if ($booking->isOnHold()) {
            $hold = \App\Models\BookingHold::where('booking_id', $booking->id)
                                           ->where('status', 'hold')->latest()->first();
            return redirect()->back()
                ->withInput()
                ->with('error', "Payment blocked — Booking {$booking->customer_booking_id} is ON HOLD." .
                    ($hold?->remarks ? " Reason: {$hold->remarks}" : ''));
        }

        // ── Guard 3: total_price sanity ────────────────────────────────
        // If total_price = 0, booking is a cash/gift — don't auto-complete
        $totalPrice = (float)($booking->total_price ?? 0);

        // ── True total: use schedule-based if quarterly wasn't in total_price ──
        $scheduleTotal = (float)($booking->down_payment ?? 0)
            + ((int)($booking->total_installments  ?? 0) * (float)($booking->monthly_installment  ?? 0))
            + ((int)($booking->quarterly_installments ?? 0) * (float)($booking->quarterly_amount ?? 0));
        $trueTotal = max($totalPrice, $scheduleTotal);

        // ── Categories that count toward the plot balance ──────────────
        $plotPriceCats = ['down_payment', 'installment', 'quarterly_installment', 'plot_balance', 'others'];

        $discSentinel = 'Settlement discount — waived amount (not collected).';

        // ── Current real cash paid BEFORE this new payment ────────────
        $alreadyPaid = PlotPayment::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
            ->sum('amount_paid');

        // Previous payment-time discount credits (old DISC- + new discount_amount)
        $alreadyDiscounted = (float) PlotPayment::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->where('remarks', '=', $discSentinel)
            ->sum('amount_paid')
            + (float) PlotPayment::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
            ->sum('discount_amount');

        // Offer/plot discount is already in total_price — do NOT subtract again.
        $remaining = max(0, $trueTotal - $alreadyPaid - $alreadyDiscounted);
        $newAmount = (float)$request->amount_paid;

        // ── Tolerance: PKR 10 for rounding differences ────────────────
        $tolerance = 10;

        // ── Guard 4: Prevent overpayment for plot-balance categories ──
        // Exception: processing_fee, fine, security_fee etc. are extras — no ceiling.
        if (
            in_array($request->payment_category, $plotPriceCats) &&
            $trueTotal > 0 &&
            $remaining <= $tolerance
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Plot balance is already fully paid (PKR " . number_format($alreadyPaid) . " cash + PKR " . number_format($alreadyDiscounted) . " settlement discounts of PKR " . number_format($trueTotal) . "). No further plot price payments are needed.");
        }

        // Warn but allow if paying more than remaining (soft guard — you can remove if needed)
        // In case they want to record a partial payment for down_payment separately
        // Just cap it silently to the remaining if overpaying:
        // if (in_array($request->payment_category, $plotPriceCats) && $totalPrice > 0 && $newAmount > $remaining) {
        //     $newAmount = $remaining; // optional: cap to remaining
        // }

        // ── Generate receipt number if blank ──────────────────────────
        $receiptNo = $request->receipt_no
            ?? ('ZV-' . strtoupper(substr($booking->customer_booking_id, -4)) . '-' . date('Ymd') . '-' . rand(100, 999));

        // ── Handle payment proof upload ────────────────────────────────
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        // ── Store the payment ──────────────────────────────────────────
        $payment = PlotPayment::create([
            'booking_id'       => $booking->id,
            'plot_id'          => $booking->plot_id,
            'receipt_no'       => $receiptNo,
            'amount_paid'      => $newAmount,
            'payment_type'     => $request->payment_type,
            'payment_category' => $request->payment_category,
            'installment_no'   => $request->installment_no,
            'quarterly_no'     => $request->quarterly_no,
            'paid_date'        => $request->paid_date,
            'status'           => 'paid',
            'remarks'          => $request->remarks,
            'payment_proof'    => $proofPath,
        ]);

        // ── Recalculate total paid AFTER this payment ──────────────────
        $totalPaidNow = PlotPayment::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
            ->sum('amount_paid');
        $totalDiscountedNow = (float) PlotPayment::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->where('remarks', '=', $discSentinel)
            ->sum('amount_paid')
            + (float) PlotPayment::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
            ->sum('discount_amount');

        $remainingNow = max(0, $trueTotal - $totalPaidNow - $totalDiscountedNow);

        // ── Mark plot as sold on first down payment ───────────────────
        if ($request->payment_category === 'down_payment') {
            Plot::where('id', $booking->plot_id)->update(['status' => 'sold']);
        }

        // ── Update booking status ──────────────────────────────────────
        // RULE: Only mark 'completed' when ALL of the following are true:
        //   1. total_price > 0 (not a free/gift plot)
        //   2. totalPaidNow >= total_price (price fully covered)
        //   3. Monthly installments fully paid (if a monthly plan exists)
        //   4. Quarterly installments fully paid (if a quarterly plan exists)
        //   5. Current status is 'active' or 'pending'

        // ── Check each installment schedule independently ──────────────
        $totalInstPlan = (int)($booking->total_installments    ?? 0);
        $totalQtrPlan  = (int)($booking->quarterly_installments ?? 0);

        $paidMonthlyCount = $totalInstPlan > 0
            ? PlotPayment::where('booking_id', $booking->id)->where('status','paid')->where('payment_category','installment')->count()
            : 0;
        $paidQuarterlyCount = $totalQtrPlan > 0
            ? PlotPayment::where('booking_id', $booking->id)->where('status','paid')->where('payment_category','quarterly_installment')->count()
            : 0;

        // Monthly done if: no plan, OR all installments paid
        $monthlyDone   = ($totalInstPlan   === 0 || $paidMonthlyCount   >= $totalInstPlan);
        // Quarterly done if: no plan, OR all quarterly paid
        $quarterlyDone = ($totalQtrPlan    === 0 || $paidQuarterlyCount >= $totalQtrPlan);

        // Fully paid: amount within tolerance AND all count-based schedules complete
        $fullyPaidNow = ($remainingNow <= $tolerance) && $monthlyDone && $quarterlyDone;

        if ($trueTotal > 0 && $fullyPaidNow && in_array($booking->status, ['active', 'pending'])) {
            $booking->update(['status' => 'completed']);
            $statusMsg = ' Booking is now FULLY PAID and marked as completed.';
        } elseif (
            $booking->status === 'pending' &&
            $totalPaidNow > 0 &&
            !$fullyPaidNow
        ) {
            // Move from pending → active once first payment is received
            $booking->update(['status' => 'active']);
            $statusMsg = ' Booking is now active.';
        } elseif ($remainingNow <= $tolerance && !$monthlyDone || $remainingNow <= $tolerance && !$quarterlyDone) {
            // Amount is covered but installment count not yet complete
            $pendingParts = [];
            if (!$monthlyDone)   $pendingParts[] = ($totalInstPlan - $paidMonthlyCount)   . ' monthly installment(s)';
            if (!$quarterlyDone) $pendingParts[] = ($totalQtrPlan  - $paidQuarterlyCount)  . ' quarterly installment(s)';
            $statusMsg = ' Note: ' . implode(' and ', $pendingParts) . ' still pending — booking not yet marked as completed.';
        } else {
            $statusMsg = '';
        }

        return redirect()
            ->route('ledger.view', $booking->id)
            ->with('success',
                "Payment of PKR " . number_format($newAmount) . " recorded. " .
                "Receipt: {$receiptNo}. " .
                "Remaining: PKR " . number_format($remainingNow) . "." .
                $statusMsg
            )
            ->with('last_payment_id', $payment->id);
    });
}

public function lumpSumSettle(Request $request, string $id): \Illuminate\Http\RedirectResponse
{
    $request->validate([
        'discount_amount' => 'nullable|numeric|min:0',
        'payment_type'    => 'required|in:cash,bank_transfer,cheque,online',
        'paid_date'       => 'required|date',
        'receipt_no'      => 'nullable|string|max:100|unique:plot_payments,receipt_no',
        'remarks'         => 'nullable|string|max:500',
    ]);

    return DB::transaction(function () use ($request, $id) {
        $booking = Booking::lockForUpdate()->findOrFail($id);

        if (!in_array($booking->status, ['active', 'pending'])) {
            return redirect()->back()->with('error', 'Booking is not in an active/pending state.');
        }
        if ($booking->isOnHold()) {
            return redirect()->back()->with('error', 'Booking is on hold — payments are blocked.');
        }

        $plotPriceCats  = ['down_payment', 'installment', 'quarterly_installment', 'plot_balance', 'others'];
        $discSentinel   = 'Settlement discount — waived amount (not collected).';
        $totalPrice     = (float)($booking->total_price ?? 0);
        $scheduleTotal  = (float)($booking->down_payment ?? 0)
            + ((int)($booking->total_installments    ?? 0) * (float)($booking->monthly_installment ?? 0))
            + ((int)($booking->quarterly_installments ?? 0) * (float)($booking->quarterly_amount ?? 0));
        $trueTotal = max($totalPrice, $scheduleTotal);

        // Real cash paid (exclude old DISC- records)
        $alreadyPaid = PlotPayment::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
            ->sum('amount_paid');
        // Old DISC- records + new discount_amount credits
        $alreadyDiscounted = (float) PlotPayment::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->where('remarks', '=', $discSentinel)
            ->sum('amount_paid')
            + (float) PlotPayment::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
            ->sum('discount_amount');
        // Offer/plot discount already baked into total_price — do NOT subtract again.
        $remaining = max(0, $trueTotal - $alreadyPaid - $alreadyDiscounted);

        if ($remaining <= 0) {
            return redirect()->back()->with('error', 'Plot balance is already fully paid.');
        }

        $discountAmt = max(0, (float)($request->discount_amount ?? 0));
        if ($discountAmt >= $remaining) {
            return redirect()->back()->with('error', 'Discount cannot equal or exceed the remaining balance.');
        }

        $finalAmount = round($remaining - $discountAmt, 2);

        $receiptNo = $request->receipt_no
            ?? ('ZV-LS-' . strtoupper(substr($booking->customer_booking_id, -4)) . '-' . date('Ymd') . '-' . rand(100, 999));

        $baseRemark = $discountAmt > 0
            ? 'Lump Sum Settlement. Early-payment discount: PKR ' . number_format($discountAmt, 2) . '.'
            : 'Lump Sum Settlement.';
        $remarks = trim($baseRemark . ' ' . ($request->remarks ?? ''));

        // Record the actual cash received; waived discount stored in discount_amount column
        PlotPayment::create([
            'booking_id'       => $booking->id,
            'plot_id'          => $booking->plot_id,
            'receipt_no'       => $receiptNo,
            'amount_paid'      => $finalAmount,
            'discount_amount'  => $discountAmt,
            'payment_type'     => $request->payment_type,
            'payment_category' => 'plot_balance',
            'paid_date'        => $request->paid_date,
            'status'           => 'paid',
            'remarks'          => $remarks,
        ]);

        $booking->update(['status' => 'completed']);

        $msg = 'Lump sum payment of PKR ' . number_format($finalAmount) . ' recorded.';
        if ($discountAmt > 0) {
            $msg .= ' Settlement discount of PKR ' . number_format($discountAmt) . ' applied.';
        }
        $msg .= ' Booking marked as COMPLETED.';

        return redirect()->route('ledger.view', $booking->id)->with('success', $msg);
    });
}

public function plotPaymentDestroy(int $id): \Illuminate\Http\RedirectResponse
{
    $payment = PlotPayment::findOrFail($id);
    $payment->delete();
    return back()->with('success', 'Payment record deleted.');
}

public function plotPaymentRefund(\Illuminate\Http\Request $request, int $id): \Illuminate\Http\RedirectResponse
{
    $request->validate([
        'refund_amount' => 'required|numeric|min:0.01',
        'refund_date'   => 'required|date',
        'refund_note'   => 'nullable|string|max:500',
    ]);

    $payment = PlotPayment::findOrFail($id);
    $payment->update([
        'is_refunded'   => true,
        'refund_amount' => $request->refund_amount,
        'refund_date'   => $request->refund_date,
        'refund_note'   => $request->refund_note,
    ]);

    return back()->with('success', 'Refund of PKR '.number_format($request->refund_amount).' recorded for this payment.');
}

public function plotPaymentUpdate(\Illuminate\Http\Request $request, int $id): \Illuminate\Http\RedirectResponse
{
    $payment = PlotPayment::findOrFail($id);

    $request->validate([
        'receipt_no'       => 'nullable|string|max:100|unique:plot_payments,receipt_no,' . $id,
        'paid_date'        => 'required|date',
        'payment_category' => 'required|in:down_payment,installment,quarterly_installment,plot_balance,processing_fee,fine,security_fee,maintenance_fee,bifurcation_fee,others',
        'payment_type'     => 'required|in:cash,bank_transfer,cheque,online',
        'amount_paid'      => 'required|numeric|min:0.01',
        'installment_no'   => 'nullable|integer|min:1',
        'quarterly_no'     => 'nullable|integer|min:1',
        'bank_ref'         => 'nullable|string|max:100',
        'remarks'          => 'nullable|string|max:500',
        'status'           => 'required|in:paid,pending',
    ]);

    $payment->update([
        'receipt_no'       => $request->receipt_no,
        'paid_date'        => $request->paid_date,
        'payment_category' => $request->payment_category,
        'payment_type'     => $request->payment_type,
        'amount_paid'      => $request->amount_paid,
        'installment_no'   => $request->installment_no ?: null,
        'quarterly_no'     => $request->quarterly_no ?: null,
        'bank_ref'         => $request->bank_ref,
        'remarks'          => $request->remarks,
        'status'           => $request->status,
    ]);

    return redirect()
        ->route('ledger.view', $payment->booking_id)
        ->with('success', 'Payment #' . ($payment->receipt_no ?? $id) . ' updated successfully.');
}

 public function paymentReceipt(int $id)
{
    // 1. Fetch data and ORDER payments
    $payment = PlotPayment::with([
        'booking.customer',
        'booking.plot',
        'booking.payments' => function($query) {
            $query->orderBy('paid_date', 'asc')->orderBy('id', 'asc');
        }
    ])->findOrFail($id);

    $allPayments = $payment->booking->payments;

    // --- NEW SMART LABEL LOGIC (Using Category) ---
    $category = trim(strtolower($payment->payment_category));
    $paymentLabel = "";

    if (str_contains($category, 'down')) {
        $paymentLabel = "Down Payment";
    } elseif (str_contains($category, 'reg')) {
        $paymentLabel = "Registration Fee";
    } else {
        // Count how many 'installments' exist before or at this one
        $installmentNumber = $allPayments->where('id', '<=', $payment->id)
            ->filter(function($p) {
                return str_contains(strtolower($p->payment_category), 'installment');
            })->count();

        $paymentLabel = "Installment #" . $installmentNumber;
    }

    // --- MATH LOGIC ---
    $amountInWords = amountInWords($payment->amount_paid);
    $totalPlotPrice = $payment->booking->plot->base_price ?? 0;
    $totalPaid = $allPayments->sum('amount_paid');
    $remainingBalance = $totalPlotPrice - $totalPaid;

    // --- GENERATE QR CODE ---
    $verificationUrl = URL::signedRoute('payment.receipt', ['id' => $id]);
    $renderer = new ImageRenderer(new RendererStyle(100), new SvgImageBackEnd());
    $writer = new Writer($renderer);
    $qrCode = base64_encode($writer->writeString($verificationUrl));

    // --- PREPARE DATA PACKAGE ---
    $data = [
        'payment'            => $payment,
        'paymentLabel'       => $paymentLabel,
        'remainingBalance'   => $remainingBalance,
        'amountInWords'      => $amountInWords,
        'qrCode'             => $qrCode,
        // If it's a down payment, we just pass the label, otherwise the number
        'currentInstallment' => $paymentLabel,
        'totalInstallments'  => $payment->booking->total_installments,
    ];

    // --- VIEW LOGIC ---
    if (request()->hasValidSignature()) {
        return view('accounts.digital_receipt_verify', $data);
    }

    if (!auth()->check()) {
        abort(403, 'Invalid Link.');
    }

    return view('accounts.payment_receipt', $data);
}
public function customerCard($bookingId)
{
    $initialBooking = Booking::findOrFail($bookingId);

    // Note: Use plural 'bookings' if your relationship is HasMany
$customer = Customer::with([
    'booking.plot.category', // Changed to singular to match your model
    'booking.payments' => function($query) {
        $query->where('status', 'paid')->orderBy('paid_date', 'desc');
    }
])->findOrFail($initialBooking->customer_id);

    if (request()->hasValidSignature()) {
        // Change this to pass $customer AND $initialBooking renamed as $booking
        return view('accounts.digital_card_verify', ['customer' => $customer, 'booking' => $initialBooking]);
    }

    if (!auth()->check()) {
        abort(403, 'Unauthorized access.');
    }

    $verificationUrl = URL::signedRoute('customer.card', ['bookingId' => $bookingId]);

    // Change 'initialBooking' to 'booking' here too
    return view('accounts.customer_card', ['customer' => $customer, 'booking' => $initialBooking, 'verificationUrl' => $verificationUrl]);
}



public function financeReport(Request $request)
{
    $query = PlotPayment::with(['booking.customer', 'booking.plot']);

    if ($request->filled('date_from'))    $query->whereDate('paid_date', '>=', $request->date_from);
    if ($request->filled('date_to'))      $query->whereDate('paid_date', '<=', $request->date_to);
    if ($request->filled('category'))     $query->where('payment_category', $request->category);
    if ($request->filled('payment_mode')) $query->where('payment_type', $request->payment_mode);
    if ($request->filled('block')) {
        $query->whereHas('booking.plot', fn($q) => $q->where('block', $request->block));
    }



    $payments = $query->orderByDesc('paid_date')->get();

    // ── PLOT-PRICE CATEGORIES (reduce the plot balance) ───────────────────
    $plotPriceCategories = ['down_payment', 'installment', 'quarterly_installment', 'plot_balance', 'others'];

    // ── Total Project Value ───────────────────────────────────────────────
    // Only count ORIGINAL bookings (no parent) to avoid double-counting.
    // Transfer bookings always have parent_booking_id set; original bookings don't.
    // We cannot use booking_type because the DB column default is 'First Allotment',
    // so transfer bookings created without setting it look like originals.
    $totalPriceAllBookings = Booking::whereNull('parent_booking_id')
        ->where('status', '!=', 'cancelled')
        ->sum('total_price');

    $discSentinel = 'Settlement discount — waived amount (not collected).';

    // ── Real cash collected — EXCLUDES settlement-discount (DISC-) records and cancelled bookings ──
    $totalPlotReceived = PlotPayment::where('status', 'paid')
        ->whereIn('payment_category', $plotPriceCategories)
        ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
        ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
        ->sum('amount_paid');

    // ── Plot-level discount credits ──────────────────────────────────────
    $financeDiscount = (float) \Illuminate\Support\Facades\DB::table('bookings')
        ->join('plots', 'bookings.plot_id', '=', 'plots.id')
        ->whereNull('bookings.parent_booking_id')
        ->where('bookings.status', '!=', 'cancelled')
        ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(plots.discount_amount, 0)'));

    // ── Payment-time (settlement) discount credits ───────────────────────
    $financePaymentDiscount = (float) PlotPayment::where('status', 'paid')
        ->whereIn('payment_category', $plotPriceCategories)
        ->where('remarks', '=', $discSentinel)
        ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
        ->sum('amount_paid')
        + (float) PlotPayment::where('status', 'paid')
        ->whereIn('payment_category', $plotPriceCategories)
        ->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', '!=', $discSentinel))
        ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
        ->sum('discount_amount');

    // ── Pending — per-booking, ACTIVE/PENDING only ──────────────────────
    // Completed/transferred bookings have remaining = 0 by definition.
    // We compute it per-booking in SQL so a completed booking with a small
    // unrecorded settlement gap does not inflate the remaining figure.
    $totalPending = (float) \Illuminate\Support\Facades\DB::selectOne("
        SELECT COALESCE(SUM(GREATEST(0,
            b.total_price
            - COALESCE(paid.cash_total, 0)
            - COALESCE(disc.disc_total, 0)
        )), 0) AS pending
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
    ", [$discSentinel, $discSentinel])->pending ?? 0;

    // ── Total all-time Zamar collection (all categories) — excl. cancelled
    $totalPaidAllTime = PlotPayment::where('status', 'paid')
        ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
        ->sum('amount_paid');

    // ── Filtered paid stats (respects date/category/block filters) ────────
    // Exclude payments from cancelled bookings
    $paidPayments   = $payments->where('status', 'paid')
                               ->filter(fn($p) => $p->booking?->status !== 'cancelled');
    $totalCollected = $paidPayments->sum('amount_paid');
    $paidTxnCount   = $paidPayments->count();
    $totalTxns      = $payments->count();
    $avgPayment     = $paidTxnCount > 0
        ? (int) round($totalCollected / $paidTxnCount)
        : 0;



    $blocks = Plot::whereNotNull('block')->distinct()->orderBy('block')->pluck('block');

    // ── Installment alerts ────────────────────────────────────────────────
    $graceDays = (int) \App\Models\SystemConfig::get('installment_grace_days', 10);

    $overdueInstallments  = collect();
    $upcomingInstallments = collect();

    $installmentBookings = Booking::with([
            'payments' => fn($q) => $q->where('payment_category', 'installment')
                                      ->where('status', 'paid')
                                   ,
            'plot',
            'customer',
        ])
        ->whereIn('status', ['active', 'pending'])
        ->where('total_installments', '>', 0)
        ->whereNotNull('monthly_installment')
        ->get();

    foreach ($installmentBookings as $booking) {
        $paidCount = $booking->payments->count();

        if ($paidCount >= $booking->total_installments) continue;

        $nextInstallmentNo = $paidCount + 1;
        $dueDate           = Carbon::parse($booking->booking_date)->addMonths($nextInstallmentNo);
        $graceDeadline     = $dueDate->copy()->addDays($graceDays);
        $today             = Carbon::today();
        $daysUntilDue      = (int) $today->diffInDays($dueDate, false);

        $record = (object) [
            'booking'             => $booking,
            'next_installment'    => $nextInstallmentNo,
            'due_date'            => $dueDate,
            'monthly_installment' => $booking->monthly_installment ?? 0,
            'days_overdue'        => 0,
            'days_until_due'      => max(0, $daysUntilDue),
        ];

        if ($today->gt($graceDeadline)) {
            $record->days_overdue = (int) $graceDeadline->diffInDays($today);
            $overdueInstallments->push($record);
        } elseif ($daysUntilDue <= 30) {
            $upcomingInstallments->push($record);
        }
    }

    $overdueInstallments  = $overdueInstallments->sortByDesc('days_overdue')->values();
    $upcomingInstallments = $upcomingInstallments->sortBy('days_until_due')->values();

    // ── All active bookings (for any view-level calculations) ────────────
    $allBookings = Booking::with('payments')
        ->whereNotIn('status', ['cancelled', 'transferred', 'swapped', 'plot_relocated'])
        ->get();

    // ── Cancellation stats ───────────────────────────────────────────────
    $cancelledCount        = Booking::where('status', 'cancelled')->count();
    $cancelledRefundTotal  = Booking::where('status', 'cancelled')->sum('cancellation_refund');
    $cancelledCollected    = PlotPayment::where('status', 'paid')
        ->whereHas('booking', fn($q) => $q->where('status', 'cancelled'))
        ->sum('amount_paid');

    // ── Discount stats ───────────────────────────────────────────────────
    $totalPlotDiscounts    = $financeDiscount;
    $totalPaymentDiscounts = $financePaymentDiscount;
    $totalDiscount         = $totalPlotDiscounts + $totalPaymentDiscounts;

    $discountedBookingsCount = (int) \Illuminate\Support\Facades\DB::table('bookings')
        ->join('plots', 'bookings.plot_id', '=', 'plots.id')
        ->whereNull('bookings.parent_booking_id')
        ->where('bookings.status', '!=', 'cancelled')
        ->where('plots.discount_amount', '>', 0)
        ->count();

    $settlementDiscountCount = (int) PlotPayment::where('status', 'paid')
        ->where(function ($q) use ($discSentinel) {
            $q->where('remarks', '=', $discSentinel)
              ->orWhere('discount_amount', '>', 0);
        })
        ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
        ->distinct('booking_id')
        ->count('booking_id');

    // Gross value = contracted price + plot discounts = original value before concessions
    $grossProjectValue = $totalPriceAllBookings + $totalPlotDiscounts;

    return view('accounts.finance_report', compact(
        'payments',
        'allBookings',
        'blocks',
        'overdueInstallments',
        'upcomingInstallments',
        'totalCollected',
        'totalPending',
        'totalTxns',
        'paidTxnCount',
        'avgPayment',
        'totalPlotReceived',
        'totalPaidAllTime',
        'totalPriceAllBookings',
        'cancelledCount',
        'cancelledRefundTotal',
        'cancelledCollected',
        'totalDiscount',
        'totalPlotDiscounts',
        'totalPaymentDiscounts',
        'discountedBookingsCount',
        'settlementDiscountCount',
        'grossProjectValue',
    ));
}




}


