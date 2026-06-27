<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'log_content',
        'action',
        'causer_type',
        'causer_id',
        'properties'
    ];

    protected $casts = [
        'properties' => 'array'
    ];

    public function causer()
    {
        return $this->morphTo();
    }
}