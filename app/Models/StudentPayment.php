<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    protected $fillable = [
        'student_id',
        'purpose',
        'counselor_id',
        'ledger_account_id',
        'amount',
        'gateway',
        'transaction_id',
        'status',
        'metadata',
        'remark',
        'paid_at',
        'receipt_sent_at',
        'recorded_by_admin_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'receipt_sent_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class);
    }

    public function accountTransaction()
    {
        return $this->hasOne(AccountTransaction::class);
    }

    public function purposeLabel(): string
    {
        return \App\Services\StudentFeeService::purposeLabels()[$this->purpose] ?? $this->purpose;
    }
}
