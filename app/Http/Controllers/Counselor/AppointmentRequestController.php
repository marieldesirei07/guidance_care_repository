<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Counselor side of appointment requests:
 * view -> accept (link an open case or open a new one, then schedule) or decline.
 * The request becomes "Scheduled" when the appointment is saved (AppointmentController@store).
 */
class AppointmentRequestController extends Controller
{
    public function show($id)
    {
        $appointmentRequest = DB::table('appointment_requests')
            ->join('users', 'appointment_requests.student_id', '=', 'users.id')
            ->leftJoin('cases', 'appointment_requests.case_id', '=', 'cases.id')
            ->select(
                'appointment_requests.*',
                'users.name as student_name',
                'users.student_number',
                'users.email as student_email',
                'cases.case_number',
                'cases.concern_type as case_concern',
                'cases.status as case_status'
            )
            ->where('appointment_requests.id', $id)
            ->first();

        if (!$appointmentRequest) {
            return redirect()->route('counselor.pending')->with('error', 'Appointment request not found.');
        }

        $openCases = DB::table('cases')
            ->where('student_id', $appointmentRequest->student_id)
            ->whereIn('status', ['Pending', 'Ongoing', 'For Follow-up'])
            ->orderBy('created_at', 'desc')
            ->get(['id', 'case_number', 'concern_type', 'status']);

        return view('counselor.appointment_requests.show', compact('appointmentRequest', 'openCases'));
    }

    public function accept(Request $request, $id)
    {
        $appointmentRequest = DB::table('appointment_requests')
            ->where('id', $id)
            ->where('status', 'Pending')
            ->first();

        if (!$appointmentRequest) {
            return redirect()->route('counselor.pending')->with('error', 'This request was already handled.');
        }

        $validated = $request->validate([
            'case_choice'  => 'required|string',
            'concern_type' => 'required_if:case_choice,new|nullable|in:Academic,Behavioral,Emotional,Personal,Others',
        ], [
            'case_choice.required'     => 'Choose an open case or "Open a new case".',
            'concern_type.required_if' => 'Choose the concern type for the new case.',
        ]);

        if ($validated['case_choice'] === 'new') {
            $student = DB::table('users')->where('id', $appointmentRequest->student_id)->first();

            $caseId = DB::table('cases')->insertGetId([
                'case_number'    => 'CASE-' . strtoupper(Str::random(6)),
                'student_id'     => $student->id,
                'student_name'   => $student->name,
                'student_number' => $student->student_number,
                'counselor_id'   => Auth::id(),
                'concern_type'   => $validated['concern_type'],
                'summary'        => 'Opened from an appointment request: ' . ($appointmentRequest->purpose ?: 'no details given'),
                'status'         => 'Ongoing',
                'opened_at'      => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        } else {
            // Must be an open case of the same student.
            $caseId = DB::table('cases')
                ->where('id', $validated['case_choice'])
                ->where('student_id', $appointmentRequest->student_id)
                ->value('id');

            if (!$caseId) {
                return back()->with('error', 'Please choose one of this student\'s cases.');
            }
        }

        DB::table('appointment_requests')->where('id', $id)->update([
            'case_id'      => $caseId,
            'counselor_id' => Auth::id(),
            'updated_at'   => now(),
        ]);

        return redirect()
            ->route('counselor.appointments.create', $caseId)
            ->with('preferred_datetime', $appointmentRequest->preferred_datetime)
            ->with('appointment_request_id', $appointmentRequest->id)
            ->with('success', 'Case ready. The student\'s preferred time is loaded — confirm or change it, then schedule.');
    }

    public function decline(Request $request, $id)
    {
        $validated = $request->validate([
            'counselor_remarks' => 'nullable|string|max:500',
        ]);

        $updated = DB::table('appointment_requests')
            ->where('id', $id)
            ->where('status', 'Pending')
            ->update([
                'status'            => 'Declined',
                'counselor_id'      => Auth::id(),
                'counselor_remarks' => $validated['counselor_remarks'] ?? null,
                'updated_at'        => now(),
            ]);

        return redirect()->route('counselor.pending')
            ->with($updated ? 'success' : 'error', $updated ? 'Appointment request declined.' : 'This request was already handled.');
    }
}
