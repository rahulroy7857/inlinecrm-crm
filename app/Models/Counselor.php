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
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}