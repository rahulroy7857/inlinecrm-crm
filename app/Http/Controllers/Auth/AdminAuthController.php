<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Services\ActivityLogger;
class AdminAuthController extends Controller
{
    public function create(): View
    {
        return view('auth.admin.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Admin::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($admin));

        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.dashboard');
    }

    public function login(): View
    {
        return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            \Log::info('Admin logged in', ['user' => Auth::guard('admin')->user()]);
            // Get active academic year
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
            $admin = Auth::guard('admin')->user();

            // Log login activity
            ActivityLogger::log(
                "Admin logged in: {$admin->name}",
                'Login',
                $admin,
                ['ip' => $request->ip()]
            );
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // Log logout activity
        if ($admin) {
            ActivityLogger::log(
                "Admin logged out: {$admin->name}",
                'Logout',
                $admin,
                ['ip' => $request->ip()]
            );
        }
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}