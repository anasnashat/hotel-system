<script setup lang="ts">
import RoomCard from '@/components/ui/card/RoomCard.vue';
import { usePage } from '@inertiajs/vue3';


const page = usePage();
const rooms = page.props.rooms


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
