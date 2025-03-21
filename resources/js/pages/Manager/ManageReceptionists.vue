<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
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

// Receptionist interface
interface Receptionist {
    id: number;
    name: string;
    email: string;
    is_banned: boolean;
}

// Props
const { receptionists: initialReceptionists } = defineProps<{ receptionists: Receptionist[] }>();

// Reactive receptionists array
const receptionists = ref<Receptionist[]>(initialReceptionists);

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentReceptionist = ref<Receptionist | null>(null);
const form = ref({ name: '', email: '' });

// Delete confirmation state
const isDeleteDialogOpen = ref(false);
const receptionistToDelete = ref<Receptionist | null>(null);

// Loading state
const isLoading = ref(false);

// Open modal for adding a new receptionist
const openAddModal = () => {
    isEditMode.value = false;
    form.value = { name: '', email: '' }; // Reset form
    isModalOpen.value = true;
};

// Open modal for editing a receptionist
const openEditModal = (receptionist: Receptionist) => {
    isEditMode.value = true;
    currentReceptionist.value = receptionist;
    form.value = { name: receptionist.name, email: receptionist.email }; // Populate form
    isModalOpen.value = true;
};

// Handle form submission (add or update)
const submitForm = () => {
    isLoading.value = true;
    if (isEditMode.value && currentReceptionist.value) {
        // Update receptionist
        router.put(route('manager.update-receptionist', currentReceptionist.value.id), form.value, {
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
        // Add new receptionist
        router.post(route('manager.store-receptionist'), form.value, {
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
const openDeleteDialog = (receptionist: Receptionist) => {
    receptionistToDelete.value = receptionist;
    isDeleteDialogOpen.value = true;
};

// Delete receptionist
const deleteReceptionist = () => {
    if (receptionistToDelete.value) {
        router.delete(route('manager.delete-receptionist', receptionistToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleteDialogOpen.value = false; // Close the dialog
                receptionistToDelete.value = null;
            },
        });
    }
};

// Ban receptionist
const banReceptionist = (id: number) => {
    if (confirm('Are you sure you want to ban this receptionist?')) {
        router.post(route('manager.ban-receptionist', id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Update the receptionists array reactively
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
        router.post(route('manager.unban-receptionist', id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Update the receptionists array reactively
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
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="receptionists.length">
                            <TableRow v-for="receptionist in receptionists" :key="receptionist.id">
                                <TableCell>{{ receptionist.id }}</TableCell>
                                <TableCell>{{ receptionist.name }}</TableCell>
                                <TableCell>{{ receptionist.email }}</TableCell>
                                <TableCell>{{ receptionist.is_banned ? 'Banned' : 'Active' }}</TableCell>
                                <TableCell>
                                    <Button
                                        variant="outline"
                                        class="text-blue-500 mr-2"
                                        @click="openEditModal(receptionist)"
                                    >
                                        Edit
                                    </Button>
                                    <Button
                                        variant="outline"
                                        class="text-red-500 mr-2"
                                        @click="openDeleteDialog(receptionist)"
                                    >
                                        Delete
                                    </Button>
                                    <Button
                                        v-if="!receptionist.is_banned"
                                        variant="outline"
                                        class="text-yellow-500"
                                        @click="banReceptionist(receptionist.id)"
                                    >
                                        Ban
                                    </Button>
                                    <Button
                                        v-else
                                        variant="outline"
                                        class="text-green-500"
                                        @click="unbanReceptionist(receptionist.id)"
                                    >
                                        Unban
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </template>
                        <TableRow v-else>
                            <TableCell colspan="5" class="h-24 text-center">No receptionists found.</TableCell>
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
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right">Name</Label>
                            <Input id="name" v-model="form.name" class="col-span-3" />
                            <!-- Display validation error for name -->
                            <p v-if="page.props.errors.name" class="col-span-4 text-sm text-red-500">
                                {{ page.props.errors.name }}
                            </p>
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="email" class="text-right">Email</Label>
                            <Input id="email" v-model="form.email" type="email" class="col-span-3" />
                            <!-- Display validation error for email -->
                            <p v-if="page.props.errors.email" class="col-span-4 text-sm text-red-500">
                                {{ page.props.errors.email }}
                            </p>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="submit" :disabled="isLoading">{{ isEditMode ? 'Update' : 'Add' }}</Button>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Are you sure?</DialogTitle>
                    <DialogDescription>
                        This action cannot be undone. This will permanently delete the receptionist.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
                    <Button type="button" variant="destructive" @click="deleteReceptionist">Delete</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
