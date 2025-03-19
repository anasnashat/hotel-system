<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const form = ref({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    router.post('/login', form.value);
};
</script>

<template>
    <div class="flex items-center justify-center h-screen">
        <form @submit.prevent="submit" class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">Login</h2>
            
            <label class="block mb-2">Email</label>
            <input v-model="form.email" type="email" class="w-full p-2 border rounded mb-4">
            
            <label class="block mb-2">Password</label>
            <input v-model="form.password" type="password" class="w-full p-2 border rounded mb-4">
            
            <div class="flex items-center">
                <input v-model="form.remember" type="checkbox" class="mr-2">
                <label>Remember Me</label>
            </div>
            
            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded w-full">
                Login
            </button>
        </form>
    </div>
</template>
