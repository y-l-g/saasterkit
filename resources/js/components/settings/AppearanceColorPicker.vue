<script setup lang="ts">
defineProps<{
    options: App.Enums.Settings.ColorEnum[];
    modelValue: string;
    type: 'primary' | 'neutral' | 'secondary';
    title: string;
    description?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

function selectOption(option: string) {
    emit('update:modelValue', option);
}
</script>

<template>
    <UPageCard :title="title" :description="description" variant="naked">
        <div class="flex flex-wrap gap-2">
            <button
                v-for="option in options"
                :key="option"
                :style="{ '--chip': `var(--color-${option}-500)` }"
                :class="[
                    'size-5 rounded-full bg-[var(--chip)]',
                    {
                        'ring-2 ring-[var(--chip)]': modelValue === option,
                        'hover:scale-110': modelValue !== option,
                    },
                ]"
                :aria-label="`Select ${option} ${type} color`"
                @click="selectOption(option)"
            />
        </div>
    </UPageCard>
</template>
