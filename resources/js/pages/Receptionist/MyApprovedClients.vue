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
    { title: 'My Approved Clients', href: route('receptionist.myApprovedClients') },
];

// Page props
const page = usePage();

// Client interface
interface Client {
    id: number;
    user: {
        name: string;
        email: string;
        gender: string;
        id: number;
    };
    phone_number: string;
    country_code: string;
    approved_by: number;
}

// Props
const { clients } = defineProps<{
    clients: {
        data: Client[];
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
        from: number;
        to: number;
    }
}>();




const isAdminOrManager = page.props.auth.user.roles.some((role: { name: string }) => ['admin', 'manager'].includes(role.name));

const tabs = [
    { label: 'Requests', href: route('clients-management.index') },
    { label: 'Reservation', href: route('receptionist.show-reservation') },
    { label: 'My Approved Clients', href: route('receptionist.myApprovedClients') },
];
if (isAdminOrManager) {
    tabs.push({ label: 'All Clients', href: route('receptionist.all-clients') });
}


// Handle page change
const handlePageChange = (page: number) => {
    router.get(route('receptionist.myApprovedClients', { page }), {}, { preserveScroll: true });
};
</script>

<template>
    <Head title="My Approved Clients" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">
            <TabsHeader title="Client Management" :tabs="tabs" />

            <!-- Clients Table -->
            <Card>
                <CardHeader>
                    <CardTitle>My Approved Clients</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>Client Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Phone Number</TableHead>
                                <TableHead>Country</TableHead>
                                <TableHead>Gender</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="clients.data.length === 0">
                                <TableCell colspan="7" class="h-24 text-center">
                                    No clients found.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="client in clients.data" :key="client.id">
                                <TableCell>{{ client.id }}</TableCell>
                                <TableCell>{{ client.user?.name || 'N/A' }}</TableCell>
                                <TableCell>{{ client.user?.email || 'N/A' }}</TableCell>
                                <TableCell>{{ client.phone_number || 'N/A' }}</TableCell>
                                <TableCell>{{ client.country_code || 'N/A' }}</TableCell>
                                <TableCell>{{ client.user?.gender || 'N/A' }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <Pagination
                    v-if="clients.last_page > 1"
                    :total="clients.total"
                    :items-per-page="clients.per_page"
                    :sibling-count="1"
                    :default-page="clients.current_page"
                    show-edges
                    @update:page="handlePageChange"
                >
                    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst @click="handlePageChange(1)" :disabled="clients.current_page === 1" />
                        <PaginationPrev @click="handlePageChange(clients.current_page - 1)" :disabled="clients.current_page === 1" />

                        <template v-for="(item, index) in items" :key="index">
                            <PaginationListItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                as-child
                            >
                                <Button
                                    class="w-10 h-10 p-0"
                                    :variant="item.value === clients.current_page ? 'default' : 'outline'"
                                    @click="handlePageChange(item.value)"
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext @click="handlePageChange(clients.current_page + 1)" :disabled="clients.current_page === clients.last_page" />
                        <PaginationLast @click="handlePageChange(clients.last_page)" :disabled="clients.current_page === clients.last_page" />
                    </PaginationList>
                </Pagination>
            </div>
        </div>


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
