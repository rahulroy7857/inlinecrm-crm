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
        'email_verified_at',
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
        'counselor_fee',
        'college_fee',
        'counselor_fee_due_date',
        'college_fee_due_date',
        'fees_set_at',
        'fees_set_by',
        'fees_set_by_account_id',
        'fee_ledger_account_id',
        'registration_fee_plan',
        'registration_fee',
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
        'email_verified_at' => 'datetime',
        'fees_set_at' => 'datetime',
        'counselor_fee_due_date' => 'date',
        'college_fee_due_date' => 'date',
        'payment_amount' => 'decimal:2',
        'counselor_fee' => 'decimal:2',
        'college_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function feesSetBy()
    {
        return $this->belongsTo(Counselor::class, 'fees_set_by');
    }

    public function feesSetByAccount()
    {
        return $this->belongsTo(Account::class, 'fees_set_by_account_id');
    }

    public function feeLedgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'fee_ledger_account_id');
    }

    public function hasPaid(): bool
    {
        if ((float) ($this->registration_fee ?? 0) > 0) {
            $paid = (float) $this->payments()
                ->where('purpose', \App\Services\StudentFeeService::PURPOSE_REGISTRATION)
                ->where('status', 'paid')
                ->sum('amount');

            return $paid >= (float) $this->registration_fee;
        }

        return $this->payment_status === 'paid';
    }

    public function hasFeesSet(): bool
    {
        return (float) ($this->registration_fee ?? 0) > 0
            || (float) ($this->counselor_fee ?? 0) > 0
            || (float) ($this->college_fee ?? 0) > 0;
    }

    public function totalFeeRemaining(): float
    {
        $summary = app(\App\Services\StudentFeeService::class)->feeSummary($this);

        return (float) ($summary['total_remaining'] ?? 0);
    }

    public function hasAllFeesPaid(): bool
    {
        return $this->hasFeesSet() && $this->totalFeeRemaining() <= 0;
    }

    public function feeCompletionStatusLabel(): string
    {
        if (!$this->hasFeesSet()) {
            return 'Fees Not Set';
        }

        return $this->hasAllFeesPaid()
            ? 'Completed'
            : 'Incomplete';
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
}
