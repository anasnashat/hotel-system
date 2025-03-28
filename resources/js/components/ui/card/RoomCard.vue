<script setup lang="ts">
import { ref, defineProps, computed } from 'vue';
import { Heart } from 'lucide-vue-next';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useCartStore } from '@/stores/cart';
import { useFavoriteStore } from '@/stores/favorite';
import Swal from 'sweetalert2';
import { router, usePage } from '@inertiajs/vue3';
import { formatCurrency } from '@/utils/currencyFormatter';
import { type Room } from '@/types';
import axios from 'axios';


const cartStore = useCartStore();
const favoriteStore = useFavoriteStore();
const favoriteCount = ref(0);



// Props for room details
const props =defineProps<{ room: Room }>();
const page = usePage();
console.log(page.props);

const isBooked = computed(() => {
  return cartStore.cart.some((item) => item.id === props.room.id);
});
interface Auth {
  user: {
    favorites: { room: { id: number } }[];
  };
}

const favorites = ref<{ room: { id: number } }[]>((page.props.auth as Auth).user.favorites);

const isFavorite = computed(() => {
  return Array.isArray(favorites.value) &&
         favorites.value.some((item: { room: { id: number } }) => item.room.id === props.room.id);
});


// Add to cart function
const addToCart = () => {
  if (!isBooked.value) {
      console.log("Adding to cart", props.room.id);
    cartStore.addToCart({
      id: props.room.id,
      name: props.room.name,
      price: props.room.price,
      image: props.room.first_image_url,
    });

    Swal.fire({
      title: "Room Booked!",
      text: `You booked the ${props.room.name} successfully! 🏨`,
      icon: "success",
      showConfirmButton: false,
      timer: 2000, // Auto-close after 2 seconds
      toast: true,
      position: "top-end",
    });
  }
};


const isModalOpen = ref(false);

const toggleFavorite = () => {
  if (!isFavorite.value) { // If NOT in favorites, add it
    favoriteStore.addFavorite(props.room);
    favorites.value.push({ room: { id: props.room.id } });

    Swal.fire({
      title: "❤️ Added to Favorites!",
      text: "This item is now in your favorites list.",
      icon: "success",
      showConfirmButton: false,
      timer: 2000,
      toast: true,
      position: "top-end",
    });
  } else { // If in favorites, remove it
    favoriteStore.removeFavorite(props.room.id);
    favorites.value = favorites.value.filter((item: { room: { id: number } }) => item.room.id !== props.room.id);
    Swal.fire({
      title: "❤️ Removed From Favorites!",
      text: "This item is now removed from your favorites list.",
      icon: "error",
      showConfirmButton: false,
      timer: 2000,
      toast: true,
      position: "top-end",
    });
  }
};




const goToRoomDetails = (roomId: number) => {
    router.visit(`/room/${roomId}`);
};



</script>

<template>
    <Card class="overflow-hidden shadow-lg bg-white dark:bg-gray-800 mt-5 relative transform transition-transform duration-300 ease-in-out hover:scale-105">
        <!-- Room Image -->
        <div class="relative">

            <img v-if="room.first_image_url" :src="room.first_image_url" alt="Room Image" class="w-full h-[300px] p-4" />


            <!-- Favorite Heart Icon -->
            <button
                @click="toggleFavorite"
                class="absolute top-3 right-3 p-2 rounded-full bg-white dark:bg-gray-900 shadow-md"
            >
                <Heart
                    :size="24"
                    class="transition-all duration-300"
                    :class="isFavorite ? 'text-red-500 fill-red-500' : 'text-gray-500'"
                />
            </button>
        </div>

        <!-- Card Header -->
        <!-- <CardHeader class="flex justify-between items-center px-4 pt-4">
          <CardTitle class="cardTitle">
            {{ room.name }}
          </CardTitle>
        </CardHeader> -->

        <!-- Card Content -->
        <CardContent class="px-4 py-2">
            <p class="text-gray-600 dark:text-gray-400 cardTitle">{{ formatCurrency(room.price/100)}} </p>
        </CardContent>

        <!-- Card Footer -->
        <CardFooter class="p-4 flex space-x-2">
            <Button
                variant="outline"
                class="w-1/2 border border-[#5b5329] text-[#5b5329] font-medium px-4 py-1 rounded-md hover:bg-[#5b5329] hover:text-white transition duration-300 dark:text-white dark:hover:bg-[#5b5329] dark:hover:text-white"
                @click="goToRoomDetails(room.id)">
                More Details
            </Button>
            <Button @click="addToCart" :disabled="!room.is_available"   :class="!room.is_available ? 'bg-gray-400 text-white border-gray-400 cursor-not-allowed' : 'bg-[#5b5329] text-white border-[#5b5329] hover:bg-white hover:text-[#5b5329]'"  class="w-1/2 border border-[#5b5329] bg-[#5b5329] text-white font-medium px-4 py-1 rounded-md hover:bg-white hover:text-[#5b5329] transition duration-300">
                {{ !room.is_available ? "Booked" : "Book Now" }}
            </Button>
        </CardFooter>
    </Card>
    <!-- Room Details Modal -->
    <Dialog v-model:open="isModalOpen">
        <DialogContent class="max-w-lg p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            <DialogHeader class="flex justify-between items-center">
                <DialogTitle class="text-xl font-bold text-gray-900 dark:text-white font-serif text-center cardTitle">
                    {{ room.number }}
                </DialogTitle>
            </DialogHeader>

            <!-- Room Image -->

            <img :src="room.first_image_url" alt="Room Image" class="w-full h-56
       rounded-lg my-3 object-cover" />
            <!-- Room Info -->
            <p class="text-gray-600 dark:text-gray-300 mb-3 font-medium font-semibold">{{ room.description }}</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-white">{{formatCurrency(room.price /100) }} per night</p>

            <!-- Room Features -->
            <ul v-if="room.features && room.features.length" class="mt-3 list-disc list-inside text-gray-600 dark:text-gray-300">
                <li v-for="(feature, index) in room.features" :key="index">{{ feature }}</li>
            </ul>

            <!-- Close Button -->
            <Button class="w-full mt-4 bg-[#5b5329] text-white hover:bg-white hover:text-[#5b5329]" @click="isModalOpen = false">
                Close
            </Button>
        </DialogContent>
    </Dialog>

</template>


<style scoped>
button {
    transition: all 0.3s ease-in-out;
}

</style>
