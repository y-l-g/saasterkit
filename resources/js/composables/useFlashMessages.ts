import { fortifyStatusMessages } from '@/utils';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import ILucideAlertCircle from '~icons/lucide/alert-circle';
import ILucideCheck from '~icons/lucide/check';
import ILucideInfo from '~icons/lucide/info';
import { useAppToast } from './useAppToast';

export function useFlashMessages() {
    const page = usePage();
    const toast = useAppToast();

    const flashToastConfig = {
        success: { icon: ILucideCheck, progress: false },
        info: {
            color: 'info',
            icon: ILucideInfo,
            progress: false,
        },
        status: {
            color: 'info',
            icon: ILucideInfo,
            progress: false,
        },
        error: {
            color: 'error',
            icon: ILucideAlertCircle,
        },
    } as const;

    watch(
        () => page.props.flash,
        (flash: any) => {
            if (!flash) return;

            for (const type in flashToastConfig) {
                const key = type as keyof typeof flashToastConfig;
                const message = flash[key];
                if (message) {
                    let description = message;

                    if (key === 'status') {
                        description =
                            fortifyStatusMessages[
                                message as App.Enums.Auth.FortifyStatusEnum
                            ] ?? message;
                    }

                    toast.add({
                        description,
                        ...flashToastConfig[key],
                    });

                    flash[key] = null;
                }
            }
        },
        { deep: true, immediate: true },
    );
}
