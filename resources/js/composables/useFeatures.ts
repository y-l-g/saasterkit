import { usePage } from '@inertiajs/vue3';

export function useFeatures() {
    const hasFeature = (
        featureName: App.Enums.Billing.FeatureEnum,
    ): boolean => {
        const subscription = usePage().props.user?.currentTeam?.subscription;
        const features = subscription?.valid
            ? subscription.plan.features
            : [];

        return features.includes(featureName);
    };

    return { hasFeature };
}
