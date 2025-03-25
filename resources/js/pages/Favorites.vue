<template>
  <NavBarMain />
  <h2 class="text-2xl font-semibold mb-4 text-center cardTitle mt-10">Favorites</h2>
  
  <div class="container mx-auto p-6">
    <div v-if="favoriteStore.isLoading" class="text-center">Loading...</div>

    <!-- Show alert if no favorite rooms exist -->
    <Alert v-if="!favoriteStore.isLoading && favoriteStore.favoriteRooms.length === 0" variant="destructive" class="mb-4">
      <AlertTitle>No Favorites Yet</AlertTitle>
      <AlertDescription>You haven't added any favorite rooms yet. Explore and save your favorite ones!</AlertDescription>
    </Alert>

    <!-- Display Table when there are favorites -->
    <Table v-if="!favoriteStore.isLoading && favoriteStore.favoriteRooms.length > 0">
      <TableHeader>
        <TableRow>
          <TableHead>Image</TableHead>
          <TableHead>Name</TableHead>
          <TableHead>Price</TableHead>
          <TableHead>Action</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="room in favoriteStore.favoriteRooms" :key="room.id">
          <TableCell>
            <img v-if="room.first_image_url" :src="room.first_image_url" alt="Room" class="w-16 h-16 object-cover rounded-md" />
            <span v-else class="text-gray-400">No Image</span>
          </TableCell>
          <TableCell>{{ room.name || "No Name" }}</TableCell>
          <TableCell>{{ room.price ? `${formatCurrency(room.price)}` : "No Price" }}</TableCell>
          <TableCell>
            <Button variant="destructive" @click="removeFromFavorites(room.id)">Remove</Button>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import Alert from "@/components/ui/alert/index.vue";
import { useFavoriteStore } from '@/stores/favorite';
import NavBarMain from '@/components/NavBarMain.vue';
import { formatCurrency } from '@/utils/currencyFormatter';

const favoriteStore = useFavoriteStore();

const removeFromFavorites = (id: number) => { 
  favoriteStore.removeFavorite(id);
};

onMounted(() => {
  favoriteStore.fetchFavorites(); // Fetch favorites when component loads
});
</script>
