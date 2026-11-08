<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Scheduled</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <p>Hello {{ $data['student_name'] ?? 'Student' }},</p>

    <p>Your guidance appointment has been scheduled.</p>

    <ul>
        <li><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($data['datetime'] ?? now())->format('M d, Y h:i A') }}</li>
        <li><strong>Location:</strong> {{ $data['location'] ?? 'Guidance Office' }}</li>
        @if(!empty($data['purpose']))
            <li><strong>Purpose:</strong> {{ $data['purpose'] }}</li>
        @endif
    </ul>

    <p>Please arrive on time. If you need to reschedule, contact the Guidance Office.</p>

    <p style="color: #6b7280; font-size: 13px;">
        GuidanceCare System • STI College Global City
    </p>
</body>
</html>