<script setup lang="ts">
import DeleteUser from '@/components/settings/ProfileDelete.vue';
import ProfileInfo from '@/components/settings/ProfileInfo.vue';
import ProfileSocialAccounts from '@/components/settings/ProfileSocialAccounts.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { settings } from '@/routes/user';

defineProps<{
    availableProviders: App.Enums.Auth.SocialiteProviderEnum[];
    linkedProviders: App.Enums.Auth.SocialiteProviderEnum[];
    userOwnsTeam: boolean;
}>();

const breadcrumbs = [
    { label: 'Settings', to: settings().url },
    { label: 'Profile' },
];
useHead({
    title: 'Profile settings',
});
</script>

<template>
    <SettingsLayout :breadcrumbs="breadcrumbs">
        <ProfileInfo />
        <ProfileSocialAccounts :availableProviders :linkedProviders />
        <DeleteUser :userOwnsTeam />
    </SettingsLayout>
</template>
