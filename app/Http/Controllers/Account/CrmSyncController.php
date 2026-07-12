<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Concerns\ManagesAccountPortal;
use App\Models\AccountTransaction;
use App\Models\LeadPayment;
use App\Models\LedgerAccount;
use App\Services\ActivityLogger;
use App\Services\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmSyncController extends Controller
{
    use ManagesAccountPortal;

    public function index(Request $request)
    {
        $syncedIds = AccountTransaction::whereNotNull('lead_payment_id')->pluck('lead_payment_id');

        $pendingPayments = LeadPayment::with('lead')
            ->whereNotIn('id', $syncedIds)
            ->orderByDesc('payment_date')
            ->paginate(20, ['*'], 'pending_page');

        $syncedPayments = LeadPayment::with('lead')
            ->whereIn('id', $syncedIds)
            ->orderByDesc('payment_date')
            ->paginate(20, ['*'], 'synced_page');

        $ledgerAccounts = LedgerAccount::where('status', 'Active')->orderBy('name')->get();

        return view('account.crm-sync.index', compact('pendingPayments', 'syncedPayments', 'ledgerAccounts'));
    }

    public function sync(Request $request)
    {
        if (!$this->accountCanManage()) {
            abort(403);
        }

        $request->validate([
            'lead_payment_ids' => 'required|array|min:1',
            'lead_payment_ids.*' => 'exists:lead_payments,id',
            'ledger_account_id' => 'required|exists:ledger_accounts,id',
        ]);

        $synced = 0;
        $skipped = 0;

        DB::transaction(function () use ($request, &$synced, &$skipped) {
            $feeService = app(StudentFeeService::class);

            foreach ($request->lead_payment_ids as $paymentId) {
                if (AccountTransaction::where('lead_payment_id', $paymentId)->exists()) {
                    $skipped++;
                    continue;
                }

                $payment = LeadPayment::with('lead')->findOrFail($paymentId);
                $entryType = in_array($payment->transaction_type, ['1', '2', '3']) ? 'credit' : 'debit';
                $category = $entryType === 'credit' ? 'income' : 'expense';

                AccountTransaction::create([
                    'ledger_account_id' => $request->ledger_account_id,
                    'lead_payment_id' => $payment->id,
                    'academic_year_id' => session('academic_year_id'),
                    'created_by' => $this->accountCreatedById(),
                    'transaction_date' => $payment->payment_date,
                    'entry_type' => $entryType,
                    'category' => $category,
                    'party_name' => $payment->lead?->name,
                    'amount' => $payment->amount,
                    'payment_mode' => $payment->payment_mode,
                    'description' => "{$payment->payment_type} — {$payment->remark}",
                    'is_crm_synced' => true,
                ]);

                $feeService->recordFromLeadPayment($payment, (int) $request->ledger_account_id);

                $synced++;
            }
        });

        ActivityLogger::log(
            "Synced {$synced} CRM payments to accounts",
            'Create',
            $this->accountActor(),
            ['synced' => $synced, 'skipped' => $skipped]
        );

        $message = "{$synced} payment(s) synced successfully.";
        if ($skipped > 0) {
            $message .= " {$skipped} already synced and skipped.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function syncAll(Request $request)
    {
        if (!$this->accountCanManage()) {
            abort(403);
        }

        $request->validate([
            'ledger_account_id' => 'required|exists:ledger_accounts,id',
        ]);

        $syncedIds = AccountTransaction::whereNotNull('lead_payment_id')->pluck('lead_payment_id');
        $pendingIds = LeadPayment::whereNotIn('id', $syncedIds)->pluck('id')->toArray();

        if (empty($pendingIds)) {
            return redirect()->back()->with('success', 'All CRM payments are already synced.');
        }

        $request->merge(['lead_payment_ids' => $pendingIds]);

        return $this->sync($request);
    }
}
