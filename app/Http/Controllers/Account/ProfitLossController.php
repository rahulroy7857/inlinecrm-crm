<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $years = AcademicYear::orderByDesc('id')->get();
        $yearId = $request->get('academic_year_id', session('academic_year_id'));

        $incomeQuery = AccountTransaction::where('entry_type', 'credit')
            ->where('category', 'income');
        $expenseQuery = AccountTransaction::where('entry_type', 'debit')
            ->where('category', 'expense');

        if ($yearId) {
            $incomeQuery->where('academic_year_id', $yearId);
            $expenseQuery->where('academic_year_id', $yearId);
        }

        $totalIncome = $incomeQuery->sum('amount');
        $totalExpense = $expenseQuery->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $incomeBreakdown = AccountTransaction::selectRaw('payment_mode, SUM(amount) as total')
            ->where('entry_type', 'credit')
            ->where('category', 'income')
            ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->groupBy('payment_mode')
            ->get();

        $expenseBreakdown = AccountTransaction::selectRaw('payment_mode, SUM(amount) as total')
            ->where('entry_type', 'debit')
            ->where('category', 'expense')
            ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->groupBy('payment_mode')
            ->get();

        $selectedYear = $years->firstWhere('id', $yearId);

        return view('account.profit-loss.index', compact(
            'years',
            'yearId',
            'selectedYear',
            'totalIncome',
            'totalExpense',
            'netProfit',
            'incomeBreakdown',
            'expenseBreakdown'
        ));
    }
}
