<?php

namespace App\Http\Controllers;


use App\Models\Booking;
use App\Models\BookingFee;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\PlotTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeManagementController extends Controller
{
 public function index(Request $request): \Illuminate\View\View
{
    $bookings = collect();

    if ($request->filled('q')) {
        // 1. Normalize the search query: remove spaces and dashes
        $q = $request->q;
        $cleanQ = str_replace([' ', '-'], '', $q);

        $bookings = Booking::with([
            'customer',
            'plot',
            'bookingFees.payments',
            'transfersFrom',
            'transfersTo',
        ])
        /** * Filter by lifecycle stages relevant to fee management.
         */
        ->whereIn('status', [
            'active', 'completed', 'transferred',
            'pending_transfer', 'swapped', 'plot_relocated',
            'pending', 'booked', 'sold', 'partial'
        ])
        ->where(function ($query) use ($q, $cleanQ) {
            // Booking ID — match with or without dashes
            $query->where('customer_booking_id', 'like', "%$q%")
                  ->orWhereRaw("REPLACE(REPLACE(customer_booking_id, '-', ''), ' ', '') LIKE ?", ["%$cleanQ%"])

                // Customer — name, CNIC, mobile, phone
                ->orWhereHas('customer', function ($c) use ($q, $cleanQ) {
                    $c->where('name', 'like', "%$q%")
                      ->orWhereRaw("REPLACE(REPLACE(cnic, '-', ''), ' ', '') LIKE ?", ["%$cleanQ%"])
                      ->orWhereRaw("REPLACE(mobile, ' ', '') LIKE ?", ["%$cleanQ%"])
                      ->orWhereRaw("REPLACE(phone,  ' ', '') LIKE ?", ["%$cleanQ%"]);
                })

                // Plot — number (with/without dashes), block
                ->orWhereHas('plot', function ($p) use ($q, $cleanQ) {
                    $p->where('plot_number', 'like', "%$q%")
                      ->orWhereRaw("REPLACE(REPLACE(plot_number, '-', ''), ' ', '') LIKE ?", ["%$cleanQ%"])
                      ->orWhere('block', 'like', "%$q%");
                })
                
                // Parent/Child bookings (Full Chain)
                ->orWhere('id', $q)
                ->orWhere('parent_booking_id', $q);
        })
        ->orderBy('created_at', 'desc')
        ->limit(100)
        ->get();

        // --- Logic for auto-creating fees ---
        foreach ($bookings as $bk) {
            $isInactive = in_array($bk->status, [
                'transferred','pending_transfer','cancelled','swapped','plot_relocated'
            ]);

            // Security fee: auto-create when flagged but no record yet
            if (!$isInactive && $bk->has_security_fee && !$bk->bookingFees->where('fee_type', 'security')->count()) {
                $newBill = \App\Models\BookingFee::create([
                    'booking_id'  => $bk->id,
                    'fee_type'    => 'security',
                    'amount'      => (float)($bk->plot->security_fee_amount ?? 0),
                    'paid_amount' => 0,
                    'status'      => 'pending',
                ]);
                $bk->bookingFees->push($newBill);
            }

            // Development fee: auto-create when flagged but no record yet
            if (!$isInactive && $bk->has_development_fee && !$bk->bookingFees->where('fee_type', 'development')->count()) {
                $newBill = \App\Models\BookingFee::create([
                    'booking_id'  => $bk->id,
                    'fee_type'    => 'development',
                    'amount'      => (float)($bk->plot->development_fee_amount ?? 0),
                    'paid_amount' => 0,
                    'status'      => 'pending',
                ]);
                $bk->bookingFees->push($newBill);
            }

            // Registry fee: auto-create when flagged but no record yet
            if (!$isInactive && $bk->has_registry_fee && !$bk->bookingFees->where('fee_type', 'registry')->count()) {
                $newBill = \App\Models\BookingFee::create([
                    'booking_id'  => $bk->id,
                    'fee_type'    => 'registry',
                    'amount'      => (float)($bk->plot->registry_fee_amount ?? 0),
                    'paid_amount' => 0,
                    'status'      => 'pending',
                ]);
                $bk->bookingFees->push($newBill);
            }

            // Transfer fee: auto-create for BUYER (to_booking) only
            if ($bk->transfersTo->isNotEmpty()) {
                foreach ($bk->transfersTo as $tr) {
                    $hasBill = $bk->bookingFees
                        ->where('fee_type', 'transfer')
                        ->where('transfer_id', $tr->id)
                        ->count();

                    if (!$hasBill) {
                        $newBill = \App\Models\BookingFee::create([
                            'booking_id'  => $bk->id,
                            'fee_type'    => 'transfer',
                            'amount'      => 0,
                            'paid_amount' => 0,
                            'status'      => 'pending',
                            'transfer_id' => $tr->id,
                        ]);
                        $bk->bookingFees->push($newBill);
                    }
                }
            }
        }
    }

    return view('accounts.fee_management', compact('bookings'));
}
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'booking_fee_id' => 'nullable|exists:booking_fees,id',
            'booking_id'     => 'required|exists:bookings,id',
            'fee_type'       => 'required|in:registry,development,security,transfer',
            'amount'         => 'required|numeric|min:0.01',
            'paid_date'      => 'required|date',
            'payment_mode'   => 'required|in:cash,bank_transfer,cheque,online',
            'receipt_no'     => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $request) {

            // Get or create the bill
            if (empty($validated['booking_fee_id'])) {
                $bf = BookingFee::create([
                    'booking_id'  => $validated['booking_id'],
                    'fee_type'    => $validated['fee_type'],
                    'amount'      => 0,
                    'paid_amount' => 0,
                    'status'      => 'partial',
                ]);
            } else {
                $bf = BookingFee::lockForUpdate()->find($validated['booking_fee_id']);
            }

            // Block duplicate for one-time fees
            if ($bf && in_array($bf->fee_type, ['registry','transfer']) && $bf->status === 'paid') {
                return back()->withErrors(['amount' => 'This fee is already fully paid.']);
            }

            $validated['receipt_no']     = $validated['receipt_no'] ?? ('FEE-'.strtoupper(substr(uniqid(),-6)));
            $validated['booking_fee_id'] = $bf->id;

            $payment = FeePayment::create($validated);

            // Update bill paid_amount + status
            $newPaid = (float)$bf->paid_amount + (float)$validated['amount'];

            if ($bf->fee_type === 'security') {
                // Security is recurring monthly — never fully "paid"
                $bf->update(['paid_amount' => $newPaid, 'status' => 'partial']);
            } elseif ($bf->fee_type === 'transfer') {
                $bf->update(['paid_amount' => $newPaid, 'status' => 'paid']);
                if ($bf->transfer_id) {
                    $this->handleTransferFullyPaid($bf);
                }
            } elseif ($bf->fee_type === 'registry') {
                // Registry: any payment settles it — preset amount is informational only
                $bf->update(['paid_amount' => $newPaid, 'status' => 'paid']);
            } else {
                // Development: open-ended recurring — preset amount is informational only
                $bf->update(['paid_amount' => $newPaid, 'status' => 'partial']);
            }

            return redirect()
                ->route('fee.management', ['q' => $request->input('_search_q')])
                ->with('success', 'Payment of PKR '.number_format($validated['amount']).' recorded. Receipt: '.$validated['receipt_no'])
                ->with('last_payment_id', $payment->id);
        });
    }

    public function updateBillAmount(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'new_amount' => 'required|numeric|min:0',
        ]);

        $bf = BookingFee::findOrFail($id);

        // Reopen registry fee if new total is greater than what's paid
        $newAmount = (float)$request->new_amount;
        $newStatus = $bf->status;

        if ($bf->fee_type === 'registry') {
            // Registry: settled once any payment exists
            $newStatus = (float)$bf->paid_amount > 0 ? 'paid' : 'pending';
        } elseif ($bf->fee_type === 'development') {
            // Development: always open; preset amount is informational only
            $newStatus = (float)$bf->paid_amount > 0 ? 'partial' : 'pending';
        }
        // Security: just update the monthly rate, status stays as-is
        $bf->update(['amount' => $newAmount, 'status' => $newStatus]);

        return redirect()
            ->route('fee.management', ['q' => $request->input('_search_q')])
            ->with('success', 'Bill amount updated to PKR ' . number_format($newAmount) . '.');
    }

   private function handleTransferFullyPaid(BookingFee $bf): void
{
    $transfer = PlotTransfer::with(['fromBooking','toBooking'])->find($bf->transfer_id);
    if (!$transfer) return;

    // ✅ Transfer count check
    $fromBooking = $transfer->fromBooking;
    if ($fromBooking && $fromBooking->plot) {
        $plot = $fromBooking->plot;

        if ((int)($plot->transfer_count ?? 0) >= 5) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'amount' => 'Transfer limit reached. Max 5 transfers allowed.',
            ]);
        }

        $plot->increment('transfer_count');
    }

    // ✅ Mark transfer completed + sync paid fee amount
    $transfer->update([
        'status'              => 'completed',
        'transfer_fee'        => $bf->paid_amount,
        'transfer_fee_status' => 'paid',
        'fee_paid_date'       => $bf->payments()->latest('paid_date')->value('paid_date') ?? now()->format('Y-m-d'),
    ]);

    // ════════════════════════════════════════
    // HANDLE EACH TRANSFER TYPE
    // ════════════════════════════════════════

    if ($transfer->transfer_type === 'ownership') {

        // Seller → transferred
        if ($transfer->fromBooking) {
            $transfer->fromBooking->update(['status' => 'transferred']);
        }

        // Buyer → active/completed
        if ($transfer->toBooking) {
            $toBooking = $transfer->toBooking;

            $toBooking->update([
                'status' => $toBooking->total_price == 0 ? 'completed' : 'active'
            ]);
        }

    } elseif ($transfer->transfer_type === 'partial') {

        if ($transfer->fromBooking) {
            $transfer->fromBooking->update(['status' => 'partial_transferred']);
        }

        if ($transfer->toBooking) {
            $toBooking = $transfer->toBooking;

            $toBooking->update([
                'status' => $toBooking->total_price == 0 ? 'completed' : 'active'
            ]);
        }

    } elseif ($transfer->transfer_type === 'swap') {

        if ($transfer->fromBooking) {
            $transfer->fromBooking->update(['status' => 'swapped']);
        }

        if ($transfer->swap_from_booking_id) {
            $swapBooking = Booking::find($transfer->swap_from_booking_id);
            if ($swapBooking) {
                $swapBooking->update(['status' => 'swapped']);
            }
        }

    } elseif ($transfer->transfer_type === 'internal') {

        if ($transfer->fromBooking) {
            $transfer->fromBooking->update(['status' => 'plot_relocated']);
        }
    }
}

    public function paymentDestroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $payment = FeePayment::findOrFail($id);
        $payment->delete();
        return back()->with('success', 'Fee payment record deleted.');
    }

    public function paymentRefund(\Illuminate\Http\Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01',
            'refund_date'   => 'required|date',
            'refund_note'   => 'nullable|string|max:500',
        ]);

        $payment = FeePayment::findOrFail($id);
        $payment->update([
            'is_refunded'   => true,
            'refund_amount' => $request->refund_amount,
            'refund_date'   => $request->refund_date,
            'refund_note'   => $request->refund_note,
        ]);

        return back()->with('success', 'Refund of PKR '.number_format($request->refund_amount).' recorded for this fee payment.');
    }

    public function history(int $id): \Illuminate\View\View
    {
        $booking = Booking::with([
            'customer',
            'plot',
            'bookingFees.payments',
            'transfersFrom',
            'transfersTo',
        ])->findOrFail($id);

        // ── Security fee monthly ledger ────────────────────────────────
        $securityLedger = null;

        $secBill     = $booking->bookingFees->firstWhere('fee_type', 'security');
        $monthlyRate = (float)($booking->plot->security_fee_amount ?? 0);

        if ($monthlyRate <= 0 && $secBill) {
            // Fallback 1: bill's own amount (set when created during a transfer)
            if ((float)$secBill->amount > 0) {
                $monthlyRate = (float)$secBill->amount;
            } else {
                // Fallback 2: derive from the first recorded payment
                $firstPmt = $secBill->payments->sortBy('paid_date')->first();
                if ($firstPmt && (float)$firstPmt->amount > 0) {
                    $monthlyRate = (float)$firstPmt->amount;
                }
            }
        }

        // Show ledger when: booking has the flag (or a bill already exists) + we have a date + a rate
        $showSecLedger = ($booking->has_security_fee || $secBill) && $booking->booking_date && $monthlyRate > 0;

        if ($showSecLedger) {
            $totalPaid     = $secBill ? (float)$secBill->paid_amount : 0;
            $start         = \Carbon\Carbon::parse($booking->booking_date)->startOfMonth();
            $nowMonth      = \Carbon\Carbon::now()->startOfMonth();
            $terminalSt    = ['transferred','partial_transferred','cancelled','swapped','plot_relocated'];
            if (in_array($booking->status, $terminalSt)) {
                $latestXfer = \App\Models\PlotTransfer::where('from_booking_id', $booking->id)
                    ->whereNotNull('transfer_date')->latest('transfer_date')->first();
                $capRaw = $latestXfer ? $latestXfer->transfer_date : $booking->updated_at;
                $cap = \Carbon\Carbon::parse($capRaw)->startOfMonth();
                if ($cap->lt($nowMonth)) $nowMonth = $cap;
            }
            $monthsElapsed    = (int)$start->diffInMonths($nowMonth) + 1;
            $monthsFullyPaid  = (int)floor($totalPaid / $monthlyRate);
            $partialRemainder = fmod($totalPaid, $monthlyRate);

            $months = [];
            $cursor = $start->copy();
            for ($i = 1; $i <= $monthsElapsed; $i++) {
                if ($i <= $monthsFullyPaid) {
                    $status = 'paid'; $paidM = $monthlyRate; $shortM = 0;
                } elseif ($i === $monthsFullyPaid + 1 && $partialRemainder > 1) {
                    $status = 'partial'; $paidM = $partialRemainder; $shortM = $monthlyRate - $partialRemainder;
                } else {
                    $status = 'unpaid'; $paidM = 0; $shortM = $monthlyRate;
                }
                $months[] = ['no' => $i, 'month' => $cursor->copy(), 'due' => $monthlyRate, 'paid' => $paidM, 'short' => $shortM, 'status' => $status];
                $cursor->addMonth();
            }

            $totalOwed      = $monthsElapsed * $monthlyRate;
            $securityLedger = [
                'monthly_rate'   => $monthlyRate,
                'months'         => $months,
                'months_elapsed' => $monthsElapsed,
                'months_paid'    => $monthsFullyPaid,
                'months_unpaid'  => max(0, $monthsElapsed - $monthsFullyPaid),
                'total_paid'     => $totalPaid,
                'total_owed'     => $totalOwed,
                'outstanding'    => max(0, $totalOwed - $totalPaid),
                'bill'           => $secBill,
                'all_payments'   => $secBill ? $secBill->payments->sortBy('paid_date')->values() : collect(),
            ];
        }

        return view('accounts.fee_history', compact('booking', 'securityLedger'));
    }

    public function receipt(int $id): \Illuminate\View\View
    {
        $payment = FeePayment::with([
            'booking.customer',
            'booking.plot',
            'bookingFee',
        ])->findOrFail($id);

        // ── Security fee: which months does this payment cover? ──────
        $securityMonthRange = null;
        if (($payment->bookingFee->fee_type ?? '') === 'security') {
            $booking     = $payment->booking;
            $monthlyRate = (float)($booking->plot->security_fee_amount ?? 0);

            if ($monthlyRate > 0 && $booking->booking_date) {
                $allPayments = FeePayment::where('booking_fee_id', $payment->booking_fee_id)
                    ->orderBy('paid_date')->orderBy('id')->get();

                $cumulativeBefore = 0;
                foreach ($allPayments as $p) {
                    if ($p->id == $payment->id) break;
                    $cumulativeBefore += (float)$p->amount;
                }
                $cumulativeAfter = $cumulativeBefore + (float)$payment->amount;

                $start       = \Carbon\Carbon::parse($booking->booking_date)->startOfMonth();
                $fromMonthNo = (int)floor($cumulativeBefore / $monthlyRate) + 1;
                $toMonthNo   = (int)floor($cumulativeAfter  / $monthlyRate);

                $securityMonthRange = [
                    'from_no'          => $fromMonthNo,
                    'to_no'            => $toMonthNo,
                    'from_str'         => $start->copy()->addMonths($fromMonthNo - 1)->format('F Y'),
                    'to_str'           => $toMonthNo > 0 ? $start->copy()->addMonths($toMonthNo - 1)->format('F Y') : null,
                    'same'             => $fromMonthNo === $toMonthNo,
                    'completes'        => $toMonthNo >= $fromMonthNo,
                    'monthly_rate'     => $monthlyRate,
                    'cumulative_after' => $cumulativeAfter,
                ];
            }
        }

        // Fill circles based on what has EVER been paid on this booking (not just this payment)
        $bk = $payment->booking;
        $allFees = \App\Models\BookingFee::where('booking_id', $bk->id)->get();
        $cbSecurity    = $allFees->where('fee_type','security')->sum('paid_amount')    > 0 ? '●' : '';
        $cbDevelopment = $allFees->where('fee_type','development')->sum('paid_amount') > 0 ? '●' : '';
        $cbRegistry    = $allFees->where('fee_type','registry')->sum('paid_amount')    > 0 ? '●' : '';
        $cbTransfer    = $allFees->where('fee_type','transfer')->sum('paid_amount')    > 0 ? '●' : '';

        return view('accounts.fee_receipt', compact('payment', 'securityMonthRange',
            'cbSecurity', 'cbDevelopment', 'cbRegistry', 'cbTransfer'));
    }

    // ── Combined receipt: all fees paid for a booking ────────────
    public function combinedReceipt(int $bookingId): \Illuminate\View\View
    {
        $booking = Booking::with([
            'customer',
            'plot',
            'bookingFees.payments' => fn($q) => $q->orderBy('paid_date')->orderBy('id'),
        ])->findOrFail($bookingId);

        $feeOrder = ['security', 'development', 'registry', 'transfer'];

        // Build per-fee-type summary
        $feeSummary = [];
        foreach ($feeOrder as $type) {
            $bf = $booking->bookingFees->firstWhere('fee_type', $type);
            if (!$bf) continue;

            $payments = $bf->payments ?? collect();
            $totalPaid = (float)$bf->paid_amount;
            $hasPaid   = $totalPaid > 0;

            // Security: compute month coverage per payment
            $secMonthInfo = [];
            if ($type === 'security') {
                $monthlyRate = (float)($booking->plot->security_fee_amount ?? 0);
                if ($monthlyRate > 0) {
                    $start = \Carbon\Carbon::parse($booking->booking_date)->startOfMonth();
                    $cumulative = 0;
                    foreach ($payments as $p) {
                        $prevCumulative = $cumulative;
                        $cumulative += (float)$p->amount;
                        $fromNo = (int)floor($prevCumulative / $monthlyRate) + 1;
                        $toNo   = (int)floor($cumulative / $monthlyRate);
                        $label  = $toNo >= $fromNo
                            ? ($fromNo === $toNo
                                ? $start->copy()->addMonths($fromNo - 1)->format('M Y')
                                : $start->copy()->addMonths($fromNo - 1)->format('M Y').' – '.$start->copy()->addMonths($toNo - 1)->format('M Y'))
                            : 'Partial – '.$start->copy()->addMonths($fromNo - 1)->format('M Y');
                        $secMonthInfo[$p->id] = $label;
                    }
                }
            }

            $feeSummary[$type] = [
                'bookingFee'   => $bf,
                'payments'     => $payments,
                'totalPaid'    => $totalPaid,
                'hasPaid'      => $hasPaid,
                'isSettled'    => (bool)$bf->is_settled,
                'secMonthInfo' => $secMonthInfo,
            ];
        }

        // Which fee circles are filled (any payment exists)
        $cbSecurity    = !empty($feeSummary['security']['hasPaid'])    ? '●' : '';
        $cbDevelopment = !empty($feeSummary['development']['hasPaid']) ? '●' : '';
        $cbRegistry    = !empty($feeSummary['registry']['hasPaid'])    ? '●' : '';
        $cbTransfer    = !empty($feeSummary['transfer']['hasPaid'])    ? '●' : '';

        $grandTotal    = collect($feeSummary)->sum(fn($f) => $f['totalPaid']);
        $receiptDate   = now()->format('d-m-Y');
        $receiptNo     = 'CR-'.$booking->customer_booking_id.'-'.now()->format('dmy');

        return view('accounts.fee_combined_receipt', compact(
            'booking', 'feeSummary',
            'cbSecurity', 'cbDevelopment', 'cbRegistry', 'cbTransfer',
            'grandTotal', 'receiptDate', 'receiptNo'
        ));
    }
}
