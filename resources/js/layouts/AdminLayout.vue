<script setup lang="ts">
import { dashboard } from '@/routes/admin';
import { index as Notificationsindex } from '@/routes/admin/notifications';
import { index } from '@/routes/admin/subscriptions';
import { usePage } from '@inertiajs/vue3';
import { BreadcrumbItem, NavigationMenuItem } from '@nuxt/ui';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import { computed } from 'vue';
import AppLayout from './AppLayout.vue';

defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

const breakpoints = useBreakpoints(breakpointsTailwind);
const isMobile = breakpoints.smallerOrEqual('sm');
const orientation = computed(() => {
    return isMobile.value ? 'vertical' : 'horizontal';
});

const links: NavigationMenuItem[][] = [
    [
        {
            label: 'Dashboard',
            icon: 'i-lucide-layout-dashboard',
            to: dashboard().url,
            exact: true,
        },
        {
            label: 'Subscriptions',
            icon: 'i-lucide-users',
            to: index().url,
        },
        {
            label: 'Notifications',
            icon: 'i-lucide-bell',
            to: Notificationsindex().url,
        },
    ],
];

const activeLabel = computed(() => {
    const allLinks = links.flat();
    const activeLink = allLinks.find((link) => link.to === usePage().url);
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
                class="mx-auto flex w-full flex-col gap-4 sm:gap-6 lg:max-w-5xl"
            >
                <slot />
            </div>
        </template>
    </AppLayout>
</template>
