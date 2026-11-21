@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">

            {{-- Header without Back button --}}
            <div style="margin-bottom: 24px;">
                <p style="font-size: 11px; font-weight: 600; color: var(--gc-primary); text-transform: uppercase; margin: 0 0 4px 0;">Guidance Office</p>
                <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0;">Create Guidance Case</h1>
                <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0 0;">Manually open a new case for a student</p>
            </div>

            @if ($errors->any())
                <div style="margin-bottom: 16px; padding: 12px 16px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 14px;">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('counselor.cases.store') }}" method="POST">
                @csrf

                @include('partials.student-picker', ['students' => $students])

                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Year / Section (optional)</label>
                    <input type="text" name="year_section" value="{{ old('year_section') }}" placeholder="e.g. BSIT 3rd Year - A507"
                           style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Concern Type *</label>
                    <select name="concern_type" required style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px;">
                        <option value="">-- Select --</option>
                        <option value="Academic" @selected(old('concern_type') == 'Academic')>Academic</option>
                        <option value="Behavioral" @selected(old('concern_type') == 'Behavioral')>Behavioral</option>
                        <option value="Emotional" @selected(old('concern_type') == 'Emotional')>Emotional</option>
                        <option value="Personal" @selected(old('concern_type') == 'Personal')>Personal</option>
                        <option value="Others" @selected(old('concern_type') == 'Others')>Others</option>
                    </select>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Summary / Notes *</label>
                    <textarea name="summary" rows="4" required
                              style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px;"
                              placeholder="Describe the concern...">{{ old('summary') }}</textarea>
                </div>

                <div style="display:flex; gap:12px;">
                    <button type="submit"
                            style="padding:10px 20px; background:var(--gc-primary); color:white; border:none; border-radius:8px; font-size:14px; font-weight:500; cursor:pointer;">
                        Create Case
                    </button>
                    <a href="{{ route('counselor.dashboard') }}"
                       style="padding:10px 20px; background:#f3f4f6; color:#374151; border-radius:8px; font-size:14px; text-decoration:none;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection