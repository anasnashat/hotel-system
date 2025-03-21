<template>
    <NavMain />
    <h2 class="text-2xl font-semibold mb-4 text-center cardTitle mt-10">Favorites</h2>
    <div class="container mx-auto p-6">
  
      <!-- Show alert if no favorite rooms exist -->
      <Alert v-if="favoriteStore.favoriteRooms.length === 0" variant="destructive" class="mb-4">
        <AlertTitle>No Favorites Yet</AlertTitle>
        <AlertDescription>
          You haven't added any favorite rooms yet. Explore and save your favorite ones!
        </AlertDescription>
      </Alert>
  
      <!-- Display message when no favorites exist -->
      <div v-if="favoriteStore.favoriteRooms.length === 0" class="text-center py-4">
        <p class="text-gray-500">You haven't added any favorites yet.</p>
        <a href="/" class="text-blue-500">Browse Products</a>
      </div>
  
      <!-- Display Table when there are favorites -->
      <Table v-else>
        <TableHeader>
          <TableRow>
            <TableHead>Image</TableHead>
            <TableHead>Name</TableHead>
            <TableHead>Price</TableHead>
            <TableHead>Action</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="item in favoriteStore.favoriteRooms" :key="item.id">
            <TableCell>
              <img v-if="item.image" :src="item.image" alt="Room" class="w-16 h-16 object-cover rounded-md" />
              <span v-else class="text-gray-400">No Image</span>
            </TableCell>
            <TableCell>{{ item.name || "No Name" }}</TableCell>
            <TableCell>{{ item.price ? `$${item.price.toFixed(2)}` : "No Price" }}</TableCell>
            <TableCell>
              <Button variant="destructive" @click="removeFromFavorites(item.id)">
                Remove
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </template>
  
  <script setup lang="ts">
  import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from "@/components/ui/table";
  import { Button } from "@/components/ui/button";
  import Alert from "@/components/ui/alert/index.vue"; //  Import Alert Components
  import { useFavoriteStore } from '@/stores/favorite';
  import NavMain from '@/components/NavMain.vue';
  
  const favoriteStore = useFavoriteStore();
  
  const removeFromFavorites = (id: number) => { 
    if (id) {
      favoriteStore.removeFavorite(id);
    }
  };
  </script>
  