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
    { title: 'Manage Floors', href: '/manager/manage-floors' },
];

// Page props
const page = usePage();

// Floor interface
interface Floor {
    id: number;
    name: string;
    number: number,
    created_by: { name: string };
}

// Props
const { floors } = defineProps<{ floors: any }>();

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentFloor = ref<Floor | null>(null);
const form = ref({ name: '' });

// Delete confirmation state
const isDeleteDialogOpen = ref(false);
const floorToDelete = ref<Floor | null>(null);

// Loading state
const isLoading = ref(false);

// Open modal for adding a new floor
const openAddModal = () => {
    isEditMode.value = false;
    form.value = { name: '' }; // Reset form
    isModalOpen.value = true;
};

// Open modal for editing a floor
const openEditModal = (floor: Floor) => {
    isEditMode.value = true;
    currentFloor.value = floor;
    form.value = { name: floor.name }; // Populate form with floor data
    isModalOpen.value = true;
};

// Handle form submission (add or update)
const submitForm = () => {
    isLoading.value = true;
    if (isEditMode.value && currentFloor.value) {
        // Update floor
        router.put(route('floors.update', currentFloor.value.id), form.value, {
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
        // Add new floor
        router.post(route('floors.store'), form.value, {
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
const openDeleteDialog = (floor: Floor) => {
    floorToDelete.value = floor;
    isDeleteDialogOpen.value = true;
};

// Delete floor
const deleteFloor = () => {
    if (floorToDelete.value) {
        router.delete(route('floors.destroy', floorToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleteDialogOpen.value = false; // Close the dialog
                floorToDelete.value = null;
            },
        });
    }
};

// Pagination
const handlePageChange = (page: number) => {
    router.get(route('floors.index', { page }), {}, { preserveScroll: true });
};

const tabs = [
    { label: 'Floors', href: route('floors.index') },
];

</script>

<template>
    <Head title="Manage Floors" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">

            <TabsHeader title="Manage Floors" :tabs="tabs" />

            <div class="flex justify-end mb-4">
                <Button @click="openAddModal" class="gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Floor
                </Button>
            </div>

            <!-- Floors Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Floors</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Number</TableHead>
                                <TableHead v-if="$page.props.auth.user.roles[0].name === 'admin'">Created By</TableHead>
                                <TableHead class="w-32">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="floors.data.length === 0">
                                <TableCell :colspan="$page.props.auth.user.roles[0].name === 'admin' ? 4 : 3" class="h-24 text-center">
                                    No floors found.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="floor in floors.data" :key="floor.id">
                                <TableCell>{{ floor.id }}</TableCell>
                                <TableCell>{{ floor.name }}</TableCell>
                                <TableCell>{{ floor.number }}</TableCell>
                                <TableCell v-if="$page.props.auth.user.roles[0].name === 'admin'">{{ floor.created_by.name }}</TableCell>
                                <TableCell v-if="page.props.auth.user.id === floor.created_by.id || page.props.auth.user.roles[0].name === 'admin'">
                                    <div class="flex gap-2">
                                        <Button variant="outline" size="sm" @click="openEditModal(floor)">
                                            Edit
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="openDeleteDialog(floor)">
                                            Delete
                                        </Button>
                                    </div>
                                </TableCell>
                                <TableCell v-else>
                                        <p class="text-gray-500 italic bg-gray-100 p-2 rounded-md border border-gray-300">
                                            You can't perform any actions on this Floor.
                                        </p>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <Pagination
                    v-if="floors.last_page > 1"
                    :total="floors.total"
                    :items-per-page="floors.per_page"
                    :sibling-count="1"
                    :default-page="floors.current_page"
                    show-edges
                    @update:page="handlePageChange"
                >
                    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst @click="handlePageChange(1)" :disabled="floors.current_page === 1" />
                        <PaginationPrev @click="handlePageChange(floors.current_page - 1)" :disabled="floors.current_page === 1" />

                        <template v-for="(item, index) in items" :key="index">
                            <PaginationListItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                as-child
                            >
                                <Button
                                    class="w-10 h-10 p-0"
                                    :variant="item.value === floors.current_page ? 'default' : 'outline'"
                                    @click="handlePageChange(item.value)"
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext @click="handlePageChange(floors.current_page + 1)" :disabled="floors.current_page === floors.last_page" />
                        <PaginationLast @click="handlePageChange(floors.last_page)" :disabled="floors.current_page === floors.last_page" />
                    </PaginationList>
                </Pagination>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>{{ isEditMode ? 'Edit Floor' : 'Create New Floor' }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="space-y-2">
                        <Label>Name</Label>
                        <Input v-model="form.name" placeholder="Enter floor name" />
                        <p v-if="page.props.errors.name" class="text-sm text-red-500">{{ page.props.errors.name }}</p>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                        <Button type="submit" :disabled="isLoading">
                            {{ isEditMode ? 'Save Changes' : 'Create Floor' }}
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
                        Are you sure you want to delete this floor? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
                    <Button type="button" variant="destructive" @click="deleteFloor">Delete</Button>
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
