<script setup lang="ts">
import CardInfo from '@/components/common/CardInfo.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/two-factor/login';
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface AuthConfigContent {
    title: string;
    description: string;
    toggleText: string;
}

const authConfigContent = computed<AuthConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Recovery Code',
            description:
                'Please confirm access to your account by entering one of your emergency recovery codes.',
            toggleText: 'login using an authentication code',
        };
    }

    return {
        title: 'Authentication Code',
        description:
            'Enter the authentication code provided by your authenticator application.',
        toggleText: 'login using a recovery code',
    };
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = [];
};

const code = ref<number[]>([]);
const codeValue = computed<string>(() => code.value.join(''));
</script>

<template>
    <AuthLayout
        :title="authConfigContent.title"
        :description="authConfigContent.description"
    >
        <Head title="Two-Factor Authentication" />
        <template v-if="!showRecoveryInput">
            <Form
                v-bind="store.form()"
                class="space-y-4"
                reset-on-error
                @error="code = []"
                #default="{ errors, processing, clearErrors }"
            >
                <input type="hidden" name="code" :value="codeValue" />
                <UFormField
                    name="code"
                    :error="errors.code"
                    class="flex justify-center"
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
                <UButton
                    variant="subtle"
                    block
                    type="submit"
                    :loading="processing"
                    >Continue</UButton
                >
                <CardInfo>
                    or you can
                    <ULink
                        active
                        @click="() => toggleRecoveryMode(clearErrors)"
                    >
                        {{ authConfigContent.toggleText }}
                    </ULink>
                </CardInfo>
            </Form>
        </template>

        <template v-else>
            <Form
                v-bind="store.form()"
                class="space-y-4"
                reset-on-error
                #default="{ errors, processing, clearErrors }"
            >
                <UFormField name="recovery_code" :error="errors.recovery_code">
                    <UInput
                        name="recovery_code"
                        type="text"
                        placeholder="Enter recovery code"
                        :autofocus="showRecoveryInput"
                        required
                        class="w-full"
                    />
                </UFormField>
                <UButton
                    variant="subtle"
                    block
                    type="submit"
                    :loading="processing"
                    >Continue</UButton
                >
                <CardInfo>
                    or you can
                    <ULink
                        active
                        @click="() => toggleRecoveryMode(clearErrors)"
                    >
                        {{ authConfigContent.toggleText }}
                    </ULink>
                </CardInfo>
            </Form>
        </template>
    </AuthLayout>
</template>
