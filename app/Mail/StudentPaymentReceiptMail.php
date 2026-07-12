<?php

namespace App\Mail;

use App\Models\StudentPayment;
use App\Services\StudentFeeService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentPaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public StudentPayment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Receipt — Inline CRM',
        );
    }

    public function content(): Content
    {
        $labels = StudentFeeService::purposeLabels();

        return new Content(
            view: 'student.emails.payment-receipt',
            with: [
                'purposeLabel' => $labels[$this->payment->purpose] ?? $this->payment->purpose,
            ],
        );
    }
}
