<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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
    is_approved: boolean; // Status field
}

const props = defineProps<{ clients: Client[] }>();
const clients = ref<Client[]>(props.clients);

const editClient = (id: number) => {
    router.get(route('manager.edit-client', id));
};

const deleteClient = (id: number) => {
    if (confirm('Are you sure you want to delete this client?')) {
        router.delete(route('manager.delete-client', id), {
            preserveScroll: true,
            onSuccess: () => {
                clients.value = clients.value.filter((client) => client.id !== id);
            },
        });
    }
};

const approveClient = (id: number) => {
    router.post(
        route('manager.approve-client'),
        { client_id: id },
        {
            preserveScroll: true,
            onSuccess: () => {
                const clientIndex = clients.value.findIndex((c) => c.id === id);
                if (clientIndex !== -1) {
                    clients.value[clientIndex].is_approved = true; // Update status
                }
            },
        },
    );
};
</script>

<template>
    <Head title="Manage Clients" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full">
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
                                    <Button 
                                        v-if="!client.is_approved" 
                                        variant="outline" 
                                        class="text-green-500 mr-2" 
                                        @click="approveClient(client.id)"
                                    > 
                                        Approve 
                                    </Button>
                                    <Button 
                                        variant="outline" 
                                        class="text-blue-500 mr-2" 
                                        @click="editClient(client.id)"
                                    > 
                                        Edit 
                                    </Button>
                                    <Button 
                                        variant="outline" 
                                        class="text-red-500" 
                                        @click="deleteClient(client.id)"
                                    > 
                                        Delete 
                                    </Button>
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
    </AppLayout>
</template>
