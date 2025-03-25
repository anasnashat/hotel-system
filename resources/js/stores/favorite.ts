import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from "axios";

export const useFavoriteStore = defineStore("favorite", () => {
  const favoriteRooms = ref<any[]>([]); // Store favorite rooms
  const isLoading = ref(false);

  // Fetch favorites from the database
  const fetchFavorites = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get("http://127.0.0.1:8000/api/favorites", {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }, // Ensure authentication
      });
      favoriteRooms.value = response.data.map((fav: any) => fav.room); // Store only rooms
    } catch (error) {
      console.error("Error fetching favorites:", error);
    }
    isLoading.value = false;
  };

  // Add a room to favorites (database)
  const addFavorite = async (room: { id: number; name: string; price: number; image?: string }) => {
    try {
      await axios.post(`http://127.0.0.1:8000/api/favorites/${room.id}`, {}, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });
      favoriteRooms.value.push(room); // Update local store
    } catch (error) {
      console.error("Error adding to favorites:", error);
    }
  };

  // Remove a room from favorites (database)
  const removeFavorite = async (roomId: number) => {
    try {
      await axios.delete(`http://127.0.0.1:8000/api/favorites/${roomId}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });
      favoriteRooms.value = favoriteRooms.value.filter(room => room.id !== roomId);
    } catch (error) {
      console.error("Error removing from favorites:", error);
    }
  };

  return {
    favoriteRooms,
    isLoading,
    fetchFavorites,
    addFavorite,
    removeFavorite,
    favoriteCount: computed(() => favoriteRooms.value.length),
  };
});
