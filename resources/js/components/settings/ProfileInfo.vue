<script setup lang="ts">
import { useAuthPage } from '@/composables/useAuthPage';
import { update } from '@/routes/user-profile-information';
import { send } from '@/routes/verification';
import { useForm } from '@inertiajs/vue3';

const page = useAuthPage();

const form = useForm({
    name: page.props.user.name,
    email: page.props.user.email,
});

const submitProfileInformation = () => {
    form.submit(update(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <UPageCard
        title="Profile information"
        description="Update your name and email address."
        variant="soft"
        icon="i-lucide-user"
    >
        <form @submit.prevent="submitProfileInformation" class="space-y-4">
            <UFormField
                label="Name"
                name="name"
                :error="form.errors.name"
                required
            >
                <UInput
                    id="name"
                    name="name"
                    v-model="form.name"
                    required
                    autocomplete="name"
                    placeholder="Full name"
                    class="w-full"
                />
            </UFormField>

            <UFormField
                label="Email address"
                name="email"
                :error="form.errors.email"
                required
            >
                <UInput
                    id="email"
                    type="email"
                    name="email"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="Email address"
                    class="w-full"
                />
            </UFormField>
            <div v-if="!page.props.user?.emailVerifiedAt">
                <div class="text-sm text-toned">
                    Your email address is unverified.
                    <ULink active :to="send().url" method="post">
                        Send verification email.
                    </ULink>
                </div>
            </div>

            <UButton
                block
                type="submit"
                variant="subtle"
                color="primary"
                :loading="form.processing"
                data-test="update-profile-button"
            >
                Save
            </UButton>
        </form>
    </UPageCard>
</template>
