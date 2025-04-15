<script setup lang="ts">
import ServiceCard from '@/components/environments/ServiceCard.vue';
import { Card } from '@/components/ui/card';
import ServiceCategory from '@/enums/ServiceCategory';
import ServiceStatus from '@/enums/ServiceStatus';
import ServiceType from '@/enums/ServiceType';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { AppWindowIcon, DatabaseIcon, DatabaseZap, DoorOpenIcon, PlusIcon } from 'lucide-vue-next';

const props = defineProps({
    environment: {
        type: Object,
        required: true,
    },
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
                    <Card class="flex select-none items-center gap-2 bg-card/30 p-4 backdrop-blur-sm text-sm group">
                        <PlusIcon class="size-4 opacity-50" />
                        <span class="opacity-50 group-hover:opacity-100 transition">Install a gateway</span>
                    </Card>
                    <!-- <ServiceCard :icon="DoorOpenIcon" :service-type="ServiceType.CADDY" :service-category="ServiceCategory.GATEWAY" :status="ServiceStatus.NOT_INSTALLED" /> -->
                </div>
                <div class="space-y-2">
                    <Card class="flex select-none items-center gap-2 bg-card/30 p-4 backdrop-blur-sm text-sm group">
                        <PlusIcon class="size-4 opacity-50" />
                        <span class="opacity-50 group-hover:opacity-100 transition">Install your application on a server</span>
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
                    <Card class="flex select-none items-center gap-2 bg-card/30 p-4 backdrop-blur-sm text-sm group">
                        <PlusIcon class="size-4 opacity-50" />
                        <span class="opacity-50 group-hover:opacity-100 transition">Add a database</span>
                    </Card>
                    <Card class="flex select-none items-center gap-2 bg-card/30 p-4 backdrop-blur-sm text-sm group">
                        <PlusIcon class="size-4 opacity-50" />
                        <span class="opacity-50 group-hover:opacity-100 transition">Add cache</span>
                    </Card>
                </div>
            </Card>
            {{ environment }}
        </div>
    </AppLayout>
</template>
