<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Support\ReservedTeamName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class TeamUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = $this->route('current_team');

        return Gate::allows(TeamMemberPermissionEnum::TEAM_UPDATE, $team);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new ReservedTeamName],
        ];
    }
}
