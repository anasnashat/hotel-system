<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/pages/Dashboard/components/NavMain.vue';
import NavUser from '@/pages/Dashboard/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';
import Alert  from '@/components/Alert.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

console.log(page.props.auth.user.roles);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'Receptionists',
            href: route('receptionist.index'),
            icon: LayoutGrid,
        },
        {
            title: 'Manager',
            href: route('manager.manage-clients'),
            icon: LayoutGrid,
        },
    ];
    console.log(user.value.roles.some((role: string) => ['admin', 'manager'].includes(role)));

    // Add Floor Management if the user is an admin or manager
    if (user.value?.roles && Array.isArray(user.value.roles) && user.value.roles.some((role: string) => ['admin', 'manager'].includes(role.name))) {
        items.push({
            title: 'Floor Management',
            href: route('floors.index'),
            icon: LayoutGrid,
        });
    }

    if (user.value?.roles && Array.isArray(user.value.roles) && user.value.roles.some((role: string) => ['admin'].includes(role.name))) {
        items.push({
            title: 'Rooms Management',
            href: route('rooms.index'),
            icon: LayoutGrid,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
    <Alert />
</template>
