<?php

declare(strict_types=1);

namespace App\Enums\Teams;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum TeamMemberPermissionEnum: string
{
    case TEAM_VIEW = 'team.view';
    case TEAM_UPDATE = 'team.update';
    case TEAM_DELETE = 'team.delete';
    case TEAM_OWNER_TRANSFER = 'team.owner.transfer';
    case TEAM_MEMBER_INVITE = 'team.member.invite';
    case TEAM_MEMBER_UPDATE = 'team.member.update';
    case TEAM_MEMBER_REMOVE = 'team.member.remove';
    case TEAM_INVITATION_CANCEL = 'team.invitation.cancel';
    case BILLING_PORTAL_VIEW = 'billing.portal.view';
    case BILLING_SETTINGS_VIEW = 'billing.settings.view';
    case BILLING_CHECKOUT_CREATE = 'billing.checkout.create';
}
