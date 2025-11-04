<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { admin } from '@/routes';
import { store } from '@/routes/admin/notifications';
import { useForm } from '@inertiajs/vue3';

const breadcrumbItems = [
    {
        label: 'Admin',
        to: admin().url,
    },
    {
        label: 'Notifications',
    },
];

const form = useForm({
    title: '',
    body: '',
});

const submit = () => {
    form.submit(store(), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AdminLayout :breadcrumbs="breadcrumbItems">
        <UPageCard
            title="Send Notification"
            description="Send a new notification to all users."
        >
            <form @submit.prevent="submit" class="space-y-4">
                <UFormField
                    label="Title"
                    name="title"
                    :error="form.errors.title"
                >
                    <UInput v-model="form.title" class="w-full" required />
                </UFormField>
                <UFormField label="Body" name="body" :error="form.errors.body">
                    <UTextarea v-model="form.body" class="w-full" required />
                </UFormField>
                <UButton
                    type="submit"
                    block
                    :loading="form.processing"
                    variant="subtle"
                >
                    Send Notification
                </UButton>
            </form>
        </UPageCard>
    </AdminLayout>
</template>
