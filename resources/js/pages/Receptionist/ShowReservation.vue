<script setup >
import { ref, computed, defineProps } from "vue";
import { router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";



const props = defineProps([
    "clients"
]);

console.log(props.clients);

const c = ref(props.clients);


</script>

<template>
    <div class="w-full">
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Client Name</TableHead>
                        <TableHead>Accompany</TableHead>
                        <TableHead>Room Number</TableHead>
                        <TableHead>Phone Number</TableHead>
                        <TableHead>Client Paid Price </TableHead>

                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="c.length">
                        <TableRow v-for="client in c" :key="client.id">
                            <TableCell>{{ client.id }}</TableCell>
                            <TableCell>{{ client.client.name || "N/A" }}</TableCell>
                            <TableCell>{{ client.accompany_number || "N/A" }}</TableCell>
                            <TableCell>{{ client.room.number || "N/A" }}</TableCell>
                            <TableCell>{{ client.client.profile.phone_number || "N/A" }}</TableCell>
                            <TableCell>{{ client.price_at_booking || "N/A" }}</TableCell>

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
