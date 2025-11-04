<?php

declare(strict_types=1);

namespace App\Enums\Auth;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum SocialiteProviderEnum: string
{
    case GOOGLE = 'google';
    case GITHUB = 'github';

    /**
     * @param  array<string>  $providers
     * @return array<self>
     */
    public static function collect(array $providers): array
    {
        return array_map(fn (string $provider) => self::from($provider), $providers);
    }
}
