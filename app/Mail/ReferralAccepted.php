<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralAccepted extends Mailable
{
    use Queueable, SerializesModels;

    /** Values read by resources/views/emails/referral_accepted.blade.php as $data['key']. */
    public array $data;

    public function __construct($referral, $counselorName = null)
    {
        $this->data = [
            'counselor_name' => $counselorName ?: 'Counselor',
            'student_name'   => data_get($referral, 'student_name'),
            'concern_type'   => data_get($referral, 'concern_type'),
            'teacher_name'   => data_get($referral, 'teacher_name'),
        ];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Student Accepted Guidance Referral',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.referral_accepted',
        );
    }
}
