<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentPayment;
use App\Services\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function __construct(
        private StudentFeeService $feeService
    ) {}

    public function index()
    {
        $student = Auth::guard('student')->user();
        $student->load(['payments' => fn ($q) => $q->latest(), 'counselor']);
        $summary = $this->feeService->feeSummary($student);
        $gateway = config('student.payment.gateway');
        $testMode = config('student.payment.test_mode');
        $purposeLabels = StudentFeeService::purposeLabels();
        $registrationPlans = StudentFeeService::registrationPlans();

        return view('student.payment.index', compact(
            'student',
            'summary',
            'gateway',
            'testMode',
            'purposeLabels',
            'registrationPlans'
        ));
    }

    public function initiate(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'purpose' => ['required', Rule::in([
                StudentFeeService::PURPOSE_REGISTRATION,
                StudentFeeService::PURPOSE_COUNSELOR,
                StudentFeeService::PURPOSE_COLLEGE,
            ])],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $purpose = $validated['purpose'];
        $amount = round((float) $validated['amount'], 2);
        $summary = $this->feeService->feeSummary($student);

        if (!$summary['fees_set']) {
            return back()->with('error', 'Fees have not been set by the accounts team yet.');
        }

        if ($summary['registration_required_first'] && $purpose !== StudentFeeService::PURPOSE_REGISTRATION) {
            return back()->with('error', 'Please pay the Registration Fee first.');
        }

        try {
            $payment = $this->feeService->recordInstallment($student, $purpose, $amount);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        if (config('student.payment.test_mode')) {
            return redirect()->route('student.payment.callback', [
                'payment' => $payment->id,
                'status' => 'success',
            ]);
        }

        return redirect()->route('student.payment.index')
            ->with('error', 'Live payment gateway is not configured. Enable test mode or configure Razorpay credentials.');
    }

    public function callback(Request $request, int $payment)
    {
        $student = Auth::guard('student')->user();
        $record = StudentPayment::where('id', $payment)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($request->query('status') === 'success') {
            $this->feeService->markPaid($record, [
                'mode' => config('student.payment.test_mode') ? 'test' : 'live',
            ]);
            $this->feeService->sendReceipt($record->fresh(['student', 'counselor']));

            return redirect()->route('student.payment.index')
                ->with('success', 'Payment successful! A receipt has been emailed to you.');
        }

        $record->update(['status' => 'failed']);

        return redirect()->route('student.payment.index')
            ->with('error', 'Payment failed. Please try again.');
    }
}
