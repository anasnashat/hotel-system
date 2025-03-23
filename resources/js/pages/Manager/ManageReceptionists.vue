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
}

// Props
const { receptionists } = defineProps<{ receptionists: any }>();

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentReceptionist = ref<Receptionist | null>(null);
const form = ref({ name: '', email: '', password: '' });

// Delete confirmation state
const isDeleteDialogOpen = ref(false);
const receptionistToDelete = ref<Receptionist | null>(null);

// Loading state
const isLoading = ref(false);

// Open modal for adding a new receptionist
const openAddModal = () => {
    isEditMode.value = false;
    form.value = { name: '', email: '', password: '' }; // Reset form
    isModalOpen.value = true;
};

// Open modal for editing a receptionist
const openEditModal = (receptionist: Receptionist) => {
    isEditMode.value = true;
    currentReceptionist.value = receptionist;
    form.value = { name: receptionist.name, email: receptionist.email, password: '' }; // Populate form
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
        router.post(route('manager.ban-receptionist', id), {  }, {
            preserveScroll: true,
            onSuccess: () => {
                const receptionistIndex = receptionists.findIndex(r => r.id === id);
                if (receptionistIndex !== -1) receptionists[receptionistIndex].is_banned = true;
            },
        });
    }
};

// Unban receptionist
const unbanReceptionist = (id: number) => {
    console.log('Unban', id);
    if (confirm('Are you sure you want to unban this receptionist?')) {

router.post(route('manager.unban-receptionist', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
        const receptionistIndex = receptionists.findIndex(r => r.id === id);
        if (receptionistIndex !== -1) receptionists[receptionistIndex].is_banned = false;
    }
});
    }
};

const tabs = [
    { label: 'All Clients', href: route('manager.manage-clients') },
    { label: 'Pending Clients', href: route('manager.manage-receptionists') },
];
</script>

<template>
    <Head title="Manage Receptionists" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">
            <TabsHeader title="Client Management" :tabs="tabs" />

            <div class="flex justify-end mb-4">
                <Button @click="openAddModal" class="gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Receptionist
                </Button>
            </div>

            <!-- Receptionists Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Receptionists</CardTitle>
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
                            <TableRow v-if="receptionists.length === 0">
                                <TableCell colspan="5" class="h-24 text-center">
                                    No receptionists found.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="receptionist in receptionists" :key="receptionist.id">
                                <TableCell>{{ receptionist.id }}</TableCell>
                                <TableCell>{{ receptionist.name }}</TableCell>
                                <TableCell>{{ receptionist.email }}</TableCell>
                                <TableCell>
                                    <span :class="receptionist.is_banned ? 'text-red-500' : 'text-green-500'">
                                        {{ receptionist.is_banned ? 'Banned' : 'Active' }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <div class="flex gap-2">
                                        <Button variant="outline" size="sm" @click="openEditModal(receptionist)">
                                            Edit
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="openDeleteDialog(receptionist)">
                                            Delete
                                        </Button>
                                        <Button
                                            v-if="!receptionist.is_banned"
                                            variant="outline"
                                            size="sm"
                                            class="text-yellow-500"
                                            @click="banReceptionist(receptionist)"
                                        >
                                            Ban
                                        </Button>
                                        <Button
                                            v-else
                                            variant="outline"
                                            size="sm"
                                            class="text-green-500"
                                            @click="unbanReceptionist(receptionist.id)"
                                        >
                                            Unban
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
                    v-if="receptionists.last_page > 1"
                    :total="receptionists.total"
                    :items-per-page="receptionists.per_page"
                    :sibling-count="1"
                    :default-page="receptionists.current_page"
                    show-edges
                    @update:page="(page) => router.get(route('receptionists.index', { page }), {}, { preserveScroll: true })"
                >
                    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst @click="router.get(route('receptionists.index', { page: 1 }), {}, { preserveScroll: true })" :disabled="receptionists.current_page === 1" />
                        <PaginationPrev @click="router.get(route('receptionists.index', { page: receptionists.current_page - 1 }), {}, { preserveScroll: true })" :disabled="receptionists.current_page === 1" />

                        <template v-for="(item, index) in items" :key="index">
                            <PaginationListItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                as-child
                            >
                                <Button
                                    class="w-10 h-10 p-0"
                                    :variant="item.value === receptionists.current_page ? 'default' : 'outline'"
                                    @click="router.get(route('receptionists.index', { page: item.value }), {}, { preserveScroll: true })"
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext @click="router.get(route('receptionists.index', { page: receptionists.current_page + 1 }), {}, { preserveScroll: true })" :disabled="receptionists.current_page === receptionists.last_page" />
                        <PaginationLast @click="router.get(route('receptionists.index', { page: receptionists.last_page }), {}, { preserveScroll: true })" :disabled="receptionists.current_page === receptionists.last_page" />
                    </PaginationList>
                </Pagination>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>{{ isEditMode ? 'Edit Receptionist' : 'Create New Receptionist' }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="space-y-2">
                        <Label>Name</Label>
                        <Input v-model="form.name" placeholder="Enter receptionist name" />
                        <p v-if="page.props.errors.name" class="text-sm text-red-500">{{ page.props.errors.name }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label>Email</Label>
                        <Input v-model="form.email" type="email" placeholder="Enter receptionist email" />
                        <p v-if="page.props.errors.email" class="text-sm text-red-500">{{ page.props.errors.email }}</p>
                    </div>
                    <div v-if="!isEditMode" class="space-y-2">
                        <Label>Password</Label>
                        <Input v-model="form.password" type="password" placeholder="Enter password" />
                        <p v-if="page.props.errors.password" class="text-sm text-red-500">{{ page.props.errors.password }}</p>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                        <Button type="submit" :disabled="isLoading">
                            {{ isEditMode ? 'Save Changes' : 'Create Receptionist' }}
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
                        Are you sure you want to delete this receptionist? This action cannot be undone.
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

<style scoped>
img {
    transition: transform 0.2s ease-in-out;
}

img:hover {
    transform: scale(1.05);
}
</style>
