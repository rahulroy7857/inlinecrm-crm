<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Concerns\ManagesAccountPortal;
use App\Models\AccountTransaction;
use App\Models\LedgerAccount;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    use ManagesAccountPortal;

    public function index(Request $request)
    {
        $query = AccountTransaction::with(['ledgerAccount', 'toLedgerAccount', 'createdBy'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }
        if ($request->filled('ledger_account_id')) {
            $query->where('ledger_account_id', $request->ledger_account_id);
        }
        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->entry_type);
        }

        $transactions = $query->paginate(25)->withQueryString();
        $ledgerAccounts = LedgerAccount::where('status', 'Active')->orderBy('name')->get();

        return view('account.transactions.index', compact('transactions', 'ledgerAccounts'));
    }

    public function create()
    {
        return redirect(account_route('lead-payments.index', ['add' => 1]));
    }

    public function store(Request $request)
    {
        $this->authorizeAccountManage();

        $data = $request->validate([
            'ledger_account_id' => 'required|exists:ledger_accounts,id',
            'to_ledger_account_id' => 'nullable|exists:ledger_accounts,id|different:ledger_account_id',
            'transaction_date' => 'required|date',
            'entry_type' => 'required|in:credit,debit',
            'category' => 'required|in:income,expense,transfer,other',
            'reference_no' => 'nullable|string|max:100',
            'party_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        if ($data['category'] === 'transfer' && empty($data['to_ledger_account_id'])) {
            return back()->withErrors(['to_ledger_account_id' => 'Target account is required for transfers.'])->withInput();
        }

        DB::transaction(function () use ($data) {
            $base = [
                'transaction_date' => $data['transaction_date'],
                'category' => $data['category'],
                'reference_no' => $data['reference_no'] ?? null,
                'party_name' => $data['party_name'] ?? null,
                'amount' => $data['amount'],
                'payment_mode' => $data['payment_mode'] ?? null,
                'description' => $data['description'] ?? null,
                'academic_year_id' => session('academic_year_id'),
                'created_by' => $this->accountCreatedById(),
                'is_crm_synced' => false,
            ];

            if ($data['category'] === 'transfer') {
                AccountTransaction::create(array_merge($base, [
                    'ledger_account_id' => $data['ledger_account_id'],
                    'to_ledger_account_id' => $data['to_ledger_account_id'],
                    'entry_type' => 'debit',
                    'description' => ($data['description'] ?? '') . ' (Transfer out)',
                ]));

                AccountTransaction::create(array_merge($base, [
                    'ledger_account_id' => $data['to_ledger_account_id'],
                    'to_ledger_account_id' => $data['ledger_account_id'],
                    'entry_type' => 'credit',
                    'description' => ($data['description'] ?? '') . ' (Transfer in)',
                ]));
            } else {
                AccountTransaction::create(array_merge($base, [
                    'ledger_account_id' => $data['ledger_account_id'],
                    'to_ledger_account_id' => $data['to_ledger_account_id'] ?? null,
                    'entry_type' => $data['entry_type'],
                ]));
            }
        });

        ActivityLogger::log(
            'Created account transaction',
            'Create',
            $this->accountActor(),
            $data
        );

        return redirect()->route('account.transactions.index')->with('success', 'Transaction recorded successfully.');
    }

    public function destroy($id)
    {
        $this->authorizeAccountManage();

        $transaction = AccountTransaction::findOrFail($id);

        if ($transaction->is_crm_synced) {
            return redirect()->back()->with('error', 'CRM-synced transactions cannot be deleted.');
        }

        $data = $transaction->toArray();
        $transaction->delete();

        ActivityLogger::log(
            'Deleted account transaction',
            'Delete',
            $this->accountActor(),
            ['transaction' => $data]
        );

        return redirect()->back()->with('success', 'Transaction deleted successfully.');
    }
}
