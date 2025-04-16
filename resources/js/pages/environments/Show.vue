<script setup lang="ts">
import ServerSelector from '@/components/ServerSelector.vue';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { PlusIcon } from 'lucide-vue-next';

const props = defineProps({
    environment: {
        type: Object,
        required: true,
    },
    servers: {
        type: Array,
        required: false,
    }
});
</script>

<template>
    <Head :title="environment.name" />

    <AppLayout
        :breadcrumbs="[
            {
                title: 'Applications',
                href: route('applications.index', { organisation: $page.props.organisation.id }),
            },
            {
                title: environment.application.name,
                href: route('applications.show', {
                    organisation: $page.props.organisation.id,
                    application: environment.application.id,
                }),
            },
            {
                title: 'Environments',
            },
            {
                title: environment.name,
                href: route('environments.show', {
                    organisation: $page.props.organisation.id,
                    application: environment.application.id,
                    environment: environment.id,
                }),
            },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="pattern-graph-paper grid grid-cols-3 gap-6 p-6">
                <div class="space-y-2">
                    <ServerSelector :servers="servers">
                        <template #trigger>
                            <Card class="group flex select-none items-center gap-2 bg-card/30 p-4 text-sm backdrop-blur-sm">
                                <PlusIcon class="size-4 opacity-50" />
                                <span class="opacity-50 transition group-hover:opacity-100">Install a gateway</span>
                            </Card>
                        </template>
                    </ServerSelector>
                    <!-- <ServiceCard :icon="DoorOpenIcon" :service-type="ServiceType.CADDY" :service-category="ServiceCategory.GATEWAY" :status="ServiceStatus.NOT_INSTALLED" /> -->
                </div>
                <div class="space-y-2">
                    <Card class="group flex select-none items-center gap-2 bg-card/30 p-4 text-sm backdrop-blur-sm">
                        <PlusIcon class="size-4 opacity-50" />
                        <span class="opacity-50 transition group-hover:opacity-100">Install your application on a server</span>
                    </Card>
                    <!-- <ServiceCard
                        :icon="AppWindowIcon"
                        :service-type="ServiceType.FRANKENPHP"
                        :service-category="ServiceCategory.APPLICATION"
                        :status="ServiceStatus.NOT_INSTALLED"
                    /> -->
                </div>
                <div class="space-y-2">
                    <!-- <ServiceCard :icon="DatabaseIcon" :service-type="ServiceType.POSTGRES" :service-category="ServiceCategory.DATABASE" :status="ServiceStatus.NOT_INSTALLED" /> -->
                    <!-- <ServiceCard :icon="DatabaseZap" :service-type="ServiceType.REDIS" :service-category="ServiceCategory.CACHE" :status="ServiceStatus.NOT_INSTALLED" /> -->
                    <Card class="group flex select-none items-center gap-2 bg-card/30 p-4 text-sm backdrop-blur-sm">
                        <PlusIcon class="size-4 opacity-50" />
                        <span class="opacity-50 transition group-hover:opacity-100">Add a database</span>
                    </Card>
                    <Card class="group flex select-none items-center gap-2 bg-card/30 p-4 text-sm backdrop-blur-sm">
                        <PlusIcon class="size-4 opacity-50" />
                        <span class="opacity-50 transition group-hover:opacity-100">Add cache</span>
                    </Card>
                </div>
            </Card>
            {{ environment }}
        </div>
    </AppLayout>
</template>
