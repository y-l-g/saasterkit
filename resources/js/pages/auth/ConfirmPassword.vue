<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';
import { Form } from '@inertiajs/vue3';

useHead({
    title: 'Confirm password',
});
</script>

<template>
    <AuthLayout
        title="Confirm your password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    >
        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
            class="space-y-4"
        >
            <UFormField
                label="Password"
                name="password"
                :error="errors.password"
            >
                <UInput
                    id="password"
                    type="password"
                    name="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                    autofocus
                />
            </UFormField>
            <UButton
                variant="subtle"
                block
                type="submit"
                :loading="processing"
                data-test="confirm-password-button"
            >
                Confirm Password
            </UButton>
        </Form>
    </AuthLayout>
</template>
