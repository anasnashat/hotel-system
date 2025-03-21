<script setup lang="ts">

</script>

<template>
      <div class="relative w-full h-[500px] flex items-center justify-center">
    <video autoplay muted loop class="w-full h-full object-cover">
      <source src="/videos/welcomVideo.mp4" type="video/mp4" />
      Your browser does not support the video tag.
    </video>
    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center ">
      <h1 class="welcome-text">Welcome to Our Hotel</h1>
    </div>
  </div>
</template>

<style>

</style>