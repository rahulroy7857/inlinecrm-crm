<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountBreak extends Model
{
    public const TYPE_TEA_COFFEE = 'tea_coffee';
    public const TYPE_LUNCH = 'lunch';
    public const TYPE_SCHOOL_COLLEGE = 'school_college_visiting';
    public const TYPE_STUDENT_MEETING = 'student_meeting';
    public const TYPE_MEETING = 'meeting';
    public const TYPE_OUTSIDE_WORK = 'outside_work';

    public const APPROVAL_PENDING = 'pending';
    public const APPROVAL_APPROVED = 'approved';
    public const APPROVAL_REJECTED = 'rejected';

    protected $fillable = [
        'account_id',
        'type',
        'approval_status',
        'requested_at',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'duration_minutes',
        'started_at',
        'ended_at',
        'exceeded_duration',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'exceeded_duration' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function approvedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function getLabelAttribute(): string
    {
        return app(\App\Services\AccountBreakSettingsService::class)->labelFor($this->type);
    }

    public function isActive(): bool
    {
        return $this->started_at !== null && $this->ended_at === null;
    }

    public function isPendingApproval(): bool
    {
        return $this->approval_status === self::APPROVAL_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->approval_status === self::APPROVAL_REJECTED;
    }

    public static function validTypes(): array
    {
        return app(\App\Services\AccountBreakSettingsService::class)->validTypes();
    }
}
