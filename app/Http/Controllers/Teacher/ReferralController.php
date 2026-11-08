<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Mail\TeacherReferralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class ReferralController extends Controller
{
    public function create()
    {
        $courses = collect();
        $sections = collect();
        $students = collect();

        if (Schema::hasTable('courses')) {
            $courses = DB::table('courses')->orderBy('level')->orderBy('name')->get();
        }

        if (Schema::hasTable('sections')) {
            $sections = DB::table('sections')->orderBy('year_level')->orderBy('name')->get();
        }

        $students = DB::table('users')
            ->where('role', 'student')
            ->orderBy('name')
            ->get();

        return view('teacher.referrals.create', compact('courses', 'sections', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_name'   => 'required|string|max:255',
            'student_number' => 'nullable|string|max:50',
            'program'        => 'required|string|max:100',
            'year_level'     => 'required|string|max:50',
            'section'        => 'required|string|max:20',
            'concern_type'   => 'required|string',
            'description'    => 'required|string|min:10',
            'urgency'        => 'required|in:Low,Medium,High',
        ]);

        // Combined label for lists/filters (e.g. "BSIT · 3rd Year · A301")
        $yearSection = $request->program . ' · ' . $request->year_level . ' · ' . strtoupper(trim($request->section));

        // Try to link a registered student by student_number (optional)
        $linkedStudent = null;
        if (!empty($request->student_number)) {
            $linkedStudent = DB::table('users')
                ->where('role', 'student')
                ->where('student_number', $request->student_number)
                ->first();
        }

        $data = [
            'teacher_id'     => Auth::id(),
            'student_id'     => $linkedStudent->id ?? null,
            'student_name'   => $request->student_name,
            'student_number' => $request->student_number,
            'year_section'   => $yearSection,
            'concern_type'   => $request->concern_type,
            'description'    => $request->description,
            'urgency'        => $request->urgency,
            'status'         => 'Pending',
            'created_at'     => now(),
            'updated_at'     => now(),
        ];

        // Only set these if the columns exist (keeps filter system compatible)
        if (Schema::hasColumn('referrals', 'year_level')) {
            $data['year_level'] = $request->year_level;
        }
        if (Schema::hasColumn('referrals', 'student_response')) {
            $data['student_response'] = 'Pending';
        }

        DB::table('referrals')->insert($data);

        // Email student only if linked account has email
        if ($linkedStudent && !empty($linkedStudent->email)) {
            try {
                $teacher = Auth::user();
                $referralData = (object) [
                    'concern_type' => $request->concern_type,
                    'urgency'      => $request->urgency,
                ];

                Mail::to($linkedStudent->email)->send(new TeacherReferralNotification(
                    $referralData,
                    $teacher->name,
                    $linkedStudent->name
                ));
            } catch (\Exception $e) {
                // Referral still succeeds if email fails
            }
        }

        return redirect()->route('teacher.dashboard')
            ->with('success', 'Referral submitted successfully.');
    }

    public function index()
    {
        $referrals = DB::table('referrals')
            ->leftJoin('users', 'referrals.student_id', '=', 'users.id')
            ->select(
                'referrals.*',
                DB::raw('COALESCE(referrals.student_name, users.name) as student_name'),
                DB::raw('COALESCE(referrals.student_number, users.student_number) as student_number')
            )
            ->where('referrals.teacher_id', auth()->id())
            ->orderBy('referrals.created_at', 'desc')
            ->get();

        return view('teacher.referrals.index', compact('referrals'));
    }

    public function show($id)
    {
        $referral = DB::table('referrals')
            ->leftJoin('users', 'referrals.student_id', '=', 'users.id')
            ->select(
                'referrals.*',
                DB::raw('COALESCE(referrals.student_name, users.name) as student_name'),
                DB::raw('COALESCE(referrals.student_number, users.student_number) as student_number')
            )
            ->where('referrals.id', $id)
            ->where('referrals.teacher_id', auth()->id())
            ->first();

        if (!$referral) {
            return redirect()->route('teacher.referrals')->with('error', 'Referral not found.');
        }

        return view('teacher.referrals.show', compact('referral'));
    }

    public function destroy($id)
    {
        DB::table('referrals')
            ->where('id', $id)
            ->where('teacher_id', auth()->id())
            ->delete();

        return back()->with('success', 'Referral deleted.');
    }
}