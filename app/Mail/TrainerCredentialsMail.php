<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TrainerCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $trainerName,
        public string $email,
        public string $trainerCode,
        public string $plainPassword,
        public string $loginUrl,
        public ?string $idCardPath = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Trainer Account Credentials & ID Card - SOPL',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trainer-credentials',
        );
    }

    public function attachments(): array
    {
        if (!$this->idCardPath || !Storage::disk('public')->exists($this->idCardPath)) {
            return [];
        }

        $filename = 'SOPL_ID_Card_'.$this->trainerCode.'.png';

        return [
            Attachment::fromStorageDisk('public', $this->idCardPath)
                ->as($filename)
                ->withMime('image/png'),
        ];
    }
}
