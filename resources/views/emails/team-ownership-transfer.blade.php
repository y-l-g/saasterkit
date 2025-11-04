<x-mail::message>
    # Team Ownership Transfer Invitation

    You have been invited to become the new owner of the **{{ $teamName }}** team by **{{ $currentOwnerName }}**.

    If you accept this, you will become the sole owner of the team, and {{ $currentOwnerName }} will be assigned a new
    role within the team.

    This invitation will expire in 7 days.

    <x-mail::button :url="$acceptUrl">
        Accept Transfer
    </x-mail::button>

    If you did not expect to receive this invitation, you may discard this email.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
