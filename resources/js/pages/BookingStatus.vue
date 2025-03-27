<script setup>
import NavBarMain from "@/components/NavBarMain.vue";
import { ref, onMounted } from "vue";
import { usePage } from '@inertiajs/vue3';

// Mock API call (replace with actual backend API)
const bookings = ref([]);

const page = usePage();
const reservations = page.props.reservations;

console.log(reservations);
const fetchBookings = async () => {
  try {
    const response = await fetch(route('reservation.index')); // Replace with real API

      console.log(response);
    if (!response.ok) throw new Error("Failed to fetch bookings");

    bookings.value = await response.json();
  } catch (error) {
    console.error("Error fetching bookings:", error);
  }
};

onMounted(fetchBookings);
</script>

<template>
    <NavBarMain/>
  <div class="max-w-3xl mx-auto p-6 bg-white shadow-lg rounded-lg m-4">
    <h1 class="text-2xl font-bold mb-4 cardTitle">Your Bookings</h1>

    <div v-if="reservations.length > 0">
      <table class="w-full border-collapse border border-gray-300">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2 border">Room</th>
            <th class="p-2 border">Status</th>
            <th class="p-2 border">Price Paid </th>
            <th class="p-2 border">Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="booking in reservations" :key="booking.id" class="border">
            <td class="p-2 border">{{ booking.room.number }}</td>
            <td class="p-2 border">
              <span class="px-2 py-1 rounded text-white"
                :class="booking.status === 'confirmed' ? 'bg-green-500' : 'bg-yellow-500'">
                {{ booking.status }}
              </span>
            </td>
            <td class="p-2 border">{{ booking.price_at_booking/100 }}</td>
              <td class="p-2 border">{{ new Date(booking.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="text-gray-500">No bookings found.</p>
  </div>
</template>
