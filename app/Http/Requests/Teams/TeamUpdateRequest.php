<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class TeamUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = $this->route('team');

        return Gate::allows(TeamMemberPermissionEnum::TEAM_UPDATE, $team);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
