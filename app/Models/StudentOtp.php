<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentOtp extends Model
{
    protected $fillable = [
        'email',
        'lead_ref',
        'otp_hash',
        'payload',
        'expires_at',
        'verified_at',
        'attempts',
    ];

    protected $casts = [
        'payload' => 'array',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
