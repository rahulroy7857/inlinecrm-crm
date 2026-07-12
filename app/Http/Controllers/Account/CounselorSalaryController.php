<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Concerns\ManagesAccountPortal;
use App\Models\Counselor;
use App\Models\LedgerAccount;
use App\Services\ActivityLogger;
use App\Services\CounselorSalaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CounselorSalaryController extends Controller
{
    use ManagesAccountPortal;

    public const VIEWABLE_PAST_MONTHS = 3;

    public function index(Request $request, CounselorSalaryService $salaryService)
    {
        $monthDate = $this->resolveMonth($request->get('month'));

        $salaries = $salaryService->calculateAllForMonth(
            (int) $monthDate->year,
            (int) $monthDate->month
        );

        return view('account.counselor-salaries.index', array_merge(
            $this->monthViewData($monthDate),
            ['salaries' => $salaries]
        ));
    }

    public function show(Request $request, int $id, CounselorSalaryService $salaryService)
    {
        $counselor = Counselor::findOrFail($id);
        $monthDate = $this->resolveMonth($request->get('month'));

        $salary = $salaryService->calculateForMonth(
            $counselor,
            (int) $monthDate->year,
            (int) $monthDate->month
        );

        $ledgerAccounts = LedgerAccount::where('status', 'Active')->orderBy('name')->get();

        return view('account.counselor-salaries.show', array_merge(
            $this->monthViewData($monthDate),
            [
                'salary' => $salary,
                'counselor' => $counselor,
                'ledgerAccounts' => $ledgerAccounts,
            ]
        ));
    }

    public function pay(Request $request, int $id, CounselorSalaryService $salaryService)
    {
        $this->authorizeAccountManage();

        $counselor = Counselor::findOrFail($id);
        $monthDate = $this->resolveMonth($request->get('month') ?: $request->input('month'));

        $validated = $request->validate([
            'month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'ledger_account_id' => ['required', 'exists:ledger_accounts,id'],
            'paid_at' => ['required', 'date'],
            'payment_mode' => ['nullable', 'string', 'max:50'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $payment = $salaryService->paySalary(
                $counselor,
                (int) $monthDate->year,
                (int) $monthDate->month,
                [
                    'ledger_account_id' => $validated['ledger_account_id'],
                    'paid_at' => $validated['paid_at'],
                    'payment_mode' => $validated['payment_mode'] ?? null,
                    'reference_no' => $validated['reference_no'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ],
                $this->accountCreatedById()
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        ActivityLogger::log(
            'Paid counselor salary',
            'Create',
            $this->accountActor(),
            [
                'counselor_id' => $counselor->id,
                'month' => $monthDate->format('Y-m'),
                'amount' => $payment->amount,
                'payment_id' => $payment->id,
            ]
        );

        return redirect(account_route('counselor-salaries.show', [
            'id' => $counselor->id,
            'month' => $monthDate->format('Y-m'),
        ]))->with('success', 'Salary marked as paid and expense recorded in ledger.');
    }

    private function monthViewData(Carbon $monthDate): array
    {
        return [
            'selectedMonth' => $monthDate->format('Y-m'),
            'monthLabel' => $monthDate->format('F Y'),
            'minMonth' => self::earliestSalaryMonth()->format('Y-m'),
            'maxMonth' => self::defaultSalaryMonth()->format('Y-m'),
            'availableMonths' => self::availableSalaryMonths(),
        ];
    }

    private function resolveMonth(?string $month): Carbon
    {
        $earliest = self::earliestSalaryMonth();
        $latest = self::defaultSalaryMonth();

        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $selected = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

            if ($selected->gt($latest)) {
                return $latest;
            }

            if ($selected->lt($earliest)) {
                return $earliest;
            }

            return $selected;
        }

        return $latest;
    }

    public static function defaultSalaryMonth(): Carbon
    {
        return now()->subMonth()->startOfMonth();
    }

    public static function earliestSalaryMonth(): Carbon
    {
        return now()->subMonths(self::VIEWABLE_PAST_MONTHS)->startOfMonth();
    }

    /** @return array<int, array{value: string, label: string}> */
    public static function availableSalaryMonths(): array
    {
        $months = [];
        $date = self::defaultSalaryMonth();

        while ($date->gte(self::earliestSalaryMonth())) {
            $months[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->format('F Y'),
            ];
            $date = $date->copy()->subMonth();
        }

        return $months;
    }
}
