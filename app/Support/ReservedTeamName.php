<?php

declare(strict_types=1);

namespace App\Support;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Routing\Route as RouteElement;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

final class ReservedTeamName implements ValidationRule
{
    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $name = Str::lower(trim((string) $value));
        $slug = Str::slug($name);
        $reservedNames = self::reservedNames();

        if (
            $slug === ''
            || in_array($name, $reservedNames, true)
            || in_array($slug, $reservedNames, true)
        ) {
            $fail('This team name is reserved and cannot be used.');
        }
    }

    /**
     * @return array<int, string>
     */
    private static function reservedNames(): array
    {
        return once(fn () => collect(self::routePrefixes())
            ->merge([
                '400',
                '401',
                '403',
                '404',
                '419',
                '422',
                '429',
                '500',
                '503',
                'account',
                'accounts',
                'admin',
                'api',
                'app',
                'apps',
                'auth',
                'billing',
                'blog',
                'checkout',
                'contact',
                'dashboard',
                'home',
                'invoices',
                'login',
                'logout',
                'new',
                'notifications',
                'oauth',
                'onboarding',
                'privacy',
                'profile',
                'register',
                'settings',
                'signin',
                'sign-in',
                'signup',
                'sign-up',
                'storage',
                'subscription',
                'subscriptions',
                'team',
                'team-invitations',
                'team-ownership-transfers',
                'teams',
                'terms',
                'user',
                'users',
            ])
            ->unique()
            ->sort()
            ->values()
            ->toArray());
    }

    /**
     * @return array<int, string>
     */
    private static function routePrefixes(): array
    {
        return collect(Route::getRoutes()->getRoutes())
            ->map(fn (RouteElement $route): string => $route->uri)
            ->map(fn (string $uri): string => explode('/', $uri)[0])
            ->reject(fn (string $uri): bool => str_contains($uri, '{'))
            ->filter(fn (string $uri): bool => $uri !== '')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
