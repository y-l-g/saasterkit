<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserTeamIsConsistent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }
        if ($user->current_team_id && $user->belongsToTeam($user->current_team_id)) {
            return $next($request);
        }

        $firstTeam = $user->teams()->first();

        if ($firstTeam) {
            $user->switchToTeam($firstTeam);
        } else {
            $user->update(['current_team_id' => null]);
        }

        return $next($request);
    }
}
