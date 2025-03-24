<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Heart, ShoppingCart, User as UserIcon  , LogOut } from 'lucide-vue-next';
import { type SharedData, type User } from '@/types';
import { useCartStore } from '@/stores/cart';
import { useFavoriteStore } from '@/stores/favorite';
import CartComponent from '@/pages/CartComponent.vue';

// Get global cart state
const cartStore = useCartStore();
const favoriteStore = useFavoriteStore();
// Get user data from Inertia
const page = usePage<SharedData>();
const user = computed(() => page.props.auth.user as User | null);
const userLoggedIn = computed(() => !!user.value);

// Logout function
const logout = () => {
    router.post('/logout');
};



</script>

<template>

    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton size="lg" class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
                        <UserInfo :user="user" />
                        <ChevronsUpDown class="ml-auto size-4" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                    :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"
                    align="end"
                    :side-offset="4"
                >
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>

  <div class="flex items-center space-x-4">
    <!-- Favorite Icon -->
     <Link href="/favorites">
    <div class="relative">
      <button class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
      <Heart :size="20" />
    </button>
    <span v-if="favoriteStore.favoriteCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{ favoriteStore.favoriteCount }}</span>
     </div>
    </Link>

    <!-- Shopping Cart Icon -->
     <Link href="/cart">
     <div class="relative">
      <button class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
      <ShoppingCart :size="20" />
    </button>
    <span v-if="cartStore.cart.length > 0"  class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{ cartStore.cart.length }}</span>
    </div>
    </Link>

    <!-- Show Register/Login if user is not logged in -->
    <template v-if="!userLoggedIn">
      <Link href="/login" class="border border-[#5b5329] text-[#5b5329] font-medium px-4 py-1 rounded-md hover:bg-[#5b5329] hover:text-white transition duration-300 dark:text-white dark:hover:bg-[#5b5329] dark:hover:text-white">Login</Link>
      <Link href="/register" class="border border-[#5b5329] text-[#5b5329] font-medium px-4 py-1 rounded-md hover:bg-[#5b5329] hover:text-white transition duration-300 dark:text-white dark:hover:bg-[#5b5329] dark:hover:text-white">Register</Link>
    </template>

    <!-- Show User Icon and Logout Button if user is logged in -->
    <template v-else>
      <Link href="/userdashboard">
      <button class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
        <UserIcon :size="20" />
      </button>
    </Link>
      <button @click="logout" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
        <LogOut :size="20" />
      </button>
    </template>
  </div>
  
</template>
