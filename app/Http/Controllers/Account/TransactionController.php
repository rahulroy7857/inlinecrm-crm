<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Concerns\ManagesAccountPortal;
use App\Models\AccountTransaction;
use App\Models\LedgerAccount;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    use ManagesAccountPortal;

    public function index(Request $request)
    {
        $query = AccountTransaction::with(['ledgerAccount', 'toLedgerAccount', 'createdBy', 'studentPayment'])
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
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $totals = [
            'credit' => (clone $query)->where('entry_type', 'credit')->sum('amount'),
            'debit' => (clone $query)->where('entry_type', 'debit')->sum('amount'),
        ];
        $totals['net'] = (float) $totals['credit'] - (float) $totals['debit'];

        $transactions = $query->paginate(25)->withQueryString();
        $ledgerAccounts = LedgerAccount::where('status', 'Active')->orderBy('name')->get();

        return view('account.transactions.index', compact('transactions', 'ledgerAccounts', 'totals'));
    }

    public function create()
    {
        $this->authorizeAccountManage();

        $ledgerAccounts = LedgerAccount::where('status', 'Active')->orderBy('name')->get();

        return view('account.transactions.create', compact('ledgerAccounts'));
    }

    public function searchPartyStudents(Request $request)
    {
        $this->authorizeAccountManage();

        $term = trim((string) $request->get('q', ''));
        $limit = $term === '' ? 10 : 25;

        $students = Student::query()
            ->select('students.id', 'students.name', 'students.mobile', 'students.lead_ref')
            ->joinSub(
                StudentPayment::query()
                    ->select('student_id', DB::raw('MAX(paid_at) as latest_paid_at'))
                    ->where('status', 'paid')
                    ->whereNotNull('paid_at')
                    ->groupBy('student_id'),
                'latest_payments',
                'latest_payments.student_id',
                '=',
                'students.id'
            )
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('students.name', 'like', "%{$term}%")
                        ->orWhere('students.mobile', 'like', "%{$term}%")
                        ->orWhere('students.email', 'like', "%{$term}%")
                        ->orWhere('students.lead_ref', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('latest_payments.latest_paid_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'results' => $students->map(fn (Student $student) => [
                'id' => $student->name,
                'text' => trim($student->name
                    . ($student->mobile ? " ({$student->mobile})" : '')
                    . ($student->lead_ref ? " — {$student->lead_ref}" : '')),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAccountManage();

        $data = $request->validate([
            'ledger_account_id' => 'required|exists:ledger_accounts,id',
            'to_ledger_account_id' => 'nullable|exists:ledger_accounts,id|different:ledger_account_id',
            'transaction_date' => 'required|date',
            'entry_type' => 'nullable|in:credit,debit',
            'category' => 'required|in:income,expense,transfer,refund,forwarding_fee,other',
            'reference_no' => 'nullable|string|max:100',
            'party_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        if ($data['category'] === 'transfer' && empty($data['to_ledger_account_id'])) {
            return back()->withErrors(['to_ledger_account_id' => 'Target account is required for transfers.'])->withInput();
        }

        // Income / forwarding fee increase balance; expense / refund reduce it.
        if (in_array($data['category'], ['income', 'forwarding_fee'], true)) {
            $data['entry_type'] = 'credit';
        } elseif (in_array($data['category'], ['expense', 'refund'], true)) {
            $data['entry_type'] = 'debit';
        } elseif (empty($data['entry_type'])) {
            $data['entry_type'] = 'debit';
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
                    'description' => trim(($data['description'] ?? '') . ' (Transfer out)'),
                ]));

                AccountTransaction::create(array_merge($base, [
                    'ledger_account_id' => $data['to_ledger_account_id'],
                    'to_ledger_account_id' => $data['ledger_account_id'],
                    'entry_type' => 'credit',
                    'description' => trim(($data['description'] ?? '') . ' (Transfer in)'),
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

        return redirect(account_route('transactions.index'))->with('success', 'Transaction recorded successfully.');
    }

    public function edit($id)
    {
        $this->authorizeAccountAdminEdit();

        $transaction = AccountTransaction::findOrFail($id);
        $ledgerAccounts = LedgerAccount::where('status', 'Active')->orderBy('name')->get();

        return view('account.transactions.edit', compact('transaction', 'ledgerAccounts'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAccountAdminEdit();

        $transaction = AccountTransaction::findOrFail($id);

        $data = $request->validate([
            'ledger_account_id' => 'required|exists:ledger_accounts,id',
            'to_ledger_account_id' => 'nullable|exists:ledger_accounts,id|different:ledger_account_id',
            'transaction_date' => 'required|date',
            'entry_type' => 'nullable|in:credit,debit',
            'category' => 'required|in:income,expense,transfer,refund,forwarding_fee,other',
            'reference_no' => 'nullable|string|max:100',
            'party_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        if ($data['category'] === 'transfer' && empty($data['to_ledger_account_id'])) {
            return back()->withErrors(['to_ledger_account_id' => 'Target account is required for transfers.'])->withInput();
        }

        if (in_array($data['category'], ['income', 'forwarding_fee'], true)) {
            $data['entry_type'] = 'credit';
        } elseif (in_array($data['category'], ['expense', 'refund'], true)) {
            $data['entry_type'] = 'debit';
        } elseif (empty($data['entry_type'])) {
            $data['entry_type'] = $transaction->entry_type ?: 'debit';
        }

        $before = $transaction->toArray();

        $transaction->update([
            'ledger_account_id' => $data['ledger_account_id'],
            'to_ledger_account_id' => $data['to_ledger_account_id'] ?? null,
            'transaction_date' => $data['transaction_date'],
            'entry_type' => $data['entry_type'],
            'category' => $data['category'],
            'reference_no' => $data['reference_no'] ?? null,
            'party_name' => $data['party_name'] ?? null,
            'amount' => $data['amount'],
            'payment_mode' => $data['payment_mode'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        if ($transaction->student_payment_id && $transaction->studentPayment) {
            $transaction->studentPayment->update([
                'ledger_account_id' => $data['ledger_account_id'],
                'amount' => $data['amount'],
                'transaction_id' => $data['reference_no'] ?? $transaction->studentPayment->transaction_id,
                'paid_at' => $data['transaction_date'],
            ]);
        }

        ActivityLogger::log(
            'Updated account transaction',
            'Update',
            $this->accountActor(),
            ['before' => $before, 'after' => $transaction->fresh()->toArray()]
        );

        return redirect(account_route('transactions.index'))->with('success', 'Transaction updated successfully.');
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
