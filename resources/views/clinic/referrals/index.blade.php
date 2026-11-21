@extends('layouts.app')

@section('content')
@php
    $referrals = $referrals ?? collect();
@endphp

{{-- Full list for the Clinic: reason and the Mark Completed action live here and on the detail page
     (the dashboard table stays compact). Need-to-know only: no case notes, no concern type. --}}
<div class="gc-page">
    <div class="gc-container gc-stack">

        <div class="gc-card"><div class="gc-card-body">
            <div class="gc-hero">
                <div class="gc-hero-text">
                    <p class="gc-eyebrow">School Clinic Portal</p>
                    <h1 class="gc-title">All Clinic Referrals</h1>
                    <p class="gc-subtitle">Every referral sent to your office by the Guidance Office.</p>
                </div>
                <div class="gc-actions">
                    <a href="{{ route('clinic.dashboard') }}" class="gc-btn gc-btn-neutral">← Back to Dashboard</a>
                </div>
            </div>
        </div></div>

        @if(session('success'))
            <div class="gc-flash gc-flash-success" role="status">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="gc-flash gc-flash-error" role="alert">{{ session('error') }}</div>
        @endif

        <div class="gc-card">
            <div class="gc-table-wrap">
                <table class="gc-table">
                    <thead>
                        <tr>
                            <th>Referral ID</th>
                            <th>Student</th>
                            <th>Case No.</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="gc-col-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $ref)
                            <tr>
                                <td class="gc-strong gc-nowrap">REF-{{ str_pad($ref->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <span class="gc-strong">{{ $ref->student_name ?? 'Unknown' }}</span>
                                    @if(!empty($ref->student_number))
                                        <br><span class="gc-muted">{{ $ref->student_number }}</span>
                                    @endif
                                </td>
                                <td class="gc-nowrap">{{ $ref->case_number ?? '—' }}</td>
                                <td class="gc-cell-clip">{{ \Illuminate\Support\Str::limit($ref->reason ?? '', 70) }}</td>
                                <td><x-gc-status-badge :status="$ref->status ?? 'Pending'" /></td>
                                <td class="gc-nowrap">{{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}</td>
                                <td class="gc-col-action">
                                    <div class="gc-row-actions">
                                        <a href="{{ route('clinic.referrals.show', $ref->id) }}" class="gc-btn gc-btn-primary gc-btn-sm">View Details</a>
                                        @if(!in_array($ref->status ?? '', ['Completed', 'Closed']))
                                            <form method="POST" action="{{ route('clinic.referrals.complete', $ref->id) }}" style="margin:0;"
                                                  onsubmit="if (!confirm('Mark this referral as completed?')) { return false; } this.querySelector('button').disabled = true;">
                                                @csrf
                                                <button type="submit" class="gc-btn gc-btn-success gc-btn-sm">Mark Completed</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="gc-empty">No clinic referrals yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
