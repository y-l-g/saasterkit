<script setup lang="ts">
import { BreadcrumbItem, NavigationMenuItem } from '@nuxt/ui';

import Container from '@/components/common/Container.vue';
import { edit as EditAppearance } from '@/routes/appearance';
import { edit as EditPassword } from '@/routes/password';
import { edit as EditProfile } from '@/routes/profile';
import { show as ShowTwoFactors } from '@/routes/two-factor';
import { teams } from '@/routes/user';
import { usePage } from '@inertiajs/vue3';
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

const links = [
    [
        {
            label: 'Profile',
            icon: 'i-lucide-user',
            to: EditProfile().url,
            exact: true,
        },
        {
            label: 'Password',
            icon: 'i-lucide-lock',
            to: EditPassword().url,
        },
        {
            label: 'Two-Factor Auth',
            icon: 'i-lucide-shield-check',
            to: ShowTwoFactors().url,
        },
        {
            label: 'Appearance',
            icon: 'i-lucide-palette',
            to: EditAppearance().url,
        },
        {
            label: 'Teams',
            icon: 'i-lucide-users',
            to: teams().url,
        },
    ],
] satisfies NavigationMenuItem[][];

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
            <Container>
                <slot />
            </Container>
        </template>
    </AppLayout>
</template>
