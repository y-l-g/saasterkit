<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class TeamMemberDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = $this->route('team');
        /** @var User $member */
        $member = $this->route('user');

        // a member can leave a team
        if ($this->user()->is($member)) {
            return true;
        }

        return Gate::allows(TeamMemberPermissionEnum::TEAM_MEMBER_REMOVE, $team);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @throws ValidationException
     */
    protected function passedValidation(): void
    {
        /** @var Team $team */
        $team = $this->route('team');
        /** @var User $member */
        $member = $this->route('user');

        throw_if($team->owner->is($member), ValidationException::withMessages([
            'member' => 'You may not leave a team that you created.',
        ]));
    }
}
