<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { useForm,router } from '@inertiajs/vue3';
import { Dialog, DialogTrigger, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
  user?: { id: number; name: string; email: string, avatar_image?: string };
  profile?: { avatar?: string };
}>();

const isModalOpen = ref(false);

// Ensure props.user has default values to prevent errors
const user = computed(() => props.user ?? { id: 0, name: '', email: '' });

// Ensure profile has default values
const profile = computed(() => props.profile ?? { avatar: '' });
console.log(profile);
// Form setup with safe initial values
const form = useForm({
  name: user.value.name || '',
  email: user.value.email || '',
  avatar: null as File | null,
});
// const form = ref({ name: user.value.name, email: user.value.email ,avatar:null as File | null});

const fileInput = ref<HTMLInputElement | null>(null);

// Watch for changes and update the form dynamically
// watch(() => props.user, (newUser) => {
//   if (newUser) {
//     form.name = newUser.name;
//     form.email = newUser.email;
//   }
// }, { immediate: true });

// Handle file input changes
const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files?.length) {
    form.avatar = target.files[0];
  }
};

// Submit form
const submit = () => {
  const formData = new FormData();
  formData.append("name", form.name);
  formData.append("email", form.email);
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
  <div class="max-w-lg mx-auto py-6">
    <Card>
      <CardHeader>
        <CardTitle class="text-xl">User Profile</CardTitle>
      </CardHeader>
      <CardContent>
        <div class="flex flex-col items-center">
            <img :src="user.avatar_image ? `/storage/${user.avatar_image}` : '/default-avatar-image.png'" 
            alt="User Avatar" 
            class="w-24 h-24 rounded-full mb-4 border shadow" />
          
          <div class="w-full space-y-4">
            <p v-if="!user.id" class="text-red-500 text-sm">Loading user data...</p>

            <div v-else>
              <p><strong>Name:</strong> {{ user.name }}</p>
              <p><strong>Email:</strong> {{ user.email }}</p>
              <Button @click="isModalOpen = true" class="w-full mt-4">Edit Profile</Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Edit Modal -->
    <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
      <DialogTrigger asChild>
        <Button class="hidden">Edit</Button>
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Edit Profile</DialogTitle>
        </DialogHeader>
        <div class="space-y-4">
          <div>
            <Label for="name">Name</Label>
            <Input v-model="form.name" id="name" type="text" />
          </div>
          <div>
            <Label for="email">Email</Label>
            <Input v-model="form.email" id="email" type="email" />
          </div>
          <div>
            <Label for="avatar">Avatar</Label>
            <Input type="file" id="avatar" @change="handleFileChange" ref="fileInput" />
          </div>
        </div>
        <DialogFooter>
          <Button @click="submit" class="w-full">Save Changes</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
