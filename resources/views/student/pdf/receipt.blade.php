<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f5f5f5; text-align: left; }
        .amount { font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Payment Receipt</h1>
    <p>Inline CRM — Student Application Fee</p>

    <table>
        <tr><th>Receipt No.</th><td>{{ $student->payment_reference ?? 'N/A' }}</td></tr>
        <tr><th>Lead ID</th><td>{{ $student->lead_ref }}</td></tr>
        <tr><th>Student Name</th><td>{{ $student->name }}</td></tr>
        <tr><th>Email</th><td>{{ $student->email }}</td></tr>
        <tr><th>Course</th><td>{{ $student->course?->name ?? 'N/A' }}</td></tr>
        <tr><th>Amount Paid</th><td class="amount">₹{{ number_format($student->payment_amount ?? 0, 2) }}</td></tr>
        <tr><th>Payment Date</th><td>{{ $student->paid_at?->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A') }}</td></tr>
        <tr><th>Status</th><td>Paid</td></tr>
    </table>

    <p style="margin-top: 24px; font-size: 10px; color: #666;">This is a computer-generated receipt. No signature required.</p>
</body>
</html>
