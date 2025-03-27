<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div>The plan:</div>
            <div>Organisations have projects</div>
            <div>Projects have environments</div>
            <div>Environments have servers</div>
            <div>Servers have services</div>
            <div>Servers can have services</div>
            <div>Services have slices, this could be a database table or a client in a websocket server</div>
            <div>Also servers need to be provisioned</div>
            <div>Users have SSH keys</div>
            <div>Keystone has its own SSH keys</div>
        </div>
    </AppLayout>
</template>
