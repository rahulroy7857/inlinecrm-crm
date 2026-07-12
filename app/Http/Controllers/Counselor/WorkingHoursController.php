<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Services\CounselorWorkingHoursService;
use App\Models\CounselorBreak;
use Illuminate\Http\Request;

class WorkingHoursController extends Controller
{
    public function __construct(
        private CounselorWorkingHoursService $workingHoursService
    ) {}

    public function status()
    {
        $counselor = auth()->guard('counselor')->user();

        return response()->json([
            'success' => true,
            'data' => $this->workingHoursService->getTodaySummary($counselor),
        ]);
    }

    public function startBreak(Request $request)
    {
        $request->validate([
            'type' => 'required|in:' . implode(',', CounselorBreak::validTypes()),
        ]);

        $counselor = auth()->guard('counselor')->user();

        try {
            $break = $this->workingHoursService->startBreak($counselor, $request->type);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $summary = $this->workingHoursService->getTodaySummary($counselor);
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
        $counselor = auth()->guard('counselor')->user();
        $break = $this->workingHoursService->endActiveBreak($counselor);

        if (!$break) {
            return response()->json([
                'success' => false,
                'message' => 'No active break found.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Break ended.',
            'data' => $this->workingHoursService->getTodaySummary($counselor),
        ]);
    }
}
