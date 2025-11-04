import { router } from '@inertiajs/vue3';
import { useToast } from '@nuxt/ui/runtime/composables/useToast.js';
import { useUrlSearchParams, whenever } from '@vueuse/core';
import ILucideAlertCircle from '~icons/lucide/alert-circle';
import ILucideCheck from '~icons/lucide/check';
import ILucideInfo from '~icons/lucide/info';

export function useUrlQueryToasts() {
    const toast = useToast();
    const params = useUrlSearchParams('history');

    const urlToastConfig = {
        success: { title: 'Success', icon: ILucideCheck },
        info: { title: 'Info', color: 'info', icon: ILucideInfo },
        error: {
            title: 'Error',
            color: 'error',
            icon: ILucideAlertCircle,
        },
    } as const;

    for (const key of Object.keys(urlToastConfig)) {
        const paramKey = key as keyof typeof urlToastConfig;

        whenever(
            () => params[paramKey],
            (message) => {
                if (typeof message !== 'string') return;

                toast.add({
                    description: message,
                    ...urlToastConfig[paramKey],
                });

                const currentParams = new URLSearchParams(
                    window.location.search,
                );
                currentParams.delete(paramKey);
                const newSearch = currentParams.toString();
                const newPath =
                    window.location.pathname +
                    (newSearch ? `?${newSearch}` : '');

                router.get(
                    newPath,
                    {},
                    {
                        replace: true,
                        preserveState: true,
                        preserveScroll: true,
                    },
                );
            },
            { immediate: true },
        );
    }
}
