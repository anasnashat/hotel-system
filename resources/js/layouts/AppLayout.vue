<script setup lang="ts">
import type { BreadcrumbItemType } from '@/types';
import SidebarProvider from '@/components/ui/sidebar/SidebarProvider.vue';
import AppSidebar from '@/components/AppSidebar.vue'

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div>
        <!-- <NavMain :items="[]"/> -->
        <SidebarProvider>
            <AppSidebar />
            <slot />
        </SidebarProvider>
    </div>
  
</template>
