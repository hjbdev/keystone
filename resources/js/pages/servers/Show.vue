<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useCycleList, useInterval } from '@vueuse/core';
import { DatabaseIcon, Layers2Icon, LoaderCircleIcon, PlusIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';

defineProps({
    server: {
        type: Object,
        required: true,
    },
});

const selectedStep = ref(null);

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
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-2xl font-semibold tracking-tight">Services</h3>
                        <div>
                            <Button
                                :as="Link"
                                :href="
                                    route('services.create', {
                                        organisation: $page.props.organisation.id,
                                        server: server.id,
                                    })
                                "
                                size="xs"
                            >
                                <PlusIcon class="size-4" />
                                Add
                            </Button>
                        </div>
                    </div>
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
                <div>
                    <h3 class="mb-3 text-2xl font-semibold tracking-tight">Deployments</h3>
                    <Card>
                        <CardContent class="py-4">
                            <div v-for="deployment in server.service_deployments" class="flex gap-4">
                                <div class="w-48 leading-none">{{ deployment.target.name }}</div>
                                <div class="w-full space-y-4">
                                    <div v-for="step in deployment.steps" class="flex items-center space-y-1">
                                        <div class="flex-1">
                                            <div class="text-sm font-semibold leading-none">
                                                {{ step.name ?? 'Unnamed Step' }}
                                            </div>
                                            <div v-if="step.error_logs">
                                                <pre class="text-xs text-muted-foreground"
                                                    >{{ step.error_logs_excerpt.length !== step.error_logs ? '... ' : ''
                                                    }}{{ step.error_logs_excerpt }}</pre
                                                >
                                            </div>
                                            <div v-else-if="step.logs">
                                                <pre class="text-xs text-muted-foreground"
                                                    >{{ step.logs_excerpt.length !== step.logs ? '... ' : '' }}{{ step.logs_excerpt }}</pre
                                                >
                                            </div>
                                        </div>
                                        <div>
                                            <Button
                                                size="xs"
                                                variant="link"
                                                @click="
                                                    () => {
                                                        selectedStep = step;
                                                    }
                                                "
                                            >
                                                View
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
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

            <Dialog :open="!!selectedStep" @update:open="($event) => (!$event ? (selectedStep = null) : null)">
                <DialogContent class="md:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>Logs for {{ selectedStep?.name }}</DialogTitle>
                    </DialogHeader>
                    <section v-if="selectedStep?.logs">
                        <h3 class="text-sm font-medium">Logs</h3>
                        <pre class="text-xs text-muted-foreground">{{ selectedStep?.logs }}</pre>
                    </section>
                    <section v-if="selectedStep?.error_logs">
                        <h3 class="text-sm font-medium">Error Logs</h3>
                        <pre class="max-w-full overflow-x-scroll text-xs text-muted-foreground">{{ selectedStep?.error_logs }}</pre>
                    </section>
                </DialogContent>
            </Dialog>

            <!-- {{ server }} -->
        </div>
    </AppLayout>
</template>
