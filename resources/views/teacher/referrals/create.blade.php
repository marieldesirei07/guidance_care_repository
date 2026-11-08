@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px;">
            <div style="margin-bottom: 24px;">
                <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">
                    Teacher Portal
                </p>
                <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">
                    Refer a Student
                </h1>
                <p style="margin: 0; font-size: 14px; color: #6b7280;">
                    Submit a guidance referral. Enter the student’s details and describe the concern.
                </p>
            </div>

            @if ($errors->any())
                <div style="margin-bottom: 16px; padding: 14px 16px; background: #fee2e2; color: #b91c1c; border-radius: 8px; font-size: 14px;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('teacher.referrals.store') }}" method="POST">
                @csrf

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Student Full Name</label>
                    <input type="text" name="student_name" value="{{ old('student_name') }}" required
                           placeholder="e.g. Juan Dela Cruz"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Student Number</label>
                        <input type="text" name="student_number" value="{{ old('student_number') }}"
                               placeholder="e.g. 02000421675"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Year / Section</label>
                        <input type="text" name="year_section" value="{{ old('year_section') }}"
                               placeholder="e.g. BSIT 3rd Year - A507"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Concern Type</label>
                        <select name="concern_type" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: white; box-sizing: border-box;">
                            <option value="">-- Select Concern --</option>
                            <option value="Academic" {{ old('concern_type') == 'Academic' ? 'selected' : '' }}>Academic</option>
                            <option value="Behavioral" {{ old('concern_type') == 'Behavioral' ? 'selected' : '' }}>Behavioral</option>
                            <option value="Emotional" {{ old('concern_type') == 'Emotional' ? 'selected' : '' }}>Emotional</option>
                            <option value="Personal" {{ old('concern_type') == 'Personal' ? 'selected' : '' }}>Personal</option>
                            <option value="Others" {{ old('concern_type') == 'Others' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Urgency</label>
                        <select name="urgency" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: white; box-sizing: border-box;">
                            <option value="Low" {{ old('urgency') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ old('urgency', 'Medium') == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ old('urgency') == 'High' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Description</label>
                    <textarea name="description" rows="5" required placeholder="Please describe the concern..."
                              style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">{{ old('description') }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="submit"
                            style="background: #2563eb; color: white; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                        Submit Referral
                    </button>
                    <a href="{{ route('teacher.dashboard') }}"
                       style="display: inline-block; background: #f3f4f6; color: #374151; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; border: 1px solid #e5e7eb;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div style="margin-top: 48px; padding: 24px 0; text-align: center;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">
                GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant
            </p>
        </div>
    </div>
</div>
@endsection