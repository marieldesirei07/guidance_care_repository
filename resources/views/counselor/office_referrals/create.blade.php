@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">

                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Refer Case to Another Office</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Student: <strong>{{ $case->student_name ?? 'Unknown' }}</strong> 
                        ({{ $case->student_number ?? 'N/A' }})
                    </p>
                    <p class="text-sm text-gray-500">
                        Case Number: <strong>{{ $case->case_number ?? $case->id }}</strong> • 
                        Concern: <strong>{{ $case->concern_type }}</strong>
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('counselor.office_referral.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="case_id" value="{{ $case->id }}">

                    {{-- Office Selection --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Refer to Office</label>
                        <select name="office" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Select Office --</option>
                            <option value="Clinic">School Clinic</option>
                            <option value="Disciplinary">Disciplinary Office</option>
                        </select>
                    </div>

                    {{-- Reason --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Referral</label>
                        <textarea name="reason" rows="4" 
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                  required
                                  placeholder="Explain why this case needs to be referred..."></textarea>
                        <p style="margin:6px 0 0; font-size:12px; color:#b45309;">Privacy: this reason is visible to the receiving office. Do not include confidential counseling details or case notes.</p>
                    </div>

                    {{-- Notes for receiving office --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Notes for the Receiving Office (Optional)
                        </label>
                        <textarea name="notes_for_office" rows="3" 
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Only include information that the receiving office needs to know."></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            Important: Do not share full confidential counseling notes. Only share what is necessary.
                        </p>
                    </div>

                    {{-- Buttons --}}
                    <div style="margin-top: 20px;">
                        <button type="submit" style="background-color: #4F46E5; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">
                            Submit Referral
                        </button>

                        <a href="{{ route('counselor.cases.show', $case->id) }}" 
                            style="margin-left: 10px; background-color: #E5E7EB; color: #1F2937; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block;">
                             Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection