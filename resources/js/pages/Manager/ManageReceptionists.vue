

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Receptionists', href: '/manager/manage-receptionists' },
];

// Page props
const page = usePage();
console.log(page.props);

// Receptionist interface
interface Receptionist {
    id: number;
    name: string;
    email: string;
    is_banned: boolean;
    created_at: string; // Added this line
}

// Props
const { receptionists: initialReceptionists } = defineProps<{ receptionists: Receptionist[] }>();

// Reactive receptionists array
const receptionists = ref<Receptionist[]>(initialReceptionists);

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentReceptionist = ref<Receptionist | null>(null);
const form = ref<{ name: string; email: string; password?: string }>({ name: '', email: '' });

// Delete confirmation state
const isDeleteDialogOpen = ref(false);

// Loading state
const isLoading = ref(false);

// Open modal for adding a new receptionist
const openAddModal = () => {
    isEditMode.value = false;
    form.value = { name: '', email: '', password: '' }; // Include password field when adding
    isModalOpen.value = true;
};

// Open modal for editing a receptionist (without password)
const openEditModal = (receptionist: Receptionist) => {
    isEditMode.value = true;
    currentReceptionist.value = receptionist;
    form.value = { name: receptionist.name, email: receptionist.email }; // No password field
    isModalOpen.value = true;
};

// Handle form submission (add or update)
const submitForm = () => {
    isLoading.value = true;
    if (isEditMode.value && currentReceptionist.value) {
        // Update receptionist
        router.put(route('manager.update-receptionist', { id: currentReceptionist.value.id }), form.value, {
            preserveScroll: true,
            onSuccess: () => {
                isModalOpen.value = false;
                isLoading.value = false;
                router.reload({ only: ['receptionists'] }); // Refresh list
            },
            onError: () => {
                isLoading.value = false;
            },
        });
    } else {
        // Add new receptionist
        router.post(route('manager.store-receptionist'), form.value, {
            preserveScroll: true,
            onSuccess: () => {
                isModalOpen.value = false;
                isLoading.value = false;
                router.reload({ only: ['receptionists'] }); // Refresh list after adding
            },
            onError: () => {
                isLoading.value = false;
            },
        });
    }
};

// Delete receptionist (Updated to match browser confirmation dialog)
const deleteReceptionist = (receptionist: Receptionist) => {
    if (window.confirm("Are you sure you want to delete this client?")) {
        router.delete(route('manager.delete-receptionist', { id: receptionist.id }), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['receptionists'] }); // Refresh the list
            },
        });
    }
};

// Ban receptionist
const banReceptionist = (id: number) => {
    if (confirm('Are you sure you want to ban this receptionist?')) {
        router.post(route('manager.ban-receptionist', { id }), {}, {
            preserveScroll: true,
            onSuccess: () => {
                receptionists.value = receptionists.value.map((r) =>
                    r.id === id ? { ...r, is_banned: true } : r
                );
            },
        });
    }
};

// Unban receptionist
const unbanReceptionist = (id: number) => {
    if (confirm('Are you sure you want to unban this receptionist?')) {
        router.post(route('manager.unban-receptionist', { id }), {}, {
            preserveScroll: true,
            onSuccess: () => {
                receptionists.value = receptionists.value.map((r) =>
                    r.id === id ? { ...r, is_banned: false } : r
                );
            },
        });
    }
};
</script>

<template>
    <Head title="Manage Receptionists" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6">
            <h1 class="text-3xl font-bold mb-6 text-center">Manage Receptionists</h1>
            <div class="flex justify-end mb-4">
                <Button @click="openAddModal">Add New Receptionist</Button>
            </div>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Created At</TableHead> 
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="receptionist in receptionists" :key="receptionist.id">
                            <TableCell>{{ receptionist.id }}</TableCell>
                            <TableCell>{{ receptionist.name }}</TableCell>
                            <TableCell>{{ receptionist.email }}</TableCell>
                            <TableCell>{{ receptionist.is_banned ? 'Banned' : 'Active' }}</TableCell>
                            <TableCell>{{ new Date(receptionist.created_at).toLocaleDateString() }}</TableCell> <!-- Added this line -->
                            <TableCell>
                                <Button variant="outline" class="text-blue-500 mr-2" @click="openEditModal(receptionist)">
                                    Edit
                                </Button>
                                <Button variant="outline" class="text-red-500 mr-2" @click="deleteReceptionist(receptionist)">
                                    Delete
                                </Button>
                                <Button v-if="!receptionist.is_banned" variant="outline" class="text-yellow-500" @click="banReceptionist(receptionist.id)">
                                    Ban
                                </Button>
                                <Button v-else variant="outline" class="text-green-500" @click="unbanReceptionist(receptionist.id)">
                                    Unban
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ isEditMode ? 'Edit Receptionist' : 'Add New Receptionist' }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm">
                    <div class="space-y-4"> 
                        <div>
                            <Label>Name</Label>
                            <Input v-model="form.name" required />
                        </div>
                        <div>
                            <Label>Email</Label>
                            <Input v-model="form.email" type="email" required />
                        </div>
                        <!-- Show password field only when adding a new receptionist -->
                        <div v-if="!isEditMode">
                            <Label>Password</Label>
                            <Input v-model="form.password" type="password" required />
                        </div>
                    </div>
                    <DialogFooter class="mt-6">
                        <Button type="submit">{{ isEditMode ? 'Update' : 'Add' }}</Button>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

