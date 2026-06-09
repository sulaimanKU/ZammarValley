<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
     public function indexCustomer(Request $request)
    {
        $query = Customer::withCount('booking as bookings_count');

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name',  'like', "%{$s}%")
                  ->orWhere('cnic',  'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('city',  'like', "%{$s}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        $totalCustomers  = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $newThisMonth    = Customer::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)->count();

        return view('customers.index', compact(
            'customers',
            'totalCustomers',
            'activeCustomers',
            'newThisMonth'
        ));
    }

    // ── Store (Create) ─────────────────────────────────────────
  public function store(Request $request)
{
    // 1. Direct Validation (Cleaner than manual Validator::make)
    $request->validate([
        'name'                => 'required|string|max:255',
        'cnic'                => 'required|string|unique:customers,cnic|min:13|max:15',
        'phone'               => 'required|string|max:20',
        'guardian_name'       => 'nullable|string|max:255',
        'email'               => 'nullable|email|max:255',
        'occupation'          => 'nullable|string|max:255',
        'age'                 => 'nullable|integer',
        'nationality'         => 'nullable|string|max:100',
        'residential_address' => 'nullable|string|max:500',
        'postal_address'      => 'nullable|string|max:500',
    ], [
        'cnic.unique' => 'A customer with this CNIC is already registered.',
        'cnic.min'    => 'CNIC must be at least 13 characters.',
    ]);

    // 2. Streamlined File Upload Helper
    $upload = function($file, $folder) use ($request) {
        if (!$file) return null;
        $name = Str::slug($request->name) . '-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        // Stores in storage/app/public/customers/{folder}
        return 'storage/' . $file->storeAs("customers/{$folder}", $name, 'public');
    };

    // 3. Create Record with all Zamar Valley fields
    $customer = Customer::create([
        'name'                => $request->name,
        'guardian_name'       => $request->guardian_name,
        'cnic'                => $request->cnic,
        'phone'               => $request->phone,
        'mobile'              => $request->phone, // Syncing phone to mobile column
        'phone_off'           => $request->phone_off,
        'phone_res'           => $request->phone_res,
        'email'               => $request->email,
        'occupation'          => $request->occupation,
        'age'                 => $request->age,
        'nationality'         => $request->nationality,
        'residential_address' => $request->residential_address,
        'postal_address'      => $request->postal_address,
        'address'             => $request->residential_address, // Shared value
        'status'              => $request->status ?? 'active',

        // Nominee Data
        'nominee_name'        => $request->nominee_name,
        'nominee_relation'    => $request->nominee_relation,
        'nominee_cnic'        => $request->nominee_cnic,
        'nominee_address'     => $request->nominee_address,

        // Media Uploads (Correct Folders)
        'customer_pic'        => $upload($request->file('customer_pic'), 'photos'),
        'cnic_pic'            => $upload($request->file('cnic_pic'), 'cnic'),
        'nominee_pic'         => $upload($request->file('nominee_pic'), 'nominee_pics'),
        'nominee_cnic_front'  => $upload($request->file('nominee_cnic_front'), 'nominee_cnics'),
        'nominee_cnic_back'   => $upload($request->file('nominee_cnic_back'), 'nominee_cnics'),
    ]);

    // 4. Response
    return redirect()->route('index.customer')
        ->with('success', 'Customer "' . $request->name . '" has been registered successfully.');
}
    // ── Show (Detail) ──────────────────────────────────────────
public function show($id)
{
    $customer = Customer::with([
        'booking.plot.pricingPlan',
        'booking.bookingFees',
        'booking.payments' => fn($q) => $q->where('status', 'paid')
    ])->findOrFail($id);

    $transferredStatuses = ['transferred', 'swapped', 'plot_relocated', 'partial_transferred'];

    // Per-booking details
    $bookingDetails = $customer->booking->map(function ($b) use ($transferredStatuses) {
        $isTransferredOut = in_array($b->status, $transferredStatuses);
        $isTransferIn     = !is_null($b->parent_booking_id);

        // For transfer-in bookings, total_price = only the remaining balance carried forward,
        // not the full plot value. Use plot->final_price for the true full value.
        $plotPrice = $isTransferIn
            ? (float)($b->plot->final_price ?? $b->plot->base_price ?? $b->total_price)
            : ((float)$b->total_price ?: (float)($b->plot->base_price ?? 0));

        $bPaid      = $b->payments->sum('amount_paid');
        // remaining = what the customer still owes on their own obligation (booking->total_price)

        // Discount info
        $rawBase      = (float)($b->plot->custom_price ?? $b->plot->base_price ?? 0);
        $plotDiscount = (float)($b->plot->discount_amount ?? 0);
        // Payment/settlement discounts (new column + old sentinel records)
        $discSentinel  = 'Settlement discount — waived amount (not collected).';
        $payDiscount   = $b->payments
            ->where('status', 'paid')
            ->sum('discount_amount')
            + $b->payments
                ->where('status', 'paid')
                ->where('remarks', $discSentinel)
                ->sum('amount_paid');
        $totalDiscount = $plotDiscount + $payDiscount;
       $bRemaining = $isTransferredOut ? 0 : max(0, (float)($b->total_price ?? 0) - $bPaid - $totalDiscount);

        $bpill = match(true) {
            $b->status === 'active'                         => 'pill-blue',
            $b->status === 'completed'                      => 'pill-green',
            in_array($b->status, $transferredStatuses)      => 'pill-purple',
            $b->status === 'pending_transfer'               => 'pill-amber',
            $b->status === 'cancelled'                      => 'pill-red',
            default                                         => 'pill-blue',
        };

        return [
            'booking'            => $b,
            'plot_price'         => $plotPrice,
            'paid'               => $bPaid,
            'remaining'          => $bRemaining,
            'pill'               => $bpill,
            'is_transferred_out' => $isTransferredOut,
            'is_transfer_in'     => $isTransferIn,
            'base_price'         => $rawBase,
            'plot_discount'      => $plotDiscount,
            'pay_discount'       => $payDiscount,
            'total_discount'     => $totalDiscount,
        ];
    });

    // Totals — only active/current bookings; transferred-out bookings
    // are excluded because the customer gave up those plots and the
    // payments they made on them belong to the transfer history, not
    // their current financial obligation.
    $activeBookings   = $bookingDetails->filter(fn($b) => !$b['is_transferred_out']);
    // Total Value   = sum of every booking's total_price (full financial commitment)
    // Total Paid    = all payments ever made across all bookings
    // Outstanding   = remaining owed on active/current bookings only
    $totalValue       = $bookingDetails->sum(fn($b) => (float) $b['booking']->total_price);
    $totalPaid        = $bookingDetails->sum('paid');
    $totalOutstanding = $activeBookings->sum('remaining');
    $totalDiscounts   = $bookingDetails->sum('total_discount');

    return view('customers.show', compact(
        'customer', 'totalPaid', 'totalValue', 'totalOutstanding', 'bookingDetails', 'totalDiscounts'
    ));
}

    // ── Edit (show edit modal data via JSON for AJAX, or redirect) ──
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    // ── Update ─────────────────────────────────────────────────
   public function update(Request $request, $id)
{
    $customer = Customer::findOrFail($id);

    // 1. Validation (Matches Store logic + dynamic CNIC unique check)
    $request->validate([
        'name'                => 'required|string|max:255',
        'cnic'                => 'required|string|min:13|max:15|unique:customers,cnic,' . $id,
        'phone'               => 'required|string|max:20',
        'guardian_name'       => 'nullable|string|max:255',
        'email'               => 'nullable|email|max:255',
        'occupation'          => 'nullable|string|max:255',
        'age'                 => 'nullable|integer',
        'nationality'         => 'nullable|string|max:100',
        'residential_address' => 'nullable|string|max:500',
        'postal_address'      => 'nullable|string|max:500',

    ], [
        'cnic.unique' => 'This CNIC is already assigned to another customer.',
    ]);

    // 2. The "Smart" Upload Helper (Deletes old file if new one arrives)
    $uploadOrKeep = function($inputName, $folder, $currentPath) use ($request) {
        if (!$request->hasFile($inputName)) {
            return $currentPath; // Return existing path if no new file
        }

        // Delete old file from storage if it exists
        // Note: We strip 'storage/' from the beginning because storage::delete expects the relative path
        if ($currentPath) {
            $oldFile = str_replace('storage/', '', $currentPath);
            \Storage::disk('public')->delete($oldFile);
        }

        $file = $request->file($inputName);
        $name = \Str::slug($request->name) . '-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        return 'storage/' . $file->storeAs("customers/{$folder}", $name, 'public');
    };

    // 3. Update Record (Syncing all Zamar Valley fields)
    $customer->update([
        'name'                => $request->name,
        'guardian_name'       => $request->guardian_name,
        'cnic'                => $request->cnic,
        'phone'               => $request->phone,
        'mobile'              => $request->phone,
        'phone_off'           => $request->phone_off,
        'phone_res'           => $request->phone_res,
        'email'               => $request->email,
        'occupation'          => $request->occupation,
        'age'                 => $request->age,
        'nationality'         => $request->nationality,
        'residential_address' => $request->residential_address,
        'postal_address'      => $request->postal_address,
        'address'             => $request->residential_address,
        'city'                => $request->city,

        // Nominee Data
        'nominee_name'        => $request->nominee_name,
        'nominee_relation'    => $request->nominee_relation,
        'nominee_cnic'        => $request->nominee_cnic,
        'nominee_address'     => $request->nominee_address,

        // Media Updates
        'customer_pic'        => $uploadOrKeep('customer_pic', 'photos', $customer->customer_pic),
        'cnic_pic'            => $uploadOrKeep('cnic_pic', 'cnic', $customer->cnic_pic),
        'nominee_pic'         => $uploadOrKeep('nominee_pic', 'nominee_pics', $customer->nominee_pic),
        'nominee_cnic_front'  => $uploadOrKeep('nominee_cnic_front', 'nominee_cnics', $customer->nominee_cnic_front),
        'nominee_cnic_back'   => $uploadOrKeep('nominee_cnic_back', 'nominee_cnics', $customer->nominee_cnic_back),
    ]);

    // 4. Response
    return redirect()->route('index.customer')
        ->with('success', 'Customer profile for "' . $customer->name . '" updated successfully.');
}

    // ── Destroy ────────────────────────────────────────────────
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        // Prevent deletion if they have active bookings
        if ($customer->booking()->whereIn('status', ['active', 'pending_transfer'])->exists()) {
            return redirect()->route('index.customer')
                ->with('error', 'Cannot delete "' . $customer->name . '" — they have active bookings.');
        }

        if ($customer->customer_pic) Storage::disk('public')->delete($customer->customer_pic);
        if ($customer->cnic_pic)     Storage::disk('public')->delete($customer->cnic_pic);

        $customer->delete();

        return redirect()->route('index.customer')
            ->with('success', 'Customer deleted successfully.');
    }
}
