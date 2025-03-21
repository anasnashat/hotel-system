<script setup lang="ts">
import { ref, onMounted } from 'vue';
import RoomCard from './RoomCard.vue';

interface Room {
  id: number;
  name: string;
  price: number;
  image: string;
  description: string;
}

const rooms = ref<Room[]>([]);

onMounted(async () => {
  const response = await fetch('/data/rooms.json');
  rooms.value = await response.json();
});
</script>

<template>
    <div class="container mx-auto mt-5">
       <a href="#rooms"><h2 class="title-text text-center">Rooms</h2></a>
    </div>
  <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <RoomCard v-for="room in rooms" :key="room.id" :room="room" />
  </div>
</template>

<style scoped>

  </style>
