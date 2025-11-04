<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    $this->owner = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->owner->id]);
    $this->member = User::factory()->create();
    $this->team->users()->syncWithPivotValues($this->member->id, ['role' => 'editor'], false);
});

it('allows an authorized user to update a team members role', function (): void {
    actingAs($this->owner)
        ->put(route('teams.members.update', [$this->team, $this->member]), ['role' => 'admin'])
        ->assertSessionHas('success');

    expect($this->member->teamRole($this->team)->value)->toBe('admin');
});

it('allows an authorized user to remove a team member', function (): void {
    actingAs($this->owner)
        ->delete(route('teams.members.destroy', [$this->team, $this->member]))
        ->assertSessionHas('success');

    expect($this->team->fresh()->hasUser($this->member))->toBeFalse();
});

it('allows a team member to leave a team', function (): void {
    actingAs($this->member)
        ->delete(route('teams.members.destroy', [$this->team, $this->member]))
        ->assertSessionHas('success');

    expect($this->team->fresh()->hasUser($this->member))->toBeFalse();
});

it('prevents the team owner from being removed or leaving the team', function (): void {
    actingAs($this->owner)
        ->delete(route('teams.members.destroy', [$this->team, $this->owner]))
        ->assertSessionHasErrors();
});
