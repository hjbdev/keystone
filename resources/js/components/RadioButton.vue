<script setup>
defineProps({
    modelValue: String,
    disabled: Boolean,
    value: String,
    name: String,
});

const emit = defineEmits(['update:modelValue']);

function onChange(event) {
    emit('update:modelValue', event.target.value)
}
</script>

<template>
    <label
        class="relative rounded-lg border-2 dark:border-white/20 px-3 py-1 dark:has-[:checked]:border-white border-black/20 has-[:checked]:border-black has-[:disabled]:opacity-40"
    >
        <input
            type="radio"
            :name="name"
            :value="value"
            class="invisible absolute inset-0"
            :disabled="disabled"
            :checked="modelValue === value"
            @change="onChange"
        />
        <slot />
    </label>
</template>
