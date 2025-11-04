<script setup lang="ts">
import { store } from '@/routes/teams/members';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    team: App.Data.Teams.TeamData;
    availableRoles: App.Data.Teams.TeamRoleData[];
}

const props = defineProps<Props>();

const inviteTeamMemberForm = useForm({
    email: '',
    role: undefined as App.Enums.Teams.RoleEnum | undefined,
});

const inviteTeamMember = () => {
    inviteTeamMemberForm.submit(store(props.team.id), {
        preserveScroll: true,
        onSuccess: () => inviteTeamMemberForm.reset(),
    });
};

const selectedRoleDescription = computed(() => {
    if (!inviteTeamMemberForm.role) {
        return '';
    }
    const selectedRole = props.availableRoles.find(
        (role) => String(role.key) === inviteTeamMemberForm.role,
    );
    return selectedRole ? selectedRole.description : '';
});
</script>

<template>
    <UPageCard
        title="Invite Team Member"
        description="Invite a new team member to your team, allowing them to collaborate with you."
        variant="soft"
    >
        <form @submit.prevent="inviteTeamMember" class="space-y-4">
            <UFormField
                label="Email"
                name="email"
                :error="inviteTeamMemberForm.errors.email"
                required
            >
                <UInput
                    required
                    id="email"
                    type="email"
                    name="email"
                    class="w-full"
                    v-model="inviteTeamMemberForm.email"
                />
            </UFormField>

            <UFormField
                v-if="availableRoles.length > 0"
                label="Role"
                name="role"
                :error="inviteTeamMemberForm.errors.role"
                required
                :help="selectedRoleDescription"
            >
                <USelect
                    v-model="inviteTeamMemberForm.role"
                    labelKey="name"
                    valueKey="key"
                    :items="availableRoles"
                    class="w-full"
                    placeholder="Select a role"
                >
                </USelect>
            </UFormField>
            <UButton
                type="submit"
                variant="subtle"
                :loading="inviteTeamMemberForm.processing"
                label="Send Invitation"
                block
            />
        </form>
    </UPageCard>
</template>
