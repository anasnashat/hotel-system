<script setup lang="ts">
import NavBarMain from "@/components/NavBarMain.vue";
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";

interface Room {
  id: number;
  name: string;
  price: number;
  image: string;
  description: string;
  features: string[];
}

const props = defineProps<{ id: number }>();
const room = ref<Room | null>(null);

// Fetch rooms from JSON
const fetchRoomDetails = async () => {
  try {
    const response = await fetch("/data/rooms.json"); 
    const rooms: Room[] = await response.json();

    // Find the room based on the ID from the route
    const roomId = Number(props.id);
    room.value = rooms.find((r) => r.id === roomId) || null;
  } catch (error) {
    console.error("Error fetching room data:", error);
  }
};

// Load room details when component is mounted
onMounted(fetchRoomDetails);
</script>

<template>
    <NavBarMain/>
    <div v-if="room" class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg m-4">
    <h1 class="text-2xl font-bold mb-4 cardTitle">{{ room.name }}</h1>
    <img :src="room.image" class="w-full h-64 object-cover rounded-md mb-4" />
    <p class="text-gray-700">{{ room.description }}</p>
    <p class="text-xl font-semibold mt-2 cardTitle">${{ room.price }} per night</p>
  </div>
</template>
