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

    public $referral;
    public $teacherName;
    public $studentName;

    public function __construct($referral, $teacherName, $studentName)
    {
        $this->referral = $referral;
        $this->teacherName = $teacherName;
        $this->studentName = $studentName;
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
            with: [
                'referral' => $this->referral,
                'teacherName' => $this->teacherName,
                'studentName' => $this->studentName,
            ],
        );
    }
}