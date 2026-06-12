<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Enums\Teams\RoleEnum;
use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TeamMemberRoleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = $this->route('current_team');

        return Gate::allows(TeamMemberPermissionEnum::TEAM_MEMBER_UPDATE, $team);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', Rule::enum(RoleEnum::class)],
        ];
    }

    protected function passedValidation(): void
    {
        /** @var Team $team */
        $team = $this->route('current_team');
        /** @var User $member */
        $member = $this->route('user');

        abort_unless($team->users()->whereKey($member->id)->exists(), 404);
    }
}
