@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">
                        Guidance Office
                    </p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">
                        Case Monitoring
                    </h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                        Search and track student guidance cases.
                    </p>
                </div>
                <a href="{{ route('counselor.dashboard') }}"
                   style="display: inline-block; font-size: 13px; color: #374151; text-decoration: none; font-weight: 500;
                          background: #f3f4f6; padding: 10px 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search + status filter --}}
        <form method="GET" action="{{ route('counselor.cases') }}"
              style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px 20px; margin-bottom: 16px;
                     display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <input type="text" name="q" value="{{ $q ?? '' }}"
                   placeholder="Search case no., student, concern..."
                   style="flex: 1; min-width: 200px; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;">

            <select name="status"
                    style="padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: white; min-width: 160px;">
                <option value="">All statuses</option>
                @foreach(['Pending','Ongoing','For Follow-up','Completed','Referred','Closed'] as $st)
                    <option value="{{ $st }}" {{ ($status ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>

            <button type="submit"
                    style="background: #2563eb; color: white; padding: 10px 18px; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;">
                Search
            </button>

            @if(($q ?? '') !== '' || ($status ?? '') !== '')
                <a href="{{ route('counselor.cases') }}"
                   style="font-size: 13px; color: #6b7280; text-decoration: none; padding: 10px 12px;">
                    Clear
                </a>
            @endif
        </form>

        {{-- Table --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: #f9fafb; text-align: left;">
                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Case No.</th>
                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Student</th>
                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Concern</th>
                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date</th>
                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cases as $case)
                            <tr style="border-top: 1px solid #f3f4f6;">
                                <td style="padding: 14px 24px; color: #111827; font-weight: 500;">{{ $case->case_number ?? 'N/A' }}</td>
                                <td style="padding: 14px 24px; color: #111827;">
                                    {{ $case->student_name ?? 'Unknown' }}
                                    @if(!empty($case->student_number))
                                        <br><span style="font-size: 12px; color: #6b7280;">{{ $case->student_number }}</span>
                                    @endif
                                </td>
                                <td style="padding: 14px 24px; color: #374151;">{{ $case->concern_type }}</td>
                                <td style="padding: 14px 24px;">
                                    @php
                                        $st = $case->status ?? 'Pending';
                                        $badge = match($st) {
                                            'Ongoing' => 'background:#dbeafe;color:#1d4ed8;',
                                            'Completed' => 'background:#dcfce7;color:#15803d;',
                                            'For Follow-up' => 'background:#fef3c7;color:#b45309;',
                                            'Referred' => 'background:#f3e8ff;color:#7c3aed;',
                                            'Closed' => 'background:#f3f4f6;color:#4b5563;',
                                            default => 'background:#f3f4f6;color:#6b7280;',
                                        };
                                    @endphp
                                    <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; {{ $badge }}">
                                        {{ $st }}
                                    </span>
                                </td>
                                <td style="padding: 14px 24px; color: #6b7280;">
                                    {{ \Carbon\Carbon::parse($case->created_at)->format('M d, Y') }}
                                </td>
                                <td style="padding: 14px 24px; text-align: right; white-space: nowrap;">
                                    <a href="{{ route('counselor.cases.show', $case->id) }}"
                                       style="display: inline-block; background: #2563eb; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 32px 24px; text-align: center; color: #9ca3af;">
                                    No cases found{{ ($q ?? '') !== '' || ($status ?? '') !== '' ? ' for this search.' : ' yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 48px; padding: 24px 0; text-align: center;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">
                GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant
            </p>
        </div>
    </div>
</div>
@endsection