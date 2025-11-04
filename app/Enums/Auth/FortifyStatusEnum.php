<?php

declare(strict_types=1);

namespace App\Enums\Auth;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum FortifyStatusEnum: string
{
    case PASSWORD_UPDATED = 'password-updated';
    case PROFILE_INFORMATION_UPDATED = 'profile-information-updated';
    case TWO_FACTOR_AUTHENTICATION_CONFIRMED = 'two-factor-authentication-confirmed';
    case TWO_FACTOR_AUTHENTICATION_DISABLED = 'two-factor-authentication-disabled';
    case TWO_FACTOR_AUTHENTICATION_ENABLED = 'two-factor-authentication-enabled';
    case VERIFICATION_LINK_SENT = 'verification-link-sent';
    case RECOVERY_CODES_GENERATED = 'recovery-codes-generated';

}
