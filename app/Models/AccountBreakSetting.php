<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountBreakSetting extends Model
{
    protected $fillable = [
        'type',
        'label',
        'duration_minutes',
        'requires_admin_approval',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'requires_admin_approval' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function durationLabel(): string
    {
        if ($this->duration_minutes) {
            return $this->duration_minutes . ' min';
        }

        if ($this->requires_admin_approval) {
            return 'Admin approval';
        }

        return 'Open ended';
    }
}
