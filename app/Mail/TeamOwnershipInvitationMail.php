<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\TeamOwnershipInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TeamOwnershipInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly TeamOwnershipInvitation $transfer
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Team Ownership Transfer Invitation',
        );
    }

    public function content(): Content
    {
        $acceptUrl = URL::temporarySignedRoute(
            'teams.ownership.invitations.mail.accept',
            now()->addDays(7),
            ['token' => $this->transfer->token]
        );

        return new Content(
            markdown: 'emails.team-ownership-transfer',
            with: [
                'acceptUrl' => $acceptUrl,
                'teamName' => $this->transfer->team->name,
                'currentOwnerName' => $this->transfer->team->owner->name,
            ],
        );
    }
}
