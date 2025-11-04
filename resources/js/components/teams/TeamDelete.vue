<script setup lang="ts">
import { useAuthPage } from '@/composables/useAuthPage';
import { portal } from '@/routes/billing';
import { edit } from '@/routes/password';
import { destroy } from '@/routes/teams';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    team: App.Data.Teams.TeamData;
}

const props = defineProps<Props>();
const passwordInput = ref<HTMLInputElement | null>(null);
const page = useAuthPage();
const confirmingTeamDeletion = ref(false);
const form = useForm({ password: '' });

const deleteTeam = (close: () => void) => {
    form.submit(destroy(props.team), {
        onSuccess: () => {
            close();
            form.reset();
        },
        onError: () => {
            passwordInput?.value?.focus();
        },
    });
};

const isRedirecting = ref(false);

const goToPortal = () => {
    isRedirecting.value = true;
    router.get(
        portal(props.team.id).url,
        {},
        {
            onFinish: () => {
                isRedirecting.value = false;
            },
        },
    );
};
</script>

<template>
    <UPageCard
        title="Delete Team"
        variant="soft"
        class="bg-linear-to-tl from-error/10 via-error/5 to-error/3"
        icon="i-lucide-trash"
        :ui="{
            leadingIcon: 'text-error',
        }"
    >
        <template #description v-if="page.props.user.hasPassword"
            >Once a team is deleted, all of its resources and data will be
            permanently deleted. Before deleting this team, please download any
            data or information regarding this team that you wish to
            retain.</template
        >
        <template #description v-else
            >To delete this team, you must
            <ULink class="underline" :to="edit().url">define a password</ULink>
        </template>
        <UModal
            v-if="!team.subscription?.id || team.subscription?.endsAt"
            v-model="confirmingTeamDeletion"
            title="Delete Team"
            description="Are you sure you want to delete this team? Once a team is
                deleted, all of its resources and data will be permanently
                deleted."
        >
            <UButton
                color="error"
                variant="subtle"
                block
                :disabled="!page.props.user.hasPassword"
                >Delete Team</UButton
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
                        >Delete Team</UButton
                    >
                    <UButton
                        color="neutral"
                        variant="subtle"
                        @click="close"
                        block
                    >
                        Cancel
                    </UButton>
                </form>
            </template>
        </UModal>
        <UModal
            v-else
            v-model="confirmingTeamDeletion"
            title="Delete Team"
            description="You can't delete this team because it has an active subscription. Please cancel your subscription."
        >
            <UButton
                color="error"
                variant="subtle"
                block
                :disabled="!page.props.user.hasPassword"
                >Delete Team</UButton
            >
            <template #body="{ close }">
                <UButton
                    block
                    variant="subtle"
                    @click="goToPortal()"
                    :loading="isRedirecting"
                    >Manage subscription</UButton
                >
                <div class="mt-6 flex justify-end gap-3">
                    <UButton
                        block
                        color="neutral"
                        variant="subtle"
                        @click="close"
                    >
                        Cancel
                    </UButton>
                </div>
            </template>
        </UModal>
    </UPageCard>
</template>
