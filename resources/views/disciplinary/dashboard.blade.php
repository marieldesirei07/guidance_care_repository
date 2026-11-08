@extends('layouts.app')

@section('content')
@php
    $referrals = \Illuminate\Support\Facades\DB::table('office_referrals')
        ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
        ->leftJoin('users', 'cases.student_id', '=', 'users.id')
        ->select(
            'office_referrals.*',
            'cases.case_number',
            'cases.concern_type',
            \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
            \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
        )
        ->where('office_referrals.office', 'Disciplinary')
        ->orderBy('office_referrals.created_at', 'desc')
        ->get();

    $pendingCount = $referrals->whereIn('status', ['Pending', 'Received', 'In Progress'])->count();
    $resolvedCount = $referrals->where('status', 'Completed')->count();
    $totalCount = $referrals->count();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">
                        Disciplinary Office Portal
                    </p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">
                        Behavioral Referrals
                    </h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                        Behavioral referrals from the Guidance Office. Limited access — confidential details are protected.
                    </p>
                </div>
                <span style="display: inline-block; font-size: 12px; font-weight: 500; color: #15803d; background: #dcfce7;
                             padding: 8px 12px; border-radius: 8px; white-space: nowrap;">
                    Privacy Protected
                </span>
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
                <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 500; color: #6b7280;">Resolved</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #15803d;">{{ $resolvedCount }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Assigned Disciplinary Referrals</h3>
            </div>

            @if($referrals->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No referrals assigned to the Disciplinary Office yet.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Case No.</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Student</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Concern</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Behavioral Notes</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $ref)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827; font-weight: 500;">{{ $ref->case_number ?? '—' }}</td>
                                    <td style="padding: 14px 24px; color: #111827;">
                                        {{ $ref->student_name ?? 'Unknown' }}
                                        @if(!empty($ref->student_number))
                                            <br><span style="font-size: 12px; color: #6b7280;">{{ $ref->student_number }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $ref->concern_type ?? '—' }}</td>
                                    <td style="padding: 14px 24px; color: #374151; max-width: 220px;">
                                        {{ \Illuminate\Support\Str::limit($ref->reason ?? '', 50) }}
                                    </td>
                                    <td style="padding: 14px 24px;">
                                        @php
                                            $st = $ref->status ?? 'Pending';
                                            $stBadge = $st === 'Completed'
                                                ? 'background:#dcfce7; color:#15803d;'
                                                : 'background:#fef3c7; color:#b45309;';
                                        @endphp
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; {{ $stBadge }}">
                                            {{ $st === 'Completed' ? 'Resolved' : $st }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 24px; color: #6b7280;">
                                        {{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right; white-space: nowrap;">
                                        @if(($ref->status ?? '') !== 'Completed')
                                           <a href="/disciplinary/referrals/{{ $ref->id }}/complete"
                                            style="display: inline-block; background: #f7b900; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500;"
                                            onclick="return confirm('Mark this referral as resolved?');">
                                                Mark Resolved
                                            </a>
                                        @else
                                            <span style="font-size: 13px; color: #9ca3af;">Done</span>
                                        @endif
                                    </td>
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