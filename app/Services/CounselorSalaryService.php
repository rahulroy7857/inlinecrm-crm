<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\ActivityLog;
use App\Models\Counselor;
use App\Models\CounselorSalaryPayment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CounselorSalaryService
{
    public function calculateForMonth(Counselor $counselor, int $year, int $month): array
    {
        $row = $this->calculateForMonthWithoutPaymentLookup($counselor, $year, $month);
        $payment = $this->paymentForMonth($counselor->id, $year, $month);
        $isPaid = $payment && $payment->isPaid();

        return array_merge($row, [
            'payment' => $payment,
            'payment_status' => $isPaid ? CounselorSalaryPayment::STATUS_PAID : CounselorSalaryPayment::STATUS_UNPAID,
            'is_paid' => $isPaid,
            'paid_at' => $payment?->paid_at,
            'paid_amount' => $payment ? (float) $payment->amount : null,
        ]);
    }

    public function calculateAllForMonth(int $year, int $month): Collection
    {
        $payments = CounselorSalaryPayment::query()
            ->with(['ledgerAccount', 'paidBy'])
            ->where('year', $year)
            ->where('month', $month)
            ->get()
            ->keyBy('counselor_id');

        return Counselor::query()
            ->where('salary', '>', 0)
            ->whereNotNull('working_days')
            ->orderBy('name')
            ->get()
            ->map(function (Counselor $counselor) use ($year, $month, $payments) {
                $row = $this->calculateForMonthWithoutPaymentLookup($counselor, $year, $month);
                $payment = $payments->get($counselor->id);
                $isPaid = $payment && $payment->isPaid();

                return array_merge($row, [
                    'payment' => $payment,
                    'payment_status' => $isPaid ? CounselorSalaryPayment::STATUS_PAID : CounselorSalaryPayment::STATUS_UNPAID,
                    'is_paid' => $isPaid,
                    'paid_at' => $payment?->paid_at,
                    'paid_amount' => $payment ? (float) $payment->amount : null,
                ]);
            });
    }

    public function paySalary(
        Counselor $counselor,
        int $year,
        int $month,
        array $data,
        ?int $paidByAccountId = null
    ): CounselorSalaryPayment {
        $existing = $this->paymentForMonth($counselor->id, $year, $month);
        if ($existing && $existing->isPaid()) {
            throw new \InvalidArgumentException('Salary for this month is already marked as paid.');
        }

        $salary = $this->calculateForMonthWithoutPaymentLookup($counselor, $year, $month);
        $amount = (float) ($data['amount'] ?? $salary['net_salary']);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Net salary amount must be greater than zero.');
        }

        $paidAt = isset($data['paid_at'])
            ? Carbon::parse($data['paid_at'])
            : now();

        return DB::transaction(function () use ($counselor, $year, $month, $salary, $amount, $data, $paidByAccountId, $paidAt) {
            $payment = CounselorSalaryPayment::updateOrCreate(
                [
                    'counselor_id' => $counselor->id,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'base_salary' => $salary['base_salary'],
                    'deduction' => $salary['deduction'],
                    'amount' => $amount,
                    'status' => CounselorSalaryPayment::STATUS_PAID,
                    'paid_at' => $paidAt,
                    'paid_by' => $paidByAccountId,
                    'ledger_account_id' => $data['ledger_account_id'],
                    'payment_mode' => $data['payment_mode'] ?? null,
                    'reference_no' => $data['reference_no'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            $monthLabel = Carbon::create($year, $month, 1)->format('F Y');

            AccountTransaction::updateOrCreate(
                ['counselor_salary_payment_id' => $payment->id],
                [
                    'ledger_account_id' => $data['ledger_account_id'],
                    'academic_year_id' => session('academic_year_id'),
                    'created_by' => $paidByAccountId,
                    'transaction_date' => $paidAt->toDateString(),
                    'entry_type' => 'debit',
                    'category' => 'expense',
                    'reference_no' => $data['reference_no'] ?? ('SAL-' . $counselor->id . '-' . sprintf('%04d%02d', $year, $month)),
                    'party_name' => $counselor->name,
                    'amount' => $amount,
                    'payment_mode' => $data['payment_mode'] ?? null,
                    'description' => trim(
                        'Counselor salary — ' . $counselor->name . ' — ' . $monthLabel
                        . (!empty($data['notes']) ? ' | ' . $data['notes'] : '')
                    ),
                    'is_crm_synced' => false,
                ]
            );

            return $payment->fresh(['ledgerAccount', 'paidBy']);
        });
    }

    public function paymentForMonth(int $counselorId, int $year, int $month): ?CounselorSalaryPayment
    {
        return CounselorSalaryPayment::query()
            ->with(['ledgerAccount', 'paidBy'])
            ->where('counselor_id', $counselorId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
    }

    private function calculateForMonthWithoutPaymentLookup(Counselor $counselor, int $year, int $month): array
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();
        $daysInMonth = $monthStart->daysInMonth;
        $baseSalary = (float) $counselor->salary;
        $dailyRate = $daysInMonth > 0 ? round($baseSalary / $daysInMonth, 2) : 0;

        $workingDays = $counselor->working_days ?? [];
        $joiningDate = $counselor->joining_date
            ? Carbon::parse($counselor->joining_date)->startOfDay()
            : null;

        $scheduledDays = $this->scheduledWorkingDates($monthStart, $monthEnd, $workingDays, $joiningDate);
        $attendance = $this->attendanceForMonth($counselor, $monthStart, $monthEnd);

        $attendedDays = 0;
        $dayDetails = [];

        foreach ($scheduledDays as $date) {
            $dateKey = $date->toDateString();
            $dayLog = $attendance->get($dateKey);
            $isPresent = $dayLog !== null && $dayLog['present'];

            if ($isPresent) {
                $attendedDays++;
            }

            $dayDetails[] = [
                'date' => $dateKey,
                'day' => $date->format('l'),
                'present' => $isPresent,
                'login_at' => $dayLog['login_at'] ?? null,
                'logout_at' => $dayLog['logout_at'] ?? null,
            ];
        }

        $expectedWorkingDays = count($scheduledDays);
        $absentDays = max(0, $expectedWorkingDays - $attendedDays);
        $deduction = round($absentDays * $dailyRate, 2);
        $netSalary = max(0, round($baseSalary - $deduction, 2));

        return [
            'counselor' => $counselor,
            'year' => $year,
            'month' => $month,
            'month_label' => $monthStart->format('F Y'),
            'base_salary' => $baseSalary,
            'days_in_month' => $daysInMonth,
            'daily_rate' => $dailyRate,
            'expected_working_days' => $expectedWorkingDays,
            'attended_days' => $attendedDays,
            'absent_days' => $absentDays,
            'deduction' => $deduction,
            'net_salary' => $netSalary,
            'day_details' => $dayDetails,
        ];
    }

    private function scheduledWorkingDates(
        Carbon $monthStart,
        Carbon $monthEnd,
        array $workingDays,
        ?Carbon $joiningDate
    ): array {
        $dates = [];

        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $date) {
            if ($joiningDate && $date->lt($joiningDate)) {
                continue;
            }

            $dayKey = strtolower($date->format('l'));

            if (in_array($dayKey, $workingDays, true)) {
                $dates[] = $date->copy();
            }
        }

        return $dates;
    }

    private function attendanceForMonth(Counselor $counselor, Carbon $monthStart, Carbon $monthEnd): Collection
    {
        $logs = ActivityLog::query()
            ->where('causer_type', Counselor::class)
            ->where('causer_id', $counselor->id)
            ->whereIn('action', ['Login', 'Logout'])
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->orderBy('created_at')
            ->get();

        $byDate = [];

        foreach ($logs as $log) {
            $dateKey = $log->created_at->toDateString();

            if (!isset($byDate[$dateKey])) {
                $byDate[$dateKey] = [
                    'login_at' => null,
                    'logout_at' => null,
                ];
            }

            $time = $log->created_at->format('H:i');

            if ($log->action === 'Login' && $byDate[$dateKey]['login_at'] === null) {
                $byDate[$dateKey]['login_at'] = $time;
            }

            if ($log->action === 'Logout') {
                $byDate[$dateKey]['logout_at'] = $time;
            }
        }

        $officeStart = $counselor->office_start_time
            ? Carbon::parse($counselor->office_start_time)->format('H:i')
            : null;
        $officeEnd = $counselor->office_end_time
            ? Carbon::parse($counselor->office_end_time)->format('H:i')
            : null;

        return collect($byDate)->map(function (array $day) use ($officeStart, $officeEnd) {
            $hasLogin = $day['login_at'] !== null;
            $hasLogout = $day['logout_at'] !== null;

            $onTime = $hasLogin && $hasLogout;

            if ($onTime && $officeStart && $day['login_at'] > $officeStart) {
                $onTime = false;
            }

            if ($onTime && $officeEnd && $day['logout_at'] < $officeEnd) {
                $onTime = false;
            }

            return [
                'login_at' => $day['login_at'],
                'logout_at' => $day['logout_at'],
                'present' => $hasLogin && $hasLogout && $onTime,
            ];
        });
    }
}
