<?php

namespace App\Http\Middleware;

use App\Services\AccountWorkingHoursService;
use App\Services\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountBreakCompliance
{
    public function __construct(
        private AccountWorkingHoursService $workingHoursService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $account = Auth::guard('account')->user();

        if (!$account) {
            return $next($request);
        }

        $logoutRequired = $this->workingHoursService->processBreakOvertime($account);
        $account->refresh();

        if (!$logoutRequired && !$account->break_login_locked) {
            return $next($request);
        }

        ActivityLogger::log(
            'Account user auto-logged out: break time exceeded',
            'Logout',
            $account,
            [
                'reason' => $account->break_login_lock_reason,
                'break_login_locked' => true,
            ]
        );

        Auth::guard('account')->logout();

        $message = $account->break_login_lock_reason
            ?: 'Your break time has exceeded the allowed limit. Admin permission is required to login again.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'break_login_locked' => true,
                'message' => $message,
                'redirect' => route('account.login'),
            ], 403);
        }

        return redirect()
            ->route('account.login')
            ->with('break_login_locked', true)
            ->with('break_login_lock_message', $message);
    }
}
