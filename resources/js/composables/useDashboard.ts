import { usePage } from '@inertiajs/vue3';
import { createSharedComposable } from '@vueuse/core';
import { ref, watch } from 'vue';

const _useDashboard = () => {
    const isNotificationsSlideoverOpen = ref(false);
    watch(
        () => usePage().url,
        () => {
            isNotificationsSlideoverOpen.value = false;
        },
    );
    return {
        isNotificationsSlideoverOpen,
    };
};

export const useDashboard = createSharedComposable(_useDashboard);
