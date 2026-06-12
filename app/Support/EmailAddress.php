<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

final class EmailAddress
{
    public static function normalize(mixed $email): ?string
    {
        if (! is_string($email)) {
            return null;
        }

        return Str::of($email)->trim()->lower()->toString();
    }

    public static function matches(mixed $left, mixed $right): bool
    {
        return is_string($left)
            && is_string($right)
            && self::normalize($left) === self::normalize($right);
    }
}
