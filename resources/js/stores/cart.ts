import { defineStore } from 'pinia';
import { ref, computed, watch, onMounted } from 'vue';

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
  const addToCart = (item: CartItem) => {
    const alreadyBooked = cart.value.some((i) => i.id === item.id);
    if (!alreadyBooked) {
      cart.value.push(item);
      saveCart(); // Save to localStorage
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
