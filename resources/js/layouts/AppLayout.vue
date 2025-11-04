<script setup lang="ts">
import NotificationsSlideover from '@/components/layout/NotificationsSlideover.vue';
import TeamMenu from '@/components/layout/TeamMenu.vue';
import UserMenu from '@/components/layout/UserMenu.vue';
import { useAuthPage } from '@/composables/useAuthPage';
import { useDashboard } from '@/composables/useDashboard';
import { AppLogoIcon } from '@/icons/AppLogoIcon';
import { admin, dashboard } from '@/routes';
import type { BreadcrumbItem, NavigationMenuItem } from '@nuxt/ui';
import { ref } from 'vue';

defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

const { isNotificationsSlideoverOpen } = useDashboard();

const open = ref(false);
const page = useAuthPage();

const hasCurrentTeamlinks: NavigationMenuItem[] = [
    {
        label: 'Dashboard',
        icon: 'i-lucide-house',
        to: dashboard().url,
        active: page.url === dashboard().url,
        onSelect: () => {
            open.value = false;
        },
    },
];

const bottomlinks: NavigationMenuItem[] = [
    {
        label: 'Github Repo',
        icon: 'i-lucide-folder',
        to: 'https://github.com/y-l-g/saasterkit',
        target: '_blank',
    },
    {
        label: 'Documentation',
        icon: 'i-lucide-info',
        to: 'https://doc.saasterkit.com',
        target: '_blank',
    },
];
</script>

<template>
    <UDashboardGroup unit="rem" storage="local">
        <UDashboardSidebar
            id="default"
            v-model:open="open"
            collapsible
            resizable
            class="bg-elevated/25"
            :ui="{ footer: 'lg:border-t lg:border-default' }"
        >
            <template #header="{ collapsed }">
                <UButton
                    :icon="AppLogoIcon"
                    color="neutral"
                    size="xl"
                    variant="link"
                    :square="collapsed"
                    class="data-[state=open]:bg-elevated"
                    :class="[!collapsed && 'py-2']"
                    :to="dashboard().url"
                    ><span v-if="!collapsed"
                        ><span class="text-default">Saas</span>terkit</span
                    ></UButton
                >
            </template>

            <template #default="{ collapsed }">
                <UNavigationMenu
                    :collapsed="collapsed"
                    :items="
                        page.props.user.currentTeamId ? hasCurrentTeamlinks : []
                    "
                    orientation="vertical"
                    tooltip
                    popover
                />

                <UNavigationMenu
                    :collapsed="collapsed"
                    :items="bottomlinks"
                    orientation="vertical"
                    tooltip
                    class="mt-auto"
                />
            </template>

            <template
                #footer="{ collapsed }"
                v-if="page.props.user.currentTeamId"
            >
                <TeamMenu :collapsed="collapsed" />
            </template>
        </UDashboardSidebar>
        <UDashboardPanel resizable>
            <template #header>
                <UDashboardNavbar :ui="{ right: 'gap-3' }">
                    <template #leading>
                        <UDashboardSidebarCollapse
                            as="button"
                            :disabled="false"
                        />
                        <UBreadcrumb :items="breadcrumbs" />
                    </template>

                    <template #right>
                        <UColorModeButton
                            color="neutral"
                            as="button"
                            :disabled="false"
                        ></UColorModeButton>
                        <UTooltip text="Notifications">
                            <UButton
                                color="neutral"
                                variant="ghost"
                                square
                                @click="isNotificationsSlideoverOpen = true"
                            >
                                <UChip
                                    color="error"
                                    inset
                                    :show="
                                        page.props.user.notifications!.length >
                                        0
                                    "
                                >
                                    <UIcon
                                        name="i-lucide-bell"
                                        class="size-5 shrink-0"
                                    />
                                </UChip>
                            </UButton>
                        </UTooltip>
                        <UButton
                            v-if="page.props.user.isAdmin"
                            :to="admin().url"
                            icon="i-lucide-shield"
                            variant="subtle"
                            color="info"
                        ></UButton>
                        <UserMenu
                    /></template>
                </UDashboardNavbar>
                <UDashboardToolbar
                    v-if="$slots.toolbar || $slots['toolbar-left']"
                    ><slot name="toolbar"></slot
                    ><template #left><slot name="toolbar-left"></slot></template
                ></UDashboardToolbar>
            </template>
            <template #body> <slot name="body"></slot></template>
        </UDashboardPanel>
        <NotificationsSlideover />
    </UDashboardGroup>
</template>
