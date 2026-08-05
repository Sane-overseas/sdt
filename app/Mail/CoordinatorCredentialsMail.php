<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CoordinatorCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $coordinatorName,
        public string $email,
        public string $coordinatorCode,
        public string $plainPassword,
        public string $loginUrl,
        public ?string $idCardPath = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Coordinator Account Credentials - SOPL',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.coordinator-credentials',
        );
    }

    public function attachments(): array
    {
        if (!$this->idCardPath || !Storage::disk('public')->exists($this->idCardPath)) {
            return [];
        }

        return [
            Attachment::fromStorageDisk('public', $this->idCardPath)
                ->as('SOPL_ID_Card_'.$this->coordinatorCode.'.png')
                ->withMime('image/png'),
        ];
    }
}
