<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = [
        'name',
        'code',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            // Generate code from name (e.g., "Bachelor of Computer Application" -> "BCA")
            $words = explode(' ', $course->name);
            $code = '';
            foreach ($words as $word) {
                $code .= strtoupper(substr($word, 0, 1));
            }
            
            // Make sure code is unique
            $baseCode = $code;
            $counter = 1;
            while (static::where('code', $code)->exists()) {
                $code = $baseCode . $counter;
                $counter++;
            }
            
            $course->code = $code;
        });
    }
}