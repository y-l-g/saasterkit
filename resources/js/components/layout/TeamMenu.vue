<script setup lang="ts">
import { useAuthPage } from '@/composables/useAuthPage';
import { update } from '@/routes/teams/current';
import { show } from '@/routes/teams/settings';
import { teams } from '@/routes/user';
import { useForm } from '@inertiajs/vue3';
import type { DropdownMenuItem } from '@nuxt/ui';
import { computed } from 'vue';

defineProps<{
    collapsed?: boolean;
}>();

const page = useAuthPage();

const switchTeamForm = useForm({
    team_id: page.props.user.currentTeam?.id,
});

const switchToTeam = (team: App.Data.Teams.TeamData) => {
    switchTeamForm.team_id = team.id;
    switchTeamForm.submit(update(), {
        preserveState: false,
    });
};

const dropdownItems = computed<DropdownMenuItem[][]>(() => {
    const teamLabelSection: DropdownMenuItem[] = [
        {
            type: 'label',
            label: page.props.user.currentTeam?.name,
            avatar: {
                alt: page.props.user.currentTeam?.name,
            },
        },
    ];

    const teamManagementSection: DropdownMenuItem[] = [
        {
            label: 'Team Settings',
            to: show({ team: page.props.user.currentTeam!.id }).url,
        },
        {
            label: 'Create New Team',
            to: teams().url,
        },
    ];

    const sections = [[...teamLabelSection], [...teamManagementSection]];

    if (page.props.user.teams?.length && page.props.user.teams?.length > 1) {
        const teamSwitcherSection: DropdownMenuItem[] = [
            {
                type: 'label',
                label: 'Switch Teams',
            },
            ...page.props.user.teams.map((team) => ({
                label: team.name,
                onClick: () => switchToTeam(team),
                icon:
                    team.id === page.props.user.currentTeam!.id
                        ? 'i-lucide-check-circle'
                        : 'i-lucide-circle',
            })),
        ];
        sections.push(teamSwitcherSection);
    }

    return sections;
});
</script>

<template>
    <UDropdownMenu
        :items="dropdownItems"
        :ui="{
            content: collapsed
                ? 'w-48'
                : 'w-(--reka-dropdown-menu-trigger-width)',
        }"
    >
        <UButton
            :label="collapsed ? undefined : page.props.user.currentTeam!.name"
            :trailing-icon="collapsed ? undefined : 'i-lucide-chevron-up-down'"
            color="neutral"
            :variant="collapsed ? 'link' : 'ghost'"
            block
            ><template #leading
                ><UAvatar :alt="page.props.user.currentTeam?.name" size="lg"
            /></template>
        </UButton>
    </UDropdownMenu>
</template>
