<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AccountBreak;
use App\Services\AccountWorkingHoursService;
use Illuminate\Http\Request;

class WorkingHoursController extends Controller
{
    public function __construct(
        private AccountWorkingHoursService $workingHoursService
    ) {}

    public function status()
    {
        $account = auth()->guard('account')->user();

        return response()->json([
            'success' => true,
            'data' => $this->workingHoursService->getTodaySummary($account),
        ]);
    }

    public function startBreak(Request $request)
    {
        $request->validate([
            'type' => 'required|in:' . implode(',', AccountBreak::validTypes()),
        ]);

        $account = auth()->guard('account')->user();

        try {
            $break = $this->workingHoursService->startBreak($account, $request->type);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $summary = $this->workingHoursService->getTodaySummary($account);
        $message = $break->isPendingApproval()
            ? 'Break request sent to admin for approval.'
            : 'Break started.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $summary,
        ]);
    }

    public function endBreak()
    {
        $account = auth()->guard('account')->user();
        $break = $this->workingHoursService->endActiveBreak($account);

        if (!$break) {
            return response()->json([
                'success' => false,
                'message' => 'No active break found.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Break ended.',
            'data' => $this->workingHoursService->getTodaySummary($account),
        ]);
    }
}
