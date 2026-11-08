<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\ReferralAccepted;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TeacherReferralController extends Controller
{
    public function index()
    {
        return view('student.teacher_referrals.index');
    }

    public function show($id)
    {
        $studentId = Auth::id();
        $studentNumber = Auth::user()->student_number ?? null;

        $referral = DB::table('referrals')
            ->leftJoin('users as teachers', 'referrals.teacher_id', '=', 'teachers.id')
            ->select('referrals.*', 'teachers.name as teacher_name')
            ->where('referrals.id', $id)
            ->where(function ($q) use ($studentId, $studentNumber) {
                $q->where('referrals.student_id', $studentId);
                if ($studentNumber) {
                    $q->orWhere('referrals.student_number', $studentNumber);
                }
            })
            ->first();

        if (!$referral) {
            return redirect()->route('student.teacher_referrals')
                ->with('error', 'Referral not found.');
        }

        return view('student.teacher_referrals.show', compact('referral'));
    }

    public function respond(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|in:Accepted,Declined',
        ]);

        $studentId = Auth::id();
        $studentNumber = Auth::user()->student_number ?? null;

        $updated = DB::table('referrals')
            ->where('id', $id)
            ->where(function ($q) use ($studentId, $studentNumber) {
                $q->where('student_id', $studentId);
                if ($studentNumber) {
                    $q->orWhere('student_number', $studentNumber);
                }
            })
            ->update([
                'student_response' => $request->response,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return redirect()->route('student.teacher_referrals')
                ->with('error', 'Referral not found.');
        }

        // Optional: email counselors when student accepts
        if ($request->response === 'Accepted') {
            $referralData = DB::table('referrals')
                ->leftJoin('users as students', 'referrals.student_id', '=', 'students.id')
                ->leftJoin('users as teachers', 'referrals.teacher_id', '=', 'teachers.id')
                ->select(
                    'referrals.*',
                    DB::raw('COALESCE(referrals.student_name, students.name) as student_name'),
                    'teachers.name as teacher_name'
                )
                ->where('referrals.id', $id)
                ->first();

            if ($referralData) {
                $counselors = User::where('role', 'counselor')->get();
                foreach ($counselors as $counselor) {
                    if (!empty($counselor->email)) {
                        try {
                            Mail::to($counselor->email)->send(new ReferralAccepted($referralData));
                        } catch (\Exception $e) {
                            // ignore mail errors
                        }
                    }
                }
            }
        }

        return redirect()->route('student.teacher_referrals.show', $id)
            ->with('success', 'Referral ' . strtolower($request->response) . '.');
    }
}