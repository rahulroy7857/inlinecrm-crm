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
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'languages' => 'array',
        'status' => 'boolean'
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}