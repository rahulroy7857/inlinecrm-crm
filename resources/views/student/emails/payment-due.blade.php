<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Payment Due</title></head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.5;">
    <div style="max-width: 560px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 12px;">{{ $recipientType === 'counselor' ? 'Student Payment Due' : 'Payment Due Reminder' }}</h2>
        @if($recipientType === 'counselor')
            <p>Student <strong>{{ $student->name }}</strong> ({{ $student->lead_ref }}) has a due amount.</p>
        @else
            <p>Hi {{ $student->name }},</p>
            <p>This is a reminder for your pending fee payment.</p>
        @endif
        <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
            <tr><td style="padding: 8px 0; color: #64748b;">Fee Type</td><td style="padding: 8px 0;">{{ $purposeLabel }}</td></tr>
            <tr><td style="padding: 8px 0; color: #64748b;">Remaining</td><td style="padding: 8px 0;"><strong>₹{{ number_format($remaining, 2) }}</strong></td></tr>
            <tr><td style="padding: 8px 0; color: #64748b;">Due Date</td><td style="padding: 8px 0;">{{ $dueDate ? $dueDate->format('d M Y') : 'Not set' }}</td></tr>
        </table>
        @if($customMessage)
            <p style="background: #f8fafc; padding: 12px; border-radius: 8px;">{{ $customMessage }}</p>
        @endif
        @if($recipientType !== 'counselor')
            <p>Please contact the <strong>Accounts</strong> team to complete your payment. You can view transaction history in the <a href="{{ route('student.payment.index') }}">Student Portal</a>.</p>
        @endif
        <p style="margin-top: 24px;">— Inline CRM</p>
    </div>
</body>
</html>
