@extends('layouts.app')

@section('content')
@php
    $requests = $requests ?? collect();
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <p style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 4px 0;">
                    Guidance Counselor Portal
                </p>
                <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0;">
                    All Student Counseling Requests
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
            <span style="background:#2563eb; color:white; padding:6px 14px; border-radius:8px; font-size:13px; font-weight:500;">All</span>
            <span style="background:white; color:#4b5563; padding:6px 14px; border-radius:8px; font-size:13px; border:1px solid #e5e7eb;">Tertiary</span>
            <span style="background:white; color:#4b5563; padding:6px 14px; border-radius:8px; font-size:13px; border:1px solid #e5e7eb;">SHS</span>
        </div>

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: #f9fafb; text-align: left;">
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Student</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Concern</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Date</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr style="border-top: 1px solid #f3f4f6;">
                                <td style="padding: 14px 20px;">
                                    <div style="font-weight: 500;">{{ $request->student_name ?? 'Unknown' }}</div>
                                    @if(!empty($request->student_number))
                                        <div style="font-size: 12px; color: #9ca3af;">{{ $request->student_number }}</div>
                                    @endif
                                </td>
                                <td style="padding: 14px 20px;">{{ $request->concern_type }}</td>
                                <td style="padding: 14px 20px; color: #6b7280;">
                                    {{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}
                                </td>
                                <td style="padding: 14px 20px; text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <a href="{{ route('counselor.pending.request', $request->id) }}"
                                           style="display:inline-block; background:#2563eb; color:white; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:500; text-decoration:none;">
                                            View Details
                                        </a>
                                        <form action="{{ route('counselor.pending.requests.destroy', $request->id) }}" method="POST" style="display:inline; margin:0;"
                                              onsubmit="return confirm('Delete this counseling request?');">
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
                                <td colspan="4" style="padding: 32px; text-align: center; color: #9ca3af;">No counseling requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection