@extends('layouts.app')

@section('content')
@php
    $referrals = \Illuminate\Support\Facades\DB::table('referrals')
        ->leftJoin('users as teachers', 'referrals.teacher_id', '=', 'teachers.id')
        ->select(
            'referrals.*',
            'teachers.name as teacher_name',
            \Illuminate\Support\Facades\DB::raw("COALESCE(referrals.student_name, (SELECT name FROM users WHERE users.id = referrals.student_id)) as display_student_name"),
            \Illuminate\Support\Facades\DB::raw("COALESCE(referrals.student_number, (SELECT student_number FROM users WHERE users.id = referrals.student_id)) as display_student_number")
        )
        ->where('referrals.status', 'Pending')
        ->orderBy('referrals.created_at', 'desc')
        ->get();

    $requests = \Illuminate\Support\Facades\DB::table('counseling_requests')
        ->where('status', 'Pending')
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <p style="font-size: 11px; font-weight: 600; color: #7c3aed; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 6px 0;">
                        Guidance Office
                    </p>
                    <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">
                        Pending Referrals & Requests
                    </h1>
                    <p style="font-size: 14px; color: #6b7280; margin: 0;">
                        Convert pending items into official cases
                    </p>
                </div>
                <a href="{{ route('counselor.dashboard') }}"
                   style="display: inline-block; background: #f3f4f6; color: #4b5563; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 12px 16px; background: #dcfce7; color: #166534; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="margin-bottom: 16px; padding: 12px 16px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 14px;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Teacher Referrals --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Teacher Referrals</h3>
            </div>

            @if($referrals->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No pending teacher referrals.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Student</th>
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Teacher</th>
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Concern</th>
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Urgency</th>
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Date</th>
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $referral)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #374151;">
                                        {{ $referral->display_student_name ?? 'Unknown' }}
                                        @if(!empty($referral->display_student_number))
                                            <br><span style="font-size: 12px; color: #9ca3af;">{{ $referral->display_student_number }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $referral->teacher_name ?? '—' }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $referral->concern_type }}</td>
                                    <td style="padding: 14px 24px;">
                                        @php
                                            $u = $referral->urgency ?? 'Medium';
                                            $uStyle = match($u) {
                                                'High' => 'background:#fee2e2; color:#991b1b;',
                                                'Low' => 'background:#dcfce7; color:#166534;',
                                                default => 'background:#fef3c7; color:#92400e;',
                                            };
                                        @endphp
                                        <span style="display:inline-block; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:500; {{ $uStyle }}">
                                            {{ $u }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 24px; color: #6b7280;">
                                        {{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y') }}
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <a href="{{ route('counselor.pending.referral', $referral->id) }}"
                                           style="display:inline-block; background:#2563eb; color:white; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:500; text-decoration:none;">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Student Counseling Requests --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Student Counseling Requests</h3>
            </div>

            @if($requests->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No pending student requests.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Student</th>
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Concern</th>
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Preferred Time</th>
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Date</th>
                                <th style="padding: 12px 24px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #374151;">
                                        {{ $request->student_name ?? 'Unknown' }}
                                        @if(!empty($request->student_number))
                                            <br><span style="font-size: 12px; color: #9ca3af;">{{ $request->student_number }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $request->concern_type }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">
                                        @if(!empty($request->preferred_datetime))
                                            {{ \Carbon\Carbon::parse($request->preferred_datetime)->format('M d, Y h:i A') }}
                                        @else
                                            <span style="color: #9ca3af;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #6b7280;">
                                        {{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <a href="{{ route('counselor.pending.request', $request->id) }}"
                                           style="display:inline-block; background:#2563eb; color:white; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:500; text-decoration:none;">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div style="margin-top: 48px; padding: 20px 0; text-align: center;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">
                GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant
            </p>
        </div>

    </div>
</div>
@endsection