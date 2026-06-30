<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'lead_id',
        'lead_ref',
        'counselor_id',
        'course_id',
        'name',
        'email',
        'mobile',
        'country',
        'state',
        'password',
        'application_status',
        'profile_completed',
        'profile_completed_at',
        'submitted_at',
        'payment_status',
        'payment_amount',
        'payment_reference',
        'paid_at',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'guardian_name',
        'relation',
        'gender',
        'dob',
        'aadhar',
        'present_address',
        'present_city',
        'present_pin',
        'permanent_address',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'profile_completed' => 'boolean',
        'status' => 'boolean',
        'dob' => 'date',
        'profile_completed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_amount' => 'decimal:2',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function payments()
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function hasRequiredDocuments(): bool
    {
        foreach (config('student.required_documents', []) as $type) {
            if (!$this->documents()->where('document_type', $type)->exists()) {
                return false;
            }
        }

        return true;
    }

    public function documentsProgressLabel(): string
    {
        $required = config('student.required_documents', []);
        $uploaded = $this->documents()->whereIn('document_type', $required)->count();

        return $uploaded . '/' . count($required);
    }

    public function applicationProgressPercent(): int
    {
        $steps = 0;

        if ($this->isProfileComplete()) {
            $steps++;
        }

        if ($this->hasPaid()) {
            $steps++;
        }

        if ($this->hasRequiredDocuments()) {
            $steps++;
        }

        if (in_array($this->application_status, ['submitted', 'under_review', 'approved'], true)) {
            $steps++;
        }

        return (int) round(($steps / 4) * 100);
    }

    public function isApplicationSubmitted(): bool
    {
        return in_array($this->application_status, ['submitted', 'under_review', 'approved'], true);
    }

    public function applicationStatusLabel(): string
    {
        return match ($this->application_status) {
            'registered' => 'Registered',
            'profile_completed' => 'Profile Completed',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst(str_replace('_', ' ', $this->application_status)),
        };
    }

    public function isProfileComplete(): bool
    {
        return (bool) $this->profile_completed;
    }

    public function hasPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
