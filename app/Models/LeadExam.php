<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadExam extends Model
{
    protected $fillable = [
        'lead_id',
        'exam_name',
        'score',
        'year',
        'remarks'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}