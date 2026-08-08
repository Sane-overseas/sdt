<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class AuthorizationLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $trainerName,
        public string $schoolName,
        public string $authLetterPath,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your School Authorization Letter - SOPL',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.authorization-letter',
        );
    }

    public function attachments(): array
    {
        if (!$this->authLetterPath || !Storage::disk('public')->exists($this->authLetterPath)) {
            return [];
        }

        return [
            Attachment::fromStorageDisk('public', $this->authLetterPath)
                ->as('SOPL_Authorization_Letter.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
