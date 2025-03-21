<template>
    <footer class="bg-gray-900 text-white py-8 mt-10">
      <div class="container mx-auto flex flex-col md:flex-row justify-between items-center px-6">
        
        <!-- Logo & Name -->
        <div class="flex items-center space-x-3">
          <span class="text-lg font-semibold cardTitle">Hotel</span>
        </div>
  
        <!-- Navigation Links -->
        <nav class="mt-4 md:mt-0">
          <ul class="flex space-x-6">
            <li><a href="#" class="hover:text-gray-400">Home</a></li>
            <li><a href="#" class="hover:text-gray-400">Rooms</a></li>
            <li><a href="#" class="hover:text-gray-400">About</a></li>
            <li><a href="#" class="hover:text-gray-400">Contact</a></li>
          </ul>
        </nav>
  
        <!-- Social Media Icons -->
        <div class="mt-4 md:mt-0 flex space-x-4">
          <a href="#" class="hover:text-gray-400">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="hover:text-gray-400">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="#" class="hover:text-gray-400">
            <i class="fab fa-instagram"></i>
          </a>
        </div>
  
      </div>
  
      <!-- Bottom Copyright -->
      <div class="text-center mt-6 text-sm text-gray-400">
        © {{ new Date().getFullYear() }} Hotel Name. All rights reserved.
      </div>
    </footer>
  </template>
  
  <script setup>
  </script>
  
  <style>
  /* If using FontAwesome for icons, ensure it's included in your project */
  </style>
  