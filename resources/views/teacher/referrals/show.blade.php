@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                <div>
                    <p style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; margin: 0 0 4px 0;">
                        Teacher Referral
                    </p>
                    <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0;">
                        {{ $referral->student_name ?? 'Unknown Student' }}
                    </h1>
                    @if(!empty($referral->student_number))
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">{{ $referral->student_number }}</p>
                    @endif
                </div>
                <a href="{{ route('teacher.referrals') }}"
                   style="display: inline-block; font-size: 13px; color: #111827; text-decoration: none; font-weight: 500;
                          background: #f3f4f6; padding: 8px 14px; border-radius: 8px;">
                    ← Back to list
                </a>
            </div>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f3f4f6; display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <div style="font-size: 11px; color: #6b7280; text-transform: uppercase;">Concern</div>
                    <div style="margin-top: 4px; font-weight: 500;">{{ $referral->concern_type }}</div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #6b7280; text-transform: uppercase;">Urgency</div>
                    <div style="margin-top: 4px;">{{ $referral->urgency }}</div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #6b7280; text-transform: uppercase;">Status</div>
                    <div style="margin-top: 4px;">{{ $referral->status }}</div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #6b7280; text-transform: uppercase;">Date</div>
                    <div style="margin-top: 4px;">{{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y') }}</div>
                </div>
            </div>

            @if(!empty($referral->year_section))
                <div style="margin-top: 16px;">
                    <div style="font-size: 11px; color: #6b7280; text-transform: uppercase;">Year / Section</div>
                    <div style="margin-top: 4px;">{{ $referral->year_section }}</div>
                </div>
            @endif

            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #f3f4f6;">
                <div style="font-size: 11px; color: #6b7280; text-transform: uppercase;">Description</div>
                <p style="margin: 6px 0 0 0; color: #374151;">{{ $referral->description }}</p>
            </div>
        </div>

    </div>
</div>
@endsection