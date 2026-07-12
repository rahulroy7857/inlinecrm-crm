<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Services\ActivityLogger;
use App\Models\Timeline;

class LeadsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $source_id;
    protected $assign_to;
    protected $rowCount = 0;
    protected $successCount = 0;
    protected $errors = [];

    public function __construct($source_id, $assign_to = null)
    {
        $this->source_id = $source_id;
        $this->assign_to = $assign_to;
    }

    public function model(array $row)
    {
        $this->rowCount++;
        unset($row['id']);

        $mobile = (string) ($row['mobile'] ?? '');
        $mobileDigits = preg_replace('/[^0-9]/', '', $mobile);
        $email = strtolower(trim($row['email'] ?? ''));

        $existingLead = Lead::findDuplicateByContact($mobile, $email ?: null);
        $academicYearId = $this->resolveAcademicYearId();

        if ($existingLead) {
            $leadData = $this->buildLeadData($row, $academicYearId, $email, true);
            $existingLead->update($leadData);

            Timeline::create([
                'lead_id' => $existingLead->id,
                'title' => 'Lead Updated',
                'description' => "Lead updated via bulk upload: {$existingLead->name} ({$existingLead->lead_id})",
                'event_type' => 'manual',
                ...Timeline::performerAttributes(auth()->guard('admin')->user() ?? auth()->guard('counselor')->user()),
                'event_date' => now(),
            ]);

            ActivityLogger::log(
                "Updated duplicate lead via Excel upload",
                'Excel upload',
                auth()->guard('admin')->user() ?? auth()->guard('counselor')->user(),
                [
                    'mobile' => $mobile,
                    'email' => $email,
                    'matched_lead_id' => $existingLead->lead_id,
                ]
            );

            $this->successCount++;
            return null;
        }

        $country = trim($row['country'] ?? '') ?: 'India';

        if ($country === 'India' && strlen($mobileDigits) !== 10) {
            ActivityLogger::log(
                "Invalid mobile number format",
                'Excel upload',
                auth()->guard('admin')->user() ?? auth()->guard('counselor')->user(),
                ['mobile' => $mobile]
            );
            return null;
        }

        $lead = new Lead();
        $lead->fill([
            ...$this->buildLeadData($row, $academicYearId, $email, false),
            'status' => 'New',
            'next_follow_up' => now()->addSeconds(10),
            'transfer_seen' => false,
            'received_at' => now(),
        ]);
        $lead->save();

        Timeline::create([
            'lead_id' => $lead->id,
            'title' => 'New Lead Created',
            'description' => "Lead created with name: {$lead->name}",
            'event_type' => 'manual',
            ...Timeline::performerAttributes(auth()->guard('admin')->user() ?? auth()->guard('counselor')->user()),
            'event_date' => now(),
        ]);

        $this->successCount++;
        return null;
    }

    protected function buildLeadData(array $row, ?int $academicYearId, string $email, bool $isUpdate): array
    {
        $data = [
            'name' => trim($row['name']),
            'mobile' => $row['mobile'],
            'source_id' => $this->source_id,
            'counselor_id' => $this->assign_to ?? null,
            'academic_year_id' => $academicYearId,
        ];

        if ($email !== '') {
            $data['personal_email'] = $email;
        }

        if (!empty(trim($row['country'] ?? ''))) {
            $data['country'] = trim($row['country']);
        } elseif (!$isUpdate) {
            $data['country'] = 'India';
        }

        if (!empty(trim($row['state'] ?? ''))) {
            $data['state'] = trim($row['state']);
        }

        $courseId = $this->resolveCourseId($row['course'] ?? null);
        if ($courseId) {
            $data['course_id'] = $courseId;
        }

        return $data;
    }

    protected function resolveAcademicYearId(): ?int
    {
        $academicYearId = session('academic_year_id');

        if (empty($academicYearId)) {
            $defaultAcademicYear = \App\Models\AcademicYear::where('status', 'active')->first();
            $academicYearId = $defaultAcademicYear ? $defaultAcademicYear->id : null;
        }

        return $academicYearId;
    }

    protected function resolveCourseId(?string $courseName): ?int
    {
        $courseName = trim($courseName ?? '');
        if ($courseName === '') {
            return null;
        }

        return Course::where('name', $courseName)->value('id');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'required',
            'email' => 'nullable|email|max:255',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
