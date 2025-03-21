<template>
  <nav class="nav-main">
    <!-- Sidebar Header -->
    <div class="nav-logo">
      <a href="/" class="text-xl font-bold text-white">Hotel System</a>
    </div>

    <!-- Navigation Links -->
    <div class="nav-links">
      <ul>
        <li v-for="item in items" :key="item.href">
          <a :href="item.href" class="nav-button">{{ item.title }}</a>
        </li>

        <!-- Authenticated user options -->
        <!-- <li v-if="isAuthenticated">
          <a href="/profile" class="nav-button">Profile</a>
        </li>
        <li v-if="isAuthenticated">
          <a href="#" @click="logout" class="nav-button">Logout</a>
        </li> -->

        <!-- Guest user links -->
        <li v-if="!isAuthenticated">
          <a href="/login" class="nav-button">Login</a>
        </li>
        <li v-if="!isAuthenticated">
          <a href="/register" class="nav-button">Register</a>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
  items: Array,  // Accepts dynamic menu items from AppSidebar.vue
  isAuthenticated: Boolean,
});

const logout = () => {
  window.location.href = "/logout"; // Simple logout redirect
};
</script>

<style scoped>
.nav-main {
  display: flex;
  flex-direction: column;
  height: 100vh; /* Full height */
  width: 250px;
  background-color: #1f2937; /* Dark gray */
  padding: 20px;
}

/* Center logo text */
.nav-logo {
  text-align: center;
  margin-bottom: 20px;
}

/* Navigation List */
.nav-links ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

/* Button Style for Navigation Links */
.nav-button {
  display: block;
  padding: 12px;
  margin-bottom: 10px;
  background-color: #374151; /* Darker Gray */
  color: #fff;
  text-decoration: none;
  border-radius: 8px;
  font-size: 16px;
  text-align: center;
  transition: all 0.3s ease;
}

/* Hover and Active Effects */
.nav-button:hover {
  background-color: #4b5563; /* Lighter Gray */
}

.nav-button:active {
  background-color: #6b7280; /* Even Lighter Gray */
}
</style>
