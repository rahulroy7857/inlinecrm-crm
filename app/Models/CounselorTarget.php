<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselorTarget extends Model
{
    protected $fillable = [
        'counselor_id',
        'course_id',
        'academic_year_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
