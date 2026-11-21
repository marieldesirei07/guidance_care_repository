@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <p style="font-size: 11px; font-weight: 600; color: #7c3aed; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 6px 0;">
                        Student Portal
                    </p>
                    <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0;">
                        Teacher Referrals
                    </h1>
                    <p style="font-size: 14px; color: #6b7280; margin: 6px 0 0 0;">
                        Accept or decline guidance referrals submitted by your teachers.
                    </p>
                </div>
                <a href="{{ route('student.dashboard') }}" style="font-size: 13px; color: var(--gc-primary); text-decoration: none;">
                    ← Back to dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 12px 14px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow: hidden;">
            @if($referrals->isEmpty())
                <div style="padding: 32px 24px; text-align: center; color: #9ca3af; font-size: 14px;">
                    No teacher referrals yet.
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; color: #6b7280; text-transform: uppercase; font-size: 11px;">
                                <th style="padding: 12px 20px; text-align: left;">Teacher</th>
                                <th style="padding: 12px 20px; text-align: left;">Concern</th>
                                <th style="padding: 12px 20px; text-align: left;">Urgency</th>
                                <th style="padding: 12px 20px; text-align: left;">Date</th>
                                <th style="padding: 12px 20px; text-align: left;">Your Response</th>
                                <th style="padding: 12px 20px; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $ref)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 20px; font-weight: 500;">{{ $ref->teacher_name ?? 'Teacher' }}</td>
                                    <td style="padding: 14px 20px;">{{ $ref->concern_type }}</td>
                                    <td style="padding: 14px 20px;">{{ $ref->urgency }}</td>
                                    <td style="padding: 14px 20px; color: #6b7280;">
                                        {{ \Carbon\Carbon::parse($ref->created_at)->format('M d, Y') }}
                                    </td>
                                    <td style="padding: 14px 20px;">
                                        @if(($ref->student_response ?? 'Pending') === 'Accepted')
                                            <span style="display:inline-block; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:500; background:#dcfce7; color:#15803d;">Accepted</span>
                                        @elseif(($ref->student_response ?? 'Pending') === 'Declined')
                                            <span style="display:inline-block; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:500; background:#fee2e2; color:#b91c1c;">Declined</span>
                                        @else
                                            <span style="display:inline-block; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:500; background:#fef3c7; color:#b45309;">Pending</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 20px; text-align: right;">
                                        @if(($ref->student_response ?? 'Pending') === 'Pending')
                                            <form action="{{ route('student.referrals.accept', $ref->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" style="background:#16a34a; color:white; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer; margin-right:6px;">
                                                    Accept
                                                </button>
                                            </form>
                                            <form action="{{ route('student.referrals.decline', $ref->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" style="background:#dc2626; color:white; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer;">
                                                    Decline
                                                </button>
                                            </form>
                                        @else
                                            <span style="font-size:12px; color:#9ca3af;">Done</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection