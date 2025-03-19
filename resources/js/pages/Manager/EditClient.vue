<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
    client: { id: number; user: { name: string; email: string } };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Clients', href: '/manager/manage-clients' },
    { title: 'Edit Client', href: '#' },
];

const form = useForm({
    name: props.client.user.name,
    email: props.client.user.email,
});

const submit = () => {
    form.put(route('manager.update-client', props.client.id), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Client updated successfully!');
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
        },
    });
};
</script>

<template>
    <Head title="Edit Client" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-10 border border-gray-200">
            <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Edit Client</h1>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <Label for="name" class="text-gray-700 text-lg">Name</Label>
                    <Input v-model="form.name" id="name" type="text" class="mt-2" />
                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                        {{ form.errors.name }}
                    </div>
                </div>

                <div>
                    <Label for="email" class="text-gray-700 text-lg">Email</Label>
                    <Input v-model="form.email" id="email" type="email" class="mt-2" />
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <Button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 text-lg font-semibold">
                    Update Client
                </Button>
            </form>
        </div>
    </AppLayout>
</template>
