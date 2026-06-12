<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamMembership
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $team = $this->team($request);

        abort_if(! $user || ! $team || ! $user->belongsToTeam($team), 403);

        $this->bindRouteTeam($request, $team);

        if (! $user->currentTeam?->is($team)) {
            $user->switchToTeam($team);
        }

        return $next($request);
    }

    private function team(Request $request): ?Team
    {
        $team = $request->route('current_team') ?? $request->route('team');

        if ($team instanceof Team) {
            return $team;
        }

        if (is_string($team)) {
            return Team::query()->where('slug', $team)->first();
        }

        return null;
    }

    private function bindRouteTeam(Request $request, Team $team): void
    {
        $route = $request->route();

        if (! $route) {
            return;
        }

        foreach (['current_team', 'team'] as $parameter) {
            if ($route->hasParameter($parameter)) {
                $route->setParameter($parameter, $team);
            }
        }
    }
}
