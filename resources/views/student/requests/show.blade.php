@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div>
                    <p style="font-size: 11px; font-weight: 600; color: #7c3aed; text-transform: uppercase; margin: 0 0 4px 0;">
                        Student Portal
                    </p>
                    <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">
                        Counseling Request Details
                    </h2>
                </div>
                <a href="{{ route('student.requests.index') }}"
                   style="display: inline-flex; align-items: center; background: #f3f4f6; color: #374151; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; white-space: nowrap; border: 1px solid #e5e7eb;">
                    ← Back to list
                </a>
            </div>

            <dl style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; font-size: 14px;">
                <div>
                    <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Concern Type</dt>
                    <dd style="margin: 0; color: #111827; font-weight: 500;">{{ $request->concern_type }}</dd>
                </div>
                <div>
                    <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Status</dt>
                    <dd style="margin: 0;">
                        @php
                            $statusColor = match($request->status ?? 'Pending') {
                                'Accepted', 'Ongoing', 'Completed' => 'background:#dcfce7; color:#166534;',
                                'Pending' => 'background:#dbeafe; color:#1d4ed8;',
                                default => 'background:#f3f4f6; color:#374151;',
                            };
                        @endphp
                        <span style="display:inline-block; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:500; {{ $statusColor }}">
                            {{ $request->status ?? 'Pending' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt style="color: #6b7280; font-size: 12px; margin-bottom: 4px;">Date Submitted</dt>
                    <dd style="margin: 0; color: #111827;">
                        {{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') }}
                    </dd>
                </div>
            </dl>

            <div>
                <p style="color: #6b7280; font-size: 12px; margin: 0 0 6px 0;">Description</p>
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; font-size: 14px; color: #374151; line-height: 1.5;">
                    {{ $request->description ?? 'No description provided.' }}
                </div>
            </div>

        </div>

    </div>
</div>
@endsection