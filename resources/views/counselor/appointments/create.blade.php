@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #fee2e2; color: #b91c1c; border-radius: 8px; font-size: 14px;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">

            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                <p style="margin: 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">
                    Schedule Appointment
                </p>
                <a href="{{ route('counselor.cases.show', $case->id) }}"
                   style="display: inline-block; font-size: 13px; color: #374151; text-decoration: none; font-weight: 500;
                          background: #f3f4f6; padding: 10px 16px; border-radius: 8px; white-space: nowrap;">
                    ← Back to case
                </a>
            </div>

            <h1 style="margin: 0 0 4px 0; font-size: 22px; font-weight: 700; color: #111827; line-height: 1.2;">
                {{ $case->student_name ?? 'Unknown Student' }}
            </h1>
            <p style="margin: 0 0 24px 0; font-size: 13px; color: #6b7280;">
                Case {{ $case->case_number ?? '#' . $case->id }}
                @if(!empty($case->student_number))
                    · {{ $case->student_number }}
                @endif
                @if(!empty($case->concern_type))
                    · {{ $case->concern_type }}
                @endif
            </p>

            <form action="{{ route('counselor.appointments.store') }}" method="POST">
                @csrf
                @php $apptRequestId = old('appointment_request_id', session('appointment_request_id')); @endphp
                @if($apptRequestId)
                    <input type="hidden" name="appointment_request_id" value="{{ $apptRequestId }}">
                @endif

                <input type="hidden" name="case_id" value="{{ $case->id }}">

                @if(!empty($case->student_id))
                    <input type="hidden" name="student_id" value="{{ $case->student_id }}">
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">
                            Date &amp; Time <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="datetime-local"
                               name="appointment_datetime"
                               value="{{ old('appointment_datetime', session('preferred_datetime') ? \Illuminate\Support\Carbon::parse(session('preferred_datetime'))->format('Y-m-d\TH:i') : '') }}"
                               required
                               style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">
                            Location
                        </label>
                        <input type="text"
                               name="location"
                               value="{{ old('location', 'Guidance Office') }}"
                               placeholder="e.g., Guidance Office, Room 101"
                               style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">
                        Purpose / Notes
                    </label>
                    <textarea name="purpose"
                              rows="4"
                              placeholder="Brief purpose of this appointment..."
                              style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; resize: vertical;">{{ old('purpose') }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('counselor.cases.show', $case->id) }}"
                       style="display: inline-block; background: #f3f4f6; color: #374151; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none;">
                        Cancel
                    </a>
                    <button type="submit"
                            style="background: #2563eb; color: white; padding: 10px 18px; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;">
                        Schedule Appointment
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection