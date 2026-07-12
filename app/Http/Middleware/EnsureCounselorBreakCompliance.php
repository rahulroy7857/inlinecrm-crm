<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use App\Services\CounselorWorkingHoursService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCounselorBreakCompliance
{
    public function __construct(
        private CounselorWorkingHoursService $workingHoursService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $counselor = Auth::guard('counselor')->user();

        if (!$counselor) {
            return $next($request);
        }

        $logoutRequired = $this->workingHoursService->processBreakOvertime($counselor);
        $counselor->refresh();

        if (!$logoutRequired && !$counselor->break_login_locked) {
            return $next($request);
        }

        ActivityLogger::log(
            'Counselor auto-logged out: break time exceeded',
            'Logout',
            $counselor,
            [
                'reason' => $counselor->break_login_lock_reason,
                'break_login_locked' => true,
            ]
        );

        Auth::guard('counselor')->logout();

        $message = $counselor->break_login_lock_reason
            ?: 'Your break time has exceeded the allowed limit. Admin permission is required to login again.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'break_login_locked' => true,
                'message' => $message,
                'redirect' => route('counselor.login'),
            ], 403);
        }

        return redirect()
            ->route('counselor.login')
            ->with('break_login_locked', true)
            ->with('break_login_lock_message', $message);
    }
}
