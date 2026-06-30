<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    protected $fillable = [
        'ledger_account_id',
        'to_ledger_account_id',
        'lead_payment_id',
        'academic_year_id',
        'created_by',
        'transaction_date',
        'entry_type',
        'category',
        'reference_no',
        'party_name',
        'amount',
        'payment_mode',
        'description',
        'is_crm_synced',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'is_crm_synced' => 'boolean',
    ];

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class);
    }

    public function toLedgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'to_ledger_account_id');
    }

    public function leadPayment()
    {
        return $this->belongsTo(LeadPayment::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Account::class, 'created_by');
    }
}
