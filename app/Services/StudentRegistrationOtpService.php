<?php

namespace App\Services;

use App\Mail\StudentOtpMail;
use App\Mail\StudentWelcomeMail;
use App\Models\Lead;
use App\Models\Student;
use App\Models\StudentOtp;
use App\Services\StudentFeeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class StudentRegistrationOtpService
{
    public function issue(Lead $lead, array $payload): array
    {
        $otp = (string) random_int(100000, 999999);

        StudentOtp::where('email', $payload['email'])
            ->whereNull('verified_at')
            ->delete();

        $record = StudentOtp::create([
            'email' => $payload['email'],
            'lead_ref' => $lead->lead_id,
            'otp_hash' => Hash::make($otp),
            'payload' => $payload,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        Mail::to($payload['email'])->send(new StudentOtpMail($otp, $payload['name'] ?? $lead->name));

        return [
            'otp_id' => $record->id,
            'email' => $payload['email'],
            'expires_at' => $record->expires_at,
        ];
    }

    public function verify(int $otpId, string $code): Student
    {
        $record = StudentOtp::findOrFail($otpId);

        if ($record->isVerified()) {
            throw new \RuntimeException('This OTP has already been used.');
        }

        if ($record->isExpired()) {
            throw new \RuntimeException('OTP has expired. Please request a new one.');
        }

        if ($record->attempts >= 5) {
            throw new \RuntimeException('Too many invalid attempts. Please register again.');
        }

        if (!Hash::check($code, $record->otp_hash)) {
            $record->increment('attempts');
            throw new \RuntimeException('Invalid OTP. Please try again.');
        }

        $payload = $record->payload;
        $lead = Lead::where('lead_id', $record->lead_ref)->firstOrFail();

        if (Student::where('lead_id', $lead->id)->exists()) {
            throw new \RuntimeException('An account already exists for this student.');
        }

        $plainPassword = $payload['password'];

        $planKey = $lead->registration_fee_plan ?: null;
        $registrationFee = $planKey
            ? (StudentFeeService::registrationPlanTotal($planKey) ?? 0)
            : 0;

        $student = DB::transaction(function () use ($lead, $payload, $plainPassword, $record, $planKey, $registrationFee) {
            $student = Student::create([
                'lead_id' => $lead->id,
                'lead_ref' => $lead->lead_id,
                'counselor_id' => $lead->counselor_id,
                'name' => $payload['name'],
                'email' => $payload['email'],
                'mobile' => $payload['mobile'],
                'country' => $payload['country'],
                'state' => $payload['state'],
                'course_id' => $payload['course_id'],
                'password' => Hash::make($plainPassword),
                'email_verified_at' => now(),
                'application_status' => 'registered',
                'registration_fee_plan' => $planKey,
                'registration_fee' => $registrationFee,
                'status' => true,
            ]);

            $record->update(['verified_at' => now()]);

            return $student;
        });

        try {
            Mail::to($student->email)->send(new StudentWelcomeMail($student, $plainPassword));
        } catch (\Throwable $e) {
            report($e);
        }

        return $student;
    }
}
