@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Guidance Counselor Portal</p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">All Teacher Referrals</h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">Complete list of teacher referrals (all statuses).</p>
                </div>
                <a href="{{ route('counselor.pending') }}"
                   style="display: inline-block; font-size: 13px; color: #374151; text-decoration: none; font-weight: 500;
                          background: #f3f4f6; padding: 10px 16px; border-radius: 8px; white-space: nowrap; border: 1px solid #e5e7eb;">
                    ← Back to Pending
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabs --}}
        <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
            <a href="{{ route('counselor.referrals', ['tab' => 'all']) }}"
               style="display: inline-block; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;
                      border: 1px solid {{ ($tab ?? 'all') === 'all' ? '#2563eb' : '#e5e7eb' }};
                      background: {{ ($tab ?? 'all') === 'all' ? '#2563eb' : '#ffffff' }};
                      color: {{ ($tab ?? 'all') === 'all' ? '#ffffff' : '#374151' }};">All</a>
            <a href="{{ route('counselor.referrals', ['tab' => 'pending']) }}"
               style="display: inline-block; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;
                      border: 1px solid {{ ($tab ?? '') === 'pending' ? '#2563eb' : '#e5e7eb' }};
                      background: {{ ($tab ?? '') === 'pending' ? '#2563eb' : '#ffffff' }};
                      color: {{ ($tab ?? '') === 'pending' ? '#ffffff' : '#374151' }};">Pending</a>
            <a href="{{ route('counselor.referrals', ['tab' => 'accepted']) }}"
               style="display: inline-block; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;
                      border: 1px solid {{ ($tab ?? '') === 'accepted' ? '#2563eb' : '#e5e7eb' }};
                      background: {{ ($tab ?? '') === 'accepted' ? '#2563eb' : '#ffffff' }};
                      color: {{ ($tab ?? '') === 'accepted' ? '#ffffff' : '#374151' }};">Accepted</a>
        </div>

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            @if($referrals->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No teacher referrals found.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Student</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Teacher</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Concern</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Urgency</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $referral)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827;">
                                        {{ $referral->student_name ?? 'Unknown' }}
                                        @if(!empty($referral->student_number))
                                            <br><span style="font-size: 12px; color: #6b7280;">{{ $referral->student_number }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $referral->teacher_name ?? '—' }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $referral->concern_type }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $referral->urgency }}</td>
                                    <td style="padding: 14px 24px;">
                                        @php
                                            $st = $referral->status ?? 'Pending';
                                            $stBadge = $st === 'Accepted'
                                                ? 'background:#dcfce7; color:#15803d;'
                                                : 'background:#fef3c7; color:#b45309;';
                                        @endphp
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; {{ $stBadge }}">{{ $st }}</span>
                                    </td>
                                    <td style="padding: 14px 24px; color: #6b7280;">{{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y') }}</td>
                                    <td style="padding: 14px 24px; text-align: right; white-space: nowrap;">
                                        <a href="{{ route('counselor.pending.referral', $referral->id) }}"
                                           style="display: inline-block; background: #2563eb; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500; margin-right: 8px;">
                                            View Details
                                        </a>
                                        <form action="{{ route('counselor.referrals.destroy', $referral->id) }}" method="POST" style="display: inline;"
                                              onsubmit="return confirm('Close this record? It is kept in the history, not erased.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Close"
                                                    style="display: inline-block; background: #f3f4f6; color: #374151; padding: 6px 12px; border: none; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; vertical-align: middle;">
                                                Close
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div style="margin-top: 48px; padding: 24px 0; text-align: center;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant</p>
        </div>
    </div>
</div>
@endsection