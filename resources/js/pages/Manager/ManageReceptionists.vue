<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { router } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Receptionists', href: '/manager/manage-receptionists' },
];

interface Receptionist {
    id: number;
    name: string;
    email: string;
    is_banned: boolean;
}

const props = defineProps<{ receptionists: Receptionist[] }>();
const receptionists = ref<Receptionist[]>(props.receptionists);

const editReceptionist = (id: number) => {
    router.get(route('manager.edit-receptionist', id));
};

const deleteReceptionist = (id: number) => {
    if (confirm('Are you sure you want to delete this receptionist?')) {
        router.delete(route('manager.delete-receptionist', id), {
            preserveScroll: true,
            onSuccess: () => {
                receptionists.value = receptionists.value.filter((r) => r.id !== id);
            },
        });
    }
};

const banReceptionist = (id: number) => {
    if (confirm('Are you sure you want to ban this receptionist?')) {
        router.post(route('manager.ban-receptionist', id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                const receptionist = receptionists.value.find((r) => r.id === id);
                if (receptionist) receptionist.is_banned = true;
            },
        });
    }
};

const unbanReceptionist = (id: number) => {
    if (confirm('Are you sure you want to unban this receptionist?')) {
        router.post(route('manager.unban-receptionist', id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                const receptionist = receptionists.value.find((r) => r.id === id);
                if (receptionist) receptionist.is_banned = false;
            },
        });
    }
};
</script>

<template>
    <Head title="Manage Receptionists" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full">
            <h1 class="text-3xl font-bold mb-6 text-center">Manage Receptionists</h1>
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
                                    <Button variant="outline" class="text-blue-500 mr-2" @click="editReceptionist(receptionist.id)">Edit</Button>
                                    <Button variant="outline" class="text-red-500 mr-2" @click="deleteReceptionist(receptionist.id)">Delete</Button>
                                    <Button v-if="!receptionist.is_banned" variant="outline" class="text-yellow-500" @click="banReceptionist(receptionist.id)">Ban</Button>
                                    <Button v-else variant="outline" class="text-green-500" @click="unbanReceptionist(receptionist.id)">Unban</Button>
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
    </AppLayout>
</template>
