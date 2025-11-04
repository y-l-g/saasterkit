<script setup lang="ts">
import { plans } from '@/config/plans';
import { register } from '@/routes';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Props {
    orientation?: 'grid' | 'column';
    onSubscribe: (payload: { stripePriceId: string }) => void;
    loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    orientation: 'grid',
    loading: false,
});

const billingInterval = ref<'month' | 'year'>('month');

const displayPlans = computed(() => {
    return plans.map((plan) => {
        const priceInfo = plan[billingInterval.value];

        const getButton = () => {
            if (plan.id === 'free') {
                return {
                    label: 'Get Started',
                    onClick: () => router.get(register().url),
                    color: 'primary' as const,
                    variant: 'subtle' as const,
                };
            }
            return {
                label: 'Subscribe',
                loading: props.loading,
                onClick: () => {
                    props.onSubscribe({
                        stripePriceId: priceInfo.stripePriceId,
                    });
                },
                color: 'primary' as const,
                variant: 'subtle' as const,
            };
        };

        return {
            title: plan.title,
            price: `${priceInfo.price}€`,
            features: plan.features,
            badge: priceInfo.badge,
            description: plan.description,
            discount: priceInfo.discount ? `${priceInfo.discount}€` : undefined,
            highlight: plan.highlight ?? false,
            scale: plan.scale ?? false,
            billingCycle: priceInfo.billingCycle ?? undefined,
            button: getButton(),
        };
    });
});

const setBillingInterval = (period: 'month' | 'year') => {
    billingInterval.value = period;
};
</script>

<template>
    <div>
        <div class="mb-12 flex justify-center">
            <UFieldGroup size="lg">
                <UButton
                    label="Month"
                    :variant="
                        billingInterval === 'month' ? 'subtle' : 'outline'
                    "
                    @click="setBillingInterval('month')"
                />
                <UButton
                    label="Year"
                    :variant="billingInterval === 'year' ? 'subtle' : 'outline'"
                    @click="setBillingInterval('year')"
                />
            </UFieldGroup>
        </div>

        <UPageGrid v-if="orientation === 'grid'">
            <UPricingPlan
                v-for="(plan, index) in displayPlans"
                :key="index"
                v-bind="plan"
                variant="soft"
            />
        </UPageGrid>

        <div v-else class="flex flex-col gap-8">
            <UPricingPlan
                v-for="(plan, index) in displayPlans"
                :key="index"
                v-bind="plan"
                :scale="false"
                variant="outline"
                orientation="horizontal"
            />
        </div>
    </div>
</template>
