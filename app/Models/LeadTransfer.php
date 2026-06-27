<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadTransfer extends Model
{
    protected $fillable = [
        'lead_id',
        'from_counselor_id',
        'to_counselor_id',
        'note',
        'transfer_date',
        'transferred_by'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function fromCounselor()
    {
        return $this->belongsTo(Counselor::class, 'from_counselor_id');
    }

    public function toCounselor()
    {
        return $this->belongsTo(Counselor::class, 'to_counselor_id');
    }
}