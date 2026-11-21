@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div style="padding: 12px 16px; background: #dcfce7; color: #166534; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header card (same structure as photo 1) --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                <div>
                    <p style="font-size: 11px; font-weight: 600; color: #7c3aed; text-transform: uppercase; margin: 0 0 4px 0;">
                        Office Referral Record
                    </p>
                    <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0;">
                        REF-{{ str_pad($referral->id, 4, '0', STR_PAD_LEFT) }}
                    </h1>
                    <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">
                        {{ $referral->student_name ?? 'Unknown Student' }}
                    </p>
                </div>

                <a href="{{ route('counselor.office_referrals') }}"
                   style="display: inline-block; font-size: 13px; color: #111827; text-decoration: none; font-weight: 500;
                          background: #f3f4f6; padding: 8px 14px; border-radius: 8px; white-space: nowrap; margin-top: 4px;">
                    ← Back to list
                </a>
            </div>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f3f4f6; display: grid; grid-template-columns: 1fr 1fr; gap: 18px 24px;">
                <div>
                    <div style="font-size: 11px; color: #6b7280; font-weight: 500; text-transform: uppercase;">Linked Case</div>
                    <div style="margin-top: 6px; font-weight: 600; color: #111827; font-size: 14px;">
                        {{ $referral->case_number ?? '—' }}
                    </div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #6b7280; font-weight: 500; text-transform: uppercase;">Receiving Office</div>
                    <div style="margin-top: 6px;">
                        <x-gc-office-badge :office="$referral->office ?? ''" />
                    </div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #6b7280; font-weight: 500; text-transform: uppercase;">Status</div>
                    <div style="margin-top: 6px;">
                        <x-gc-status-badge :status="$referral->status ?? 'Pending'" />
                    </div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #6b7280; font-weight: 500; text-transform: uppercase;">Date Referred</div>
                    <div style="margin-top: 6px; color: #111827; font-size: 14px;">
                        {{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Reason --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px;">
            <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 6px;">Reason for Referral</div>
            <p style="margin: 0; font-size: 14px; color: #4b5563;">
                {{ $referral->reason ?? '—' }}
            </p>
        </div>

        {{-- Notes --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px;">
            <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 6px;">Notes for Receiving Office</div>
            <p style="margin: 0; font-size: 14px; color: #4b5563;">
                {{ !empty($referral->notes_for_office) ? $referral->notes_for_office : 'No additional notes.' }}
            </p>
        </div>

        {{-- Linked case footer --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <div style="font-size: 12px; color: #9ca3af; margin-bottom: 4px;">Linked Counseling Case</div>
                <div style="font-size: 14px; font-weight: 600; color: #111827;">
                    {{ $referral->case_number ?? '—' }}
                    @if(!empty($referral->concern_type))
                        · {{ $referral->concern_type }}
                    @endif
                </div>
            </div>
            @if(!empty($referral->case_id))
                <a href="{{ route('counselor.cases.show', $referral->case_id) }}"
                   style="display: inline-block; background: var(--gc-primary); color: white; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;">
                    View Full Case Record
                </a>
            @endif
        </div>


    </div>
</div>
@endsection