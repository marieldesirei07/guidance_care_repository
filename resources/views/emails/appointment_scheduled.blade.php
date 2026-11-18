<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Scheduled</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
    <div style="max-width:560px; margin:32px auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
        <div style="padding:28px 32px;">
            <p style="margin:0 0 8px 0; font-size:11px; font-weight:700; letter-spacing:0.06em; color:#2563eb; text-transform:uppercase;">
                GuidanceCare
            </p>
            <h1 style="margin:0 0 16px 0; font-size:22px; font-weight:700; color:#111827;">
                Appointment Scheduled
            </h1>
            <p style="margin:0 0 16px 0; font-size:14px; line-height:1.6; color:#374151;">
                Hello {{ $data['student_name'] ?? 'Student' }},
            </p>
            <p style="margin:0 0 20px 0; font-size:14px; line-height:1.6; color:#374151;">
                An appointment has been scheduled for you with the Guidance Office.
            </p>

            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:16px 18px; margin-bottom:20px;">
                <p style="margin:0 0 10px 0; font-size:13px; color:#6b7280;">
                    <strong style="color:#111827;">Date &amp; Time</strong><br>
                    {{ \Carbon\Carbon::parse($data['datetime'])->format('M d, Y h:i A') }}
                </p>
                <p style="margin:0 0 10px 0; font-size:13px; color:#6b7280;">
                    <strong style="color:#111827;">Location</strong><br>
                    {{ $data['location'] ?? 'Guidance Office' }}
                </p>
                @if(!empty($data['purpose']))
                <p style="margin:0; font-size:13px; color:#6b7280;">
                    <strong style="color:#111827;">Purpose</strong><br>
                    {{ $data['purpose'] }}
                </p>
                @endif
            </div>

            <p style="margin:0 0 8px 0; font-size:13px; line-height:1.6; color:#6b7280;">
                If you need to reschedule, please contact the Guidance Office.
            </p>
            <p style="margin:24px 0 0 0; font-size:12px; color:#9ca3af; line-height:1.5;">
                GuidanceCare • STI College Global City<br>
                This email does not include confidential counseling details.
            </p>
        </div>
    </div>
</body>
</html>