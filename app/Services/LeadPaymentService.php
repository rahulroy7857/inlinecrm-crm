<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\LeadPayment;
use Illuminate\Support\Facades\DB;

class LeadPaymentService
{
    public function __construct(
        private StudentFeeService $studentFeeService
    ) {
    }

    public function create(array $data, ?int $ledgerAccountId = null, ?int $createdById = null): LeadPayment
    {
        return DB::transaction(function () use ($data, $ledgerAccountId, $createdById) {
            $payment = LeadPayment::create([
                'lead_id' => $data['lead_id'],
                'payment_date' => $data['payment_date'],
                'payment_type' => $data['payment_type'],
                'transaction_type' => (string) $data['transaction_type'],
                'payment_mode' => $data['payment_mode'],
                'amount' => $data['amount'],
                'remark' => $data['remarks'] ?? null,
            ]);

            if ($ledgerAccountId) {
                $this->syncToLedgerAccount($payment, $ledgerAccountId, $createdById);
            }

            $this->studentFeeService->recordFromLeadPayment($payment, $ledgerAccountId);

            return $payment;
        });
    }

    public function syncToLedgerAccount(LeadPayment $payment, int $ledgerAccountId, ?int $createdById = null): AccountTransaction
    {
        if (AccountTransaction::where('lead_payment_id', $payment->id)->exists()) {
            return AccountTransaction::where('lead_payment_id', $payment->id)->first();
        }

        $payment->loadMissing('lead');
        $entryType = in_array($payment->transaction_type, ['1', '2', '3'], true) ? 'credit' : 'debit';
        $category = $entryType === 'credit' ? 'income' : 'expense';

        return AccountTransaction::create([
            'ledger_account_id' => $ledgerAccountId,
            'lead_payment_id' => $payment->id,
            'academic_year_id' => session('academic_year_id'),
            'created_by' => $createdById,
            'transaction_date' => $payment->payment_date,
            'entry_type' => $entryType,
            'category' => $category,
            'party_name' => $payment->lead?->name,
            'amount' => $payment->amount,
            'payment_mode' => $payment->payment_mode,
            'description' => "{$payment->payment_type} — {$payment->remark}",
            'is_crm_synced' => true,
        ]);
    }

    /**
     * Apply existing lead payments to student fee balances (idempotent).
     */
    public function applyExistingToStudentFees(?int $leadPaymentId = null): int
    {
        $query = LeadPayment::query()->orderBy('id');

        if ($leadPaymentId) {
            $query->where('id', $leadPaymentId);
        }

        $applied = 0;

        $query->chunkById(100, function ($payments) use (&$applied) {
            foreach ($payments as $payment) {
                if ($this->studentFeeService->recordFromLeadPayment($payment)) {
                    $applied++;
                }
            }
        });

        return $applied;
    }
}
