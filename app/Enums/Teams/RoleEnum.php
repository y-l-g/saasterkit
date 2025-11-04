<?php

declare(strict_types=1);

namespace App\Enums\Teams;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum RoleEnum: string
{
    case ADMIN = 'admin';
    case EDITOR = 'editor';

    public function name(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::EDITOR => 'Editor',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator users can perform any action.',
            self::EDITOR => 'Editor users have the ability to read, create, and update.',
        };
    }

    /**
     * @return TeamMemberPermissionEnum[]
     */
    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                TeamMemberPermissionEnum::TEAM_VIEW,
                TeamMemberPermissionEnum::TEAM_UPDATE,
            ],
            self::EDITOR => [
                TeamMemberPermissionEnum::TEAM_VIEW,
            ],
        };
    }
}
