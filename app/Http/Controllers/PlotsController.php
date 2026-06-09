<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Booking;
use App\Models\BookingFee;
use App\Models\City;
use App\Models\Customer;
use App\Models\FeePayment;
use App\Models\Plot;
use App\Models\PlotCategory;
use App\Models\PlotPayment;
use App\Models\PlotPricingPlan;
use App\Models\PropertyFeature;
use App\Models\Sector;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as DBFacade;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\URL;
use App\Traits\HasSocietyConfig;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
class PlotsController extends Controller
{

 use HasSocietyConfig;


public function index()
{
    $plots = Plot::with('category')
        ->orderBy('created_at', 'desc')
        ->get();

    $stats = [
        'total'           => Plot::count(),
        'available'       => Plot::where('status', 'available')->count(),
        'sold'            => Plot::where('status', 'sold')->count(),
        'booked'          => Plot::where('status', 'booked')->count(),
        'reserved'        => Plot::where('status', 'reserved')->count(),
        'total_marla'     => (float) Plot::where('unit', 'Marla')->sum('size'),
        'remaining_marla' => (float) Plot::where('unit', 'Marla')->where('status', 'available')->sum('size'),
    ];

    // Build a fee-status map keyed by plot_id.
    // We look at each plot's ACTIVE booking (not cancelled/transferred/swapped)
    // and its booking_fees to determine which fees are required and whether they're paid.
    $plotIds = $plots->pluck('id');

    $activeBookings = \App\Models\Booking::with('bookingFees')
        ->whereIn('plot_id', $plotIds)
        ->whereNotIn('status', ['cancelled', 'transferred', 'swapped', 'plot_relocated'])
        ->get()
        ->keyBy('plot_id');   // one active booking per plot

    $plotFeeMap = [];
    foreach ($plots as $plot) {
        $booking = $activeBookings->get($plot->id);

        if (!$booking) {
            $plotFeeMap[$plot->id] = null; // no active booking — show nothing
            continue;
        }

        $fees = $booking->bookingFees->groupBy('fee_type');

        $registryBill    = $fees->get('registry',    collect())->first();
        $developmentBill = $fees->get('development',  collect())->first();
        $securityBill    = $fees->get('security',     collect())->first();

        $regRequired  = (bool) $booking->has_registry_fee;
        $devRequired  = (bool) $booking->has_development_fee;
        $secRequired  = (bool) $booking->has_security_fee;

        $regOk  = !$regRequired  || ($registryBill    && $registryBill->is_settled);
        $devOk  = !$devRequired  || ($developmentBill && $developmentBill->is_settled);
        $secOk  = !$secRequired  || ($securityBill    && $securityBill->is_settled);

        $plotFeeMap[$plot->id] = [
            'booking_id'           => $booking->id,
            'registry_required'    => $regRequired,
            'development_required' => $devRequired,
            'security_required'    => $secRequired,
            'registry_ok'          => $regOk,
            'development_ok'       => $devOk,
            'security_ok'          => $secOk,
        ];
    }

    return view('layouts.plots', [
        'plots'        => $plots,
        'stats'        => $stats,
        'plotFeeMap'   => $plotFeeMap,
        'categories'   => PlotCategory::orderBy('name')->get(),
        'pricingPlans' => PlotPricingPlan::where('is_active', 1)->get(),
        'cities'       => City::orderBy('name')->get(),
        'societies'    => Society::orderBy('name')->get(),
        'sectors'      => Sector::orderBy('name')->get(),
        'blocks'       => Block::orderBy('name')->get(),
        'features'     => PropertyFeature::orderBy('name')->get(),
    ]);
}

public function addPlot()
{
    $categories   = PlotCategory::orderBy('name')->get();
  $pricingPlans = PlotPricingPlan::where('is_active', 1)->get()->map(function($p) {
    return [
        'id'                 => $p->id,
        'plot_category_id'   => $p->plot_category_id,
        'size'               => $p->size,
        'unit'               => $p->unit,
        'base_price'         => $p->base_price,
        'down_payment'       => $p->down_payment,
        'processing_fee'     => $p->processing_fee,
        'total_installments' => $p->total_installments,
        'installment_amount' => $p->installment_amount ?? null,
        'is_active'          => $p->is_active,
    ];
});
    $cities       = City::orderBy('name')->get();
    $societies    = Society::orderBy('name')->get();
    $sectors      = Sector::orderBy('name')->get();
$blocks = Block::orderBy('name')->get();
    $features     = PropertyFeature::orderBy('name')->get();

    return view('plots_detail.addPlotView', compact(
        'categories',
        'pricingPlans',
        'cities',
        'societies',
        'sectors',
        'blocks',
        'features'
    ));
}



    public function categoriesView(){
$categories = PlotCategory::all();
     return view('plots_detail.categoriesView',compact('categories'));
    }

    public function createCategory(){
         $propFeature = PropertyFeature::all();
        return view('plots_detail.createCategory',compact('propFeature'));
    }
    public function categoryStore(Request $req){
        // dd($req->all());
        // die();
       $validate = $req->validate([
           'name'          => 'required',
           'property_type' => 'required',
           'prefix'        => 'nullable|string|max:10', // Add this line
    ]);

        PlotCategory::create($validate);

        return redirect()->back()->with('success','New Category Plan created successfully!');
    }

    public function show($id)
{

    $category = PlotCategory::findOrFail($id);

    return view('plots_detail.categoryshow', compact('category'));
}

public function categoryUpdate(Request $req, $id) {
    $validate = $req->validate([
        'name'               => 'required|string',
        'prefix'             => 'nullable|string',
        'property_type'      => 'required',

    ]);

    $category = PlotCategory::findOrFail($id);
    $category->update($validate);

    return redirect()->route('categories.view')->with('success', 'Category updated successfully!');
}


public function categoryEdit($id) {
      $propFeature = PropertyFeature::all();
    $category = PlotCategory::findOrFail($id);
    return view('plots_detail.categoryEdit', compact('category','propFeature'));
}
 public function propertyFeatureView(){

       $pro_features = PropertyFeature::all();
        return view('property.propertyFeatureView',compact('pro_features'));
    }

    public function propertyFeatureStore(Request $request){
        $request->validate([
            'name' => 'required'
        ]);

        PropertyFeature::create([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success','Property Feature Added');
    }

    public function propertyFeatureEdit($id){
 $propertyFeatureEdit = PropertyFeature::findOrFail($id);
          return view('property.propertyFeatureEdit' ,compact('propertyFeatureEdit'));
    }

    public function propertyFeatureDestroy($id){
        $propertyFeatureDestroy = PropertyFeature::findOrFail($id);
        $propertyFeatureDestroy->delete();
        return redirect()->back()->with('success','Deleted Successcefully');
    }

    public function propertyFeatureUpdate(Request $request,$id){

            $validate = $request->validate([
                'name' => 'required'
            ]);

            $update = PropertyFeature::findOrFail($id);

            $update->update($request->all());

            return redirect()->back()->with('success','Property Feature Updated ');

    }

public function plotStore(Request $request)
{
    $request->validate([
        'plot_image'  => 'nullable|image|mimes:jpg,jpeg,png',
        'plot_number' => 'required',
    ]);

    $file_path = null;

    try {
        // Duplicate check: same plot number + same block + same street
        $duplicate = Plot::where('plot_number', $request->plot_number)
            ->when($request->filled('block'),         fn($q) => $q->where('block',         $request->block))
            ->when($request->filled('street_number'), fn($q) => $q->where('street_number', $request->street_number))
            ->exists();

        if ($duplicate) {
            return back()->withInput()
                ->withErrors(['plot_number' => 'Plot #'.$request->plot_number.' already exists in Block '.$request->block.' / Street '.$request->street_number.'.']);
        }

        // Image upload
        if ($request->hasFile('plot_image')) {
            $file      = $request->file('plot_image');
            $fileName  = 'plot_'.$request->plot_number.'_'.time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('plot_images', $fileName, 'public');
            $file_path = 'storage/plot_images/'.$fileName;
        }

        $plot = Plot::create([
            // Reference
            'plot_category_id'       => $request->plot_category_id      ?: null,

            // Location
            'city'                   => $request->city,
            'society'                => $request->society,
            'sector'                 => $request->sector,
            'block'                  => $request->block,
            'street_number'          => $request->street_number,
            'street_size'            => $request->street_size            ?: null,

            // Plot identity
            'plot_number'            => $request->plot_number,
            'size'                   => $request->size,
            'unit'                   => $request->unit,
            'status'                 => 'available',
            'price_type'             => $request->price_type             ?? 'cash',

            // Pricing — 3-tier model
            'base_price'             => $request->base_price             ?: null,
            'discount_amount'        => $request->filled('discount_amount') ? $request->discount_amount : null,
            'discount_reason'        => $request->filled('discount_reason') ? trim($request->discount_reason) : null,
            'down_payment'           => $request->down_payment           ?: null,

            // Quarterly instalments
            'quarterly_installments' => $request->quarterly_installments ?: null,
            'quarterly_amount'       => $request->quarterly_amount       ?: null,

            // Monthly instalments
            'total_installments'     => $request->total_installments     ?: null,
            'installment_amount'     => $request->installment_amount     ?: null,

            // Fee flags + default amounts
            'has_registry_fee'        => $request->boolean('has_registry_fee'),
            'registry_fee_amount'     => $request->boolean('has_registry_fee')    ? ($request->registry_fee_amount    ?: null) : null,
            'has_development_fee'     => $request->boolean('has_development_fee'),
            'development_fee_amount'  => $request->boolean('has_development_fee') ? ($request->development_fee_amount ?: null) : null,
            'has_security_fee'        => $request->boolean('has_security_fee'),
            'security_fee_amount'     => $request->boolean('has_security_fee')    ? ($request->security_fee_amount    ?: null) : null,

            // Extra
            'plot_image'             => $file_path,
            'property_features'      => $request->property_features,
            'description'            => $request->description,
        ]);

        return redirect()->route('booking.create', $plot->id);

    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
}



 public function plotShow($id)
{
    // 1. Decode Hashid
    $decoded = Hashids::decode($id);
    if (empty($decoded)) {
        abort(404, "Invalid Property Reference");
    }
    $plotId = $decoded[0];

    // 2. Fetch Plot Data
    $plot = Plot::with(['category', 'pricingPlan'])->findOrFail($plotId);

    // 3. Digital Verification Logic (Signed Route Check)
    if (request()->hasValidSignature()) {
        return view('plots_detail.digital_verify', compact('plot'));
    }

    // 4. Authentication Guard for PDF Generation
    if (!auth()->check()) {
        abort(403, "Access Denied. Please scan the QR code to verify.");
    }

    // 5. Generate Signed URL for QR Code Verification
   // Generate signed URL
$verificationUrl = URL::signedRoute('plots.show', ['id' => $id]);

// Generate QR
$renderer = new ImageRenderer(
    new RendererStyle(100),
    new SvgImageBackEnd()
);

$writer = new Writer($renderer);
$qrCode = base64_encode($writer->writeString($verificationUrl));

    // 7. Get Dynamic Society Config from your Trait
    $config = $this->societyConfig();

    // 8. Load View with both Plot Data, QR Code, and Trait Configuration
    $pdf = Pdf::loadView('plots_detail.show_plot', compact('plot', 'qrCode', 'config'))
              ->setPaper('a4', 'portrait')
              ->setOptions([
                  'isRemoteEnabled' => true,
                  'defaultFont' => 'sans-serif'
              ]);

    // 9. Stream PDF
    return $pdf->stream('Plot-'.$plot->plot_number.'-Specification.pdf');
}
    public function plotsEdit($id){

        $plot        = Plot::with(['pricingPlan'])->findOrFail($id);
        $categories   = PlotCategory::all();
        $pricingPlans = PlotPricingPlan::where('is_active', 1)->get();
        $cities       = City::all();
        $blocks       = Block::all();
        $societies    = Society::all();
        $sectors      = Sector::all();
        $features     = PropertyFeature::all();

        // Find the active booking for this plot (if any)
        $activeBooking = \App\Models\Booking::where('plot_id', $id)
            ->whereNotIn('status', ['cancelled', 'transferred', 'swapped', 'plot_relocated'])
            ->with('payments')
            ->latest()
            ->first();

        // Price fields are locked once any plot-payment has been recorded
        $hasPayments = $activeBooking
            && $activeBooking->payments->where('status', 'paid')->count() > 0;

        // Lock prices when plot is booked/sold AND payments exist
        $priceLocked = in_array($plot->status, ['booked', 'sold']) && $hasPayments;

        return view('plots_detail.edit_plot', compact(
            'plot', 'categories', 'pricingPlans', 'cities', 'blocks', 'societies', 'sectors', 'features',
            'activeBooking', 'hasPayments', 'priceLocked'
        ));
    }

   public function plotUpdate(Request $request, $id)
{
    $plot = Plot::findOrFail($id);

    $request->validate([
        'plot_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    ]);

    // Re-derive price lock (same logic as plotsEdit — cannot trust hidden inputs)
    $activeBooking = \App\Models\Booking::where('plot_id', $id)
        ->whereNotIn('status', ['cancelled', 'transferred', 'swapped', 'plot_relocated'])
        ->with('payments')
        ->latest()
        ->first();

    $hasPayments = $activeBooking
        && $activeBooking->payments->where('status', 'paid')->count() > 0;

    $priceLocked = in_array($plot->status, ['booked', 'sold']) && $hasPayments;

    try {
        // Duplicate check: same plot number + same block + same street (excluding self)
        if ($request->plot_number) {
            $exists = Plot::where('plot_number', $request->plot_number)
                ->where('id', '!=', $id)
                ->when($request->filled('block'),         fn($q) => $q->where('block',         $request->block))
                ->when($request->filled('street_number'), fn($q) => $q->where('street_number', $request->street_number))
                ->exists();
            if ($exists) {
                return back()->withInput()
                    ->withErrors(['plot_number' => 'Plot #'.$request->plot_number.' already exists in Block '.$request->block.' / Street '.$request->street_number.'.']);
            }
        }

        // Handle image upload
        $final_path = $plot->plot_image;
        if ($request->hasFile('plot_image')) {
            if ($plot->plot_image) {
                \Storage::disk('public')->delete(str_replace('storage/', '', $plot->plot_image));
            }
            $file     = $request->file('plot_image');
            $fileName = 'plot_'.($request->plot_number ?? $plot->plot_number).'_'.time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('plot_images', $fileName, 'public');
            $final_path = 'storage/plot_images/'.$fileName;
        }

        // Build update array — price fields are kept as-is when locked
        $data = [
            'plot_number'       => $request->plot_number ?: $plot->plot_number,
            'size'              => $request->filled('size') ? $request->size : null,
            'unit'              => $request->unit ?: $plot->unit,
            'status'            => $request->status ?? $plot->status,
            'property_features' => $request->property_features,
            'description'       => $request->description,
            'block'             => $request->block,
            'street_number'     => $request->street_number,
            'street_size'       => $request->filled('street_size') ? $request->street_size : null,
            'city'              => $request->city,
            'society'           => $request->society,
            'sector'            => $request->sector,
            'plot_image'        => $final_path,
        ];

        // Discount can always be updated — an offer can be applied/removed at any time
        $data['discount_amount'] = $request->filled('discount_amount') ? $request->discount_amount : null;
        $data['discount_reason'] = $request->filled('discount_reason') ? trim($request->discount_reason) : null;

        // Fee settings — always editable (flags + default amounts)
        $data['has_registry_fee']      = $request->boolean('has_registry_fee');
        $data['registry_fee_amount']   = $request->boolean('has_registry_fee') && $request->filled('registry_fee_amount')
                                            ? $request->registry_fee_amount : null;
        $data['has_development_fee']   = $request->boolean('has_development_fee');
        $data['development_fee_amount'] = $request->boolean('has_development_fee') && $request->filled('development_fee_amount')
                                            ? $request->development_fee_amount : null;
        $data['has_security_fee']      = $request->boolean('has_security_fee');
        $data['security_fee_amount']   = $request->boolean('has_security_fee') && $request->filled('security_fee_amount')
                                            ? $request->security_fee_amount : null;

        if (!$priceLocked) {
            // Only update price fields when not locked
            $data['plot_category_id']       = $request->plot_category_id ?: $plot->plot_category_id;
            $data['price_type']             = $request->price_type ?? $plot->price_type;
            $data['base_price']             = $request->filled('base_price') ? $request->base_price : null;
            $data['down_payment']           = $request->filled('down_payment') ? $request->down_payment : null;
            $data['quarterly_installments'] = $request->filled('quarterly_installments') ? $request->quarterly_installments : null;
            $data['quarterly_amount']       = $request->filled('quarterly_amount') ? $request->quarterly_amount : null;
            $data['total_installments']     = $request->filled('total_installments') ? $request->total_installments : null;
            $data['installment_amount']     = $request->filled('installment_amount') ? $request->installment_amount : null;
        }

        $plot->update($data);

        // ── Sync fee flags + BookingFee records to the active booking ─────────
        // This lets the user add/change fees on a plot even after a booking exists.
        if ($activeBooking) {
            $feeSync = [
                'registry'    => ['has_registry_fee',    'registry_fee_amount'],
                'development' => ['has_development_fee', 'development_fee_amount'],
                'security'    => ['has_security_fee',    'security_fee_amount'],
            ];

            $bookingFeeUpdate = [];
            foreach ($feeSync as $feeType => [$flagCol, $amtCol]) {
                $enabled = (bool) $data[$flagCol];
                $bookingFeeUpdate[$flagCol] = $enabled;

                // Find existing BookingFee for this type (if any)
                $existingFee = \App\Models\BookingFee::where('booking_id', $activeBooking->id)
                    ->where('fee_type', $feeType)
                    ->first();

                if ($enabled && isset($data[$amtCol]) && $data[$amtCol] > 0) {
                    if (!$existingFee) {
                        // Create a new BookingFee — fee was added after booking
                        \App\Models\BookingFee::create([
                            'booking_id'  => $activeBooking->id,
                            'fee_type'    => $feeType,
                            'amount'      => $data[$amtCol],
                            'paid_amount' => 0,
                            'status'      => 'pending',
                        ]);
                    } elseif ($existingFee->paid_amount == 0) {
                        // No payments yet — safe to update the amount
                        $existingFee->update(['amount' => $data[$amtCol]]);
                    }
                } elseif (!$enabled && $existingFee && $existingFee->paid_amount == 0) {
                    // Fee disabled and nothing paid yet — remove it
                    $existingFee->delete();
                }
            }

            $activeBooking->update($bookingFeeUpdate);
        }

        // Cascade price changes to the active booking (only when prices were not locked,
        // i.e. no payments exist yet — safe to update the booking's financial columns too)
        if (!$priceLocked && $activeBooking) {
            $bookingUpdate = [];

            // Only cascade if a price field was actually submitted
            if ($request->filled('base_price')) {
                $bookingUpdate['total_price'] = $request->base_price;
            }
            if ($request->filled('down_payment')) {
                $bookingUpdate['down_payment'] = $request->down_payment;
            }
            if ($request->filled('total_installments')) {
                $bookingUpdate['total_installments'] = $request->total_installments;
            }
            if ($request->filled('installment_amount')) {
                $bookingUpdate['monthly_installment'] = $request->installment_amount;
            }
            if ($request->filled('quarterly_installments')) {
                $bookingUpdate['quarterly_installments'] = $request->quarterly_installments;
            }
            if ($request->filled('quarterly_amount')) {
                $bookingUpdate['quarterly_amount'] = $request->quarterly_amount;
            }

            if (!empty($bookingUpdate)) {
                $activeBooking->update($bookingUpdate);
            }
        }

        return redirect()->route('index.plots')->with('success', 'Plot updated successfully!');

    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
}

    public function plotDestroy($id)
{
    $plot = Plot::findOrFail($id);

    // Block delete if any non-cancelled booking references this plot
    $hasActiveBooking = \App\Models\Booking::where('plot_id', $id)
        ->whereNotIn('status', ['cancelled'])
        ->exists();

    if ($hasActiveBooking) {
        return redirect()->route('index.plots')
            ->with('error', 'Plot #'.$plot->plot_number.' cannot be deleted — it has an active or completed booking. Cancel the booking first.');
    }

    try {
        if ($plot->plot_image) {
            $pathForDeletion = str_replace('storage/', '', $plot->plot_image);
            if (Storage::disk('public')->exists($pathForDeletion)) {
                Storage::disk('public')->delete($pathForDeletion);
            }
        }

        $plot->delete();

        return redirect()->route('index.plots')
            ->with('success', 'Plot #'.$plot->plot_number.' deleted successfully.');

    } catch (\Exception $e) {
        return redirect()->route('index.plots')
            ->with('error', 'Could not delete plot: '.$e->getMessage());
    }
}

public function blockView()
{

   $blocks = Block::all();
    return view('blocks.blockView' , compact('blocks'));
}
public function sectorView()
{

   $sectors = Sector::all();
    return view('sectors.sectorView' , compact('sectors'));
}

public function blockStore(Request $request)
{
    // 1. Validate the incoming request
    $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string|max:500' // Added description validation
    ]);

    // 2. Create the record in the database
    Block::create([
        'name'        => $request->name,
        'description' => $request->description, // Added description to the save logic
    ]);

    // 3. Return with success message
    return redirect()->back()->with('success', 'Block Added Successfully');
}
public function sectorStore(Request $request)
{
   $request->validate([
    'name' => 'required'
   ]);

   Sector::create([
       'name' => $request->name
   ]);

   return redirect()->back()->with('success','Sector Added Successfully');
}

public function societyView()
{

   $societies = Society::all();
    return view('society.societyView' , compact('societies'));
}
public function societyEditView($id){
    $societyEdit = Society::findOrFail($id);
    return view('society.societyEdit',compact('societyEdit'));
}

public function societyUpdate(Request $req , $id)
{
    $req->validate([
        'name' => 'required'
    ]);
    $societyUpdate = Society::findOrFail($id);

    $societyUpdate->update($req->all());
    return redirect()->back()->with('success','Sector Updated');
}

public function societyStore(Request $request)
{
   $request->validate([
    'name' => 'required'
   ]);

   Society::create([
       'name' => $request->name
   ]);

   return redirect()->back()->with('success','Society Added Successfully');
}

public function blockEditView($id){
    $blockEdit = Block::findOrFail($id);
    return view('blocks.blockEdit',compact('blockEdit'));
}
public function sectorEditView($id){
    $sectorEdit = Sector::findOrFail($id);
    return view('sectors.sectorEdit',compact('sectorEdit'));
}

public function societyDestroy($id){
    $societyDel = Society::findOrFail($id);
    $societyDel->delete();
    return redirect()->back()->with('success','Sector Deleted');
}
public function blockUpdate(Request $req, $id)
{
    // 1. Validate both fields
    $req->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string|max:500' // Ensure description is validated
    ]);

    // 2. Find the block or throw 404
    $blockUpdate = Block::findOrFail($id);

    // 3. Update using all validated data
    $blockUpdate->update([
        'name'        => $req->name,
        'description' => $req->description,
    ]);

    // 4. Redirect to the main view (usually better than back() for updates)
    return redirect()->route('blocks.index')->with('success', 'Block Updated Successfully');
}
public function sectorUpdate(Request $req , $id)
{
    $req->validate([
        'name' => 'required'
    ]);
    $sectorUpdate = Sector::findOrFail($id);

    $sectorUpdate->update($req->all());
    return redirect()->back()->with('success','Sector Updated');
}

public function blockDestroy($id){
    $blockDel = Block::findOrFail($id);
    $blockDel->delete();
    return redirect()->back()->with('success','Block Deleted');
}
public function sectorDestroy($id){
    $sectorDel = Sector::findOrFail($id);
    $sectorDel->delete();
    return redirect()->back()->with('success','Sector Deleted');
}

public function plotPricingView()
{
    $categories   = PlotCategory::all();
   $pricingPlans= PlotPricingPlan::with('category')->paginate(15);

return view('plots_detail.plotPricing',compact('categories','pricingPlans'));
}


public function storePricePlan(Request $req)
{
    $validate = $req->validate([
        'plot_category_id'   => 'required|exists:plot_categories,id',
        'size'               => 'required|numeric',
        'unit'               => 'required',
        'base_price'         => 'required|numeric',
        'down_payment'       => 'required|numeric',
        'processing_fee'     => 'required|numeric',
       'total_installments' => 'nullable|numeric',

        'effective_from'     => 'date',
    ]);


    $validate['is_active'] = true;
    $validate['effective_from'] = $validate['effective_from'] ?? now();

    PlotPricingPlan::create($validate);

    return redirect()->back()->with('success', 'Pricing plan added to category!');
}

public function PlotPricingDestroy($id){
    $plan = PlotPricingPlan::findOrFail($id);




    $plan->delete();

    return back()->with('success', 'Pricing plan deleted successfully!');
}

public function PlotCategoryDestroy($id)
{
    // Ensure the category exists
    $category = PlotCategory::findOrFail($id);

    // Check for Plots - If the relationship isn't set up, use DB directly
    $hasPlots = \DB::table('plots')->where('plot_category_id', $id)->exists();

    // Check for Pricing Plans
    $hasPricing = PlotPricingPlan::where('plot_category_id', $id)->exists();

    if ($hasPlots || $hasPricing) {
        return back()->with('error', 'Cannot delete category: It is currently linked to plots or pricing plans.');
    }

    // Perform the deletion
    $category->delete();

    return back()->with('success', 'Plot category deleted successfully.');
}

public function updatePricePlan(Request $request, $id)
{
    $validated = $request->validate([
        'plot_category_id'   => 'required|exists:plot_categories,id',
        'size'               => 'required|numeric',
        'unit'               => 'required',
        'base_price'         => 'nullable|numeric',
        'down_payment'       => 'nullable|numeric',
        'processing_fee'     => 'nullable|numeric',
        'total_installments' => 'nullable|numeric',
        'effective_from'     => 'nullable|date',
        'is_active'          => 'required|boolean',
    ]);

    PlotPricingPlan::findOrFail($id)->update($validated);

    return redirect()->back()->with('success', 'Pricing plan updated successfully!');
}
}
