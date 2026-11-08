<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Referral Accepted</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
    <div style="max-width:560px; margin:32px auto; background:white; border-radius:12px; overflow:hidden; border:1px solid #e5e7eb;">
        <div style="background:#059669; padding:20px 24px;">
            <p style="margin:0; color:white; font-size:13px; font-weight:600;">GUIDANCECARE</p>
            <h1 style="margin:6px 0 0 0; color:white; font-size:20px;">Referral Accepted</h1>
        </div>
        <div style="padding:24px;">
            <p style="margin:0 0 16px 0; color:#374151; font-size:15px;">Hello Counselor,</p>
            <p style="margin:0 0 20px 0; color:#4b5563; font-size:14px; line-height:1.6;">
                A student has <strong>accepted</strong> a teacher referral. You can now convert it into a case.
            </p>
            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:16px;">
                <p style="margin:0 0 8px 0; font-size:13px; color:#6b7280;"><strong>Student:</strong> {{ $referral->student_name ?? 'Student' }}</p>
                <p style="margin:0 0 8px 0; font-size:13px; color:#6b7280;"><strong>Concern:</strong> {{ $referral->concern_type }}</p>
                <p style="margin:0; font-size:13px; color:#6b7280;"><strong>Teacher:</strong> {{ $referral->teacher_name ?? 'Teacher' }}</p>
            </div>
        </div>
        <div style="padding:14px 24px; background:#f9fafb; border-top:1px solid #e5e7eb; text-align:center;">
            <p style="margin:0; font-size:11px; color:#9ca3af;">GuidanceCare • STI College Global City</p>
        </div>
    </div>
</body>
</html>