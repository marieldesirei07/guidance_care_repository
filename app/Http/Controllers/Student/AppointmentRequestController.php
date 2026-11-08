<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentRequestController extends Controller
{
    public function create()
    {
        // Optional: student's open cases (if any)
        $cases = DB::table('cases')
            ->where('student_id', Auth::id())
            ->whereIn('status', ['Pending', 'Ongoing', 'For Follow-up'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.appointments.request', compact('cases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferred_datetime' => 'required|date|after:now',
            'purpose'            => 'nullable|string|max:1000',
            'case_id'            => 'nullable|exists:cases,id',
        ]);

        DB::table('appointments')->insert([
            'case_id'              => $validated['case_id'] ?? null,
            'student_id'           => Auth::id(),
            'counselor_id'         => null,
            'appointment_datetime' => $validated['preferred_datetime'],
            'location'             => null,
            'purpose'              => $validated['purpose'] ?? 'Student-requested appointment',
            'status'               => 'Requested',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return redirect()->route('student.dashboard')
            ->with('success', 'Appointment request submitted. The guidance office will confirm the schedule.');
    }
}