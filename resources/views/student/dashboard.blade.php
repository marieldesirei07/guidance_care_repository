@extends('layouts.app')

@section('content')
@php
    $studentId     = auth()->id();
    $studentNumber = auth()->user()->student_number ?? null;

    $appointments = \Illuminate\Support\Facades\DB::table('appointments')
        ->leftJoin('cases', 'appointments.case_id', '=', 'cases.id')
        ->select('appointments.*', 'cases.concern_type', 'cases.case_number')
        ->where('appointments.student_id', $studentId)
        ->orderBy('appointments.appointment_datetime', 'desc')
        ->limit(5)
        ->get();

    $requests = \Illuminate\Support\Facades\DB::table('counseling_requests')
        ->where('student_id', $studentId)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $upcomingCount = \Illuminate\Support\Facades\DB::table('appointments')
        ->where('student_id', $studentId)
        ->where('status', 'Scheduled')
        ->where('appointment_datetime', '>=', now())
        ->count();

    $pendingRequestCount = \Illuminate\Support\Facades\DB::table('counseling_requests')
        ->where('student_id', $studentId)
        ->where('status', 'Pending')
        ->count();

    $awaitingReferralCount = \Illuminate\Support\Facades\DB::table('referrals')
        ->where(function ($q) use ($studentId, $studentNumber) {
            $q->where('student_id', $studentId);
            if ($studentNumber) {
                $q->orWhere('student_number', $studentNumber);
            }
        })
        ->where('student_response', 'Pending')
        ->whereNotIn('status', ['Declined', 'Closed'])
        ->count();
@endphp

<div class="gc-page">
    <div class="gc-container gc-stack">

        {{-- 1. Header --}}
        <div class="gc-card gc-card-body">
            <div class="gc-hero">
                <div class="gc-hero-text">
                    <p class="gc-eyebrow">Student Portal</p>
                    <h1 class="gc-title">Welcome, {{ auth()->user()->name }}</h1>
                    <p class="gc-subtitle">Request counseling, track your appointments, and respond to teacher referrals.</p>
                </div>
                <div class="gc-actions">
                    <a href="{{ route('student.request.create') }}" class="gc-btn gc-btn-primary">+ Request Counseling</a>
                    <a href="{{ route('student.appointments.request') }}" class="gc-btn gc-btn-secondary">Request Appointment</a>
                    <a href="{{ route('student.teacher_referrals') }}" class="gc-btn gc-btn-secondary">Teacher Referrals</a>
                </div>
            </div>
        </div>

        {{-- Feedback after actions --}}
        @if(session('success'))
            <div class="gc-flash gc-flash-success" role="status">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="gc-flash gc-flash-error" role="alert">{{ session('error') }}</div>
        @endif

        {{-- 2. Stats --}}
        <div class="gc-stats">
            <a href="{{ route('student.appointments.index') }}" class="gc-stat" aria-label="Upcoming Appointments: open the list">
                <p class="gc-stat-label">Upcoming Appointments</p>
                <p class="gc-stat-value is-active">{{ $upcomingCount }}</p>
            </a>
            <a href="{{ route('student.requests.index') }}" class="gc-stat" aria-label="Pending Requests: open the list">
                <p class="gc-stat-label">Pending Requests</p>
                <p class="gc-stat-value is-pending">{{ $pendingRequestCount }}</p>
            </a>
            <a href="{{ route('student.teacher_referrals') }}" class="gc-stat" aria-label="Referrals Awaiting Your Response: open the list">
                <p class="gc-stat-label">Referrals Awaiting Your Response</p>
                <p class="gc-stat-value">{{ $awaitingReferralCount }}</p>
            </a>
        </div>

        {{-- 3a. My Appointments --}}
        <div class="gc-card">
            <x-gc-section-header title="My Appointments" :href="route('student.appointments.index')" />

            @if($appointments->isEmpty())
                <div class="gc-empty">No appointments scheduled yet.</div>
            @else
                <div class="gc-table-wrap">
                    <table class="gc-table">
                        <thead>
                            <tr>
                                <th>Date &amp; Time</th>
                                <th>Case</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th class="gc-col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $apt)
                                <tr>
                                    <td class="gc-nowrap">{{ \Carbon\Carbon::parse($apt->appointment_datetime)->format('M d, Y h:i A') }}</td>
                                    <td>{{ $apt->case_number ?? '—' }}</td>
                                    <td>{{ $apt->location ?? 'Guidance Office' }}</td>
                                    <td><x-gc-status-badge :status="$apt->status ?? 'Scheduled'" /></td>
                                    <td class="gc-col-action">
                                        <div class="gc-row-actions">
                                            <a href="{{ route('student.appointments.show', $apt->id) }}" class="gc-btn gc-btn-primary gc-btn-sm">View Details</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- 3b. My Counseling Requests --}}
        <div class="gc-card">
            <x-gc-section-header title="My Counseling Requests" :href="route('student.requests.index')" />

            @if($requests->isEmpty())
                <div class="gc-empty">No counseling requests yet.</div>
            @else
                <div class="gc-table-wrap">
                    <table class="gc-table">
                        <thead>
                            <tr>
                                <th>Concern</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="gc-col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                                <tr>
                                    <td>{{ $req->concern_type }}</td>
                                    <td class="gc-nowrap">{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}</td>
                                    <td><x-gc-status-badge :status="$req->status ?? 'Pending'" /></td>
                                    <td class="gc-col-action">
                                        <div class="gc-row-actions">
                                            <a href="{{ route('student.requests.show', $req->id) }}" class="gc-btn gc-btn-primary gc-btn-sm">View Details</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
