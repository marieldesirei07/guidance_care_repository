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

    public $appointment;
    public $studentName;
    public $caseNumber;

    public function __construct($appointment, $studentName, $caseNumber)
    {
        $this->appointment = $appointment;
        $this->studentName = $studentName;
        $this->caseNumber = $caseNumber;
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
            with: [
                'appointment' => $this->appointment,
                'studentName' => $this->studentName,
                'caseNumber' => $this->caseNumber,
            ],
        );
    }
}