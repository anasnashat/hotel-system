<script setup lang="ts">
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Users, Folder, BookOpen } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import NavFooter from '@/components/NavFooter.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Define default sidebar menu items
const mainNavItems = computed(() => {
    const items = [
        { title: 'Dashboard', href: route('dashboard'), icon: LayoutGrid },
    ];

    // Add role-based items
    if (user.value?.roles?.includes('admin')) {
        items.push({ title: 'Manage Users', href: route('admin.dashboard'), icon: Users });
    }
    if (user.value?.roles?.includes('manager')) {
        items.push({ title: 'Manage Clients', href: route('manager.'), icon: Users });
    }
    
    return items;
});

// Footer links
const footerNavItems = [
    { title: 'Github', href: 'https://github.com', icon: Folder },
    { title: 'Docs', href: 'https://laravel.com/docs', icon: BookOpen },
];
</script>
<template>
    <div v-if="true" class="sidebar">
      <slot />
    </div>
  </template>
  
  <style scoped>
  .sidebar {
    width: 250px;
    background: white;
    color: #222;
    height: 100vh;
  }
  </style>
