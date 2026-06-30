<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\StudentApplicationSubmittedMail;
use App\Services\StudentDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    public function submit(Request $request, StudentDocumentService $documents)
    {
        $student = Auth::guard('student')->user();

        if (!$student->isProfileComplete()) {
            return redirect()->route('student.profile.complete')
                ->with('error', 'Please complete your profile before submitting.');
        }

        if (!$student->hasPaid()) {
            return redirect()->route('student.payment.index')
                ->with('error', 'Please complete payment before submitting your application.');
        }

        if ($student->application_status === 'submitted') {
            return redirect()->route('student.dashboard')
                ->with('info', 'Your application has already been submitted.');
        }

        $student->update([
            'application_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $student->load(['course', 'counselor', 'lead']);

        $applicationPdf = $documents->applicationPdf($student);
        $receiptPdf = $documents->receiptPdf($student);

        $recipients = collect([$student->email]);

        if ($student->counselor?->email) {
            $recipients->push($student->counselor->email);
        }

        foreach ($recipients->unique() as $email) {
            try {
                Mail::to($email)->send(new StudentApplicationSubmittedMail(
                    $student,
                    $applicationPdf,
                    $receiptPdf,
                    $email === $student->email ? 'student' : 'counselor'
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'Application submitted! PDF copies have been emailed to you and your counselor.');
    }
}
