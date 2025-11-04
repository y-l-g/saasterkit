<script setup lang="ts">
import { useTeamPermissions } from '@/composables/useTeamPermissions';
import { send } from '@/routes/teams/ownership/invitations';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    team: App.Data.Teams.TeamData;
}>();

const { hasTeamPermission } = useTeamPermissions();

const form = useForm({
    email: '',
});

const transferTeamOwnership = () => {
    form.submit(send({ team: props.team.id }), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <UPageCard
        v-if="hasTeamPermission('team.owner.transfer')"
        title="Transfer Team Ownership"
        description="Transfer the ownership of this team to another member. They will receive an email to confirm the transfer. Once confirmed, you will become an admin of the team."
        variant="soft"
        class="bg-linear-to-tl from-error/10 via-error/5 to-error/3"
    >
        <form @submit.prevent="transferTeamOwnership" class="space-y-4">
            <UFormField :error="form.errors.email">
                <UInput
                    v-model="form.email"
                    type="email"
                    required
                    placeholder="Mail of a team member"
                    class="w-full"
                />
            </UFormField>
            <div class="flex justify-end">
                <UButton
                    :loading="form.processing"
                    variant="subtle"
                    color="error"
                    type="submit"
                    block
                >
                    Send Transfer Invitation
                </UButton>
            </div>
        </form>
    </UPageCard>
</template>
