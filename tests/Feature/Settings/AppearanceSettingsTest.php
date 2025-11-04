<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can update appearance settings', function (): void {
    $user = User::factory()->create([
        'primary_color' => 'gray',
        'secondary_color' => 'red',
        'neutral_color' => 'lime',
    ]);

    actingAs($user)
        ->patch(route('appearance.update'), [
            'primary_color' => 'red',
            'secondary_color' => 'blue',
            'neutral_color' => 'slate',
        ])
        ->assertSessionHas('success')
        ->assertRedirect();

    $user->refresh();

    expect($user->primary_color)->toBe('red');
    expect($user->secondary_color)->toBe('blue');
    expect($user->neutral_color)->toBe('slate');
});
