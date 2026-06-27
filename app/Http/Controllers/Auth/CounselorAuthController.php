<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;
class CounselorAuthController extends Controller
{
    public function login()
    {
        return view('counselor.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Add status check to credentials
        $credentials['status'] = 1;

        if (Auth::guard('counselor')->attempt($credentials)) {
            $request->session()->regenerate();
            \Log::info('Counselor logged in', ['user' => Auth::guard('counselor')->user()]);
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            if ($activeYear) {
                session([
                    'academic_year_id' => $activeYear->id,
                    'academic_year_name' => $activeYear->name
                ]);
            } else {
                // If no active year, set session to null and show toast
                session([
                    'academic_year_id' => null,
                    'academic_year_name' => null,
                ]);
            }
            $counselor = Auth::guard('counselor')->user();

            // Log login activity
            ActivityLogger::log(
                "Admin logged in: {$counselor->name}",
                'Login',
                $counselor,
                ['ip' => $request->ip()]
            );

            return redirect()->intended(route('counselor.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or your account is inactive.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Log the logout activity
        if (Auth::guard('counselor')->check()) {
            ActivityLogger::log(
                "Counselor logged out: " . Auth::guard('counselor')->user()->name,
                'Logout',
                Auth::guard('counselor')->user(),
                []
            );
        }

        Auth::guard('counselor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('counselor.login');
    }
}