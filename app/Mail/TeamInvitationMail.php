<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TeamInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly TeamInvitation $invitation
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Team Invitation',
        );
    }

    public function content(): Content
    {
        $acceptUrl = URL::signedRoute('team.invitations.mail.accept', [
            'invitation' => $this->invitation,
        ]);

        return new Content(
            markdown: 'emails.team-invitation',
            with: [
                'acceptUrl' => $acceptUrl,
            ],
        );
    }
}
