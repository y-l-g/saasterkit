<script setup lang="ts">
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { confirm } from '@/routes/two-factor';
import { Form } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { computed, nextTick, ref, useTemplateRef, watch } from 'vue';

interface Props {
    requiresConfirmation: boolean;
    twoFactorEnabled: boolean;
}

const props = defineProps<Props>();
const open = defineModel<boolean>('open');

const { copy, copied } = useClipboard();
const { qrCodeSvg, manualSetupKey, clearSetupData, fetchSetupData, errors } =
    useTwoFactorAuth();
const showVerificationStep = ref(false);
const code = ref<number[]>([]);
const codeValue = computed<string>(() => code.value.join(''));

const pinInputContainerRef = useTemplateRef('pinInputContainerRef');

const modalConfig = computed<{
    title: string;
    description: string;
    buttonText: string;
}>(() => {
    if (props.twoFactorEnabled) {
        return {
            title: 'Two-Factor Authentication Enabled',
            description:
                'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
            buttonText: 'Close',
        };
    }

    if (showVerificationStep.value) {
        return {
            title: 'Verify Authentication Code',
            description: 'Enter the 6-digit code from your authenticator app',
            buttonText: 'Continue',
        };
    }

    return {
        title: 'Enable Two-Factor Authentication',
        description:
            'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app',
        buttonText: 'Continue',
    };
});

const handleModalNextStep = () => {
    if (props.requiresConfirmation) {
        showVerificationStep.value = true;

        nextTick(() => {
            pinInputContainerRef.value?.querySelector('input')?.focus();
        });

        return;
    }

    clearSetupData();
    open.value = false;
};

const resetModalState = () => {
    if (props.twoFactorEnabled) {
        clearSetupData();
    }

    showVerificationStep.value = false;
    code.value = [];
};

watch(
    () => open.value,
    async (open) => {
        if (!open) {
            resetModalState();
            return;
        }

        if (!qrCodeSvg.value) {
            await fetchSetupData();
        }
    },
);
</script>

<template>
    <UModal v-model:open="open">
        <template #content>
            <UPageCard
                :title="modalConfig.title"
                :description="modalConfig.description"
                icon="i-lucide-scan-line"
            >
                <template v-if="!showVerificationStep">
                    <UAlert
                        v-if="errors?.length"
                        color="error"
                        variant="subtle"
                        icon="i-lucide-alert-triangle"
                        :title="errors[0]"
                    />

                    <template v-else>
                        <UIcon
                            v-if="!qrCodeSvg"
                            name="i-lucide-loader2"
                            class="size-6 animate-spin"
                        />
                        <div v-else class="flex justify-center">
                            <div class="bg-white p-12">
                                <div v-html="qrCodeSvg" />
                            </div>
                        </div>
                        <UButton
                            variant="subtle"
                            block
                            @click="handleModalNextStep"
                        >
                            {{ modalConfig.buttonText }}
                        </UButton>

                        <USeparator label="or, enter the code manually" />
                        <UIcon
                            v-if="!manualSetupKey"
                            name="ILucideLoader2"
                            class="size-4 animate-spin"
                        />
                        <UFieldGroup v-else>
                            <UInput
                                :model-value="manualSetupKey"
                                readonly
                                class="flex-1"
                                :ui="{
                                    base: 'h-full w-full bg-background p-3',
                                }"
                            />
                            <UButton
                                @click="copy(manualSetupKey || '')"
                                color="neutral"
                                variant="ghost"
                                :icon="
                                    copied ? 'i-lucide-check' : 'i-lucide-copy'
                                "
                            />
                        </UFieldGroup>
                    </template>
                </template>
                <Form
                    v-else
                    v-bind="confirm.form()"
                    reset-on-error
                    @finish="code = []"
                    @success="open = false"
                    v-slot="{ errors, processing }"
                >
                    <input type="hidden" name="code" :value="codeValue" />
                    <div
                        ref="pinInputContainerRef"
                        class="relative w-full space-y-4"
                    >
                        <UFormField
                            name="code"
                            :error="errors?.confirmTwoFactorAuthentication.code"
                            class="flex justify-center"
                            required
                        >
                            <UPinInput
                                id="otp"
                                placeholder="○"
                                v-model="code"
                                type="number"
                                otp
                                :length="6"
                                :disabled="processing"
                                autofocus
                            />
                        </UFormField>

                        <div class="flex w-full items-center space-x-5">
                            <UButton
                                type="button"
                                variant="subtle"
                                color="neutral"
                                class="w-auto flex-1"
                                @click="showVerificationStep = false"
                                :disabled="processing"
                            >
                                Back
                            </UButton>
                            <UButton
                                type="submit"
                                variant="subtle"
                                class="w-auto flex-1"
                                :loading="processing"
                                :disabled="codeValue.length < 6"
                            >
                                Confirm
                            </UButton>
                        </div>
                    </div>
                </Form>
            </UPageCard>
        </template>
    </UModal>
</template>
