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

    public $referral;

    public function __construct($referral)
    {
        $this->referral = $referral;
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