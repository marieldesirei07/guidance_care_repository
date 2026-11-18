@extends('layouts.app')

@section('content')
@php
    $teacherId = auth()->id();

    $referrals = \Illuminate\Support\Facades\DB::table('referrals')
        ->leftJoin('users', 'referrals.student_id', '=', 'users.id')
        ->select(
            'referrals.*',
            \Illuminate\Support\Facades\DB::raw("COALESCE(users.name, referrals.student_name, 'Unknown') as student_name"),
            \Illuminate\Support\Facades\DB::raw("COALESCE(users.student_number, referrals.student_number, '') as student_number")
        )
        ->where('referrals.teacher_id', $teacherId)
        ->orderBy('referrals.created_at', 'desc')
        ->limit(10)
        ->get();

    $total    = \Illuminate\Support\Facades\DB::table('referrals')->where('teacher_id', $teacherId)->count();
    $pending  = \Illuminate\Support\Facades\DB::table('referrals')->where('teacher_id', $teacherId)->where('status', 'Pending')->count();
    $accepted = \Illuminate\Support\Facades\DB::table('referrals')->where('teacher_id', $teacherId)->whereIn('status', ['Accepted', 'In Progress', 'Closed'])->count();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:24px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div>
                <p style="margin:0 0 6px 0; font-size:11px; font-weight:600; color:#2563eb; text-transform:uppercase; letter-spacing:0.05em;">Teacher Portal</p>
                <h1 style="margin:0 0 4px 0; font-size:24px; font-weight:700; color:#111827;">Welcome, {{ auth()->user()->name }}</h1>
                <p style="margin:0; font-size:14px; color:#6b7280;">Refer students to the Guidance Office and track your submissions.</p>
            </div>
            <a href="{{ route('teacher.referrals.create') }}"
               style="display:inline-block; background:#2563eb; color:white; padding:10px 18px; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none;">
                + Refer a Student
            </a>
        </div>

        @if(session('success'))
            <div style="margin-bottom:16px; padding:12px 16px; background:#d1fae5; color:#065f46; border-radius:8px; font-size:14px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-bottom:20px;">
            <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:20px;">
                <p style="margin:0 0 8px 0; font-size:13px; color:#6b7280;">Total Referrals</p>
                <p style="margin:0; font-size:28px; font-weight:700; color:#111827;">{{ $total }}</p>
            </div>
            <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:20px;">
                <p style="margin:0 0 8px 0; font-size:13px; color:#6b7280;">Pending</p>
                <p style="margin:0; font-size:28px; font-weight:700; color:#d97706;">{{ $pending }}</p>
            </div>
            <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:20px;">
                <p style="margin:0 0 8px 0; font-size:13px; color:#6b7280;">Accepted</p>
                <p style="margin:0; font-size:28px; font-weight:700; color:#059669;">{{ $accepted }}</p>
            </div>
        </div>

        {{-- My Referrals --}}
        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
            <div style="padding:16px 24px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f3f4f6;">
                <h2 style="margin:0; font-size:16px; font-weight:600; color:#111827;">My Referrals</h2>
                <a href="{{ route('teacher.referrals.index') }}" style="font-size:13px; color:#2563eb; text-decoration:none; font-weight:500;">
                    View All →
                </a>
            </div>

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
                                            'Closed' => 'background:#e5e7eb; color:#374151;',
                                            default => 'background:#e5e7eb; color:#374151;',
                                        };
                                    @endphp
                                    <span style="display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:500; {{ $statusStyle }}">
                                        {{ $ref->status }}
                                    </span>
                                </td>
                                <td style="padding:14px 24px; color:#6b7280;">
                                    {{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}
                                </td>
                                <td style="padding:14px 24px; text-align:right;">
                                    <a href="{{ route('teacher.referrals.show', $ref->id) }}"
                                       style="display:inline-block; background:#2563eb; color:white; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:500; text-decoration:none;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:32px 24px; text-align:center; color:#6b7280;">
                                    No referrals yet. Click “+ Refer a Student” to submit one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:48px; text-align:center;">
            <p style="font-size:12px; color:#9ca3af; margin:0;">
                GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant
            </p>
        </div>
    </div>
</div>
@endsection