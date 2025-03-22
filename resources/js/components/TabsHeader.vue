<script setup lang="ts">

import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

// Define props
const props = defineProps<{
    title: string; // Dynamic title
    tabs: { label: string; href: string }[]; // Array of tabs
}>();

console.log(window.location.href);

// Helper function to check if a tab is active
const isActiveTab = (href: string) => {
    return window.location.href === href;
};
</script>

<template>
    <div class="mb-8 my-5">
        <!-- Dynamic Title -->
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ title }}</h1>

        <!-- Tabs -->
        <div class="mt-4 flex space-x-4 border-b">
            <Link
                v-for="(tab, index) in tabs"
                :key="index"
                :href="tab.href"
                class="relative pb-2 px-4 text-sm font-medium transition-colors"
                :class="{
                    'text-primary': isActiveTab(tab.href),
                    'text-muted-foreground hover:text-primary': !isActiveTab(tab.href),
                }"
            >
                {{ tab.label }}
                <!-- Underline for active tab -->
                <span
                    v-if="isActiveTab(tab.href)"
                    class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"
                ></span>
            </Link>
        </div>
    </div>
</template>
