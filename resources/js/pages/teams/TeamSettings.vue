<script setup lang="ts">
import TeamDelete from '@/components/teams/TeamDelete.vue';
import TeamMemberList from '@/components/teams/TeamMemberList.vue';
import TeamPendingSentInvitations from '@/components/teams/TeamPendingSentInvitations.vue';
import TeamSendInvitation from '@/components/teams/TeamSendInvitation.vue';
import TeamSendOwnershipInvitation from '@/components/teams/TeamSendOwnershipInvitation.vue';
import TeamUpdateName from '@/components/teams/TeamUpdateName.vue';
import { useTeamPermissions } from '@/composables/useTeamPermissions';
import TeamsLayout from '@/layouts/TeamLayout.vue';
import { show } from '@/routes/teams/settings';
import { PaginatedCollection } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    team: App.Data.Teams.TeamData;
    availableRoles: App.Data.Teams.TeamRoleData[];
    members: PaginatedCollection<App.Data.Teams.TeamMemberData>;
}>();

const breadcrumbs = computed(() => [
    {
        label: props.team.name,
        to: show({ team: props.team.id }).url,
    },
]);

useHead({
    title: 'Team Settings',
});

const { hasTeamPermission } = useTeamPermissions();
</script>

<template>
    <TeamsLayout :breadcrumbs>
        <TeamUpdateName :team />
        <TeamSendInvitation
            :team
            :available-roles
            v-if="hasTeamPermission('team.member.invite')"
        />
        <TeamPendingSentInvitations
            :team
            v-if="hasTeamPermission('team.member.invite')"
        />
        <TeamMemberList :team :available-roles :members />
        <TeamSendOwnershipInvitation
            :team
            v-if="hasTeamPermission('team.owner.transfer')"
        />
        <TeamDelete :team v-if="hasTeamPermission('team.delete')" />
    </TeamsLayout>
</template>
