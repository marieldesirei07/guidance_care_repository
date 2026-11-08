@extends('layouts.app')

@section('content')
@php
    $studentId = auth()->id();
    $requests = \Illuminate\Support\Facades\DB::table('counseling_requests')
        ->where('student_id', $studentId)
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Student Portal</p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">My Counseling Requests</h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">All guidance / counseling requests you submitted.</p>
                </div>
                <a href="{{ route('student.dashboard') }}"
                   style="display: inline-block; font-size: 13px; color: #374151; text-decoration: none; font-weight: 500;
                          background: #f3f4f6; padding: 10px 16px; border-radius: 8px; white-space: nowrap; border: 1px solid #e5e7eb;">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            @if($requests->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No counseling requests yet.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Concern</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #374151;">{{ $req->concern_type }}</td>
                                    <td style="padding: 14px 24px; color: #6b7280;">{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}</td>
                                    <td style="padding: 14px 24px;">
                                        @php
                                            $st = $req->status ?? 'Pending';
                                            $stBadge = $st === 'Accepted'
                                                ? 'background:#dcfce7; color:#15803d;'
                                                : 'background:#fef3c7; color:#b45309;';
                                        @endphp
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; {{ $stBadge }}">{{ $st }}</span>
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right; white-space: nowrap;">
                                        <a href="{{ route('student.requests.show', $req->id) }}"
                                           style="display: inline-block; background: #2563eb; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500; margin-right: 8px;">
                                            View Details
                                        </a>
                                        <form action="{{ route('student.requests.destroy', $req->id) }}" method="POST" style="display: inline;"
                                              onsubmit="return confirm('Delete this request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete"
                                                    style="display: inline-flex; align-items: center; justify-content: center;
                                                           width: 36px; height: 36px; padding: 0;
                                                           background: #fce7f3; color: #e11d48;
                                                           border: none; border-radius: 10px; cursor: pointer; vertical-align: middle;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
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