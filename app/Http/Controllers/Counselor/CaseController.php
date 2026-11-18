<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CaseController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $cases = \Illuminate\Support\Facades\DB::table('cases')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select(
                'cases.id',
                'cases.case_number',
                'cases.student_id',
                'cases.counselor_id',
                'cases.referral_id',
                'cases.request_id',
                'cases.concern_type',
                'cases.summary',
                'cases.status',
                'cases.opened_at',
                'cases.closed_at',
                'cases.created_at',
                'cases.updated_at',
                \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
                \Illuminate\Support\Facades\DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
            )
            ->orderBy('cases.created_at', 'desc')
            ->get();

        if ($status !== '') {
            $cases = $cases->filter(function ($case) use ($status) {
                return ($case->status ?? '') === $status;
            })->values();
        }

        if ($q !== '') {
            $cases = $cases->filter(function ($case) use ($q) {
                $haystack = implode(' ', [
                    $case->case_number ?? '',
                    $case->student_name ?? '',
                    $case->student_number ?? '',
                    $case->concern_type ?? '',
                    $case->summary ?? '',
                ]);
                return stripos($haystack, $q) !== false;
            })->values();
        }

        return view('counselor.cases.index', compact('cases', 'q', 'status'));
    }

    public function show($id)
    {
        $case = DB::table('cases')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select(
                'cases.*',
                DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
                DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
            )
            ->where('cases.id', $id)
            ->first();

        if (!$case) {
            return redirect()->route('counselor.cases')->with('error', 'Case not found.');
        }

        // Preferred time from linked counseling request (if any)
        $preferredDatetime = null;
        if (!empty($case->request_id)) {
            $preferredDatetime = DB::table('counseling_requests')
                ->where('id', $case->request_id)
                ->value('preferred_datetime');
        }

        $notes = [];
        if (\Schema::hasTable('case_notes')) {
            $notes = DB::table('case_notes')
                ->where('case_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $appointments = [];
        if (\Schema::hasTable('appointments')) {
            $appointments = DB::table('appointments')
                ->where('case_id', $id)
                ->orderBy('appointment_datetime', 'desc')
                ->get();
        }

        return view('counselor.cases.show', compact('case', 'notes', 'appointments', 'preferredDatetime'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Ongoing,For Follow-up,Completed,Referred,Closed',
        ]);

        DB::table('cases')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Case status updated successfully.');
    }

    public function pending()
    {
        $referrals = DB::table('referrals')
            ->leftJoin('users as students', 'referrals.student_id', '=', 'students.id')
            ->leftJoin('users as teachers', 'referrals.teacher_id', '=', 'teachers.id')
            ->select(
                'referrals.*',
                DB::raw('COALESCE(referrals.student_name, students.name) as student_name'),
                DB::raw('COALESCE(referrals.student_number, students.student_number) as student_number'),
                'teachers.name as teacher_name'
            )
            ->where('referrals.status', 'Pending')
            ->orderBy('referrals.created_at', 'desc')
            ->get();

        $requests = DB::table('counseling_requests')
            ->leftJoin('users', 'counseling_requests.student_id', '=', 'users.id')
            ->select(
                'counseling_requests.*',
                DB::raw('COALESCE(counseling_requests.student_name, users.name) as student_name'),
                DB::raw('COALESCE(counseling_requests.student_number, users.student_number) as student_number')
            )
            ->where('counseling_requests.status', 'Pending')
            ->orderBy('counseling_requests.created_at', 'desc')
            ->get();

        return view('counselor.cases.pending', compact('referrals', 'requests'));
    }

    public function allReferrals(Request $request)
    {
        $tab = $request->get('tab', 'all'); // all | pending | accepted

        $query = DB::table('referrals')
            ->leftJoin('users as students', 'referrals.student_id', '=', 'students.id')
            ->leftJoin('users as teachers', 'referrals.teacher_id', '=', 'teachers.id')
            ->select(
                'referrals.*',
                DB::raw('COALESCE(referrals.student_name, students.name) as student_name'),
                DB::raw('COALESCE(referrals.student_number, students.student_number) as student_number'),
                'teachers.name as teacher_name'
            )
            ->orderBy('referrals.created_at', 'desc');

        if ($tab === 'pending') {
            $query->where('referrals.status', 'Pending');
        } elseif ($tab === 'accepted') {
            $query->where('referrals.status', 'Accepted');
        }

        $referrals = $query->get();

        return view('counselor.referrals.index', compact('referrals', 'tab'));
    }

    public function allRequests(Request $request)
    {
        $tab = $request->get('tab', 'all'); // all | pending | accepted

        $query = DB::table('counseling_requests')
            ->leftJoin('users', 'counseling_requests.student_id', '=', 'users.id')
            ->select(
                'counseling_requests.*',
                DB::raw('COALESCE(counseling_requests.student_name, users.name) as student_name'),
                DB::raw('COALESCE(counseling_requests.student_number, users.student_number) as student_number')
            )
            ->orderBy('counseling_requests.created_at', 'desc');

        if ($tab === 'pending') {
            $query->where('counseling_requests.status', 'Pending');
        } elseif ($tab === 'accepted') {
            $query->where('counseling_requests.status', 'Accepted');
        }

        $requests = $query->get();

        return view('counselor.requests.index', compact('requests', 'tab'));
    }

    public function createFromReferral($id)
    {
        $referral = DB::table('referrals')->where('id', $id)->first();

        if (!$referral) {
            return back()->with('error', 'Referral not found.');
        }

        $studentId = $referral->student_id;

        if (!$studentId && !empty($referral->student_number)) {
            $matched = DB::table('users')
                ->where('role', 'student')
                ->where('student_number', $referral->student_number)
                ->first();
            $studentId = $matched->id ?? null;
        }

        $caseNumber = 'CASE-' . strtoupper(Str::random(6));

        $caseId = DB::table('cases')->insertGetId([
            'case_number'    => $caseNumber,
            'student_id'     => $studentId,
            'student_name'   => $referral->student_name ?? null,
            'student_number' => $referral->student_number ?? null,
            'year_section'   => $referral->year_section ?? null,
            'counselor_id'   => Auth::id(),
            'referral_id'    => $referral->id,
            'concern_type'   => $referral->concern_type,
            'summary'        => $referral->description,
            'status'         => 'Ongoing',
            'opened_at'      => now(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        DB::table('referrals')->where('id', $id)->update([
            'status' => 'Accepted',
            'updated_at' => now(),
        ]);

        return redirect()->route('counselor.cases.show', $caseId)
            ->with('success', 'Case created successfully from referral.');
    }

    public function createFromRequest($id)
    {
        $request = DB::table('counseling_requests')->where('id', $id)->first();

        if (!$request) {
            return redirect()->route('counselor.pending')->with('error', 'Request not found.');
        }

        $caseNumber = 'CASE-' . strtoupper(Str::random(6));

        $caseId = DB::table('cases')->insertGetId([
            'case_number'   => $caseNumber,
            'student_id'    => $request->student_id,
            'counselor_id'  => Auth::id(),
            'request_id'    => $request->id,
            'referral_id'   => null,
            'concern_type'  => $request->concern_type,
            'summary'       => $request->description,
            'status'        => 'Ongoing',
            'student_name'  => $request->student_name ?? null,
            'student_number'=> $request->student_number ?? null,
            'year_section'  => $request->year_section ?? null,
            'opened_at'     => now(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('counseling_requests')->where('id', $id)->update([
            'status'     => 'Accepted',
            'updated_at' => now(),
        ]);

        // Prefer student-requested time → go straight to schedule form
        if (!empty($request->preferred_datetime)) {
            return redirect()
                ->route('counselor.appointments.create', $caseId)
                ->with('preferred_datetime', $request->preferred_datetime)
                ->with('success', 'Case created. Preferred time loaded — confirm or change the appointment.');
        }

        return redirect()
            ->route('counselor.cases.show', $caseId)
            ->with('success', 'Case created successfully from request.');
    }

    public function storeNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|min:3',
        ]);

        DB::table('case_notes')->insert([
            'case_id'      => $id,
            'counselor_id' => auth()->id(),
            'note'         => $request->note,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return back()->with('success', 'Note added successfully.');
    }

    public function officeReferrals()
    {
        $officeReferrals = DB::table('office_referrals')
            ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select(
                'office_referrals.id',
                'office_referrals.office',
                'office_referrals.status',
                'office_referrals.reason',
                'office_referrals.created_at',
                'office_referrals.case_id',
                DB::raw("COALESCE(cases.case_number, CAST(office_referrals.case_id AS TEXT), '—') as case_number"),
                DB::raw("COALESCE(cases.student_name, users.name, 'Unknown') as student_name"),
                DB::raw('COALESCE(cases.student_number, users.student_number) as student_number'),
                'cases.concern_type'
            )
            ->orderBy('office_referrals.created_at', 'desc')
            ->get();

        return view('counselor.office_referrals.index', compact('officeReferrals'));
    }

    public function showOfficeReferral($id)
    {
        $referral = DB::table('office_referrals')
            ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select(
                'office_referrals.*',
                'cases.case_number',
                'cases.concern_type',
                'cases.status as case_status',
                DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
                DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
            )
            ->where('office_referrals.id', $id)
            ->first();

        if (!$referral) {
            return redirect()->route('counselor.office_referrals')->with('error', 'Referral not found.');
        }

        return view('counselor.office_referrals.show', compact('referral'));
    }

    public function showPendingReferral($id)
    {
        $referral = DB::table('referrals')
            ->leftJoin('users as students', 'referrals.student_id', '=', 'students.id')
            ->leftJoin('users as teachers', 'referrals.teacher_id', '=', 'teachers.id')
            ->select(
                'referrals.*',
                DB::raw('COALESCE(referrals.student_name, students.name) as student_name'),
                DB::raw('COALESCE(referrals.student_number, students.student_number) as student_number'),
                'teachers.name as teacher_name'
            )
            ->where('referrals.id', $id)
            ->first();

        if (!$referral) {
            return redirect()->route('counselor.pending')->with('error', 'Referral not found.');
        }

        return view('counselor.cases.pending_referral', compact('referral'));
    }

    public function showPendingRequest($id)
    {
        $request = DB::table('counseling_requests')
            ->leftJoin('users', 'counseling_requests.student_id', '=', 'users.id')
            ->select(
                'counseling_requests.*',
                DB::raw('COALESCE(counseling_requests.student_name, users.name) as student_name'),
                DB::raw('COALESCE(counseling_requests.student_number, users.student_number) as student_number')
            )
            ->where('counseling_requests.id', $id)
            ->first();

        if (!$request) {
            return redirect()->route('counselor.pending')->with('error', 'Request not found.');
        }

        return view('counselor.cases.pending_request', compact('request'));
    }

    public function showClinicReferral($id)
    {
        $referral = DB::table('office_referrals')
            ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select(
                'office_referrals.id',
                'office_referrals.case_id',
                'office_referrals.office',
                'office_referrals.reason',
                'office_referrals.notes_for_office',
                'office_referrals.status',
                'office_referrals.created_at',
                'office_referrals.updated_at',
                'cases.case_number',
                'cases.concern_type',
                DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
                DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
            )
            ->where('office_referrals.id', $id)
            ->where('office_referrals.office', 'Clinic')
            ->first();

        if (!$referral) {
            return redirect()->route('clinic.dashboard')->with('error', 'Referral not found.');
        }

        return view('clinic.referrals.show', compact('referral'));
    }

    public function showDisciplinaryReferral($id)
    {
        $referral = DB::table('office_referrals')
            ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select(
                'office_referrals.id',
                'office_referrals.case_id',
                'office_referrals.office',
                'office_referrals.reason',
                'office_referrals.notes_for_office',
                'office_referrals.status',
                'office_referrals.created_at',
                'office_referrals.updated_at',
                'cases.case_number',
                'cases.concern_type',
                DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
                DB::raw('COALESCE(cases.student_number, users.student_number) as student_number'),
                'cases.year_section'
            )
            ->where('office_referrals.id', $id)
            ->where('office_referrals.office', 'Disciplinary')
            ->first();

        if (!$referral) {
            return redirect()->route('disciplinary.dashboard')->with('error', 'Referral not found.');
        }

        return view('disciplinary.referrals.show', compact('referral'));
    }

    public function clinicReferralsIndex()
    {
        $referrals = DB::table('office_referrals')
            ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select(
                'office_referrals.id',
                'office_referrals.case_id',
                'office_referrals.office',
                'office_referrals.reason',
                'office_referrals.status',
                'office_referrals.created_at',
                'office_referrals.updated_at',
                'cases.case_number',
                DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
                DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
            )
            ->where('office_referrals.office', 'Clinic')
            ->orderBy('office_referrals.created_at', 'desc')
            ->get();

        return view('clinic.referrals.index', compact('referrals'));
    }

    public function disciplinaryReferralsIndex()
    {
        $referrals = DB::table('office_referrals')
            ->leftJoin('cases', 'office_referrals.case_id', '=', 'cases.id')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select(
                'office_referrals.id',
                'office_referrals.case_id',
                'office_referrals.office',
                'office_referrals.reason',
                'office_referrals.status',
                'office_referrals.created_at',
                'office_referrals.updated_at',
                'cases.case_number',
                DB::raw('COALESCE(cases.student_name, users.name) as student_name'),
                DB::raw('COALESCE(cases.student_number, users.student_number) as student_number')
            )
            ->where('office_referrals.office', 'Disciplinary')
            ->orderBy('office_referrals.created_at', 'desc')
            ->get();

        return view('disciplinary.referrals.index', compact('referrals'));
    }

    public function pendingReferralsAll(Request $request)
    {
        $level = $request->query('level', 'all');

        $query = DB::table('referrals')
            ->leftJoin('users as students', 'referrals.student_id', '=', 'students.id')
            ->leftJoin('users as teachers', 'referrals.teacher_id', '=', 'teachers.id')
            ->leftJoin('courses', 'referrals.course_id', '=', 'courses.id')
            ->select(
                'referrals.*',
                DB::raw('COALESCE(referrals.student_name, students.name) as student_name'),
                DB::raw('COALESCE(referrals.student_number, students.student_number) as student_number'),
                'teachers.name as teacher_name',
                'courses.level as education_level'
            )
            ->where('referrals.status', 'Pending');

        if ($level === 'Tertiary' || $level === 'SHS') {
            $query->where('courses.level', $level);
        }

        $referrals = $query->orderBy('referrals.created_at', 'desc')->get();

        return view('counselor.cases.pending_referrals', compact('referrals', 'level'));
    }

    public function pendingRequestsAll(Request $request)
    {
        $level = $request->query('level', 'all');

        $query = DB::table('counseling_requests')
            ->leftJoin('users', 'counseling_requests.student_id', '=', 'users.id')
            ->leftJoin('courses', 'users.course_id', '=', 'courses.id')
            ->select(
                'counseling_requests.*',
                DB::raw('COALESCE(counseling_requests.student_name, users.name) as student_name'),
                DB::raw('COALESCE(counseling_requests.student_number, users.student_number) as student_number'),
                'courses.level as education_level'
            )
            ->where('counseling_requests.status', 'Pending');

        if ($level === 'Tertiary' || $level === 'SHS') {
            $query->where('courses.level', $level);
        }

        $requests = $query->orderBy('counseling_requests.created_at', 'desc')->get();

        return view('counselor.cases.pending_requests', compact('requests', 'level'));
    }

    public function destroy($id)
    {
        if (\Schema::hasTable('case_notes')) {
            DB::table('case_notes')->where('case_id', $id)->delete();
        }
        if (\Schema::hasTable('appointments')) {
            DB::table('appointments')->where('case_id', $id)->delete();
        }
        if (\Schema::hasTable('office_referrals')) {
            DB::table('office_referrals')->where('case_id', $id)->delete();
        }

        DB::table('cases')->where('id', $id)->delete();

        return redirect()->route('counselor.cases')->with('success', 'Case deleted successfully.');
    }

    public function destroyOfficeReferral($id)
    {
        DB::table('office_referrals')->where('id', $id)->delete();
        return back()->with('success', 'Office referral deleted.');
    }

    public function destroyPendingReferral($id)
    {
        DB::table('referrals')->where('id', $id)->delete();
        return back()->with('success', 'Teacher referral deleted.');
    }

    public function destroyPendingRequest($id)
    {
        DB::table('counseling_requests')->where('id', $id)->delete();
        return back()->with('success', 'Student request deleted.');
    }

    public function create()
    {
        $students = DB::table('users')
            ->where('role', 'student')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'student_number']);

        return view('counselor.cases.create', compact('students'));
    }

    public function store(Request $request)
    {
        // Cases always belong to a school-provisioned student account.
        $validated = $request->validate([
            'student_id'   => [
                'required',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('role', 'student'),
            ],
            'year_section' => 'nullable|string|max:100',
            'concern_type' => 'required|string|max:100',
            'summary'      => 'required|string|min:10',
        ], [
            'student_id.required' => 'Please select the student from the list.',
            'student_id.exists'   => 'Please select a registered student from the list.',
        ]);

        $student = DB::table('users')->where('id', $validated['student_id'])->first();

        $caseNumber = 'CASE-' . strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));

        $id = DB::table('cases')->insertGetId([
            'case_number'    => $caseNumber,
            'student_id'     => $student->id,
            'counselor_id'   => auth()->id(),
            'student_name'   => $student->name,
            'student_number' => $student->student_number,
            'year_section'   => $validated['year_section'] ?? null,
            'concern_type'   => $validated['concern_type'],
            'summary'        => $validated['summary'],
            'status'         => 'Ongoing',
            'opened_at'      => now(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->route('counselor.cases.show', $id)
            ->with('success', 'Case created successfully.');
    }

    public function declineReferral($id)
    {
        $updated = \Illuminate\Support\Facades\DB::table('referrals')
            ->where('id', $id)
            ->where('status', 'Pending')
            ->update([
                'status' => 'Closed', // 'Declined' is not allowed by the status column; Closed = declined/handled
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return back()->with('error', 'Referral not found or already handled.');
        }

        return back()->with('success', 'Teacher referral declined and closed.');
    }

    public function declineRequest($id)
    {
        $updated = \Illuminate\Support\Facades\DB::table('counseling_requests')
            ->where('id', $id)
            ->where('status', 'Pending')
            ->update([
                'status' => 'Closed', // 'Declined' is not allowed by the status column; Closed = declined/handled
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return back()->with('error', 'Request not found or already handled.');
        }

        return back()->with('success', 'Student counseling request declined and closed.');
    }
}