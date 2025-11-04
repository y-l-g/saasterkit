export const formatDate = (
    dateString: string | null | undefined,
    locale: string,
) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString(locale);
};

export const fortifyStatusMessages: Record<
    App.Enums.Auth.FortifyStatusEnum,
    string
> = {
    'profile-information-updated':
        'Your profile information has been updated successfully.',
    'password-updated': 'Your password has been updated successfully.',
    'two-factor-authentication-enabled':
        'Two-factor authentication has been enabled.',
    'two-factor-authentication-disabled':
        'Two-factor authentication has been disabled.',
    'two-factor-authentication-confirmed':
        'Two-factor authentication has been confirmed.',
    'recovery-codes-generated': 'New recovery codes have been generated.',
    'verification-link-sent':
        'A new verification link has been sent to your email address.',
};

export function randomInt(min: number, max: number): number {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

export function randomFrom<T>(array: T[]): T {
    return array[Math.floor(Math.random() * array.length)];
}
