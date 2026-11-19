<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells a counselor that a teacher submitted a new referral.
 * Deliberately leaves out the teacher's description: details are read inside GuidanceCare.
 */
class NewReferralForCounselor extends Mailable
{
    use Queueable, SerializesModels;

    /** Values read by resources/views/emails/new_referral_counselor.blade.php as $data['key']. */
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data + [
            'counselor_name' => 'Counselor',
            'student_name'   => null,
            'student_number' => null,
            'teacher_name'   => null,
            'concern_type'   => null,
            'urgency'        => null,
            'pending_url'    => null,
        ];
    }

    public function envelope(): Envelope
    {
        $urgent = ($this->data['urgency'] ?? null) === 'High' ? ' (High urgency)' : '';

        return new Envelope(
            subject: 'GuidanceCare: New teacher referral' . $urgent,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new_referral_counselor',
        );
    }
}
