<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Student;
use App\Services\ActivityLogger;
use App\Services\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentFeeController extends Controller
{
    public function __construct(
        private StudentFeeService $feeService
    ) {}

    public function index(Request $request)
    {
        $counselorId = auth()->guard('counselor')->id();
        $query = Student::with(['course', 'feeLedgerAccount'])
            ->where('counselor_id', $counselorId)
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $search = $request->string('q')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('lead_ref', 'like', "%{$search}%");
            });
        }

        return view('counselor.student-fees.index', [
            'students' => $query->paginate(20)->withQueryString(),
            'plans' => StudentFeeService::registrationPlans(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $counselor = auth()->guard('counselor')->user();
        $student = Student::where('counselor_id', $counselor->id)->findOrFail($id);
        $planKeys = array_keys(StudentFeeService::registrationPlans());

        $validated = $request->validate([
            'registration_fee_plan' => ['required', Rule::in($planKeys)],
            'registration_fee_due_date' => ['required', 'date'],
        ]);

        $this->feeService->setFees($student, $validated, null, $counselor->id);
        Lead::where('id', $student->lead_id)
            ->where('counselor_id', $counselor->id)
            ->update(['registration_fee_plan' => $validated['registration_fee_plan']]);

        ActivityLogger::log(
            "Updated student fees for {$student->name}",
            'Update',
            $counselor,
            array_merge($validated, ['student_id' => $student->id])
        );

        return back()->with('success', 'Registration details updated successfully.');
    }

    public function updateRegistrationPlan(Request $request, int $leadId)
    {
        $counselorId = auth()->guard('counselor')->id();
        $lead = Lead::where('id', $leadId)
            ->where('counselor_id', $counselorId)
            ->firstOrFail();

        $planKeys = array_keys(StudentFeeService::registrationPlans());

        $validated = $request->validate([
            'registration_fee_plan' => ['nullable', Rule::in($planKeys)],
        ]);

        $planKey = $validated['registration_fee_plan'] ?? null;

        $lead->update(['registration_fee_plan' => $planKey]);

        // If the student has already registered, apply the plan to them now.
        $student = Student::where('lead_id', $lead->id)->first();
        if ($student) {
            $this->feeService->applyRegistrationPlan($student, $planKey);
        }

        $label = $planKey
            ? (StudentFeeService::registrationPlans()[$planKey]['label'] ?? $planKey)
            : null;

        return back()->with(
            'success',
            $label
                ? "Registration plan set to {$label}."
                : 'Registration plan cleared.'
        );
    }

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
