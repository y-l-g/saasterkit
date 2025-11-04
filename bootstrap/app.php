<?php

use App\Http\Middleware\DisableSsr;
use App\Http\Middleware\EnsureUserHasPassword;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserTeamIsConsistent;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirectIfNoTeam;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Symfony\Component\HttpFoundation\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies();

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            EnsureUserTeamIsConsistent::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
        ]);

        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->alias([
            'has.password' => EnsureUserHasPassword::class,
            'admin' => EnsureUserIsAdmin::class,
            'has.team' => RedirectIfNoTeam::class,
            'nossr' => DisableSsr::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
