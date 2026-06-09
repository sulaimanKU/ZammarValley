<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\City;
use App\Models\PropertyFeature;
use App\Models\Sector;
use App\Models\Society;
use App\Models\SystemConfig;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettingController extends Controller
{

    public function index(){
       $Society            = Society::count();
    $sectors            = Sector::count();
    $roles              = Role::all();
    $permissions        = Permission::get();
    $roleAndPermissions = Role::with('permissions')->get();
    $propertyFeatures   = PropertyFeature::count();
    $citiesCount        = City::count();
    $blocks             = Block::count();

    $settings = SystemConfig::allAsArray();


    return view('layouts.settings.setting', compact(
        'permissions', 'roleAndPermissions', 'roles',
        'propertyFeatures', 'citiesCount', 'blocks',
        'sectors', 'Society', 'settings'
    ));


    }


    public function updateProfile(Request $request)
{
    $user = auth()->user();

    // 1. Validation
    $request->validate([
        'name'           => 'required|string|max:255',
        'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
        'phone_number'   => 'nullable|string|max:20',
        'address'        => 'nullable|string|max:500',
        'profile_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'password'       => 'nullable|min:8',
        'confirm_password' => 'same:password',
    ]);

    // 2. Handle Profile Image (Using your specific logic)
    if ($request->hasFile('profile_image')) {

        // Delete old image from storage/app/public/user_image/ if it exists
        if ($user->profile_image && \Storage::disk('public')->exists('user_image/' . $user->profile_image)) {
            \Storage::disk('public')->delete('user_image/' . $user->profile_image);
        }

        $file = $request->file('profile_image');
        $fileName = 'profile_'.time().'.'.$file->getClientOriginalExtension();

        // Stores in: storage/app/public/user_image/
        $file->storeAs('user_image/', $fileName, 'public');

        $user->profile_image = $fileName;
    }

    // 3. Update Other Fields
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone_number = $request->phone_number;
    $user->address = $request->address;

    // 4. Password Update (Only if provided)
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'Profile updated successfully!');
}

    public function storePermission(Request $request){
        $request->validate([

        ]);
        // dd($request->all());

        Permission::create([
          'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Permission created successfully!');
    }
 public function roleCreate(){
    $groupedPermissions = Permission::all()->groupBy('module');
    return view('layouts.settings.roleCreate',compact('groupedPermissions'));
 }

   public function storeRole(Request $request)
{
    $request->validate([
        'role_name'   => 'required|unique:roles,name|max:255',
        'permissions' => 'nullable|array',
        'description' => 'nullable|string|max:500', // Matches the new textarea in your Blade
    ]);

    DB::beginTransaction();

    try {
        // 1. Create the Role
        $role = Role::create([
            'name'        => $request->role_name,
            'description' => $request->description, // Added this if you have the column
            'guard_name'  => 'web'
        ]);


        if ($request->has('permissions') && !empty($request->permissions)) {
            $permissionIds = collect($request->permissions)->map(fn($id) => (int)$id)->toArray();
            $role->syncPermissions($permissionIds);
        }

        DB::commit();


        return redirect()->back()
            ->with('success', "Role '{$request->role_name}' created successfully with " . count($request->permissions ?? []) . " permissions.");

    } catch (\Exception $exe) {
        DB::rollBack();

        // Return back to the form with the user's typed input and the error
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error creating role: ' . $exe->getMessage());
    }
}

public function RolePermissionEdit($id){
    $role = Role::findOrFail($id);


    $groupedPermissions = Permission::all()->groupBy('module');


    $rolePermissions = $role->permissions->pluck('id')->toArray();

    return view('layouts.settings.rolesEdit', compact('role', 'groupedPermissions', 'rolePermissions'));
}
    public function RolePermissionUpdate(Request $request ,$id){
       $request->validate([
        'role_name'    => 'required|string|max:255',
        'permissions'  => 'nullable|array',
        'permissions.*'=> 'exists:permissions,id',
    ]);

    $role = Role::findOrFail($id);
    $role->update([
        'name' => $request->role_name,
    ]);

    $role->permissions()->sync($request->permissions ?? []);
    return redirect()->back()->with('success', 'Role and Permissions updated successfully!');

    }


    public function cityView ()
    {
        $cities = City::all();
        return view('manage_cities.citiesView',compact('cities'));
    }

    public function cityStore(Request $request){
        $request->validate([
            'name' => 'required'
        ]);
        City::create([
            'name' => $request->name
        ]);
         return redirect()->back()->with('success','City Added');
    }

    public function cityEditView($id){
        $city = City::findOrFail($id);
        return view('manage_cities.cityEdit',compact('city'));
    }

    public function cityUpdate(Request $request,$id){
        $request->validate([
             'name' => 'required'
        ]);
        $cityUpd = City::findOrFail($id);
        $cityUpd->update($request->all());

        return redirect()->back()->with('success','City Updated');

    }
    public function cityDestroy($id){
        $delCity = City::findOrFail($id);
        $delCity->delete();
         return redirect()->back()->with('success','City Deleted');
    }
    public function logoView(){
        return view('config_society.logo');
    }

    public function saveLogo(Request $request)
    {
        $request->validate([
            'society_logo' => 'required|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $oldLogo = SystemConfig::get('society_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }


        $path = $request->file('society_logo')->store('society', 'public');
        SystemConfig::set('society_logo', $path, 'society');

        return redirect()->back()->with('success', 'Logo uploaded successfully.');
    }

     public function showIdentity()
    {
        $settings = SystemConfig::allAsArray();
        return view('config_society.identity', compact('settings'));
    }

 public function saveIdentity(Request $request)
    {
        $request->validate([
            'society_name'       => 'required|string|max:100',
            'society_tagline'    => 'nullable|string|max:200',
            'society_phone'      => 'nullable|string|max:30',
            'society_phone2'     => 'nullable|string|max:30',
            'society_phone3'     => 'nullable|string|max:30',
            'society_email'      => 'nullable|email|max:100',
            'society_address'    => 'nullable|string|max:500',
            'default_plot_sizes' => 'nullable|string|max:100',
        ]);

        SystemConfig::set('society_name',       $request->society_name,                   'society');
        SystemConfig::set('society_tagline',    $request->society_tagline    ?? '',        'society');
        SystemConfig::set('society_phone',      $request->society_phone      ?? '',        'society');
        SystemConfig::set('society_phone2',     $request->society_phone2     ?? '',        'society');
        SystemConfig::set('society_phone3',     $request->society_phone3     ?? '',        'society');
        SystemConfig::set('society_email',      $request->society_email      ?? '',        'society');
        SystemConfig::set('society_address',    $request->society_address    ?? '',        'society');
        SystemConfig::set('default_plot_unit',  $request->default_plot_unit  ?? 'Marla',  'society');
        SystemConfig::set('default_plot_sizes', $request->default_plot_sizes ?? '3,5,7,10,20', 'society');

        return redirect()->back()->with('success', 'Society identity saved successfully.');
    }

     public function showFinance()
    {
        $settings = SystemConfig::allAsArray();
        return view('config_society.finance', compact('settings'));
    }
public function saveFinance(Request $request)
    {
        $request->validate([
            'currency_symbol'        => 'required|string|max:10',
            'default_transfer_fee'   => 'required|numeric|min:0',
            'late_fine_percent'      => 'required|numeric|min:0|max:100',
            'installment_grace_days' => 'required|integer|min:0|max:60',
        ]);

        SystemConfig::set('currency_symbol',        $request->currency_symbol,        'finance');
        SystemConfig::set('default_transfer_fee',   $request->default_transfer_fee,   'finance');
        SystemConfig::set('late_fine_percent',       $request->late_fine_percent,       'finance');
        SystemConfig::set('installment_grace_days',  $request->installment_grace_days,  'finance');

        return redirect()->back()->with('success', 'Finance settings saved successfully.');
    }

    public function showDocs()
    {
        $settings = SystemConfig::allAsArray();
        return view('config_society.docs', compact('settings'));
    }


     public function saveDocs(Request $request)
    {
        $request->validate([
            'receipt_prefix'      => 'nullable|string|max:10',
            'booking_id_prefix'   => 'nullable|string|max:10',
            'deed_prefix'         => 'nullable|string|max:10',
            'doc_watermark_text'  => 'nullable|string|max:100',
            'receipt_footer_note' => 'nullable|string|max:500',
        ]);

        SystemConfig::set('receipt_prefix',       $request->receipt_prefix      ?? 'REC',  'documents');
        SystemConfig::set('booking_id_prefix',    $request->booking_id_prefix   ?? 'ZV',   'documents');
        SystemConfig::set('deed_prefix',          $request->deed_prefix         ?? 'DEED', 'documents');
        SystemConfig::set('doc_watermark_text',   $request->doc_watermark_text  ?? '',      'documents');
        SystemConfig::set('receipt_footer_note',  $request->receipt_footer_note ?? '',      'documents');


        SystemConfig::set('show_logo_on_receipt', $request->has('show_logo_on_receipt') ? '1' : '0', 'documents');

        return redirect()->back()->with('success', 'Document settings saved successfully.');
    }


    public function showEmail()
{
    return view('config_society.email', [
        'mail_host'       => env('MAIL_HOST', 'smtp.gmail.com'),
        'mail_port'       => env('MAIL_PORT', '587'),
        'mail_username'   => env('MAIL_USERNAME', ''),
        'mail_password'   => env('MAIL_PASSWORD', ''),
        'mail_encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'mail_from_name'  => env('MAIL_FROM_NAME', ''),
    ]);
}

public function saveEmail(Request $request)
{
    $request->validate([
        'mail_host'       => 'required|string',
        'mail_port'       => 'required|numeric',
        'mail_username'   => 'required|email',
        'mail_password'   => 'required|string',
        'mail_encryption' => 'required|in:tls,ssl,none',
        'mail_from_name'  => 'required|string',
    ]);

    // Update .env file
    $this->updateEnv([
        'MAIL_MAILER'       => 'smtp',
        'MAIL_HOST'         => $request->mail_host,
        'MAIL_PORT'         => $request->mail_port,
        'MAIL_USERNAME'     => $request->mail_username,
        'MAIL_PASSWORD'     => $request->mail_password,
        'MAIL_ENCRYPTION'   => $request->mail_encryption,
        'MAIL_FROM_ADDRESS' => $request->mail_username,
        'MAIL_FROM_NAME'    => '"'.$request->mail_from_name.'"',
    ]);

    // Clear config cache
    Artisan::call('config:clear');

    return back()->with('success', 'Email configuration saved successfully.');
}


private function updateEnv(array $data)
{
    $envPath = base_path('.env');
    $envContent = file_get_contents($envPath);

    foreach ($data as $key => $value) {
        // If key exists — replace it
        if (preg_match("/^{$key}=/m", $envContent)) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            // If key doesn't exist — append it
            $envContent .= "\n{$key}={$value}";
        }
    }

    file_put_contents($envPath, $envContent);
}


    public function testEmail()
{
    try {
        Mail::raw('This is a test email from Zamar Valley ERP system.', function($msg) {
            $msg->to(env('MAIL_USERNAME'))
                ->subject('Test Email — Zamar Valley');
        });
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
}
