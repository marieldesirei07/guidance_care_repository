<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Student side of appointment requests.
 * A request is saved in `appointment_requests`; the counselor turns it into a real appointment.
 */
class AppointmentRequestController extends Controller
{
    public function create()
    {
        // Only this student's own open cases can be linked.
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
            'preferred_datetime' => 'required|date|after_or_equal:tomorrow',
            'purpose'            => 'required|string|min:5|max:1000',
            'case_id'            => [
                'nullable',
                Rule::exists('cases', 'id')->where('student_id', Auth::id()),
            ],
        ], [
            'preferred_datetime.after_or_equal' => 'Please choose a date starting tomorrow. Same-day requests are not allowed.',
            'case_id.exists'           => 'Please choose one of your own cases.',
        ]);

        $hasOpenRequest = DB::table('appointment_requests')
            ->where('student_id', Auth::id())
            ->where('status', 'Pending')
            ->exists();

        if ($hasOpenRequest) {
            return back()->withInput()
                ->with('error', 'You already have a pending appointment request. Please wait for the guidance office to respond.');
        }

        DB::table('appointment_requests')->insert([
            'student_id'         => Auth::id(),
            'case_id'            => $validated['case_id'] ?? null,
            'preferred_datetime' => $validated['preferred_datetime'],
            'purpose'            => $validated['purpose'],
            'status'             => 'Pending',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return redirect()->route('student.appointments.index')
            ->with('success', 'Appointment request sent. The guidance office will confirm the schedule.');
    }
}
