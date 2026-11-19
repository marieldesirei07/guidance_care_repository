<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Sample guidance data for the defense demo.
 * Needs DemoUsersSeeder first. Safe to run more than once: it stops if the demo cases already exist.
 *
 * Every referral and case links to a school-provisioned student account (no unregistered students).
 *
 * What it creates:
 *  - 2 pending teacher referrals (Ana Ramos, Juan Dela Cruz)
 *  - 1 pending counseling request (Ana Ramos, with a preferred time)
 *  - CASE-DEMO01: Ana Ramos, from a counseling request, with a note and a scheduled appointment
 *  - CASE-DEMO02: Maria Santos, from a teacher referral, referred to the Clinic
 *  - CASE-DEMO03: Paolo Reyes, opened by the counselor, referred to the Disciplinary Office
 *  - Appointment requests: Juan Dela Cruz (pending, no case yet), Maria Santos (declined with remarks)
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('cases')->where('case_number', 'CASE-DEMO01')->exists()) {
            return;
        }

        $user = fn ($email) => DB::table('users')->where('email', $email . '@globalcity.sti.edu.ph')->first();

        $student   = $user('ramos.356101');
        $juan      = $user('delacruz.111222');
        $maria     = $user('santos.333444');
        $paolo     = $user('reyes.555666');
        $teacher   = $user('carlo.villanueva');
        $counselor = $user('grace.lim');

        if (!$student || !$juan || !$maria || !$paolo || !$teacher || !$counselor) {
            return; // demo accounts missing; run DemoUsersSeeder first
        }

        $now = now();
        $ts  = fn ($daysAgo) => $now->copy()->subDays($daysAgo);

        // ---- Pending items for the counselor queue ----
        DB::table('referrals')->insert([
            'teacher_id'       => $teacher->id,
            'student_id'       => $student->id,
            'student_name'     => $student->name,
            'student_number'   => $student->student_number,
            'year_section'     => 'BSIT 3rd Year - A507',
            'concern_type'     => 'Academic',
            'description'      => 'Has missed several quizzes and seems distracted in class. Grades dropped this term.',
            'urgency'          => 'High',
            'status'           => 'Pending',
            'student_response' => 'Pending',
            'created_at'       => $ts(1),
            'updated_at'       => $ts(1),
        ]);

        DB::table('referrals')->insert([
            'teacher_id'       => $teacher->id,
            'student_id'       => $juan->id,
            'student_name'     => $juan->name,
            'student_number'   => $juan->student_number,
            'year_section'     => 'BSIT 2nd Year - A201',
            'concern_type'     => 'Behavioral',
            'description'      => 'Frequently arrives late and has had two arguments with classmates this week.',
            'urgency'          => 'Medium',
            'status'           => 'Pending',
            'student_response' => 'Pending',
            'created_at'       => $ts(2),
            'updated_at'       => $ts(2),
        ]);

        DB::table('counseling_requests')->insert([
            'student_id'         => $student->id,
            'student_name'       => $student->name,
            'student_number'     => $student->student_number,
            'year_section'       => 'BSIT 3rd Year - A507',
            'concern_type'       => 'Personal',
            'description'        => 'I would like to talk to someone about managing stress from family responsibilities.',
            'preferred_datetime' => $now->copy()->addDays(3)->setTime(14, 0),
            'status'             => 'Pending',
            'created_at'         => $ts(0),
            'updated_at'         => $ts(0),
        ]);

        // ---- CASE-DEMO01: registered student, from a counseling request ----
        $requestId = DB::table('counseling_requests')->insertGetId([
            'student_id'     => $student->id,
            'student_name'   => $student->name,
            'student_number' => $student->student_number,
            'year_section'   => 'BSIT 3rd Year - A507',
            'concern_type'   => 'Academic',
            'description'    => 'I am struggling to keep up with my thesis deadlines and need advice on time management.',
            'status'         => 'Accepted',
            'created_at'     => $ts(6),
            'updated_at'     => $ts(5),
        ]);

        $case1 = DB::table('cases')->insertGetId([
            'case_number'    => 'CASE-DEMO01',
            'student_id'     => $student->id,
            'student_name'   => $student->name,
            'student_number' => $student->student_number,
            'year_section'   => 'BSIT 3rd Year - A507',
            'counselor_id'   => $counselor->id,
            'request_id'     => $requestId,
            'concern_type'   => 'Academic',
            'summary'        => 'Student requested help with time management for thesis deadlines.',
            'status'         => 'Ongoing',
            'opened_at'      => $ts(5),
            'created_at'     => $ts(5),
            'updated_at'     => $ts(5),
        ]);

        DB::table('case_notes')->insert([
            'case_id'      => $case1,
            'counselor_id' => $counselor->id,
            'note'         => 'Initial interview done. Student agreed to try a weekly planner. Follow-up session scheduled.',
            'created_at'   => $ts(4),
            'updated_at'   => $ts(4),
        ]);

        DB::table('appointments')->insert([
            'case_id'              => $case1,
            'student_id'           => $student->id,
            'counselor_id'         => $counselor->id,
            'appointment_datetime' => $now->copy()->addDays(2)->setTime(10, 0),
            'location'             => 'Guidance Office',
            'purpose'              => 'Follow-up session on time management.',
            'status'               => 'Scheduled',
            'created_at'           => $ts(4),
            'updated_at'           => $ts(4),
        ]);

        // ---- CASE-DEMO02: Maria Santos, from a teacher referral, sent to the Clinic ----
        $referralId = DB::table('referrals')->insertGetId([
            'teacher_id'       => $teacher->id,
            'student_id'       => $maria->id,
            'student_name'     => $maria->name,
            'student_number'   => $maria->student_number,
            'year_section'     => 'BSHM 1st Year - B105',
            'concern_type'     => 'Emotional',
            'description'      => 'Appeared very tired and anxious in class; mentioned not sleeping well for weeks.',
            'urgency'          => 'High',
            'status'           => 'Accepted',
            'student_response' => 'Pending',
            'created_at'       => $ts(5),
            'updated_at'       => $ts(4),
        ]);

        $case2 = DB::table('cases')->insertGetId([
            'case_number'    => 'CASE-DEMO02',
            'student_id'     => $maria->id,
            'student_name'   => $maria->name,
            'student_number' => $maria->student_number,
            'year_section'   => 'BSHM 1st Year - B105',
            'counselor_id'   => $counselor->id,
            'referral_id'    => $referralId,
            'concern_type'   => 'Emotional',
            'summary'        => 'Teacher referral: signs of fatigue and anxiety.',
            'status'         => 'Referred',
            'opened_at'      => $ts(4),
            'created_at'     => $ts(4),
            'updated_at'     => $ts(3),
        ]);

        DB::table('case_notes')->insert([
            'case_id'      => $case2,
            'counselor_id' => $counselor->id,
            'note'         => 'CONFIDENTIAL: Student shared family-related stress. Recommended a health check for sleep problems.',
            'created_at'   => $ts(3),
            'updated_at'   => $ts(3),
        ]);

        DB::table('office_referrals')->insert([
            'case_id'          => $case2,
            'referred_by'      => $counselor->id,
            'office'           => 'Clinic',
            'reason'           => 'Please assess the student for possible sleep problems and fatigue.',
            'notes_for_office' => 'Student is aware of the referral and agreed to visit the Clinic this week.',
            'status'           => 'Pending',
            'created_at'       => $ts(3),
            'updated_at'       => $ts(3),
        ]);

        // ---- CASE-DEMO03: Paolo Reyes, opened by the counselor, sent to Disciplinary ----
        $case3 = DB::table('cases')->insertGetId([
            'case_number'    => 'CASE-DEMO03',
            'student_id'     => $paolo->id,
            'student_name'   => $paolo->name,
            'student_number' => $paolo->student_number,
            'year_section'   => 'BSCS 2nd Year - C302',
            'counselor_id'   => $counselor->id,
            'concern_type'   => 'Behavioral',
            'summary'        => 'Walk-in: involved in a hallway altercation reported by security.',
            'status'         => 'Referred',
            'opened_at'      => $ts(2),
            'created_at'     => $ts(2),
            'updated_at'     => $ts(1),
        ]);

        DB::table('case_notes')->insert([
            'case_id'      => $case3,
            'counselor_id' => $counselor->id,
            'note'         => 'CONFIDENTIAL: Student admitted to the incident and expressed regret. No injuries reported.',
            'created_at'   => $ts(1),
            'updated_at'   => $ts(1),
        ]);

        DB::table('office_referrals')->insert([
            'case_id'          => $case3,
            'referred_by'      => $counselor->id,
            'office'           => 'Disciplinary',
            'reason'           => 'Hallway altercation reported by security; please handle per the student handbook.',
            'notes_for_office' => 'Counseling session already held; student was cooperative.',
            'status'           => 'Pending',
            'created_at'       => $ts(1),
            'updated_at'       => $ts(1),
        ]);

        // ---- Appointment requests (before a case or schedule exists) ----
        DB::table('appointment_requests')->insert([
            'student_id'         => $juan->id,
            'case_id'            => null,
            'preferred_datetime' => $now->copy()->addDays(4)->setTime(15, 0),
            'purpose'            => 'I would like to talk about my schedule and being late to class.',
            'status'             => 'Pending',
            'created_at'         => $ts(0),
            'updated_at'         => $ts(0),
        ]);

        DB::table('appointment_requests')->insert([
            'student_id'         => $maria->id,
            'case_id'            => $case2,
            'preferred_datetime' => $now->copy()->addDays(1)->setTime(7, 0),
            'purpose'            => 'Follow-up after my clinic visit.',
            'status'             => 'Declined',
            'counselor_id'       => $counselor->id,
            'counselor_remarks'  => 'The office opens at 8:00 AM. Please request a time between 8:00 AM and 5:00 PM.',
            'created_at'         => $ts(2),
            'updated_at'         => $ts(1),
        ]);
    }
}
