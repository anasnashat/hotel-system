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
    { title: 'Manage Receptionists', href: '/manager/manage-receptionists' },
];

// Page props
const page = usePage();
const isAdmin = page.props.auth.user.roles.some((role: { name: string }) => ['admin'].includes(role.name));

// Receptionist interface
interface Receptionist {
    id: number;
    name: string;
    email: string;
    is_banned: boolean;
    created_at: string;
    profile?: {
        created_by: {
            name: string;
        };
        national_id: string;
    };
}

// Props
const { receptionists: initialReceptionists } = defineProps<{ receptionists: any }>();

// Reactive receptionists array
const receptionists = ref<Receptionist[]>(initialReceptionists);

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentReceptionist = ref<Receptionist | null>(null);
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
const receptionistToDelete = ref<Receptionist | null>(null);

// Loading state
const isLoading = ref(false);

// Open modal for adding a new receptionist
const openAddModal = () => {
    isEditMode.value = false;
    form.value = { name: '', email: '', password: '', national_id: '', avatar_image: null }; // Reset form
    isModalOpen.value = true;
};

// Open modal for editing a receptionist
const openEditModal = (receptionist: Receptionist) => {
    isEditMode.value = true;
    currentReceptionist.value = receptionist;
    form.value = {
        name: receptionist.name,
        email: receptionist.email,
        password: '', // Password is not populated for security reasons
        national_id: receptionist.profile?.national_id || '',
        avatar_image: null, // Avatar image is not populated (can be handled separately if needed)
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
    if (form.value.avatar_image) {
        formData.append('avatar_image', form.value.avatar_image);
    }

    if (isEditMode.value && currentReceptionist.value) {
        formData.append('_method', 'PUT'); // Use PUT for updates
    }

    const url = isEditMode.value && currentReceptionist.value
        ? route('manager.update-receptionist', currentReceptionist.value.id)
        : route('manager.store-receptionist');

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
const openDeleteDialog = (receptionist: Receptionist) => {
    receptionistToDelete.value = receptionist;
    isDeleteDialogOpen.value = true;
};

const deleteReceptionist = () => {
    if (receptionistToDelete.value) {
        router.delete(route('manager.delete-receptionist', receptionistToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                receptionistToDelete.value = null;
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

// Pagination
const handlePageChange = (page: number) => {
    router.get(route('manager.manage-receptionists', { page }), {}, { preserveScroll: true });
};

const tabs = [
    { label: 'Receptionists', href: route('manager.manage-receptionists') },
];
</script>

<template>
    <Head title="Manage Receptionists" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">
            <TabsHeader title="Manage Receptionists" :tabs="tabs" />

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
                                <TableHead>Created At</TableHead>
                                <TableHead v-if="isAdmin">Created By</TableHead>
                                <TableHead class="w-32">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="receptionists.length === 0">
                                <TableCell colspan="8" class="h-24 text-center">
                                    No receptionists found.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="receptionist in receptionists" :key="receptionist.id">
                                <TableCell>{{ receptionist.id }}</TableCell>
                                <TableCell>{{ receptionist.name }}</TableCell>
                                <TableCell>{{ receptionist.email }}</TableCell>
                                <TableCell>{{ receptionist.is_banned ? 'Banned' : 'Active' }}</TableCell>
                                <TableCell>{{ new Date(receptionist.created_at).toLocaleDateString() }}</TableCell>
                                <TableCell v-if="isAdmin">{{ receptionist.profile?.created_by?.name }}</TableCell>
                                <TableCell v-if="isAdmin || page.props.auth.user.id === receptionist.profile?.created_by?.id">
                                    <div class="flex gap-2">
                                        <Button
                                            v-if="isAdmin || page.props.auth.user.id === receptionist.profile?.created_by?.id"
                                            variant="outline"
                                            size="sm"
                                            @click="openEditModal(receptionist)"
                                        >
                                            Edit
                                        </Button>
                                        <Button
                                            v-if="isAdmin || page.props.auth.user.id === receptionist.profile?.created_by?.id"
                                            variant="destructive"
                                            size="sm"
                                            @click="openDeleteDialog(receptionist)"
                                        >
                                            Delete
                                        </Button>
                                        <Button
                                            v-if="!receptionist.is_banned"
                                            variant="outline"
                                            size="sm"
                                            class="text-yellow-500"
                                            @click="banReceptionist(receptionist.id)"
                                        >
                                            Ban
                                        </Button>
                                        <Button
                                            v-if="receptionist.is_banned"
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
                    v-if="initialReceptionists.last_page > 1"
                    :total="initialReceptionists.total"
                    :items-per-page="initialReceptionists.per_page"
                    :sibling-count="1"
                    :default-page="initialReceptionists.current_page"
                    show-edges
                    @update:page="handlePageChange"
                >
                    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst @click="handlePageChange(1)" :disabled="initialReceptionists.current_page === 1" />
                        <PaginationPrev @click="handlePageChange(initialReceptionists.current_page - 1)" :disabled="initialReceptionists.current_page === 1" />

                        <template v-for="(item, index) in items" :key="index">
                            <PaginationListItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                as-child
                            >
                                <Button
                                    class="w-10 h-10 p-0"
                                    :variant="item.value === initialReceptionists.current_page ? 'default' : 'outline'"
                                    @click="handlePageChange(item.value)"
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext @click="handlePageChange(initialReceptionists.current_page + 1)" :disabled="initialReceptionists.current_page === initialReceptionists.last_page" />
                        <PaginationLast @click="handlePageChange(initialReceptionists.last_page)" :disabled="initialReceptionists.current_page === initialReceptionists.last_page" />
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
                <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">
                    <!-- Name Field -->
                    <div class="space-y-2">
                        <Label>Name</Label>
                        <Input v-model="form.name" placeholder="Enter receptionist name" />
                        <p v-if="page.props.errors.name" class="text-sm text-red-500">{{ page.props.errors.name }}</p>
                    </div>

                    <!-- Email Field -->
                    <div class="space-y-2">
                        <Label>Email</Label>
                        <Input v-model="form.email" type="email" placeholder="Enter receptionist email" />
                        <p v-if="page.props.errors.email" class="text-sm text-red-500">{{ page.props.errors.email }}</p>
                    </div>

                    <!-- Password Field -->
                    <div v-if="!isEditMode" class="space-y-2">
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
