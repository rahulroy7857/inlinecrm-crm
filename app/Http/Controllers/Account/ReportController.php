<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('account.reports.index');
    }

    public function accountStatement(Request $request)
    {
        $ledgerAccounts = LedgerAccount::where('status', 'Active')->orderBy('name')->get();
        $accountId = $request->get('ledger_account_id', $ledgerAccounts->first()?->id);

        $account = $accountId ? LedgerAccount::find($accountId) : null;
        $transactions = collect();
        $openingBalance = 0;
        $closingBalance = 0;

        if ($account) {
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');

            $priorCredits = $account->transactions()
                ->when($fromDate, fn ($q) => $q->whereDate('transaction_date', '<', $fromDate))
                ->where('entry_type', 'credit')
                ->sum('amount');
            $priorDebits = $account->transactions()
                ->when($fromDate, fn ($q) => $q->whereDate('transaction_date', '<', $fromDate))
                ->where('entry_type', 'debit')
                ->sum('amount');

            $openingBalance = (float) $account->opening_balance + $priorCredits - $priorDebits;

            $transactions = $account->transactions()
                ->when($fromDate, fn ($q) => $q->whereDate('transaction_date', '>=', $fromDate))
                ->when($toDate, fn ($q) => $q->whereDate('transaction_date', '<=', $toDate))
                ->orderBy('transaction_date')
                ->orderBy('id')
                ->get();

            $periodCredits = $transactions->where('entry_type', 'credit')->sum('amount');
            $periodDebits = $transactions->where('entry_type', 'debit')->sum('amount');
            $closingBalance = $openingBalance + $periodCredits - $periodDebits;
        }

        return view('account.reports.account-statement', compact(
            'ledgerAccounts',
            'account',
            'transactions',
            'openingBalance',
            'closingBalance'
        ));
    }

    public function cashFlow(Request $request)
    {
        $yearId = $request->get('academic_year_id', session('academic_year_id'));

        $monthly = AccountTransaction::selectRaw("DATE_FORMAT(transaction_date, '%Y-%m') as month")
            ->selectRaw("SUM(CASE WHEN entry_type = 'credit' THEN amount ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE 0 END) as expense")
            ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('account.reports.cash-flow', compact('monthly', 'yearId'));
    }

    public function ledgerSummary()
    {
        $accounts = LedgerAccount::withCount('transactions')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(function ($account) {
                $account->balance = $account->current_balance;
                return $account;
            });

        $totalBalance = $accounts->sum('balance');

        return view('account.reports.ledger-summary', compact('accounts', 'totalBalance'));
    }
}
