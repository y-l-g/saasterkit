<script setup lang="ts">
import { useTeamPermissions } from '@/composables/useTeamPermissions';
import { destroy } from '@/routes/teams/invitations';
import { router } from '@inertiajs/vue3';

interface Props {
    team: App.Data.Teams.TeamData;
}

defineProps<Props>();

const { hasTeamPermission } = useTeamPermissions();

const cancelTeamInvitation = (
    invitation: App.Data.Teams.TeamInvitationData,
) => {
    router.delete(destroy(invitation), {
        preserveScroll: true,
    });
};
</script>

<template>
    <UPageCard
        title="Pending Team Invitations"
        description="These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation."
        variant="soft"
    >
        <template
            #description
            v-if="team.invitations && team.invitations.length > 0"
            >These people have been invited to your team and have been sent an
            invitation email. They may join the team by accepting the email
            invitation.</template
        >
        <template #description v-else
            >You have no pending invitations for your team</template
        >
        <div
            class="divide-y divide-muted"
            v-if="team.invitations && team.invitations.length > 0"
        >
            <div
                v-for="invitation in team.invitations"
                :key="invitation.id"
                class="flex items-center justify-between py-4"
            >
                <p class="text-toned">{{ invitation.email }}</p>
                <UButton
                    v-if="hasTeamPermission('team.invitation.cancel')"
                    variant="subtle"
                    color="error"
                    @click="cancelTeamInvitation(invitation)"
                    >Cancel</UButton
                >
            </div>
        </div>
    </UPageCard>
</template>
