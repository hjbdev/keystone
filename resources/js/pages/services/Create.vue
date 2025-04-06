<script setup>
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import ServiceCategory, { DescriptionMap as serviceCategoryDescriptions } from '@/enums/ServiceCategory';
import RadioButton from '@/components/RadioButton.vue';
import { AppWindowIcon, ArchiveIcon, DatabaseIcon, DatabaseZapIcon, DoorOpenIcon } from 'lucide-vue-next';

const props = defineProps({});

const form = useForm({
    name: null,
    category: null,
    type: null,
});

function getIcon(category) {
    switch (category) {
        case ServiceCategory.DATABASE:
            return DatabaseIcon;
        case ServiceCategory.CACHE:
            return DatabaseZapIcon;
        case ServiceCategory.APPLICATION:
            return AppWindowIcon;
        case ServiceCategory.GATEWAY:
            return DoorOpenIcon;
        case ServiceCategory.STORAGE:
            return ArchiveIcon;
        default:
            return null;
    }
}
</script>

<template>
    <Head title="Add Service to Server" />

    <AppLayout
        :breadcrumbs="[
            {
                title: 'Servers',
                href: route('servers.index', {
                    organisation: $page.props.organisation.id,
                }),
            },
            {
                title: 'Services',
            },
            {
                title: 'Create',
            },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-2">
                <RadioButton
                    v-for="(category, categoryKey) in ServiceCategory"
                    v-model="form.category"
                    :value="category"
                    name="category"
                    class="py-3 flex gap-3"
                >
                    <component :is="getIcon(category)" class="size-5" />
                    <div>
                        <h4 class="text-lg font-semibold tracking-tighter leading-none mb-1">{{ category }}</h4>
                        <p class="text-sm">{{ serviceCategoryDescriptions[categoryKey] }}</p>
                    </div>
                </RadioButton>
            </div>

            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    v-model="form.name"
                    placeholder="postgres-db"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="flex items-center justify-end">
                <Button @click="form.post(route('servers.store', { organisation: $page.props.organisation.id }))">Submit</Button>
            </div>
        </div>
    </AppLayout>
</template>
