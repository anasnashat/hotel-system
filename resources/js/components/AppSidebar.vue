<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue'; // Ensure correct path
import Alert from '@/components/Alert.vue';
import { computed, watchEffect } from 'vue';

interface User {
  roles: string[];
}

type PageProps = {
  auth: {
    user: User | null;
  };
  [key: string]: any;
};

// Get user data from Inertia
const page = usePage<PageProps>();
const user = computed(() => page.props.auth.user ?? { roles: [] });

// Debugging: Log user roles
watchEffect(() => {
    console.log("User data:", user.value);
    console.log("User roles:", user.value.roles);
});

// Define sidebar navigation items
const mainNavItems = computed<NavItem[]>(() => {
    console.log("Computing mainNavItems...");

    const items: NavItem[] = [
        { title: 'Dashboard', href: route('dashboard'), icon: LayoutGrid },
        { title: 'Receptionists', href: route('receptionist.index', {}, false), icon: Users },
        { title: 'Manager', href: route('manager.manage-clients', {}, false), icon: Users },
    ];

    if (user.value.roles.some((role: string) => ['admin', 'manager'].includes(role))) {
        items.push({ title: 'Floor Management', href: route('floors.index', {}, false), icon: LayoutGrid });
    }
    if (user.value.roles.includes('admin')) {
        items.push({ title: 'Manage Manager', href: route('manager.manage-clients', {}, false), icon: Users });
    }

    return items;
});

// Define footer navigation items
const footerNavItems: NavItem[] = [
    { title: 'Github Repo', href: 'https://github.com/laravel/vue-starter-kit', icon: Folder },
    { title: 'Documentation', href: 'https://laravel.com/docs/starter-kits', icon: BookOpen },
];
</script>

<template>
    <Sidebar collapsible variant="inset" :open="true" class="sidebar">
        <SidebarHeader class="sidebar-header">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo class="hidden-logo" /> <!-- Hide Logo -->
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- Sidebar Content (Scrolls in Y-axis) -->
        <SidebarContent class="sidebar-content">
            <NavMain :items="mainNavItems" :isAuthenticated="!!user" />
        </SidebarContent>

        <!-- Sidebar Footer (Always at Bottom) -->
        <SidebarFooter class="sidebar-footer">
            <NavFooter :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
</template>


<style scoped>
/* Sidebar container */
.sidebar {
    display: flex;
    flex-direction: column;
    height: 100vh; /* Full viewport height */
    width: 250px; /* Adjust as needed */
    overflow: hidden; /* Prevent extra scrollbars */
}

/* Hide the logo */
.hidden-logo {
    display: none; /* Hide logo */
}

/* Content should scroll only in Y direction */
.sidebar-content {
    flex-grow: 1; /* Fill remaining space */
    overflow-y: auto; /* Enable vertical scrolling */
    overflow-x: hidden; /* Prevent horizontal scrolling */
    width: 100%;
}

/* Footer should stay at bottom */
.sidebar-footer {
    margin-top: auto; /* Push footer to bottom */
    padding: 1rem;
    background-color: var(--sidebar-bg); /* Optional styling */
    width: 100%;
}
</style>
