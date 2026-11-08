<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send email reminders for appointments scheduled tomorrow';

        public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString(); // 2026-10-14

        $appointments = DB::table('appointments')
            ->leftJoin('users', 'appointments.student_id', '=', 'users.id')
            ->leftJoin('cases', 'appointments.case_id', '=', 'cases.id')
            ->select(
                'appointments.*',
                'users.name as student_name',
                'users.email as student_email',
                'cases.case_number'
            )
            ->whereDate('appointments.appointment_datetime', $tomorrow)
            ->where('appointments.status', 'Scheduled')
            ->whereNull('appointments.reminder_sent_at')
            ->whereNotNull('appointments.student_id')
            ->get();

        $sent = 0;

        foreach ($appointments as $apt) {
            if (empty($apt->student_email)) {
                continue;
            }

            try {
                Mail::to($apt->student_email)->send(new AppointmentReminder(
                    $apt,
                    $apt->student_name ?? 'Student',
                    $apt->case_number ?? 'N/A'
                ));

                DB::table('appointments')->where('id', $apt->id)->update([
                    'reminder_sent_at' => now(),
                ]);

                $sent++;
            } catch (\Exception $e) {
                $this->error('Failed for appointment #' . $apt->id . ': ' . $e->getMessage());
            }
        }

        $this->info("Sent {$sent} appointment reminder(s).");
        return Command::SUCCESS;
    }
}