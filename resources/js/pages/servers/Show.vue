<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const props = defineProps({
    server: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="server.name" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            {{ server }}

            <div v-if="$page.props.flash?.server_credentials" class="p-5">
                <div class="mb-4 text-sm font-medium text-gray-900 dark:text-white">
                    WILL NOT BE SHOWN AGAIN: 
                    {{ $page.props.flash.server_credentials }}
                </div>
            </div>
        </div>
    </AppLayout>
</template>
