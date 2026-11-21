@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        @if(!isset($appointment) || !$appointment)
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; text-align: center;">
                <p style="color: #9ca3af; margin: 0 0 12px 0;">Appointment not found.</p>
                <a href="{{ route('student.appointments.index') }}" style="color: #7c3aed; font-size: 14px; text-decoration: none;">
                    ← Back to appointments
                </a>
            </div>
        @else
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">

                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                    <div>
                        <p style="font-size: 11px; font-weight: 600; color: #7c3aed; text-transform: uppercase; margin: 0 0 4px 0;">
                            Student Portal
                        </p>
                        <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">
                            Appointment Details
                        </h2>
                    </div>
                    <a href="{{ route('student.appointments.index') }}"
                       style="display: inline-block; background: #f3f4f6; color: #4b5563; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; white-space: nowrap;">
                        ← Back to list
                    </a>
                </div>

                <dl style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; font-size: 14px;">
                    <div>
                        <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Date & Time</dt>
                        <dd style="margin: 0; color: #111827; font-weight: 500;">
                            {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y h:i A') }}
                        </dd>
                    </div>
                    <div>
                        <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Status</dt>
                        <dd style="margin: 0;">
                            <x-gc-status-badge :status="$appointment->status ?? 'Scheduled'" />
                        </dd>
                    </div>
                    <div>
                        <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Case Number</dt>
                        <dd style="margin: 0; color: #111827;">{{ $appointment->case_number ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Concern Type</dt>
                        <dd style="margin: 0; color: #111827;">{{ $appointment->concern_type ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Location</dt>
                        <dd style="margin: 0; color: #111827;">{{ $appointment->location ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Case Status</dt>
                        <dd style="margin: 0;">@if($appointment->case_status)<x-gc-status-badge :status="$appointment->case_status" />@else — @endif</dd>
                    </div>
                </dl>

                <div>
                    <p style="color: #6b7280; font-size: 12px; margin: 0 0 6px 0;">Purpose / Notes</p>
                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; font-size: 14px; color: #374151; line-height: 1.5;">
                        {{ $appointment->purpose ?? 'No purpose notes provided.' }}
                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
@endsection