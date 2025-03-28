<template>
  <NavBarMain />
  <h2 class="text-2xl font-semibold mb-4 text-center cardTitle mt-10">Favorites</h2>

  <div class="container mx-auto p-6">
    <div v-if="favoriteStore.isLoading" class="text-center">Loading...</div>

    <!-- Show alert if no favorite rooms exist -->
    <Alert v-if="favorites.length === 0" variant="destructive" class="mb-4">
        <div class="text-center py-10">
            <h3 class="text-lg font-medium text-gray-700">Your Favorites is empty 😢</h3>
            <p class="text-gray-500">Start adding items to your Favorites!</p>
        </div>
    </Alert>

    <!-- Display Table when there are favorites -->
    <Table v-if="favorites.length > 0">
      <TableHeader>
        <TableRow>
          <TableHead>Image</TableHead>
          <TableHead>Room Number</TableHead>
          <TableHead>Price</TableHead>
          <TableHead>Action</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="room in favorites" :key="room.id">
          <TableCell>
<!--              <div> {{ room}} </div>-->
            <img v-if="room.room.media" :src="room.room.media[0].original_url" alt="Room" class="w-16 h-16 object-cover rounded-md" />
            <span v-else class="text-gray-400">No Image</span>
          </TableCell>
          <TableCell>{{ room.room.number || "No number" }}</TableCell>
          <TableCell>{{ room.room.price ? `${formatCurrency(room.room.price / 100)}` : "No Price" }}</TableCell>
          <TableCell>
            <Button variant="destructive" @click="removeFromFavorites(room.room.id)">Remove</Button>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>

<script setup lang="ts">
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import Alert from "@/components/ui/alert/index.vue";
import { useFavoriteStore } from '@/stores/favorite';
import NavBarMain from '@/components/NavBarMain.vue';
import { formatCurrency } from '@/utils/currencyFormatter';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const favoriteStore = useFavoriteStore();
const page = usePage();
console.log(page.props);
const removeFromFavorites = (id: number) => {
  favoriteStore.removeFavorite(id);
  favorites.value = favorites.value.filter((room: any) => room.room.id !== id);
    console.log(favorites.value);

};
const favorites = ref(page.props.auth.user.favorites);



</script>
