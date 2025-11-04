<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\TeamInvitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class TeamInvitationDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var TeamInvitation $invitation */
        $invitation = $this->route('invitation');

        return Gate::allows(TeamMemberPermissionEnum::TEAM_INVITATION_CANCEL, $invitation->team);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [];
    }
}
