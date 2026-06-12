<script setup lang="ts">
import { BreadcrumbItem } from '@nuxt/ui';

import Container from '@/components/common/Container.vue';
import { edit as EditAppearance } from '@/routes/appearance';
import { edit as EditPassword } from '@/routes/password';
import { edit as EditProfile } from '@/routes/profile';
import { show as ShowTwoFactors } from '@/routes/two-factor';
import { teams } from '@/routes/user';
import { useAuthPage } from '@/composables/useAuthPage';
import { toDropdownMenuItems } from '@/utils/navigationMenu';
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
const page = useAuthPage();
const currentTeamSlug = computed(() => page.props.user.currentTeam!.slug);

const links = computed(() => [
    [
        {
            label: 'Profile',
            icon: 'i-lucide-user',
            to: EditProfile(currentTeamSlug.value).url,
            exact: true,
        },
        {
            label: 'Password',
            icon: 'i-lucide-lock',
            to: EditPassword(currentTeamSlug.value).url,
        },
        {
            label: 'Two-Factor Auth',
            icon: 'i-lucide-shield-check',
            to: ShowTwoFactors(currentTeamSlug.value).url,
        },
        {
            label: 'Appearance',
            icon: 'i-lucide-palette',
            to: EditAppearance(currentTeamSlug.value).url,
        },
        {
            label: 'Teams',
            icon: 'i-lucide-users',
            to: teams(currentTeamSlug.value).url,
        },
    ],
]);
const dropdownLinks = computed(() => toDropdownMenuItems(links.value));

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
                :items="dropdownLinks"
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
