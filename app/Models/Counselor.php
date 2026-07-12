<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Counselor extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'languages',
        'status',
        'break_login_locked',
        'break_login_locked_at',
        'break_login_lock_reason',
        'break_login_unlocked_by',
        'break_login_unlocked_at',
        'joining_date',
        'office_start_time',
        'office_end_time',
        'working_days',
        'salary',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'languages' => 'array',
        'working_days' => 'array',
        'status' => 'boolean',
        'break_login_locked' => 'boolean',
        'break_login_locked_at' => 'datetime',
        'break_login_unlocked_at' => 'datetime',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function unlockedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'break_login_unlocked_by');
    }

    public function isBreakLoginLocked(): bool
    {
        return (bool) $this->break_login_locked;
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function breaks()
    {
        return $this->hasMany(CounselorBreak::class);
    }
}