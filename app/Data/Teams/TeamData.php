<?php

declare(strict_types=1);

namespace App\Data\Teams;

use App\Data\Billing\SubscriptionData;
use App\Models\Team;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class TeamData extends Data
{
    /**
     * @param  Lazy|Collection<int, TeamInvitationData>  $invitations
     */
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly DateTime $createdAt,
        public readonly string $name,
        public readonly Lazy|TeamMemberData $owner,
        public readonly Lazy|Collection $invitations,
        public readonly Lazy|null|SubscriptionData $subscription,
    ) {}

    public static function fromModel(Team $team): self
    {
        return new self(
            id: $team->id,
            name: $team->name,
            userId: $team->user_id,
            createdAt: $team->created_at,
            owner: Lazy::whenLoaded('owner', $team, fn () => TeamMemberData::from($team->owner)),
            invitations: Lazy::whenLoaded('users', $team, fn () => TeamInvitationData::collect($team->teamInvitations)),
            subscription: Lazy::whenLoaded('defaultSubscription', $team, fn (): ?SubscriptionData => $team->defaultSubscription?->valid() ? SubscriptionData::fromModel($team->defaultSubscription) : null),

        );
    }
}
