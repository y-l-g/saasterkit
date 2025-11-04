<script setup lang="ts">
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { regenerateRecoveryCodes } from '@/routes/two-factor';
import { Form } from '@inertiajs/vue3';
import { nextTick, onMounted, ref, useTemplateRef } from 'vue';

const { recoveryCodesList, fetchRecoveryCodes, errors } = useTwoFactorAuth();
const isRecoveryCodesVisible = ref<boolean>(false);
const recoveryCodeSectionRef = useTemplateRef('recoveryCodeSectionRef');

const toggleRecoveryCodesVisibility = async () => {
    if (!isRecoveryCodesVisible.value && !recoveryCodesList.value.length) {
        await fetchRecoveryCodes();
    }

    isRecoveryCodesVisible.value = !isRecoveryCodesVisible.value;

    if (isRecoveryCodesVisible.value) {
        await nextTick();
        recoveryCodeSectionRef.value?.scrollIntoView({ behavior: 'smooth' });
    }
};

onMounted(async () => {
    if (!recoveryCodesList.value.length) {
        await fetchRecoveryCodes();
    }
});
</script>

<template>
    <UPageCard
        title="2FA Recovery Codes"
        description="Recovery codes let you regain access if you lose your 2FA
                device. Store them in a secure password manager."
        variant="soft"
        icon="i-lucide-lock-keyhole"
    >
        <div class="flex flex-col gap-3">
            <UButton
                @click="toggleRecoveryCodesVisibility"
                class="w-fit"
                variant="subtle"
                :icon="
                    isRecoveryCodesVisible ? 'i-lucide-eye-off' : 'i-lucide-eye'
                "
            >
                {{ isRecoveryCodesVisible ? 'Hide' : 'View' }} Recovery Codes
            </UButton>

            <Form
                v-if="isRecoveryCodesVisible"
                v-bind="regenerateRecoveryCodes.form()"
                method="post"
                :options="{ preserveScroll: true }"
                @success="fetchRecoveryCodes"
                #default="{ processing }"
            >
                <UButton
                    variant="subtle"
                    color="secondary"
                    type="submit"
                    :loading="processing"
                    icon="i-lucide-refresh-cw"
                >
                    Regenerate Codes
                </UButton>
            </Form>
        </div>
        <div v-if="isRecoveryCodesVisible">
            <UAlert
                v-if="errors?.length"
                class="mt-4"
                color="error"
                variant="soft"
                icon="i-lucide-alert-triangle"
                title="Error"
                :description="errors[0]"
            />

            <div v-else ref="recoveryCodeSectionRef">
                <div v-if="!recoveryCodesList.length" class="space-y-2">
                    <USkeleton v-for="n in 8" :key="n" class="h-4 w-full" />
                </div>
                <div
                    v-else
                    v-for="(code, index) in recoveryCodesList"
                    :key="index"
                    class="font-mono"
                >
                    {{ code }}
                </div>
                <UBadge variant="soft" color="secondary" size="lg" class="mt-3">
                    <span>
                        Each recovery code can be used once to access your
                        account and will be removed after use. If you need more,
                        click
                        <span class="font-bold">Regenerate Codes</span> above.
                    </span>
                </UBadge>
            </div>
        </div>
    </UPageCard>
</template>
