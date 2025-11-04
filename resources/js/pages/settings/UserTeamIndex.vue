<script setup lang="ts">
import UserTeams from '@/components/settings/UserTeams.vue';
import TeamCreate from '@/components/teams/TeamCreate.vue';
import InvitedInvitations from '@/components/teams/TeamPendingReceivedInvitations.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { computed } from 'vue';

defineProps<{
    teams: App.Data.Teams.UserTeamIndexData[];
    invitations: App.Data.Teams.TeamInvitationData[];
}>();

const breadcrumbs = computed(() => [
    { label: 'Teams' },
    {
        label: 'My Teams',
    },
]);
useHead({
    title: 'Your Teams',
});
</script>

<template>
    <SettingsLayout :breadcrumbs="breadcrumbs">
        <TeamCreate />
        <UserTeams :teams="teams" />
        <InvitedInvitations :invitations="invitations" />
    </SettingsLayout>
</template>
