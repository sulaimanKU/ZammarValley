<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

public function index() {
    $roles = Role::all();
    $users = User::with('roles')->paginate(10);

    // "Online" = user has an active session with last_activity within the past 5 minutes
    $onlineThreshold = now()->subMinutes(5)->timestamp;

    $onlineUserIds = DB::table('sessions')
        ->whereNotNull('user_id')
        ->where('last_activity', '>=', $onlineThreshold)
        ->pluck('user_id')
        ->unique()
        ->toArray();

    $totalUsers   = User::count();
    $onlineCount  = count($onlineUserIds);
    $offlineCount = $totalUsers - $onlineCount;
    $activeCount  = $onlineCount;

    return view('userManagment.userIndex', compact(
        'users', 'roles', 'activeCount', 'onlineCount', 'offlineCount', 'onlineUserIds'
    ));
}
public function searchUser(Request $request)
{
    $request->validate([
   'search' => 'required'
    ]);
    $user = User::where('email',$request->search)->first();
    $roles = Role::all();

    return view('userManagment.searchUser',compact('user','roles'));

}

public function addUser(){
      $roles = Role::all();
    return view('userManagment.addUser',compact('roles'));
}
    public function storeUser(Request $request){
        // dd($request->all());
        // die();


        $request->validate([
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email',
        'role'          => 'required|exists:roles,name', // Validate that the role exists in Spatie's table
        'password'      => 'required|min:8',
        'phone_number' => 'required',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg',
        'cnic_no' => 'required',
        'address' => 'required',

    ]);
DB::beginTransaction();
$fileName = null;
try{


    if($request->hasFile('profile_image')){
         $file = $request->file('profile_image');
         $fileName = 'profile_'.time().'.'.$file->getClientOriginalExtension();
         $file->storeAs('user_image/',$fileName,'public');
    }

    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'phone_number' => $request->phone_number,
        'cnic_no' => $request->cnic_no,
        'address' => $request->address,
        'profile_image'    =>  $fileName,
    ]);
    $user->assignRole($request->role);
    DB::commit();
    return redirect()->back()->with('success','User'.$request->name.'created Successfully');
}catch(\Exception $exe){
    DB::rollBack();
    if($fileName && Storage::disk('public')->exists('user_image/'.$fileName)){
        Storage::disk('public')->delete('user_image/'.$fileName);
    }
    return redirect()->back()->with('error',$exe->getMessage());

}
    }

    public function roleDestroy($id){
     DB::beginTransaction();
     try{
        $role = Role::findOrFail($id);

        if($role->name == 'admin'){
            return redirect()->back()->with('error','The Admin role cannot be deleted.');
        }
        $role->delete();DB::commit();
        return redirect()->back()->with('success','Role Deleted');
     }catch(\Exception $exe){
        return redirect()->back()->with('error',$exe->getMessage());
     }
}


public function userEdit($id){
$user = User::findOrFail($id);
$roles = Role::all();
return  view('userManagment.userEdit',compact('user','roles'));
}
public function userUpdate(Request $request, $id)
{
    $user = User::findOrFail($id);
    $currentUser = auth()->user(); // Get the person currently logged in

    // 1. SECURITY CHECK: Prevent self-role-changing
    // If the person being edited IS the person logged in
    if ($currentUser->id == $user->id) {
        if ($request->has('role') && $request->role !== $user->getRoleNames()->first()) {
            return redirect()->back()->with('error', 'Security Alert: You cannot change your own role.');
        }

        if ($request->has('is_active') && $request->is_active == 0) {
            return redirect()->back()->with('error', 'Security Alert: You cannot deactivate your own account.');
        }
    }

    $password = $user->password;
    $fileName = $user->profile_image;

    DB::beginTransaction();
    try {
        // Handle Password
        if ($request->filled('password')) {
            $password = Hash::make($request->password);
        }

        // Handle Image Upload
        if ($request->hasFile('profile_image')) {
            // Added a missing slash / in the delete path
            if ($user->profile_image && Storage::disk('public')->exists('user_image/' . $user->profile_image)) {
                Storage::disk('public')->delete('user_image/' . $user->profile_image);
            }
            $file = $request->file('profile_image');
            $fileName = 'profile_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('user_image', $fileName, 'public');
        }

        // Update User
      $user->update([
    'name'          => $request->name,
    'email'         => $request->email,
    'is_active'     => $request->is_active,
    'profile_image' => $fileName,
    'password'      => $password,
    'phone_number'  => $request->phone_number,
    'cnic_no'       => $request->cnic_no,
    'address'       => $request->address,

    'status'        => ($request->is_active == 1) ? 1 : 0
]);

        // 2. ONLY sync role if the user is NOT editing themselves
        if ($currentUser->id != $user->id) {
            $user->syncRoles($request->role);
        }

        DB::commit();
        return redirect()->route('index.user')->with('success', 'User updated successfully!');

    } catch (\Exception $exe) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: ' . $exe->getMessage());
    }
}

public function userDestroy($id){
    $user = User::findOrFail($id);
       $path = null;
       DB::beginTransaction();
       try{


    if($user->profile_image){
        $path = 'user_image/' .$user->profile_image;
        if(Storage::disk('public')->exists($path)){
             Storage::disk('public')->delete($path);
        }
    }
    $user->roles()->detach();
    $user->delete();
    DB::commit();
    return redirect()->back()->with('success','User ' . $user->name . 'Deleted Successfully');
      }catch (\Exception $e) {
        DB::rollBack(); // Failure! Put the image back (conceptually) and don't delete user.
        return redirect()->back()->with('error', 'Could not delete user: ' . $e->getMessage());
    }
}
public function quickRegister(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'    => 'required|string|max:255',
        'cnic'    => 'required|string|unique:customers,cnic|min:13|max:15',
        'phone'   => 'required|string|max:20',
        'email'   => 'nullable|email|max:255',
        'address' => 'nullable|string|max:500',
    ], [
        'cnic.unique' => 'A customer with this CNIC already exists.',
        'cnic.min'    => 'CNIC must be 13 digits.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Please fix the errors below.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $customer = User::create([
        'name'    => $request->name,
        'cnic'    => $request->cnic,
        'phone'   => $request->phone,
        'email'   => $request->email,
        'address' => $request->address,
    ]);

    return response()->json([
        'success'  => true,
        'message'  => 'Customer registered successfully.',
        'customer' => [
            'id'   => $customer->id,
            'name' => $customer->name,
            'cnic' => $customer->cnic,
        ],
    ]);
}



}
