import { defineStore } from "pinia";
import { computed, ref, watch } from "vue";

export const useFavoriteStore = defineStore("favorite", () => {
  // Load favorites from localStorage or initialize as an empty array
  const favoriteRooms = ref(JSON.parse(localStorage.getItem("favoriteRooms") || "[]"));

  // Save favorites to localStorage whenever it changes
  watch(favoriteRooms, (newFavorites) => {
    localStorage.setItem("favoriteRooms", JSON.stringify(newFavorites));
  }, { deep: true });

  // Add a room to favorites (ensure unique entries)
  const addFavorite = (room: { id: number; name: string; price: number; image?: string }) => {
    if (!room || !room.id) return; // Prevent invalid rooms
    if (!favoriteRooms.value.some((fav: { id: number }) => fav.id === room.id)) {
      favoriteRooms.value.push(room);
    }
  };

  // Remove a room from favorites
  const removeFavorite = (roomId: number) => {
    favoriteRooms.value = favoriteRooms.value.filter((fav: { id: number }) => fav.id !== roomId);
  };

  return { 
    favoriteRooms, 
    addFavorite, 
    removeFavorite, 
    favoriteCount: computed(() => favoriteRooms.value.length) 
  };
});
