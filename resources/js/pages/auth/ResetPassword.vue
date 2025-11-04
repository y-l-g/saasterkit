<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    token: string;
    email: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submitReset = () => {
    form.submit(update(), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AuthLayout
        title="Reset password"
        description="Please enter your new password below"
    >
        <Head title="Reset password" />

        <form @submit.prevent="submitReset" class="space-y-4">
            <UFormField label="Email" name="email" :error="form.errors.email">
                <UInput
                    required
                    id="email"
                    type="email"
                    name="email"
                    class="w-full"
                    autocomplete="email"
                    v-model="form.email"
                    readonly
                />
            </UFormField>

            <UFormField
                label="Password"
                name="password"
                :error="form.errors.password"
            >
                <UInput
                    id="password"
                    type="password"
                    name="password"
                    class="w-full"
                    autocomplete="new-password"
                    v-model="form.password"
                    autofocus
                    placeholder="Password"
                />
            </UFormField>

            <UFormField
                label="Confirm Password"
                name="password_confirmation"
                :error="form.errors.password_confirmation"
            >
                <UInput
                    id="password_confirmation"
                    type="password"
                    class="w-full"
                    name="password_confirmation"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                    placeholder="Confirm password"
                />
            </UFormField>

            <UButton
                type="submit"
                variant="subtle"
                block
                class="w-full"
                :loading="form.processing"
                data-test="reset-password-button"
            >
                Reset password
            </UButton>
        </form>
    </AuthLayout>
</template>
