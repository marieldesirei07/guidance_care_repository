@extends('layouts.app')

@section('content')
@php
    $studentId = auth()->id();
    $appointments = \Illuminate\Support\Facades\DB::table('appointments')
        ->leftJoin('cases', 'appointments.case_id', '=', 'cases.id')
        ->select('appointments.*', 'cases.case_number', 'cases.concern_type')
        ->where('appointments.student_id', $studentId)
        ->orderBy('appointments.appointment_datetime', 'desc')
        ->get();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Student Portal</p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">My Appointments</h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">All scheduled guidance appointments.</p>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <a href="{{ route('student.appointments.request') }}"
                       style="display: inline-block; font-size: 13px; color: white; text-decoration: none; font-weight: 500;
                              background: #2563eb; padding: 10px 16px; border-radius: 8px; white-space: nowrap;">
                        + Request Appointment
                    </a>
                    <a href="{{ route('student.dashboard') }}"
                       style="display: inline-block; font-size: 13px; color: #374151; text-decoration: none; font-weight: 500;
                              background: #f3f4f6; padding: 10px 16px; border-radius: 8px; white-space: nowrap; border: 1px solid #e5e7eb;">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #fee2e2; color: #b91c1c; border-radius: 8px; font-size: 14px;">
                {{ session('error') }}
            </div>
        @endif

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
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
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; {{ $stBadge }}">{{ $st }}</span>
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right; white-space: nowrap;">
                                        <a href="{{ route('student.appointments.show', $apt->id) }}"
                                           style="display: inline-block; background: #2563eb; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500; margin-right: 8px;">
                                            View Details
                                        </a>
                                        @if(in_array($apt->status ?? '', ['Scheduled', 'Rescheduled']))
                                        <form action="{{ route('student.appointments.destroy', $apt->id) }}" method="POST" style="display: inline;"
                                              onsubmit="return confirm('Cancel this appointment? The Guidance Office will see it as Cancelled.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Cancel"
                                                    style="display: inline-block; background: #fef3c7; color: #b45309; padding: 6px 12px; border: none; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; vertical-align: middle;">
                                                Cancel
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- My appointment requests --}}
        @php $appointmentRequests = $appointmentRequests ?? collect(); @endphp
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-top: 24px;">
            <div style="padding: 18px 24px; border-bottom: 1px solid #f3f4f6;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">My Appointment Requests</h3>
            </div>
            @if($appointmentRequests->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No appointment requests yet.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Preferred Time</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Purpose</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointmentRequests as $ar)
                                @php
                                    $colors = ['Pending' => ['#fef3c7', '#92400e'], 'Scheduled' => ['#dbeafe', '#1e40af'], 'Declined' => ['#fee2e2', '#991b1b']];
                                    [$bg, $fg] = $colors[$ar->status] ?? ['#f3f4f6', '#374151'];
                                @endphp
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827;">{{ \Carbon\Carbon::parse($ar->preferred_datetime)->format('M d, Y h:i A') }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ \Illuminate\Support\Str::limit($ar->purpose, 60) }}</td>
                                    <td style="padding: 14px 24px;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; background: {{ $bg }}; color: {{ $fg }};">{{ $ar->status }}</span>
                                    </td>
                                    <td style="padding: 14px 24px; color: #6b7280; font-size: 13px;">
                                        @if($ar->status === 'Scheduled')
                                            See the appointment above{{ $ar->case_number ? ' (' . $ar->case_number . ')' : '' }}.
                                        @else
                                            {{ $ar->counselor_remarks ?: '—' }}
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
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant</p>
        </div>
    </div>
</div>
@endsection