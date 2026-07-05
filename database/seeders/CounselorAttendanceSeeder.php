<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Counselor;
use App\Http\Controllers\Account\CounselorSalaryController;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class CounselorAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $defaultWorkingDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $salaries = [35000, 40000, 45000, 50000];
        $seededMonths = [];

        Counselor::query()->each(function (Counselor $counselor, int $index) use (
            $defaultWorkingDays,
            $salaries,
            &$seededMonths
        ) {
            if (empty($counselor->working_days) || (float) $counselor->salary <= 0) {
                $counselor->update([
                    'joining_date' => $counselor->joining_date ?? '2025-01-01',
                    'office_start_time' => $counselor->office_start_time ?? '09:00',
                    'office_end_time' => $counselor->office_end_time ?? '18:00',
                    'working_days' => $counselor->working_days ?: $defaultWorkingDays,
                    'salary' => (float) $counselor->salary > 0
                        ? $counselor->salary
                        : $salaries[$index % count($salaries)],
                ]);
                $counselor->refresh();
            }

            foreach (CounselorSalaryController::availableSalaryMonths() as $month) {
                $targetMonth = Carbon::createFromFormat('Y-m', $month['value'])->startOfMonth();
                $monthStart = $targetMonth->copy()->startOfDay();
                $monthEnd = $targetMonth->copy()->endOfMonth()->endOfDay();
                $seededMonths[$month['label']] = true;

                ActivityLog::query()
                    ->where('causer_type', Counselor::class)
                    ->where('causer_id', $counselor->id)
                    ->whereIn('action', ['Login', 'Logout'])
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->delete();

                $this->seedAttendanceForMonth($counselor, $monthStart, $monthEnd);
            }
        });

        $monthList = implode(', ', array_keys($seededMonths));
        $this->command?->info(
            'Seeded attendance for ' . Counselor::count() . " counselors across: {$monthList}."
        );
    }

    private function seedAttendanceForMonth(Counselor $counselor, Carbon $monthStart, Carbon $monthEnd): void
    {
        $officeStart = Carbon::parse($counselor->office_start_time);
        $officeEnd = Carbon::parse($counselor->office_end_time);
        $workingDays = $counselor->working_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $joiningDate = $counselor->joining_date
            ? Carbon::parse($counselor->joining_date)->startOfDay()
            : null;

        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $date) {
            if ($joiningDate && $date->lt($joiningDate)) {
                continue;
            }

            $dayKey = strtolower($date->format('l'));
            if (!in_array($dayKey, $workingDays, true)) {
                continue;
            }

            $roll = random_int(1, 100);

            if ($roll <= 15) {
                continue;
            }

            if ($roll <= 25) {
                $loginAt = $date->copy()->setTimeFromTimeString($officeStart->format('H:i'))->addMinutes(random_int(10, 45));
                $logoutAt = $date->copy()->setTimeFromTimeString($officeEnd->format('H:i'))->subMinutes(random_int(5, 30));
            } else {
                $loginAt = $date->copy()->setTimeFromTimeString($officeStart->format('H:i'))->subMinutes(random_int(0, 10));
                $logoutAt = $date->copy()->setTimeFromTimeString($officeEnd->format('H:i'))->addMinutes(random_int(0, 15));
            }

            ActivityLog::create([
                'log_content' => "Counselor logged in: {$counselor->name}",
                'action' => 'Login',
                'causer_type' => Counselor::class,
                'causer_id' => $counselor->id,
                'properties' => ['ip' => '127.0.0.1', 'seeded' => true],
                'created_at' => $loginAt,
                'updated_at' => $loginAt,
            ]);

            ActivityLog::create([
                'log_content' => 'Counselor logged out: ' . $counselor->name,
                'action' => 'Logout',
                'causer_type' => Counselor::class,
                'causer_id' => $counselor->id,
                'properties' => ['seeded' => true],
                'created_at' => $logoutAt,
                'updated_at' => $logoutAt,
            ]);
        }
    }
}
