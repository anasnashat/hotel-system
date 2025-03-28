<script setup lang="ts">
import NavBarMain from "@/components/NavBarMain.vue";
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { type Room } from '@/types';

const props=defineProps<{ id: number }>();
const room = ref<Room | null>(null);
const fetchRooms = async () => {
  try {
        const response = await axios.get(`http://127.0.0.1:8000/api/rooms/${props.id}`);
        room.value = response.data.data;
        console.log("Room Details:", room.value);
    } catch (error) {
        console.error('Error fetching room details:', error);
    }
}
onMounted(fetchRooms);
</script>

<template>
    <NavBarMain/>
    <div v-if="room" class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg m-4">
      <h1 class="text-2xl font-bold mb-4 cardTitle">{{ room.name }}</h1>
      <img :src="room.first_image_url" class="w-full h-64 object-cover rounded-md mb-4" />
      <p class="text-gray-700">{{ room.description }}</p>
      <p class="text-xl font-semibold mt-2 cardTitle">${{ room.price/100 }} per night</p>
    </div>
</template>


