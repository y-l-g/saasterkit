<script setup lang="ts">
import { useAuthPage } from '@/composables/useAuthPage';
import { useDashboard } from '@/composables/useDashboard';
import { dismiss } from '@/routes/notifications';
import { router } from '@inertiajs/vue3';
import { formatTimeAgo } from '@vueuse/core';
import { computed } from 'vue';

const { isNotificationsSlideoverOpen } = useDashboard();
const page = useAuthPage();
const notifications = computed(() => page.props.user.notifications || []);

const dismissNotification = (notificationId: number) => {
    router.post(
        dismiss({ notification: notificationId }).url,
        {},
        {
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <USlideover
        v-model:open="isNotificationsSlideoverOpen"
        title="Notifications"
    >
        <template #body>
            <div class="divide-y divide-muted">
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="py-4"
                >
                    <p class="text-sm font-medium text-highlighted">
                        {{ notification.title }}
                    </p>

                    <time
                        :datetime="notification.createdAt"
                        class="text-xs text-muted"
                        v-text="formatTimeAgo(new Date(notification.createdAt))"
                    />

                    <p class="text-sm text-toned">
                        {{ notification.body }}
                    </p>
                    <UButton
                        size="xs"
                        class="mt-2"
                        variant="subtle"
                        color="warning"
                        @click.stop="dismissNotification(notification.id)"
                        >Dismiss</UButton
                    >
                </div>
            </div>
        </template>
    </USlideover>
</template>
