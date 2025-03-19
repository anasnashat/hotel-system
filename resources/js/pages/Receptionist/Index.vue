<script setup lang="ts">
import { ref, computed, defineProps } from "vue";
import { router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";

interface Request {
    id: number;
    user: { name: string; email: string };
    national_id: string;
    phone_number: string;
}

const props = defineProps<{ requests: Request[] }>();

const requests = ref<Request[]>(props.requests);

const approveRequest = (id: number) => {
    router.post(route("receptionist.approve"), { client_id: id }, {
        preserveScroll: true,
        onSuccess: () => {
            requests.value = requests.value.filter((req) => req.id !== id);
        }
    });
};
</script>

<template>
    <div class="w-full">
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Client Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>National ID</TableHead>
                        <TableHead>Phone Number</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="requests.length">
                        <TableRow v-for="request in requests" :key="request.id">
                            <TableCell>{{ request.id }}</TableCell>
                            <TableCell>{{ request.user.name || "N/A" }}</TableCell>
                            <TableCell>{{ request.user.email || "N/A" }}</TableCell>
                            <TableCell>{{ request.national_id || "N/A" }}</TableCell>
                            <TableCell>{{ request.phone_number || "N/A" }}</TableCell>
                            <TableCell>
                                <Button variant="outline" class="text-green-500" @click="approveRequest(request.id)">
                                    Approve
                                </Button>
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell colspan="6" class="h-24 text-center">No requests found.</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
