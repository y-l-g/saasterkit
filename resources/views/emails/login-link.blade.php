<x-mail::message>
    # Your Login Link

    You recently tried to sign in using a social account, but an account with your email address already exists.

    Click the button below to log in securely to your account. You can then link your social accounts from your profile
    page.

    <x-mail::button :url="$loginUrl">
        Log In
    </x-mail::button>

    This link will expire in 5 minutes.

    If you did not request this, you can safely ignore this email.
</x-mail::message>
