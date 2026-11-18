@extends('layouts.app')

@section('content')
<div style="padding: 32px 0 48px 0;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="display:flex; flex-direction:column; gap:24px;">

        {{-- Header --}}
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:24px; box-shadow:0 1px 2px rgba(0,0,0,0.04);">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                <div style="min-width:0; flex:1;">
                    <p style="margin:0 0 6px 0; font-size:11px; font-weight:600; color:#2563eb; text-transform:uppercase; letter-spacing:0.05em;">
                        Student Portal
                    </p>
                    <h1 style="margin:0 0 4px 0; font-size:24px; font-weight:700; color:#111827;">
                        Teacher Referrals
                    </h1>
                    <p style="margin:0; font-size:14px; color:#6b7280;">
                        Accept or decline guidance referrals submitted by your teachers.
                    </p>
                </div>

                <a href="{{ route('student.dashboard') }}"
                   style="display:inline-block; padding:8px 14px; background:#f3f4f6; color:#374151; border-radius:8px; font-size:13px; font-weight:500; text-decoration:none; border:1px solid #e5e7eb; white-space:nowrap;">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        {{-- Table Card --}}
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 2px rgba(0,0,0,0.04);">
            @if(!isset($referrals) || $referrals->isEmpty())
                <div style="padding:40px 24px; text-align:center; color:#9ca3af; font-size:14px;">
                    No teacher referrals found.
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:14px;">
                        <thead>
                            <tr style="background:#f9fafb; text-align:left;">
                                <th style="padding:12px 24px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase;">Teacher</th>
                                <th style="padding:12px 24px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase;">Concern</th>
                                <th style="padding:12px 24px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase;">Urgency</th>
                                <th style="padding:12px 24px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase;">Date</th>
                                <th style="padding:12px 24px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase;">Your Response</th>
                                <th style="padding:12px 24px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $ref)
                                @php
                                    $response = $ref->student_response ?? $ref->response ?? $ref->status ?? 'Pending';
                                    $bg = '#fef3c7'; $fg = '#b45309';
                                    if (in_array($response, ['Accepted', 'Accepted by Student'])) { $bg = '#dcfce7'; $fg = '#15803d'; }
                                    elseif (in_array($response, ['Declined', 'Declined by Student'])) { $bg = '#fee2e2'; $fg = '#b91c1c'; }
                                @endphp
                                <tr style="border-top:1px solid #f3f4f6;">
                                    <td style="padding:14px 24px; color:#111827; font-weight:500;">
                                        {{ $ref->teacher_name ?? 'Teacher' }}
                                    </td>
                                    <td style="padding:14px 24px; color:#374151;">
                                        {{ $ref->concern_type ?? '—' }}
                                    </td>
                                    <td style="padding:14px 24px; color:#374151;">
                                        {{ $ref->urgency ?? '—' }}
                                    </td>
                                    <td style="padding:14px 24px; color:#6b7280; white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}
                                    </td>
                                    <td style="padding:14px 24px;">
                                        <span style="display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:500; background:{{ $bg }}; color:{{ $fg }};">
                                            {{ $response }}
                                        </span>
                                    </td>
                                    <td style="padding:14px 24px; text-align:right;">
                                        <div style="display:inline-flex; align-items:center; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                                            <a href="{{ route('student.teacher_referrals.show', $ref->id) }}"
                                               style="display:inline-block; padding:6px 12px; background:#2563eb; color:#fff; border-radius:6px; font-size:12px; font-weight:500; text-decoration:none;">
                                                View Details
                                            </a>
                                            <form action="{{ route('student.teacher_referrals.destroy', $ref->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Delete this referral from your list?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#fef2f2; color:#dc2626; border:none; border-radius:6px; cursor:pointer;">
                                                    🗑
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div style="padding: 16px 0 8px 0; text-align:center;">
            <p style="margin:0; font-size:12px; color:#9ca3af;">
                GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant
            </p>
        </div>

    </div>
</div>
@endsection