<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $studentName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Student Registration OTP — Inline CRM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'student.emails.otp',
        );
    }
}
