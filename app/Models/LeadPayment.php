<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadPayment extends Model
{
    protected $fillable = [
        'lead_id',
        'payment_date',
        'payment_type',
        'transaction_type',
        'payment_mode',
        'amount',
        'remark',
        'transaction_other_message',
        'payment_type_other_message',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function accountTransaction()
    {
        return $this->hasOne(AccountTransaction::class);
    }

    public function studentPayment()
    {
        return $this->hasOne(StudentPayment::class);
    }
}