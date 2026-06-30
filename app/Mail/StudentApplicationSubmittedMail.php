<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentApplicationSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Student $student,
        public string $applicationPdf,
        public string $receiptPdf,
        public string $recipientType = 'student'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'counselor'
            ? "Application Submitted — {$this->student->name} ({$this->student->lead_ref})"
            : 'Your Application Has Been Submitted — Inline CRM';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'student.emails.application-submitted',
            with: [
                'recipientType' => $this->recipientType,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->applicationPdf, 'application.pdf')
                ->withMime('application/pdf'),
            Attachment::fromData(fn () => $this->receiptPdf, 'payment-receipt.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
