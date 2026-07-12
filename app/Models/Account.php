<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Account extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'role',
        'status',
        'break_login_locked',
        'break_login_locked_at',
        'break_login_lock_reason',
        'break_login_unlocked_by',
        'break_login_unlocked_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'break_login_locked' => 'boolean',
        'break_login_locked_at' => 'datetime',
        'break_login_unlocked_at' => 'datetime',
    ];

    public function breaks()
    {
        return $this->hasMany(AccountBreak::class);
    }

    public function isBreakLoginLocked(): bool
    {
        return (bool) $this->break_login_locked;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canManage(): bool
    {
        return in_array($this->role, ['admin', 'accountant']);
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'created_by');
    }
}
