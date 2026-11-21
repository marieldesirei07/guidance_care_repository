@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div style="padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Case Info Card --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 11px; font-weight: 600; color: var(--gc-primary); text-transform: uppercase; letter-spacing: 0.05em;">
                        Case Details
                    </p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">
                        {{ $case->case_number ?? 'Case #'.$case->id }}
                    </h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                        {{ $case->student_name ?? 'Unknown' }}
                        @if(!empty($case->student_number))
                            · {{ $case->student_number }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('counselor.cases') }}"
                   style="display: inline-block; font-size: 13px; color: #374151; text-decoration: none; font-weight: 500;
                          background: #f3f4f6; padding: 10px 16px; border-radius: 8px; white-space: nowrap; border: 1px solid #e5e7eb;">
                    ← Back to list
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 24px;">
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 500; color: #6b7280;">Concern Type</p>
                    <p style="margin: 0; font-size: 14px; color: #111827;">{{ $case->concern_type }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 500; color: #6b7280;">Current Status</p>
                    <x-gc-status-badge :status="$case->status" />
                </div>
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 500; color: #6b7280;">Opened</p>
                    <p style="margin: 0; font-size: 14px; color: #111827;">{{ \Carbon\Carbon::parse($case->created_at)->format('M d, Y h:i A') }}</p>
                </div>
            </div>

            @if(!empty($case->summary))
                <div style="margin-top: 20px; padding: 14px 16px; background: #f9fafb; border-radius: 8px;">
                    <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 500; color: #6b7280;">Summary</p>
                    <p style="margin: 0; font-size: 14px; color: #374151;">{{ $case->summary }}</p>
                </div>
            @endif

            {{-- Update Status --}}
            <form action="{{ route('counselor.cases.status', $case->id) }}" method="POST" style="margin-top: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @csrf
                @method('PUT')
                <select name="status"
                        style="padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; color: #374151; background: white;">
                    @foreach(['Pending', 'Ongoing', 'For Follow-up', 'Completed', 'Referred', 'Closed'] as $s)
                        <option value="{{ $s }}" {{ ($case->status ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        style="background: var(--gc-primary); color: white; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                    Update Status
                </button>
            </form>
        </div>

        {{-- Appointments --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Appointments</h3>
                <a href="{{ route('counselor.appointments.create', $case->id) }}"
                   style="display: inline-block; background: #fff; color: var(--gc-primary); border: 1px solid #bfdbfe; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;">
                    + New Appointment
                </a>
            </div>
            @forelse($appointments as $apt)
                <div style="border-top: 1px solid #f3f4f6; padding: 12px 0; font-size: 14px; color: #374151;">
                    {{ \Carbon\Carbon::parse($apt->appointment_datetime)->format('M d, Y h:i A') }}
                    <span style="margin-left:6px;"><x-gc-status-badge :status="$apt->status ?? 'Scheduled'" /></span>
                    @if(!empty($apt->location))
                        <span style="color: #6b7280;"> · {{ $apt->location }}</span>
                    @endif
                </div>
            @empty
                <p style="margin: 0; font-size: 14px; color: #9ca3af;">No appointments yet.</p>
            @endforelse
        </div>

        {{-- Case Notes --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Case Notes</h3>
                <a href="{{ route('counselor.office_referral.create', $case->id) }}"
                   style="display: inline-block; background: #fff; color: var(--gc-primary); border: 1px solid #bfdbfe; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;">
                    Office Referral
                </a>
            </div>

            @forelse($notes as $note)
                <div style="border-top: 1px solid #f3f4f6; padding: 12px 0;">
                    <p style="margin: 0; font-size: 14px; color: #374151;">{{ $note->note ?? $note->content ?? '' }}</p>
                    <span style="display: block; font-size: 12px; color: #9ca3af; margin-top: 4px;">
                        {{ \Carbon\Carbon::parse($note->created_at)->format('M d, Y h:i A') }}
                    </span>
                </div>
            @empty
                <p style="margin: 0 0 16px 0; font-size: 14px; color: #9ca3af;">No notes recorded yet.</p>
            @endforelse

            <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                <h4 style="margin: 0 0 8px 0; font-size: 13px; font-weight: 500; color: #374151;">Add New Note</h4>
                <form action="{{ route('counselor.cases.notes.store', $case->id) }}" method="POST">
                    @csrf
                    <textarea name="note" rows="3" required
                              placeholder="Write your counseling note here..."
                              style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; margin-bottom: 12px; box-sizing: border-box;"></textarea>
                    <button type="submit"
                            style="background: var(--gc-primary); color: white; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                        Save Note
                    </button>
                </form>
            </div>
        </div>


    </div>
</div>
@endsection