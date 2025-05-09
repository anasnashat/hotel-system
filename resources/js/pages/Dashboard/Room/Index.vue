<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
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
    is_available: boolean;
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
    is_available: false,
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
    form.value = { capacity: '', price: '', description: '', is_available: false ,floor_id: '', images: [] };
    isModalOpen.value = true;
};

// Open modal for editing a room
const openEditModal = (room: Room) => {
    isEditMode.value = true;
    currentRoom.value = room;
    form.value = {
        capacity: room.capacity.toString(),
        price: (room.price / 100).toFixed(2),
        description: room.description,
        floor_id: room.floor_id.toString(),
        images: [],
        is_available: room.is_available,

    };
    isModalOpen.value = true;
};

// Handle form submission
const submitForm = () => {
    isLoading.value = true;
    const formData = new FormData();

    formData.append('capacity', form.value.capacity);
    formData.append('price', Math.round(parseFloat(form.value.price)).toString());
    formData.append('description', form.value.description);
    formData.append('floor_id', form.value.floor_id);
formData.append('is_available', form.value.is_available ? '1' : '0');    // formData.append('is_available', form.value.is_available.toString());


    form.value.images.forEach((image, index) => {
        formData.append(`images[${index}]`, image);
    });

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
        onError: () => {
            isLoading.value = false;
        },
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

const isAdmin = () => {
  return page.props.auth.user.roles.includes('admin')
}
console.log("isAdmin", isAdmin());
// Pagination
const handlePageChange = (page: number) => {
    router.get(route('rooms.index', { page }), {}, { preserveScroll: true });
};

const tabs = [
    { label: 'Rooms', href: route('rooms.index') },
];

</script>

<template>
    <Head title="Manage Rooms" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">

            <TabsHeader title="Manage Rooms" :tabs="tabs" />

            <div class="flex justify-end mb-4">
                <Button @click="openAddModal" class="gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Room
                </Button>
            </div>

            <!-- Rooms Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Rooms</CardTitle>
                </CardHeader>
                <CardContent  >
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-24">Image</TableHead>
                                <TableHead>Capacity</TableHead>
                                <TableHead>Price</TableHead>
                                <TableHead>Description</TableHead>
                                <TableHead>Floor</TableHead>
                                <TableHead v-if="isAdmin()">Manager</TableHead>
                                <TableHead class="w-32">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="rooms.data.length === 0">
                                <TableCell :colspan="isAdmin() ? 7 : 6" class="h-24 text-center">
                                    No rooms found.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="room in rooms.data" :key="room.id">
                                <TableCell>
                                    <img v-if="room.first_image_url" :src="room.first_image_url" class="w-16 h-16 rounded-lg object-cover border" alt="Room image" />
                                    <div v-else class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-gray-400 dark:text-gray-300 text-sm">No image</span>
                                    </div>
                                </TableCell>
                                <TableCell>{{ room.capacity }}</TableCell>
                                <TableCell>${{ (room.price / 100).toFixed(2) }}</TableCell>
                                <TableCell class="max-w-xs truncate">{{ room.description }}</TableCell>
                                <TableCell>{{ room.floor_name }}</TableCell>
                                <TableCell v-if="isAdmin()">{{ room.manager_name }}</TableCell>

                                <TableCell v-if="room.manager_id === $page.props.auth.user.id || isAdmin()">
                                    <div class="flex gap-2">
                                        <Button variant="outline" size="sm" @click="openEditModal(room)">
                                            Edit
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="openDeleteDialog(room)">
                                            Delete
                                        </Button>
                                    </div>
                                </TableCell>
                                <TableCell v-else>
                                    <div class="flex gap-2">
                                        <p class="text-gray-500 italic bg-gray-100 p-2 rounded-md border border-gray-300">
                                            You can't perform any actions on this Room.
                                        </p>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <div class="mt-6 flex justify-center">
                <Pagination
                    v-if="rooms.last_page > 1"
                    :total="rooms.total"
                    :items-per-page="rooms.per_page"
                    :sibling-count="1"
                    :default-page="rooms.current_page"
                    show-edges
                    @update:page="handlePageChange"
                >
                    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst @click="handlePageChange(1)" :disabled="rooms.current_page === 1" />
                        <PaginationPrev @click="handlePageChange(rooms.current_page - 1)" :disabled="rooms.current_page === 1" />

                        <template v-for="(item, index) in items" :key="index">
                            <PaginationListItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                as-child
                            >
                                <Button
                                    class="w-10 h-10 p-0"
                                    :variant="item.value === rooms.current_page ? 'default' : 'outline'"
                                    @click="handlePageChange(item.value)"
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext @click="handlePageChange(rooms.current_page + 1)" :disabled="rooms.current_page === rooms.last_page" />
                        <PaginationLast @click="handlePageChange(rooms.last_page)" :disabled="rooms.current_page === rooms.last_page" />
                    </PaginationList>
                </Pagination>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{{ isEditMode ? 'Edit Room' : 'Create New Room' }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="space-y-2">
                        <Label>Capacity</Label>
                        <Input v-model="form.capacity" type="number" placeholder="Enter room capacity" />
                        <p v-if="page.props.errors.capacity" class="text-sm text-red-500">{{ page.props.errors.capacity }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label>Price  ($)</Label>
                        <Input v-model="form.price" type="number" step="0.01" placeholder="Enter price in dollars" />
                        <p v-if="page.props.errors.price" class="text-sm text-red-500">{{ page.props.errors.price }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label>Description</Label>
                        <Input v-model="form.description" placeholder="Enter room description" />
                        <p v-if="page.props.errors.description" class="text-sm text-red-500">{{ page.props.errors.description }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label>Floor</Label>
                        <Select v-model="form.floor_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Select Floor" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="floor in page.props.floors" :key="floor.id" :value="floor.id.toString()">
                                    {{ floor.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="page.props.errors.floor_id" class="text-sm text-red-500">{{ page.props.errors.floor_id }}</p>
                    </div>
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
                    <div class="space-y-2">
                        <Label>Upload New Images</Label>
                        <Input type="file" multiple @change="handleImageUpload" />
                        <p v-if="page.props.errors.images" class="text-sm text-red-500">{{ page.props.errors.images }}</p>
                    </div>

<div v-if="isEditMode" class="space-y-2">
    <Label>Reservation Status</Label>
    <div class="flex items-center space-x-3">
        <button
            type="button"
            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2"
            :class="[form.is_available ?  'bg-green-500': 'bg-red-500' ]"
            @click="form.is_available = !form.is_available"
        >
            <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                :class="[form.is_available ? 'translate-x-5' : 'translate-x-0']"
            />
        </button>
        <span class="text-sm" :class="form.is_available ?  'text-green-600' :'text-red-600'  ">
            {{ form.is_available ? 'Available' : 'Reserved' }}
        </span>
    </div>
</div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                        <Button type="submit" :disabled="isLoading">
                            {{ isEditMode ? 'Save Changes' : 'Create Room' }}
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
                        Are you sure you want to delete this room? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
                    <Button type="button" variant="destructive" @click="deleteRoom">Delete</Button>
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
