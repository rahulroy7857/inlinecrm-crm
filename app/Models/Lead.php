<?php

namespace App\Models;
use App\Traits\HasLeadId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo as RelationsBelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasLeadId, SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'next_follow_up',
        'source_id',
        'counselor_id',
        'academic_year_id',
        'course_id',
        'specialization',
        'college_id',
        'transfer_seen',
        'country',
        'state',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'guardian_name',
        'relation',
        'gender',
        'dob',
        'aadhar',
        'notes',
        'languages',
        'mobile',
        'alternative_mobile',
        'father_mobile',
        'mother_mobile',
        'guardian_mobile',
        'personal_email',
        'father_email',
        'mother_email',
        'guardian_email',
        'present_address',
        'present_country',
        'present_state',
        'present_city',
        'present_place',
        'present_pin',
        'permanent_address',
        'permanent_country',
        'permanent_state',
        'permanent_city',
        'permanent_place',
        'permanent_pin',
        'agent_commission',
        'agent_id',
        'terms_and_conditions',
        'admission_no',
        'admission_date',
        'commission',
        'photo',
        'reservation_date',
        'reservation_note',
        'application_date',
        'application_note',
        'cancel_date',
        'cancel_reason',
        'cancel_note',
        'picked_at',
        'received_at'
    ];

    protected $casts = [
        'dob' => 'date',
        'next_follow_up' => 'datetime',
        'picked_at' => 'datetime',
        'received_at' => 'datetime',
        'languages' => 'array',
    ];

    // Relationships
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function counselor(): RelationsBelongsTo
    {
        return $this->belongsTo(Counselor::class);
    }

    public function agent(): RelationsBelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(LeadEducation::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(LeadExam::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LeadPayment::class)->orderByDesc('payment_date')->orderByDesc('id');
    }

    public function contactLogs(): HasMany
    {
        return $this->hasMany(LeadContactLog::class);
    }

    public static function getStatuses()
    {
        return LeadStatus::getAllStatuses();
    }

    public function getStatusBadgeAttribute()
    {
        return LeadStatus::getBadge($this->status);
    }

    public function timeline()
    {
        return $this->hasMany(Timeline::class)->orderBy('event_date', 'desc');
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }
}