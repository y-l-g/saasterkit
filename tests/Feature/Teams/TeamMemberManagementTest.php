<?php

declare(strict_types=1);

use App\Enums\Teams\RoleEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->owner = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->owner->id]);
    $this->member = User::factory()->create();
    $this->team->users()->syncWithPivotValues($this->member->id, ['role' => 'editor'], false);
});

it('allows an authorized user to update a team members role', function (): void {
    actingAs($this->owner)
        ->put(scoped_route('teams.members.update', $this->team, ['user' => $this->member]), ['role' => 'admin'])
        ->assertSessionHas('success');

    expect($this->member->teamRole($this->team)->value)->toBe('admin');
});

it('allows an admin member to update another team members role', function (): void {
    $admin = User::factory()->create();
    $this->team->users()->syncWithPivotValues($admin->id, ['role' => RoleEnum::ADMIN->value], false);
    $admin->switchToTeam($this->team);

    actingAs($admin)
        ->put(scoped_route('teams.members.update', $this->team, ['user' => $this->member]), ['role' => RoleEnum::ADMIN->value])
        ->assertSessionHas('success');

    expect($this->member->teamRole($this->team))->toBe(RoleEnum::ADMIN);
});

it('allows an authorized user to remove a team member', function (): void {
    actingAs($this->owner)
        ->delete(scoped_route('teams.members.destroy', $this->team, ['user' => $this->member]))
        ->assertSessionHas('success');

    expect($this->team->fresh()->hasUser($this->member))->toBeFalse();
});

it('allows an admin member to remove another team member', function (): void {
    $admin = User::factory()->create();
    $this->team->users()->syncWithPivotValues($admin->id, ['role' => RoleEnum::ADMIN->value], false);
    $admin->switchToTeam($this->team);

    actingAs($admin)
        ->delete(scoped_route('teams.members.destroy', $this->team, ['user' => $this->member]))
        ->assertSessionHas('success');

    expect($this->team->fresh()->hasUser($this->member))->toBeFalse();
});

it('allows a team member to leave a team', function (): void {
    actingAs($this->member)
        ->delete(scoped_route('teams.members.destroy', $this->team, ['user' => $this->member]))
        ->assertSessionHas('success');

    expect($this->team->fresh()->hasUser($this->member))->toBeFalse();
});

it('prevents the team owner from being removed or leaving the team', function (): void {
    actingAs($this->owner)
        ->delete(scoped_route('teams.members.destroy', $this->team, ['user' => $this->owner]))
        ->assertSessionHasErrors();
});

it('rejects role updates for users who are not team members', function (): void {
    $nonMember = User::factory()->create();

    actingAs($this->owner)
        ->put(scoped_route('teams.members.update', $this->team, ['user' => $nonMember]), ['role' => 'admin'])
        ->assertNotFound();
});

it('rejects removals for users who are not team members', function (): void {
    $nonMember = User::factory()->create();

    actingAs($this->owner)
        ->delete(scoped_route('teams.members.destroy', $this->team, ['user' => $nonMember]))
        ->assertNotFound();
});
