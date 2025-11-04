<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Enums\Teams\RoleEnum;
use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TeamInvitationSendRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = $this->route('team');

        return Gate::allows(TeamMemberPermissionEnum::TEAM_MEMBER_INVITE, $team);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Team $team */
        $team = $this->route('team');

        return [
            'email' => [
                'required',
                'email',
                Rule::unique('team_invitations')->where('team_id', $team->id),
            ],
            'role' => ['required', 'string', Rule::enum(RoleEnum::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'An invitation has already been sent to this user.',
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

                if (is_string($email) && $team->hasUserWithEmail($email)) {
                    $validator->errors()->add(
                        'email',
                        'This user already belongs to the team.'
                    );
                }
            },
        ];
    }
}
