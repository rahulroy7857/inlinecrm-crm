<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselorSalaryPayment extends Model
{
    public const STATUS_PAID = 'paid';
    public const STATUS_UNPAID = 'unpaid';

    protected $fillable = [
        'counselor_id',
        'year',
        'month',
        'base_salary',
        'deduction',
        'amount',
        'status',
        'paid_at',
        'paid_by',
        'ledger_account_id',
        'payment_mode',
        'reference_no',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'deduction' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'year' => 'integer',
        'month' => 'integer',
    ];

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(Account::class, 'paid_by');
    }

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class);
    }

    public function accountTransaction()
    {
        return $this->hasOne(AccountTransaction::class, 'counselor_salary_payment_id');
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function monthKey(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }
}
