import { usePage } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';

export function useUserTheme() {
    const appConfig = useAppConfig();
    const page = usePage();
    const user = computed(() => page.props.user);

    watchEffect(() => {
        if (user.value) {
            if (user.value.primaryColor) {
                appConfig.ui.colors.primary = user.value.primaryColor;
            }
            if (user.value.neutralColor) {
                appConfig.ui.colors.neutral = user.value.neutralColor;
            } else if (user.value.secondaryColor) {
                appConfig.ui.colors.neutral = user.value.secondaryColor;
            }
        }
    });
}
