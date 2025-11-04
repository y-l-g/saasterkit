<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Teams\TeamRoleData;
use App\Enums\Teams\RoleEnum;
use Illuminate\Support\Collection;

final class RoleService
{
    /**
     * @return Collection<int, TeamRoleData>
     */
    public function all(): Collection
    {
        return collect(RoleEnum::cases())
            ->map(fn (RoleEnum $role): TeamRoleData => TeamRoleData::from([
                'key' => $role->value,
                'name' => $role->name(),
                'description' => $role->description(),
                'permissions' => $role->permissions(),
            ]));
    }

    public function getRoleByKey(?string $key): ?TeamRoleData
    {
        return $key ? $this->all()->firstWhere('key', $key) : null;
    }
}
