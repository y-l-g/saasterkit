<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('can update appearance settings', function (): void {
    $user = User::factory()->create([
        'primary_color' => 'gray',
        'secondary_color' => 'red',
        'neutral_color' => 'lime',
    ]);
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->patch(scoped_route('appearance.update', $team), [
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
