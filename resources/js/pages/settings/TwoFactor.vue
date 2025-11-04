<script setup lang="ts">
import TwoFactorRecoveryCodes from '@/components/settings/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/settings/TwoFactorSetupModal.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { disable, enable } from '@/routes/two-factor';
import { settings } from '@/routes/user';
import { Form } from '@inertiajs/vue3';
import { onUnmounted, ref } from 'vue';

interface Props {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => {
    clearTwoFactorAuthData();
});

const breadcrumbs = [
    { label: 'Settings', to: settings().url },
    { label: 'Two-Factor Auth' },
];
useHead({
    title: 'Two-Factor Authentication',
});
</script>

<template>
    <SettingsLayout :breadcrumbs="breadcrumbs">
        <UPageCard
            title="Two-Factor Authentication"
            description="Manage your two-factor authentication settings. When you enable two-factor authentication, you will be
                        prompted for a secure pin during login. This pin can be
                        retrieved from a TOTP-supported application on your
                        phone."
            variant="soft"
            icon="i-lucide-shield-check"
        >
            <div
                v-if="!twoFactorEnabled"
                class="flex flex-col items-start justify-start space-y-4"
            >
                <UBadge color="error" variant="soft">Disabled</UBadge>
                <UButton
                    variant="subtle"
                    v-if="hasSetupData"
                    @click="showSetupModal = true"
                    icon="i-lucide-shield-check"
                >
                    Continue Setup
                </UButton>
                <Form
                    v-else
                    v-bind="enable.form()"
                    @success="showSetupModal = true"
                    #default="{ processing }"
                >
                    <UButton
                        type="submit"
                        variant="subtle"
                        :loading="processing"
                        icon="i-lucide-shield-check"
                        >Enable 2FA
                    </UButton>
                </Form>
            </div>

            <div
                v-else
                class="flex flex-col items-start justify-start space-y-4"
            >
                <UBadge variant="soft">Enabled</UBadge>

                <TwoFactorRecoveryCodes />

                <Form v-bind="disable.form()" #default="{ processing }">
                    <UButton
                        color="error"
                        variant="subtle"
                        type="submit"
                        :loading="processing"
                        icon="i-lucide-shield-ban"
                    >
                        Disable 2FA
                    </UButton>
                </Form>
            </div>
            <TwoFactorSetupModal
                v-model:open="showSetupModal"
                :requiresConfirmation="requiresConfirmation"
                :twoFactorEnabled="twoFactorEnabled"
            />
        </UPageCard>
    </SettingsLayout>
</template>
