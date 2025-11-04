<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class TeamSwitchRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = Team::query()->findOrFail($this->input('team_id'));
        /** @var User $user */
        $user = $this->user();

        return $user->belongsToTeam($team);
    }

    /**
     * @return array<string, array<int, string|object>>
     */
    public function rules(): array
    {
        return [
            'team_id' => ['required', 'integer', 'exists:teams,id'],
        ];
    }
}
