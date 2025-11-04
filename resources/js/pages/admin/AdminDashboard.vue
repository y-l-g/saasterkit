<script setup lang="ts">
import CurrentOverviewStats from '@/components/admin/CurrentOverviewStats.vue';
import MonthPerformanceStats from '@/components/admin/MonthPerformanceStats.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { admin } from '@/routes';
import type { BreadcrumbItem } from '@nuxt/ui';

defineProps<{
    monthPerformance: {
        newSubscriptions: { value: number; variation: number };
        canceledSubscriptions: { value: number; variation: number };
        newUsers: { value: number; variation: number };
    };
    currentOverview: {
        activeSubscriptions: number;
        subscriptionsOnTrial: number;
        subscriptionsOnGracePeriod: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        label: 'Admin',
        to: admin().url,
    },
    {
        label: 'Dashboard',
    },
];
</script>

<template>
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <MonthPerformanceStats :stats="monthPerformance" />
            <CurrentOverviewStats :stats="currentOverview" />
        </div>
    </AdminLayout>
</template>
