@extends('layouts.app')

@section('content')
<div style="padding-top:32px; padding-bottom:40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:28px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; gap:12px; flex-wrap:wrap;">
                <div>
                    <p style="margin:0 0 6px 0; font-size:11px; font-weight:600; color:#2563eb; text-transform:uppercase;">Referral Details</p>
                    <h1 style="margin:0 0 4px 0; font-size:22px; font-weight:700; color:#111827;">{{ $referral->student_name }}</h1>
                    <p style="margin:0; font-size:14px; color:#6b7280;">
                    @if($referral->student_number) {{ $referral->student_number }} @endif
                    @if(!empty($referral->year_section)) · {{ $referral->year_section }} @endif
                </p>
                </div>
                <a href="{{ route('teacher.referrals.index') }}"
                style="display:inline-block; padding:8px 14px; background:#f3f4f6; color:#374151; border-radius:8px; font-size:13px; font-weight:500; text-decoration:none; border:1px solid #e5e7eb;">
                    ← Back to list
                </a>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                <div>
                    <p style="margin:0 0 4px 0; font-size:12px; color:#6b7280;">Concern Type</p>
                    <p style="margin:0; font-size:14px; font-weight:500; color:#111827;">{{ $referral->concern_type }}</p>
                </div>
                <div>
                    <p style="margin:0 0 4px 0; font-size:12px; color:#6b7280;">Urgency</p>
                    <p style="margin:0; font-size:14px; font-weight:500; color:#111827;">{{ $referral->urgency }}</p>
                </div>
                <div>
                    <p style="margin:0 0 4px 0; font-size:12px; color:#6b7280;">Status</p>
                    <p style="margin:0; font-size:14px; font-weight:500; color:#111827;">{{ $referral->status }}</p>
                </div>
                <div>
                    <p style="margin:0 0 4px 0; font-size:12px; color:#6b7280;">Submitted</p>
                    <p style="margin:0; font-size:14px; font-weight:500; color:#111827;">{{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y h:i A') }}</p>
                </div>
            </div>

            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:16px;">
                <p style="margin:0 0 6px 0; font-size:12px; color:#6b7280;">Description</p>
                <p style="margin:0; font-size:14px; color:#374151; line-height:1.6;">{{ $referral->description }}</p>
            </div>
        </div>

        <div style="margin-top:32px; text-align:center;">
            <p style="font-size:12px; color:#9ca3af; margin:0;">GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant</p>
        </div>
    </div>
</div>
@endsection