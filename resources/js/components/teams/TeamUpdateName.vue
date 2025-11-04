<script setup lang="ts">
import { useTeamPermissions } from '@/composables/useTeamPermissions';
import { update } from '@/routes/teams';
import { useForm } from '@inertiajs/vue3';

interface Props {
    team: App.Data.Teams.TeamData;
}

const props = defineProps<Props>();

const { hasTeamPermission } = useTeamPermissions();

const form = useForm({
    name: props.team.name,
});

const updateTeamName = () => {
    form.submit(update(props.team), {
        preserveScroll: true,
    });
};
</script>

<template>
    <UPageCard title="Team Owner" variant="soft">
        <div class="flex items-center space-x-4">
            <UAvatar size="lg" :alt="team.owner?.name" />
            <div>
                <p class="font-medium">{{ team.owner?.name }}</p>
                <p class="text-toned">{{ team.owner?.email }}</p>
            </div>
        </div>
    </UPageCard>
    <UPageCard title="Team name" variant="soft">
        <form @submit.prevent="updateTeamName" class="space-y-4">
            <UFormField name="name" :error="form.errors.name" required>
                <UInput
                    required
                    id="name"
                    name="name"
                    class="w-full"
                    v-model="form.name"
                    :disabled="!hasTeamPermission('team.update')"
                />
            </UFormField>

            <div v-if="hasTeamPermission('team.update')">
                <UButton
                    type="submit"
                    variant="subtle"
                    :loading="form.processing"
                    label="Save"
                    block
                />
            </div>
        </form>
    </UPageCard>
</template>
