<?php

namespace App\Notifications;

use App\Models\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class TeamInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly TeamInvitation $invitation
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = URL::temporarySignedRoute(
            'team.invitations.mail.accept',
            TeamInvitation::defaultExpiresAt(),
            ['invitation' => $this->invitation],
        );

        return (new MailMessage)
            ->subject(Lang::get('Team Invitation'))
            ->line(Lang::get('You have been invited to join the **:team** team!', ['team' => $this->invitation->team->name]))
            ->line(Lang::get('If you do not have an account, you may create one by clicking the button below.'))
            ->line(Lang::get('If you already have an account, you may accept this invitation by clicking the button below:'))
            ->line(Lang::get('This invitation will expire in :days days.', ['days' => TeamInvitation::DEFAULT_EXPIRATION_DAYS]))
            ->action(Lang::get('Accept Invitation'), $acceptUrl)
            ->line(Lang::get('If you did not expect to receive an invitation to this team, you may discard this email.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
