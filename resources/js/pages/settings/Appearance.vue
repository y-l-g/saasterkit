<script setup lang="ts">
import AppearanceColorPicker from '@/components/settings/AppearanceColorPicker.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { update } from '@/routes/appearance';
import { settings } from '@/routes/user';
import { useForm } from '@inertiajs/vue3';
import { useColorMode } from '@vueuse/core';
const breadcrumbs = [
    { label: 'Settings', to: settings().url },
    { label: 'Appearance' },
];

const appConfig = useAppConfig();
const colors: App.Enums.Settings.ColorEnum[] = [
    'gray',
    'red',
    'orange',
    'amber',
    'yellow',
    'lime',
    'green',
    'emerald',
    'teal',
    'cyan',
    'sky',
    'blue',
    'indigo',
    'violet',
    'purple',
    'fuchsia',
    'pink',
    'rose',
];

const neutrals: App.Enums.Settings.ColorEnum[] = [
    'slate',
    'gray',
    'zinc',
    'old-neutral',
    'stone',
];

const colorMode = useColorMode().store;

const items = [
    { value: 'light', label: 'Light', icon: 'i-lucide-sun' },
    { value: 'dark', label: 'Dark', icon: 'i-lucide-moon' },
    { value: 'auto', label: 'System', icon: 'i-lucide-monitor' },
];

const toast = useToast();

const form = useForm({
    primary_color: appConfig.ui.colors.primary,
    secondary_color: appConfig.ui.colors.secondary,
    neutral_color: appConfig.ui.colors.neutral,
});

const submit = () => {
    form.primary_color = appConfig.ui.colors.primary;
    form.secondary_color = appConfig.ui.colors.secondary;
    form.neutral_color = appConfig.ui.colors.neutral;
    form.submit(update(), {
        onError: () => {
            toast.add({
                title: 'An error occurred',
                description: 'Your appearance settings could not be saved.',
                color: 'error',
            });
        },
    });
};
</script>

<template>
    <SettingsLayout :breadcrumbs="breadcrumbs">
        <UPageCard
            title="Appearance"
            description="Update your account's appearance mode"
            variant="soft"
            icon="i-lucide-palette"
            :ui="{
                container: 'gap-y-6',
            }"
        >
            <UTabs
                class="w-fit"
                variant="link"
                v-model="colorMode"
                :items="items"
                :content="false"
            />
            <AppearanceColorPicker
                :options="colors"
                v-model="appConfig.ui.colors.primary"
                type="primary"
                title="Primary color"
                description="Main CTAs, active navigation, brand elements, important links"
            />
            <AppearanceColorPicker
                :options="colors"
                v-model="appConfig.ui.colors.secondary"
                type="secondary"
                title="Secondary color"
                description="Secondary buttons, alternative actions, complementary UI elements"
            />
            <AppearanceColorPicker
                :options="neutrals"
                v-model="appConfig.ui.colors.neutral"
                type="neutral"
                title="Neutral color"
                description="Text, borders, backgrounds, disabled states"
            />
            <UButton
                @click="submit"
                block
                type="submit"
                variant="subtle"
                :loading="form.processing"
                >Save changes</UButton
            >
        </UPageCard>
    </SettingsLayout>
</template>
