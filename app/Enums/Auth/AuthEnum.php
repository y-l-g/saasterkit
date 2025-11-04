<?php

declare(strict_types=1);

namespace App\Enums\Auth;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum AuthEnum: string
{
    case ACCEPT_TEAM_OWNERSHIP_INVITATION = 'accept-team-ownership-invitation';
    case ACCEPT_TEAM_INVITATION = 'accept-team-invitation';
    case SEND_APP_NOTIFICATION = 'send-app-notification';
}
