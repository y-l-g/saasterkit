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
        v-if="team.invitations && team.invitations.length > 0"
        title="Pending Team Invitations"
        description="These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation."
        variant="soft"
    >
        <div class="space-y-4">
            <div
                v-for="invitation in team.invitations"
                :key="invitation.id"
                class="flex items-center justify-between"
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
