<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\Concerns\LogsOutGuard;

class AccountAuthController extends Controller
{
    use LogsOutGuard;
    public function login()
    {
        return view('account.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials['status'] = 1;

        if (Auth::guard('account')->attempt($credentials)) {
            $request->session()->regenerate();

            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            if ($activeYear) {
                session([
                    'academic_year_id' => $activeYear->id,
                    'academic_year_name' => $activeYear->name,
                ]);
            } else {
                session([
                    'academic_year_id' => null,
                    'academic_year_name' => null,
                ]);
            }

            $account = Auth::guard('account')->user();

            ActivityLogger::log(
                "Account user logged in: {$account->name}",
                'Login',
                $account,
                ['ip' => $request->ip()]
            );

            return redirect()->intended(route('account.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or your account is inactive.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        return $this->performGuardLogout($request, 'account', 'account.login', function ($account) {
            ActivityLogger::log(
                'Account user logged out: ' . $account->name,
                'Logout',
                $account,
                []
            );
        });
    }
}
