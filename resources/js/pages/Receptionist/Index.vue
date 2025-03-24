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
    { title: 'Clients Requests', href: '/Client/requests' },
];

// Page props
const page = usePage();

// Request interface
interface Request {
    id: number;
    user: { name: string; email: string };
    national_id: string;
    phone_number: string;
    status: string; // Added status field
}

// Props
const { requests: initialRequests } = defineProps<{ requests: Request[] }>();

// Reactive requests array
const requests = ref<Request[]>(initialRequests);

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentRequest = ref<Request | null>(null);
const form = ref({ name: '', email: '', national_id: '', phone_number: '' });

// Delete confirmation state
const isDeleteDialogOpen = ref(false);
const requestToDelete = ref<Request | null>(null);

// Loading state
const isLoading = ref(false);

// Open modal for editing a request
const openEditModal = (request: Request) => {
    isEditMode.value = true;
    currentRequest.value = request;
    form.value = {
        name: request.user.name,
        email: request.user.email,
        national_id: request.national_id,
        phone_number: request.phone_number,
    };
    isModalOpen.value = true;
};

// Handle form submission (edit)
const submitForm = () => {
    isLoading.value = true;
    if (isEditMode.value && currentRequest.value) {
        // Update request
        router.put(route('clients-management.update', currentRequest.value.id), form.value, {
            preserveScroll: true,
            onSuccess: () => {
                // Update the local state
                const index = requests.value.findIndex(r => r.id === currentRequest.value?.id);
                if (index !== -1) {
                    requests.value[index] = {
                        ...requests.value[index],
                        user: {
                            ...requests.value[index].user,
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
    }
};

// Open delete confirmation dialog
const openDeleteDialog = (request: Request) => {
    requestToDelete.value = request;
    isDeleteDialogOpen.value = true;
};

// Delete request
const deleteRequest = () => {
    if (requestToDelete.value) {
        router.delete(route('clients-management.destroy', requestToDelete.value.user.id), {
            preserveScroll: true,
            onSuccess: () => {
                // Remove the deleted request from the local state
                requests.value = requests.value.filter(r => r.id !== requestToDelete.value?.id);

                isDeleteDialogOpen.value = false; // Close the dialog
                requestToDelete.value = null;
            },
        });
    }
};

// Approve request
const approveRequest = (client_id: number) => {
    router.post(route('client.approve'), { client_id }, {
        preserveScroll: true,
        onSuccess: () => {
            // Update the local state
            const requestIndex = requests.value.findIndex(r => r.id === client_id);
            if (requestIndex !== -1) {
                requests.value[requestIndex].status = 'Approved';
            }
        },
    });
};

const isAdminOrManager = page.props.auth.user.roles.some((role: { name: string }) => ['admin', 'manager'].includes(role.name));

const tabs = [
    { label: 'All Clients', href: route('receptionist.all-clients') },
    { label: 'Requests', href: route('clients-management.index') },
];
if (isAdminOrManager){
    tabs.push({ label: 'Reservation', href: route('receptionist.show-reservation') })
}
</script>

<template>
    <Head title="Receptionist Requests" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">
            <TabsHeader title="Client Management" :tabs="tabs" />

            <!-- Requests Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Requests</CardTitle>
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
                                <TableHead>Status</TableHead>
                                <TableHead class="w-32">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="requests.length === 0">
                                <TableCell colspan="7" class="h-24 text-center">
                                    No requests found.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="request in requests" :key="request.id">
                                <TableCell>{{ request.id }}</TableCell>
                                <TableCell>{{ request.user.name || 'N/A' }}</TableCell>
                                <TableCell>{{ request.user.email || 'N/A' }}</TableCell>
                                <TableCell>{{ request.national_id || 'N/A' }}</TableCell>
                                <TableCell>{{ request.phone_number || 'N/A' }}</TableCell>
                                <TableCell>
                                    <span :class="request.status === 'Approved' ? 'text-green-500' : 'text-yellow-500'">
                                        {{ request.status || 'Pending' }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <div class="flex gap-2">
                                        <Button
                                            v-if="request.status !== 'Approved'"
                                            variant="outline"
                                            size="sm"
                                            class="text-green-500"
                                            @click="approveRequest(request.id)"
                                        >
                                            Approve
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="openEditModal(request)"
                                        >
                                            Edit
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="openDeleteDialog(request)"
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

        <!-- Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>Edit Request</DialogTitle>
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
                            Save Changes
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
                        Are you sure you want to delete this request? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
                    <Button type="button" variant="destructive" @click="deleteRequest">Delete</Button>
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
