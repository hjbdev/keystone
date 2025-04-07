<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, WhenVisible } from '@inertiajs/vue3';

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
            <Tabs default-value="dashboard" class="w-[400px]" :unmount-on-hide="false">
                <TabsList>
                    <TabsTrigger value="dashboard"> Dashboard </TabsTrigger>
                    <TabsTrigger value="settings"> Settings </TabsTrigger>
                </TabsList>
                <TabsContent value="dashboard"> Overview on organisation. </TabsContent>
                <TabsContent value="settings"> 
                    <WhenVisible data="providers">
                        <template #fallback>
                            Loading...
                        </template>
                        <h3 class="text-2xl font-bold tracking-tight mt-4">Server Providers</h3>
                        <p class="text-sm text-muted-foreground mb-4">Manage your server providers.</p>
                        <div class="border border-muted-background divide-y divide-y-muted-background rounded-md">
                            <div v-for="provider in providers" class="py-1 px-2 flex gap-2 items-center">
                                {{ provider.name }}
                                <span class="uppercase text-xs ml-auto text-muted-foreground">{{ provider.type }}</span>
                            </div>
                        </div>
                    </WhenVisible>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
