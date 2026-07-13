<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;
use App\Http\Controllers\Auth\Concerns\LogsOutGuard;

class CounselorAuthController extends Controller
{
    use LogsOutGuard;
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
            $counselor = Auth::guard('counselor')->user();

            if ($counselor->isBreakLoginLocked()) {
                Auth::guard('counselor')->logout();

                return back()
                    ->with('break_login_locked', true)
                    ->with(
                        'break_login_lock_message',
                        $counselor->break_login_lock_reason
                            ?: 'Your break time has exceeded the allowed limit. Admin permission is required to login again.'
                    )
                    ->onlyInput('email');
            }

            $request->session()->regenerate();
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
                "Counselor logged in: {$counselor->name}",
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
        return $this->performGuardLogout($request, 'counselor', 'counselor.login', function ($counselor) {
            ActivityLogger::log(
                'Counselor logged out: ' . $counselor->name,
                'Logout',
                $counselor,
                []
            );
        });
    }
}