<script setup>
import { Card } from '@/components/ui/card';
import ServiceCategory from '@/enums/ServiceCategory';
import ServiceStatus from '@/enums/ServiceStatus';
import ServiceType from '@/enums/ServiceType';
import { DoorOpenIcon } from 'lucide-vue-next';

defineProps({
    icon: {
        type: [Object, Function],
        default: () => DoorOpenIcon,
    },
    serviceType: {
        type: String,
        default: ServiceType.GATEWAY,
    },
    serviceCategory: {
        type: String,
        default: ServiceCategory.DATABASE
    },
    status: {
        type: String,
        default: ServiceStatus.UNKNOWN,
    },
});
</script>
<template>
    <Card class="flex select-none items-center justify-between gap-4 bg-card/30 p-4 backdrop-blur-sm">
        <div class="flex items-center gap-3">
            <component :is="icon" class="size-4 opacity-50" />
            <div>
                <div class="capitalize">{{ serviceCategory }}</div>
                <div class="text-xs opacity-50">{{ serviceType }}</div>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <span
                class="inline-block size-1 rounded-full dark:bg-zinc-500"
                :class="{
                    'bg-zinc-300 dark:bg-zinc-500': status === ServiceStatus.UNKNOWN || status === ServiceStatus.NOT_INSTALLED,
                    'bg-green-300 dark:bg-green-500': status === ServiceStatus.RUNNING,
                    'bg-red-300 dark:bg-red-500': status === ServiceStatus.STOPPED,
                    'bg-yellow-300 dark:bg-yellow-500': status === ServiceStatus.INSTALLING,
                }"
            ></span>
            <span
                class="text-xs dark:text-zinc-500"
                :class="{
                    'text-zinc-300 dark:text-zinc-500': status === ServiceStatus.UNKNOWN || status === ServiceStatus.NOT_INSTALLED,
                    'text-green-300 dark:text-green-500': status === ServiceStatus.RUNNING,
                    'text-red-300 dark:text-red-500': status === ServiceStatus.STOPPED,
                    'text-yellow-300 dark:text-yellow-500': status === ServiceStatus.INSTALLING,
                }"
                >{{ status.replaceAll('-', ' ') }}</span
            >
        </div>
    </Card>
</template>
