<?php

declare(strict_types=1);

use App\Enums\Teams\RoleEnum;
use App\Enums\Teams\TeamMemberPermissionEnum;

it('keeps team role permissions aligned with their descriptions', function (): void {
    expect(RoleEnum::ADMIN->permissions())
        ->toEqualCanonicalizing(TeamMemberPermissionEnum::cases());

    expect(RoleEnum::EDITOR->permissions())
        ->toContain(TeamMemberPermissionEnum::TEAM_VIEW);
});
