<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'is_active'];
    // example name: 2026-2027

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->is_active) {
                // Deactivate all other academic years
                static::where('id', '!=', $model->id)->update(['is_active' => false]);
            }
        });
    }
}