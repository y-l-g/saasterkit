<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    stats: {
        newSubscriptions: { value: number; variation: number };
        canceledSubscriptions: { value: number; variation: number };
        newUsers: { value: number; variation: number };
    };
}>();

const formattedStats = computed(() => [
    {
        title: `New subscriptions`,
        icon: 'i-lucide-user-plus',
        value: props.stats.newSubscriptions.value,
        variation: props.stats.newSubscriptions.variation,
        is_good_when_positive: true,
    },
    {
        title: `Canceled Subscriptions`,
        icon: 'i-lucide-user-minus',
        value: props.stats.canceledSubscriptions.value,
        variation: props.stats.canceledSubscriptions.variation,
        is_good_when_positive: false,
    },
    {
        title: `New Users`,
        icon: 'i-lucide-user-round-plus',
        value: props.stats.newUsers.value,
        variation: props.stats.newUsers.variation,
        is_good_when_positive: true,
    },
]);
</script>

<template>
    <UPageCard
        title="Month overview"
        description="30 Past days compared to 30 previous days"
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

                    <UBadge
                        :color="
                            stat.variation >= 0 === stat.is_good_when_positive
                                ? 'success'
                                : 'error'
                        "
                        variant="subtle"
                    >
                        {{ stat.variation >= 0 ? '+' : ''
                        }}{{ stat.variation }}%
                    </UBadge>
                </div>
            </UPageCard>
        </UPageGrid>
    </UPageCard>
</template>
