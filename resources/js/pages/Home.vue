<script setup>
import NavBarMain from '@/components/NavBarMain.vue';
import RoomsList from '@/components/ui/card/RoomsList.vue';
import WelcomeVideo from '@/components/WelcomeVideo.vue';
import Footer from '@/components/Footer.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const rooms = computed(() => page.props.rooms);
</script>







<template>
    <NavBarMain/>
    <WelcomeVideo/>
    <RoomsList :rooms="rooms"/>
    <Footer/>
</template>
