<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerAccount extends Model
{
    protected $fillable = [
        'name',
        'type',
        'account_number',
        'bank_name',
        'ifsc_code',
        'opening_balance' => 'nullable|decimal:2',
        'status',
        'description',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public function getCurrentBalanceAttribute(): float
    {
        $credits = $this->transactions()->where('entry_type', 'credit')->sum('amount');
        $debits = $this->transactions()->where('entry_type', 'debit')->sum('amount');

        return (float) $this->opening_balance + $credits - $debits;
    }
}
