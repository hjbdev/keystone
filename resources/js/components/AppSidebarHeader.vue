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

const organisation = usePage().props.organisation ?? null;
const application = usePage().props.application ?? null;
const environment = usePage().props.environment ?? null;
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-in-out group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex w-full items-center gap-4">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
            <div class="gap-0.25 ml-auto flex items-center">
                <Button
                    :as="organisation ? Link : 'button'"
                    :href="organisation ? route('organisations.show', { organisation: organisation?.id }) : null"
                    variant="ghost"
                    size="xs"
                >
                    {{ organisation?.name ?? 'Select Organisation' }}
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
            <div v-if="organisation" class="gap-0.25 flex items-center">
                <Button
                    :disabled="!organisation?.applications?.length"
                    :as="application ? Link : 'button'"
                    :href="
                        application ? route('applications.show', { organisation: application.organisation_id, application: application.id }) : null
                    "
                    variant="ghost"
                    size="xs"
                >
                    {{ application?.name ?? 'Application' }}
                </Button>
                <DropdownMenu>
                    <DropdownMenuTrigger :as="Button" size="iconxs" variant="ghost" :disabled="!organisation?.applications?.length">
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
            <div class="gap-0.25 flex items-center">
                <Button
                    :disabled="!application?.environments?.length"
                    :as="environment ? Link : 'button'"
                    :href="
                        environment
                            ? route('environments.show', {
                                  organisation: application.organisation_id,
                                  application: application.id,
                                  environment: environment.id,
                              })
                            : null
                    "
                    variant="ghost"
                    size="xs"
                >
                    {{ environment?.name ?? 'Environment' }}
                </Button>
                <DropdownMenu>
                    <DropdownMenuTrigger :as="Button" size="iconxs" variant="ghost" :disabled="!application?.environments?.length">
                        <ChevronsUpDown class="size-3" />
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DropdownMenuItem
                            v-for="env in application?.environments"
                            :as="Link"
                            :href="
                                route('environments.show', {
                                    organisation: application.organisation_id,
                                    application: application.id,
                                    environment: env.id,
                                })
                            "
                            >{{ env.name }}</DropdownMenuItem
                        >
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
    </header>
</template>
