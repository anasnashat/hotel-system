<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- <AppSidebar/> -->
            <slot />
    </AppLayout>
</template>
