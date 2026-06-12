<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait GeneratesUniqueTeamSlugs
{
    protected static function generateUniqueTeamSlug(string $name, ?int $exceptTeamId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'team';

        $query = static::query()
            ->where(function ($query) use ($baseSlug): void {
                $query->where('slug', $baseSlug)
                    ->orWhere('slug', 'like', $baseSlug.'-%');
            });

        if ($exceptTeamId !== null) {
            $query->whereKeyNot($exceptTeamId);
        }

        $existingSlugs = $query->pluck('slug');

        if ($existingSlugs->isEmpty()) {
            return $baseSlug;
        }

        $maxSuffix = $existingSlugs
            ->map(function (string $slug) use ($baseSlug): ?int {
                if ($slug === $baseSlug) {
                    return 0;
                }

                if (preg_match('/^'.preg_quote($baseSlug, '/').'-(\d+)$/', $slug, $matches) === 1) {
                    return (int) $matches[1];
                }

                return null;
            })
            ->filter(fn (?int $suffix): bool => $suffix !== null)
            ->max() ?? 0;

        return $baseSlug.'-'.($maxSuffix + 1);
    }
}
