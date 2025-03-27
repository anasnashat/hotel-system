<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Payment Successful" />
  <div class="container mx-auto text-center py-10 bg-gray-50 shadow-md rounded-lg">
    <h2 class="text-3xl font-bold text-green-600 mb-4">Payment Successful! 🎉</h2>
    <p class="text-lg text-gray-700 mb-8">
      Thank you for your booking. Your payment has been processed successfully.
    </p>
    <Link
      href="/"
      class="inline-block px-8 py-3 bg-green-500 text-white font-medium rounded-md hover:bg-green-600 transition duration-200"
    >
      Go to Home
    </Link>
  </div>
</template>