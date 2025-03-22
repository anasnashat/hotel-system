<script setup lang="ts">
import { Sidebar, SidebarContent, SidebarHeader } from '@/components/ui/sidebar';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
  items?: any[];
}>();
</script>

<template>
  <Sidebar>
    <SidebarHeader>
      <AppLogoIcon />
    </SidebarHeader>
    <SidebarContent>
      <slot />
    </SidebarContent>
  </Sidebar>
</template> 