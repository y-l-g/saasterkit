<script setup lang="ts">
import PasswordInput from '@/components/PasswordInput.vue';
import { useAuthPage } from '@/composables/useAuthPage';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { settings } from '@/routes/user';
import { update } from '@/routes/user-password';
import { Form } from '@inertiajs/vue3';

const page = useAuthPage();
const currentTeamSlug = page.props.user.currentTeam!.slug;

const breadcrumbs = [
    { label: 'Settings', to: settings(currentTeamSlug).url },
    { label: 'Password' },
];
useHead({
    title: 'Password settings',
});
</script>

<template>
    <SettingsLayout :breadcrumbs="breadcrumbs">
        <UPageCard
            :title="
                page.props.user.hasPassword
                    ? 'Update password'
                    : 'Create a password'
            "
            description="Ensure your account is using a long, random password to stay secure."
            variant="soft"
            icon="i-lucide-lock"
        >
            <Form
                :action="update()"
                :options="{
                    preserveScroll: true,
                }"
                reset-on-success
                :reset-on-error="[
                    'password',
                    'password_confirmation',
                    'current_password',
                ]"
                class="space-y-4"
                v-slot="{ errors, processing }"
            >
                <UFormField
                    v-if="page.props.user.hasPassword"
                    label="Current password"
                    name="current_password"
                    :error="errors.current_password"
                    required
                >
                    <PasswordInput
                        required
                        id="current_password"
                        name="current_password"
                        autocomplete="current-password"
                        placeholder="Current password"
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    label="New password"
                    name="password"
                    :error="errors.password"
                    required
                >
                    <PasswordInput
                        required
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        placeholder="New password"
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    label="Confirm password"
                    name="password_confirmation"
                    :error="errors.password_confirmation"
                    required
                >
                    <PasswordInput
                        required
                        id="password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        class="w-full"
                    />
                </UFormField>
                <UButton
                    block
                    variant="subtle"
                    type="submit"
                    :loading="processing"
                    data-test="update-password-button"
                    >Save password
                </UButton>
            </Form>
        </UPageCard>
    </SettingsLayout>
</template>
