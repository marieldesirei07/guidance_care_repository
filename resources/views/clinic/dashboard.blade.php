@extends('layouts.app')

@section('content')
@php
    $referrals = \Illuminate\Support\Facades\DB::table('office_referrals')
        ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
        ->leftJoin('users', 'cases.student_id', '=', 'users.id')
        ->select(
            'office_referrals.*',
            'cases.case_number',
            \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
            \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
        )
        ->where('office_referrals.office', 'Clinic')
        ->orderBy('office_referrals.created_at', 'desc')
        ->get();

    // Counts use the full list; the table shows the latest 10 and "View All" opens the rest.
    $totalCount     = $referrals->count();
    $pendingCount   = $referrals->whereIn('status', ['Pending', 'Received', 'In Progress'])->count();
    $completedCount = $referrals->where('status', 'Completed')->count();
    $recent         = $referrals->take(10);
@endphp

<div class="gc-page">
    <div class="gc-container gc-stack">

        {{-- 1. Header --}}
        <div class="gc-card gc-card-body">
            <div class="gc-hero">
                <div class="gc-hero-text">
                    <p class="gc-eyebrow">School Clinic Portal</p>
                    <h1 class="gc-title">Clinic Referrals</h1>
                    <p class="gc-subtitle">Medical / health referrals from the Guidance Office. Limited access — confidential details are protected.</p>
                </div>
                <span class="gc-chip-privacy">Privacy Protected</span>
            </div>
        </div>

        @if(session('success'))
            <div class="gc-flash gc-flash-success" role="status">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="gc-flash gc-flash-error" role="alert">{{ session('error') }}</div>
        @endif

        {{-- 2. Stats --}}
        <div class="gc-stats">
            <div class="gc-stat">
                <p class="gc-stat-label">Total Referrals</p>
                <p class="gc-stat-value">{{ $totalCount }}</p>
            </div>
            <div class="gc-stat">
                <p class="gc-stat-label">Pending / Active</p>
                <p class="gc-stat-value is-pending">{{ $pendingCount }}</p>
            </div>
            <div class="gc-stat">
                <p class="gc-stat-label">Completed</p>
                <p class="gc-stat-value is-done">{{ $completedCount }}</p>
            </div>
        </div>

        {{-- 3. Table --}}
        <div class="gc-card">
            <x-gc-section-header title="Assigned Clinic Referrals" :href="route('clinic.referrals.index')" />

            @if($recent->isEmpty())
                <div class="gc-empty">No referrals assigned to the Clinic yet.</div>
            @else
                <div class="gc-table-wrap">
                    <table class="gc-table">
                        <thead>
                            <tr>
                                <th>Case No.</th>
                                <th>Student</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="gc-col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent as $ref)
                                <tr>
                                    <td class="gc-strong">{{ $ref->case_number ?? '—' }}</td>
                                    <td>
                                        <span class="gc-strong">{{ $ref->student_name ?? 'Unknown' }}</span>
                                        @if(!empty($ref->student_number))
                                            <br><span class="gc-muted">{{ $ref->student_number }}</span>
                                        @endif
                                    </td>
                                    <td class="gc-cell-clip">{{ \Illuminate\Support\Str::limit($ref->reason ?? '', 50) }}</td>
                                    <td><x-gc-status-badge :status="$ref->status ?? 'Pending'" /></td>
                                    <td class="gc-nowrap">{{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}</td>
                                    <td class="gc-col-action">
                                        <div class="gc-row-actions">
                                            <a href="{{ route('clinic.referrals.show', $ref->id) }}" class="gc-btn gc-btn-secondary gc-btn-sm">View Details</a>
                                            @if(($ref->status ?? '') !== 'Completed')
                                                <form method="POST" action="{{ route('clinic.referrals.complete', $ref->id) }}" style="margin:0;"
                                                      onsubmit="if (!confirm('Mark this referral as completed?')) { return false; } this.querySelector('button').disabled = true;">
                                                    @csrf
                                                    <button type="submit" class="gc-btn gc-btn-primary gc-btn-sm">Mark Completed</button>
                                                </form>
                                            @else
                                                <span class="gc-muted">Done</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <p class="gc-footer">GuidanceCare System • STI College Global City • Centralized Student Support &amp; Privacy Compliant</p>
    </div>
</div>
@endsection
