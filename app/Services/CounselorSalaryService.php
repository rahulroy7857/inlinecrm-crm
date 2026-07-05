<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Counselor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class CounselorSalaryService
{
    public function calculateForMonth(Counselor $counselor, int $year, int $month): array
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

    public function calculateAllForMonth(int $year, int $month): Collection
    {
        return Counselor::query()
            ->where('salary', '>', 0)
            ->whereNotNull('working_days')
            ->orderBy('name')
            ->get()
            ->map(fn (Counselor $counselor) => $this->calculateForMonth($counselor, $year, $month));
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

        return collect($byDate)->map(function (array $day, string $dateKey) use ($officeStart, $officeEnd) {
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
                'date' => $dateKey,
                'login_at' => $day['login_at'],
                'logout_at' => $day['logout_at'],
                'present' => $hasLogin && $hasLogout && $onTime,
            ];
        });
    }
}
