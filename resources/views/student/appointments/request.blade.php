@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="padding: 24px;">

                <div style="margin-bottom: 24px;">
                    <p style="font-size: 11px; font-weight: 600; color: var(--gc-primary); text-transform: uppercase; margin: 0 0 4px 0;">
                        Student Portal
                    </p>
                    <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">
                        Request an Appointment
                    </h2>
                    <p style="font-size: 13px; color: #6b7280; margin: 8px 0 0 0;">
                        Suggest a preferred date and time. The guidance counselor will confirm or reschedule.
                    </p>
                </div>

                @if (session('error'))
                    <div style="margin-bottom: 16px; padding: 12px 16px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 14px;">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div style="margin-bottom: 16px; padding: 12px 16px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 14px;">
                        <ul style="margin: 0; padding-left: 18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('student.appointments.request.store') }}" method="POST"
                      onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    @csrf

                    @if(isset($cases) && $cases->count())
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">
                                Related Case (optional)
                            </label>
                            <select name="case_id"
                                    style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px;">
                                <option value="">-- No specific case --</option>
                                @foreach($cases as $case)
                                    <option value="{{ $case->id }}" @selected(old('case_id') == $case->id)>
                                        {{ $case->case_number ?? ('Case #' . $case->id) }} — {{ $case->concern_type }} ({{ $case->status }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">
                            Preferred Date & Time
                        </label>
                        <input type="datetime-local" name="preferred_datetime" required
                               min="{{ now()->addDay()->startOfDay()->format('Y-m-d\TH:i') }}"
                               value="{{ old('preferred_datetime') }}" aria-describedby="preferred_help"
                               style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px;">
                        <p class="gc-help" id="preferred_help">You can only choose a date starting tomorrow. Same-day requests are not allowed.</p>
                        @error('preferred_datetime')<p class="gc-help" style="color:#b91c1c;">{{ $message }}</p>@enderror
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">
                            Purpose / Notes *
                        </label>
                        <textarea name="purpose" rows="4" required minlength="5"
                                  placeholder="Why do you want this appointment?"
                                  style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px;">{{ old('purpose') }}</textarea>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit"
                                style="background: var(--gc-primary); color: white; padding: 10px 18px; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;">
                            Submit Request
                        </button>
                        <a href="{{ route('student.dashboard') }}"
                           style="display: inline-block; background: #e5e7eb; color: #374151; padding: 10px 18px; border-radius: 8px; font-size: 14px; text-decoration: none;">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection