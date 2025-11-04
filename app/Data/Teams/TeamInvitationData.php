<?php

declare(strict_types=1);

namespace App\Data\Teams;

use App\Enums\Teams\RoleEnum;
use App\Models\TeamInvitation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class TeamInvitationData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly ?RoleEnum $role,
        public readonly Lazy|null|TeamData $team,
    ) {}

    public static function fromModel(TeamInvitation $invitation): self
    {
        return new self(
            id: $invitation->id,
            email: $invitation->email,
            role: $invitation->role,
            team: Lazy::whenLoaded('team', $invitation, fn () => TeamData::from($invitation->team)),
        );
    }
}
