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
        to: show({ team: props.team.id }).url,
    },
    {
        label: 'Billing',
    },
]);

const isProcessingCheckout = ref(false);

const handleSubscribeForTeam = ({
    stripePriceId,
}: {
    stripePriceId: string;
}) => {
    isProcessingCheckout.value = true;
    router.get(
        checkout({
            stripePriceId: stripePriceId,
            team: props.team.id,
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
                variant: 'link',
                icon: 'i-lucide-download',
            });
        },
    },
];
const isRedirecting = ref(false);

const goToPortal = () => {
    isRedirecting.value = true;
    router.get(
        portal(props.team.id).url,
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
                <div
                    v-if="
                        team.subscription &&
                        team.subscription.onGracePeriod &&
                        team.subscription.endsAt
                    "
                    class="space-y-3"
                >
                    <p>
                        You have cancelled your
                        <strong>{{ team.subscription.plan.name }}</strong>
                        plan. Your access will end
                        {{
                            useTimeAgoIntl(team.subscription.endsAt, {
                                locale: 'en',
                            })
                        }}
                        >.
                    </p>
                    <UButton
                        @click="goToPortal()"
                        :loading="isRedirecting"
                        variant="subtle"
                    >
                        Resume Subscription
                    </UButton>
                </div>
                <div v-if="team.subscription && !team.subscription.endsAt">
                    <div
                        v-if="
                            team.subscription.status === 'trialing' &&
                            team.subscription.endsAt
                        "
                    ></div>
                    <p>
                        Your team is on trial on the
                        <strong>{{ team.subscription.plan.name }}</strong>
                        plan. Trial will ends
                        {{
                            useTimeAgoIntl(team.subscription.trialEndsAt!, {
                                locale: 'en',
                            })
                        }}
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
