<script setup lang="ts">
import CardInfo from '@/components/common/CardInfo.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { redirect } from '@/routes/provider';
import { Form } from '@inertiajs/vue3';
import IBiGithub from '~icons/bi/github';
import IBiGoogle from '~icons/bi/google';
useHead({
    title: 'Log in',
});
</script>

<template>
    <AuthBase title="Login" description="Access your account.">
        <Form
            :action="store()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="space-y-4"
        >
            <UFormField
                label="Email address"
                name="email"
                :error="errors.email"
            >
                <UInput
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="w-full"
                />
            </UFormField>

            <UFormField
                name="password"
                :error="errors.password"
                label="Password"
            >
                <template #hint>
                    <ULink
                        :to="request().url"
                        class="text-sm"
                        :tabindex="5"
                        inactive-class="text-primary font-medium"
                    >
                        Forgot password?
                    </ULink>
                </template>
                <UInput
                    id="password"
                    type="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Password"
                    class="w-full"
                />
            </UFormField>

            <UCheckbox
                id="remember"
                name="remember"
                :tabindex="3"
                label="Remember me"
            />

            <UButton
                block
                variant="subtle"
                type="submit"
                class="mt-4"
                :tabindex="4"
                :loading="processing"
                data-test="login-button"
            >
                Log in
            </UButton>

            <CardInfo>
                Don't have an account?
                <ULink :to="register().url" :tabindex="5" active>Sign up</ULink>
            </CardInfo>
        </Form>
        <USeparator label="or continue with" />
        <div class="space-y-2">
            <UButton
                as="a"
                :icon="IBiGoogle"
                variant="subtle"
                block
                label="Google"
                :href="redirect({ provider: 'google' }).url"
                external
                target="_self"
            ></UButton>
            <UButton
                as="a"
                :icon="IBiGithub"
                variant="subtle"
                block
                label="GitHub"
                :href="redirect({ provider: 'github' }).url"
                external
                target="_self"
            ></UButton>
        </div>
    </AuthBase>
</template>
