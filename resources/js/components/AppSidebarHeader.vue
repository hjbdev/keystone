<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';
import { Button } from './ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from './ui/dropdown-menu';

defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();

const organisation = usePage().props.organisation ?? { name: 'Select Organisation' };
const application = usePage().props.application ?? { name: 'Select Application' };
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-in-out group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-4">
            <SidebarTrigger class="-ml-1" />
            <div class="gap-0.25 flex items-center">
                <Button :as="Link" :href="route('organisations.show', { organisation: organisation?.id })" variant="ghost" size="xs">
                    {{ organisation?.name }}
                </Button>
                <DropdownMenu>
                    <DropdownMenuTrigger :as="Button" size="iconxs" variant="ghost">
                        <ChevronsUpDown class="size-3" />
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DropdownMenuItem
                            v-for="org in $page.props.auth.user?.organisations"
                            :as="Link"
                            :href="route('organisations.show', { organisation: org.id })"
                            >{{ org.name }}</DropdownMenuItem
                        >
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
            <div class="gap-0.25 flex items-center">
                <Button
                    :as="Link"
                    :href="route('applications.show', { organisation: application.organisation_id, application: application.id })"
                    variant="ghost"
                    size="xs"
                >
                    {{ application?.name }}
                </Button>
                <DropdownMenu>
                    <DropdownMenuTrigger :as="Button" size="iconxs" variant="ghost">
                        <ChevronsUpDown class="size-3" />
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DropdownMenuItem
                            v-for="app in organisation?.applications"
                            :as="Link"
                            :href="route('applications.show', { organisation: app.organisation_id, application: app.id })"
                            >{{ app.name }}</DropdownMenuItem
                        >
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
            <template v-if="breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
    </header>
</template>
