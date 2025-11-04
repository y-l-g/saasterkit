<script setup lang="ts">
import { destroy } from '@/routes/teams/members';
import { useForm } from '@inertiajs/vue3';

interface Props {
    team: App.Data.Teams.TeamData;
    member: App.Data.Teams.TeamMemberData;
}

const props = defineProps<Props>();

const removeTeamMemberForm = useForm({});

const removeTeamMember = (close: () => void) => {
    removeTeamMemberForm.submit(
        destroy({
            team: props.team.id,
            user: props.member,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                close();
            },
        },
    );
};
</script>

<template>
    <UModal
        title="Remove Team Member"
        description="Are you sure you would like to remove this person from the team?"
    >
        <UButton
            color="error"
            variant="subtle"
            icon="i-lucide-log-out"
            class="size-8"
        ></UButton>
        <template #body="{ close }">
            <div class="mt-6 flex justify-end gap-3">
                <UButton variant="subtle" color="neutral" @click="close"
                    >Cancel</UButton
                >
                <UButton
                    variant="subtle"
                    color="error"
                    @click="removeTeamMember(close)"
                    :loading="removeTeamMemberForm.processing"
                    >Remove</UButton
                >
            </div>
        </template>
    </UModal>
</template>
