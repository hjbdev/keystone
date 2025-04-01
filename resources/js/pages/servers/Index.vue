<script setup>
import { Badge } from '@/components/ui/badge';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    servers: {
        type: [Object, null],
        required: true,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout
        :breadcrumbs="[
            {
                title: 'Servers',
                href: route('servers.index', {
                    organisation: $page.props.organisation.id,
                }),
            },
        ]"
    >
        <div class="flex justify-between items-center gap-3 p-4">
            <h2 class="text-3xl font-bold tracking-tight">Servers</h2>
            <div>
                <Button :as="Link" :href="route('servers.create', {
                    organisation: $page.props.organisation.id,
                })">Create</Button>
            </div>
        </div>
        <div class="grid gap-4 rounded-xl p-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="server in servers.data" :key="`server{$servers.id}`" class="relative w-full">
                <CardHeader>
                    <CardTitle>{{ server.name }}</CardTitle>
                    <CardDescription>
                        <Badge :variant="server.status === 'active' ? 'success' : 'secondary'">{{ server.status.replace('-', ' ') }}</Badge> &bull;
                        {{ server.ipv4 || server.ipv6 }}</CardDescription
                    >
                </CardHeader>
                <Link
                    :href="
                        route('servers.show', {
                            organisation: $page.props.organisation.id,
                            server: server.id,
                        })
                    "
                    class="absolute inset-0"
                ></Link>
            </Card>

            <div>@todo pagination</div>
        </div>
    </AppLayout>
</template>
