<?php

declare(strict_types=1);

namespace App\Data\Teams;

use App\Models\Team;
use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserTeamIndexData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly bool $isOwner,
        public readonly bool $isCurrentTeam,
    ) {}

    public static function fromTeamForUser(Team $team, User $currentUser): self
    {
        return new self(
            id: $team->id,
            name: $team->name,
            isOwner: $team->user_id === $currentUser->id,
            isCurrentTeam: $team->id === $currentUser->current_team_id,
        );
    }
}
