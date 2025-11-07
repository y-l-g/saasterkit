<script setup lang="ts">
import { dashboard, login, logout, privacy, register } from '@/routes';
import { router, usePage } from '@inertiajs/vue3';
import { DrawerProps, NavigationMenuItem } from '@nuxt/ui';
import { useBreakpoints } from '@vueuse/core';
import IBiTwitterX from '~icons/bi/twitter-x';
import ILucideDollarSign from '~icons/lucide/dollar-sign';
import ILucideGithub from '~icons/lucide/github';

const page = usePage();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const appUrl = import.meta.env.VITE_APP_URL;

useSeoMeta({
    titleTemplate: '%s - %siteName',
    description: 'The default description for your SaaS.',
    ogDescription: 'The default description for your SaaS.',
    ogTitle: appName,
    ogImage: `${appUrl}/seo_image.jpg`,
    twitterImage: `${appUrl}/seo_image.jpg`,
    twitterCard: 'summary_large_image',
});

const breakpoints = useBreakpoints({
    xs: 410,
});

const isXs = breakpoints.smallerOrEqual('xs');

const drawerMenuProps: DrawerProps = {
    direction: 'top',
};

const footerItems: NavigationMenuItem[] = [
    {
        label: 'Privacy',
        to: privacy().url,
    },
    {
        label: 'Documentation',
        to: 'https://doc.saasterkit.com',
        target: '_blank',
    },
];
</script>

<template>
    <UHeader
        to="/"
        mode="drawer"
        :menu="drawerMenuProps"
        :ui="{
            toggle: 'hidden',
        }"
    >
        <template #title>
            <UButton
                variant="link"
                size="xl"
                :icon="ILucideDollarSign"
                class="cursor-pointer"
                ><span v-if="!isXs" class="ml-[-12px]"
                    ><span class="text-default">aas</span>terkit</span
                ></UButton
            >
        </template>
        <template #right>
            <UColorModeButton
                variant="link"
                size="lg"
                as="button"
                :disabled="false"
            />

            <UTooltip text="Open on GitHub" :kbds="['meta', 'G']">
                <UButton
                    color="neutral"
                    variant="link"
                    to="https://github.com/y-l-g/saasterkit"
                    target="_blank"
                    :icon="ILucideGithub"
                    aria-label="GitHub"
                />
            </UTooltip>
            <template v-if="!page.props.user">
                <UButton
                    label="Login"
                    :to="login().url"
                    variant="link"
                    color="neutral"
                />
                <UButton label="Register" :to="register().url" variant="soft" />
            </template>
            <template v-else>
                <UButton
                    label="Dashboard"
                    :to="dashboard().url"
                    variant="link"
                    color="neutral"
                />
                <UButton
                    label="Log Out"
                    :onclick="() => router.post(logout().url)"
                    variant="soft"
                    color="neutral"
                />
            </template>
        </template>
    </UHeader>

    <main><slot></slot></main>

    <UFooter>
        <template #left>
            <p class="text-sm text-muted">
                {{ new Date().getFullYear() }}
                Laravel Starter Kit by
                <a href="https://y-l.fr">YL</a>
            </p>
        </template>
        <UNavigationMenu :items="footerItems" variant="link" />
        <template #right>
            <UButton
                :icon="IBiTwitterX"
                color="neutral"
                variant="link"
                to="https://x.com/_y_l_g_"
                target="_blank"
                aria-label="X" />
            <UButton
                to="https://github.com/y-l-g/saasterkit"
                target="_blank"
                color="neutral"
                variant="link"
                :icon="ILucideGithub"
                aria-label="GitHub Repository" /></template
    ></UFooter>
</template>
