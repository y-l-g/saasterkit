<script setup lang="ts">
import { useTable } from '@/composables/useTable';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { admin } from '@/routes';
import { index } from '@/routes/admin/subscriptions';
import { PaginatedCollection } from '@/types';
import { formatDate } from '@/utils';
import type { RadioGroupItem, TableColumn } from '@nuxt/ui';
import { useChangeCase } from '@vueuse/integrations/useChangeCase';
import { h, resolveComponent, watch } from 'vue';

const breadcrumbItems = [
    {
        label: 'Admin',
        to: admin().url,
    },
    {
        label: 'Subscriptions',
    },
];

const props = defineProps<{
    subscriptions: PaginatedCollection<App.Data.Billing.SubscriptionData>;
}>();

const { sort, filters, onSort, onPageChange, syncCurrentPage } = useTable(
    index().url,
    props.subscriptions.current_page,
    { search: '', plan: '', status: '' },
);
const periodItems: RadioGroupItem[] = [
    { value: 'month', label: 'Monthly' },
    { value: 'year', label: 'Yearly' },
    { value: '', label: 'All' },
];
const planItems: RadioGroupItem[] = [
    { value: 'pro', label: 'Pro' },
    { value: 'premium', label: 'Premium' },
    { value: '', label: 'All' },
];
const statusItems: RadioGroupItem[] = [
    { value: 'canceled', label: 'Canceled' },
    { value: 'active', label: 'Active' },
    { value: 'trialing', label: 'Trialing' },
    { value: '', label: 'All' },
];

watch(
    () => props.subscriptions.current_page,
    (newPage) => {
        syncCurrentPage(newPage);
    },
);

const UButton = resolveComponent('UButton');
const UBadge = resolveComponent('UBadge');

const columns: TableColumn<App.Data.Billing.SubscriptionData>[] = [
    {
        accessorKey: 'id',
        header: 'ID',
    },
    {
        accessorKey: 'plan.name',
        header: 'Plan',
        cell: ({ row }) =>
            useChangeCase(row.original.plan?.name, 'sentenceCase').value,
    },
    {
        accessorKey: 'period',
        header: 'Period',
        cell: ({ row }) =>
            useChangeCase(row.original.period, 'sentenceCase').value,
    },
    {
        accessorKey: 'team.owner.email',
        header: 'Owner Email',
        cell: ({ row }) => row.original.team?.owner?.email,
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
            const status = row.original.status;
            const color = {
                active: 'success' as const,
                canceled: 'error' as const,
                trialing: 'neutral' as const,
            }[status as 'active' | 'canceled' | 'trialing'];

            return h(
                UBadge,
                { class: 'capitalize', variant: 'subtle', color },
                () => status,
            );
        },
    },
    {
        accessorKey: 'endsAt',
        header: 'End',
        cell: ({ row }) => {
            if (row.original.endsAt) {
                return formatDate(row.original.endsAt, 'en');
            }
            return '—';
        },
    },
    {
        accessorKey: 'createdAt',
        cell: ({ row }) => formatDate(row.original.createdAt, 'en'),
        header: ({ column }) => {
            const isSortedAsc = sort.value === column.id;
            const isSortedDesc = sort.value === `-${column.id}`;

            let iconName = 'i-lucide-arrow-up-down';
            if (isSortedAsc) iconName = 'i-lucide-arrow-up-narrow-wide';
            if (isSortedDesc) iconName = 'i-lucide-arrow-down-wide-narrow';

            return h(UButton, {
                color: 'neutral',
                variant: 'ghost',
                label: 'Created On',
                icon: iconName,
                class: '-mx-2.5',
                onClick: () => onSort(column.id),
            });
        },
    },
];
</script>

<template>
    <AdminLayout :breadcrumbs="breadcrumbItems">
        <UCard>
            <template #header>
                <h1 class="text-lg leading-6 font-semibold">
                    Subscription Management
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    A list of all the subscriptions in your application.
                </p>
            </template>
            <div
                class="flex justify-between border-b border-accented px-4 py-3.5"
            >
                <UInput
                    v-model="filters.search"
                    placeholder="Search..."
                    icon="i-lucide-search"
                    class="max-w-sm"
                />
                <UPopover mode="click" :open-delay="500" :close-delay="300">
                    <UButton
                        icon="i-lucide-filter"
                        color="neutral"
                        variant="subtle"
                    />

                    <template #content>
                        <UPageCard orientation="horizontal">
                            <UFormField label="Billing Period">
                                <URadioGroup
                                    size="sm"
                                    variant="table"
                                    v-model="filters.period"
                                    :items="periodItems"
                            /></UFormField>

                            <UFormField label="Plan">
                                <URadioGroup
                                    size="sm"
                                    variant="table"
                                    v-model="filters.plan"
                                    :items="planItems"
                            /></UFormField>
                            <UFormField label="Status">
                                <URadioGroup
                                    size="sm"
                                    variant="table"
                                    v-model="filters.status"
                                    :items="statusItems"
                            /></UFormField>
                        </UPageCard>
                    </template>
                </UPopover>
            </div>
            <UTable
                :data="subscriptions.data"
                :columns="columns"
                sticky
                class="max-h-[400px] lg:max-h-none"
            />

            <template
                #footer
                v-if="subscriptions?.total > subscriptions.per_page"
            >
                <div class="flex items-center justify-center">
                    <UPagination
                        :total="subscriptions.total"
                        :itemsPerPage="subscriptions.per_page"
                        :model-value="subscriptions.current_page"
                        @update:page="onPageChange"
                    />
                </div>
            </template>
        </UCard>
    </AdminLayout>
</template>
