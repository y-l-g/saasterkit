<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Enums\Teams\RoleEnum;
use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Support\EmailAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TeamInvitationSendRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = $this->route('current_team');

        return Gate::allows(TeamMemberPermissionEnum::TEAM_MEMBER_INVITE, $team);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => EmailAddress::normalize($this->input('email')),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Team $team */
        $team = $this->route('current_team');

        return [
            'email' => [
                'required',
                'email',
                Rule::unique('team_invitations')
                    ->where('team_id', $team->id)
                    ->whereNull('accepted_at'),
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
                $team = $this->route('current_team');
                $email = $this->input('email');

                if (! is_string($email)) {
                    return;
                }

                if ($team->hasUserWithEmail($email)) {
                    $validator->errors()->add('email', 'This user already belongs to the team.');
                }

                if (! $validator->errors()->has('email') && $team->teamInvitations()
                    ->whereRaw('lower(email) = ?', [$email])
                    ->pending()
                    ->exists()) {
                    $validator->errors()->add('email', 'An invitation has already been sent to this user.');
                }
            },
        ];
    }
}
