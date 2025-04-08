<script setup lang="ts">
import { WavesIcon } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <WavesIcon v-bind="$attrs" :class="className" />
</template>
