<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentScheduled;

class AppointmentController extends Controller
{
    public function index()
    {
        $filter = request('filter', 'all'); // all | today | upcoming | past

        $query = DB::table('appointments')
            ->leftJoin('cases', 'appointments.case_id', '=', 'cases.id')
            ->leftJoin('users', 'appointments.student_id', '=', 'users.id')
            ->select(
                'appointments.*',
                'cases.case_number',
                'cases.concern_type',
                DB::raw('COALESCE(users.name, cases.student_name) as student_name'),
                DB::raw('COALESCE(users.student_number, cases.student_number) as student_number')
            );

        if ($filter === 'today') {
            $query->whereDate('appointments.appointment_datetime', today());
        } elseif ($filter === 'upcoming') {
            $query->where('appointments.appointment_datetime', '>', now())
                  ->where('appointments.status', 'Scheduled');
        } elseif ($filter === 'past') {
            $query->where('appointments.appointment_datetime', '<', now());
        }

        $appointments = $query->orderBy('appointments.appointment_datetime', 'desc')->get();

        return view('counselor.appointments.index', compact('appointments', 'filter'));
    }

    public function create($caseId)
    {
        $case = DB::table('cases')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select(
                'cases.*',
                DB::raw('COALESCE(users.name, cases.student_name) as student_name'),
                DB::raw('COALESCE(users.student_number, cases.student_number) as student_number')
            )
            ->where('cases.id', $caseId)
            ->first();

        if (!$case) {
            return redirect()->route('counselor.cases')->with('error', 'Case not found.');
        }

        return view('counselor.appointments.create', compact('case'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id'              => 'required|exists:cases,id',
            'student_id'           => 'nullable|exists:users,id',
            'appointment_datetime' => 'required|date|after:now',
            'location'             => 'nullable|string|max:255',
            'purpose'              => 'nullable|string|max:1000',
        ]);

        $id = DB::table('appointments')->insertGetId([
            'case_id'              => $validated['case_id'],
            'student_id'           => $validated['student_id'] ?? null,
            'counselor_id'         => auth()->id(),
            'appointment_datetime' => $validated['appointment_datetime'],
            'location'             => $validated['location'],
            'purpose'              => $validated['purpose'],
            'status'               => 'Scheduled',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        if (!empty($validated['student_id'])) {
            $student = DB::table('users')->where('id', $validated['student_id'])->first();
            if ($student && !empty($student->email)) {
                try {
                    Mail::to($student->email)->send(new AppointmentScheduled([
                        'student_name' => $student->name,
                        'datetime'     => $validated['appointment_datetime'],
                        'location'     => $validated['location'] ?? 'Guidance Office',
                        'purpose'      => $validated['purpose'] ?? '',
                    ]));
                } catch (\Exception $e) {
                }
            }
        }

        return redirect()->route('counselor.cases.show', $validated['case_id'])
            ->with('success', 'Appointment scheduled successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Scheduled,Completed,Cancelled,Rescheduled',
        ]);

        DB::table('appointments')->where('id', $id)->update([
            'status'     => $request->status,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Appointment status updated.');
    }
}