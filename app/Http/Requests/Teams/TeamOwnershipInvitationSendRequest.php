<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

class TeamOwnershipInvitationSendRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = $this->route('team');

        return Gate::allows(TeamMemberPermissionEnum::TEAM_OWNER_TRANSFER, $team);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    /**
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Team $team */
                $team = $this->route('team');
                $email = $this->input('email');

                $newOwner = User::query()->where('email', $email)->first();

                if (! $newOwner) {
                    $validator->errors()->add('email', 'This user does not exist.');

                    return;
                }

                if ($this->user()?->is($newOwner)) {
                    $validator->errors()->add('email', 'You cannot transfer ownership to yourself.');

                    return;
                }

                if (! $team->hasUser($newOwner)) {
                    $validator->errors()->add('email', 'This user does not belong to the team.');
                }
            },
        ];
    }
}
