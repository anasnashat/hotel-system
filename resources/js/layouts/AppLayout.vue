<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import type { BreadcrumbItemType } from '@/types';


interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div>
        <NavMain :items="[]"/>
        <slot />
    </div>
  
</template>
