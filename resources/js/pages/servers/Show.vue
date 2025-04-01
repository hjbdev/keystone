<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useCycleList, useInterval } from '@vueuse/core';
import { DatabaseIcon, Layers2Icon, LoaderCircleIcon } from 'lucide-vue-next';
import { watch } from 'vue';

const props = defineProps({
    server: {
        type: Object,
        required: true,
    },
});

const { state: provisionMessage, next } = useCycleList([
    'Provisioning your server...',
    'Updating dependencies...',
    'Tightening security...',
    'Installing packages...',
    'Configuring ssh...',
    'Installing docker...',
]);
const { counter, reset, pause, resume } = useInterval(5000, { controls: true });

watch(counter, () => {
    next();
});
</script>

<template>
    <Head :title="server.name" />

    <AppLayout
        :breadcrumbs="[
            {
                title: 'Servers',
                href: route('servers.index', {
                    organisation: $page.props.organisation.id,
                }),
            },
            {
                title: server.name,
                href: route('servers.show', {
                    organisation: $page.props.organisation.id,
                    server: server.id,
                }),
            },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <h2 class="text-3xl font-bold tracking-tight">{{ server.name }}</h2>
                <div>
                    <Badge :variant="server.status === 'active' ? 'success' : 'secondary'">{{ server.status }}</Badge>
                </div>
                <div class="leading-none opacity-40">{{ server.ipv4 }} &bull; {{ server.ipv6 }}</div>
            </div>

            <template v-if="server.status === 'active'">
                <div>
                    <h3 class="mb-3 text-2xl font-semibold tracking-tight">Services</h3>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <Card v-for="service in server.services" :key="service.id">
                            <CardHeader>
                                <div class="flex items-center gap-2">
                                    <DatabaseIcon v-if="service.category === 'database'" class="size-5 opacity-50" />
                                    <CardTitle>{{ service.name }}</CardTitle>
                                    <Badge :variant="service.status === 'active' ? 'success' : 'secondary'">{{
                                        service.status.replace('-', ' ')
                                    }}</Badge>
                                </div>
                                <CardDescription>
                                    <span class="capitalize">{{ service.type }}</span> {{ service.version }} &bull;
                                    <Layers2Icon class="inline-block size-4" /> {{ service.slices?.length }} slices
                                </CardDescription>
                            </CardHeader>
                        </Card>
                    </div>
                </div>
            </template>
            <template v-else-if="server.status === 'provisioning'">
                <div class="flex items-center gap-4 py-6">
                    <div class="flex-0 flex-shrink">
                        <LoaderCircleIcon class="size-8 animate-spin" />
                    </div>
                    <div class="relative flex-grow">
                        <Transition
                            enter-active-class="transition duration-500 ease-in-out"
                            enter-from-class="opacity-0 -translate-x-4"
                            enter-to-class="opacity-100 translate-x-0"
                            leave-active-class="transition absolute left-0 duration-500 ease-in-out"
                            leave-from-class="opacity-100 translate-x-0"
                            leave-to-class="opacity-0 translate-x-4"
                        >
                            <div :key="provisionMessage">{{ provisionMessage }}</div>
                        </Transition>
                    </div>
                </div>
            </template>
            <template> Something else </template>

            <div v-if="$page.props.flash?.server_credentials" class="p-5">
                <div class="mb-4 text-sm font-medium text-gray-900 dark:text-white">
                    WILL NOT BE SHOWN AGAIN:
                    {{ $page.props.flash.server_credentials }}
                </div>
            </div>

            <!-- {{ server }} -->
        </div>
    </AppLayout>
</template>
