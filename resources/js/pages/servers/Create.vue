<script setup>
import RadioButton from '@/components/RadioButton.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    locations: Array,
    serverTypes: Array,
    images: Array,
});

const form = useForm({
    provider: 'hetzner',
    location: null,
    server_type: null,
    image: null,
});

const serverProviders = [
    {
        name: 'Hetzner',
        value: 'hetzner',
    },
    {
        name: 'Digital Ocean',
        value: 'digital-ocean',
        disabled: true,
    },
];

watch(
    () => form.provider,
    (provider) => {
        console.log(provider);
    },
);

if (form.provider && !props.locations) {
    router.reload({
        only: ['locations'],
        data: {
            provider: form.provider,
        },
        async: true,
    });
}
</script>

<template>
    <Head title="Create Server" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-wrap gap-2">
                <RadioButton
                    v-for="serverProvider in serverProviders"
                    v-model="form.provider"
                    :value="serverProvider.value"
                    :disabled="serverProvider.disabled"
                    name="server-provider"
                >
                    {{ serverProvider.name }}
                </RadioButton>
            </div>
            <div v-if="form.provider" class="flex flex-wrap gap-2">
                <RadioButton
                    v-for="location in locations"
                    v-model="form.location"
                    :value="location.id"
                    :disabled="location.disabled"
                    name="location"
                >
                    {{ location.city }}
                </RadioButton>
            </div>
            <div v-if="form.location" class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
                <RadioButton
                    v-for="serverType in serverTypes?.sort((a, b) => a.cores - b.cores) ?? []"
                    v-model="form.server_type"
                    :value="serverType.id"
                    :disabled="serverType.disabled"
                    name="server-type"
                >
                    <h5 class="text-lg font-semibold uppercase tracking-tight">{{ serverType.name }}</h5>
                    <p class="text-sm opacity-60">{{ serverType.cores }} cores &bull; {{ serverType.memory }} GB RAM &bull; {{ serverType.disk }} GB disk</p>
                </RadioButton>
            </div>
            <div v-if="form.server_type" class="flex gap-2 flex-wrap">
                <RadioButton
                    v-for="image in images"
                    v-model="form.image"
                    :value="image.id"
                    :disabled="image.disabled"
                    name="image"
                >
                    <h5 class="text-lg font-semibold tracking-tight">{{ image.name }}</h5>
                </RadioButton>
            </div>
            <div class="flex justify-end items-center">
                <Button @click="form.post(route('servers.store', { organisation: $page.props.organisation.id }))">Submit</Button>
            </div>
        </div>
    </AppLayout>
</template>
