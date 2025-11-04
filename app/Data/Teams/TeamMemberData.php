<?php

declare(strict_types=1);

namespace App\Data\Teams;

use App\Enums\Teams\RoleEnum;
use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class TeamMemberData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly Lazy|null|RoleEnum $role,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: Lazy::whenLoaded(
                'pivot',
                $user,
                fn () => $user->pivot->role?->value ? RoleEnum::from($user->pivot->role->value) : null
            ),
        );
    }
}
