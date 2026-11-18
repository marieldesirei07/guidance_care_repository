<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeacherReferralNotification extends Mailable
{
    use Queueable, SerializesModels;

    /** Values read by resources/views/emails/teacher_referral.blade.php as $data['key']. */
    public array $data;

    public function __construct($referral, $teacherName, $studentName)
    {
        $this->data = [
            'student_name' => $studentName,
            'concern_type' => data_get($referral, 'concern_type'),
            'teacher_name' => $teacherName,
            'urgency'      => data_get($referral, 'urgency'),
        ];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'GuidanceCare: A teacher referred you for guidance support',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.teacher_referral',
        );
    }
}
