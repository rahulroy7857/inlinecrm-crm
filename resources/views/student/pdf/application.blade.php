<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Student Application Form</h1>
    <p class="meta">Lead ID: {{ $student->lead_ref }} | Generated: {{ now()->format('d M Y, h:i A') }}</p>

    <table>
        <tr><th colspan="2">Personal Information</th></tr>
        <tr><td width="35%">Name</td><td>{{ $student->name }}</td></tr>
        <tr><td>Email</td><td>{{ $student->email }}</td></tr>
        <tr><td>Mobile</td><td>{{ $student->mobile }}</td></tr>
        <tr><td>Country / State</td><td>{{ $student->country }} / {{ $student->state }}</td></tr>
        <tr><td>Course</td><td>{{ $student->course?->name ?? 'N/A' }}</td></tr>
        <tr><td>Gender</td><td>{{ $student->gender ?? 'N/A' }}</td></tr>
        <tr><td>Date of Birth</td><td>{{ $student->dob?->format('d M Y') ?? 'N/A' }}</td></tr>
        <tr><td>Aadhar</td><td>{{ $student->aadhar ?? 'N/A' }}</td></tr>
        <tr><th colspan="2">Parent / Guardian</th></tr>
        <tr><td>Father's Name</td><td>{{ $student->father_name ?? 'N/A' }}</td></tr>
        <tr><td>Father's Occupation</td><td>{{ $student->father_occupation ?? 'N/A' }}</td></tr>
        <tr><td>Mother's Name</td><td>{{ $student->mother_name ?? 'N/A' }}</td></tr>
        <tr><td>Mother's Occupation</td><td>{{ $student->mother_occupation ?? 'N/A' }}</td></tr>
        <tr><td>Guardian</td><td>{{ $student->guardian_name ?? 'N/A' }} ({{ $student->relation ?? 'N/A' }})</td></tr>
        <tr><th colspan="2">Address</th></tr>
        <tr><td>Present Address</td><td>{{ $student->present_address ?? 'N/A' }}, {{ $student->present_city ?? '' }} {{ $student->present_pin ?? '' }}</td></tr>
        <tr><td>Permanent Address</td><td>{{ $student->permanent_address ?? 'N/A' }}</td></tr>
        <tr><th colspan="2">Application</th></tr>
        <tr><td>Status</td><td>{{ $student->applicationStatusLabel() }}</td></tr>
        <tr><td>Counselor</td><td>{{ $student->counselor?->name ?? 'Not Assigned' }}</td></tr>
        <tr><td>Submitted On</td><td>{{ $student->submitted_at?->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A') }}</td></tr>
    </table>
</body>
</html>
