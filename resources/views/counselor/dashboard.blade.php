@extends('layouts.app')

@section('content')
@php
    $pendingReferrals = \Illuminate\Support\Facades\DB::table('referrals')->where('status', 'Pending')->count();
    $pendingRequests  = \Illuminate\Support\Facades\DB::table('counseling_requests')->where('status', 'Pending')->count();
    $pendingAppointmentRequests = \Illuminate\Support\Facades\DB::table('appointment_requests')->where('status', 'Pending')->count();
    // Everything in the Pending queue whose current status is Pending (same count as the sidebar badge)
    $pendingTotal     = $pendingReferrals + $pendingRequests + $pendingAppointmentRequests;

    $ongoingCases = \Illuminate\Support\Facades\DB::table('cases')
        ->whereIn('status', ['Ongoing', 'For Follow-up', 'Pending'])
        ->count();

    $todayAppointments = \Illuminate\Support\Facades\DB::table('appointments')
        ->whereDate('appointment_datetime', today())
        ->count();

    $activeReferrals = \Illuminate\Support\Facades\DB::table('office_referrals')
        ->whereIn('status', ['Pending', 'Received', 'In Progress'])
        ->count();

    $cases = \Illuminate\Support\Facades\DB::table('cases')
        ->leftJoin('users', 'cases.student_id', '=', 'users.id')
        ->select(
            'cases.*',
            \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
            \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
        )
        ->orderBy('cases.created_at', 'desc')
        ->limit(10)
        ->get();

    // Which office currently handles each listed case (latest office referral), for the Office column.
    $caseOffice = \Illuminate\Support\Facades\DB::table('office_referrals')
        ->whereIn('case_id', $cases->pluck('id'))
        ->orderBy('created_at')
        ->pluck('office', 'case_id');

    $officeReferrals = \Illuminate\Support\Facades\DB::table('office_referrals')
        ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
        ->leftJoin('users', 'cases.student_id', '=', 'users.id')
        ->select(
            'office_referrals.*',
            'cases.case_number',
            'cases.concern_type',
            \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
            \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
        )
        ->whereIn('office_referrals.status', ['Pending', 'Received', 'In Progress', 'Completed'])
        ->orderBy('office_referrals.created_at', 'desc')
        ->limit(10)
        ->get();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: var(--gc-primary); text-transform: uppercase; letter-spacing: 0.05em;">
                        Guidance Counselor Portal · Full Access
                    </p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">
                        Counselor Command Center
                    </h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                        Full overview of counseling cases, medical/disciplinary referrals, and appointments.
                    </p>
                </div>
                <a href="{{ route('counselor.cases.create') }}"
                   style="display: inline-block; background: var(--gc-primary); color: white; padding: 10px 18px;
                          border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; white-space: nowrap;">
                    + Create Guidance Case
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
            <div class="gc-stat">
                    <p style="margin: 0 0 8px 0; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Pending Requests</p>
                    <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $pendingTotal }}</p>
                </div>
            <div class="gc-stat">
                    <p style="margin: 0 0 8px 0; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Today's Appointments</p>
                    <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $todayAppointments }}</p>
                </div>
            <div class="gc-stat">
                    <p style="margin: 0 0 8px 0; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ongoing Cases</p>
                    <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $ongoingCases }}</p>
                </div>
            <div class="gc-stat">
                    <p style="margin: 0 0 8px 0; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Active Referrals</p>
                    <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $activeReferrals }}</p>
                </div>
        </div>

        {{-- Counseling Cases --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 24px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Counseling Cases & Student Records</h3>
                <a href="{{ route('counselor.cases') }}" style="font-size: 13px; color: var(--gc-primary); text-decoration: none; font-weight: 500;">View All →</a>
            </div>

            @if($cases->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No cases yet.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px; table-layout: fixed;">
                        {{-- Same column widths in both dashboard tables so the Office and Status pills line up. --}}
                        <colgroup><col style="width: 14%;"><col style="width: 20%;"><col style="width: 20%;"><col style="width: 16%;"><col style="width: 14%;"><col style="width: 16%;"></colgroup>
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Case ID</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Student Name</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Category / Concern</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Office</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cases as $case)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827; font-weight: 500;">{{ $case->case_number ?? 'CASE-'.$case->id }}</td>
                                    <td style="padding: 14px 24px; color: #111827;">
                                        {{ $case->student_name ?? 'Unknown' }}
                                        @if(!empty($case->student_number))
                                            <br><span style="font-size: 12px; color: #6b7280;">{{ $case->student_number }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $case->concern_type }}</td>
                                    <td style="padding: 14px 24px;">
                                        @if(($case->status ?? '') === 'Referred' && isset($caseOffice[$case->id]))
                                            <x-gc-office-badge :office="$caseOffice[$case->id]" />
                                        @else
                                            <x-gc-office-badge office="Guidance" />
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px;">
                                        @php $status = $case->status ?? 'Pending'; @endphp
                                        <x-gc-status-badge :status="$status" />
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <a href="{{ route('counselor.cases.show', $case->id) }}"
                                           style="display: inline-block; background: var(--gc-primary); color: white; padding: 6px 12px;
                                                  border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500;">
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

        {{-- Active Office Referrals --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Active Clinic & Disciplinary Referrals (Guidance-Linked)</h3>
                <a href="{{ route('counselor.office_referrals') }}" style="font-size: 13px; color: var(--gc-primary); text-decoration: none; font-weight: 500;">View All →</a>
            </div>

            @if($officeReferrals->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No active office referrals yet.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px; table-layout: fixed;">
                        {{-- Same column widths in both dashboard tables so the Office and Status pills line up. --}}
                        <colgroup><col style="width: 14%;"><col style="width: 20%;"><col style="width: 20%;"><col style="width: 16%;"><col style="width: 14%;"><col style="width: 16%;"></colgroup>
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Referral ID</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Student Name</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Linked Counseling Case</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Receiving Office</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($officeReferrals as $ref)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827; font-weight: 500;">REF-{{ str_pad($ref->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td style="padding: 14px 24px; color: #111827;">
                                        {{ $ref->student_name ?? 'Unknown' }}
                                        @if(!empty($ref->student_number))
                                            <br><span style="font-size: 12px; color: #6b7280;">{{ $ref->student_number }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $ref->case_number ?? '—' }}</td>
                                    <td style="padding: 14px 24px;">
                                        <x-gc-office-badge :office="$ref->office ?? ''" />
                                    </td>
                                    <td style="padding: 14px 24px;">
                                        @php $st = $ref->status ?? 'Pending'; @endphp
                                        <x-gc-status-badge :status="$st" />
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <a href="{{ route('counselor.office_referrals.show', $ref->id) }}"
                                           style="display: inline-block; background: var(--gc-primary); color: white; padding: 6px 12px;
                                                  border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500;">
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


    </div>
</div>
@endsection