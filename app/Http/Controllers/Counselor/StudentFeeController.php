<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Student;
use App\Services\StudentFeeService;
use Illuminate\Http\Request;

class StudentFeeController extends Controller
{
    public function __construct(
        private StudentFeeService $feeService
    ) {}

    public function sendDueReminder(Request $request, int $leadId)
    {
        $counselorId = auth()->guard('counselor')->id();
        $lead = Lead::where('id', $leadId)
            ->where('counselor_id', $counselorId)
            ->firstOrFail();

        $student = Student::where('lead_id', $lead->id)->firstOrFail();

        $validated = $request->validate([
            'purpose' => ['required', 'in:registration_fee,counselor_fee,college_fee'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->feeService->sendDueReminder(
                $student->load('counselor'),
                $validated['purpose'],
                $validated['message'] ?? null
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Unable to send due reminder email.');
        }

        return back()->with('success', 'Due amount notification emailed to the student.');
    }
}
