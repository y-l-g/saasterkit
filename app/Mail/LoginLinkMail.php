<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class LoginLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Login Link',
        );
    }

    public function content(): Content
    {
        $loginUrl = URL::temporarySignedRoute(
            'auth.login.link',
            now()->addMinutes(5),
            ['user' => $this->user->id]
        );

        return new Content(
            markdown: 'emails.login-link',
            with: [
                'loginUrl' => $loginUrl,
            ],
        );
    }
}
