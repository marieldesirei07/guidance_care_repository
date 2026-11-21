<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CounselingRequestController extends Controller
{
    public function create()
    {
        return view('student.requests.create');
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name'         => 'required|string|max:255',
            'student_number'       => 'nullable|string|max:50',
            'program'              => 'nullable|string|max:100',
            'year_section'         => 'required|string|max:100',
            'concern_type'         => 'required|string|max:100',
            'description'          => 'required|string|min:10',
            'preferred_datetime'   => 'nullable|date|after_or_equal:tomorrow',
        ], [
            'preferred_datetime.after_or_equal' => 'Please choose a date starting tomorrow. Same-day requests are not allowed.',
        ]);

        DB::table('counseling_requests')->insert([
            'student_id'           => Auth::id(),
            'student_name'         => $validated['student_name'],
            'student_number'       => $validated['student_number'] ?? null,
            'program'              => $validated['program'] ?? null,
            'year_section'         => $validated['year_section'],
            'concern_type'         => $validated['concern_type'],
            'description'          => $validated['description'],
            'preferred_datetime'   => $validated['preferred_datetime'] ?? null,
            'status'               => 'Pending',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return redirect()->route('student.dashboard')
            ->with('success', 'Your counseling request has been submitted.');
    }

    public function show($id)
    {
        $request = DB::table('counseling_requests')
            ->where('id', $id)
            ->where('student_id', auth()->id())
            ->first();

        if (!$request) {
            return redirect()->route('student.requests.index')
                ->with('error', 'Request not found.');
        }

        return view('student.requests.show', compact('request'));
    }

    public function destroy($id)
    {
        DB::table('counseling_requests')
            ->where('id', $id)
            ->where('student_id', auth()->id())
            ->delete();

        return back()->with('success', 'Request deleted.');
    }
}