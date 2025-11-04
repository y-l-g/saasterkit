<x-mail::message>
    # Team Invitation

    You have been invited to join the **{{ $invitation->team->name }}** team!

    If you do not have an account, you may create one by clicking the button below. After creating an account, you may
    click the invitation acceptance button in this email to accept the team invitation:

    <x-mail::button :url="route('register')">
        Create Account
    </x-mail::button>

    If you already have an account, you may accept this invitation by clicking the button below:

    <x-mail::button :url="$acceptUrl">
        Accept Invitation
    </x-mail::button>

    If you did not expect to receive an invitation to this team, you may discard this email.
</x-mail::message>