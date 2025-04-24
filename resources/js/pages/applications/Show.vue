<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Layers2Icon } from 'lucide-vue-next';

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout
        :breadcrumbs="[
            {
                title: 'Applications',
                href: route('applications.index', { organisation: $page.props.organisation.id }),
            },
            {
                title: props.application.name,
                href: route('applications.show', {
                    organisation: $page.props.organisation.id,
                    application: props.application.id,
                }),
            },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <h2 class="text-3xl font-bold tracking-tight">{{ application.name }}</h2>
            </div>

            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-2xl font-semibold tracking-tight">Environments</h3>
                    <div>
                        <!-- <Button
                            :as="Link"
                            :href="
                                route('environments.create', {
                                    organisation: $page.props.organisation.id,
                                    server: application.id,
                                })
                            "
                            size="xs"
                        >
                            <PlusIcon class="size-4" />
                            Add
                        </Button> -->
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="environment in application.environments" :key="environment.id" class="relative">
                        <Link class="absolute inset-0" :href="route('environments.show', {
                            organisation: $page.props.organisation.id,
                            application: application.id,
                            environment: environment.id,
                        })"></Link>
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <CardTitle>{{ environment.name }}</CardTitle>
                                <Badge :variant="environment.status === 'active' ? 'success' : 'secondary'">{{ environment.status.replace('-', ' ') }}</Badge>
                            </div>
                            <CardDescription>
                                <span class="capitalize">{{ environment.type }}</span> {{ environment.version }}
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
            {{ application }}
        </div>
    </AppLayout>
</template>
