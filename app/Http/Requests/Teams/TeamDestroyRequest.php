<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

class TeamDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = $this->route('current_team');

        return Gate::allows(TeamMemberPermissionEnum::TEAM_DELETE, $team);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return ['password' => ['required', 'current_password']];
    }

    /**
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('password')) {
                    return;
                }

                /** @var Team $team */
                $team = $this->route('current_team');

                if ($team->subscribed('default')) {
                    $validator->errors()->add(
                        'subscription',
                        'Cancel this team subscription before deleting the team.',
                    );
                }
            },
        ];
    }
}
