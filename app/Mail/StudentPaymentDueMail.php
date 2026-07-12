<?php

namespace App\Mail;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentPaymentDueMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Student $student,
        public string $purposeLabel,
        public float $remaining,
        public ?Carbon $dueDate,
        public ?string $customMessage = null,
        public string $recipientType = 'student'
    ) {}

    public function envelope(): Envelope
    {
        $prefix = $this->recipientType === 'counselor' ? 'Student Due Reminder' : 'Payment Due Reminder';

        return new Envelope(
            subject: "{$prefix} — {$this->purposeLabel}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'student.emails.payment-due',
        );
    }
}
