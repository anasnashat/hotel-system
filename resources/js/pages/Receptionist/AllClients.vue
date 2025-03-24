<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import TabsHeader from '@/components/TabsHeader.vue';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clients', href: route('receptionist.all-clients') },
];

// Page props
const page = usePage();

// Client interface
interface Client {
    id: number;
    user: { name: string; email: string };
    national_id: string;
    phone_number: string;
}

// Props
const { clients: initialClients } = defineProps<{ clients: Client[] }>();

// Reactive clients array
const clients = ref<Client[]>(initialClients);

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentClient = ref<Client | null>(null);
const form = ref({ name: '', email: '', national_id: '', phone_number: '' });

// Delete confirmation state
const isDeleteDialogOpen = ref(false);
const clientToDelete = ref<Client | null>(null);

// Loading state
const isLoading = ref(false);

// Open modal for adding/editing a client
const openClientModal = (client: Client | null) => {
    if (client) {
        isEditMode.value = true;
        currentClient.value = client;
        form.value = {
            name: client.user.name,
            email: client.user.email,
            national_id: client.national_id,
            phone_number: client.phone_number,
        };
    } else {
        isEditMode.value = false;
        currentClient.value = null;
        form.value = { name: '', email: '', national_id: '', phone_number: '' };
    }
    isModalOpen.value = true;
};

// Handle form submission (create/edit)
const submitForm = () => {
    isLoading.value = true;
    if (isEditMode.value && currentClient.value) {
        // Update client
        router.put(route('clients.update', currentClient.value.id), form.value, {
            preserveScroll: true,
            onSuccess: () => {
                // Update the local state
                const index = clients.value.findIndex(c => c.id === currentClient.value?.id);
                if (index !== -1) {
                    clients.value[index] = {
                        ...clients.value[index],
                        user: {
                            ...clients.value[index].user,
                            name: form.value.name,
                            email: form.value.email,
                        },
                        national_id: form.value.national_id,
                        phone_number: form.value.phone_number,
                    };
                }

                isModalOpen.value = false;
                isLoading.value = false;
            },
            onError: () => {
                isLoading.value = false;
            },
        });
    } else {
        // Create client
        router.post(route('clients.store'), form.value, {
            preserveScroll: true,
            onSuccess: () => {
                // Add the new client to the local state
                clients.value.push({
                    id: clients.value.length + 1,
                    user: {
                        name: form.value.name,
                        email: form.value.email,
                    },
                    national_id: form.value.national_id,
                    phone_number: form.value.phone_number,
                });

                isModalOpen.value = false;
                isLoading.value = false;
            },
            onError: () => {
                isLoading.value = false;
            },
        });
    }
};

// Open delete confirmation dialog
const openDeleteDialog = (client: Client) => {
    clientToDelete.value = client;
    isDeleteDialogOpen.value = true;
};

// Delete client
const deleteClient = () => {
    if (clientToDelete.value) {
        router.delete(route('clients.destroy', clientToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                // Remove the deleted client from the local state
                clients.value = clients.value.filter(c => c.id !== clientToDelete.value?.id);

                isDeleteDialogOpen.value = false; // Close the dialog
                clientToDelete.value = null;
            },
        });
    }
};
const isAdminOrManager = page.props.auth.user.roles.some((role: { name: string }) => ['admin', 'manager'].includes(role.name));

const tabs = [
    { label: 'Requests', href: route('clients-management.index') },
    { label: 'Reservation', href: route('receptionist.show-reservation') }
];
if (isAdminOrManager){
    tabs.push(    { label: 'All Clients', href: route('receptionist.all-clients') })
}
</script>

<template>
    <Head title="Clients" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">
            <TabsHeader title="Client Management" :tabs="tabs" />

            <!-- Clients Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Clients</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>Client Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>National ID</TableHead>
                                <TableHead>Phone Number</TableHead>
                                <TableHead class="w-32">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="clients.length === 0">
                                <TableCell colspan="6" class="h-24 text-center">
                                    No clients found.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="client in clients" :key="client.id">
                                <TableCell>{{ client.id }}</TableCell>
                                <TableCell>{{ client.user.name || 'N/A' }}</TableCell>
                                <TableCell>{{ client.user.email || 'N/A' }}</TableCell>
                                <TableCell>{{ client.national_id || 'N/A' }}</TableCell>
                                <TableCell>{{ client.phone_number || 'N/A' }}</TableCell>
                                <TableCell>
                                    <div class="flex gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="openClientModal(client)"
                                        >
                                            Edit
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="openDeleteDialog(client)"
                                        >
                                            Delete
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>

        <!-- Add/Edit Client Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>{{ isEditMode ? 'Edit Client' : 'Add Client' }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="space-y-2">
                        <Label>Name</Label>
                        <Input v-model="form.name" placeholder="Enter client name" />
                        <p v-if="page.props.errors.name" class="text-sm text-red-500">{{ page.props.errors.name }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label>Email</Label>
                        <Input v-model="form.email" type="email" placeholder="Enter client email" />
                        <p v-if="page.props.errors.email" class="text-sm text-red-500">{{ page.props.errors.email }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label>National ID</Label>
                        <Input v-model="form.national_id" placeholder="Enter national ID" />
                        <p v-if="page.props.errors.national_id" class="text-sm text-red-500">{{ page.props.errors.national_id }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label>Phone Number</Label>
                        <Input v-model="form.phone_number" placeholder="Enter phone number" />
                        <p v-if="page.props.errors.phone_number" class="text-sm text-red-500">{{ page.props.errors.phone_number }}</p>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                        <Button type="submit" :disabled="isLoading">
                            {{ isEditMode ? 'Save Changes' : 'Add Client' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Confirm Deletion</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete this client? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
                    <Button type="button" variant="destructive" @click="deleteClient">Delete</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
img {
    transition: transform 0.2s ease-in-out;
}

img:hover {
    transform: scale(1.05);
}
</style>
