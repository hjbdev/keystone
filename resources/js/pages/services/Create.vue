<script setup>
import RadioButton from '@/components/RadioButton.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({

});

const form = useForm({
    name: null,
    category: null,
    type: null,
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
            }
        ]"
    >
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            
          
            <div>
                
            </div>
            
            <div class="flex items-center justify-end">
                <Button @click="form.post(route('servers.store', { organisation: $page.props.organisation.id }))">Submit</Button>
            </div>
        </div>
    </AppLayout>
</template>
