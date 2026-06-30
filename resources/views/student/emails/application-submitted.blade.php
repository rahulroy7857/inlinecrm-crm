<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    @if($recipientType === 'counselor')
        <h2>Student Application Submitted</h2>
        <p>Dear Counselor,</p>
        <p><strong>{{ $student->name }}</strong> (Lead ID: {{ $student->lead_ref }}) has submitted their application.</p>
    @else
        <h2>Application Submitted Successfully</h2>
        <p>Dear {{ $student->name }},</p>
        <p>Your application has been submitted successfully. Please find attached your application form and payment receipt.</p>
    @endif

    <table style="margin: 20px 0; border-collapse: collapse;">
        <tr><td style="padding: 4px 16px 4px 0;"><strong>Lead ID:</strong></td><td>{{ $student->lead_ref }}</td></tr>
        <tr><td style="padding: 4px 16px 4px 0;"><strong>Course:</strong></td><td>{{ $student->course?->name ?? 'N/A' }}</td></tr>
        <tr><td style="padding: 4px 16px 4px 0;"><strong>Submitted On:</strong></td><td>{{ $student->submitted_at?->format('d M Y, h:i A') }}</td></tr>
        <tr><td style="padding: 4px 16px 4px 0;"><strong>Status:</strong></td><td>{{ $student->applicationStatusLabel() }}</td></tr>
    </table>

    <p>Best regards,<br>Inline CRM Team</p>
</body>
</html>
