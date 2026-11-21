@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div>
                    <p style="font-size: 11px; font-weight: 600; color: #7c3aed; text-transform: uppercase; margin: 0 0 4px 0;">
                        Student Portal
                    </p>
                    <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">
                        Referral Details
                    </h2>
                </div>
                <a href="{{ route('student.teacher_referrals') }}"
                   style="display: inline-flex; align-items: center; background: #f3f4f6; color: #374151; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; white-space: nowrap; border: 1px solid #e5e7eb;">
                    ← Back to list
                </a>
            </div>

            @if(session('success'))
                <div style="margin-bottom: 16px; padding: 12px; background: #dcfce7; color: #166534; border-radius: 8px; font-size: 14px;">
                    {{ session('success') }}
                </div>
            @endif

            <dl style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; font-size: 14px;">
                <div>
                    <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Teacher</dt>
                    <dd style="margin: 0; color: #111827; font-weight: 500;">{{ $referral->teacher_name ?? 'Teacher' }}</dd>
                </div>
                <div>
                    <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Date</dt>
                    <dd style="margin: 0; color: #111827;">{{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y') }}</dd>
                </div>
                <div>
                    <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Concern Type</dt>
                    <dd style="margin: 0; color: #111827; font-weight: 500;">{{ $referral->concern_type }}</dd>
                </div>
                <div>
                    <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Urgency</dt>
                    <dd style="margin: 0; color: #111827;">{{ $referral->urgency ?? '—' }}</dd>
                </div>
            </dl>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; margin: 0 0 6px 0;">Reason / Description</p>
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; font-size: 14px; color: #374151;">
                    {{ $referral->description ?? 'No description.' }}
                </div>
            </div>

            @php $response = $referral->student_response ?? 'Pending'; @endphp

            @if(($referral->student_response ?? 'Pending') === 'Pending')
                <div style="display: flex; justify-content: flex-start; gap: 10px; margin-top: 20px;">
                    <form action="{{ route('student.teacher_referrals.respond', $referral->id) }}" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="response" value="Accepted">
                        <button type="submit"
                                style="background: #16a34a; color: white; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                            Accept
                        </button>
                    </form>
                    <form action="{{ route('student.teacher_referrals.respond', $referral->id) }}" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="response" value="Declined">
                        <button type="submit"
                                style="background: #dc2626; color: white; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                            Decline
                        </button>
                    </form>
                </div>
            @else
                <p style="font-size: 14px; color: #6b7280; margin: 0;">
                    Your response:
                    <x-gc-status-badge :status="$response" />
                </p>
            @endif

        </div>
    </div>
</div>
@endsection