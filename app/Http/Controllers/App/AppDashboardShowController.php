<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AppDashboardShowController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('app/Dashboard');
    }
}
