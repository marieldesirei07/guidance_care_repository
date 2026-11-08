<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Reminder</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
    <div style="max-width:560px; margin:32px auto; background:white; border-radius:12px; overflow:hidden; border:1px solid #e5e7eb;">
        <div style="background:#7c3aed; padding:20px 24px;">
            <p style="margin:0; color:white; font-size:13px; font-weight:600; letter-spacing:0.04em;">GUIDANCECARE</p>
            <h1 style="margin:6px 0 0 0; color:white; font-size:20px;">Appointment Reminder</h1>
        </div>
        <div style="padding:24px;">
            <p style="margin:0 0 16px 0; color:#374151; font-size:15px;">Hello {{ $appointment->student_name ?? 'Student' }},</p>
            <p style="margin:0 0 20px 0; color:#4b5563; font-size:14px; line-height:1.6;">
                This is a reminder that you have a guidance appointment <strong>tomorrow</strong>.
            </p>
            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:16px; margin-bottom:20px;">
                <p style="margin:0 0 8px 0; font-size:13px; color:#6b7280;"><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y h:i A') }}</p>
                <p style="margin:0 0 8px 0; font-size:13px; color:#6b7280;"><strong>Location:</strong> {{ $appointment->location ?? 'Guidance Office' }}</p>
                <p style="margin:0; font-size:13px; color:#6b7280;"><strong>Purpose:</strong> {{ $appointment->purpose ?? 'Counseling session' }}</p>
            </div>
            <p style="margin:0; color:#9ca3af; font-size:12px;">
                Please arrive on time. Contact the Guidance Office if you need to reschedule.
            </p>
        </div>
        <div style="padding:14px 24px; background:#f9fafb; border-top:1px solid #e5e7eb; text-align:center;">
            <p style="margin:0; font-size:11px; color:#9ca3af;">GuidanceCare • STI College Global City</p>
        </div>
    </div>
</body>
</html>