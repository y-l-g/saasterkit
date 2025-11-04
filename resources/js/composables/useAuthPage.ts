import type { AuthenticatedPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useAuthPage<T>() {
    const page = usePage<AuthenticatedPageProps & T>();
    const user = computed(() => page.props.user);
    if (!user.value) {
        throw new Error('User is not defined');
    }
    return page;
}
