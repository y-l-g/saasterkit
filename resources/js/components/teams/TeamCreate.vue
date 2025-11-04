<script setup lang="ts">
import { store } from '@/routes/teams';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
});

const createTeam = () => {
    form.submit(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <UPageCard title="Create Team" variant="soft" icon="i-lucide-users">
        <form @submit.prevent="createTeam" class="space-y-4">
            <UFormField
                label="Team Name"
                name="name"
                :error="form.errors.name"
                required
            >
                <UInput
                    id="name"
                    name="name"
                    required
                    v-model="form.name"
                    autofocus
                    class="w-full"
                    placeholder="Team Name"
                />
            </UFormField>

            <UButton
                type="submit"
                variant="subtle"
                :loading="form.processing"
                block
            >
                Create
            </UButton>
        </form>
    </UPageCard>
</template>
