@extends('layouts.app')

@section('content')
<div style="padding-top:32px; padding-bottom:40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:24px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <p style="margin:0 0 6px 0; font-size:11px; font-weight:600; color:#2563eb; text-transform:uppercase;">Teacher Portal</p>
                <h1 style="margin:0 0 4px 0; font-size:22px; font-weight:700; color:#111827;">All My Referrals</h1>
                <p style="margin:0; font-size:14px; color:#6b7280;">Complete list of students you referred to Guidance.</p>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('teacher.dashboard') }}" style="padding:8px 14px; background:#f3f4f6; color:#374151; border-radius:8px; font-size:13px; text-decoration:none; font-weight:500;">← Back to Dashboard</a>
                <a href="{{ route('teacher.referrals.create') }}" style="padding:8px 14px; background:#2563eb; color:white; border-radius:8px; font-size:13px; text-decoration:none; font-weight:500;">+ Refer a Student</a>
            </div>
        </div>

        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="background:#f9fafb; text-align:left;">
                            <th style="padding:12px 24px; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase;">Student</th>
                            <th style="padding:12px 24px; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase;">Concern</th>
                            <th style="padding:12px 24px; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase;">Urgency</th>
                            <th style="padding:12px 24px; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase;">Status</th>
                            <th style="padding:12px 24px; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase;">Date</th>
                            <th style="padding:12px 24px; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $ref)
                            <tr style="border-top:1px solid #f3f4f6;">
                                <td style="padding:14px 24px;">
                                    <div style="font-weight:500; color:#111827;">{{ $ref->student_name }}</div>
                                    @if($ref->student_number)
                                        <div style="font-size:12px; color:#6b7280;">{{ $ref->student_number }}</div>
                                    @endif
                                </td>
                                <td style="padding:14px 24px; color:#374151;">{{ $ref->concern_type }}</td>
                                <td style="padding:14px 24px; color:#374151;">{{ $ref->urgency }}</td>
                                <td style="padding:14px 24px;">
                                    @php
                                        $statusStyle = match($ref->status) {
                                            'Pending' => 'background:#fef3c7; color:#b45309;',
                                            'Accepted', 'In Progress' => 'background:#d1fae5; color:#047857;',
                                            default => 'background:#e5e7eb; color:#374151;',
                                        };
                                    @endphp
                                    <span style="display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:500; {{ $statusStyle }}">
                                        {{ $ref->status }}
                                    </span>
                                </td>
                                <td style="padding:14px 24px; color:#6b7280;">{{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}</td>
                                <td style="padding:14px 24px; text-align:right;">
                                    <a href="{{ route('teacher.referrals.show', $ref->id) }}"
                                       style="display:inline-block; background:#2563eb; color:white; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:500; text-decoration:none;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:32px; text-align:center; color:#6b7280;">No referrals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:40px; text-align:center;">
            <p style="font-size:12px; color:#9ca3af; margin:0;">GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant</p>
        </div>
    </div>
</div>
@endsection