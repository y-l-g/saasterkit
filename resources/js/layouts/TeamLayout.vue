<script setup lang="ts">
import { useAuthPage } from '@/composables/useAuthPage';
import { useTeamPermissions } from '@/composables/useTeamPermissions';
import { show as showBilling } from '@/routes/billing';
import { show } from '@/routes/teams/settings';
import { BreadcrumbItem, NavigationMenuItem } from '@nuxt/ui';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import { computed } from 'vue';
import AppLayout from './AppLayout.vue';

defineProps<{
    breadcrumbs: BreadcrumbItem[];
}>();

const { hasTeamPermission } = useTeamPermissions();

const page = useAuthPage();

const breakpoints = useBreakpoints(breakpointsTailwind);
const isMobile = breakpoints.smallerOrEqual('sm');
const orientation = computed(() => {
    return isMobile.value ? 'vertical' : 'horizontal';
});

const links = computed<NavigationMenuItem[][]>(() => {
    const menuLinks = [
        {
            label: 'Team Settings',
            icon: 'i-lucide-user',
            to: show(page.props.user.currentTeamId!).url,
        },
    ];

    if (hasTeamPermission('billing.settings.view')) {
        menuLinks.push({
            label: 'Billing',
            icon: 'i-lucide-circle-dollar-sign',
            to: showBilling(page.props.user.currentTeamId!).url,
        });
    }

    return [menuLinks];
});

const activeLabel = computed(() => {
    const allLinks = links.value.flat();
    const activeLink = allLinks.find((link) => link.to === page.url);
    return activeLink?.label || 'Settings';
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #toolbar>
            <UNavigationMenu
                v-if="!isMobile"
                :orientation="orientation"
                :items="links"
                class="-mx-1 flex-1"
            />
            <UDropdownMenu
                v-else
                :items="links"
                :content="{
                    align: 'start',
                    side: 'bottom',
                    sideOffset: 8,
                }"
            >
                <UButton
                    :label="activeLabel"
                    icon="i-lucide-settings"
                    trailing-icon="i-lucide-chevron-down"
                    variant="ghost"
                />
            </UDropdownMenu>
        </template>

        <template #body>
            <div
                class="mx-auto flex w-full flex-col gap-4 sm:gap-6 lg:max-w-2xl"
            >
                <slot />
            </div>
        </template>
    </AppLayout>
</template>
