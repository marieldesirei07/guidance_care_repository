@extends('layouts.app')

@section('content')
@php
    $teacherId = auth()->id();

    $myReferrals = \Illuminate\Support\Facades\DB::table('referrals')
        ->where('teacher_id', $teacherId)
        ->orderBy('created_at', 'desc')
        ->limit(8)
        ->get();

    $pendingCount = \Illuminate\Support\Facades\DB::table('referrals')
        ->where('teacher_id', $teacherId)
        ->where('status', 'Pending')
        ->count();

    $acceptedCount = \Illuminate\Support\Facades\DB::table('referrals')
        ->where('teacher_id', $teacherId)
        ->where('status', 'Accepted')
        ->count();

    $totalCount = \Illuminate\Support\Facades\DB::table('referrals')
        ->where('teacher_id', $teacherId)
        ->count();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">
                        Teacher Portal
                    </p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">
                        Welcome, {{ auth()->user()->name }}
                    </h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                        Refer students to the Guidance Office and track your submissions.
                    </p>
                </div>
                <a href="{{ route('teacher.referrals.create') }}"
                   style="display: inline-block; background: #2563eb; color: white; padding: 10px 16px;
                          border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none;">
                    + Refer a Student
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px;">
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
                <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 500; color: #6b7280;">Total Referrals</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $totalCount }}</p>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
                <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 500; color: #6b7280;">Pending</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #b45309;">{{ $pendingCount }}</p>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
                <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 500; color: #6b7280;">Accepted</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #15803d;">{{ $acceptedCount }}</p>
            </div>
        </div>

        {{-- My Referrals --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">My Referrals</h3>
            </div>
            @if($myReferrals->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">You haven’t submitted any referrals yet.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Student</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Concern</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Urgency</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myReferrals as $ref)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827;">
                                        {{ $ref->student_name ?? 'Unknown' }}
                                        @if(!empty($ref->student_number))
                                            <br><span style="font-size: 12px; color: #6b7280;">{{ $ref->student_number }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $ref->concern_type }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $ref->urgency }}</td>
                                    <td style="padding: 14px 24px;">
                                        @php
                                            $st = $ref->status ?? 'Pending';
                                            $stBadge = $st === 'Accepted'
                                                ? 'background:#dcfce7; color:#15803d;'
                                                : 'background:#fef3c7; color:#b45309;';
                                        @endphp
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; {{ $stBadge }}">{{ $st }}</span>
                                    </td>
                                    <td style="padding: 14px 24px; color: #6b7280;">{{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div style="margin-top: 48px; padding: 24px 0; text-align: center;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">
                GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant
            </p>
        </div>
    </div>
</div>
@endsection