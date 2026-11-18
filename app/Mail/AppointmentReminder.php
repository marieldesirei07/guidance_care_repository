<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    /** Values read by resources/views/emails/appointment_reminder.blade.php as $data['key']. */
    public array $data;

    public function __construct($appointment, $studentName, $caseNumber = null)
    {
        $this->data = [
            'student_name' => $studentName,
            'datetime'     => data_get($appointment, 'appointment_datetime'),
            'location'     => data_get($appointment, 'location'),
            'purpose'      => data_get($appointment, 'purpose'),
            'case_number'  => $caseNumber,
        ];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Upcoming GuidanceCare Appointment',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment_reminder',
        );
    }
}
