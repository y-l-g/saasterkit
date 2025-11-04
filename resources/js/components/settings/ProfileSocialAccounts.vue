<script setup lang="ts">
import { link, unlink } from '@/routes/provider';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    availableProviders: App.Enums.Auth.SocialiteProviderEnum[];
    linkedProviders: App.Enums.Auth.SocialiteProviderEnum[];
}>();

const isLinked = (provider: App.Enums.Auth.SocialiteProviderEnum) => {
    return props.linkedProviders.includes(provider);
};

const unlinkAccount = (provider: App.Enums.Auth.SocialiteProviderEnum) => {
    router.delete(unlink({ provider }).url, {
        preserveScroll: true,
    });
};

const providerIcons = {
    google: 'i-bi-google',
    github: 'i-bi-github',
};
</script>

<template>
    <UPageCard
        title="Social accounts"
        description="Connect your social accounts to enable one-click login."
        icon="i-lucide-share2"
        variant="soft"
    >
        <div class="space-y-4">
            <div
                v-for="provider in availableProviders"
                :key="provider"
                class="flex items-center justify-between"
            >
                <div class="flex items-center gap-3">
                    <UIcon :name="providerIcons[provider]" class="h-5 w-5" />
                    <span class="capitalize">{{ provider }}</span>
                </div>

                <UButton
                    v-if="isLinked(provider)"
                    variant="subtle"
                    color="error"
                    @click="unlinkAccount(provider)"
                >
                    Disconnect
                </UButton>
                <UButton
                    v-else
                    as="a"
                    :href="link({ provider }).url"
                    variant="subtle"
                    external
                    target="_self"
                >
                    Connect
                </UButton>
            </div>
        </div>
    </UPageCard>
</template>
