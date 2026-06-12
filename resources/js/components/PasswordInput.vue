<script setup lang="ts">
import { computed, ref, useAttrs, useTemplateRef } from 'vue';
import type { HTMLAttributes } from 'vue';

defineOptions({ inheritAttrs: false });

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();

const showPassword = ref(false);
const attrs = useAttrs();
const inputRef = useTemplateRef<{ $el?: HTMLElement }>('inputRef');
const controlledInputId = computed(() =>
    typeof attrs.id === 'string' ? attrs.id : undefined,
);

function inputElement(): HTMLInputElement | null {
    const element = inputRef.value?.$el;

    return element instanceof HTMLElement
        ? element.querySelector<HTMLInputElement>('input')
        : null;
}

defineExpose({
    $el: inputRef,
    focus: () => {
        inputElement()?.focus();
    },
});
</script>

<template>
    <UInput
        ref="inputRef"
        :type="showPassword ? 'text' : 'password'"
        :class="props.class"
        v-bind="$attrs"
    >
        <template #trailing>
            <UButton
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                :aria-controls="controlledInputId"
                :aria-pressed="showPassword"
                :icon="showPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                color="neutral"
                size="xs"
                type="button"
                variant="link"
                @click.prevent.stop="showPassword = !showPassword"
            />
        </template>
    </UInput>
</template>
