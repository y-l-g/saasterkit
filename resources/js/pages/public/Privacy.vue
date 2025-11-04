<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
import { useHead } from '@unhead/vue';

defineProps<{ content: string }>();

useHead({
    title: 'Privacy',
});
</script>

<template>
    <PublicLayout>
        <UPageSection>
            <article v-html="content" class="prose dark:prose-invert"></article
        ></UPageSection>
    </PublicLayout>
</template>
