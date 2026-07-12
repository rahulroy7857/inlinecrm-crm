<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DaybookController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        $transactions = AccountTransaction::with(['ledgerAccount', 'toLedgerAccount', 'createdBy'])
            ->whereDate('transaction_date', $date)
            ->orderBy('id')
            ->get();

        $totalCredit = $transactions->where('entry_type', 'credit')->sum('amount');
        $totalDebit = $transactions->where('entry_type', 'debit')->sum('amount');

        return view('account.daybook.index', compact('date', 'transactions', 'totalCredit', 'totalDebit'));
    }
}
