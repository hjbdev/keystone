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
        class="relative rounded-lg border-2 border-white/20 px-3 py-1 has-[:checked]:border-white has-[:disabled]:opacity-40"
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
