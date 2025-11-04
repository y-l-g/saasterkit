<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class PasswordSettingsShowController
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('settings/Password');
    }
}
