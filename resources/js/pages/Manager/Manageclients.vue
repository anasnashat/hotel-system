<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { router } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Clients', href: '/manager/manage-clients' },
];

interface Client {
    id: number;
    user: { name: string; email: string };
    is_approved: boolean;
}

const props = defineProps<{ clients: Client[] }>();
const clients = ref<Client[]>(props.clients);

const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentClient = ref<Client | null>(null);
const form = ref({ name: '', email: '' });

const openAddModal = () => {
    isEditMode.value = false;
    form.value = { name: '', email: '' };
    isModalOpen.value = true;
};

const openEditModal = (client: Client) => {
    isEditMode.value = true;
    currentClient.value = client;
    form.value = { name: client.user.name, email: client.user.email };
    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditMode.value && currentClient.value) {
        router.put(route('manager.update-client', currentClient.value.id), form.value, {
            preserveScroll: true,
            onSuccess: () => isModalOpen.value = false,
        });
    } else {
        router.post(route('manager.store-client'), form.value, {
            preserveScroll: true,
            onSuccess: () => isModalOpen.value = false,
        });
    }
};

const deleteClient = (id: number) => {
    if (confirm('Are you sure you want to delete this client?')) {
        router.delete(route('manager.delete-client', id), {
            preserveScroll: true,
            onSuccess: () => clients.value = clients.value.filter(client => client.id !== id),
        });
    }
};

const approveClient = (id: number) => {
    router.post(route('manager.approve-client'), { client_id: id }, {
        preserveScroll: true,
        onSuccess: () => {
            const clientIndex = clients.value.findIndex(c => c.id === id);
            if (clientIndex !== -1) clients.value[clientIndex].is_approved = true;
        },
    });
};
</script>

<template>
    <Head title="Manage Clients" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6">
            <h1 class="text-3xl font-bold mb-6 text-center">Manage Clients</h1>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="clients.length">
                            <TableRow v-for="client in clients" :key="client.id">
                                <TableCell>{{ client.id }}</TableCell>
                                <TableCell>{{ client.user.name }}</TableCell>
                                <TableCell>{{ client.user.email }}</TableCell>
                                <TableCell>
                                    <span :class="client.is_approved ? 'text-green-500' : 'text-yellow-500'">
                                        {{ client.is_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <Button v-if="!client.is_approved" variant="outline" class="text-green-500 mr-2" @click="approveClient(client.id)">Approve</Button>
                                    <Button variant="outline" class="text-blue-500 mr-2" @click="openEditModal(client)">Edit</Button>
                                    <Button variant="outline" class="text-red-500" @click="deleteClient(client.id)">Delete</Button>
                                </TableCell>
                            </TableRow>
                        </template>
                        <TableRow v-else>
                            <TableCell colspan="5" class="h-24 text-center">No clients found.</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ isEditMode ? 'Edit Client' : 'Add New Client' }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm">
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right">Name</Label>
                            <Input id="name" v-model="form.name" class="col-span-3" />
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="email" class="text-right">Email</Label>
                            <Input id="email" v-model="form.email" type="email" class="col-span-3" />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="submit">{{ isEditMode ? 'Update' : 'Add' }}</Button>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
