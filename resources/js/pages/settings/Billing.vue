<script setup lang="ts">
import PricingPlans from '@/components/billing/PricingPlans.vue';
import TeamsLayout from '@/layouts/TeamLayout.vue';
import { checkout, portal, show } from '@/routes/billing';
import { Deferred, router } from '@inertiajs/vue3';
import type { TableColumn } from '@nuxt/ui';
import { useTimeAgoIntl } from '@vueuse/core';
import { computed, h, ref, resolveComponent } from 'vue';

interface Props {
    invoices?: Array<App.Data.Billing.InvoiceData>;
    team: App.Data.Teams.TeamData;
}

const props = defineProps<Props>();

const breadcrumbs = computed(() => [
    {
        label: props.team.name,
        to: show(props.team.slug).url,
    },
    {
        label: 'Billing',
    },
]);

const isProcessingCheckout = ref(false);
const subscriptionNotice = computed(() => {
    const subscription = props.team.subscription;

    if (!subscription || (subscription.valid && !subscription.onGracePeriod)) {
        return null;
    }

    if (subscription.onGracePeriod) {
        return {
            color: 'warning' as const,
            title: 'Subscription ending',
            description:
                'This subscription has been canceled and remains available until the end of the billing period.',
            actionLabel: 'Resume Subscription',
            showCheckout: false,
        };
    }

    if (subscription.status === 'canceled') {
        return {
            color: 'neutral' as const,
            title: 'Subscription ended',
            description:
                'This team is back on the free plan and can start a new subscription.',
            actionLabel: null,
            showCheckout: true,
        };
    }

    return {
        color: 'warning' as const,
        title: 'Subscription pending',
        description: 'Stripe has not marked this subscription active yet.',
        actionLabel: 'Manage Subscription',
        showCheckout: false,
    };
});

const handleSubscribeForTeam = ({
    stripePriceId,
}: {
    stripePriceId: string;
}) => {
    isProcessingCheckout.value = true;
    router.get(
        checkout({
            current_team: props.team.slug,
            stripePriceId: stripePriceId,
        }).url,
        {},
        { onFinish: () => (isProcessingCheckout.value = false) },
    );
};

const UBadge = resolveComponent('UBadge');
const UButton = resolveComponent('UButton');

const columns: TableColumn<App.Data.Billing.InvoiceData>[] = [
    {
        accessorKey: 'number',
        header: 'Invoice',
    },
    {
        accessorKey: 'date',
        header: 'Date',
        cell: ({ row }) =>
            new Date(row.getValue('date') as string).toLocaleDateString('en'),
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
            const color = {
                paid: 'success' as const,
                draft: 'neutral' as const,
                open: 'error' as const,
            }[row.getValue('status') as string];

            return h(
                UBadge,
                { class: 'capitalize', variant: 'subtle', color },
                () => row.getValue('status'),
            );
        },
    },
    {
        accessorKey: 'total',
        header: 'Amount',
    },
    {
        accessorKey: 'url',
        header: 'Download',
        cell: ({ row }) => {
            return h(UButton, {
                to: row.getValue('url'),
                target: '_blank',
                rel: 'noopener noreferrer',
                variant: 'link',
                icon: 'i-lucide-download',
                'aria-label': `Download invoice ${row.getValue('number')}`,
            });
        },
    },
];
const isRedirecting = ref(false);

const goToPortal = () => {
    isRedirecting.value = true;
    router.get(
        portal(props.team.slug).url,
        {},
        {
            onFinish: () => {
                isRedirecting.value = false;
            },
        },
    );
};
</script>

<template>
    <TeamsLayout :breadcrumbs="breadcrumbs">
        <UPageCard
            title="My Subscription"
            icon="i-lucide-credit-card"
            variant="soft"
        >
            <template #description>
                <div v-if="!team.subscription">
                    <p class="text-toned">
                        Your team is not subscribed to any plan.
                    </p>
                    <PricingPlans
                        class="mt-8"
                        orientation="column"
                        :on-subscribe="handleSubscribeForTeam"
                        :loading="isProcessingCheckout"
                    />
                </div>
                <div v-else-if="subscriptionNotice" class="space-y-4">
                    <UAlert
                        :color="subscriptionNotice.color"
                        variant="soft"
                        :title="subscriptionNotice.title"
                        :description="subscriptionNotice.description"
                    />
                    <p
                        v-if="
                            team.subscription.onGracePeriod &&
                            team.subscription.endsAt
                        "
                    >
                        You have canceled your
                        <strong>{{ team.subscription.plan.name }}</strong>
                        plan. Your access ends
                        {{
                            useTimeAgoIntl(team.subscription.endsAt, {
                                locale: 'en',
                            })
                        }}.
                    </p>
                    <UButton
                        v-if="subscriptionNotice.actionLabel"
                        @click="goToPortal()"
                        :loading="isRedirecting"
                        variant="subtle"
                    >
                        {{ subscriptionNotice.actionLabel }}
                    </UButton>
                    <PricingPlans
                        v-if="subscriptionNotice.showCheckout"
                        class="mt-8"
                        orientation="column"
                        :on-subscribe="handleSubscribeForTeam"
                        :loading="isProcessingCheckout"
                    />
                </div>
                <div v-else-if="team.subscription?.valid" class="space-y-3">
                    <p
                        v-if="
                            team.subscription.status === 'trialing' &&
                            team.subscription.trialEndsAt
                        "
                    >
                        Your team is trialing the
                        <strong>{{ team.subscription.plan.name }}</strong>
                        plan. Trial ends
                        {{
                            useTimeAgoIntl(team.subscription.trialEndsAt!, {
                                locale: 'en',
                            })
                        }}.
                    </p>
                    <p v-else>
                        Your team is subscribed to the
                        <strong>{{ team.subscription.plan.name }}</strong>
                        plan.
                    </p>

                    <UButton
                        @click="goToPortal()"
                        :loading="isRedirecting"
                        class="mt-4"
                        variant="subtle"
                    >
                        Manage Subscription
                    </UButton>
                </div>
            </template>
        </UPageCard>
        <Deferred data="invoices">
            <template #fallback>
                <UButton loading variant="link" />
            </template>
            <div v-if="invoices && invoices.length > 0">
                <UPageCard
                    title="Invoices"
                    icon="i-lucide-file-text"
                    variant="soft"
                >
                    <UTable :data="invoices" :columns="columns" />
                </UPageCard>
            </div>
        </Deferred>
    </TeamsLayout>
</template>
