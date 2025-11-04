<?php

declare(strict_types=1);

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseHas;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('successfully creates a new admin user with valid inputs', function (): void {
    artisan('app:create-admin')
        ->expectsQuestion('Full Name?', 'Admin User')
        ->expectsQuestion('Email?', 'admin@example.com')
        ->expectsQuestion('Password?', 'password')
        ->expectsQuestion('Confirm Password?', 'password')
        ->expectsOutput('Admin user created successfully.')
        ->assertExitCode(0);

    assertDatabaseHas('users', [
        'email' => 'admin@example.com',
        'is_admin' => true,
    ]);
});

it('fails and displays validation errors with an invalid email', function (): void {
    artisan('app:create-admin')
        ->expectsQuestion('Full Name?', 'Admin User')
        ->expectsQuestion('Email?', 'not-an-email')
        ->expectsQuestion('Password?', 'password')
        ->expectsQuestion('Confirm Password?', 'password')
        ->expectsOutput('Admin user not created. See error messages below:')
        ->assertExitCode(1);
});
