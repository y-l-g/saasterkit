<script setup lang="ts">
import { accept } from '@/routes/teams/invitations';
import { useForm } from '@inertiajs/vue3';
import type { CheckboxGroupItem } from '@nuxt/ui';
import { ref } from 'vue';

const props = defineProps<{
    invitations: App.Data.Teams.TeamInvitationData[];
}>();

const items = ref<CheckboxGroupItem[]>(
    props.invitations.map((invitation) => ({
        label: invitation.team?.name,
        description: invitation.team?.owner?.email,
        id: invitation.id,
    })),
);
const form = useForm({
    invitations: [],
});
const acceptTeamInvitations = () => {
    form.submit(accept(), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <UPageCard title="Team Invitations" variant="soft">
        <template #description v-if="invitations.length > 0"
            >You have been invited
        </template>
        <template #description v-else
            >You have no pending team invitations.</template
        >
        <div class="space-y-4" v-if="invitations.length > 0">
            <UFormField :error="form.errors.invitations">
                <UCheckboxGroup
                    variant="card"
                    v-model="form.invitations"
                    value-key="id"
                    :items="items"
                />
            </UFormField>
            <UButton
                variant="subtle"
                @click="acceptTeamInvitations()"
                :loading="form.processing"
                >Accept Team Invitations</UButton
            >
        </div>
    </UPageCard>
</template>
