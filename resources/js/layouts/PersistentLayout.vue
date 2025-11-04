<script setup lang="ts">
import { useCookieConsent } from '@/composables/useCookieConsent';
import { useFlashMessages } from '@/composables/useFlashMessages';
import { useUrlQueryToasts } from '@/composables/useUrlQueryToasts';
import { useUserTheme } from '@/composables/useUserTheme';
import { onMounted } from 'vue';

onMounted(() => {
    useFlashMessages();
    useUrlQueryToasts();
    useCookieConsent();
    useUserTheme();
});
</script>

<template>
    <Suspense>
        <UApp>
            <slot></slot>
        </UApp>
    </Suspense>
</template>
