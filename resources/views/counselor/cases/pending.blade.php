@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">
                        Guidance Counselor Portal
                    </p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">
                        Pending Referrals & Requests
                    </h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                        Convert pending teacher referrals and student requests into official cases.
                    </p>
                </div>
                <a href="{{ route('counselor.dashboard') }}"
                   style="display: inline-block; font-size: 13px; color: #374151; text-decoration: none; font-weight: 500;
                          background: #f3f4f6; padding: 10px 16px; border-radius: 8px; white-space: nowrap; border: 1px solid #e5e7eb;">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        {{-- Teacher Referrals --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 20px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Teacher Referrals</h3>
                <a href="{{ route('counselor.referrals') }}" style="font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500;">View All →</a>
            </div>
            @if($referrals->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No pending teacher referrals.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Student</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Teacher</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Concern</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Urgency</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $referral)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827;">
                                        {{ $referral->student_name ?? 'Unknown' }}
                                        @if(!empty($referral->student_number))
                                            <br><span style="font-size: 12px; color: #6b7280;">{{ $referral->student_number }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $referral->teacher_name ?? '—' }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $referral->concern_type }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $referral->urgency }}</td>
                                    <td style="padding: 14px 24px; color: #6b7280;">{{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y') }}</td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <a href="{{ route('counselor.pending.referral', $referral->id) }}"
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

        {{-- Student Requests --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Student Counseling Requests</h3>
                <a href="{{ route('counselor.requests') }}" style="font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500;">View All →</a>
            </div>
            @if($requests->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No pending student requests.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Student</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Concern</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Preferred Time</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827;">
                                        {{ $request->student_name ?? 'Unknown' }}
                                        @if(!empty($request->student_number))
                                            <br><span style="font-size: 12px; color: #6b7280;">{{ $request->student_number }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $request->concern_type }}</td>
                                    <td style="padding: 14px 24px; color: #374151; font-size: 13px;">
                                        @if(!empty($request->preferred_datetime))
                                            {{ \Carbon\Carbon::parse(str_replace('T', ' ', $request->preferred_datetime))->format('M d, Y h:i A') }}
                                        @else
                                            <span style="color: #9ca3af;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #6b7280;">{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <a href="{{ route('counselor.pending.request', $request->id) }}"
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