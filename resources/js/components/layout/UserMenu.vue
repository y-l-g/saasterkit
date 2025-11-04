<script setup lang="ts">
import { useAuthPage } from '@/composables/useAuthPage';
import { logout } from '@/routes';
import { settings } from '@/routes/user';
import { router } from '@inertiajs/vue3';
import type { DropdownMenuItem } from '@nuxt/ui';
import { computed } from 'vue';

const page = useAuthPage();

const items = computed<DropdownMenuItem[][]>(() => [
    [
        {
            type: 'label',
            label: page.props.user.name,
            avatar: {
                alt: page.props.user.name,
            },
        },
    ],
    [
        {
            label: 'Settings',
            icon: 'i-lucide-settings',
            to: settings().url,
        },
    ],
    [
        {
            label: 'Log out',
            icon: 'i-lucide-log-out',
            onClick: () => {
                router.post(logout());
            },
        },
    ],
]);
</script>

<template>
    <UDropdownMenu :items="items">
        <UButton variant="link"
            ><UAvatar :alt="page.props.user.name" size="lg"
        /></UButton>
    </UDropdownMenu>
</template>
