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
                <p style="font-size: 11px; font-weight: 600; color: var(--gc-primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 4px 0;">
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
                                    <x-gc-office-badge :office="$ref->office ?? ''" />
                                </td>
                                <td style="padding: 14px 20px;">
                                    <x-gc-status-badge :status="$ref->status ?? 'Pending'" />
                                </td>
                                <td style="padding: 14px 20px; color: #6b7280;">
                                    {{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}
                                </td>
                                <td style="padding: 14px 20px; text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <a href="{{ route('counselor.office_referrals.show', $ref->id) }}"
                                           style="display:inline-block; background:var(--gc-primary); color:white; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:500; text-decoration:none;">
                                            View Details
                                        </a>
                                        <form action="{{ route('counselor.office_referrals.destroy', $ref->id) }}" method="POST" style="display:inline; margin:0;"
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
                                <td colspan="6" style="padding: 40px 20px; text-align: center; color: #9ca3af;">
                                    No office referrals found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection