<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Mail\NewReferralForCounselor;
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
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('teacher.referrals.create', compact('courses', 'sections', 'students'));
    }

    public function store(Request $request)
    {
        // Every student has a school-provisioned account: the teacher picks one.
        $request->validate([
            'student_id'   => [
                'required',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('role', 'student'),
            ],
            'year_section' => 'nullable|string|max:100',
            'concern_type' => 'required|string|max:100',
            'urgency'      => 'required|in:Low,Medium,High',
            'description'  => 'required|string|min:10',
        ], [
            'student_id.required' => 'Please select the student from the list.',
            'student_id.exists'   => 'Please select a registered student from the list.',
        ]);

        $student = DB::table('users')->where('id', $request->student_id)->first();

        DB::table('referrals')->insert([
            'teacher_id'     => auth()->id(),
            'student_id'     => $student->id,
            'student_name'   => $student->name,           // copy kept for display/history
            'student_number' => $student->student_number,
            'year_section'   => $request->year_section,
            'concern_type'   => $request->concern_type,
            'description'    => $request->description,
            'urgency'        => $request->urgency,
            'status'         => 'Pending',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $emailNote = $this->sendReferralEmails($student, $request);

        return redirect()->route('teacher.dashboard')
            ->with('success', 'Referral submitted successfully.' . $emailNote);
    }

    /**
     * Notify the student (to accept or decline) and every counselor (new item in the Pending Queue).
     * Mail problems never block the referral itself.
     */
    private function sendReferralEmails($student, Request $request): string
    {
        $teacherName = auth()->user()->name;
        $gapMs       = (int) config('guidancecare.mail_gap_ms', 0);
        $sent        = 0;
        $failed      = 0;

        $send = function (callable $mail) use (&$sent, &$failed, $gapMs) {
            if ($sent + $failed > 0 && $gapMs > 0) {
                usleep($gapMs * 1000);
            }
            try {
                $mail();
                $sent++;
            } catch (\Throwable $e) {
                // Mailtrap's free sandbox refuses bursts ("Too many emails per second"): wait and retry once.
                if (str_contains($e->getMessage(), 'Too many emails')) {
                    usleep(2500 * 1000);
                    try {
                        $mail();
                        $sent++;
                        return;
                    } catch (\Throwable $retryError) {
                        $e = $retryError;
                    }
                }
                $failed++;
                report($e);
            }
        };

        // Student copy is optional (see config/guidancecare.php: referral_email_student).
        if (config('guidancecare.referral_email_student') && !empty($student->email)) {
            $send(fn () => Mail::to($student->email)->send(new TeacherReferralNotification(
                ['concern_type' => $request->concern_type, 'urgency' => $request->urgency],
                $teacherName,
                $student->name
            )));
        }

        $counselors = DB::table('users')->where('role', 'counselor')->whereNotNull('email')->get(['name', 'email']);
        foreach ($counselors as $counselor) {
            $send(fn () => Mail::to($counselor->email)->send(new NewReferralForCounselor([
                'counselor_name' => $counselor->name,
                'student_name'   => $student->name,
                'student_number' => $student->student_number,
                'teacher_name'   => $teacherName,
                'concern_type'   => $request->concern_type,
                'urgency'        => $request->urgency,
                'pending_url'    => route('counselor.pending'),
            ])));
        }

        if ($failed > 0) {
            return " ({$failed} notification email(s) could not be sent.)";
        }

        if ($sent === 0) {
            return '';
        }

        return config('guidancecare.referral_email_student')
            ? ' The student and the guidance office were notified by email.'
            : ' The guidance office was notified by email.';
    }

    public function index()
    {
        $teacherId = auth()->id();

        $referrals = DB::table('referrals')
            ->leftJoin('users', 'referrals.student_id', '=', 'users.id')
            ->select(
                'referrals.*',
                DB::raw("COALESCE(users.name, referrals.student_name, 'Unknown') as student_name"),
                DB::raw("COALESCE(users.student_number, referrals.student_number, '') as student_number")
            )
            ->where('referrals.teacher_id', $teacherId)
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
                DB::raw("COALESCE(users.name, referrals.student_name, 'Unknown') as student_name"),
                DB::raw("COALESCE(users.student_number, referrals.student_number, '') as student_number")
            )
            ->where('referrals.id', $id)
            ->where('referrals.teacher_id', auth()->id())
            ->first();

        if (!$referral) {
            return redirect()->route('teacher.dashboard')->with('error', 'Referral not found.');
        }

        $yearSection = $referral->year_section ?? '';

        return view('teacher.referrals.show', compact('referral', 'yearSection'));
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