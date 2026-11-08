@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <p style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 4px 0;">
                    Guidance Office
                </p>
                <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">
                    All Appointments
                </h1>
                <p style="font-size: 13px; color: #6b7280; margin: 0;">
                    Manage scheduled, completed, and cancelled appointments
                </p>
            </div>
            <a href="{{ route('counselor.dashboard') }}"
               style="display: inline-block; background: #f3f4f6; color: #4b5563; padding: 8px 14px; border-radius: 8px; font-size: 13px; text-decoration: none; font-weight: 500;">
                ← Back to Dashboard
            </a>
        </div>

        {{-- Filters --}}
        <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
            @foreach(['all' => 'All', 'today' => 'Today', 'upcoming' => 'Upcoming', 'past' => 'Past'] as $key => $label)
                <a href="{{ route('counselor.appointments', ['filter' => $key]) }}"
                   style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;
                          {{ $filter === $key ? 'background:#2563eb; color:white;' : 'background:white; color:#374151; border:1px solid #e5e7eb;' }}">
                    {{ $label }}
                </a>
            @endforeach
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
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Date & Time</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Student</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Case</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Location</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase;">Status</th>
                            <th style="padding: 12px 20px; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $apt)
                            <tr style="border-top: 1px solid #f3f4f6;">
                                <td style="padding: 14px 20px; color: #374151;">
                                    {{ \Carbon\Carbon::parse($apt->appointment_datetime)->format('M d, Y h:i A') }}
                                </td>
                                <td style="padding: 14px 20px;">
                                    <div style="font-weight: 500; color: #111827;">{{ $apt->student_name ?? 'Unknown' }}</div>
                                    @if(!empty($apt->student_number))
                                        <div style="font-size: 12px; color: #6b7280;">{{ $apt->student_number }}</div>
                                    @endif
                                </td>
                                <td style="padding: 14px 20px; color: #374151;">
                                    {{ $apt->case_number ?? '—' }}
                                </td>
                                <td style="padding: 14px 20px; color: #374151;">
                                    {{ $apt->location ?? '—' }}
                                </td>
                                <td style="padding: 14px 20px;">
                                    @php $st = $apt->status ?? 'Scheduled'; @endphp
                                    @if($st === 'Completed')
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#dcfce7; color:#166534;">Completed</span>
                                    @elseif($st === 'Cancelled')
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#fee2e2; color:#991b1b;">Cancelled</span>
                                    @elseif($st === 'Rescheduled')
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#fef3c7; color:#b45309;">Rescheduled</span>
                                    @else
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#dbeafe; color:#1d4ed8;">Scheduled</span>
                                    @endif
                                </td>
                                <td style="padding: 14px 20px; text-align: right;">
                                    <form action="{{ route('counselor.appointments.status', $apt->id) }}" method="POST" style="display:inline-flex; gap:6px; align-items:center;">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" style="font-size:12px; padding:4px 8px; border-radius:6px; border:1px solid #d1d5db;">
                                            <option value="Scheduled"   {{ $st === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="Completed"   {{ $st === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Cancelled"   {{ $st === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            <option value="Rescheduled" {{ $st === 'Rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                        </select>
                                        <button type="submit" style="padding:4px 10px; background:#2563eb; color:white; border:none; border-radius:6px; font-size:12px; cursor:pointer;">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 40px 20px; text-align: center; color: #9ca3af;">
                                    No appointments found.
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