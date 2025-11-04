import { useToast } from '@nuxt/ui/runtime/composables/useToast.js';
import { useStorage } from '@vueuse/core';

export function useCookieConsent() {
    const toast = useToast();
    const cookie = useStorage('cookie-consent', 'pending');

    if (cookie.value !== 'accepted') {
        toast.add({
            title: 'We use first-party cookies to enhance your experience on our website.',
            duration: 0,
            close: false,
            actions: [
                {
                    label: 'Accept',
                    color: 'neutral',
                    variant: 'outline',
                    onClick: () => {
                        cookie.value = 'accepted';
                    },
                },
                { label: 'Opt out', color: 'neutral', variant: 'ghost' },
            ],
        });
    }
}
