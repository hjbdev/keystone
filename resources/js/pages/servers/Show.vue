<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { DatabaseIcon, Layers2Icon } from 'lucide-vue-next';

const props = defineProps({
    server: {
        type: Object,
        required: true,
    },
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

            <div>
                <h3 class="text-2xl font-semibold tracking-tight mb-3">Services</h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="service in server.services" :key="service.id">
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <DatabaseIcon v-if="service.category === 'database'" class="size-5 opacity-50" />
                                <CardTitle>{{ service.name }}</CardTitle>
                                <Badge :variant="service.status === 'active' ? 'success' : 'secondary'">{{ service.status.replace('-', ' ') }}</Badge>
                            </div>
                            <CardDescription>
                                <span class="capitalize">{{ service.type }}</span> {{ service.version }} &bull;
                                <Layers2Icon class="inline-block size-4" /> {{ service.slices?.length }} slices
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>

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
