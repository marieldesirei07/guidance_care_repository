@extends('layouts.app')

@section('content')
@php
    $studentId = auth()->id();

    $appointments = \Illuminate\Support\Facades\DB::table('appointments')
        ->leftJoin('cases', 'appointments.case_id', '=', 'cases.id')
        ->select(
            'appointments.*',
            'cases.concern_type',
            'cases.case_number'
        )
        ->where('appointments.student_id', $studentId)
        ->orderBy('appointments.appointment_datetime', 'desc')
        ->limit(5)
        ->get();

    $requests = \Illuminate\Support\Facades\DB::table('counseling_requests')
        ->where('student_id', $studentId)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    // Teacher referrals about this student (by student_id or student_number)
    $teacherReferralsCount = 0;
    if (\Illuminate\Support\Facades\Schema::hasTable('referrals')) {
        $teacherReferralsCount = \Illuminate\Support\Facades\DB::table('referrals')
            ->where(function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
                $num = auth()->user()->student_number ?? null;
                if ($num) {
                    $q->orWhere('student_number', $num);
                }
            })
            ->where('status', 'Pending')
            ->count();
    }
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">
                        Student Portal
                    </p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">
                        Welcome, {{ auth()->user()->name }}
                    </h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                        Request counseling, request an appointment, or respond to teacher referrals.
                    </p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('student.request.create') }}"
                       style="display: inline-block; background: #2563eb; color: white; padding: 10px 16px;
                              border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none;">
                        + Request Counseling
                    </a>
                    <a href="{{ route('student.teacher_referrals') }}"
                       style="display: inline-block; background: #f7b900; color: white; padding: 10px 16px;
                              border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none;">
                        Teacher Referrals
                        @if($teacherReferralsCount > 0)
                            <span style="display: inline-block; background: #111827; color: white; font-size: 11px; font-weight: 600;
                                         padding: 2px 7px; border-radius: 9999px; margin-left: 4px;">
                                {{ $teacherReferralsCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- My Appointments --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 20px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">My Appointments</h3>
                <a href="{{ route('student.appointments') }}" style="font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500;">View All →</a>
            </div>
            @if($appointments->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No appointments yet.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date & Time</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Case</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Location</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $apt)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827;">
                                        {{ \Carbon\Carbon::parse($apt->appointment_datetime)->format('M d, Y h:i A') }}
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $apt->case_number ?? '—' }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $apt->location ?? 'Guidance Office' }}</td>
                                    <td style="padding: 14px 24px;">
                                        @php
                                            $st = $apt->status ?? 'Scheduled';
                                            $stBadge = $st === 'Completed'
                                                ? 'background:#dcfce7; color:#15803d;'
                                                : 'background:#dbeafe; color:#1d4ed8;';
                                        @endphp
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; {{ $stBadge }}">
                                            {{ $st }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <a href="{{ route('student.appointments.show', $apt->id) }}"
                                           style="display: inline-block; background: #2563eb; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500;">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- My Counseling Requests --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">My Counseling Requests</h3>
                <a href="{{ route('student.requests') }}" style="font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500;">View All →</a>
            </div>
            @if($requests->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No counseling requests yet.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Concern</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #374151;">{{ $req->concern_type }}</td>
                                    <td style="padding: 14px 24px; color: #6b7280;">{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}</td>
                                    <td style="padding: 14px 24px;">
                                        @php
                                            $st = $req->status ?? 'Pending';
                                            $stBadge = $st === 'Accepted'
                                                ? 'background:#dcfce7; color:#15803d;'
                                                : 'background:#fef3c7; color:#b45309;';
                                        @endphp
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; {{ $stBadge }}">
                                            {{ $st }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <a href="{{ route('student.requests.show', $req->id) }}"
                                           style="display: inline-block; background: #2563eb; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500;">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div style="margin-top: 48px; padding: 24px 0; text-align: center;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">
                GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant
            </p>
        </div>

    </div>
</div>
@endsection