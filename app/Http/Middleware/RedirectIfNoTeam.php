<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNoTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && is_null($request->user()->current_team_id)) {
            return to_route('onboarding');
        }

        return $next($request);
    }
}
