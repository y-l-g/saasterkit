import { usePage } from '@inertiajs/vue3';

export function useFeatures() {
    const hasFeature = (
        featureName: App.Enums.Billing.FeatureEnum,
    ): boolean => {
        const features =
            usePage().props.user?.currentTeam?.subscription?.plan?.features ??
            [];
        return features.includes(featureName);
    };

    return { hasFeature };
}
