<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\LedgerAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
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

        return view('account.dashboard', compact(
            'ledgerAccounts',
            'totalBalance',
            'totalIncome',
            'totalExpense',
            'todayTransactions',
            'recentTransactions'
        ));
    }
}
