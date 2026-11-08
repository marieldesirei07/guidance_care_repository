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
            'program'              => 'required|string|max:100',
            'year_level'           => 'required|string|max:50',
            'section'              => 'required|string|max:50',
            'concern_type'         => 'required|string|max:100',
            'description'          => 'required|string|min:10',
            'preferred_datetime'   => 'nullable|date|after:now',
        ]);

        $yearSection = $validated['year_level'] . ' - ' . $validated['section'];

        DB::table('counseling_requests')->insert([
            'student_id'           => Auth::id(),
            'student_name'         => $validated['student_name'],
            'student_number'       => $validated['student_number'] ?? null,
            'program'              => $validated['program'],
            'year_section'         => $yearSection,
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
            return redirect()->route('student.requests')
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