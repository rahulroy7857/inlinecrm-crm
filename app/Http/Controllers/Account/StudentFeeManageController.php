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

        $student = Student::findOrFail($id);
        $planKeys = array_keys(StudentFeeService::registrationPlans());

        $validated = $request->validate([
            'registration_fee_plan' => ['required', Rule::in($planKeys)],
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

        ActivityLogger::log(
            "Updated student fees for {$student->name}",
            'Update',
            $this->accountActor(),
            $validated
        );

        return back()->with('success', 'Student fees updated successfully.');
    }
}
