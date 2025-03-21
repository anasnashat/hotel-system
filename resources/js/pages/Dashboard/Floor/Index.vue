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
</script>

<template>
    <Head title="Manage Floors" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6">
            <h1 class="text-3xl font-bold mb-6 text-center">Manage Floors</h1>
            <div class="flex justify-end mb-4">
                <Button @click="openAddModal">Add New Floor</Button>
            </div>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Created By</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="floors.data.length">
                            <TableRow v-for="floor in floors.data" :key="floor.id">
                                <TableCell>{{ floor.id }}</TableCell>
                                <TableCell>{{ floor.name }}</TableCell>
                                <TableCell>{{ floor.created_by.name }}</TableCell>
                                <TableCell>
                                    <Button
                                        variant="outline"
                                        class="text-blue-500 mr-2"
                                        @click="openEditModal(floor)"
                                    >
                                        Edit
                                    </Button>
                                    <Button
                                        variant="outline"
                                        class="text-red-500"
                                        @click="openDeleteDialog(floor)"
                                    >
                                        Delete
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </template>
                        <TableRow v-else>
                            <TableCell colspan="4" class="h-24 text-center">No floors found.</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <Pagination
                    v-if="floors.next_page_url || floors.prev_page_url"
                    v-slot="{ page }"
                    :total="floors.total"
                    :items-per-page="floors.per_page"
                    :sibling-count="1"
                    :default-page="floors.current_page"
                    show-edges
                >
                    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst :href="floors.first_page_url" />
                        <PaginationPrev :href="floors.prev_page_url" />

                        <template v-for="(item, index) in items">
                            <PaginationListItem
                                v-if="item.type === 'page'"
                                :key="index"
                                :value="item.value"
                                as-child
                            >
                                <Button
                                    class="w-10 h-10 p-0"
                                    :variant="item.value === page ? 'default' : 'outline'"
                                    :href="floors.path + '?page=' + item.value"
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext :href="floors.next_page_url" />
                        <PaginationLast :href="floors.last_page_url" />
                    </PaginationList>
                </Pagination>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ isEditMode ? 'Edit Floor' : 'Add New Floor' }}</DialogTitle>
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
                        This action cannot be undone. This will permanently delete the floor.
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
