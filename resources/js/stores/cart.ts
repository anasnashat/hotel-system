import { defineStore } from 'pinia';
import { ref, computed, watch, onMounted } from 'vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import axios from "axios";

interface CartItem {
  id: number;
  name: string;
  price: number;
  image: string;
}

export const useCartStore = defineStore('cart', () => {
  // Load cart from localStorage (if exists)
  const cart = ref<CartItem[]>(JSON.parse(localStorage.getItem('cart') || '[]'));

  // Add a room to the cart (book it)
    const addToCart = async (item: CartItem) => {
        try {
            await router.post('/cart',
                {
                    room_id: item.id,
                    accompany_number: 1
                },
                {
                    preserveScroll: true,
                    onSuccess: (response) => {
                        if (response.props.message) {
                            Swal.fire({
                                title: 'Success',
                                text: response.props.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    onError: (errors) => {
                        let errorMessage = 'Failed to add item to cart';
                        if (errors.message) errorMessage = errors.message;
                        if (errors.error) errorMessage = errors.error;

                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            );
        } catch (error) {
            console.error('Error adding item to cart:', error);
            Swal.fire({
                title: 'Error',
                text: 'Failed to add item to cart',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    };

  // Remove a room from the cart (cancel booking)
  const removeFromCart = (itemId: number) => {
    cart.value = cart.value.filter((item) => item.id !== itemId);
    saveCart(); // Save to localStorage
  };

  // Compute total price of booked rooms
  const totalPrice = computed(() => {
    return cart.value.reduce((total, item) => total + item.price, 0);
  });

  // Save cart to localStorage
  const saveCart = () => {
    localStorage.setItem('cart', JSON.stringify(cart.value));
  };

  // Watch for cart changes and update localStorage
   watch(cart, saveCart, { deep: true });

  // Load cart from localStorage when the store initializes
  onMounted(() => {
    cart.value = JSON.parse(localStorage.getItem('cart') || '[]');
  });

  return { cart, addToCart, removeFromCart, totalPrice };
});
