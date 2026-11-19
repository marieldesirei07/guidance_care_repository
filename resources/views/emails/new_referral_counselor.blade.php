<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Teacher Referral</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
    <div style="max-width:560px; margin:32px auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
        <div style="padding:28px 32px;">
            <p style="margin:0 0 8px 0; font-size:11px; font-weight:700; letter-spacing:0.06em; color:#2563eb; text-transform:uppercase;">
                GuidanceCare
            </p>
            <h1 style="margin:0 0 16px 0; font-size:22px; font-weight:700; color:#111827;">
                New Teacher Referral
            </h1>
            <p style="margin:0 0 16px 0; font-size:14px; line-height:1.6; color:#374151;">
                Hello {{ $data['counselor_name'] ?? 'Counselor' }},
            </p>
            <p style="margin:0 0 20px 0; font-size:14px; line-height:1.6; color:#374151;">
                A teacher submitted a new guidance referral. It is waiting in your Pending Queue.
            </p>

            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:16px 18px; margin-bottom:20px;">
                <p style="margin:0 0 10px 0; font-size:13px; color:#6b7280;">
                    <strong style="color:#111827;">Student</strong><br>
                    {{ $data['student_name'] ?? '—' }}@if(!empty($data['student_number'])) · {{ $data['student_number'] }}@endif
                </p>
                <p style="margin:0 0 10px 0; font-size:13px; color:#6b7280;">
                    <strong style="color:#111827;">Referred by</strong><br>
                    {{ $data['teacher_name'] ?? '—' }}
                </p>
                <p style="margin:0 0 10px 0; font-size:13px; color:#6b7280;">
                    <strong style="color:#111827;">Concern Type</strong><br>
                    {{ $data['concern_type'] ?? '—' }}
                </p>
                <p style="margin:0; font-size:13px; color:#6b7280;">
                    <strong style="color:#111827;">Urgency</strong><br>
                    {{ $data['urgency'] ?? '—' }}
                </p>
            </div>

            @if(!empty($data['pending_url']))
            <p style="margin:0 0 8px 0;">
                <a href="{{ $data['pending_url'] }}" style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; padding:10px 18px; border-radius:8px; font-size:14px; font-weight:600;">Open Pending Queue</a>
            </p>
            @endif

            <p style="margin:24px 0 0 0; font-size:12px; color:#9ca3af; line-height:1.5;">
                GuidanceCare • STI College Global City<br>
                The teacher's description is not included in this email. Read it in GuidanceCare.
            </p>
        </div>
    </div>
</body>
</html>
