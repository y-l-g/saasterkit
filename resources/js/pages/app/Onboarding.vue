<script setup lang="ts">
import Container from '@/components/common/Container.vue';
import CreateTeam from '@/components/teams/TeamCreate.vue';
import InvitedInvitations from '@/components/teams/TeamPendingReceivedInvitations.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { onboarding } from '@/routes';
import type { BreadcrumbItem } from '@nuxt/ui';

defineProps<{
    invitations: App.Data.Teams.TeamInvitationData[];
}>();

const breadcrumbItems = [
    {
        label: 'Onboarding',
        to: onboarding().url,
    },
] satisfies BreadcrumbItem[];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <template #body>
            <Container>
                <UPageCard
                    variant="naked"
                    title="Welcome!"
                    description="Get started by creating a team or accepting any pending team invitations. "
                >
                </UPageCard>
                <CreateTeam />
                <InvitedInvitations :invitations="invitations" />
            </Container>
        </template>
    </AppLayout>
</template>
