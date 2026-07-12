<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\LedgerAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private \App\Services\AccountWorkingHoursService $workingHoursService
    ) {}

    public function index()
    {
        $yearId = session('academic_year_id');

        $ledgerAccounts = LedgerAccount::where('status', 'Active')->get();
        $totalBalance = $ledgerAccounts->sum(fn ($a) => $a->current_balance);

        $query = AccountTransaction::query();
        if ($yearId) {
            $query->where('academic_year_id', $yearId);
        }

        $totalIncome = (clone $query)->where('entry_type', 'credit')->where('category', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('entry_type', 'debit')->where('category', 'expense')->sum('amount');
        $todayTransactions = AccountTransaction::whereDate('transaction_date', Carbon::today())->count();

        $recentTransactions = AccountTransaction::with(['ledgerAccount', 'createdBy'])
            ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $currentYear = (int) date('Y');
        $baseQuery = AccountTransaction::query()->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId));

        $monthlyIncomeRows = (clone $baseQuery)
            ->where('entry_type', 'credit')
            ->where('category', 'income')
            ->whereYear('transaction_date', $currentYear)
            ->select(DB::raw('MONTH(transaction_date) as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyExpenseRows = (clone $baseQuery)
            ->where('entry_type', 'debit')
            ->where('category', 'expense')
            ->whereYear('transaction_date', $currentYear)
            ->select(DB::raw('MONTH(transaction_date) as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyIncomeData = array_fill(1, 12, 0);
        $monthlyExpenseData = array_fill(1, 12, 0);

        foreach ($monthlyIncomeRows as $month => $total) {
            $monthlyIncomeData[(int) $month] = (float) $total;
        }

        foreach ($monthlyExpenseRows as $month => $total) {
            $monthlyExpenseData[(int) $month] = (float) $total;
        }

        $accountBalanceLabels = $ledgerAccounts->pluck('name')->values()->all();
        $accountBalanceValues = $ledgerAccounts->pluck('current_balance')->map(fn ($v) => (float) $v)->values()->all();
        $netProfit = (float) $totalIncome - (float) $totalExpense;

        $workingHours = null;
        if (auth()->guard('account')->check()) {
            $workingHours = $this->workingHoursService->getTodaySummary(
                auth()->guard('account')->user()
            );
        }

        return view('account.dashboard', compact(
            'ledgerAccounts',
            'totalBalance',
            'totalIncome',
            'totalExpense',
            'todayTransactions',
            'recentTransactions',
            'monthlyIncomeData',
            'monthlyExpenseData',
            'accountBalanceLabels',
            'accountBalanceValues',
            'netProfit',
            'workingHours'
        ));
    }
}
