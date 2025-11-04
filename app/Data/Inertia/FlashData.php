<?php

declare(strict_types=1);

namespace App\Data\Inertia;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class FlashData extends Data
{
    public function __construct(
        public readonly Optional|null|string $success,
        public readonly Optional|null|string $error,
        public readonly Optional|null|string $info,
        public readonly Optional|null|string $status,
    ) {}
}
