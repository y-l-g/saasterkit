<script setup lang="ts">
import { useCookieConsent } from '@/composables/useCookieConsent';
import { useFlashMessages } from '@/composables/useFlashMessages';
import { useUrlQueryToasts } from '@/composables/useUrlQueryToasts';
import { useUserTheme } from '@/composables/useUserTheme';
import { onMounted } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

onMounted(() => {
    useFlashMessages();
    useUrlQueryToasts();
    useCookieConsent();
    useUserTheme();
});

useHead({
    titleTemplate: `%s - ${appName}`,
    title: appName,
});
</script>

<template>
    <Suspense>
        <UApp>
            <slot></slot>
        </UApp>
    </Suspense>
</template>
