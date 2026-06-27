<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadContactLog extends Model
{
    protected $fillable = [
        'lead_id',
        'contact_date',
        'remark',
        'duration',
        'type',
        'response_type',
        'status',
        'contacted_by'
    ];

    protected $casts = [
        'contact_date' => 'datetime'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}