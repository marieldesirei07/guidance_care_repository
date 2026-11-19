@extends('layouts.app')

@section('content')
<div class="gc-page">
    <div class="gc-container gc-stack" style="max-width: 860px;">

        <div class="gc-card gc-card-body">
            <div class="gc-hero">
                <div class="gc-hero-text">
                    <p class="gc-eyebrow">Appointment Request</p>
                    <h1 class="gc-title">{{ $appointmentRequest->student_name }}</h1>
                    <p class="gc-subtitle">{{ $appointmentRequest->student_number }} · {{ $appointmentRequest->student_email }}</p>
                </div>
                <div class="gc-actions">
                    <a href="{{ route('counselor.pending') }}" class="gc-btn gc-btn-secondary">← Back to Pending</a>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="gc-flash gc-flash-error" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="gc-flash gc-flash-error" role="alert">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="gc-card gc-card-body">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:20px;">
                <div>
                    <p class="gc-stat-label">Preferred Date & Time</p>
                    <p class="gc-strong" style="margin:4px 0 0;">{{ \Carbon\Carbon::parse($appointmentRequest->preferred_datetime)->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="gc-stat-label">Status</p>
                    <p style="margin:4px 0 0;"><span class="gc-badge {{ $appointmentRequest->status === 'Pending' ? 'gc-badge-pending' : ($appointmentRequest->status === 'Scheduled' ? 'gc-badge-active' : 'gc-badge-danger') }}">{{ $appointmentRequest->status }}</span></p>
                </div>
                <div>
                    <p class="gc-stat-label">Related Case</p>
                    <p style="margin:4px 0 0;">{{ $appointmentRequest->case_number ? $appointmentRequest->case_number . ' (' . $appointmentRequest->case_concern . ')' : 'None chosen by the student' }}</p>
                </div>
                <div>
                    <p class="gc-stat-label">Requested On</p>
                    <p style="margin:4px 0 0;">{{ \Carbon\Carbon::parse($appointmentRequest->created_at)->format('M d, Y h:i A') }}</p>
                </div>
            </div>

            <div style="margin-top:20px; padding:16px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px;">
                <p class="gc-stat-label" style="margin:0 0 6px;">Purpose</p>
                <p style="margin:0; font-size:14px; color:#374151; white-space:pre-line;">{{ $appointmentRequest->purpose }}</p>
            </div>
        </div>

        @if($appointmentRequest->status === 'Pending')
            <div class="gc-card gc-card-body">
                <h2 class="gc-card-title" style="margin:0 0 4px;">Accept and Schedule</h2>
                <p class="gc-muted" style="margin:0 0 16px; font-size:13px;">Every appointment belongs to a case. Choose one of the student's open cases, or open a new one. You will then confirm the date, time and location.</p>

                <form method="POST" action="{{ route('counselor.appointment_requests.accept', $appointmentRequest->id) }}"
                      onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    @csrf
                    @php $choice = old('case_choice', $appointmentRequest->case_id ?: ($openCases->isEmpty() ? 'new' : '')); @endphp

                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Case *</label>
                    <select name="case_choice" id="case_choice" required
                            style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; margin-bottom:16px;">
                        <option value="" disabled @selected($choice === '')>-- Choose a case --</option>
                        @foreach($openCases as $case)
                            <option value="{{ $case->id }}" @selected((string) $choice === (string) $case->id)>
                                {{ $case->case_number }} — {{ $case->concern_type }} ({{ $case->status }})
                            </option>
                        @endforeach
                        <option value="new" @selected($choice === 'new')>+ Open a new case for this student</option>
                    </select>

                    <div id="new_case_fields" style="margin-bottom:16px;">
                        <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Concern Type (new case)</label>
                        <select name="concern_type" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px;">
                            <option value="">-- Select --</option>
                            @foreach(['Academic', 'Behavioral', 'Emotional', 'Personal', 'Others'] as $type)
                                <option value="{{ $type }}" @selected(old('concern_type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="gc-actions">
                        <button type="submit" class="gc-btn gc-btn-primary">Accept &amp; Schedule</button>
                    </div>
                </form>
            </div>

            <div class="gc-card gc-card-body">
                <h2 class="gc-card-title" style="margin:0 0 4px;">Decline</h2>
                <p class="gc-muted" style="margin:0 0 12px; font-size:13px;">The student will see the request as Declined, with your remarks.</p>
                <form method="POST" action="{{ route('counselor.appointment_requests.decline', $appointmentRequest->id) }}"
                      onsubmit="return confirm('Decline this appointment request?');">
                    @csrf
                    <textarea name="counselor_remarks" rows="2" maxlength="500" placeholder="Optional: e.g. Please choose a weekday afternoon."
                              style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; margin-bottom:12px;">{{ old('counselor_remarks') }}</textarea>
                    <button type="submit" class="gc-btn" style="background:#dc2626; color:white;">Decline Request</button>
                </form>
            </div>
        @else
            <div class="gc-card gc-card-body">
                <p style="margin:0; font-size:14px; color:#374151;">This request has already been handled ({{ $appointmentRequest->status }}).</p>
                @if($appointmentRequest->counselor_remarks)
                    <p class="gc-muted" style="margin:8px 0 0; font-size:13px;">Remarks: {{ $appointmentRequest->counselor_remarks }}</p>
                @endif
            </div>
        @endif

    </div>
</div>

<script>
    (function () {
        var sel = document.getElementById('case_choice');
        var box = document.getElementById('new_case_fields');
        if (!sel || !box) return;
        function sync() { box.style.display = sel.value === 'new' ? 'block' : 'none'; }
        sel.addEventListener('change', sync);
        sync();
    })();
</script>
@endsection
