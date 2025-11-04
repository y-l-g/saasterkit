<script setup lang="ts">
import { computed } from 'vue';
import CkeckCircle from '~icons/lucide/check-circle';
import Hourglass from '~icons/lucide/hourglass';
import PauseCircle from '~icons/lucide/pause-circle';

const props = defineProps<{
    stats: {
        activeSubscriptions: number;
        subscriptionsOnTrial: number;
        subscriptionsOnGracePeriod: number;
    };
}>();

const formattedStats = computed(() => [
    {
        title: 'Active Subscriptions',
        icon: CkeckCircle,
        value: props.stats.activeSubscriptions,
    },
    {
        title: 'Subscriptions on Trial',
        icon: Hourglass,
        value: props.stats.subscriptionsOnTrial,
    },
    {
        title: 'Subscriptions on Grace Period',
        icon: PauseCircle,
        value: props.stats.subscriptionsOnGracePeriod,
    },
]);
</script>

<template>
    <UPageCard
        title="Current overview"
        description="Current overview of active subscriptions"
        variant="naked"
    >
        <UPageGrid class="gap-4 sm:gap-6 lg:grid-cols-3 lg:gap-px">
            <UPageCard
                v-for="(stat, index) in formattedStats"
                :key="index"
                :icon="stat.icon"
                :title="stat.title"
                variant="subtle"
                :ui="{
                    container: 'gap-y-1.5',
                    wrapper: 'items-start',
                    leading:
                        'p-2.5 rounded-full bg-primary/10 ring ring-inset ring-primary/25',
                    title: 'font-normal text-muted text-xs uppercase',
                }"
                class="first:rounded-l-lg last:rounded-r-lg hover:z-1 lg:rounded-none"
            >
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-semibold text-highlighted">
                        {{ stat.value }}
                    </span>
                </div>
            </UPageCard>
        </UPageGrid>
    </UPageCard>
</template>
