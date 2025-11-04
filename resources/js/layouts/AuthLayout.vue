<script setup lang="ts">
import { AppLogoIcon } from '@/icons/AppLogoIcon';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex h-screen items-center justify-center px-4">
        <UPageCard
            variant="subtle"
            class="w-full max-w-sm"
            :title="title"
            :description="description"
            :icon="AppLogoIcon"
        >
            <slot />
        </UPageCard>
    </div>
</template>
