<?php

declare(strict_types=1);

namespace App\Data\Teams;

use App\Enums\Teams\RoleEnum;
use App\Enums\Teams\TeamMemberPermissionEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class TeamRoleData extends Data
{
    /**
     * @param  TeamMemberPermissionEnum[]  $permissions
     */
    public function __construct(
        #[WithCast(EnumCast::class)]
        public readonly RoleEnum $key,
        public readonly string $name,
        public readonly array $permissions,
        public readonly string $description,
    ) {}
}
