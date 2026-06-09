<?php

namespace App\Http\Controllers;

use App\Helpers\AppConfig;
use App\Models\Block;
use App\Models\Booking;
use App\Models\BookingFee;
use App\Models\FeePayment;
use App\Models\Customer;
use App\Models\Plot;
use BaconQrCode\Encoder\QrCode as EncoderQrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Flysystem\DecoratedAdapter;


use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\URL;
use App\Traits\HasSocietyConfig;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    use HasSocietyConfig;
public function index(Request $request)
{
    $query = Booking::with(['customer', 'plot'])
        ->orderByDesc('created_at');

    // ── Text search ───────────────────────────────────────────────

    if ($request->filled('search')) {
        $s = trim($request->search);
        $query->where(function ($q) use ($s) {
            $q->where('customer_booking_id', 'like', "%$s%")
              ->orWhereHas('customer', fn($cq) =>
                    $cq->where('name',   'like', "%$s%")
                       ->orWhere('cnic',  'like', "%$s%")
                       ->orWhere('phone', 'like', "%$s%")
                       ->orWhere('mobile','like', "%$s%")
              )
              ->orWhereHas('plot', fn($pq) =>
                    $pq->where('plot_number', 'like', "%$s%")
                       ->orWhere('block',     'like', "%$s%")
              );
        });
    }

    // ── Status filter ─────────────────────────────────────────────
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // ── Pending fee filters ───────────────────────────────────────
    // "Pending" = fee is required but no paid bill exists yet
    if ($request->filled('fee_filter')) {
        match($request->fee_filter) {
            'pending_registry'    => $query->where('has_registry_fee', true)
                ->whereDoesntHave('bookingFees', fn($bf) => $bf->where('fee_type','registry')->where('status','paid')),
            'pending_development' => $query->where('has_development_fee', true)
                ->whereDoesntHave('bookingFees', fn($bf) => $bf->where('fee_type','development')->where('status','paid')),
            'pending_security'    => $query->where('has_security_fee', true)
                ->where(function($q) {
                    $q->whereDoesntHave('bookingFees', fn($bf) => $bf->where('fee_type','security'))
                      ->orWhereHas('bookingFees', fn($bf) => $bf->where('fee_type','security')->where('paid_amount', 0));
                }),
            'paid_registry'       => $query->whereHas('bookingFees', fn($bf) => $bf->where('fee_type','registry')->where('status','paid')),
            'paid_development'    => $query->whereHas('bookingFees', fn($bf) => $bf->where('fee_type','development')->where('status','paid')),
            'paid_security'       => $query->where('has_security_fee', true)
                ->whereHas('bookingFees', fn($bf) => $bf->where('fee_type','security')->where('paid_amount', '>', 0)),
            default => null,
        };
    }

    $all_bookings = $query->paginate(20)->withQueryString();

    // ── Transfer map ──────────────────────────────────────────────
    $transferredStatuses = ['transferred','partial_transferred','swapped','plot_relocated','pending_transfer'];

    $transferredIds = $all_bookings->filter(fn($b) => in_array($b->status, $transferredStatuses))->pluck('id');

    $transferMap = [];
    if ($transferredIds->isNotEmpty()) {
        $transfers = \App\Models\PlotTransfer::where(function($q) use ($transferredIds) {
            $q->whereIn('from_booking_id', $transferredIds)
              ->orWhereIn('to_booking_id', $transferredIds)
              ->orWhereIn('swap_from_booking_id', $transferredIds);
        })->latest()->get();

        foreach ($transfers as $t) {
            if ($t->from_booking_id && !isset($transferMap[$t->from_booking_id]))
                $transferMap[$t->from_booking_id] = $t;
            if ($t->to_booking_id && !isset($transferMap[$t->to_booking_id]))
                $transferMap[$t->to_booking_id] = $t;
            if ($t->transfer_type === 'swap' && $t->swap_from_booking_id && !isset($transferMap[$t->swap_from_booking_id]))
                $transferMap[$t->swap_from_booking_id] = $t;
        }
    }

    // ── Hold map ──────────────────────────────────────────────────
    $allIds = $all_bookings->pluck('id');

    $holdRecords = \App\Models\BookingHold::whereIn('booking_id', $allIds)
        ->where('status', 'hold')
        ->latest()
        ->get()
        ->keyBy('booking_id');

    $holdMap     = $allIds->mapWithKeys(fn($id) => [$id => $holdRecords->has($id)])->toArray();
    $holdInfoMap = $holdRecords->toArray();

    // ── Fee status map ────────────────────────────────────────────
    // IMPORTANT: We load registry + development bills and cross-reference
    // with the booking's has_registry_fee / has_development_fee flags.
    // If a fee is REQUIRED (flag=1) but NO paid bill exists → blocked.
    // If a fee is NOT required (flag=0) → ok regardless of bills.
    $feeRecords = \App\Models\BookingFee::whereIn('booking_id', $allIds)
        ->whereIn('fee_type', ['registry', 'development', 'security'])
        ->get()
        ->groupBy('booking_id');

    $feeStatusMap = [];
    foreach ($all_bookings as $booking) {
        $bid  = $booking->id;
        $fees = $feeRecords->get($bid, collect());

        $registryBill    = $fees->firstWhere('fee_type', 'registry');
        $developmentBill = $fees->firstWhere('fee_type', 'development');
        $securityBill    = $fees->firstWhere('fee_type', 'security');

        // If the booking requires the fee (flag=true), it's only ok when a settled bill exists.
        // If no bill exists yet but the flag is set, treat as pending (not ok).
        $registryOk    = !$booking->has_registry_fee    || ($registryBill    && $registryBill->is_settled);
        $developmentOk = !$booking->has_development_fee || ($developmentBill && $developmentBill->is_settled);

        // Security fee: monthly check — all months from booking_date to now must be paid
        $secMonthlyRate  = (float)($booking->plot->security_fee_amount ?? 0);
        $secMonthsPaid   = null;
        $secMonthsTotal  = null;
        $secMonthsUnpaid = null;
        if ($booking->has_security_fee && $secMonthlyRate > 0 && $booking->booking_date) {
            $secStart = \Carbon\Carbon::parse($booking->booking_date)->startOfMonth();
            $secNow   = \Carbon\Carbon::now()->startOfMonth();
            $terminalSt = ['transferred','partial_transferred','cancelled','swapped','plot_relocated'];
            if (in_array($booking->status, $terminalSt)) {
                $rt = $transferMap[$booking->id] ?? null;
                $capRaw = ($rt && $rt->transfer_date) ? $rt->transfer_date : $booking->updated_at;
                $cap = \Carbon\Carbon::parse($capRaw)->startOfMonth();
                if ($cap->lt($secNow)) $secNow = $cap;
            }
            $secMonthsTotal = (int)$secStart->diffInMonths($secNow) + 1;
            $secTotalPaid   = $securityBill ? (float)$securityBill->paid_amount : 0;
            $secMonthsPaid  = (int)floor($secTotalPaid / $secMonthlyRate);
            $secMonthsUnpaid = max(0, $secMonthsTotal - $secMonthsPaid);
            $securityOk     = $secTotalPaid >= ($secMonthsTotal * $secMonthlyRate);
        } else {
            $securityOk = !$securityBill || $securityBill->is_settled;
        }

        $feeStatusMap[$bid] = [
            'registry_ok'          => $registryOk,
            'development_ok'       => $developmentOk,
            'security_ok'          => $securityOk,
            'all_ok'               => $registryOk && $developmentOk && $securityOk,
            'registry_required'    => $booking->has_registry_fee    || (bool) $registryBill,
            'development_required' => $booking->has_development_fee || (bool) $developmentBill,
            'security_required'    => $booking->has_security_fee,
            'registry_bill'        => $registryBill,
            'development_bill'     => $developmentBill,
            'security_bill'        => $securityBill,
            'sec_months_paid'      => $secMonthsPaid,
            'sec_months_total'     => $secMonthsTotal,
            'sec_months_unpaid'    => $secMonthsUnpaid,
        ];
    }

    return view('layouts.booking', compact(
        'all_bookings',
        'transferMap',
        'holdMap',
        'holdInfoMap',
        'feeStatusMap'
    ));
}

public function hold(Request $request, $id)
{
    $request->validate([
        'remarks' => 'required|string|max:500',
    ]);

    $booking = \App\Models\Booking::findOrFail($id);

    // Prevent double-hold
    if ($booking->isOnHold()) {
        return redirect()->back()->with('error', 'Booking is already on hold.');
    }

    // Can only hold active or pending bookings
    if (!in_array($booking->status, ['active', 'pending', 'pending_transfer'])) {
        return redirect()->back()->with('error', 'Only active or pending bookings can be put on hold.');
    }

    \App\Models\BookingHold::create([
        'booking_id' => $booking->id,
        'status'     => 'hold',
        'remarks'    => $request->remarks,
        'created_by' => auth()->id(),
    ]);

    return redirect()->back()->with('success', "Booking {$booking->customer_booking_id} is now ON HOLD. Payments will be blocked.");
}


public function unhold(Request $request, $id)
{
    $booking = \App\Models\Booking::findOrFail($id);

    $hold = \App\Models\BookingHold::where('booking_id', $booking->id)
                                    ->where('status', 'hold')
                                    ->latest()
                                    ->first();

    if (!$hold) {
        return redirect()->back()->with('error', 'No active hold found for this booking.');
    }

    $hold->update([
        'status'  => 'active',
        'remarks' => $hold->remarks . ' [Released by ' . auth()->user()->name . ' on ' . now()->format('d M Y H:i') . ']',
    ]);

    return redirect()->back()->with('success', "Booking {$booking->customer_booking_id} hold has been RELEASED. Payments are now accepted.");
}

public function searchPlots()
{
    $availablePlots = Plot::where('status', 'available')
                          ->orderBy('block')
                          ->orderBy('street_number')
                          ->orderBy('plot_number')
                          ->get();

     $blocks = Block::orderBy('name')->get();
    $config = $this->societyConfig(); // from HasSocietyConfig trait

    return view('booking.bookingSearch', compact('availablePlots', 'blocks', 'config'));
}
public function NewBooking($plotId)
{
    $plot = Plot::findOrFail($plotId);

    // Check still available
    if ($plot->status !== 'available') {
        return redirect()->route('booking.search')
            ->with('error', 'Plot #'.$plot->plot_number.' is no longer available.');
    }

    $config = $this->societyConfig();

    // Pre-populate fee defaults from the plot
    $plotFees = [
        'has_registry_fee'       => (bool) $plot->has_registry_fee,
        'registry_fee_amount'    => $plot->registry_fee_amount,
        'has_development_fee'    => (bool) $plot->has_development_fee,
        'development_fee_amount' => $plot->development_fee_amount,
        'has_security_fee'       => (bool) $plot->has_security_fee,
        'security_fee_amount'    => $plot->security_fee_amount,
    ];

    return view('booking.bookingIndex', compact('plot', 'config', 'plotFees'));
}



public function bookingStore(Request $request)
{
    $request->validate([
        'plot_id'              => 'required|exists:plots,id',
        'customer_booking_id'  => 'required|string|max:100|unique:bookings,customer_booking_id',
        'name'                 => 'required|string',
        'guardian_name'        => 'required|string',
        'cnic'                 => 'required|string',
        'mobile'               => 'required|string',
        'booking_date'         => 'required|date',
        'customer_pic'         => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        'cnic_pic'             => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
        'cnic_pic_back'        => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
    ]);

    DB::beginTransaction();

    try {
        $plot = Plot::findOrFail($request->plot_id);

        if ($plot->status !== 'available') {
            return back()->withInput()
                ->withErrors(['plot_id' => 'This plot is no longer available.']);
        }

       // ✅ 1. Handle File Uploads with your specific folders
$customerPic = $this->uploadFile($request, 'customer_pic', 'photos');
$cnicPic     = $this->uploadFile($request, 'cnic_pic', 'cnic');
$nomineePic  = $this->uploadFile($request, 'nominee_pic', 'nominee_pics');
$nomFront    = $this->uploadFile($request, 'nominee_cnic_front', 'nominee_cnics');
$nomBack     = $this->uploadFile($request, 'nominee_cnic_back', 'nominee_cnics');
// ✅ 2. Prepare Data Array
$data = [
    'name'                => $request->name,
    'guardian_name'       => $request->guardian_name,
    'phone'               => $request->phone ?? $request->mobile,
    'mobile'              => $request->mobile ?? $request->phone,
    'phone_res'           => $request->phone_res,
    'phone_off'           => $request->phone_off,
    'email'               => $request->email,
    'age'                 => $request->age,
    'nationality'         => $request->nationality ?? 'Pakistani',
    'occupation'          => $request->occupation,
    'city'                => $request->city,
    'residential_address' => $request->residential_address,
    'postal_address'      => $request->postal_address,
    'address'             => $request->residential_address, // Syncing general address field
    'nominee_name'        => $request->nominee_name,
    'nominee_relation'    => $request->nominee_relation,
    'nominee_cnic'        => $request->nominee_cnic,
    'nominee_address'     => $request->nominee_address,
    'status'              => 'active',
];
// ✅ 3. ONLY add file paths to the array if a new file was actually uploaded
// This prevents overwriting existing images with NULL during an update
if ($customerPic) $data['customer_pic'] = $customerPic;
if ($cnicPic)     $data['cnic_pic']     = $cnicPic;
if ($nomineePic)  $data['nominee_pic']  = $nomineePic;
if ($nomFront)    $data['nominee_cnic_front'] = $nomFront;
if ($nomBack)     $data['nominee_cnic_back']  = $nomBack;

       // ✅ 4. Create or Update
$customer = Customer::updateOrCreate(
    ['cnic' => $request->cnic],
    $data
);

        $ref = $request->customer_booking_id;

        // ✅ CREATE BOOKING (IMPORTANT: store in variable)
        $booking = Booking::create([
            'customer_booking_id'    => $ref,
            'customer_id'            => $customer->id,
            'plot_id'                => $plot->id,
            'total_price'            => $request->total_price,
            'down_payment'           => $request->down_payment ?: null,
            'quarterly_installments' => $request->quarterly_installments ?: null,
            'quarterly_amount'       => $request->quarterly_amount ?: null,
            'total_installments'     => $request->total_installments ?: null,
            'monthly_installment'    => $request->monthly_installment ?: null,
            'has_registry_fee'       => $request->boolean('has_registry_fee'),
            'has_development_fee'    => $request->boolean('has_development_fee'),
            'has_security_fee'       => $request->boolean('has_security_fee'),
            'booking_date'           => $request->booking_date,
            'status'                 => 'pending',
            'remarks'                => $request->remarks,
            'created_by'             => auth()->id(),
        ]);

        // ✅ STORE FEES + optional at-booking payment
        $feeDefs = [
            'registry'    => ['has_registry_fee',    'registry_fee_amount',    'reg_paid_amount', 'reg_paid_date', 'reg_payment_mode', 'reg_receipt_no'],
            'development' => ['has_development_fee', 'development_fee_amount', 'dev_paid_amount', 'dev_paid_date', 'dev_payment_mode', 'dev_receipt_no'],
            'security'    => ['has_security_fee',    'security_fee_amount',    'sec_paid_amount', 'sec_paid_date', 'sec_payment_mode', 'sec_receipt_no'],
        ];

        foreach ($feeDefs as $feeType => [$hasFlag, $amtField, $paidAmtField, $paidDateField, $modeField, $receiptField]) {
            if (!$request->boolean($hasFlag)) continue;

            $billAmt   = (float) ($request->$amtField ?? 0);
            $paidAmt   = (float) ($request->$paidAmtField ?? 0);
            $isSettled = $paidAmt > 0 && $billAmt > 0 && $paidAmt >= $billAmt;

            $feeRec = BookingFee::create([
                'booking_id'  => $booking->id,
                'fee_type'    => $feeType,
                'amount'      => $billAmt,
                'paid_amount' => $paidAmt,
                'status'      => $isSettled ? 'paid' : ($paidAmt > 0 ? 'partial' : 'pending'),
            ]);

            if ($paidAmt > 0) {
                FeePayment::create([
                    'booking_fee_id' => $feeRec->id,
                    'booking_id'     => $booking->id,
                    'amount'         => $paidAmt,
                    'paid_date'      => $request->$paidDateField ?: $request->booking_date,
                    'payment_mode'   => $request->$modeField ?? 'cash',
                    'receipt_no'     => $request->$receiptField ?: null,
                    'notes'          => 'Recorded at time of booking.',
                ]);
            }
        }

        // ✅ Update plot status + sync fee flags from booking selection back to plot
        $plotSync = ['status' => 'booked'];
        if ($request->boolean('has_registry_fee') && $request->registry_fee_amount) {
            $plotSync['has_registry_fee']    = true;
            $plotSync['registry_fee_amount'] = $request->registry_fee_amount;
        }
        if ($request->boolean('has_development_fee') && $request->development_fee_amount) {
            $plotSync['has_development_fee']    = true;
            $plotSync['development_fee_amount'] = $request->development_fee_amount;
        }
        if ($request->boolean('has_security_fee') && $request->security_fee_amount) {
            $plotSync['has_security_fee']    = true;
            $plotSync['security_fee_amount'] = $request->security_fee_amount;
        }
        $plot->update($plotSync);

        DB::commit();

        return redirect()->route('index.booking')
            ->with('success', 'Booking saved! Reference: '.$ref);

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->withInput()->with('error', $e->getMessage());
    }
}
private function uploadFile(Request $request, string $field, string $subFolder): ?string
{
    if (!$request->hasFile($field)) return null;

    $file = $request->file($field);

    // Get the name or default to 'customer'
    $originalName = $request->name ?? 'customer';

    // Remove spaces manually to avoid URL issues
    $safeName = str_replace(' ', '_', $originalName);

    // Create a clean filename: name-timestamp.ext
    $fileName = $safeName . '-' . time() . '.' . $file->getClientOriginalExtension();

    // Store in: public/customers/{subFolder}
    $path = $file->storeAs("customers/{$subFolder}", $fileName, 'public');

    return 'storage/' . $path;
}

   public function bookingDetailView($id)
{
    $detail = Booking::with([
        'customer',
        'plot.category',
        'payments',
        'planChanges.changedBy',
    ])->findOrFail($id);

    return view('booking.booking_detail', compact('detail'));
}

// ── Change Installment Plan ───────────────────────────────────────────
public function changePlan(Request $request, $id)
{
    $booking = Booking::with('payments')->findOrFail($id);

    if (!in_array($booking->status, ['active', 'pending'])) {
        return back()->with('error', 'Installment plan can only be changed on active or pending bookings.');
    }

    if (!$booking->total_installments) {
        return back()->with('error', 'This booking has no monthly installment plan to change.');
    }

    $validated = $request->validate([
        'new_total_installments' => ['required', 'integer', 'min:1'],
        'reason'                 => ['nullable', 'string', 'max:500'],
    ]);

    $newTotal = (int) $validated['new_total_installments'];

    // ── Remaining balance calculation ─────────────────────────────────
    $plotCats = ['down_payment', 'installment', 'quarterly_installment', 'plot_balance', 'others'];

    $totalPaid = $booking->payments
        ->where('status', 'paid')
        ->whereIn('payment_category', $plotCats)
        ->sum('amount_paid');

    $remainingBalance = max(0, (float) $booking->total_price - (float) $totalPaid);

    // Count monthly installments already received
    $installmentsPaid = $booking->payments
        ->where('status', 'paid')
        ->where('payment_category', 'installment')
        ->count();

    // New total must be at least equal to what's already been paid
    if ($newTotal < $installmentsPaid) {
        return back()
            ->withInput()
            ->withErrors(['new_total_installments' =>
                'New total ('.$newTotal.') cannot be less than the installments already paid ('.$installmentsPaid.').'
            ]);
    }

    $installmentsRemaining = $newTotal - $installmentsPaid;

    if ($installmentsRemaining <= 0) {
        return back()->with('error', 'All installments are already paid — no remaining installments to redistribute.');
    }

    // ── Exclude future quarterly payments from the monthly portion ────
    // Quarterly payments will cover their own share; monthly installments
    // only need to cover what remains AFTER all outstanding quarterly dues.
    $totalQuarterlyCount  = (int)($booking->quarterly_installments ?? 0);
    $quarterlyAmount      = (float)($booking->quarterly_amount ?? 0);
    $paidQuarterlyCount   = $booking->payments
        ->where('status', 'paid')
        ->where('payment_category', 'quarterly_installment')
        ->count();
    $remainingQuarterlyCount = max(0, $totalQuarterlyCount - $paidQuarterlyCount);
    $futureQuarterlyTotal    = $remainingQuarterlyCount * $quarterlyAmount;

    // Monthly portion = total remaining minus what quarterly will cover
    $monthlyPortion = max(0, $remainingBalance - $futureQuarterlyTotal);

    $newMonthlyAmount = $monthlyPortion > 0
        ? round($monthlyPortion / $installmentsRemaining, 2)
        : 0;

    \DB::transaction(function () use ($booking, $newTotal, $newMonthlyAmount, $installmentsPaid, $remainingBalance, $validated) {
        \App\Models\BookingPlanChange::create([
            'booking_id'        => $booking->id,
            'changed_by'        => auth()->id(),
            'old_installments'  => $booking->total_installments,
            'new_installments'  => $newTotal,
            'old_monthly_amount'=> (float) $booking->monthly_installment,
            'new_monthly_amount'=> $newMonthlyAmount,
            'installments_paid' => $installmentsPaid,
            'remaining_balance' => $remainingBalance,
            'reason'            => $validated['reason'] ?? null,
        ]);

        $booking->update([
            'total_installments'  => $newTotal,
            'monthly_installment' => $newMonthlyAmount,
        ]);
    });

    return redirect()
        ->route('booking.detail.view', $booking->id)
        ->with('success',
            'Installment plan updated to '.$newTotal.' installments. '
            .'New monthly amount: PKR '.number_format($newMonthlyAmount).'.'
        );
}
public function edit($id)
{
    $booking = Booking::with(['customer', 'plot.category', 'payments', 'bookingFees'])
        ->findOrFail($id);

    if (in_array($booking->status, ['transferred', 'completed', 'cancelled'])) {
        return redirect()
            ->route('booking.detail.view', $id)
            ->with('info', 'Booking #' . $booking->customer_booking_id . ' is ' . ucfirst($booking->status) . ' and cannot be edited. Viewing detail instead.');
    }

    return view('booking.booking_edit', compact('booking'));
}
public function update(Request $request, $id)
{
    $booking = Booking::with('bookingFees')->findOrFail($id);

    if (in_array($booking->status, ['transferred', 'completed', 'cancelled'])) {
        return redirect()->back()->with('error', 'This booking cannot be edited.');
    }

    $request->validate([
        'customer_booking_id' => [
            'required', 'string', 'max:100',
            \Illuminate\Validation\Rule::unique('bookings', 'customer_booking_id')->ignore($booking->id),
        ],
        'booking_date'           => 'required|date',
        'remarks'                => 'nullable|string',
        'registry_fee_amount'    => 'nullable|numeric|min:0',
        'development_fee_amount' => 'nullable|numeric|min:0',
    ]);

    // ── Fee guard: cannot disable a fee that already has payments ──────
    foreach (['registry', 'development', 'security'] as $type) {
        $hasFlag = 'has_' . $type . '_fee';
        $enabled = $request->boolean($hasFlag);
        $feeRec  = $booking->bookingFees->where('fee_type', $type)->first();

        if (!$enabled && $feeRec && (float)$feeRec->paid_amount > 0) {
            $label = ucfirst($type);
            return redirect()->back()->withInput()
                ->with('error', "Cannot disable {$label} Fee — PKR " . number_format($feeRec->paid_amount) . ' has already been collected. Clear the fee payments first.');
        }
    }

    // ── Upsert registry and development fees ────────────────────────────
    foreach (['registry', 'development'] as $type) {
        $hasFlag  = 'has_' . $type . '_fee';
        $amtField = $type . '_fee_amount';
        $enabled  = $request->boolean($hasFlag);
        $amount   = (float)($request->$amtField ?? 0);
        $feeRec   = $booking->bookingFees->where('fee_type', $type)->first();

        if ($enabled && $amount > 0) {
            // Never let the amount drop below what's already been paid
            if ($feeRec) {
                $safAmount = max($amount, (float)$feeRec->paid_amount);
                $feeRec->update([
                    'amount' => $safAmount,
                    'status' => (float)$feeRec->paid_amount >= $safAmount
                        ? 'paid'
                        : ((float)$feeRec->paid_amount > 0 ? 'partial' : 'pending'),
                ]);
            } else {
                BookingFee::create([
                    'booking_id'  => $booking->id,
                    'fee_type'    => $type,
                    'amount'      => $amount,
                    'paid_amount' => 0,
                    'status'      => 'pending',
                ]);
            }
        } elseif (!$enabled && $feeRec && (float)$feeRec->paid_amount == 0) {
            // Safe to remove — no payments made
            $feeRec->delete();
        }
    }

    // ── Security fee: toggle only (monthly rate lives on the plot) ──────
    // BookingFee record for security is managed via fee payments, nothing to upsert here.

    $booking->update([
        'customer_booking_id' => $request->customer_booking_id,
        'booking_date'        => $request->booking_date,
        'remarks'             => $request->remarks,
        'has_registry_fee'    => $request->boolean('has_registry_fee'),
        'has_development_fee' => $request->boolean('has_development_fee'),
        'has_security_fee'    => $request->boolean('has_security_fee'),
        'created_by'          => auth()->id(),
    ]);

    return redirect()
        ->route('booking.detail.view', $booking->id)
        ->with('success', 'Booking updated successfully.');
}


public function downloadPDF($id)
{
    // 1. Decode the HashID
    $decoded = Hashids::decode($id);
    if (empty($decoded)) {
        abort(404, "Invalid Booking Reference");
    }
    $bookingId = $decoded[0];

    // 2. Fetch Booking with new 'plot' columns and filtered payments
    // We include 'plot' to access base_price and installment_amount
    $booking = Booking::with([
        'customer',
        'plot.category',
        'payments' => function ($query) {
            $query->where('status', 'paid')
                  // Explicitly exclude possession fees if you don't want them in the ledger
                  ->where('payment_category', '!=', 'possession_fee')
                  ->orderBy('paid_date', 'asc');
        },
        'createdBy',
    ])->findOrFail($bookingId);

    $customer = $booking->customer;

    // 3. Digital ledger for QR-scanned links (Mobile View)
    if (request()->hasValidSignature()) {
        return view('booking.digital_ledger', compact('customer', 'booking'));
    }

    // 4. Auth check for direct PDF downloads
    if (!auth()->check()) {
        abort(403, "Unauthorized. Please scan the QR code on your document to verify.");
    }

    // 5. Generate Signed QR Code for Verification
    $verificationUrl = URL::signedRoute('downloadPDF', ['id' => $id]);
    $renderer = new ImageRenderer(
        new RendererStyle(100, 1),
        new SvgImageBackEnd()
    );
    $writer = new Writer($renderer);
    $qrCode = base64_encode($writer->writeString($verificationUrl));

    // 6. Society configuration (Logo, Address, etc.)
    $sc = $this->societyConfig();

    // 7. Data Normalization for the View
    // This ensures that if the booking columns are empty, the PDF uses the Plot table values
    $booking->display_total_price = $booking->total_price ?: ($booking->plot->base_price ?? 0);
    $booking->display_monthly_installment = $booking->monthly_installment ?: ($booking->plot->installment_amount ?? 0);

    // 8. Generate and Stream the PDF
    $pdf = Pdf::loadView('booking.booking_pdf', compact('booking', 'qrCode', 'sc'))
              ->setPaper('a4', 'portrait')
              ->setOptions([
                  'isRemoteEnabled' => true,
                  'defaultFont' => 'sans-serif'
              ]);

    return $pdf->stream("ZV-Statement-{$booking->customer_booking_id}.pdf");
}
public function bookingReport(Request $request)
{
    $query = Booking::with([
        'customer',
        'plot',
        'payments' => fn($q) => $q->orderBy('paid_date'),
    ]);

    // ── Filters ───────────────────────────────────────────────────
    if ($request->filled('from_date'))    $query->whereDate('booking_date', '>=', $request->from_date);
    if ($request->filled('to_date'))      $query->whereDate('booking_date', '<=', $request->to_date);
    if ($request->filled('booking_type')) $query->where('booking_type', $request->booking_type);
    if ($request->filled('status'))       $query->where('status', $request->status);

    if ($request->filled('payment_plan')) {
        if ($request->payment_plan === 'installment') {
            $query->where('total_installments', '>', 0);
        } elseif ($request->payment_plan === 'cash') {
            $query->where(fn($q) =>
                $q->where('total_installments', 0)->orWhereNull('total_installments')
            );
        }
    }

    $all_bookings = $query->latest('booking_date')->get();

    // ── Categories that reduce the plot balance ───────────────────
    $plotPriceCats    = ['down_payment', 'installment', 'quarterly_installment', 'plot_balance', 'others'];
    $plotPriceCatsSQL = '"down_payment","installment","quarterly_installment","plot_balance","others"';

    // ── Global remaining (active + pending + in-progress statuses) ─
    // Discount on the linked plot is treated as a credit — reduces what customers owe.
    // total_price is already NET (base minus offer discount) — do NOT subtract discount_amount.
    $totalOutstanding = Booking::whereIn('bookings.status', [
        'active', 'pending', 'pending_transfer', 'partial_transferred',
    ])->sum(DB::raw("
        bookings.total_price
        - COALESCE((
            SELECT SUM(amount_paid)
            FROM plot_payments
            WHERE plot_payments.booking_id = bookings.id
              AND plot_payments.status = 'paid'
              AND plot_payments.payment_category IN ({$plotPriceCatsSQL})
        ), 0)
    "));
    $totalOutstanding = max(0, $totalOutstanding);

    // ── Summary stats across the filtered result set ───────────────
    // Project value = original bookings only (parent_booking_id IS NULL, not cancelled).
    // Transfer children (parent set) carry only the *remaining* balance — if we
    // also added the parent's total_price we would double-count the same plot.
    // Example: A books at 50, pays 10, transfers 40 to B.
    //   Revenue  = 50 (A's original price)     — NOT 50+40=90
    //   Collected = 10 (A) + B's payments       — all cash that came in
    //   Remaining = 50 - collected              — what is still owed
    $totalRevenue = $all_bookings->sum(function ($b) {
        if ($b->status === 'cancelled') return 0;
        if (!is_null($b->parent_booking_id)) return 0; // transfer child — skip
        return (float)($b->total_price ?? 0);
    });

    $totalCollected = $all_bookings->sum(function ($b) use ($plotPriceCats) {
        if ($b->status === 'cancelled') return 0;
        return $b->payments
            ->where('status', 'paid')
            ->whereIn('payment_category', $plotPriceCats)
            ->sum('amount_paid');
    });

    // ── Discount stats (from the filtered booking set) ────────────
    $totalDiscount = $all_bookings
        ->where('status', '!=', 'cancelled')
        ->whereNull('parent_booking_id')
        ->sum(fn($b) => (float)($b->plot->discount_amount ?? 0));

    $discountedCount = $all_bookings
        ->where('status', '!=', 'cancelled')
        ->filter(fn($b) => ($b->plot->discount_amount ?? 0) > 0)
        ->count();

    // Gross revenue = contracted value + discounts given = price before concessions
    $grossRevenue = $totalRevenue + $totalDiscount;

    // Remaining — completed bookings contribute 0 (fully settled by definition).
    $discSentinel = 'Settlement discount — waived amount (not collected).';
    $totalRemaining = (float) $all_bookings
        ->filter(fn($b) => in_array($b->status, ['active','pending','pending_transfer']))
        ->sum(function ($b) use ($plotPriceCats, $discSentinel) {
            $paid = $b->payments->where('status','paid')->whereIn('payment_category', $plotPriceCats)
                ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)->sum('amount_paid');
            $disc = $b->payments->where('status','paid')->whereIn('payment_category', $plotPriceCats)
                ->filter(fn($p) => ($p->remarks ?? '') === $discSentinel)->sum('amount_paid')
                + $b->payments->where('status','paid')->whereIn('payment_category', $plotPriceCats)
                ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinel)->sum('discount_amount');
            return max(0, (float)($b->total_price ?? 0) - $paid - $disc);
        });

    // ── Block list for filter dropdown ────────────────────────────
    $blocks = \App\Models\Plot::whereNotNull('block')
                ->distinct()->orderBy('block')->pluck('block');

    return view('booking.booking_report', compact(
        'all_bookings',
        'totalOutstanding',
        'totalRevenue',
        'totalCollected',
        'totalRemaining',
        'totalDiscount',
        'discountedCount',
        'grossRevenue',
        'blocks',
        'plotPriceCats',
    ));
}

public function bookingReceipt($id)
{
    $booking = Booking::with('customer', 'plot', 'pricingPlan')->findOrFail($id);

    // ★ QR now points to the full ledger for this booking
    $ledgerUrl = route('ledger.view', $booking->id);

   $qrCode = null;
    if (AppConfig::qrEnabled()) {
        $renderer = new ImageRenderer(new RendererStyle(80), new SvgImageBackEnd());
        $writer   = new Writer($renderer);
        $qrCode   = base64_encode(
            $writer->writeString(route('ledger.view', $booking->id))
        );
    }

    return view('booking.booking_receipt_pdf', compact('booking', 'qrCode'));
}
public function bookingDestroy(Request $request, $id)
{
    $booking = Booking::with(['plot', 'payments'])->findOrFail($id);

    // Hard delete only if no money has ever been collected
    $totalPaid = $booking->payments->where('status', 'paid')->sum('amount_paid');
    if ($totalPaid > 0) {
        return redirect()->back()->with('error', 'This booking has recorded payments and cannot be hard-deleted. Use Cancel Booking instead to keep payment history.');
    }

    if ($booking->plot) {
        $booking->plot->update(['status' => 'available']);
    }

    $booking->delete();

    return redirect()->back()->with('success', 'Booking deleted and plot is now available.');
}

/**
 * Cancel a booking — keeps all payment history, records reason + agreed refund.
 * Releases the plot back to available.
 */
public function cancelBooking(Request $request, $id)
{
    $request->validate([
        'cancellation_reason'  => 'required|string|max:1000',
        'cancellation_refund'  => 'nullable|numeric|min:0',
    ]);

    $booking = Booking::with('plot')->findOrFail($id);

    if (in_array($booking->status, ['cancelled', 'transferred', 'completed'])) {
        return redirect()->back()->with('error', "Booking is '{$booking->status}' and cannot be cancelled.");
    }

    DB::transaction(function () use ($booking, $request) {
        $booking->update([
            'status'               => 'cancelled',
            'cancellation_reason'  => $request->cancellation_reason,
            'cancellation_refund'  => $request->cancellation_refund ?? 0,
            'cancelled_at'         => now(),
            'cancelled_by'         => auth()->id(),
        ]);

        // Release the plot
        if ($booking->plot) {
            $booking->plot->update(['status' => 'available']);
        }
    });

    return redirect()->route('ledger.view', $booking->id)
        ->with('success', "Booking {$booking->customer_booking_id} has been cancelled. Plot is now available.");
}

public function bookingApplicationForm(string $id): \Illuminate\View\View
{
    $decoded = Hashids::decode($id);
    if (empty($decoded)) abort(404);

    $booking = Booking::with(['customer', 'plot.category'])
                      ->findOrFail($decoded[0]);

    // QR code
    $qrCode = null;
    try {
        $qrUrl  = URL::signedRoute('downloadPDF', ['id' => $id]);
        $qrCode = base64_encode(
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(120)->margin(1)->generate($qrUrl)
        );
    } catch (\Throwable $e) {}

    // Customer photo → base64
    $customerPicB64 = null;
    if ($booking->customer->customer_pic) {
        $path = storage_path('app/public/'.ltrim(str_replace('storage/','',$booking->customer->customer_pic),'/'));
        if (file_exists($path)) {
            $customerPicB64 = 'data:'.mime_content_type($path).';base64,'.base64_encode(file_get_contents($path));
        }
    }

    // Nominee photo → base64
    $nomineePicB64 = null;
    if ($booking->customer->nominee_pic ?? null) {
        $path = storage_path('app/public/'.ltrim(str_replace('storage/','',$booking->customer->nominee_pic),'/'));
        if (file_exists($path)) {
            $nomineePicB64 = 'data:'.mime_content_type($path).';base64,'.base64_encode(file_get_contents($path));
        }
    }

    $sc = $this->societyConfig();

    // ── return HTML view — browser handles print/save as PDF ──
    return view('booking.application_form',
        compact('booking', 'qrCode', 'sc', 'customerPicB64', 'nomineePicB64')
    );
}


public function bookingAgreement(string $id): \Illuminate\View\View
{
    $booking = Booking::with(['customer', 'plot.category'])
                      ->findOrFail($id);

    $sc = $this->societyConfig();

    return view('booking.booking_agreement', compact('booking', 'sc'));
}
public function publicVerify($booking_id)
{
    $booking = \App\Models\Booking::with([
            'customer',
            'plot',
            'plot.category',
            'payments' => fn($q) => $q->where('status', 'paid')->orderBy('created_at'),
        ])
        ->where('customer_booking_id', $booking_id)
        ->first();


    return view('booking.booking_verify', compact('booking'));
}

// ── Weekly Offer / Sunday Property Offer Letter ──────────────────────
public function weeklyOfferLetter($id)
{
    $booking = Booking::with([
        'customer',
        'plot.category',
        'payments' => fn($q) => $q->where('status', 'paid')->orderBy('paid_date'),
        'bookingFees',
    ])->findOrFail($id);

    $sc = $this->societyConfig();

    return view('booking.weekly_offer_letter', compact('booking', 'sc'));
}

public function weeklyOfferPdf($id)
{
    $booking = Booking::with([
        'customer',
        'plot.category',
        'payments' => fn($q) => $q->where('status', 'paid')->orderBy('paid_date'),
        'bookingFees',
    ])->findOrFail($id);

    $sc = $this->societyConfig();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('booking.weekly_offer_letter', compact('booking', 'sc'))
        ->setPaper([0, 0, 595.28, 419.53], 'landscape'); // A5 landscape in points

    return $pdf->download('possession-slip-' . ($booking->customer_booking_id ?? $id) . '.pdf');
}

// ── Customer Statement ────────────────────────────────────────────────
public function customerStatement($customerId)
{
    $plotPriceCats       = ['down_payment','installment','quarterly_installment','plot_balance','others'];
    $transferredStatuses = ['transferred', 'swapped', 'plot_relocated', 'partial_transferred'];

    $customer = Customer::findOrFail($customerId);

    $bookings = Booking::with([
        'plot.category',
        'plot.pricingPlan',
        'payments'             => fn($q) => $q->where('status','paid')->orderBy('paid_date'),
        'bookingFees.payments' => fn($q) => $q->orderBy('paid_date'),
    ])
    ->where('customer_id', $customerId)
    ->whereNotIn('status', ['cancelled'])
    ->get();

    $bookingData = $bookings->map(function ($b) use ($plotPriceCats, $transferredStatuses) {
        $isTransferredOut = in_array($b->status, $transferredStatuses);
        $isTransferIn     = !is_null($b->parent_booking_id);

        $fullPlotPrice = $isTransferIn
            ? (float)($b->plot->final_price ?? $b->plot->base_price ?? $b->total_price)
            : (float)($b->total_price ?? 0);

        $outgoingTransfer = $b->transfersFrom->where('status','completed')->sortByDesc('transfer_date')->first();
        $incomingTransfer = $b->transfersTo->where('status','completed')->sortByDesc('transfer_date')->first();

        $discSentinelStmt = 'Settlement discount — waived amount (not collected).';

        $paid        = $b->payments->whereIn('payment_category', $plotPriceCats)
                         ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinelStmt)
                         ->sum('amount_paid');
        $plotDiscount = (float)($b->plot->discount_amount ?? 0);
        $payDiscount  = $b->payments
                         ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinelStmt)
                         ->sum('discount_amount')
                         + $b->payments
                         ->filter(fn($p) => ($p->remarks ?? '') === $discSentinelStmt)
                         ->sum('amount_paid');
        $discount = $plotDiscount + $payDiscount;

        // remaining = what THIS customer still owes on their obligation
        // (transferred-out = 0 because they gave the plot away)
        // total_price is already net (base minus plot discount) — only subtract payment-level credits
        $remaining = $isTransferredOut ? 0 : max(0, (float)($b->total_price ?? 0) - $paid - $payDiscount);

        $feePayments = $b->bookingFees->flatMap(fn($bf) => $bf->payments->map(fn($fp) => [
            'date'     => $fp->paid_date,
            'fee_type' => $bf->fee_type,
            'amount'   => $fp->amount,
            'receipt'  => $fp->receipt_no,
            'mode'     => $fp->payment_mode,
        ]))->sortBy('date')->values();

        return [
            'booking'            => $b,
            'full_plot_price'    => $fullPlotPrice,   // full market value of the plot
            'paid'               => $paid,
            'discount'           => $discount,
            'remaining'          => $remaining,
            'is_transferred_out'  => $isTransferredOut,
            'is_transfer_in'      => $isTransferIn,
            'outgoing_transfer'   => $outgoingTransfer,
            'incoming_transfer'   => $incomingTransfer,
            'registry_fee'        => $b->bookingFees->firstWhere('fee_type','registry'),
            'development_fee'     => $b->bookingFees->firstWhere('fee_type','development'),
            'security_fee'        => $b->bookingFees->firstWhere('fee_type','security'),
            'transfer_fee'        => $b->bookingFees->firstWhere('fee_type','transfer'),
            'fee_payments'        => $feePayments,
        ];
    });

    $activeData = $bookingData->filter(fn($r) => !$r['is_transferred_out']);

    // Total Plot Value  = sum of each booking's total_price across ALL bookings
    // Total Paid        = every rupee this customer has ever paid
    // Total Discount    = sum of all discount credits given
    // Total Remaining   = what is still owed on current/active plots only
    $grandTotal     = $bookingData->sum(fn($r) => (float) $r['booking']->total_price);
    $grandPaid      = $bookingData->sum('paid');
    $grandDiscount  = $bookingData->sum('discount');
    $grandRemaining = $activeData->sum('remaining');

    $sc = $this->societyConfig();

    $pdf = Pdf::loadView('customers.statement_pdf', compact(
        'customer','bookingData','grandTotal','grandPaid','grandDiscount','grandRemaining','sc'
    ))
    ->setPaper('a4','portrait')
    ->setOptions(['isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);

    return $pdf->stream("Statement-{$customer->name}.pdf");
}

public function sendCustomerStatement(Request $request, $customerId)
{
    $plotPriceCats = ['down_payment','installment','quarterly_installment','plot_balance','others'];

    $customer = Customer::findOrFail($customerId);

    if (!$customer->email) {
        return redirect()->back()->with('error', 'Customer has no email address on file.');
    }

    $transferredStatuses = ['transferred', 'swapped', 'plot_relocated', 'partial_transferred'];

    $bookings = Booking::with([
        'plot.category',
        'plot.pricingPlan',
        'payments'                     => fn($q) => $q->where('status','paid')->orderBy('paid_date'),
        'bookingFees.payments'         => fn($q) => $q->orderBy('paid_date'),
        'transfersFrom.toCustomer',
        'transfersTo.fromCustomer',
        'transfersTo.fromBooking.customer',
    ])
    ->where('customer_id', $customerId)
    ->whereNotIn('status', ['cancelled'])
    ->get();

    $bookingData = $bookings->map(function ($b) use ($plotPriceCats, $transferredStatuses) {
        $isTransferredOut = in_array($b->status, $transferredStatuses);
        $isTransferIn     = !is_null($b->parent_booking_id);

        $fullPlotPrice = $isTransferIn
            ? (float)($b->plot->final_price ?? $b->plot->base_price ?? $b->total_price)
            : (float)($b->total_price ?? 0);

        // Transfer details for display
        $outgoingTransfer = $b->transfersFrom->where('status','completed')->sortByDesc('transfer_date')->first();
        $incomingTransfer = $b->transfersTo->where('status','completed')->sortByDesc('transfer_date')->first();

        $discSentinelEmail = 'Settlement discount — waived amount (not collected).';
        $paid        = $b->payments->whereIn('payment_category', $plotPriceCats)
                         ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinelEmail)
                         ->sum('amount_paid');
        $plotDiscountE = (float)($b->plot->discount_amount ?? 0);
        $payDiscountE  = $b->payments
                         ->filter(fn($p) => ($p->remarks ?? '') !== $discSentinelEmail)
                         ->sum('discount_amount')
                         + $b->payments
                         ->filter(fn($p) => ($p->remarks ?? '') === $discSentinelEmail)
                         ->sum('amount_paid');
        $discount  = $plotDiscountE + $payDiscountE;
        // total_price is already net (base minus plot discount) — only subtract payment-level credits
        $remaining = $isTransferredOut ? 0 : max(0, (float)($b->total_price ?? 0) - $paid - $payDiscount);

        $feePayments = $b->bookingFees->flatMap(fn($bf) => $bf->payments->map(fn($fp) => [
            'date'     => $fp->paid_date,
            'fee_type' => $bf->fee_type,
            'amount'   => $fp->amount,
            'receipt'  => $fp->receipt_no,
            'mode'     => $fp->payment_mode,
        ]))->sortBy('date')->values();
        return [
            'booking'            => $b,
            'full_plot_price'    => $fullPlotPrice,
            'paid'               => $paid,
            'discount'           => $discount,
            'remaining'          => $remaining,
            'is_transferred_out'  => $isTransferredOut,
            'is_transfer_in'      => $isTransferIn,
            'outgoing_transfer'   => $outgoingTransfer,
            'incoming_transfer'   => $incomingTransfer,
            'registry_fee'        => $b->bookingFees->firstWhere('fee_type','registry'),
            'development_fee'     => $b->bookingFees->firstWhere('fee_type','development'),
            'security_fee'        => $b->bookingFees->firstWhere('fee_type','security'),
            'transfer_fee'        => $b->bookingFees->firstWhere('fee_type','transfer'),
            'fee_payments'        => $feePayments,
        ];
    });

    $activeData     = $bookingData->filter(fn($r) => !$r['is_transferred_out']);
    $grandTotal     = $bookingData->sum(fn($r) => (float) $r['booking']->total_price);
    $grandPaid      = $bookingData->sum('paid');
    $grandDiscount  = $bookingData->sum('discount');
    $grandRemaining = $activeData->sum('remaining');

    $sc = $this->societyConfig();

    $pdfContent = Pdf::loadView('customers.statement_pdf', compact(
        'customer','bookingData','grandTotal','grandPaid','grandDiscount','grandRemaining','sc'
    ))
    ->setPaper('a4','portrait')
    ->setOptions(['isRemoteEnabled' => true, 'defaultFont' => 'sans-serif'])
    ->output();

    $societyName = $sc['name'] ?? config('app.name');

    try {
        \Illuminate\Support\Facades\Mail::raw(
            "Dear {$customer->name},\n\nPlease find your account statement attached.\n\nRegards,\n{$societyName}",
            function ($message) use ($customer, $pdfContent, $societyName) {
                $message->to($customer->email, $customer->name)
                    ->subject("Your Account Statement — {$societyName}")
                    ->attachData($pdfContent, "Statement-{$customer->name}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            }
        );

        return redirect()->back()->with('success', "Statement sent to {$customer->email}");
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Email failed: ' . $e->getMessage());
    }
}

public function cancellationNoticePdf($id)
{
    $booking = \App\Models\Booking::with([
        'customer',
        'plot.category',
        'payments' => fn($q) => $q->where('status','paid')->orderBy('paid_date'),
        'cancelledBy',
        'createdBy',
    ])->findOrFail($id);

    if ($booking->status !== 'cancelled') {
        abort(404, 'This booking is not cancelled.');
    }

    $plotPriceCats = ['down_payment','installment','quarterly_installment','plot_balance','others'];
    $totalPaid = $booking->payments
        ->where('status','paid')
        ->whereIn('payment_category', $plotPriceCats)
        ->sum('amount_paid');

    $refundAmount = (float)($booking->cancellation_refund ?? 0);
    $netRetained  = max(0, $totalPaid - $refundAmount);

    $sc = $this->societyConfig();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'booking.cancellation_notice_pdf',
        compact('booking', 'totalPaid', 'refundAmount', 'netRetained', 'sc')
    )
    ->setPaper('a4', 'portrait')
    ->setOptions(['isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);

    return $pdf->stream("Cancellation-{$booking->customer_booking_id}.pdf");
}
}
