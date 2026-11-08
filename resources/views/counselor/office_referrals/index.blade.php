@extends('layouts.app')

@section('content')
@php
    $officeReferrals = \Illuminate\Support\Facades\DB::table('office_referrals')
        ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
        ->leftJoin('users', 'cases.student_id', '=', 'users.id')
        ->select(
            'office_referrals.*',
            'cases.case_number',
            'cases.concern_type',
            \Illuminate\Support\Facades\DB::raw('COALESCE(users.name, cases.student_name) as student_name'),
            \Illuminate\Support\Facades\DB::raw('COALESCE(users.student_number, cases.student_number) as student_number')
        )
        ->orderBy('office_referrals.created_at', 'desc')
        ->get();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <p style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 4px 0;">
                    Guidance Office
                </p>
                <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">
                    Office Referrals
                </h1>
                <p style="font-size: 13px; color: #6b7280; margin: 0;">
                    Referrals sent to Clinic and Disciplinary
                </p>
            </div>
            <a href="{{ route('counselor.dashboard') }}"
               style="display: inline-block; background: #f3f4f6; color: #4b5563; padding: 8px 14px; border-radius: 8px; font-size: 13px; text-decoration: none; font-weight: 500;">
                ← Back to Dashboard
            </a>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 12px 16px; background: #dcfce7; color: #166534; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: #f9fafb; text-align: left;">
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Case No.</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Student</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Office</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Status</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Date</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($officeReferrals as $ref)
                            <tr style="border-top: 1px solid #f3f4f6;">
                                <td style="padding: 14px 20px; font-weight: 500; color: #111827;">
                                    {{ $ref->case_number ?? 'N/A' }}
                                </td>
                                <td style="padding: 14px 20px;">
                                    <div style="font-weight: 500; color: #111827;">{{ $ref->student_name ?? 'Unknown' }}</div>
                                    @if(!empty($ref->student_number))
                                        <div style="font-size: 12px; color: #6b7280;">{{ $ref->student_number }}</div>
                                    @endif
                                </td>
                                <td style="padding: 14px 20px;">
                                    @if(($ref->office ?? '') === 'Clinic')
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#dbeafe; color:#1d4ed8;">Clinic</span>
                                    @else
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#fef3c7; color:#b45309;">Disciplinary</span>
                                    @endif
                                </td>
                                <td style="padding: 14px 20px;">
                                    @if(($ref->status ?? '') === 'Completed')
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#dbeafe; color:#1d4ed8;">Completed</span>
                                    @else
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#fef3c7; color:#b45309;">{{ $ref->status ?? 'Pending' }}</span>
                                    @endif
                                </td>
                                <td style="padding: 14px 20px; color: #6b7280;">
                                    {{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}
                                </td>
                                <td style="padding: 14px 20px; text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <a href="{{ route('counselor.office_referrals.show', $ref->id) }}"
                                           style="display:inline-block; background:#2563eb; color:white; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:500; text-decoration:none;">
                                            View Details
                                        </a>
                                        <form action="{{ route('counselor.office_referrals.destroy', $ref->id) }}" method="POST" style="display:inline; margin:0;"
                                              onsubmit="return confirm('Delete this office referral?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete"
                                                    style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#fee2e2; border:1px solid #fecaca; border-radius:8px; cursor:pointer; padding:0;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 40px 20px; text-align: center; color: #9ca3af;">
                                    No office referrals found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 48px; padding: 20px 0; text-align: center;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">
                GuidanceCare System • STI College Global City
            </p>
        </div>
    </div>
</div>
@endsection