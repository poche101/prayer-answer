<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the authentication attempt.
     */
    public function login(Request $request)
    {
        // 1. Validate the input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Attempt to log the user in
        if (Auth::attempt($credentials)) {
            // This is where it crashes if you are on an 'api' route.
            // Ensure this request comes from routes/web.php
            $request->session()->regenerate();

            // intended(route('dashboard')) is safer than just 'dashboard'
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back to the Admin Portal!');
        }


        throw ValidationException::withMessages([
            'email' => [('The provided credentials do not match our records.')],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Ensure session is cleared safely
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')
            ->with('success', 'You have been logged out.');
    }
}
