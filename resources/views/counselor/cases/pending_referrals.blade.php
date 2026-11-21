@extends('layouts.app')

@section('content')
@php
    $referrals = $referrals ?? collect();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <p style="font-size: 11px; font-weight: 600; color: var(--gc-primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 4px 0;">
                    Guidance Counselor Portal
                </p>
                <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0;">
                    All Teacher Referrals
                </h1>
            </div>
            <a href="{{ route('counselor.pending') }}"
               style="display: inline-block; background: #f3f4f6; color: #4b5563; padding: 8px 14px; border-radius: 8px; font-size: 13px; text-decoration: none; font-weight: 500;">
                ← Back to Pending
            </a>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 12px 16px; background: #dcfce7; color: #166534; border-radius: 8px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: flex; gap: 8px; margin-bottom: 20px;">
            <span style="background:var(--gc-primary); color:white; padding:6px 14px; border-radius:8px; font-size:13px; font-weight:500;">All</span>
            <span style="background:white; color:#4b5563; padding:6px 14px; border-radius:8px; font-size:13px; border:1px solid #e5e7eb;">Tertiary</span>
            <span style="background:white; color:#4b5563; padding:6px 14px; border-radius:8px; font-size:13px; border:1px solid #e5e7eb;">SHS</span>
        </div>

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: #f9fafb; text-align: left;">
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Student</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Teacher</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Concern</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Student Response</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Date</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $referral)
                            <tr style="border-top: 1px solid #f3f4f6;">
                                <td style="padding: 14px 20px;">
                                    <div style="font-weight: 500;">{{ $referral->student_name ?? 'Unknown' }}</div>
                                    @if(!empty($referral->student_number))
                                        <div style="font-size: 12px; color: #9ca3af;">{{ $referral->student_number }}</div>
                                    @endif
                                </td>
                                <td style="padding: 14px 20px;">{{ $referral->teacher_name ?? '—' }}</td>
                                <td style="padding: 14px 20px;">{{ $referral->concern_type }}</td>
                                <td style="padding: 14px 20px;">
                                    @php $sr = $referral->student_response ?? 'Pending'; @endphp
                                    <x-gc-status-badge :status="$sr" />
                                </td>
                                <td style="padding: 14px 20px; color: #6b7280;">
                                    {{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y') }}
                                </td>
                                <td style="padding: 14px 20px; text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <a href="{{ route('counselor.pending.referral', $referral->id) }}"
                                           style="display:inline-block; background:var(--gc-primary); color:white; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:500; text-decoration:none;">
                                            View Details
                                        </a>
                                        <form action="{{ route('counselor.pending.referrals.destroy', $referral->id) }}" method="POST" style="display:inline; margin:0;"
                                              onsubmit="return confirm('Close this record? It is kept in the history, not erased.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Close" class="gc-btn gc-btn-sm gc-btn-danger-outline">Close</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 32px; text-align: center; color: #9ca3af;">No teacher referrals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection