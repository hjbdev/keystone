<script setup>
import { Badge } from '@/components/ui/badge';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    applications: {
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
                title: 'Applications',
                href: route('applications.index', {
                    organisation: $page.props.organisation.id,
                }),
            },
        ]"
    >
        <div class="flex justify-between items-center gap-3 p-4">
            <h2 class="text-3xl font-bold tracking-tight">Applications</h2>
            <div>
                <!-- <Button :as="Link" :href="route('applications.create', {
                    organisation: $page.props.organisation.id,
                })">Create</Button> -->
            </div>
        </div>
        <div class="grid gap-4 rounded-xl p-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="application in applications.data" :key="`application{$applications.id}`" class="relative w-full">
                <CardHeader>
                    <CardTitle>{{ application.name }}</CardTitle>
                </CardHeader>
                <Link
                    :href="
                        route('applications.show', {
                            organisation: $page.props.organisation.id,
                            application: application.id,
                        })
                    "
                    class="absolute inset-0"
                ></Link>
            </Card>
        </div>
    </AppLayout>
</template>
