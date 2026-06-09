<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
  public function store(LoginRequest $request): RedirectResponse
{
  $user = User::where('email', $request->email)->first();

    // Block inactive users before they even login
    if (!$user || $user->is_active == 0) {
        return redirect()->back()->with('error', 'Your account is not active. Please contact admin.');
    }

    $request->authenticate();
    $request->session()->regenerate();


    // Set user status to online

   $user->update(['status' => 1]);


    // Redirect based on role
    return redirect()->route('index.dashboard');



}

    /**
     * Destroy an authenticated session.
     */
   public function destroy(Request $request): RedirectResponse
{
    // Set user status to offline
    $user = auth()->user();
    if ($user) {
        $user->status = 0;
        $user->save();
    }

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}
}
