<?php

declare(strict_types=1);

namespace App\Data\Auth;

use App\Data\AppNotifications\AppNotificationData;
use App\Data\Teams\TeamData;
use App\Models\User;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserData extends Data
{
    /**
     * @param  Lazy|Collection<int, TeamData>  $teams
     * @param  Lazy|Collection<int, AppNotificationData>  $notifications
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $isAdmin,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly DateTime $createdAt,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?DateTime $emailVerifiedAt,
        public readonly ?string $neutralColor,
        public readonly ?int $currentTeamId,
        public readonly ?string $primaryColor,
        public readonly ?string $secondaryColor,
        public bool $hasPassword,
        public readonly Lazy|null|TeamData $currentTeam,
        /** @var Lazy|Collection<int, TeamData> */
        public readonly Lazy|Collection $teams,
        public readonly Lazy|Collection $notifications,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            isAdmin: $user->is_admin,
            createdAt: $user->created_at,
            emailVerifiedAt: $user->email_verified_at,
            neutralColor: $user->neutral_color,
            primaryColor: $user->primary_color,
            secondaryColor: $user->secondary_color,
            currentTeamId: $user->current_team_id,
            hasPassword: $user->hasPassword(),
            currentTeam: Lazy::whenLoaded('currentTeam', $user, fn () => TeamData::optional($user->currentTeam)),
            teams: Lazy::whenLoaded('teams', $user, fn () => TeamData::collect($user->teams)),
            notifications: Lazy::whenLoaded('notifications', $user, fn () => AppNotificationData::collect($user->notifications)),
        );
    }
}
