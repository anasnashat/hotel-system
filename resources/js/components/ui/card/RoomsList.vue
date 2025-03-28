<script setup lang="ts">
import { ref, onMounted } from 'vue';
import RoomCard from './RoomCard.vue';
import axios from "axios";
import { type Room } from '@/types';

// const rooms = ref<Room[]>([]);
//
// const fetchRooms = async () => {
//     try {
//         const response = await axios.get("http://127.0.0.1:8000/api/rooms");
//         rooms.value = response.data.data;
//     } catch (error) {
//         console.error("Error fetching rooms:", error);
//     }
// };
//
// onMounted(fetchRooms);

const props = defineProps<{
  rooms:any
}>();

console.log(props.rooms);

</script>

<template>
    <div class="container mx-auto mt-5">
       <a href="#rooms"><h2 class="title-text text-center">Rooms</h2></a>
    </div>
  <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <RoomCard v-for="room in props.rooms" :key="room.id" :room="room" />
  </div>
</template>

<style scoped>

  </style>
