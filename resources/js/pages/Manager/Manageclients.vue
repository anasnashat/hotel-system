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
import { Head, Link } from '@inertiajs/vue3'; // Import Link from Inertia
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
} from '@/components/ui/pagination';
import TabsHeader from '@/components/TabsHeader.vue';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Clients', href: '/manager/manage-clients' },
];

// Page props
const page = usePage();
console.log(page.props);

// Client interface
interface Client {
    id: number;
    user: { name: string; email: string };
    is_approved: boolean;
}

// Props
const { clients } = defineProps<{ clients: any }>();

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentClient = ref<Client | null>(null);
const form = ref({ name: '', email: '' });

// Delete confirmation state
const isDeleteDialogOpen = ref(false);
const clientToDelete = ref<Client | null>(null);

// Loading state
const isLoading = ref(false);

// Open modal for adding a new client
const openAddModal = () => {
    isEditMode.value = false;
    form.value = { name: '', email: '' }; // Reset form
    isModalOpen.value = true;
};

// Open modal for editing a client
const openEditModal = (client: Client) => {
    isEditMode.value = true;
    currentClient.value = client;
    form.value = { name: client.user.name, email: client.user.email }; // Populate form with client data
    isModalOpen.value = true;
};

// Handle form submission (add or update)
const submitForm = () => {
    isLoading.value = true;
    if (isEditMode.value && currentClient.value) {
        // Update client
        router.put(route('manager.update-client', currentClient.value.id), form.value, {
            preserveScroll: true,
            onSuccess: () => {
                isModalOpen.value = false;
                isLoading.value = false;
            },
            onError: () => {
                isLoading.value = false;
            },
        });
    } else {
        // Add new client
        router.post(route('manager.store-client'), form.value, {
            preserveScroll: true,
            onSuccess: () => {
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
        router.delete(route('manager.delete-client', clientToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleteDialogOpen.value = false; // Close the dialog
                clientToDelete.value = null;
            },
        });
    }
};

// Approve client
const approveClient = (id: number) => {
    router.post(route('manager.approve-client'), { client_id: id }, {
        preserveScroll: true,
        onSuccess: () => {
            const clientIndex = clients.findIndex(c => c.id === id);
            if (clientIndex !== -1) clients[clientIndex].is_approved = true;
        },
    });
};

const tabs = [
    { label: 'All Clients', href: route('manager.manage-clients') },
    { label: 'Pending Clients', href: route('manager.manage-receptionists') },
];
</script>

<template>
    <Head title="Manage Clients" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">
            <!-- Title and Tabs -->
            <TabsHeader title="Client Management" :tabs="tabs" />

            <!-- Add Client Button -->
            <div class="flex justify-end mb-4">
                <Button @click="openAddModal" class="gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Client
                </Button>
            </div>

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
                                <TableHead>Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="w-32">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="clients.length === 0">
                                <TableCell colspan="5" class="h-24 text-center">
                                    No clients found.
                                </TableCell>
                            </TableRow>
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
                                    <div class="flex gap-2">
                                        <Button v-if="!client.is_approved" variant="outline" size="sm" @click="approveClient(client.id)">
                                            Approve
                                        </Button>
                                        <Button variant="outline" size="sm" @click="openEditModal(client)">
                                            Edit
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="openDeleteDialog(client)">
                                            Delete
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <Pagination
                    v-if="clients.last_page > 1"
                    :total="clients.total"
                    :items-per-page="clients.per_page"
                    :sibling-count="1"
                    :default-page="clients.current_page"
                    show-edges
                    @update:page="(page) => router.get(route('clients.index', { page }), {}, { preserveScroll: true })"
                >
                    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst @click="router.get(route('clients.index', { page: 1 }), {}, { preserveScroll: true })" :disabled="clients.current_page === 1" />
                        <PaginationPrev @click="router.get(route('clients.index', { page: clients.current_page - 1 }), {}, { preserveScroll: true })" :disabled="clients.current_page === 1" />

                        <template v-for="(item, index) in items" :key="index">
                            <PaginationListItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                as-child
                            >
                                <Button
                                    class="w-10 h-10 p-0"
                                    :variant="item.value === clients.current_page ? 'default' : 'outline'"
                                    @click="router.get(route('clients.index', { page: item.value }), {}, { preserveScroll: true })"
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext @click="router.get(route('clients.index', { page: clients.current_page + 1 }), {}, { preserveScroll: true })" :disabled="clients.current_page === clients.last_page" />
                        <PaginationLast @click="router.get(route('clients.index', { page: clients.last_page }), {}, { preserveScroll: true })" :disabled="clients.current_page === clients.last_page" />
                    </PaginationList>
                </Pagination>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>{{ isEditMode ? 'Edit Client' : 'Create New Client' }}</DialogTitle>
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
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                        <Button type="submit" :disabled="isLoading">
                            {{ isEditMode ? 'Save Changes' : 'Create Client' }}
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
