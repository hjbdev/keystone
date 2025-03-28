<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    servers: {
        type: [Array, null],
        required: true,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            {{ servers.data }}

            <div>@todo pagination</div>
        </div>
    </AppLayout>
</template>
