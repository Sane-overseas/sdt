<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainerRegistrationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $trainerName,
        public string $instructorCode,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Received - Waiting for Approval | SOPL',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trainer-registration-received',
        );
    }
}
