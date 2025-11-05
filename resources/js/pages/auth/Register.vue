<script setup lang="ts">
import CardInfo from '@/components/common/CardInfo.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { redirect } from '@/routes/provider';
import { store } from '@/routes/register';
import { Form } from '@inertiajs/vue3';
import IBiGithub from '~icons/bi/github';
import IBiGoogle from '~icons/bi/google';
useHead({
    title: 'Register',
});
</script>

<template>
    <AuthBase title="Sign in" description="Create your account">
        <Form
            :action="store()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="space-y-4"
        >
            <UFormField label="Name" name="name" :error="errors.name" required>
                <UInput
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Full name"
                    class="w-full"
                />
            </UFormField>

            <UFormField
                label="Email address"
                name="email"
                :error="errors.email"
                required
            >
                <UInput
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                    class="w-full"
                />
            </UFormField>

            <UFormField
                label="Password"
                name="password"
                :error="errors.password"
                required
            >
                <UInput
                    id="password"
                    type="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    class="w-full"
                />
            </UFormField>

            <UFormField
                label="Confirm password"
                name="password_confirmation"
                :error="errors.password_confirmation"
                required
            >
                <UInput
                    id="password_confirmation"
                    type="password"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    class="w-full"
                />
            </UFormField>

            <UButton
                variant="subtle"
                block
                type="submit"
                class="mt-2"
                :tabindex="5"
                :loading="processing"
                data-test="register-user-button"
            >
                Create account
            </UButton>
            <CardInfo>
                Already have an account?
                <ULink :to="login().url" :tabindex="6" active>Log in</ULink>
            </CardInfo>
        </Form>
        <USeparator label="or continue with" />
        <div class="space-y-2">
            <UButton
                :icon="IBiGoogle"
                variant="subtle"
                block
                label="Google"
                :href="redirect({ provider: 'google' }).url"
                external
                target="_self"
            ></UButton>
            <UButton
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
