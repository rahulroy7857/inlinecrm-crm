<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Payment Receipt</title></head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.5;">
    <div style="max-width: 560px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 12px;">Payment Receipt</h2>
        <p>Hi {{ $payment->student->name }},</p>
        <p>We received your payment. Details:</p>
        <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
            <tr><td style="padding: 8px 0; color: #64748b;">Student</td><td style="padding: 8px 0;">{{ $payment->student->name }}</td></tr>
            <tr><td style="padding: 8px 0; color: #64748b;">Lead ID</td><td style="padding: 8px 0;">{{ $payment->student->lead_ref }}</td></tr>
            <tr><td style="padding: 8px 0; color: #64748b;">Purpose</td><td style="padding: 8px 0;">{{ $purposeLabel }}</td></tr>
            <tr><td style="padding: 8px 0; color: #64748b;">Amount</td><td style="padding: 8px 0;"><strong>₹{{ number_format($payment->amount, 2) }}</strong></td></tr>
            <tr><td style="padding: 8px 0; color: #64748b;">Transaction ID</td><td style="padding: 8px 0;">{{ $payment->transaction_id }}</td></tr>
            <tr><td style="padding: 8px 0; color: #64748b;">Counselor</td><td style="padding: 8px 0;">{{ $payment->counselor?->name ?? '—' }}</td></tr>
            <tr><td style="padding: 8px 0; color: #64748b;">Paid At</td><td style="padding: 8px 0;">{{ optional($payment->paid_at)->format('d M Y, h:i A') }}</td></tr>
        </table>
        <p style="margin-top: 24px;">— Inline CRM</p>
    </div>
</body>
</html>
