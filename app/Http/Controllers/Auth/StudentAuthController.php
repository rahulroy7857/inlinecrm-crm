<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\Concerns\LogsOutGuard;

class StudentAuthController extends Controller
{
    use LogsOutGuard;
    public function login()
    {
        return view('student.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials['status'] = 1;

        if (Auth::guard('student')->attempt($credentials)) {
            $request->session()->regenerate();

            $student = Auth::guard('student')->user();

            ActivityLogger::log(
                "Student logged in: {$student->name}",
                'Login',
                $student,
                ['ip' => $request->ip()]
            );

            if (!$student->isProfileComplete()) {
                return redirect()->route('student.dashboard');
            }

            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or your account is inactive.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        return $this->performGuardLogout($request, 'student', 'student.login', function ($student) {
            ActivityLogger::log(
                'Student logged out: ' . $student->name,
                'Logout',
                $student,
                []
            );
        });
    }
}
