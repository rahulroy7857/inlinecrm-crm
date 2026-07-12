<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>OTP Verification</title></head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.5;">
    <div style="max-width: 560px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 12px;">Email Verification OTP</h2>
        <p>Hi {{ $studentName }},</p>
        <p>Use this 6-digit OTP to complete your student registration:</p>
        <p style="font-size: 32px; letter-spacing: 8px; font-weight: bold; margin: 24px 0;">{{ $otp }}</p>
        <p>This OTP is valid for <strong>10 minutes</strong>.</p>
        <p style="color: #64748b; font-size: 13px;">If you did not request this, you can ignore this email.</p>
        <p style="margin-top: 24px;">— Inline CRM</p>
    </div>
</body>
</html>
