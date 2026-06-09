<?php

namespace App\Http\Controllers;

use App\Helpers\AppConfig;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Plot;
use App\Models\PlotTransfer;
use App\Models\BookingFee;
use App\Models\FeePayment;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Traits\HasSocietyConfig;


class TransferController extends Controller
{
     use HasSocietyConfig;
    public function index(Request $request)
    {
        $query = PlotTransfer::with([
            'fromCustomer',
            'toCustomer',
            'plot',
            'swapPlot',
            'fromBooking.bookingFees',
            'fromBooking.plot',
            'toBooking',
        ])->latest();

        if ($request->filled('type'))   $query->where('transfer_type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('deed_no', 'like', "%$s%")
                    ->orWhereHas('fromCustomer', fn($q2) => $q2->where('name', 'like', "%$s%"))
                    ->orWhereHas('toCustomer',   fn($q2) => $q2->where('name', 'like', "%$s%"))
                    ->orWhereHas('plot',          fn($q2) => $q2->where('plot_number', 'like', "%$s%"));
            });
        }

        $transfers = $query->paginate(15)->withQueryString();

        $plotPriceCats = ['down_payment','installment','quarterly_installment','plot_balance','others'];

        // Stats
        $stats = [
            'total'     => PlotTransfer::count(),
            'pending'   => PlotTransfer::where('status', 'pending')->count(),
            'completed' => PlotTransfer::where('status', 'completed')->count(),

            // Actual transfer fees collected (from booking_fees table)
            'fees_collected' => \App\Models\BookingFee::where('fee_type', 'transfer')
                ->where('status', 'paid')
                ->sum('paid_amount'),

            // Total balance that was transferred across all completed transfers
            'total_balance_transferred' => PlotTransfer::where('status', 'completed')
                ->whereIn('transfer_type', ['ownership', 'partial'])
                ->sum('remaining_balance_transferred'),

            // Current remaining on all active to_bookings (chain holders still paying)
            // = sum of (total_price - paid) for bookings that came from a transfer
            'chain_remaining' => \App\Models\Booking::whereNotNull('parent_booking_id')
                ->whereIn('status', ['active', 'pending_transfer', 'partial_transferred'])
                ->get()
                ->sum(function ($b) use ($plotPriceCats) {
                    $paid = \App\Models\PlotPayment::where('booking_id', $b->id)
                        ->where('status', 'paid')
                        ->whereIn('payment_category', $plotPriceCats)
                        ->sum('amount_paid');
                    return max(0, (float)$b->total_price - $paid);
                }),
        ];

        return view('transfers.transfer_index', compact('transfers', 'stats'));
    }
public function search(Request $request): \Illuminate\View\View
{
    $bookings = collect();

    if ($request->filled('q')) {
        $originalQ = trim($request->q);
        // Strip spaces AND dashes for ID/CNIC/plot-number matching
        $cleanQ = str_replace([' ', '-'], '', $originalQ);

        $bookings = Booking::with(['customer', 'plot', 'payments', 'bookingFees', 'activeHold'])
            ->whereIn('status', [
                'active', 'completed', 'booked', 'pending',
                'transferred', 'pending_transfer', 'swapped', 'plot_relocated'
            ])
            ->where(function ($query) use ($cleanQ, $originalQ) {
                // Booking ID — match with or without dashes
                $query->where('customer_booking_id', 'like', "%$originalQ%")
                      ->orWhereRaw("REPLACE(REPLACE(customer_booking_id, '-', ''), ' ', '') LIKE ?", ["%$cleanQ%"])
                      ->orWhere('remarks', 'like', "%$originalQ%")

                // Customer — name, CNIC, mobile, email
                ->orWhereHas('customer', function ($c) use ($cleanQ, $originalQ) {
                    $c->where('name', 'like', "%$originalQ%")
                      ->orWhereRaw("REPLACE(REPLACE(cnic, '-', ''), ' ', '') LIKE ?", ["%$cleanQ%"])
                      ->orWhereRaw("REPLACE(mobile, ' ', '') LIKE ?", ["%$cleanQ%"])
                      ->orWhereRaw("REPLACE(phone,  ' ', '') LIKE ?", ["%$cleanQ%"])
                      ->orWhere('email', 'like', "%$originalQ%");
                })

                // Plot — number (with/without dashes), block, sector, society
                ->orWhereHas('plot', function ($p) use ($cleanQ, $originalQ) {
                    $p->where('plot_number', 'like', "%$originalQ%")
                      ->orWhereRaw("REPLACE(REPLACE(plot_number, '-', ''), ' ', '') LIKE ?", ["%$cleanQ%"])
                      ->orWhere('block',   'like', "%$originalQ%")
                      ->orWhere('sector',  'like', "%$originalQ%")
                      ->orWhere('society', 'like', "%$originalQ%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    return view('transfers.transfer_search', compact('bookings'));
}
    // ── Create: Show form ─────────────────────────────────────
public function create(string $id): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
{
    $fromBooking = Booking::with(['customer', 'plot', 'payments'])
        ->findOrFail($id);

    // If already transferred, walk the chain to find the current active booking
    if (in_array($fromBooking->status, ['transferred', 'partial_transferred'])) {
        $current  = $fromBooking;
        $visited  = [$fromBooking->id];
        $maxHops  = 20; // safety guard against infinite loops

        while (in_array($current->status, ['transferred', 'partial_transferred']) && $maxHops-- > 0) {
            $nextTransfer = \App\Models\PlotTransfer::where('from_booking_id', $current->id)
                ->whereNotNull('to_booking_id')
                ->where('status', 'completed')
                ->latest('id')
                ->first();

            if (!$nextTransfer || in_array($nextTransfer->to_booking_id, $visited)) break;

            $visited[] = $nextTransfer->to_booking_id;
            $next = Booking::with(['customer', 'plot', 'payments'])->find($nextTransfer->to_booking_id);
            if (!$next) break;
            $current = $next;
        }

        if ($current->id !== $fromBooking->id) {
            return redirect()
                ->route('transfers.create', $current->id)
                ->with('info', "Booking {$fromBooking->customer_booking_id} was previously transferred. Showing current booking: {$current->customer_booking_id}.");
        }

        // Chain ended but booking is still in a non-transferable state
        return redirect()->route('transfers.search')
            ->with('error', "Booking {$fromBooking->customer_booking_id} is in '{$fromBooking->status}' state and no active successor booking was found.");
    }

    // Block other terminal states (cancelled, swapped, etc.)
    $blocked = ['swapped','plot_relocated','pending_transfer','cancelled'];
    if (in_array($fromBooking->status, $blocked)) {
        return redirect()->route('transfers.search')
            ->with('error', "Booking {$fromBooking->customer_booking_id} is in '{$fromBooking->status}' state and cannot be transferred.");
    }

    // Block on-hold bookings
    if ($fromBooking->isOnHold()) {
        return redirect()->route('transfers.search')
            ->with('error', "Booking {$fromBooking->customer_booking_id} is currently on hold. Release the hold before transferring.");
    }

    // Calculate financials
    $plotCats  = ['down_payment','quarterly_installment','installment','plot_balance','others'];
    $totalPaid = $fromBooking->payments
        ->where('status', 'paid')
        ->whereIn('payment_category', $plotCats)
        ->sum('amount_paid');
    $totalDisc = $fromBooking->payments
        ->where('status', 'paid')
        ->whereIn('payment_category', $plotCats)
        ->sum('discount_amount');

    $remainingBalance = max(0, $fromBooking->total_price - $totalPaid - $totalDisc);
    $downPaid         = $fromBooking->payments->where('payment_category','down_payment')->where('status','paid')->sum('amount_paid');
    $remainingDown    = max(0, ($fromBooking->down_payment ?? 0) - $downPaid);

    $paidInstCount    = $fromBooking->payments->where('payment_category','installment')->where('status','paid')->count();
    $paidQtrCount     = $fromBooking->payments->where('payment_category','quarterly_installment')->where('status','paid')->count();
    $remainingInst    = max(0, ($fromBooking->total_installments ?? 0) - $paidInstCount);
    $remainingQtr     = max(0, ($fromBooking->quarterly_installments ?? 0) - $paidQtrCount);

    // For swap: only completed bookings can swap
    $swapBookings = Booking::with(['customer','plot'])
        ->where('status', 'completed')
        ->where('id', '!=', $fromBooking->id)
        ->get();

    // For ownership/partial: all customers
    $customers = Customer::orderBy('name')->get();

    $deedNo             = PlotTransfer::generateDeedNo();
    $defaultTransferFee = (float) \App\Models\SystemConfig::get('default_transfer_fee', 0);
    $transferCount      = (int)($fromBooking->plot->transfer_count ?? 0);
    $transfersRemaining = max(0, 5 - $transferCount);

    // Block if development or security fee is outstanding. Registry is optional.
    $blockingMessages = [];

    $devFeeCheck = \App\Models\BookingFee::where('booking_id', $fromBooking->id)
        ->where('fee_type', 'development')->first();
    if (($fromBooking->has_development_fee || $devFeeCheck) && $devFeeCheck) {
        $devPaid = (float)($devFeeCheck->paid_amount ?? 0);
        $devAmt  = (float)($devFeeCheck->amount ?? 0);
        if ($devPaid == 0) {
            $blockingMessages[] = 'Development Fee has never been paid' . ($devAmt > 0 ? ' (PKR '.number_format($devAmt).' due)' : '');
        } elseif ($devAmt > 0 && $devPaid < $devAmt) {
            $blockingMessages[] = 'Development Fee — PKR '.number_format($devAmt - $devPaid).' outstanding';
        }
    }

    $secFeeCheck = \App\Models\BookingFee::where('booking_id', $fromBooking->id)
        ->where('fee_type', 'security')->first();
    if ($fromBooking->has_security_fee && $secFeeCheck && (float)$secFeeCheck->paid_amount == 0) {
        $blockingMessages[] = 'Security Fee has never been paid — at least one month must be paid before transferring.';
    }

    if (!empty($blockingMessages)) {
        return redirect()->route('transfers.search')
            ->with('error', 'Transfer blocked: ' . implode('; ', $blockingMessages) . '. Please clear these fees before proceeding.');
    }

    return view('transfers.transfer_create', compact(
        'fromBooking',
        'remainingBalance',
        'remainingDown',
        'remainingInst',
        'remainingQtr',
        'paidInstCount',
        'paidQtrCount',
        'totalPaid',
        'totalDisc',
        'swapBookings',
        'customers',
        'deedNo',
        'defaultTransferFee',
        'transferCount',
        'transfersRemaining'
    ));
}

    // ── Store: Process transfer ───────────────────────────────
public function store(Request $request): \Illuminate\Http\RedirectResponse
{
    $request->validate([
        'transfer_type'        => 'required|in:ownership,swap,partial,internal',
        'from_booking_id'      => 'required|exists:bookings,id',
        'transfer_date'        => 'required|date',
        'transfer_fee'         => 'required|numeric|min:1',
        'reason'               => 'nullable|string|max:1000',
        'notes'                => 'nullable|string|max:1000',
        'to_customer_id'       => 'required_if:transfer_type,ownership,partial',
        'swap_from_booking_id' => 'required_if:transfer_type,swap',
        'ownership_percentage' => 'required_if:transfer_type,partial|nullable|numeric|min:1|max:99',
        'new_block'            => 'required_if:transfer_type,internal',
        'new_plot_number'      => 'required_if:transfer_type,internal',
        'witness1_name'        => 'nullable|string|max:255',
        'witness1_cnic'        => 'nullable|string|max:20',
        'witness1_address'     => 'nullable|string|max:500',
        'witness2_name'        => 'nullable|string|max:255',
        'witness2_cnic'        => 'nullable|string|max:20',
        'witness2_address'     => 'nullable|string|max:500',
    ]);

    $blockedStatuses = [
        'transferred','partial_transferred','swapped',
        'plot_relocated','pending_transfer','cancelled',
    ];

    try {
        DB::transaction(function () use ($request, $blockedStatuses) {

            $fromBooking = Booking::with(['plot','payments'])
                ->lockForUpdate()
                ->findOrFail($request->from_booking_id);

            if (in_array($fromBooking->status, $blockedStatuses)) {
                throw new \Exception(
                    "Booking {$fromBooking->customer_booking_id} is in '{$fromBooking->status}' state and cannot be transferred."
                );
            }

            // ── Block if development or security fee is outstanding. ──
            $storeBlocks = [];

            // Development Fee Check
            $devFeeChk = \App\Models\BookingFee::where('booking_id', $fromBooking->id)->where('fee_type','development')->first();
            if (($fromBooking->has_development_fee || $devFeeChk) && $devFeeChk) {
                $devPaid = (float)($devFeeChk->paid_amount ?? 0);
                $devAmt  = (float)($devFeeChk->amount ?? 0);
                if ($devPaid == 0) {
                    $storeBlocks[] = 'Development Fee has never been paid' . ($devAmt > 0 ? ' (PKR '.number_format($devAmt).' due)' : '');
                } elseif ($devAmt > 0 && $devPaid < $devAmt) {
                    $storeBlocks[] = 'Development Fee — PKR '.number_format($devAmt - $devPaid).' outstanding';
                }
            }

            // Security Fee Check (Strict: Must be paid up to transfer date)
            $secFeeChk = \App\Models\BookingFee::where('booking_id', $fromBooking->id)->where('fee_type','security')->first();
            $secRequiredForSettlement = 0;
            if ($fromBooking->has_security_fee && $fromBooking->booking_date) {
                $secMonthlyRate = (float)($fromBooking->plot->security_fee_amount ?? 0);
                if ($secMonthlyRate > 0) {
                    $secStart = \Carbon\Carbon::parse($fromBooking->booking_date)->startOfMonth();
                    $secEnd   = \Carbon\Carbon::parse($request->transfer_date)->startOfMonth();

                    if ($secEnd->gte($secStart)) {
                        $secMonthsTotal = (int)$secStart->diffInMonths($secEnd) + 1;
                        $secRequired    = $secMonthsTotal * $secMonthlyRate;
                        $secRequiredForSettlement = $secRequired;
                        $secTotalPaid   = $secFeeChk ? (float)$secFeeChk->paid_amount : 0;

                        if ($secTotalPaid < $secRequired) {
                            $unpaidMonths = $secMonthsTotal - (int)floor($secTotalPaid / $secMonthlyRate);
                            $outstanding  = $secRequired - $secTotalPaid;
                            $storeBlocks[] = "Security Fee — PKR " . number_format($outstanding) . " outstanding ({$unpaidMonths} month(s) due up to transfer date)";
                        }
                    }
                } elseif (!$secFeeChk || (float)$secFeeChk->paid_amount <= 0) {
                    // Fallback if rate is missing but fee is enabled
                    $storeBlocks[] = 'Security Fee record not found or never paid.';
                }
            }

            if (!empty($storeBlocks)) {
                throw new \Exception('Transfer blocked: ' . implode('; ', $storeBlocks) . '.');
            }

            // ── Transfer limit: max 5 per plot ────────────────────────
            $transferCount = (int)($fromBooking->plot->transfer_count ?? 0);
            if ($transferCount >= 5) {
                throw new \Exception(
                    "Transfer limit reached. Plot #{$fromBooking->plot->plot_number} has already been transferred {$transferCount} times (maximum 5 allowed)."
                );
            }

            // ── Financials ────────────────────────────────────────────
            $plotCats = ['down_payment','quarterly_installment','installment','plot_balance','others'];
            $payments = $fromBooking->payments()->where('status','paid');

            $paidDown  = (clone $payments)->where('payment_category','down_payment')->sum('amount_paid');
            $paidPlot  = (clone $payments)->whereIn('payment_category', $plotCats)->sum('amount_paid');
            $discPlot  = (clone $payments)->whereIn('payment_category', $plotCats)->sum('discount_amount');

            $paidInstCount = $fromBooking->payments()
                ->where('payment_category','installment')
                ->where('status','paid')->count();
            $paidQtrCount  = $fromBooking->payments()
                ->where('payment_category','quarterly_installment')
                ->where('status','paid')->count();

            $remainingBalance = max(0, $fromBooking->total_price - $paidPlot - $discPlot);
            $remainingDown    = max(0, ($fromBooking->down_payment ?? 0) - $paidDown);
            $remainingInst    = max(0, ($fromBooking->total_installments ?? 0) - $paidInstCount);
            $remainingQtr     = max(0, ($fromBooking->quarterly_installments ?? 0) - $paidQtrCount);

            // ── Transfer Financials ──────────────────────────────────────
            $toBookingId = null;
            $carryRegistry = false;
            $carryDevelopment = false;
            $carrySecurity = false;

            if ($request->transfer_type === 'ownership' || $request->transfer_type === 'partial') {
                $pct = ($request->transfer_type === 'partial') ? ($request->ownership_percentage / 100) : 1;

                // One-time fees only carry if unpaid
                $oldRegFee = \App\Models\BookingFee::where('booking_id', $fromBooking->id)->where('fee_type', 'registry')->first();
                $oldDevFee = \App\Models\BookingFee::where('booking_id', $fromBooking->id)->where('fee_type', 'development')->first();

                $carryRegistry    = $fromBooking->has_registry_fee && (!$oldRegFee || !$oldRegFee->is_settled);
                $carryDevelopment = $fromBooking->has_development_fee && (!$oldDevFee || !$oldDevFee->is_settled);

                // Security fee is for everyone if the plot has a rate
                $secRate = (float)($fromBooking->plot->security_fee_amount ?? 0);
                $carrySecurity = $secRate > 0;

                $newBooking = Booking::create([
                    'customer_booking_id'    => 'ZV-'.strtoupper(substr(uniqid(),-5)).'-'.rand(100,999),
                    'customer_id'            => $request->to_customer_id,
                    'plot_id'                => $fromBooking->plot_id,
                    'parent_booking_id'      => $fromBooking->id,
                    'booking_type'           => 'Transfer',
                    'booking_date'           => $request->transfer_date,
                    'total_price'            => round($remainingBalance * $pct, 2),
                    'down_payment'           => null,
                    'total_installments'     => null,
                    'monthly_installment'    => null,
                    'quarterly_installments' => null,
                    'quarterly_amount'       => null,
                    'has_registry_fee'       => $carryRegistry,
                    'has_development_fee'    => $carryDevelopment,
                    'has_security_fee'       => $carrySecurity,
                    'status'                 => ($request->transfer_type === 'ownership' ? 'pending_transfer' : 'partial_transferred'),
                    'created_by'             => auth()->id(),
                ]);

                $toBookingId = $newBooking->id;

                if ($request->transfer_type === 'ownership') {
                    $fromBooking->update(['status' => 'pending_transfer']);
                } else {
                    $fromBooking->update(['status' => 'partial_transferred']);
                }

                // ── Execute Fee Carry & Cleanup ──

                // 1. Registry Fee
                if ($carryRegistry) {
                    $regAmt = (float)($fromBooking->plot->registry_fee_amount ?? 0);
                    if ($regAmt > 0) {
                        \App\Models\BookingFee::create([
                            'booking_id'  => $toBookingId,
                            'fee_type'    => 'registry',
                            'amount'      => $regAmt,
                            'paid_amount' => 0,
                            'status'      => 'pending',
                        ]);
                    }
                    if ($oldRegFee) $oldRegFee->delete();
                    $fromBooking->update(['has_registry_fee' => false]);
                }

                // 2. Development Fee
                if ($carryDevelopment) {
                    $devAmt = (float)($fromBooking->plot->development_fee_amount ?? 0);
                    if ($devAmt > 0) {
                        \App\Models\BookingFee::create([
                            'booking_id'  => $toBookingId,
                            'fee_type'    => 'development',
                            'amount'      => $devAmt,
                            'paid_amount' => 0,
                            'status'      => 'pending',
                        ]);
                    }
                    if ($oldDevFee) $oldDevFee->delete();
                    $fromBooking->update(['has_development_fee' => false]);
                }

                // 3. Security Fee
                if ($carrySecurity) {
                    \App\Models\BookingFee::create([
                        'booking_id'  => $toBookingId,
                        'fee_type'    => 'security',
                        'amount'      => $secRate,
                        'paid_amount' => 0,
                        'status'      => 'pending',
                    ]);

                    // Mark Seller's security fee as settled up to transfer date.
                    // We sum actual payments to ensure accuracy (fixing the "not counting" issue).
                    if ($secFeeChk) {
                        $actualPaidA = (float)\App\Models\FeePayment::where('booking_fee_id', $secFeeChk->id)->sum('amount');

                        $secFeeChk->update([
                            'paid_amount' => max($actualPaidA, $secRequiredForSettlement),
                            'status'      => 'paid'
                        ]);
                    }
                    $fromBooking->update(['has_security_fee' => false]);
                }
            }

            // ════════════════════════════════════════════════════════
            // SWAP — both must be completed, plots exchanged
            // ════════════════════════════════════════════════════════
            elseif ($request->transfer_type === 'swap') {

                $swapBooking = Booking::with('plot')
                    ->lockForUpdate()
                    ->findOrFail($request->swap_from_booking_id);

                if ($fromBooking->status !== 'completed') {
                    throw new \Exception("From booking must be fully paid to swap.");
                }
                if ($swapBooking->status !== 'completed') {
                    throw new \Exception("Target booking must be fully paid to swap.");
                }
                if ($swapBooking->id === $fromBooking->id) {
                    throw new \Exception("A booking cannot be swapped with itself.");
                }

                $tempPlotId = $fromBooking->plot_id;
                $fromBooking->update(['plot_id' => $swapBooking->plot_id, 'status' => 'swapped']);
                $swapBooking->update(['plot_id' => $tempPlotId,           'status' => 'swapped']);
            }

            // ════════════════════════════════════════════════════════
            // INTERNAL — same customer, different block/plot number
            // ════════════════════════════════════════════════════════
            elseif ($request->transfer_type === 'internal') {

                $fromBooking->plot->update([
                    'block'       => $request->new_block,
                    'plot_number' => $request->new_plot_number,
                ]);

                $fromBooking->update(['status' => 'plot_relocated']);
            }

            $transferFeeAmount = (float)($request->transfer_fee ?? 0);

            // ── PlotTransfer record — store result in $transfer ───────
            $transfer = PlotTransfer::create([
                'deed_no'                       => PlotTransfer::generateDeedNo(),
                'transfer_type'                 => $request->transfer_type,
                'transfer_date'                 => $request->transfer_date,
                'status'                        => 'pending',
                'from_booking_id'               => $fromBooking->id,
                'from_customer_id'              => $fromBooking->customer_id,
                'plot_id'                       => $fromBooking->plot_id,
                'to_customer_id'                => $request->to_customer_id ?? null,
                'to_booking_id'                 => $toBookingId,
                'swap_plot_id'                  => isset($swapBooking) ? $swapBooking->plot_id : null,
                'swap_from_booking_id'          => $request->swap_from_booking_id ?? null,
                'ownership_percentage'          => $request->ownership_percentage ?? null,
                'new_block'                     => $request->new_block ?? null,
                'new_plot_number'               => $request->new_plot_number ?? null,
                'remaining_balance_transferred' => $remainingBalance,
                'transfer_fee'                  => $transferFeeAmount,
                'transfer_fee_status'           => $transferFeeAmount > 0 ? 'pending' : 'waived',
                'reason'                        => $request->reason,
                'notes'                         => $request->notes,

                // ── SAVING THE WITNESSES ──
                'witness1_name'                 => $request->witness1_name,
                'witness1_cnic'                 => $request->witness1_cnic,
                'witness1_address'              => $request->witness1_address,
                'witness2_name'                 => $request->witness2_name,
                'witness2_cnic'                 => $request->witness2_cnic,
                'witness2_address'              => $request->witness2_address,

                'approved_by'                   => null,
            ]);

            // ── Transfer fee bill → BUYER (B) only ───────────────────
            if ($toBookingId) {
                \App\Models\BookingFee::create([
                    'booking_id'  => $toBookingId,
                    'fee_type'    => 'transfer',
                    'amount'      => $transferFeeAmount,
                    'paid_amount' => 0,
                    'status'      => $transferFeeAmount > 0 ? 'pending' : 'waived',
                    'transfer_id' => $transfer->id,
                ]);
            }

            // ── Auto-complete when no fee is required ─────────────────
            // Ownership/partial with fee=0: complete immediately (no fee payment flow will trigger)
            // Swap/internal: no buyer booking — also complete immediately
            $needsAutoComplete = ($transferFeeAmount == 0 && $toBookingId)   // waived-fee ownership/partial
                              || in_array($request->transfer_type, ['swap', 'internal']); // no buyer

            if ($needsAutoComplete) {
                // Increment plot transfer_count
                $fromBooking->plot->increment('transfer_count');

                // Mark transfer record as completed
                $transfer->update([
                    'status'              => 'completed',
                    'transfer_fee_status' => $transferFeeAmount == 0 ? 'waived' : $transfer->transfer_fee_status,
                ]);

                // Finalize booking statuses for waived-fee ownership/partial
                if ($request->transfer_type === 'ownership' && $toBookingId) {
                    $fromBooking->update(['status' => 'transferred']);
                    $toBook = Booking::find($toBookingId);
                    if ($toBook) {
                        $toBook->update(['status' => $toBook->total_price == 0 ? 'completed' : 'active']);
                    }
                } elseif ($request->transfer_type === 'partial' && $toBookingId) {
                    // fromBooking already set to partial_transferred above; finalize to_booking
                    $toBook = Booking::find($toBookingId);
                    if ($toBook) {
                        $toBook->update(['status' => 'partial_transferred']);
                    }
                }
                // swap/internal: booking statuses were already set above (swapped/plot_relocated)
            }

        }); // end DB::transaction

        return redirect()->route('index.transfer')
            ->with('success', 'Transfer created successfully. Transfer fee bill has been generated for the new owner.');

    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
}
public function swapDeed($id)
{
    $transfer = PlotTransfer::with([
        'fromBooking.customer',
        'fromBooking.plot',
        'fromCustomer',
        'toCustomer',
    ])->findOrFail($id);

    if ($transfer->transfer_type !== 'swap') {
        return redirect()->route('index.transfer')
            ->with('error', 'This document is only available for swap transfers.');
    }

    // Load swap booking
    $swapBooking = \App\Models\Booking::with(['customer','plot'])
        ->find($transfer->swap_from_booking_id);

    if (!$swapBooking) {
        return redirect()->route('index.transfer')
            ->with('error', 'Swap booking record not found.');
    }

    /* ───────────── Plot A Calculations ───────────── */

    $fromTotalPrice = $transfer->fromBooking->total_price;

    $fromPaid = \App\Models\PlotPayment::where('booking_id', $transfer->fromBooking->id)
        ->where('status', 'paid')
        ->sum('amount_paid');

    $fromRemaining = max(0, $fromTotalPrice - $fromPaid);


    /* ───────────── Plot B Calculations ───────────── */

    $toTotalPrice = $swapBooking->total_price;

    $toPaid = \App\Models\PlotPayment::where('booking_id', $swapBooking->id)
        ->where('status', 'paid')
        ->sum('amount_paid');

    $toRemaining = max(0, $toTotalPrice - $toPaid);


    /* ───────────── QR Code ───────────── */

    $qrUrl = route('transfer.qr.verify', $transfer->id);

    $qrCodeSvg = base64_encode(
        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(120)
            ->generate($qrUrl)
    );


    /* ───────────── Generate PDF ───────────── */

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'transfers.swap_deed_pdf',
        compact(
            'transfer',
            'swapBooking',
            'qrCodeSvg',

            'fromTotalPrice',
            'fromPaid',
            'fromRemaining',

            'toTotalPrice',
            'toPaid',
            'toRemaining'
        )
    )->setPaper('a4', 'portrait');

    return $pdf->stream('SwapDeed-' . $transfer->deed_no . '.pdf');
}
public function qrCode($id)
{
    $transfer = PlotTransfer::with([
        'fromBooking.customer',
        'fromBooking.plot',
        'fromCustomer',
        'toCustomer',
    ])->findOrFail($id);

    $swapBooking = null;
    if ($transfer->transfer_type === 'swap' && $transfer->swap_from_booking_id) {
        $swapBooking = Booking::with(['customer', 'plot'])
            ->find($transfer->swap_from_booking_id);
    }

    $qrUrl     = route('transfer.qr.verify', $transfer->id);
    $qrCodeSvg = QrCode::format('svg')->size(200)->generate($qrUrl);

    return view('transfers.qr_code', compact(
        'transfer',
        'swapBooking',
        'qrUrl',
        'qrCodeSvg',
    ));
}

    // ── Show transfer deed ────────────────────────────────────
public function deed($id)
{
    $transfer = PlotTransfer::with([
        'fromBooking.customer',
        'fromBooking.plot.category',
        'fromBooking.payments',
        'fromBooking.bookingFees.payments',   // ← fee status for seller
        'toBooking.customer',
        'toBooking.plot',
        'toBooking.bookingFees.payments',     // ← fee status for buyer
        'fromCustomer',
        'toCustomer',
        'plot.category',
    ])->findOrFail($id);

    if ($transfer->transfer_type === 'swap') {
        return redirect()->route('transfer.swap.deed', $id);
    }

    // ── Build full ownership chain for this plot (max 5) ─────────
    // Find all transfers involving this plot, ordered oldest→newest
    $plotId = $transfer->plot_id ?? $transfer->fromBooking?->plot_id;

    $allTransfers = PlotTransfer::with([
        'fromBooking.customer',
        'toBooking.customer',
        'fromCustomer',
        'toCustomer',
    ])
    ->where('plot_id', $plotId)
    ->whereIn('status', ['completed', 'pending'])
    ->orderBy('transfer_date', 'asc')
    ->take(5)
    ->get();

    // Original booking (first owner before any transfer)
    $originalBooking = \App\Models\Booking::with('customer')
        ->where('plot_id', $plotId)
        ->whereNull('parent_booking_id')
        ->oldest('booking_date')
        ->first();

    // ── Fee summary for the from_booking ─────────────────────────
    // booking_fees has: id, booking_id, fee_type, amount, paid_amount, status, transfer_id
    $fromBookingFees = $transfer->fromBooking
        ? \App\Models\BookingFee::where('booking_id', $transfer->fromBooking->id)
            ->get()
            ->keyBy('fee_type')
        : collect();

    // ── Fee summary for to_booking (buyer) ────────────────────────
    $toBookingFees = $transfer->toBooking
        ? \App\Models\BookingFee::where('booking_id', $transfer->toBooking->id)
            ->get()
            ->keyBy('fee_type')
        : collect();

    // ── Transfer fee payments for this specific transfer ──────────
    $transferFeeBill = \App\Models\BookingFee::where('transfer_id', $transfer->id)
        ->where('fee_type', 'transfer')
        ->first();

    $transferFeePayments = $transferFeeBill
        ? \App\Models\FeePayment::where('booking_fee_id', $transferFeeBill->id)
            ->orderBy('paid_date')
            ->get()
        : collect();

    // ── System config (logo, name etc.) ──────────────────────────
    $sc = \App\Models\SystemConfig::allAsArray();

    // ── QR code ───────────────────────────────────────────────────
    $qrUrl  = route('transfer.qr.verify', $transfer->id);
    $qrCode = base64_encode(
        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($qrUrl)
    );

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'transfers.transfer_deed',
        compact(
            'transfer',
            'qrCode',
            'sc',
            'allTransfers',
            'originalBooking',
            'fromBookingFees',
            'toBookingFees',
            'transferFeeBill',
            'transferFeePayments',
        )
    )->setPaper('a4', 'portrait');

    return $pdf->stream('Deed-' . $transfer->deed_no . '.pdf');
}

// ── 2. APPLICATION FORM PDF ────────────────────────────────────────
public function applicationForm($id)
{
    $transfer = PlotTransfer::with([
        'fromBooking.customer',
        'fromBooking.plot',
        'fromBooking.payments',
        'toBooking.customer',
        'fromCustomer',
        'toCustomer',
        'plot',
    ])->findOrFail($id);

     $qrUrl  = route('transfer.qr.verify', $transfer->id);
    $qrCode = base64_encode(
        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($qrUrl)
    );

    // Pass both $transfer and $qrCode to the view
    return view('transfers.transfer_application_form', compact('transfer', 'qrCode'));
}
// ── 3. QR VERIFY VIEW ─────────────────────────────────────────────
public function qrVerify($id)
{
    $transfer = PlotTransfer::with([
        'fromBooking.customer',
        'fromBooking.plot',
        'toBooking.customer',
    ])->findOrFail($id);

    $swapBooking = $transfer->toBooking;

    // CHANGE: Look for the fee on the NEW owner's booking (to_booking_id)
    $transferFee = \App\Models\BookingFee::where('booking_id', $transfer->to_booking_id)
        ->where('fee_type', 'transfer')
        ->first();

    $isFeePaid = false;
    if ($transferFee) {
        // Now this will find the record with 100,000 paid
        $isFeePaid = ($transferFee->status === 'paid' || $transferFee->paid_amount > 0);
    }

    // REMOVE the dd() and return the view
    return view('transfers.qr_verify', compact('transfer', 'swapBooking', 'isFeePaid'));
}
     // ── Transfer history for a booking ───────────────────────
    public function history($bookingId)
    {
        $transfers = PlotTransfer::with([
            'fromCustomer',
            'toCustomer',
            'plot'
        ])->where('from_booking_id', $bookingId)
            ->orWhere('to_booking_id', $bookingId)
            ->latest()->get();

        return response()->json($transfers);
    }


    public function payFee($id)
{
    $transfer = PlotTransfer::with([
        'fromCustomer',
        'toCustomer',
        'plot',
        'fromBooking',
        'toBooking',
    ])->findOrFail($id);

    // Only pending transfers can be paid
    if ($transfer->status !== 'pending') {
        return redirect()->route('index.transfer')
            ->with('error', 'This transfer fee has already been settled.');
    }

    return view('transfers.transfer_pay_fee', compact('transfer'));
}

    // ── Process fee payment ───────────────────────────────────────
   public function processPayment(Request $request, $id)
{
    $transfer = PlotTransfer::with(['fromBooking', 'toBooking'])->findOrFail($id);

    if ($transfer->status !== 'pending') {
        return redirect()->route('index.transfer')
            ->with('error', 'This transfer has already been settled or rejected.');
    }

    $proofRule = $request->payment_method === 'cash'
        ? 'nullable|image|mimes:jpeg,png,webp'
        : 'required|image|mimes:jpeg,png,webp';

    $request->validate([
        'payment_method' => 'required|in:cash,bank_transfer,cheque,online,pay_order',
        'receipt_no'     => 'required|string|max:100|unique:plot_transfers,transfer_fee_receipt_no',
        'payment_date'   => 'required|date',
        'paid_by'        => 'required|string|max:255',
        'notes'          => 'nullable|string|max:500',
        'payment_proof'  => $proofRule,
    ]);

    $filename = null;
    if ($request->hasFile('payment_proof')) {
        $file     = $request->file('payment_proof');
        $filename = 'transfer-proofs/' . $transfer->deed_no . '-' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('transferFeeRec/', $filename, 'public');
    }

    DB::transaction(function () use ($transfer, $request, $filename) {

        // ── Mark transfer as completed ─────────────────────────────
        $transfer->update([
            'transfer_fee_status'     => 'paid',
            'transfer_fee_receipt_no' => $request->receipt_no,
            'status'                  => 'completed',
            'payment_proof'           => $filename,
            'notes'                   => $transfer->notes
                ? $transfer->notes . "\nPayment: " . $request->notes
                : $request->notes,
            'fee_paid_date'           => $request->payment_date,
            'approved_by'             => $request->paid_by,
            'payment_method'          => $request->payment_method,
        ]);

        // ════════════════════════════════════════════════════════════
        // OWNERSHIP — final statuses after fee paid
        // from_booking → transferred        (old owner fully done)
        // to_booking   → active/completed   (new owner starts paying)
        // ════════════════════════════════════════════════════════════
        if ($transfer->transfer_type === 'ownership') {

            // Old booking fully closed
            if ($transfer->fromBooking) {
                $transfer->fromBooking->update(['status' => 'transferred']);
            }

            // New owner booking — check if any balance remains
            if ($transfer->toBooking) {
                $paidAmount = $transfer->toBooking
                    ->payments()->where('status', 'paid')->sum('amount_paid');
                $remaining  = max(0, $transfer->toBooking->total_price - $paidAmount);

                $transfer->toBooking->update([
                    'status' => $remaining > 0 ? 'active' : 'completed',
                ]);
            }
        }

        // ════════════════════════════════════════════════════════════
        // PARTIAL — final statuses after fee paid
        // from_booking → partial_transferred  (old owner partial done)
        // to_booking   → active/completed     (new partial owner starts)
        // ════════════════════════════════════════════════════════════
        elseif ($transfer->transfer_type === 'partial') {

            // Old booking marked as partial transfer complete
            if ($transfer->fromBooking) {
                $transfer->fromBooking->update(['status' => 'partial_transferred']);
            }

            // New partial owner booking — check if any balance remains
            if ($transfer->toBooking) {
                $paidAmount = $transfer->toBooking
                    ->payments()->where('status', 'paid')->sum('amount_paid');
                $remaining  = max(0, $transfer->toBooking->total_price - $paidAmount);

                $transfer->toBooking->update([
                    'status' => $remaining > 0 ? 'active' : 'completed',
                ]);
            }
        }

        // ════════════════════════════════════════════════════════════
        // SWAP — final statuses after fee paid
        // from_booking → swapped  (already set in store, confirmed)
        // swap_booking → swapped  (already set in store, confirmed)
        // ════════════════════════════════════════════════════════════
        elseif ($transfer->transfer_type === 'swap') {

            // Confirm both as swapped (they were set in store already,
            // this re-confirms after fee is paid)
            if ($transfer->fromBooking) {
                $transfer->fromBooking->update(['status' => 'swapped']);
            }

            if ($transfer->swap_from_booking_id) {
                $swapBooking = Booking::find($transfer->swap_from_booking_id);
                if ($swapBooking) {
                    $swapBooking->update(['status' => 'swapped']);
                }
            }
        }

        // ════════════════════════════════════════════════════════════
        // INTERNAL — final statuses after fee paid
        // from_booking → plot_relocated  (plot details changed, booking active again)
        // ════════════════════════════════════════════════════════════
        elseif ($transfer->transfer_type === 'internal') {

            // Booking is relocated — mark as active again since
            // it is the same owner, just a different plot number/block
            if ($transfer->fromBooking) {
                $transfer->fromBooking->update(['status' => 'plot_relocated']);
            }
        }
    });

    return redirect()->route('index.transfer')->with('success', 'Transfer fee paid and transfer completed successfully.');
}

    // ── Show fee receipt ──────────────────────────────────────────
    public function feeReceipt(Request $request, $id)
    {
        $transfer = PlotTransfer::with([
            'fromCustomer',
            'toCustomer',
            'plot',
            'fromBooking',
            'toBooking',
        ])->findOrFail($id);

        // Pass payment info from query string (set during processPayment redirect)
        $paymentInfo = [
            'method' => $request->query('payment_method', 'cash'),
            'date'   => $request->query('payment_date', $transfer->updated_at->format('Y-m-d')),
            'paidBy' => $request->query('paid_by', $transfer->approved_by ?? 'Admin'),
        ];

        return view('transfers.transfer_fee_receipt', compact('transfer', 'paymentInfo'));
    }


    public function edit($id)
    {
        $transfer = PlotTransfer::with([
            'fromCustomer',
            'toCustomer',
            'plot',
            'fromBooking',
            'toBooking',
        ])->findOrFail($id);

        if ($transfer->status === 'completed') {
            return redirect()->route('transfers.index')
                ->with('error', 'Completed transfers cannot be edited.');
        }

        $customers = Customer::orderBy('name')->get();

        return view('transfers.transfer_edit', compact('transfer', 'customers'));
    }

    // ── Update: Save edits ────────────────────────────────────
   public function update(Request $request, $id)
{
    $transfer = PlotTransfer::findOrFail($id);

    if ($transfer->status === 'completed') {
        return redirect()->route('index.transfer')
            ->with('error', 'Completed transfers cannot be edited.');
    }

    $request->validate([
        'transfer_date'        => 'required|date',
        'reason'               => 'nullable|string|max:1000',
        'notes'                => 'nullable|string|max:1000',
        'to_customer_id'       => 'nullable|exists:customers,id',
        'ownership_percentage' => 'nullable|numeric|min:1|max:99',
        'new_block'            => 'nullable|string|max:50',
        'new_plot_number'      => 'nullable|string|max:50',

        // ── Witness Validation ──
        'witness1_name'        => 'nullable|string|max:255',
        'witness1_cnic'        => 'nullable|string|max:20',
        'witness1_address'     => 'nullable|string|max:500',
        'witness2_name'        => 'nullable|string|max:255',
        'witness2_cnic'        => 'nullable|string|max:20',
        'witness2_address'     => 'nullable|string|max:500',
    ]);

    $transfer->update([
        'transfer_date'        => $request->transfer_date,
        'to_customer_id'       => $request->to_customer_id       ?? $transfer->to_customer_id,
        'ownership_percentage' => $request->ownership_percentage ?? $transfer->ownership_percentage,
        'new_block'            => $request->new_block            ?? $transfer->new_block,
        'new_plot_number'      => $request->new_plot_number      ?? $transfer->new_plot_number,
        'reason'               => $request->reason,
        'notes'                => $request->notes,

        // ── Witness Fields ──
        'witness1_name'        => $request->witness1_name,
        'witness1_cnic'        => $request->witness1_cnic,
        'witness1_address'     => $request->witness1_address,
        'witness2_name'        => $request->witness2_name,
        'witness2_cnic'        => $request->witness2_cnic,
        'witness2_address'     => $request->witness2_address,

        // Keep your status logic
        'status' => in_array($request->transfer_fee_status, ['paid', 'waived'])
            ? 'completed' : 'pending',
    ]);

    return redirect()->route('index.transfer')
        ->with('success', 'Transfer ' . $transfer->deed_no . ' updated successfully.');
}

    // ── Destroy: Delete transfer record ──────────────────────
    public function destroy($id)
    {
        $transfer = PlotTransfer::with('fromBooking')->findOrFail($id);

        if ($transfer->status === 'completed') {
            return redirect()->route('transfers.index')
                ->with('error', 'Completed transfers cannot be deleted.');
        }

        // Restore original customer if ownership was already updated
        if ($transfer->fromBooking && $transfer->from_customer_id) {
            $transfer->fromBooking->update(['customer_id' => $transfer->from_customer_id]);
        }

        $deedNo = $transfer->deed_no;
        $transfer->delete();

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer ' . $deedNo . ' deleted. Original owner restored.');
    }

//    public function approve($id)
// {
//     $transfer = PlotTransfer::with(['fromBooking.payments'])->findOrFail($id);

//     if ($transfer->status !== 'pending') {
//         return redirect()->route('index.transfer')
//             ->with('error', 'Only pending transfers can be approved.');
//     }

//     // ✅ No dues check — balance carries over to new owner

//     // Transfer fee must be paid or waived before approval
//     if ($transfer->transfer_fee > 0 && $transfer->transfer_fee_status === 'pending') {
//         return redirect()->route('index.transfer')
//             ->with('error', 'Transfer fee of PKR ' . number_format($transfer->transfer_fee) . ' must be paid or waived before approval.');
//     }

//     $transfer->update(['status' => 'approved']);

//     return redirect()->route('index.transfer')
//         ->with('success', 'Transfer ' . $transfer->deed_no . ' approved. Ready to process payment and complete.');
// }

   public function quickRegister(Request $request)
{
    // Basic validation to ensure required fields exist
    $request->validate([
        'name' => 'required|string',
        'cnic' => 'required|unique:customers,cnic',
        'phone' => 'required',
    ]);

    // File Upload Helper
    $uploadFile = function($file, $subFolder) use ($request) {
        if (!$file) return null;
        $filename = Str::slug($request->name) . '-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        // Stores in storage/app/public/customers/{subfolder}
        return 'storage/' . $file->storeAs("customers/{$subFolder}", $filename, 'public');
    };

    $customer = Customer::create([
        'name'                => $request->name,
        'guardian_name'       => $request->guardian_name,
        'cnic'                => $request->cnic,
        'phone'               => $request->phone,
        'mobile'              => $request->phone, // Mapping phone to mobile
        'phone_off'           => $request->phone_off,
        'phone_res'           => $request->phone_res,
        'email'               => $request->email,
        'occupation'          => $request->occupation,
        'age'                 => $request->age,
        'nationality'         => $request->nationality,
        'residential_address' => $request->residential_address,
        'postal_address'      => $request->postal_address,
        'address'             => $request->residential_address, // Fallback for general address column
        'status'              => 'active',

        // Nominee Info
        'nominee_name'        => $request->nominee_name,
        'nominee_relation'    => $request->nominee_relation,
        'nominee_cnic'        => $request->nominee_cnic,
        'nominee_address'     => $request->nominee_address,

        // Image Storage logic
        'customer_pic'        => $uploadFile($request->file('customer_pic'), 'photos'),
        'cnic_pic'            => $uploadFile($request->file('cnic_pic'), 'cnic'),
        'nominee_pic'         => $uploadFile($request->file('nominee_pic'), 'nominee_pics'),
        'nominee_cnic_front'  => $uploadFile($request->file('nominee_cnic_front'), 'nominee_cnics'),
        'nominee_cnic_back'   => $uploadFile($request->file('nominee_cnic_back'), 'nominee_cnics'),
    ]);

    return response()->json([
        'success'  => true,
        'message'  => 'Customer registered successfully in Zamar Valley system.',
        'customer' => [
            'id'   => $customer->id,
            'name' => $customer->name,
            'cnic' => $customer->cnic,
        ],
    ]);
}
   public function possessionLetter($bookingId)
{
    $booking = Booking::with(['customer', 'plot.category', 'payments'])->findOrFail($bookingId);
    $parentBooking = $booking->parent_booking_id
        ? Booking::with('payments')->find($booking->parent_booking_id)
        : null;

    // Block if this booking is no longer the current owner
    $transferredStatuses = ['transferred', 'partial_transferred', 'swapped', 'plot_relocated', 'pending_transfer', 'cancelled'];
    if (in_array($booking->status, $transferredStatuses)) {
        return back()->with('error', 'Possession letter is not available for this booking — the plot has been transferred to a new owner.');
    }

    $duesCheck = $this->checkDuesCleared($bookingId);
    if (!$duesCheck['cleared']) {
        $reasons = [];
        if ($duesCheck['outstanding'] > 10) {
            $reasons[] = 'outstanding plot balance of PKR ' . number_format($duesCheck['outstanding']);
        }
        if (($duesCheck['pending_inst'] ?? 0) > 0) {
            $reasons[] = $duesCheck['pending_inst'] . ' monthly installment(s) not recorded';
        }
        if (($duesCheck['pending_qtr'] ?? 0) > 0) {
            $reasons[] = $duesCheck['pending_qtr'] . ' quarterly installment(s) not recorded';
        }
        $msg = empty($reasons)
            ? 'All dues must be cleared before possession can be issued.'
            : 'Possession blocked — ' . implode('; ', $reasons) . '.';
        return back()->with('error', $msg);
    }

    $verifyUrl = URL::signedRoute('verify_possession', ['booking' => $booking->id]);

    // Generate QR as Base64 SVG string for the PDF
    $qrCode = base64_encode(QrCode::format('svg')
        ->size(100)
        ->margin(0)
        ->errorCorrection('H')
        ->generate($verifyUrl));
    // ── Transfer chain — loaded for ALL bookings ──────────────────────────
    // For booking_type = 'Transfer': shows full ownership history in the PDF.
    // For booking_type = 'First Allotment': $transferChain will be empty →
    // the blade @if block simply won't render the transfer section.
    $transferChain = PlotTransfer::where('from_booking_id', $booking->id)
        ->orWhere('to_booking_id', $booking->id)
        ->with(['fromCustomer', 'toCustomer'])
        ->orderBy('transfer_date', 'asc')
        ->get();

    // Also check via plot — some transfers link via plot_id not booking_id
    if ($transferChain->isEmpty() && $booking->plot) {
        $transferChain = PlotTransfer::where('plot_id', $booking->plot->id)
            ->with(['fromCustomer', 'toCustomer'])
            ->orderBy('transfer_date', 'asc')
            ->get();
    }

    $sc     = $this->societyConfig();

    $customerPicB64 = null;
    $picPath = $booking->customer->customer_pic ?? null;
    if ($picPath) {
        $absPath = storage_path('app/public/' . ltrim($picPath, '/'));
        if (file_exists($absPath)) {
            $ext = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));
            $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';
            $customerPicB64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($absPath));
        }
    }

    $pdf = Pdf::loadView(
        'booking.possession_letter',
        compact('booking', 'qrCode', 'sc', 'transferChain', 'parentBooking', 'customerPicB64')
    )
    ->setPaper('a4', 'portrait')
    ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'Helvetica']);

    return $pdf->stream("PossessionLetter-{$booking->customer_booking_id}.pdf");
}
public function verifyPossession(Booking $booking)
{
    $booking->load(['customer', 'plot.category', 'payments']);
    $sc = $this->societyConfig();
    $dues = $this->checkDuesCleared($booking->id);

    // This is the key: Browsers need a URL, not a local path
    $socLogo = $sc['logo'] ?? null;
    $logoUrl = ($socLogo) ? asset('storage/' . ltrim($socLogo, '/')) : null;

    return view('booking.verify_possession', compact('booking', 'dues', 'sc', 'logoUrl'));
}
    /**
     * Check whether all monthly security fee instalments up to the current month are paid.
     *
     * Returns an array with:
     *   ok             – true if no outstanding security fee
     *   monthly_amount – PKR per month
     *   months_elapsed – total months due (booking month → current month, inclusive)
     *   months_paid    – how many full months have been paid
     *   months_unpaid  – months still outstanding
     *   total_owed     – cumulative amount due so far
     *   total_paid     – cumulative amount actually paid
     */
    private function securityFeeCheck(Booking $booking): array
    {
        if (!$booking->has_security_fee) {
            return ['ok' => true];
        }

        $monthlyAmount = (float)($booking->plot->security_fee_amount ?? 0);

        // If no monthly rate is configured we cannot block the transfer
        if ($monthlyAmount <= 0) {
            return ['ok' => true, 'monthly_amount' => 0];
        }

        $bookingStart  = \Carbon\Carbon::parse($booking->booking_date)->startOfMonth();
        $currentMonth  = \Carbon\Carbon::now()->startOfMonth();
        $monthsElapsed = (int)$bookingStart->diffInMonths($currentMonth) + 1; // inclusive of booking month

        $totalOwed = $monthsElapsed * $monthlyAmount;

        $secFee    = \App\Models\BookingFee::where('booking_id', $booking->id)
                         ->where('fee_type', 'security')->first();
        $totalPaid = $secFee ? (float)$secFee->paid_amount : 0;

        $monthsPaid   = (int)floor($totalPaid / $monthlyAmount);
        $monthsUnpaid = max(0, $monthsElapsed - $monthsPaid);

        return [
            'ok'             => $totalPaid >= $totalOwed,
            'monthly_amount' => $monthlyAmount,
            'months_elapsed' => $monthsElapsed,
            'months_paid'    => $monthsPaid,
            'months_unpaid'  => $monthsUnpaid,
            'total_owed'     => $totalOwed,
            'total_paid'     => $totalPaid,
        ];
    }

    private function checkDuesCleared($bookingId): array
    {
        $booking    = Booking::with('payments')->findOrFail($bookingId);

        // A completed booking is always considered cleared — status is the authoritative source
        // (mirrors ledger view logic for lump-sum / settlement-discount bookings)
        if ($booking->status === 'completed') {
            return [
                'cleared'      => true,
                'outstanding'  => 0,
                'total_price'  => $booking->total_price,
                'total_paid'   => $booking->total_price,
                'pending_inst' => 0,
                'pending_qtr'  => 0,
                'total_inst'   => (int)($booking->total_installments ?? 0),
                'total_qtr'    => (int)($booking->quarterly_installments ?? 0),
                'booking_id'   => $bookingId,
            ];
        }

        $discSentinel = 'Settlement discount — waived amount (not collected).';
        $plotPriceCats = ['down_payment','installment','quarterly_installment','plot_balance','others'];

        $payments = $booking->payments;

        // Real cash paid towards plot price (matches ledger calculation)
        $totalPaidReal = $payments
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)
            ->sum('amount_paid');

        // Settlement discount credits (matches ledger calculation)
        $discountCredits = $payments
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->filter(fn($p) => ($p->remarks ?? '') === $discSentinel)
            ->sum('amount_paid')
            + $payments
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)
            ->sum('discount_amount');

        // Use schedule-based total just like ledger (handles quarterly not in total_price)
        $totalPrice     = (float) $booking->total_price;
        $totalInst      = (int)($booking->total_installments ?? 0);
        $totalQtr       = (int)($booking->quarterly_installments ?? 0);
        $monthlyAmount  = (float)($booking->monthly_installment ?? 0);
        $quarterlyAmt   = (float)($booking->quarterly_amount ?? 0);
        $downDue        = (float)($booking->down_payment ?? 0);

        $scheduleTotal = $downDue + ($totalInst * $monthlyAmount) + ($totalQtr * $quarterlyAmt);
        $trueTotal     = max($totalPrice, $scheduleTotal);

        $outstanding = max(0, $trueTotal - $totalPaidReal - $discountCredits);

        $paidInst    = $payments->where('payment_category','installment')->where('status','paid')->count();
        $pendingInst = max(0, $totalInst - $paidInst);

        $paidQtr     = $payments->where('payment_category','quarterly_installment')->where('status','paid')->count();
        $pendingQtr  = max(0, $totalQtr - $paidQtr);

        // Tolerance of PKR 10 for rounding (mirrors ledger view logic)
        $tolerance   = 10;
        $amountDone  = $outstanding <= $tolerance;
        $monthlyDone = ($totalInst === 0 || $paidInst  >= $totalInst);
        $quarterlyDone = ($totalQtr === 0 || $paidQtr  >= $totalQtr);
        $cleared     = $amountDone && $monthlyDone && $quarterlyDone;

        return [
            'cleared'      => $cleared,
            'outstanding'  => $outstanding,
            'total_price'  => $trueTotal,
            'total_paid'   => $totalPaidReal + $discountCredits,
            'pending_inst' => $pendingInst,
            'pending_qtr'  => $pendingQtr,
            'total_inst'   => $totalInst,
            'total_qtr'    => $totalQtr,
            'booking_id'   => $bookingId,
        ];
    }
public function reject($id)
{
    $transfer = PlotTransfer::findOrFail($id);

    if ($transfer->status === 'completed') {
        return redirect()->back()->with('error', 'Cannot reject a completed transfer. Only pending or approved transfers can be rejected.');
    }

    try {
        DB::transaction(function () use ($transfer, &$fromBooking, &$revertLog) {
            $revertLog = [];

            // ── 1. Restore fromBooking status ──────────────────────
            $fromBooking = Booking::with(['payments', 'plot'])->find($transfer->from_booking_id);

            if ($fromBooking) {
                $plotCats  = ['down_payment', 'installment', 'quarterly_installment', 'plot_balance', 'others'];
                $payments  = $fromBooking->payments()->where('status', 'paid');

                $totalPaidPlot = (clone $payments)->whereIn('payment_category', $plotCats)->sum('amount_paid');
                $totalPaidProc = (clone $payments)->where('payment_category', 'processing_fee')->sum('amount_paid');

                $isPlotPaid     = $fromBooking->total_price > 0 && $totalPaidPlot >= $fromBooking->total_price;
                $isProcPaid     = ($fromBooking->processing_fee ?? 0) == 0 || $totalPaidProc >= $fromBooking->processing_fee;
                $restoredStatus = ($isPlotPaid && $isProcPaid) ? 'completed' : 'active';

                $fromBooking->update(['status' => $restoredStatus]);
                $revertLog[] = "Booking {$fromBooking->customer_booking_id} restored to {$restoredStatus}.";

                // ── Restore seller fee bills that were forcibly closed at transfer time ──
                // When a transfer is created, the seller's dev/security/registry fee bills are
                // forcibly set to paid and the has_*_fee flags set to false. We detect which
                // ones were carried by checking whether the buyer's booking had that fee type,
                // then restore the seller's bills from actual FeePayment records.
                $buyerFeeTypes = $transfer->to_booking_id
                    ? \App\Models\BookingFee::where('booking_id', $transfer->to_booking_id)
                        ->whereIn('fee_type', ['registry', 'development', 'security'])
                        ->pluck('fee_type')
                        ->toArray()
                    : [];

                $flagUpdates = [];
                foreach (['registry', 'development', 'security'] as $feeType) {
                    // Security is mandatory for every plot — always restore it.
                    // Registry is restored if the plot has it enabled (buyer may not have gotten a bill
                    // when the fee amount was null/zero at transfer time).
                    // Development is only restored if the buyer explicitly received it.
                    $plotHasIt = (bool)($fromBooking->plot->{"has_{$feeType}_fee"} ?? false);
                    $isMandatory = ($feeType === 'security') || ($feeType === 'registry' && $plotHasIt);
                    if (!$isMandatory && !in_array($feeType, $buyerFeeTypes)) continue;

                    $sellerBill = \App\Models\BookingFee::where('booking_id', $fromBooking->id)
                        ->where('fee_type', $feeType)->first();

                    // If the bill was deleted/never created, recreate it so the flag restore works.
                    if (!$sellerBill && in_array($feeType, ['security', 'registry'])) {
                        $rateKey = "{$feeType}_fee_amount";
                        $rate    = (float)($fromBooking->plot->{$rateKey} ?? 0);
                        $sellerBill = \App\Models\BookingFee::create([
                            'booking_id'  => $fromBooking->id,
                            'fee_type'    => $feeType,
                            'amount'      => $rate,
                            'paid_amount' => 0,
                            'status'      => 'pending',
                        ]);
                        $revertLog[] = ucfirst($feeType) . " fee bill recreated (PKR " . number_format($rate) . ").";
                    }

                    if (!$sellerBill) continue;

                    // Recalculate from actual FeePayment records against this bill
                    $actualPaid = (float)\App\Models\FeePayment::where('booking_fee_id', $sellerBill->id)
                        ->sum('amount');

                    if ($feeType === 'security') {
                        $newStatus = $actualPaid > 0 ? 'partial' : 'pending';
                    } else {
                        $billAmount = (float)$sellerBill->amount;
                        $newStatus  = ($billAmount > 0 && $actualPaid >= $billAmount) ? 'paid'
                            : ($actualPaid > 0 ? 'partial' : 'pending');
                    }

                    $sellerBill->update(['paid_amount' => $actualPaid, 'status' => $newStatus]);
                    $flagUpdates["has_{$feeType}_fee"] = true;
                    $revertLog[] = ucfirst($feeType) . " fee restored (PKR " . number_format($actualPaid) . " paid).";
                }

                if (!empty($flagUpdates)) {
                    $fromBooking->update($flagUpdates);
                }
            }

            // ── 2. Fully clean up toBooking and all its children ───
            if ($transfer->to_booking_id) {
                $toBooking = Booking::find($transfer->to_booking_id);

                if ($toBooking) {
                    $feeIds = \App\Models\BookingFee::where('booking_id', $toBooking->id)->pluck('id');
                    if ($feeIds->isNotEmpty()) {
                        \App\Models\FeePayment::whereIn('booking_fee_id', $feeIds)->delete();
                    }
                    \App\Models\BookingFee::where('booking_id', $toBooking->id)->delete();
                    \App\Models\PlotPayment::where('booking_id', $toBooking->id)->delete();
                    \App\Models\BookingHold::where('booking_id', $toBooking->id)->delete();
                    $toBooking->forceDelete();
                    $revertLog[] = "Buyer booking and all associated records removed.";
                }
            }

            // ── 3. Delete the transfer record ──────────────────────
            $transfer->forceDelete();
        });

        $detail = implode(' ', $revertLog);
        return redirect()->route('index.transfer')
            ->with('success', "Transfer rejected and fully reverted. {$detail}");

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Revert failed: ' . $e->getMessage());
    }
}
}


