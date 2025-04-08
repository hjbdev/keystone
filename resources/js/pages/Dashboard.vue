<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChevronRightIcon } from 'lucide-vue-next';

defineProps({
    organisations: {
        type: Array,
        required: true,
    },
});

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
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <Card class="w-80">
                <CardHeader class="border-b border-b-muted-background">
                    <CardTitle>Your Organisation</CardTitle>
                    <CardDescription>
                        Select an organisation to view its details.
                    </CardDescription>
                </CardHeader>
                <CardContent class="divide-y divide-y-muted-foreground p-0">
                    <Link v-for="organisation in organisations" :href="route('organisations.show', { organisation: organisation.id })" class="py-3 px-6 hover:bg-muted flex justify-between items-center">
                        <div>{{ organisation.name }}</div>
                        <ChevronRightIcon class="size-4 text-muted-foreground" />
                    </Link>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
