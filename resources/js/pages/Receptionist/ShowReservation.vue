<script setup lang="ts">
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { defineProps, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
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
import AppLayout from '@/layouts/AppLayout.vue';
import TabsHeader from '@/components/TabsHeader.vue';

// Props
const props = defineProps<{ clients: any }>();

// Reactive clients array
const clients = ref(props.clients);
const page = usePage() ;
// Pagination
const handlePageChange = (page: number) => {
    router.get(route('clients.index', { page }), {}, { preserveScroll: true });
};

const isAdminOrManager = page.props.auth.user.roles.some((role: { name: string }) => ['admin', 'manager'].includes(role.name));

const tabs = [
    { label: 'Requests', href: route('clients-management.index') },
    { label: 'Reservation', href: route('receptionist.show-reservation') },
    { label: 'My Approved Clients', href: route('receptionist.myApprovedClients') },
];
if (isAdminOrManager) {
    tabs.push({ label: 'All Clients', href: route('receptionist.all-clients') });
}
</script>

<template>
    <Head title="Receptionist Requests" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full p-6 max-w-8xl">
            <TabsHeader title="Client Management" :tabs="tabs" />

        <Card>
            <CardHeader>
                <CardTitle>Clients</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Client Name</TableHead>
                            <TableHead>Accompany</TableHead>
                            <TableHead>Room Number</TableHead>
                            <TableHead>Phone Number</TableHead>
                            <TableHead>Client Paid Price</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="clients.length === 0">
                            <TableCell colspan="6" class="h-24 text-center">
                                No clients found.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="client in clients" :key="client.id">
                            <TableCell>{{ client.id }}</TableCell>
                            <TableCell>{{ client.client.name || 'N/A' }}</TableCell>
                            <TableCell>{{ client.accompany_number || 'N/A' }}</TableCell>
                            <TableCell>{{ client.room?.number || 'N/A' }}</TableCell>
                            <TableCell>{{ client.client.profile.phone_number || 'N/A' }}</TableCell>
                            <TableCell>{{ client.price_at_booking || 'N/A' }}</TableCell>
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
