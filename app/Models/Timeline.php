<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timeline extends Model
{
    protected $fillable = [
        'lead_id',
        'title',
        'description',
        'performed_by',
        'event_type',
        'event_date'
    ];

    protected $dates = [
        'event_date'
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}