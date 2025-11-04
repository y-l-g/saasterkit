<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->user->teams()->sync($this->team->id, false);
    $this->user->current_team_id = $this->team->id;
    $this->user->save();
});

it('denies access if user is not team owner', function (): void {
    $member = User::factory()->create();
    $this->team->users()->syncWithPivotValues($member->id, ['role' => 'editor'], false);

    actingAs($member)->get(route('billing.show', $this->team))->assertForbidden();
});

it('renders the billing settings page for a team owner', function (): void {
    actingAs($this->user)->get(route('billing.show', $this->team))->assertOk();
});
