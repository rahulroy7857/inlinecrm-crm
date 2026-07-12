<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Welcome to Inline CRM Student Portal</h2>

    <p>Dear {{ $student->name }},</p>

    <p>Your student account has been created successfully. You can now log in to complete your profile and submit your application.</p>

    <table style="margin: 20px 0; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 16px 8px 0;"><strong>Login URL:</strong></td>
            <td><a href="{{ $loginUrl }}">{{ $loginUrl }}</a></td>
        </tr>
        <tr>
            <td style="padding: 8px 16px 8px 0;"><strong>Username (Email):</strong></td>
            <td>{{ $username }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 16px 8px 0;"><strong>Password:</strong></td>
            <td>{{ $password }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 16px 8px 0;"><strong>Lead ID:</strong></td>
            <td>{{ $student->lead_ref }}</td>
        </tr>
    </table>

    <p>After logging in, please complete your profile, pay the application fee, and submit your application.</p>

    <p>Best regards,<br>Inline CRM Team</p>
</body>
</html>
