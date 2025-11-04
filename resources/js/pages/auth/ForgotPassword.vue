<script setup lang="ts">
import CardInfo from '@/components/common/CardInfo.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import password from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthLayout
        title="Forgot password"
        description="Enter your email to receive a password reset link"
    >
        <Head title="Forgot password" />
        <Form
            class="space-y-4"
            v-bind="password.email.form()"
            v-slot="{ errors, processing }"
        >
            <UFormField
                :error="errors.email"
                label="Email address"
                name="email"
                required
            >
                <UInput
                    required
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    autofocus
                    class="w-full"
                    placeholder="email@example.com"
                    :loading="processing"
                />
            </UFormField>

            <UButton
                variant="subtle"
                block
                :loading="processing"
                data-test="email-password-reset-link-button"
                type="submit"
            >
                Email password reset link
            </UButton>
        </Form>

        <CardInfo>
            Or, return to
            <ULink :href="login().url" active>log in</ULink>
        </CardInfo>
    </AuthLayout>
</template>
