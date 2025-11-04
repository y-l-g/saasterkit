<?php

declare(strict_types=1);

namespace App\Data\Inertia;

use App\Data\Auth\UserData;
use App\Enums\Teams\TeamMemberPermissionEnum;
use Spatie\LaravelData\Attributes\AutoClosureLazy;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AppPagePropsData extends Data
{
    /**
     * @param  array<TeamMemberPermissionEnum>  $permissions
     */
    public function __construct(
        #[AutoClosureLazy()]
        public readonly Lazy|null|UserData $user,
        public readonly array $permissions,
        public readonly FlashData $flash
    ) {}
}
