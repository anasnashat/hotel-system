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
import { Head } from '@inertiajs/vue3';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Rooms', href: '/manager/manage-rooms' },
];

// Page props
const page = usePage();

// Room interface
interface Room {
    id: number;
    capacity: number;
    price: number;
    description: string;
    floor_id: number;
    floor_name: string;
    manager_name?: string;
    first_image_url?: string;
    images?: { id: number; url: string }[];
}

// Props
const { rooms } = defineProps<{ rooms: any }>();

// Modal and form state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentRoom = ref<Room | null>(null);
const form = ref({
    capacity: '',
    price: '',
    description: '',
    floor_id: '',
    images: [] as File[],
});

// Delete confirmation state
const isDeleteDialogOpen = ref(false);
const roomToDelete = ref<Room | null>(null);

// Loading state
const isLoading = ref(false);

const deleteImage = (imageId: number) => {
    if (confirm('Are you sure you want to delete this image?')) {
        router.delete(route('rooms.images.destroy', { room: currentRoom.value?.id, image: imageId }), {
            preserveScroll: true,
            onSuccess: () => {
                if (currentRoom.value) {
                    // Remove the deleted image from the current room's images
                    currentRoom.value.images = currentRoom.value.images.filter(img => img.id !== imageId);
                }
            },
        });
    }
};

// Handle image upload
const handleImageUpload = (event: Event) => {
    const files = (event.target as HTMLInputElement).files;
    if (files) {
        form.value.images = Array.from(files);
    }
};

// Open modal for adding a new room
const openAddModal = () => {
    isEditMode.value = false;
    form.value = { capacity: '', price: '', description: '', floor_id: '', images: [] };
    isModalOpen.value = true;
};

// Open modal for editing a room
const openEditModal = (room) => {
    console.log('Editing room:', room); // Debugging log
    console.log('Room floor_id:', room.floor_id); // Debugging log
    isEditMode.value = true;
    currentRoom.value = room;
    form.value = {
        capacity: room.capacity.toString(),
        price: (room.price / 100).toFixed(2),
        description: room.description,
        floor_id: room.floor_id, // Ensure floor_id is set
        images: []
    };
    console.log('Form floor_id:', form.value.floor_id); // Debugging log
    isModalOpen.value = true;
};

// Handle form submission
const submitForm = () => {
    isLoading.value = true;
    const formData = new FormData();

    // Ensure numerical values are properly formatted
    formData.append('capacity', form.value.capacity);
    formData.append('price', Math.round(parseFloat(form.value.price) * 100).toString()); // Handle decimal conversion
    formData.append('description', form.value.description);
    formData.append('floor_id', form.value.floor_id);

    // Append images only if they exist
    form.value.images.forEach((image, index) => {
        formData.append(`images[${index}]`, image);
    });

    // Add _method for Laravel form method spoofing
    if (isEditMode.value && currentRoom.value) {
        formData.append('_method', 'PUT');
    }

    const url = isEditMode.value && currentRoom.value
        ? route('rooms.update', currentRoom.value.id)
        : route('rooms.store');

    router.post(url, formData, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            isModalOpen.value = false;
            isLoading.value = false;
        },
        onError: (errors) => {
            isLoading.value = false;
            // Keep modal open to show errors
        }
    });
};
// Delete handling
const openDeleteDialog = (room: Room) => {
    roomToDelete.value = room;
    isDeleteDialogOpen.value = true;
};

const deleteRoom = () => {
    if (roomToDelete.value) {
        router.delete(route('rooms.destroy', roomToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                roomToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Manage Rooms" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Room Management</h1>
                <Button @click="openAddModal" class="gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Room
                </Button>
            </div>

            <!-- Rooms Table -->
            <div class="bg-white rounded-lg shadow-sm border">
                <Table class="min-w-full">
                    <TableHeader class="bg-gray-50">
                        <TableRow>
                            <TableHead class="w-24">Image</TableHead>
                            <TableHead>Capacity</TableHead>
                            <TableHead>Price</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Floor</TableHead>
                            <TableHead v-if="$page.props.auth.user.roles[0].name === 'admin'">Manager</TableHead>
                            <TableHead class="w-32">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="rooms.data.length === 0">
                            <TableCell :colspan="$page.props.auth.user.roles[0].name === 'admin' ? 7 : 6" class="py-12 text-center text-gray-500">
                                No rooms found
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="room in rooms.data" :key="room.id" class="hover:bg-gray-50 transition-colors">
                            <TableCell>
                                <img v-if="room.first_image_url" :src="room.first_image_url"
                                     class="w-16 h-16 rounded-lg object-cover border" alt="Room image">
                                <div v-else class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <span class="text-gray-400 text-sm">No image</span>
                                </div>
                            </TableCell>
                            <TableCell>{{ room.capacity }}</TableCell>
                            <TableCell>${{ (room.price / 100).toFixed(2) }}</TableCell>
                            <TableCell class="max-w-xs truncate">{{ room.description }}</TableCell>
                            <TableCell>{{ room.floor_name }}</TableCell>
                            <TableCell v-if="$page.props.auth.user.roles[0].name === 'admin'">{{ room.manager_name }}</TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" @click="openEditModal(room)">
                                        Edit
                                    </Button>
                                    <Button variant="destructive" size="sm" @click="openDeleteDialog(room)">
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <!-- Pagination component here -->
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle class="text-lg">{{ isEditMode ? 'Edit Room' : 'Create New Room' }}</DialogTitle>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="space-y-4">
                        <!-- Capacity -->
                        <div>
                            <Label>Capacity</Label>
                            <Input v-model="form.capacity" type="number"
                                   class="mt-1" placeholder="Enter room capacity" />
                            <p v-if="page.props.errors.capacity" class="text-red-500 text-sm mt-1">
                                {{ page.props.errors.capacity }}
                            </p>
                        </div>

                        <!-- Price -->
                        <div>
                            <Label>Price per night ($)</Label>
                            <Input v-model="form.price" type="number" step="0.01"
                                   class="mt-1" placeholder="Enter price in dollars" />
                            <p v-if="page.props.errors.price" class="text-red-500 text-sm mt-1">
                                {{ page.props.errors.price }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <Label>Description</Label>
                            <Input v-model="form.description"
                                   class="mt-1" placeholder="Enter room description" />
                            <p v-if="page.props.errors.description" class="text-red-500 text-sm mt-1">
                                {{ page.props.errors.description }}
                            </p>
                        </div>

                        <!-- Floor Selection -->
                        <div>
                            <Label>Floor</Label>
                            <select
                                v-model="form.floor_id"
                                class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Select Floor</option>
                                <option
                                    v-for="floor in $page.props.floors"
                                    :key="floor.id"
                                    :value="floor.id"
                                    :selected="isEditMode && form.floor_id === floor.id"
                                >
                                    {{ floor.name }}
                                </option>
                            </select>
                            <p v-if="page.props.errors.floor_id" class="text-red-500 text-sm mt-1">
                                {{ page.props.errors.floor_id }}
                            </p>
                        </div>

                        <!-- Existing Images -->
                        <!-- Existing Images -->
                        <div v-if="isEditMode && currentRoom?.images?.length">
                            <Label>Existing Images</Label>
                            <div class="mt-2 grid grid-cols-3 gap-4">
                                <div v-for="image in currentRoom.images" :key="image.id" class="relative">
                                    <img :src="image.url" alt="Room image" class="w-full h-24 object-cover rounded-lg border" />
                                    <button
                                        type="button"
                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                        @click="deleteImage(image.id)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <Label>Upload New Images</Label>
                            <div class="mt-1 flex items-center gap-4">
                                <Input id="images" type="file" multiple
                                       @change="handleImageUpload"
                                       class="w-full" />
                            </div>
                            <p class="text-gray-500 text-sm mt-2">
                                Upload up to 5 images (JPEG, PNG, JPG, GIF)
                            </p>
                            <p v-if="page.props.errors.images" class="text-red-500 text-sm mt-1">
                                {{ page.props.errors.images }}
                            </p>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="isModalOpen = false">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="isLoading">
                            <span v-if="isLoading">Processing...</span>
                            <span v-else>{{ isEditMode ? 'Save Changes' : 'Create Room' }}</span>
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle class="text-red-600">Confirm Deletion</DialogTitle>
                    <DialogDescription class="mt-2">
                        Are you sure you want to delete this room? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isDeleteDialogOpen = false">
                        Cancel
                    </Button>
                    <Button type="button" variant="destructive" @click="deleteRoom">
                        Confirm Delete
                    </Button>
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

select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1.25rem;
}
</style>
