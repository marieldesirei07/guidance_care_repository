@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">

            <!-- Top bar -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2px;">
                <p style="margin: 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">
                    Teacher Referral
                </p>
                <a href="{{ route('counselor.pending') }}"
                style="display: inline-block; font-size: 13px; color: #374151; text-decoration: none; font-weight: 500;
                        background: #f3f4f6; padding: 10px 16px; border-radius: 8px; white-space: nowrap;">
                    ← Back to list
                </a>
            </div>

            <!-- Student name (higher / tighter) -->
            <h1 style="margin: 0 0 4px 0; font-size: 22px; font-weight: 700; color: #111827; line-height: 1.2;">
                {{ $referral->student_name ?? 'Unknown Student' }}
            </h1>
            <p style="margin: 0 0 24px 0; font-size: 13px; color: #6b7280;">
                Referred by {{ $referral->teacher_name ?? 'Teacher' }}
                @if(!empty($referral->student_number))
                    · {{ $referral->student_number }}
                @endif
            </p>

            <!-- Meta grid -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f3f4f6;">
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase;">Concern</p>
                    <p style="margin: 0; font-size: 14px; font-weight: 500; color: #111827;">{{ $referral->concern_type }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase;">Urgency</p>
                    <p style="margin: 0; font-size: 14px; font-weight: 500; color: #111827;">{{ $referral->urgency }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase;">Date</p>
                    <p style="margin: 0; font-size: 14px; font-weight: 500; color: #111827;">
                        {{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <!-- Description box -->
            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px 18px; margin-bottom: 24px;">
                <p style="margin: 0 0 8px 0; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase;">Description</p>
                <p style="margin: 0; font-size: 14px; color: #374151; line-height: 1.6; white-space: pre-wrap;">{{ $referral->description }}</p>
            </div>

            <!-- Student response -->
            <p style="margin: 0 0 20px 0; font-size: 13px; color: #6b7280;">
                Student response:
                <strong style="color:#111827;">{{ $referral->student_response ?? 'Pending' }}</strong>
            </p>

            @if(($referral->status ?? '') === 'Pending')
                <div style="display: flex; justify-content: flex-end;">
                    <form action="{{ route('counselor.cases.fromReferral', $referral->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                style="background:#2563eb; color:white; padding:10px 18px; border:none; border-radius:8px; font-size:14px; font-weight:500; cursor:pointer;">
                            Create Case
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection