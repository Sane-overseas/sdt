<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainerRevisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $trainerName,
        public string $remarks,
        public string $editUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Correct Your Trainer Registration - SOPL',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trainer-revision',
        );
    }
}
