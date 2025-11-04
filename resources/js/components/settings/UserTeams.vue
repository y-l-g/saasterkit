<script setup lang="ts">
import { show } from '@/routes/teams/settings';
import { router } from '@inertiajs/vue3';

defineProps<{
    teams: App.Data.Teams.UserTeamIndexData[];
}>();
</script>

<template>
    <UPageCard title="Your teams" variant="soft">
        <template #description v-if="!teams.length">
            You don't belong to any team
        </template>

        <UPageList divide v-if="teams.length">
            <UPageCard
                v-for="team in teams"
                :key="team.id"
                :variant="team.isCurrentTeam ? 'soft' : 'ghost'"
                class="cursor-pointer"
                @click="router.get(show(team).url)"
                highlight-color="neutral"
                :ui="{ body: 'w-full' }"
            >
                <template #body>
                    <div class="flex items-center justify-between">
                        <UUser
                            :name="team.name"
                            :avatar="{ alt: team.name }"
                            size="xl"
                        />
                        <template v-if="team.isOwner">
                            <div class="flex items-center justify-center gap-2">
                                <UIcon
                                    name="i-lucide-star"
                                    class="text-yellow-500"
                                />
                                <div class="text-xs text-toned">Owner</div>
                            </div>
                        </template>
                    </div>
                </template>
            </UPageCard>
        </UPageList>
    </UPageCard>
</template>
