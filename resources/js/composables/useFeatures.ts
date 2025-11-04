import { usePage } from '@inertiajs/vue3';

interface UseFeaturesReturn {
    hasFeature: (featureName: string) => boolean;
}

export function useFeatures(): UseFeaturesReturn {
    const hasFeature = (featureName: string): boolean => {
        const features =
            usePage().props.user?.currentTeam?.subscription?.plan?.features ??
            [];
        return features.includes(featureName);
    };

    return { hasFeature };
}
