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
import Alert from '@/components/Alert.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const hasRole = (roleName: string) => {
    return user.value?.roles?.includes(roleName);
};

const hasAnyRole = (roleNames: string[]) => {
    return roleNames.some(role => user.value?.roles?.includes(role));
};

const mainNavItems = computed<NavItem[]>(() => {
    const baseItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'Manage Clients',
            href: route('clients-management.index'),
            icon: LayoutGrid,
        }
    ];

    // Manager-specific items
    if (hasAnyRole(['admin', 'manager'])) {
        baseItems.push(

            {
                title: 'Manage Receptionist',
                href: route('manager.manage-receptionists'),
                icon: LayoutGrid,
            }
        );
    }

    // Admin/manager items
    if (hasAnyRole(['admin', 'manager'])) {
        baseItems.push(
            {
                title: 'Manage Floors',
                href: route('floors.index'),
                icon: LayoutGrid,
            },
            {
                title: 'Manage Rooms',
                href: route('rooms.index'),
                icon: LayoutGrid,
            }
        );
    }

    // Admin-only items
    if (hasRole('admin')) {
        baseItems.push(
            {
                title: 'Manage Manager',
                href: route('managers.index'),
                icon: LayoutGrid,
            },
        );
    }

    return baseItems;
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
    <div class="flex min-h-screen">
        <!-- Sidebar -->
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

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <slot />
        </main>
    </div>

    <!-- Alert Component -->
    <Alert />
</template>
