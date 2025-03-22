<template>
  <footer class="bg-gray-900 text-white py-8 mt-10">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center px-6">
      
      <!-- Logo & Name -->
      <div class="flex items-center space-x-3">
        <h1 class="text-lg font-bold footerTitle">Hotel</h1>
      </div>

      <!-- Navigation Links -->
      <nav class="mt-4 md:mt-0">
        <ul class="flex space-x-6">
          <li><a href="#" class="hover:text-gray-400 transition-colors">Home</a></li>
          <li><a href="#" class="hover:text-gray-400 transition-colors">Rooms</a></li>
        </ul>
      </nav>

      <!-- Social Media Icons -->
      <div class="mt-4 md:mt-0 flex space-x-4">
        <a href="#" class="icon">
          <Facebook class="icon-style" />
        </a>
        <a href="#" class="icon">
          <Instagram class="icon-style" />
        </a>
        <a href="#" class="icon">
          <Music2 class="icon-style" />
        </a>
      </div>

    </div>

    <!-- Bottom Copyright -->
    <div class="text-center mt-6 text-sm text-gray-400">
      © {{ new Date().getFullYear() }} Hotel . All rights reserved.
    </div>
  </footer>
</template>

<script setup>
import { Facebook, Instagram, Music2 } from 'lucide-vue-next';
</script>

<style scoped>
/* Social Media Icon Styles */
.icon {
  @apply transition-transform duration-300 hover:scale-110;
}
.icon-style {
  @apply w-6 h-6 text-white hover:text-gray-400;
}
.footerTitle{
  font-size: 30px;
  margin-top: 10px;
}
</style>
