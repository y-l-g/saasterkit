<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@nuxt/ui';

const breadcrumbs = [
    {
        label: 'Dashboard',
        to: dashboard().url,
    },
] satisfies BreadcrumbItem[];

const features = [
    {
        icon: 'i-lucide-rocket',
        title: 'Production-ready from day one',
        description:
            'Pre-configured with TypeScript, ESLint, Tailwind CSS, and Vite. Focus on building features, not setting up tooling.',
    },
    {
        icon: 'i-lucide-palette',
        title: 'Beautiful by default',
        description:
            "Powered by Nuxt UI's design system with automatic dark mode, consistent spacing, and polished components that look great out of the box.",
    },
    {
        icon: 'i-lucide-zap',
        title: 'Lightning fast',
        description:
            "Optimized with Vite's blazing fast HMR, automatic code splitting, and tree-shaking. Your users will love the speed.",
    },
    {
        icon: 'i-lucide-blocks',
        title: '100+ components included',
        description:
            "Access Nuxt UI's comprehensive component library. From forms to navigation, everything is accessible, responsive, and customizable.",
    },
    {
        icon: 'i-lucide-code-2',
        title: 'Developer experience first',
        description:
            'Auto-imports, hot module replacement, TypeScript support, and Vue DevTools integration. Write less boilerplate and ship more features.',
    },
    {
        icon: 'i-lucide-shield-check',
        title: 'Built for scale',
        description:
            'Modern Vue 3 architecture with Composition API, proper error handling, and security best practices built-in.',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs>
        <template #body>
            <div class="mt-[-55px]">
                <UPageSection
                    id="features"
                    title="Everything you need to build modern Vue apps"
                    description="Start with a solid foundation. This template includes all the essentials for building production-ready applications with Nuxt UI's powerful component system."
                    :features
                />
            </div>
        </template>
    </AppLayout>
</template>
