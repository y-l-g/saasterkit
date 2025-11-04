<script setup lang="ts">
import { useAuthPage } from '@/composables/useAuthPage';
import { useTeamPermissions } from '@/composables/useTeamPermissions';
import { show } from '@/routes/teams/settings';
import { PaginatedCollection } from '@/types';
import { router } from '@inertiajs/vue3';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import LeaveTeamModal from './TeamLeaveModal.vue';
import ManageRoleModal from './TeamManageRoleModal.vue';
import RemoveTeamMemberModal from './TeamRemoveMemberModal.vue';

interface Props {
    team: App.Data.Teams.TeamData;
    availableRoles: App.Data.Teams.TeamRoleData[];
    members: PaginatedCollection<App.Data.Teams.TeamMemberData>;
}

const page = useAuthPage();

const props = defineProps<Props>();

const breakpoints = useBreakpoints(breakpointsTailwind);
const isMobile = breakpoints.smallerOrEqual('sm');

const { hasTeamPermission } = useTeamPermissions();

function onPageChange(page: number) {
    router.get(
        show(props.team.id, {
            query: {
                page,
            },
        }).url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}
</script>

<template>
    <UCard variant="soft">
        <template #header>
            <UPageCard
                title="Team Members"
                :description="
                    members.data.length > 0
                        ? 'People that are part of this team.'
                        : 'Nobody but you in this team'
                "
                variant="naked"
            />
        </template>
        <div class="divide-y divide-muted">
            <div
                v-for="member in members.data"
                :key="member.id"
                class="flex flex-col gap-4 py-2 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-4">
                    <UAvatar v-if="!isMobile" :alt="member.name" size="lg" />
                    <div>
                        <p class="font-medium">{{ member.name }}</p>
                        <p class="text-sm text-toned">
                            {{ member.email }}
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center space-x-2"
                    v-if="member.id != team.userId"
                >
                    <template v-if="member.id != page.props.user.id">
                        <ManageRoleModal
                            :team="team"
                            :member="member"
                            :available-roles="availableRoles"
                        />
                        <RemoveTeamMemberModal
                            v-if="hasTeamPermission('team.member.remove')"
                            :team="team"
                            :member="member"
                        />
                    </template>

                    <LeaveTeamModal
                        v-if="member.id === page.props.user.id"
                        :team="team"
                    />
                </div>
            </div>
        </div>

        <template #footer v-if="members?.total > members.per_page">
            <div class="flex items-center justify-center">
                <UPagination
                    :total="members.total"
                    :itemsPerPage="members.per_page"
                    :model-value="members.current_page"
                    @update:page="onPageChange"
                />
            </div>
        </template>
    </UCard>
</template>
