<script setup>
import RadioButton from '@/components/RadioButton.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    providers: Array,
    locations: Array,
    serverTypes: Array,
    images: Array,
});

const form = useForm({
    provider: null,
    location: null,
    network_zone: null,
    server_type: null,
    image: null,
});

watch(
    () => form.provider,
    () => {
        loadLocations();
    },
);

watch(
    () => form.location,
    (location) => {
        const selectedLoc = props.locations.find((loc) => loc.id === location)?.networkZone;
        form.network_zone = selectedLoc;
        loadServerTypes();
    },
);

loadLocations();
loadServerTypes();

function loadLocations() {
    if (form.provider && !props.locations) {
        router.reload({
            only: ['locations'],
            data: {
                provider: form.provider,
            },
            async: true,
        });
    }
}

function loadServerTypes() {
    if (form.location && !props.serverTypes) {
        router.reload({
            only: ['serverTypes', 'images'],
            data: {
                provider: form.provider,
                location: form.location,
            },
            async: true,
        });
    }
}
</script>

<template>
    <Head title="Create Server" />

    <AppLayout
        :breadcrumbs="[
            {
                title: 'Servers',
                href: route('servers.index', {
                    organisation: $page.props.organisation.id,
                }),
            },
            {
                title: 'Create',
            },
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-wrap gap-2">
                <RadioButton
                    v-for="provider in providers"
                    v-model="form.provider"
                    :value="provider.id"
                    :disabled="provider.disabled"
                    name="server-provider"
                >
                    {{ provider.name }}
                </RadioButton>
            </div>
            <div v-if="form.provider" class="flex flex-wrap gap-2">
                <RadioButton v-for="location in locations" v-model="form.location" :value="location.id" :disabled="location.disabled" name="location">
                    {{ location.city }}
                </RadioButton>
            </div>
            <div v-if="form.location" class="grid gap-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <RadioButton
                    v-for="serverType in serverTypes?.sort((a, b) => a.cores - b.cores) ?? []"
                    v-model="form.server_type"
                    :value="serverType.id"
                    :disabled="serverType.disabled"
                    name="server-type"
                >
                    <h5 class="text-lg font-semibold uppercase tracking-tight">{{ serverType.name }}</h5>
                    <p class="text-sm opacity-60">
                        {{ serverType.cores }} cores &bull; {{ serverType.memory }} GB RAM &bull; {{ serverType.disk }} GB disk
                    </p>
                </RadioButton>
            </div>
            <div v-if="form.server_type" class="flex flex-wrap gap-2">
                <RadioButton v-for="image in images" v-model="form.image" :value="image.id" :disabled="image.disabled" name="image">
                    <h5 class="text-lg font-semibold tracking-tight">{{ image.name }}</h5>
                </RadioButton>
            </div>
            <div class="flex items-center justify-end">
                <Button @click="form.post(route('servers.store', { organisation: $page.props.organisation.id }))">Submit</Button>
            </div>
        </div>
    </AppLayout>
</template>
