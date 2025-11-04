<script setup lang="ts">
import { useAuthPage } from '@/composables/useAuthPage';
import { edit } from '@/routes/password';
import { destroy } from '@/routes/profile';
import { teams } from '@/routes/user';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    userOwnsTeam: boolean;
}>();

const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const page = useAuthPage();
const deleteTeam = (close: () => void) => {
    form.submit(destroy(), {
        preserveScroll: true,
        onSuccess: () => {
            close();
            form.reset();
        },
        onError: () => {
            passwordInput?.value?.focus();
        },
    });
};
</script>

<template>
    <UPageCard
        title="Delete account"
        variant="soft"
        class="bg-linear-to-tl from-error/10 via-error/5 to-error/3"
        icon="i-lucide-trash"
        :ui="{
            leadingIcon: 'text-error',
        }"
    >
        <template
            #description
            v-if="page.props.user.hasPassword && !userOwnsTeam"
            >Delete your account and all of its resources.</template
        >
        <template #description v-else-if="!page.props.user.hasPassword"
            >To delete your account, you must
            <ULink class="underline" :to="edit().url">define a password</ULink>
        </template>
        <template #description v-else-if="userOwnsTeam"
            >To delete your account, you must
            <ULink class="underline" :to="teams().url"
                >delete all the teams you own</ULink
            >
        </template>
        <UModal
            title="Are you sure you want to delete your account?"
            description="Once your account is deleted, all of its resources and data will
                also be permanently deleted. Please enter your password to
                confirm you would like to permanently delete your account."
        >
            <UButton
                color="error"
                variant="subtle"
                :disabled="!page.props.user.hasPassword || userOwnsTeam"
                block
                data-test="delete-user-button"
                >Delete account</UButton
            >

            <template #body="{ close }">
                <form @submit.prevent="deleteTeam(close)" class="space-y-4">
                    <UFormField
                        name="password"
                        :error="form.errors.password"
                        class="grid gap-2"
                        label="Password"
                        required
                    >
                        <UInput
                            required
                            id="password"
                            type="password"
                            name="password"
                            ref="passwordInput"
                            v-model="form.password"
                            placeholder="Password"
                            class="w-full"
                        />
                    </UFormField>

                    <UButton
                        type="submit"
                        color="error"
                        block
                        variant="subtle"
                        :loading="form.processing"
                        data-test="confirm-delete-user-button"
                    >
                        Delete account
                    </UButton>
                    <UButton
                        color="neutral"
                        block
                        variant="subtle"
                        @click="
                            close();
                            form.reset();
                        "
                    >
                        Cancel
                    </UButton>
                </form>
            </template>
        </UModal>
    </UPageCard>
</template>
