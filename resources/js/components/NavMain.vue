<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from "vue";
  import { Button } from "@/components/ui/button";
  import NavLink from "./NavLink.vue";
  
  // Mobile menu state
  const isMenuOpen = ref(false);
  const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
  };
  
  // Dark mode state
  const isDarkMode = ref(localStorage.getItem("theme") === "dark");
  
  const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    document.documentElement.classList.toggle("dark", isDarkMode.value);
    localStorage.setItem("theme", isDarkMode.value ? "dark" : "light");
  };
  
  // Set dark mode on mount if it was previously enabled
  onMounted(() => {
    if (localStorage.getItem("theme") === "dark") {
      document.documentElement.classList.add("dark");
      isDarkMode.value = true;
    }
  });
defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
</script>

<template>
    <nav class="shadow-md bg-white transition duration-300">
      <div class="container mx-auto flex justify-between items-center p-4">
        <!-- Logo -->
        <a href="/" class="text-2xl font-bold text-primary">
          HotelName
        </a>
  
        <!-- Navigation Links -->
        <div class="hidden md:flex space-x-6">
          <NavLink href="/" text="Home" />
          <NavLink href="/rooms" text="Rooms" />
          <NavLink href="/about" text="About Us" />
          <NavLink href="/contact" text="Contact" />
        </div>
  
        <!-- Right Section: Icons & Buttons -->
        <div class="flex items-center space-x-4">
          <!-- Favorite & Cart Icons -->
          <HeartIcon class="w-6 h-6 text-gray-700 cursor-pointer hover:text-primary" />
          <ShoppingCartIcon class="w-6 h-6 text-gray-700 cursor-pointer hover:text-primary" />
  
          <!-- Dark Mode Toggle -->
          <button @click="toggleDarkMode" class="p-2 rounded-full transition">
            <SunIcon v-if="isDarkMode" class="w-6 h-6 text-white" />
            <MoonIcon v-else class="w-6 h-6 text-gray-700" />
          </button>
  
          <!-- Login / Register Buttons -->
          <Button variant="outline" class="hidden md:block">Login</Button>
          <Button class="hidden md:block">Sign Up</Button>
  
          <!-- Mobile Menu Button -->
          <button class="md:hidden text-gray-700" @click="toggleMenu">
            <MenuIcon class="w-6 h-6" />
          </button>
        </div>
      </div>
  
      <!-- Mobile Menu -->
      <div v-if="isMenuOpen" class="md:hidden bg-white border-t shadow-sm p-4">
        <NavLink href="/" text="Home" />
        <NavLink href="/rooms" text="Rooms" />
        <NavLink href="/about" text="About Us" />
        <NavLink href="/contact" text="Contact" />
        <div class="mt-4 space-y-2">
          <Button variant="outline" class="w-full">Login</Button>
          <Button class="w-full">Sign Up</Button>
        </div>
      </div>
    </nav>
  </template>
  
  <style>
  /* Tailwind already handles dark mode, but you can add extra styles if needed */
  </style>
  
