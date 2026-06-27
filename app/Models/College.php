<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class College extends Model
{
    protected $fillable = [
        'name',
        'place',
        'code',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($college) {
            // Generate code from name
            $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $college->name), 0, 3));
            
            // Make sure code is unique
            $baseCode = $code;
            $counter = 1;
            while (static::where('code', $code)->exists()) {
                $code = $baseCode . $counter;
                $counter++;
            }
            
            $college->code = $code;
        });
    }
}