<script setup>
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    servers: {
        type: [Array, null],
        required: true,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="[
        {
            title: 'Servers',
            href: route('servers.index', {
                organisation: $page.props.organisation.id,
            }),
        },
    ]">
        <div class="grid gap-4 rounded-xl p-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="server in servers.data" :key="`server{$servers.id}`" class="w-full relative">
                <CardHeader>
                    <CardTitle>{{ server.name }}</CardTitle>
                    <CardDescription
                        ><span class="inline-block rounded-md bg-green-200 px-2 text-xs uppercase text-green-800">{{ server.status }}</span> &bull;
                        {{ server.ipv4 || server.ipv6 }}</CardDescription
                    >
                </CardHeader>
                <Link :href="route('servers.show', {
                    organisation: $page.props.organisation.id,
                    server: server.id,
                })" class="absolute inset-0"></Link>
            </Card>

            <div>@todo pagination</div>
        </div>
    </AppLayout>
</template>
