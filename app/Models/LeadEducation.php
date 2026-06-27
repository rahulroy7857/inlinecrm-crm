<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadEducation extends Model
{
    protected $fillable = [
        'lead_id',
        'qualification',
        'marks',
        'institute',
        'year'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}