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
    PaginationEllipsis, PaginationFirst, PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext, PaginationPrev
} from '@/components/ui/pagination';
import TabsHeader from '@/components/TabsHeader.vue';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Managers', href: '/admin/manage-managers' },
];

// Page props
const page = usePage();

// Manager interface
interface Manager {
    id: number;
    name: string;
    email: string;
    national_id: string;
    avatar_image?: string;
    created_at: string;
}

// Props
const { managers } = defineProps<{ managers: any }>();

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentManager = ref<Manager | null>(null);
const form = ref({
    name: '',
    email: '',
    password: '',
    national_id: '',
    avatar_image: null as File | null,
});

// Handle file upload
const handleFileUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        form.value.avatar_image = input.files[0]; // Store the selected file
    }
};

// Delete confirmation state
const isDeleteDialogOpen = ref(false);
const managerToDelete = ref<Manager | null>(null);

// Loading state
const isLoading = ref(false);

// Open modal for adding a new manager
const openAddModal = () => {
    isEditMode.value = false;
    form.value = { name: '', email: '', password: '', national_id: '', avatar_image: null }; // Reset form
    isModalOpen.value = true;
};

// Open modal for editing a manager
const openEditModal = (manager: Manager) => {
    isEditMode.value = true;
    currentManager.value = manager;
    form.value = {
        name: manager.name,
        email: manager.email,
        password: '', // Password is not populated for security reasons
        national_id: manager.profile.national_id,
        avatar_image: manager.avatar_image || null, 
    };
    isModalOpen.value = true;
};

// Handle form submission (add or update)
const submitForm = () => {
    isLoading.value = true;

    // Create FormData object
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('email', form.value.email);
    formData.append('password', form.value.password);
    formData.append('national_id', form.value.national_id);
    if (form.value.avatar_image instanceof File) {
        formData.append('avatar_image', form.value.avatar_image);
    }

    if (isEditMode.value && currentManager.value) {
        formData.append('_method', 'PUT'); // Use PUT for updates
    }

    const url = isEditMode.value && currentManager.value
        ? route('managers.update', currentManager.value.id)
        : route('managers.store');

    router.post(url, formData, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            isModalOpen.value = false;
            isLoading.value = false;
        },
        onError: () => {
            isLoading.value = false;
        },
    });
};

// Delete handling
const openDeleteDialog = (manager: Manager) => {
    managerToDelete.value = manager;
    isDeleteDialogOpen.value = true;
};

const deleteManager = () => {
    if (managerToDelete.value) {
        router.delete(route('managers.destroy', managerToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                managerToDelete.value = null;
            },
        });
    }
};

// Pagination
const handlePageChange = (page: number) => {
    router.get(route('managers.index', { page }), {}, { preserveScroll: true });
};

const tabs = [
    { label: 'Managers', href: route('managers.index') },
];
</script>

<template>
    <Head title="Manage Managers" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">
            <TabsHeader title="Manage Managers" :tabs="tabs" />

            <div class="flex justify-end mb-4">
                <Button @click="openAddModal" class="gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Manager
                </Button>
            </div>

            <!-- Managers Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Managers</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Image</TableHead>
                                <TableHead>ID</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>National ID</TableHead>
                                <TableHead>Created At</TableHead>
                                <TableHead class="w-32">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="managers.data.length === 0">
                                <TableCell colspan="6" class="h-24 text-center">
                                    No managers found.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="manager in managers.data" :key="manager.id">
                                <TableCell>
                                    <img 
                                        :src="manager.avatar_image? `/storage/${manager.avatar_image}` : '/default-avatar-image.png'"
                                        alt="Avatar" 
                                        class="w-20 h-20 rounded-full object-cover border" />
                                    
                                </TableCell>
                                <TableCell>{{ manager.id }}</TableCell>
                                <TableCell>{{ manager.name }}</TableCell>
                                <TableCell>{{ manager.email }}</TableCell>
                                <TableCell>{{ manager.profile.national_id }}</TableCell>
                                <TableCell>{{ new Date(manager.created_at).toLocaleDateString() }}</TableCell>
                                <TableCell>
                                    <div class="flex gap-2">
                                        <Button variant="outline" size="sm" @click="openEditModal(manager)">
                                            Edit
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="openDeleteDialog(manager)">
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
                    v-if="managers.last_page > 1"
                    :total="managers.total"
                    :items-per-page="managers.per_page"
                    :sibling-count="1"
                    :default-page="managers.current_page"
                    show-edges
                    @update:page="handlePageChange"
                >
                    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst @click="handlePageChange(1)" :disabled="managers.current_page === 1" />
                        <PaginationPrev @click="handlePageChange(managers.current_page - 1)" :disabled="managers.current_page === 1" />

                        <template v-for="(item, index) in items" :key="index">
                            <PaginationListItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                as-child
                            >
                                <Button
                                    class="w-10 h-10 p-0"
                                    :variant="item.value === managers.current_page ? 'default' : 'outline'"
                                    @click="handlePageChange(item.value)"
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext @click="handlePageChange(managers.current_page + 1)" :disabled="managers.current_page === managers.last_page" />
                        <PaginationLast @click="handlePageChange(managers.last_page)" :disabled="managers.current_page === managers.last_page" />
                    </PaginationList>
                </Pagination>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>{{ isEditMode ? 'Edit Manager' : 'Create New Manager' }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">
                    <!-- Name Field -->
                    <div class="space-y-2">
                        <Label>Name</Label>
                        <Input v-model="form.name" placeholder="Enter manager name" />
                        <p v-if="page.props.errors.name" class="text-sm text-red-500">{{ page.props.errors.name }}</p>
                    </div>

                    <!-- Email Field -->
                    <div class="space-y-2">
                        <Label>Email</Label>
                        <Input v-model="form.email" type="email" placeholder="Enter manager email" />
                        <p v-if="page.props.errors.email" class="text-sm text-red-500">{{ page.props.errors.email }}</p>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <Label>Password</Label>
                        <Input v-model="form.password" type="password" placeholder="Enter password" />
                        <p v-if="page.props.errors.password" class="text-sm text-red-500">{{ page.props.errors.password }}</p>
                    </div>

                    <!-- National ID Field -->
                    <div class="space-y-2">
                        <Label>National ID</Label>
                        <Input v-model="form.national_id" placeholder="Enter national ID" />
                        <p v-if="page.props.errors.national_id" class="text-sm text-red-500">{{ page.props.errors.national_id }}</p>
                    </div>

                    <!-- Avatar Image Field -->
                    <div class="space-y-2">
                        <Label>Avatar Image</Label>
                        <Input type="file" @change="handleFileUpload" accept="image/*" />
                        <p v-if="page.props.errors.avatar_image" class="text-sm text-red-500">{{ page.props.errors.avatar_image }}</p>
                    </div>

                    <!-- Form Actions -->
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                        <Button type="submit" :disabled="isLoading">
                            {{ isEditMode ? 'Save Changes' : 'Create Manager' }}
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
                        Are you sure you want to delete this manager? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
                    <Button type="button" variant="destructive" @click="deleteManager">Delete</Button>
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
