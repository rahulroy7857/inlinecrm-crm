<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Concerns\ManagesAccountPortal;
use App\Models\LedgerAccount;
use App\Models\Student;
use App\Services\ActivityLogger;
use App\Services\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentFeeManageController extends Controller
{
    use ManagesAccountPortal;

    public function __construct(
        private StudentFeeService $feeService
    ) {}

    public function index(Request $request)
    {
        $query = Student::with(['counselor', 'course', 'feeLedgerAccount'])
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('lead_ref', 'like', "%{$q}%");
            });
        }

        $students = $query->paginate(20)->withQueryString();
        $plans = StudentFeeService::registrationPlans();
        $ledgerAccounts = LedgerAccount::where('status', 'Active')->orderBy('name')->get();
        $feeService = $this->feeService;

        return view('account.student-fees.index', compact(
            'students',
            'plans',
            'ledgerAccounts',
            'feeService'
        ));
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeAccountManage();

        if (! is_admin_account_portal()) {
            abort(403, 'Fee details can only be edited from the Admin panel.');
        }

        $student = Student::findOrFail($id);
        $planKeys = array_keys(StudentFeeService::registrationPlans());

        $validated = $request->validate([
            'registration_fee_plan' => ['required', Rule::in($planKeys)],
            'registration_fee_due_date' => ['required', 'date'],
            'counselor_fee' => ['required', 'numeric', 'min:0'],
            'college_fee' => ['required', 'numeric', 'min:0'],
            'counselor_fee_due_date' => ['nullable', 'date'],
            'college_fee_due_date' => ['nullable', 'date'],
            'fee_ledger_account_id' => ['required', 'exists:ledger_accounts,id'],
        ]);

        $this->feeService->setFees(
            $student,
            $validated,
            $this->accountCreatedById(),
            null
        );

        if ($student->lead_id) {
            \App\Models\Lead::where('id', $student->lead_id)
                ->update(['registration_fee_plan' => $validated['registration_fee_plan']]);
        }

        ActivityLogger::log(
            "Updated student fees for {$student->name}",
            'Update',
            $this->accountActor(),
            $validated
        );

        return back()->with('success', 'Student fees updated successfully.');
    }

    public function recordPayment(Request $request, int $id)
    {
        $this->authorizeAccountManage();

        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'purpose' => ['required', Rule::in([
                StudentFeeService::PURPOSE_REGISTRATION,
                StudentFeeService::PURPOSE_COUNSELOR,
                StudentFeeService::PURPOSE_COLLEGE,
            ])],
            'amount' => ['required', 'numeric', 'min:1'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'remark' => ['nullable', 'string', 'max:500'],
        ]);

        $summary = $this->feeService->feeSummary($student);

        if (!$summary['fees_set']) {
            return back()->with('error', 'Set student fees before recording a payment.');
        }

        if ($summary['registration_required_first'] && $validated['purpose'] !== StudentFeeService::PURPOSE_REGISTRATION) {
            return back()->with('error', 'Please collect the Registration Fee first.');
        }

        $extra = [
            'gateway' => 'account',
            'metadata' => [
                'mode' => 'account',
                'recorded_via' => is_admin_account_portal() ? 'admin' : 'account',
            ],
        ];

        if (!empty($validated['transaction_id'])) {
            $extra['transaction_id'] = $validated['transaction_id'];
        }

        if (!empty($validated['remark'])) {
            $extra['remark'] = $validated['remark'];
        }

        try {
            $payment = $this->feeService->recordInstallment(
                $student,
                $validated['purpose'],
                round((float) $validated['amount'], 2),
                $extra
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->feeService->markPaid($payment, [
            'mode' => 'account',
            'recorded_by_account_id' => $this->accountCreatedById(),
        ]);
        $this->feeService->sendReceipt($payment->fresh(['student', 'counselor']));

        ActivityLogger::log(
            "Recorded student fee payment for {$student->name} (₹{$validated['amount']})",
            'Create',
            $this->accountActor(),
            [
                'student_id' => $student->id,
                'purpose' => $validated['purpose'],
                'amount' => $validated['amount'],
                'transaction_id' => $payment->transaction_id,
            ]
        );

        return back()->with('success', 'Payment recorded successfully. Receipt emailed to the student.');
    }
}
