interface PlanConfig {
    id: 'free' | 'pro' | 'premium';
    title: string;
    features: string[];
    description: string;
    month: {
        price: number;
        stripePriceId: string;
        discount?: number;
        badge?: string;
        billingCycle?: '/month' | '/year';
    };
    year: {
        price: number;
        stripePriceId: string;
        discount?: number;
        badge?: string;
        billingCycle?: '/month' | '/year';
    };
    highlight?: boolean;
    scale?: boolean;
}
const freeFeatures = [
    'Up to 10 projects',
    'Basic analytics',
    'Community support',
];
const basicFeatures = [...freeFeatures, 'CSV export', 'Email support'];
const premiumFeatures = [
    ...basicFeatures,
    'Premium templates',
    'High-quality PDF export',
    'API access',
];

export const plans: PlanConfig[] = [
    {
        id: 'free',
        title: 'Free',
        description: 'For bootstrappers and indie hackers.',
        features: freeFeatures,
        month: { price: 0, stripePriceId: '' },
        year: { price: 0, stripePriceId: '' },
    },
    {
        id: 'pro',
        title: 'Pro',
        description: 'For small businesses',
        features: basicFeatures,
        month: {
            price: 8,
            stripePriceId: import.meta.env.VITE_STRIPE_PRICE_PRO_MONTH,
            billingCycle: '/month',
        },
        year: {
            price: 96,
            stripePriceId: import.meta.env.VITE_STRIPE_PRICE_PRO_YEAR,
            discount: 80,
            badge: 'Most Popular',
            billingCycle: '/year',
        },
        highlight: true,
        scale: true,
    },
    {
        id: 'premium',
        title: 'Premium',
        description: 'For large companies',
        features: premiumFeatures,
        month: {
            price: 12,
            stripePriceId: import.meta.env.VITE_STRIPE_PRICE_PREMIUM_MONTH,
            billingCycle: '/month',
        },
        year: {
            price: 144,
            stripePriceId: import.meta.env.VITE_STRIPE_PRICE_PREMIUM_YEAR,
            discount: 120,
            billingCycle: '/year',
        },
    },
];
