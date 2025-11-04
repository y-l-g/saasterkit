<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import verification from '@/routes/verification';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthLayout
        title="Verify email"
        description="Please verify your email address by clicking on the link we just emailed to you."
    >
        <Head title="Email verification" />

        <Form
            v-bind="verification.send.form()"
            class="flex justify-between"
            v-slot="{ processing }"
        >
            <UButton :loading="processing" variant="subtle" type="submit">
                Resend verification email
            </UButton>
            <UButton
                method="post"
                variant="subtle"
                color="warning"
                :to="logout().url"
            >
                Log out
            </UButton>
        </Form>
    </AuthLayout>
</template>
