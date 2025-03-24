<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { useForm,router } from '@inertiajs/vue3';
import { Dialog, DialogTrigger, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import NavBarMain from "@/components/NavBarMain.vue";
import axios from 'axios';

const props = defineProps<{
  user?: { id: number; name: string; email: string, avatar_image?: string };
  profile?: { avatar?: string ; phone_number?: string; national_id?: string};
}>();

const isModalOpen = ref(false);

// Ensure props.user has default values to prevent errors
const user = computed(() => props.user ?? { id: 0, name: '', email: '' });

// Ensure profile has default values
const profile = computed(() => props.profile ?? { avatar: '', phone_number: '', national_id: '' });
console.log(profile);

// Form setup with safe initial values
const form = useForm({
  name: user.value.name || '',
  email: user.value.email || '',
  phone_number: profile.value.phone_number || '',
  national_id: profile.value.national_id || '',
  avatar: null as File | null,
});

const fileInput = ref<HTMLInputElement | null>(null);

// Function to check email or phone existence
const checkExistence = async (field: string, value: string) => {
  if (!value) return false;
  try {
    const response = await axios.post('/check-existence', { field, value });
    return response.data.exists;
  } catch (error) {
    // console.error(`Error checking ${field}:`, error.response?.data || error.message);
    return false;
  }
};

// Handle file input changes
const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files?.length) {
    form.avatar = target.files[0];
  }
};

// Submit form
const submit = async () => {
  // Check if email exists
  if (form.email && form.email !== user.value.email) {
    const emailExists = await checkExistence("email", form.email);
    if (emailExists) {
      alert("This email is already registered.");
      return;
    }
  }

  // Check if phone number exists
  if (form.phone_number && form.phone_number !== profile.value.phone_number) {
    const phoneExists = await checkExistence("phone_number", form.phone_number);
    if (phoneExists) {
      alert("This phone number is already registered.");
      return;
    }
  }
  const formData = new FormData();
  formData.append("name", form.name);
  formData.append("email", form.email);
  formData.append("phone_number", form.phone_number);
  formData.append("national_id", form.national_id);
  if (form.avatar) {
    formData.append("avatar", form.avatar);
  }
  if (!user.value.id) return; // Prevent errors if user ID is missing
  form.post(route('userdashboard.update', user.value.id ),{
    preserveScroll: true,
    onSuccess: () => {
      isModalOpen.value = false;
      window.location.reload();
    }
  });
};
</script>

<template>
  <NavBarMain />
  <div class="max-w-lg mx-auto py-8">
    <Card class="min-h-[500px] rounded-2xl shadow-xl bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-300">
      <CardHeader class="text-center">
        <CardTitle class="text-2xl font-bold text-gray-800">User Profile</CardTitle>
      </CardHeader>
      <CardContent class="flex flex-col items-center space-y-6">
        <!-- Avatar -->
        <div class="relative">
          <img 
            :src="user.avatar_image ? `/storage/${user.avatar_image}` : '/default-avatar-image.png'"
            alt="User Avatar"
            class="w-36 h-36 rounded-full border-4 border-gray-300 shadow-lg object-cover transition-transform transform hover:scale-105"
          />
        </div>

        <!-- User Info -->
        <div class="w-full text-center space-y-2">
          <p v-if="!user.id" class="text-red-500 text-sm">Loading user data...</p>
          <div v-else>
            <p class="text-lg font-semibold text-gray-700"><strong>Name:</strong> {{ user.name }}</p>
            <p class="text-lg font-semibold text-gray-700"><strong>Email:</strong> {{ user.email }}</p>
            <p class="text-lg font-semibold text-gray-700"><strong>Phone:</strong> {{ profile.phone_number || 'N/A' }}</p>
    <p class="text-lg font-semibold text-gray-700"><strong>National ID:</strong> {{ profile.national_id || 'N/A' }}</p>
            <Button 
              @click="isModalOpen = true" 
              class="mt-5 bg-[#5b5329] hover:bg-[#403d1e] transition-all text-white font-medium px-6 py-2 rounded-lg shadow-md hover:shadow-lg"
            >
              Edit Profile
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Edit Modal -->
    <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
      <DialogTrigger asChild>
        <Button class="hidden">Edit</Button>
      </DialogTrigger>
      <DialogContent class="bg-white rounded-xl shadow-xl p-6">
        <DialogHeader>
          <DialogTitle class="text-xl font-semibold text-gray-800">Edit Profile</DialogTitle>
        </DialogHeader>
        <div class="space-y-4">
          <div>
            <Label for="name" class="text-gray-700 font-medium">Name</Label>
            <Input v-model="form.name" id="name" type="text" class="border-gray-300 rounded-lg p-2" />
          </div>
          <div>
            <Label for="email" class="text-gray-700 font-medium">Email</Label>
            <Input v-model="form.email" id="email" type="email" class="border-gray-300 rounded-lg p-2" />
          </div>
          <div>
        <Label for="phone">Phone Number</Label>
        <Input v-model="form.phone_number" id="phone" type="text" class="border-gray-300 rounded-lg p-2" />
      </div>
          <div>
            <Label for="avatar" class="text-gray-700 font-medium">Avatar</Label>
            <Input type="file" id="avatar" @change="handleFileChange" ref="fileInput" class="border-gray-300 rounded-lg p-2" />
          </div>
        </div>
        <DialogFooter class="mt-4">
          <Button 
            @click="submit" 
            class="w-full bg-[#5b5329] hover:bg-[#403d1e] transition-all text-white font-medium px-6 py-2 rounded-lg shadow-md hover:shadow-lg"
          >
            Save Changes
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
