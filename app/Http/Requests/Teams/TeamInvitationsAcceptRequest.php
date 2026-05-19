<?php

namespace App\Http\Requests\Teams;

use App\Models\TeamInvitation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TeamInvitationsAcceptRequest extends FormRequest
{
    public function authorize(): bool
    {
        foreach (TeamInvitation::query()->whereIn('id', $this->input('invitations', []))->get() as $invitation) {
            if ($invitation->email !== $this->user()->email) {

                return false;

            }
        }

        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invitations' => ['required', 'array'],
            'invitations.*' => ['exists:team_invitations,id'],
        ];
    }
}
