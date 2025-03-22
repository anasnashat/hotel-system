<script setup lang="ts">
defineProps<{
  href: string;
  text: string;
}>();
</script>

<template>
  <a :href="href" class="text-gray-700 dark:text-white hover:text-primary transition-colors">
    {{ text }}
  </a>
</template> 