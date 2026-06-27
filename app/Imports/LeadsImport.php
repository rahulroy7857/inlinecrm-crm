<?php

namespace App\Imports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;
use App\Models\Timeline;

class LeadsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $source_id;
    protected $assign_to;
    protected $rowCount = 0;
    protected $successCount = 0;
    protected $errors = [];

    public function __construct($source_id, $assign_to)
    {
        $this->source_id = $source_id;
        $this->assign_to = $assign_to;
    }

    public function model(array $row)
    {
        $this->rowCount++;
        // Prevent accidental ID override
        unset($row['id']);

        $mobile = (string) $row['mobile'];
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        // Check all phone and email fields in a single query
        $existingLead = Lead::where(function($query) use ($mobile) {
            // Check all phone numbers
            $query->where('mobile', $mobile)
                ->orWhere('alternative_mobile', $mobile)
                ->orWhere('father_mobile', $mobile)
                ->orWhere('mother_mobile', $mobile)
                ->orWhere('guardian_mobile', $mobile);
        })
        ->orWhere(function($query) use ($row) {
            // Check all email addresses
            $query->where('personal_email', $row['email'])
                ->orWhere('father_email', $row['email'])
                ->orWhere('mother_email', $row['email'])
                ->orWhere('guardian_email', $row['email']);
        })
        ->first();
        
        $academicYearId = session('academic_year_id');
                
        // If no academic year in session, get the first active one
        if (empty($academicYearId)) {
            $defaultAcademicYear = \App\Models\AcademicYear::where('status', 'active')->first();
            $academicYearId = $defaultAcademicYear ? $defaultAcademicYear->id : null;
        }
        
        if(!$existingLead){

            if($row['country'] == 'India' || $row['country'] == '') {
                // Ensure mobile number is 10 digits for India
                if (strlen($mobile) == 10) {
                    $lead = new Lead();
                    $lead->fill([
                        'name' => trim($row['name']),
                        'mobile' => $row['mobile'],
                        'personal_email' => strtolower(trim($row['email'])),
                        'source_id' => $this->source_id,
                        'cource' => $row['course'],
                        'status' => 'New',
                        'next_follow_up' => now()->addSeconds(10),
                        'counselor_id' => $this->assign_to ?? NULL,
                        'state' => $row['state'] ?? null,
                        'country' => $row['country'] ?? 'India',
                        'academic_year_id' => $academicYearId,
                        'transfer_seen' => false,
                        'received_at' => now(),
                        'created_by' => auth()->guard('admin')->id()
                    ]);
                    $lead->save();
                    if($lead) {
                        $this->successCount++;
                    } 
                } else {
                    ActivityLogger::log(
                        "Invalid mobile number format",
                        'Excel upload',
                        auth()->guard('admin')->user(),
                        [
                            'mobile' => $row['mobile']
                        ]
                    );
                }
            } else {
                $lead = new Lead();
                $lead->fill([
                    'name' => trim($row['name']),
                    'mobile' => $row['mobile'],
                    'personal_email' => strtolower(trim($row['email'])),
                    'source_id' => $this->source_id,
                    'cource' => $row['course'],
                    'status' => 'New',
                    'next_follow_up' => now()->addSeconds(10),
                    'state' => $row['state'] ?? null,
                    'country' => $row['country'] ?? null,
                    'academic_year_id' => $academicYearId,
                    'transfer_seen' => false,
                    'counselor_id' => $this->assign_to ?? NULL,
                    'received_at' => now(),
                    'created_by' => auth()->guard('admin')->id()
                ]);
                $lead->save();
                Timeline::create([
                    'lead_id' => $lead->id,
                    'title' => 'New Lead Created',
                    'description' => "Lead created with name: {$lead->name}",
                    'event_type' => 'manual',
                    'performed_by' => auth()->id(),
                    'event_date' => now(),
                ]);
                if($lead) {
                    $this->successCount++;
                } 
            }   
        } else {
            ActivityLogger::log(
                "Duplicate lead found",
                'Excel upload',
                auth()->guard('admin')->user(),
                [
                    'mobile' => $row['mobile'],
                    'email' => $row['email'],
                    'matched_lead_id' => $existingLead->lead_id
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'required',
            'email' => 'required|max:255'
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