<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, WhenVisible } from '@inertiajs/vue3';
import { AppWindowIcon, ServerIcon, UserIcon } from 'lucide-vue-next';

defineProps({
    organisation: {
        type: Object,
        required: true,
    },
    providers: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head :title="organisation.name" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <h2 class="text-3xl font-bold tracking-tight">{{ organisation.name }}</h2>
            <Tabs default-value="dashboard" :unmount-on-hide="false">
                <TabsList>
                    <TabsTrigger value="dashboard"> Dashboard </TabsTrigger>
                    <TabsTrigger value="settings"> Settings </TabsTrigger>
                </TabsList>
                <TabsContent value="dashboard">
                    <h3 class="mt-4 text-2xl font-bold tracking-tight">Your Resources</h3>
                    <p class="mb-4 text-sm text-muted-foreground">Your organisation, at a glance.</p>
                    <div class="grid w-full gap-4 md:grid-cols-3">
                        <Card class="relative">
                            <Link
                                :href="
                                    route('applications.index', {
                                        organisation: organisation.id,
                                    })
                                "
                                class="absolute inset-0"
                            />
                            <CardContent class="flex items-center gap-4 p-4">
                                <AppWindowIcon class="size-6 text-muted-foreground" />
                                <div>
                                    <h4 class="mb-1 text-3xl font-medium leading-none">{{ organisation.applications_count }}</h4>
                                    <p class="text-sm text-muted-foreground">Application{{ organisation.applications_count === 1 ? '' : 's' }}</p>
                                </div>
                            </CardContent>
                        </Card>
                        <Card class="relative">
                            <Link
                                class="absolute inset-0"
                                :href="
                                    route('servers.index', {
                                        organisation: organisation.id,
                                    })
                                "
                            />
                            <CardContent class="flex items-center gap-4 p-4">
                                <ServerIcon class="size-6 text-muted-foreground" />
                                <div>
                                    <h4 class="mb-1 text-3xl font-medium leading-none">{{ organisation.servers_count }}</h4>
                                    <p class="text-sm text-muted-foreground">Server{{ organisation.servers_count === 1 ? '' : 's' }}</p>
                                </div>
                            </CardContent>
                        </Card>
                        <Card class="relative">
                            <CardContent class="flex items-center gap-4 p-4">
                                <UserIcon class="size-6 text-muted-foreground" />
                                <div>
                                    <h4 class="mb-1 text-3xl font-medium leading-none">{{ organisation.members_count }}</h4>
                                    <p class="text-sm text-muted-foreground">Member{{ organisation.members_count === 1 ? '' : 's' }}</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>
                <TabsContent value="settings">
                    <WhenVisible data="providers">
                        <template #fallback> Loading... </template>
                        <h3 class="mt-4 text-2xl font-bold tracking-tight">Server Providers</h3>
                        <p class="mb-4 text-sm text-muted-foreground">Manage your server providers.</p>
                        <div class="border-muted-background divide-y-muted-background divide-y rounded-md border max-w-80">
                            <div v-for="provider in providers" class="flex items-center gap-2 px-2 py-1">
                                {{ provider.name }}
                                <span class="ml-auto text-xs uppercase text-muted-foreground">{{ provider.type }}</span>
                            </div>
                        </div>
                    </WhenVisible>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
