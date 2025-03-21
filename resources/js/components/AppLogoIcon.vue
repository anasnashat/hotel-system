<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
  <div class="flex items-center space-x-2">
    <a href="/"><span class="logo-text">Hotel</span></a>
  </div>
</template>

<style scoped>
.logo-text {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  font-weight: bold;
  color: #5b5329;
  text-shadow: 2px 2px 4px rgba(45, 42, 42, 0.6);
  transition: text-shadow 0.3s ease-in-out;
}

.dark .logo-text {
  text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.6);
}
  </style>

