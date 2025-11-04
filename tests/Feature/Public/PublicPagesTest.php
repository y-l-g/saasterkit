<?php

declare(strict_types=1);

use App\Services\PlanService;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\get;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('renders the welcome page with pricing plans', function (): void {
    get(route('home'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('public/Welcome')
                ->has('plans', count(app(PlanService::class)->all()))
        );
});

it('renders the privacy policy page', function (): void {
    get(route('privacy'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('public/Privacy')
                ->has('content')
        );
});
