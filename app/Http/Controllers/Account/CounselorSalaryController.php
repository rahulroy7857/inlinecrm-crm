<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Concerns\ManagesAccountPortal;
use App\Models\Counselor;
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

        return view('account.counselor-salaries.show', array_merge(
            $this->monthViewData($monthDate),
            ['salary' => $salary, 'counselor' => $counselor]
        ));
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
