<script setup lang="ts">
import { computed } from 'vue';
import { useCartStore } from '@/stores/cart';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table';
import { Card, CardContent } from '@/components/ui/card';
import NavMain from '@/components/NavMain.vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

interface User {
  id: number;
  name: string;
  email: string;
}

type PageProps = {
  auth: {
    user: User | null;
  };
  [key: string]: any;
}

const cartStore = useCartStore();

// Get user data from Inertia
const page = usePage<PageProps>();
const user = computed(() => page.props.auth?.user|| null);
const userLoggedIn = computed(() => !!user.value);


const totalPrice = computed(() => {
  return cartStore.cart.reduce((sum, item) => sum + item.price * 1, 0);
});

const removeItem = (id: number) => {
  cartStore.removeFromCart(id);
};
// Handle checkout click
const handleCheckout = () => {
  if (!userLoggedIn.value) {
    Swal.fire({
      title: 'Please log in or register before checking out.',
      icon: 'warning',
      confirmButtonText: 'OK'
    });
    
  } else {
    // Proceed with checkout logic (redirect to checkout page or API call)
    console.log('Proceeding to checkout...');
  }
};
</script>

<template>
  <NavMain/>
  <div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4 text-center cardTitle">Shopping Cart</h2>
    <Card>
      <CardContent>

        <!-- Show Empty Cart Message -->
        <div v-if="cartStore.cart.length === 0" class="text-center py-10">
          <h3 class="text-lg font-medium text-gray-700">Your cart is empty 😢</h3>
          <p class="text-gray-500">Start adding items to your cart!</p>
        </div>

        <!-- Show Cart Table If Items Exist -->
        <div v-else class="border border-gray-200 shadow-sm rounded-lg p-4">
          <Table class="border border-gray-200 shadow-sm rounded-lg">
            <TableHeader class="bg-gray-100">
              <TableRow>
                <TableHead class="text-left p-3">Image</TableHead>
                <TableHead class="text-left p-3">Name</TableHead>
                <TableHead class="text-left p-3">Price</TableHead>
                <TableHead class="text-left p-3">Quantity</TableHead>
                <TableHead class="text-left p-3">Total</TableHead>
                <TableHead class="text-left p-3">Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="item in cartStore.cart" :key="item.id" class="border-b">
                <TableCell class="p-3">
                  <img :src="item.image" alt="Product" class="w-16 h-16 object-cover rounded-md" />
                </TableCell>
                <TableCell class="p-3">{{ item.name }}</TableCell>
                <TableCell class="p-3">${{ item.price.toFixed(2) }}</TableCell>
                <TableCell class="p-3">1</TableCell>
                <TableCell class="p-3">${{ (item.price * 1).toFixed(2) }}</TableCell>
                <TableCell class="p-3">
                  <Button variant="destructive" @click="removeItem(item.id)">Delete</Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>

          <!-- Total Price & Checkout Button -->
          <div class="flex justify-between items-center mt-6">
            <h3 class="text-xl font-semibold cardTitle">Total: ${{ totalPrice.toFixed(2) }}</h3>
            <Button variant="default" class="w-1/4 border border-[#5b5329] bg-[#5b5329] text-white font-medium px-4 py-1 rounded-md hover:bg-white hover:text-[#5b5329] transition duration-300" @click="handleCheckout">Checkout</Button>
          </div>
        </div>
        <p v-if="!userLoggedIn" class="text-red-500 mt-4 text-center">
          Please <a href="/login" class="text-blue-500">log in</a> to proceed to checkout.
        </p>
      </CardContent>
    </Card>
  </div>
</template>
