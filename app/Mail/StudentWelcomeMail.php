<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Student $student,
        public string $plainPassword
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Inline CRM — Your Student Portal Login',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'student.emails.welcome',
            with: [
                'loginUrl' => route('student.login'),
                'username' => $this->student->email,
                'password' => $this->plainPassword,
            ],
        );
    }
}
