<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { LucideSun, LucideMoon } from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import NavUser from '@/components/NavUser.vue';
 
// Dark Mode State (Persistent)
const isDarkMode = ref(localStorage.getItem('theme') === 'dark');

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    document.documentElement.classList.toggle('dark', isDarkMode.value);
    localStorage.setItem('theme', isDarkMode.value ? 'dark' : 'light');
};

onMounted(() => {
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
    }
});
</script>

<template>
  <nav class="bg-white dark:bg-gray-900 shadow-md p-4">
    <div class="container mx-auto flex items-center justify-between">
      
      <!-- Hotel Logo -->
      <AppLogoIcon />

      <!-- Navigation Actions (Cart, Favorite, User Auth, Dark Mode) -->
      <div class="flex items-center space-x-4">
        <NavUser />

        <!-- Dark Mode Toggle -->
        <button 
          @click="toggleDarkMode" 
          class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
          <LucideSun v-if="isDarkMode" :size="20" />
          <LucideMoon v-else :size="20" />
        </button>
      </div>
    </div>
  </nav>
</template>
