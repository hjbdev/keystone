<script setup>
import InputError from '@/components/InputError.vue';
import RadioButton from '@/components/RadioButton.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ServiceCategory, { DescriptionMap as serviceCategoryDescriptions } from '@/enums/ServiceCategory';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { AppWindowIcon, ArchiveIcon, DatabaseIcon, DatabaseZapIcon, DoorOpenIcon } from 'lucide-vue-next';
import { watch } from 'vue';

const props = defineProps({
    services: Object,
});

const form = useForm({
    name: null,
    category: null,
    service: null,
    version: null,
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

function generateServiceName() {
    let str = '';

    if (form.category) {
        str += form.category.toLowerCase() + '-';
    }

    if (form.service) {
        str += form.service.toLowerCase() + '-';
    }

    if (form.version) {
        str += form.version.toLowerCase();
    }

    return str;
}

watch([() => form.category, () => form.service, () => form.version], () => {
    form.name = generateServiceName();
});
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
            <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                <RadioButton
                    v-for="(category, categoryKey) in ServiceCategory"
                    v-model="form.category"
                    :value="category"
                    name="category"
                    class="flex gap-3 py-3"
                >
                    <component :is="getIcon(category)" class="size-5" />
                    <div>
                        <h4 class="mb-1 text-lg font-semibold leading-none tracking-tighter">{{ category }}</h4>
                        <p class="text-sm">{{ serviceCategoryDescriptions[categoryKey] }}</p>
                    </div>
                </RadioButton>
            </div>

            <div v-if="form.category" class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                <RadioButton
                    v-for="service in services[form.category]"
                    v-model="form.service"
                    :value="service.name"
                    name="service"
                    class="py-3"
                >
                    <h4 class="mb-1 text-lg font-semibold leading-none tracking-tighter">{{ service.name }}</h4>
                </RadioButton>
            </div>

            <div v-if="form.service" class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                <RadioButton
                    v-for="(version, versionKey) in services[form.category][form.service].versions"
                    v-model="form.version"
                    :value="versionKey"
                    name="version"
                    class="py-3"
                >
                    <h4 class="mb-1 text-lg font-semibold leading-none tracking-tighter">{{ version.name }}</h4>
                </RadioButton>
            </div>

            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input id="name" type="text" required autofocus :tabindex="1" v-model="form.name" placeholder="postgres-db" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="flex items-center justify-end">
                <Button @click="form.post(route('services.store', { organisation: $page.props.organisation.id, server: $page.props.server.id }))">Submit</Button>
            </div>
        </div>
    </AppLayout>
</template>
