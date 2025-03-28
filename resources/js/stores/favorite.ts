import { defineStore } from "pinia";
import { computed, ref, watch } from "vue";
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export const useFavoriteStore = defineStore("favorite", () => {
  // Load favorites from localStorage or initialize as an empty array
  const favoriteRooms = ref(JSON.parse(localStorage.getItem("favoriteRooms") || "[]"));

  // Save favorites to localStorage whenever it changes
  watch(favoriteRooms, (newFavorites) => {
    localStorage.setItem("favoriteRooms", JSON.stringify(newFavorites));
  }, { deep: true });

  // Add a room to favorites (ensure unique entries)
  const addFavorite = (room: { id: number; name: string; price: number; image?: string }) => {

      router.post('/favorites', { room_id: room.id }, {
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
          }
      });


    if (!room || !room.id) return; // Prevent invalid rooms
    if (!favoriteRooms.value.some((fav: { id: number }) => fav.id === room.id)) {
      favoriteRooms.value.push(room);
    }
  };

  // Remove a room from favorites
const removeFavorite = (roomId: number) => {
    router.delete('/favorites', {
        data: { room_id: roomId },
    });

    favoriteRooms.value = favoriteRooms.value.filter((fav: { id: number }) => fav.id !== roomId);
};


  return {
    favoriteRooms,
    addFavorite,
    removeFavorite,
    favoriteCount: computed(() => favoriteRooms.value.length)
  };
});
