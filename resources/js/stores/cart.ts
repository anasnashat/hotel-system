import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

interface CartItem {
  id: number;
  name: string;
  price: number;
  image: string;
}

export const useCartStore = defineStore('cart', () => {
  const cart = ref<CartItem[]>([]); // Store booked rooms

  // Add a room to the cart (book it)
  const addToCart = (item: CartItem) => {
    const alreadyBooked = cart.value.some((i) => i.id === item.id);
    if (!alreadyBooked) {
      cart.value.push(item); // Add room only if not already booked
    }
  };

  // Remove a room from the cart (cancel booking)
  const removeFromCart = (itemId: number) => {
    cart.value = cart.value.filter((item) => item.id !== itemId);
  };

  // Compute total price of booked rooms
  const totalPrice = computed(() => {
    return cart.value.reduce((total, item) => total + item.price, 0);
  });

  return { cart, addToCart, removeFromCart, totalPrice };
});
