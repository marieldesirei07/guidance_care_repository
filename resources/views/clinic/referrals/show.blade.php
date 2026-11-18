@extends('layouts.app')

@section('content')
@php
    $referral = $referral ?? null;
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">

            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                <div>
                    <p style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 6px 0;">
                        School Clinic Portal (Limited Access)
                    </p>
                    <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0;">
                        REF-{{ str_pad($referral->id ?? 0, 4, '0', STR_PAD_LEFT) }}
                    </h1>
                </div>
                <a href="{{ route('clinic.dashboard') }}"
                   style="display: inline-block; background: #f3f4f6; color: #4b5563; padding: 8px 14px; border-radius: 8px; font-size: 13px; text-decoration: none; font-weight: 500;">
                    ← Back to Dashboard
                </a>
            </div>

            <div style="background: #fef3c7; color: #b45309; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 24px;">
                Privacy Protected: Full counseling records are hidden from this office.
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <p style="font-size: 12px; color: #9ca3af; margin: 0 0 4px 0;">Student Name</p>
                    <p style="font-size: 15px; font-weight: 600; color: #111827; margin: 0;">
                        {{ $referral->student_name ?? 'Unknown' }}
                    </p>
                </div>
                <div>
                    <p style="font-size: 12px; color: #9ca3af; margin: 0 0 4px 0;">Student Number</p>
                    <p style="font-size: 15px; font-weight: 600; color: #111827; margin: 0;">
                        {{ $referral->student_number ?? '—' }}
                    </p>
                </div>
                <div>
                    <p style="font-size: 12px; color: #9ca3af; margin: 0 0 4px 0;">Linked Guidance Case</p>
                    <p style="font-size: 15px; font-weight: 600; color: #2563eb; margin: 0;">
                        {{ $referral->case_number ?? '—' }}
                    </p>
                </div>
                <div>
                    <p style="font-size: 12px; color: #9ca3af; margin: 0 0 4px 0;">Status</p>
                    @if(($referral->status ?? '') === 'Completed')
                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:13px; font-weight:500; background:#dcfce7; color:#166534;">
                            Completed
                        </span>
                    @else
                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:13px; font-weight:500; background:#dbeafe; color:#1d4ed8;">
                            {{ $referral->status ?? 'Pending' }}
                        </span>
                    @endif
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="font-size: 12px; color: #9ca3af; margin: 0 0 6px 0;">Reason for Referral</p>
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; color: #374151; font-size: 14px;">
                    {{ $referral->reason ?? '—' }}
                </div>
            </div>

            @if(!empty($referral->notes_for_office))
            <div style="margin-bottom: 20px;">
                <p style="font-size: 12px; color: #9ca3af; margin: 0 0 6px 0;">Notes from Guidance Office</p>
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; color: #374151; font-size: 14px; white-space: pre-line;">
                    {{ $referral->notes_for_office }}
                </div>
            </div>
            @endif

            <div style="margin-bottom: 20px;">
                <p style="font-size: 12px; color: #9ca3af; margin: 0 0 4px 0;">Date Referred</p>
                <p style="font-size: 14px; color: #374151; margin: 0;">
                    {{ isset($referral->created_at) ? \Carbon\Carbon::parse($referral->created_at)->format('M d, Y h:i A') : '—' }}
                </p>
            </div>

            @if(($referral->status ?? '') === 'Completed')
                <span style="display:inline-block; padding:6px 12px; border-radius:9999px; font-size:13px; font-weight:500; background:#dcfce7; color:#166534;">
                    Completed
                </span>
            @else
                <form method="POST" action="{{ route('clinic.referrals.complete', $referral->id) }}" style="display:inline; margin:0;"
                      onsubmit="if (!confirm('Mark this referral as completed?')) { return false; } this.querySelector('button').disabled = true;">
                    @csrf
                    <button type="submit"
                            style="display:inline-block; background:#16a34a; color:white; padding:8px 16px; border:0; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer;">
                        Mark Completed
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>
@endsection