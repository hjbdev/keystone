<script setup>
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Deferred, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Card } from './ui/card';
import { LoaderCircleIcon } from 'lucide-vue-next';

const props = defineProps({
    servers: {
        type: Array,
        required: false,
    },
});

const isOpen = ref(false);

watch(isOpen, () => {
    if (isOpen.value && props.servers === undefined) {
        router.reload({
            only: ['servers'],
            async: true,
        });
    }
});
</script>
<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger class="block w-full">
            <slot name="trigger" />
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Servers</DialogTitle>
                <DialogDescription>Select an active server to install the gateway on.</DialogDescription>
                <div class="my-2 max-h-80 overflow-y-auto">
                    <Deferred data="servers">
                        <template #fallback>
                            <div class="flex justify-center py-4">
                                <LoaderCircleIcon class="text-muted-foreground size-6 animate-spin" />
                            </div>
                        </template>
                        <Card
                            v-for="(server, serverIndex) in servers"
                            :key="`serverPicker-${server.id}`"
                            :data-index="serverIndex"
                            class="flex gap-4 p-2"
                            :class="{
                                'rounded-b-none': serverIndex !== servers.length - 1,
                                'rounded-t-none': serverIndex !== 0,
                                'border-b-0': serverIndex !== servers.length - 1,
                            }"
                        >
                            <div>{{ server.name }}</div>
                            <div></div>
                        </Card>
                        <Card v-if="servers.length === 0" class="p-2 text-sm text-muted-foreground"> No servers available </Card>
                    </Deferred>
                </div>
            </DialogHeader>
            <DialogFooter> Dismiss </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
