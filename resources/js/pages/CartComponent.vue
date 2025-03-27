<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table';
import { Card, CardContent } from '@/components/ui/card';
import { usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import NavBarMain from '@/components/NavBarMain.vue';
import { formatCurrency } from '@/utils/currencyFormatter';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Room {
    id: number;
    number: string;
    price: number;
    capacity: number; // Added capacity field
}

interface CartItem {
    id: number;
    room_id: number;
    room: Room;
    accompany_number: number; // Added quantity field
    image?: string;
}

type PageProps = {
    auth: {
        user: User | null;
    };
    cartItems: CartItem[];
    [key: string]: any;
}

const page = usePage<PageProps>();
const user = computed(() => page.props.auth?.user || null);
const userLoggedIn = computed(() => !!user.value);
const cartItems = computed(() => page.props.cartItems || []);

const totalPrice = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + (item.room.price / 100), 0);
});

const updateQuantity = async (room_id: number, newQuantity: number) => {
    if (newQuantity < 1) return;

    try {
        await router.post('/cart', {
            room_id,
            accompany_number: newQuantity
        }, {
            preserveScroll: true,
            onSuccess: () => {
            },
            onError: () => {
                Swal.fire({
                    title: 'Failed to update quantity',
                    text: 'Please try again',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    } catch (error) {
        console.error('Error updating quantity:', error);
        Swal.fire({
            title: 'Error',
            text: 'Failed to update quantity',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};



const removeItem = async (room_id: number) => {
    try {
        await router.delete('/cart', {
            data: { room_id },
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Item removed from cart',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            },
            onError: () => {
                Swal.fire({
                    title: 'Failed to remove item',
                    text: 'Please try again',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    } catch (error) {
        console.error('Error removing item:', error);
        Swal.fire({
            title: 'Error',
            text: 'Failed to remove item from cart',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const handleCheckout = () => {
    if (!userLoggedIn.value) {
        Swal.fire({
            title: 'Please log in or register before checking out.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    } else if (cartItems.value.length === 0) {
        Swal.fire({
            title: 'Your cart is empty',
            text: 'Add items to your cart before checkout',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    } else {
        router.visit('/checkout');
    }
};
</script>

<template>
    <NavBarMain/>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-4 text-center cardTitle">Shopping Cart</h2>
        <Card>
            <CardContent>
                <!-- Show Empty Cart Message -->
                <div v-if="cartItems.length === 0" class="text-center py-10">
                    <h3 class="text-lg font-medium text-gray-700">Your cart is empty 😢</h3>
                    <p class="text-gray-500">Start adding items to your cart!</p>
                </div>

                <!-- Show Cart Table If Items Exist -->
                <div v-else class="border border-gray-200 shadow-sm rounded-lg p-4">
                    <Table class="border border-gray-200 shadow-sm rounded-lg">
                        <TableHeader class="bg-gray-100">
                            <TableRow>
                                <TableHead class="text-left p-3">Name</TableHead>
                                <TableHead class="text-left p-3">Price</TableHead>
                                <TableHead class="text-left p-3">accompany number</TableHead>
                                <TableHead class="text-left p-3">Total</TableHead>
                                <TableHead class="text-left p-3">Action</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in cartItems" :key="item.id" class="border-b">
                                
                                <TableCell class="p-3">{{ item.room.number }}</TableCell>
                                <TableCell class="p-3">{{ formatCurrency(item.room.price / 100) }}</TableCell>
                                <TableCell class="p-3">
                                    <div class="flex items-center gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="updateQuantity(item.room_id, item.accompany_number - 1)"
                                            :disabled="item.accompany_number <= 1"
                                        >
                                            -
                                        </Button>
                                        <span>{{ item.accompany_number }}</span>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="updateQuantity(item.room_id, item.accompany_number + 1)"
                                            :disabled="item.accompany_number >= item.room.capacity"
                                        >
                                            +
                                        </Button>
                                    </div>
                                </TableCell>
                                <TableCell class="p-3">{{ formatCurrency(item.room.price  / 100) }}</TableCell>
                                <TableCell class="p-3">
                                    <Button
                                        variant="destructive"
                                        @click="removeItem(item.room_id)"
                                    >
                                        Delete
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Total Price & Checkout Button -->
                    <div class="flex justify-between items-center mt-6">
                        <h3 class="text-xl font-semibold cardTitle">Total: {{ formatCurrency(totalPrice) }}</h3>
                        <Button
                            variant="default"
                            class="w-1/4 border border-[#5b5329] bg-[#5b5329] text-white font-medium px-4 py-1 rounded-md hover:bg-white hover:text-[#5b5329] transition duration-300"
                            @click="handleCheckout"
                        >
                            Checkout
                        </Button>
                    </div>
                </div>
                <p v-if="!userLoggedIn && cartItems.length > 0" class="text-red-500 mt-4 text-center">
                    Please <a href="/login" class="text-blue-500 hover:underline">log in</a> to proceed to checkout.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
