<script setup lang="ts">
import { useAuthPage } from '@/composables/useAuthPage';
import { destroy } from '@/routes/teams/members';
import { useForm } from '@inertiajs/vue3';

interface Props {
    team: App.Data.Teams.TeamData;
}

const props = defineProps<Props>();
const page = useAuthPage();

const leaveTeamForm = useForm({
    member: '',
});

const leaveTeam = (close: () => void) => {
    leaveTeamForm.submit(
        destroy({ team: props.team.id, user: page.props.user }),
        {
            onSuccess: () => {
                close();
            },
        },
    );
};
</script>

<template>
    <UModal
        title="Leave Team"
        description="Are you sure you would like to leave this team?"
    >
        <div class="flex flex-col">
            <UButton
                color="error"
                variant="subtle"
                block
                icon="i-lucide-log-out"
                :disabled="team.userId === page.props.user.id"
            ></UButton>
        </div>
        <template #body="{ close }">
            <UFormField :error="leaveTeamForm.errors.member"></UFormField>
            <div class="mt-6 flex justify-end gap-3">
                <UButton
                    variant="subtle"
                    color="neutral"
                    @click="
                        close();
                        leaveTeamForm.reset();
                        leaveTeamForm.clearErrors();
                    "
                    >Cancel</UButton
                >
                <UButton
                    variant="subtle"
                    color="error"
                    @click="leaveTeam(close)"
                    :loading="leaveTeamForm.processing"
                    :disabled="team.userId === page.props.user.id"
                    >Leave</UButton
                >
            </div>
        </template>
    </UModal>
</template>
