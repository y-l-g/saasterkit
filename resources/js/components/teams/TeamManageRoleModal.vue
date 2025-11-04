<script setup lang="ts">
import { useTeamPermissions } from '@/composables/useTeamPermissions';
import { update } from '@/routes/teams/members';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    team: App.Data.Teams.TeamData;
    member: App.Data.Teams.TeamMemberData;
    availableRoles: App.Data.Teams.TeamRoleData[];
}

const props = defineProps<Props>();

const { hasTeamPermission } = useTeamPermissions();

const updateRoleForm = useForm({
    role: undefined as App.Enums.Teams.RoleEnum | undefined,
});

const manageRole = (teamMember: App.Data.Teams.TeamMemberData) => {
    updateRoleForm.role = teamMember?.role ?? undefined;
};

const updateRole = (close: () => void) => {
    updateRoleForm.submit(
        update({
            team: props.team.id,
            user: props.member,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                close();
                updateRoleForm.reset();
            },
        },
    );
};

const selectedRoleDescription = computed(() => {
    if (!updateRoleForm.role) {
        return '';
    }
    const selectedRole = props.availableRoles.find(
        (role) => String(role.key) === updateRoleForm.role,
    );
    return selectedRole ? selectedRole.description : '';
});
</script>

<template>
    <div>
        <UModal
            v-if="
                hasTeamPermission('team.member.update') && availableRoles.length
            "
            title="Manage Role"
        >
            <UButton
                variant="subtle"
                class="flex size-8 items-center justify-center"
                @click="manageRole(member)"
            >
                {{ member.role ? member.role.charAt(0).toUpperCase() : 'X' }}
            </UButton>

            <template #body="{ close }">
                <UFormField
                    :error="updateRoleForm.errors.role"
                    :help="selectedRoleDescription"
                >
                    <USelect
                        v-model="updateRoleForm.role"
                        :items="availableRoles"
                        labelKey="name"
                        valueKey="key"
                        class="w-full"
                        placeholder="Select a role"
                    >
                    </USelect>
                </UFormField>
                <div class="mt-6 flex justify-end gap-3">
                    <UButton variant="subtle" color="neutral" @click="close">
                        Cancel
                    </UButton>
                    <UButton
                        variant="subtle"
                        @click="updateRole(close)"
                        :loading="updateRoleForm.processing"
                    >
                        Save
                    </UButton>
                </div>
            </template>
        </UModal>

        <span v-else-if="availableRoles.length" class="text-sm text-toned">
            {{ member.role ? member.role : '' }}
        </span>
    </div>
</template>
